<?php
/*
 * +--------------------------------------------------------------------------+
 * | Copyright (c) 2013 Add This, LLC                                         |
 * +--------------------------------------------------------------------------+
 * | This program is free software; you can redistribute it and/or modify     |
 * | it under the terms of the GNU General Public License as published by     |
 * | the Free Software Foundation; either version 3 of the License, or        |
 * | (at your option) any later version.                                      |
 * |                                                                          |
 * | This program is distributed in the hope that it will be useful,          |
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of           |
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            |
 * | GNU General Public License for more details.                             |
 * |                                                                          |
 * | You should have received a copy of the GNU General Public License        |
 * | along with this program.  If not, see <http://www.gnu.org/licenses/>.    |
 * +--------------------------------------------------------------------------+
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

if(!defined('DS')){
    define('DS',DIRECTORY_SEPARATOR);
}

jimport('joomla.plugin.plugin');
jimport('joomla.version');

/**
 * plgContentAddThis
 *
 * Creates AddThis sharing button with each and every posts.
 * Enable Welcome bar tool.
 * Reads the user settings and creates the config accordingly.
 *
 * @author AddThis Team - Sol, Vipin
 * @version 2.0.0
 */
class plgContentAddThis extends JPlugin {

   /**
    * Constructor
    *
    * Loads the plugin settings and assigns them to class variables
    *
    * @param reference $subject
    * @param object $config
    */
    public function __construct(&$subject, $config)
    {    	
        parent::__construct($subject, $config);
        $this->setBaseURL();
        $this->setPageProtocol();
        $this->populateParams();
        
        $this->appendAddThisScript();
    }

    /**
     * onPrepareContent
     *
     * Content creation listening event for Joomla 1.5 version
     *
     * @param reference $article
     * @param reference $params
     * @param integer $limitstart
     * @return void
     * @see http://docs.joomla.org/Reference:Content_Events_for_Plugin_System#5.5.2_onPrepareContent
     */
    public function onPrepareContent(&$article, &$params, $limitstart)
    {
    	$this->createAddThis($article);
    }

    /***
     * onContentBeforeDisplay
     *
     * Content creation listening event for Joomla 1.6 version
     *
     * @param reference $item
     * @param reference $article
     * @return void
     */
    public function onContentBeforeDisplay($item, &$article)
	{
		$this->createAddThis($article);
    }

    /**
     * Creates configuration script and addthis button code while content is being prepared
     * and appends it to the article or post
     *
     * @param object $article
     * @return void
     */
    private function createAddThis($article)
    {
    	//Creating div elements for AddThis
		$outputValue = " <div class='joomla_add_this'>";
		$outputValue .= "<!-- AddThis Button BEGIN -->" . PHP_EOL;

		//Creates addthis configuration script
	    $outputValue .= "<script type='text/javascript'>\r\n";
	    $outputValue .= "var addthis_product = 'jlp-2.0';\r\n";
		$outputValue .= "var addthis_config = {\r\n";
		$configValue = $this->prepareConfigValues();

    	//Removing the last comma and end of line characters
    	if("" != trim($configValue))
		{
		  	$outputValue .= implode( ',', explode( ',', $configValue, -1 ));
		}
		$outputValue .= "\n}\n</script>". PHP_EOL;

        $article_url = "addthis:url='".urldecode($this->getArticleUrl($article))."' addthis:title='".$this->escapeText($article->title)."'";
    	
    	//Creates the button code depending on the button style chosen
        $buttonValue = $this->getButtonSet($article_url, $article);

		$outputValue .= $buttonValue;

		$outputValue .= "<!-- AddThis Button END -->". PHP_EOL;
		$outputValue .= "</div>";

        //Regular expression for finding the custom tag which disables AddThis button in the article.
        $switchregex = "#{addthis (on|off)}#s";

		if(class_exists("JFactory"))
		{
			//Gets frontpage
			$menu = JFactory::getApplication()->getMenu();
			//Sets the visibility of AddThis button in frontpage depending on user's settings
			if(($menu->getActive() == $menu->getDefault()) && ($this->arrParamValues["show_frontpage"] == false)) {
			  $hide_frontpage = true;
			  $outputValue = "";
			}
		}

        //Ensuring the custom tag is not present in the article text.
        //Positioning button according to the position chosen
        if(isset($article->text))
           $article->text = strpos($article->text, '{addthis off}') == false ? "top" == $this->arrParamValues["position"] ? $outputValue . $article->text : $article->text.$outputValue : preg_replace($switchregex, '', $article->text);
        if(isset($article->introtext))
           $article->introtext = strpos($article->introtext, '{addthis off}') == false ? "top" == $this->arrParamValues["position"] ? $outputValue . $article->introtext : $article->introtext.$outputValue : preg_replace($switchregex, '', $article->introtext);
    }

    /**
    * getArticleUrl
    *
    * Gets the static url for the article
    *
    * @param object $article - Joomla article object
    * @return string returns the permalink of a particular post or page
    **/
    private function getArticleUrl(&$article)
    {
        if (!is_null($article))
        {
            require_once( JPATH_SITE . DS . 'components' . DS . 'com_content' . DS . 'helpers' . DS . 'route.php');
			if(isset($article->id) && isset($article->catid))
			{
				$url = JRoute::_(ContentHelperRoute::getArticleRoute($article->id, $article->catid));
				return JRoute::_($this->baseURL . $url, true, 0);
			}
			else
			{
			    return $this->baseURL;
			}
        }
    }

	/**
     * escapeText
     *
     * Escapes single quotes
     *
     * @param string $text - string to be escaped
     * @return string returns text with special characters encoded
     */
    private function escapeText($text)
    {
    	$cleanedText = htmlspecialchars($text);
    	return str_replace("'", "\'", $cleanedText);
    }

    /**
     * populateParams
     *
     * Gets the plugin parameters and holds them as a collection
     *
     * @return void
     */
     private function populateParams()
     {
     	$version = new JVersion;
        $joomlaVersion = $version->RELEASE;

     	// Loading plugin parameters for Joomla 1.5
        if($joomlaVersion < 1.6){
	        $plugin = JPluginHelper::getPlugin('content', 'addthis');
	        $params = new JParameter($plugin->params);
	    }

        $arrParams = array("profile_id", "button_style", "menu_version","custom_button_code",
        				   "addthis_brand", "addthis_header_color", "addthis_header_background", "addthis_services_compact",
        				   "addthis_services_exclude", "addthis_services_expanded", "addthis_services_custom", "addthis_offset_top",
        				   "addthis_offset_left", "addthis_hover_delay", "addthis_click", "addthis_hover_direction",
        				   "addthis_data_track_clickback", "addthis_language", "position", "show_frontpage", "toolbox_more_services_mode",
        				   "addthis_use_css", "addthis_ga_tracker");
        
        foreach ( $arrParams as $key => $value ) {
			if($value == "profile_id")
        		$this->arrParamValues[$value] = $joomlaVersion > 1.5 ? urlencode($this->params->def($value)): urlencode($params->get($value));
        	else
				$this->arrParamValues[$value] = $joomlaVersion > 1.5 ? $this->params->def($value): $params->get($value);		
		}
     }

    /**
     * prepareConfigValues
     *
     * Prepares configuration values for AddThis button from user saved settings
     *
     * @return void
     */
    private function prepareConfigValues()
    {
    	$configValue = "";
		$arrConfigs = array("profile_id" => "pubid", "addthis_brand" => "ui_cobrand", "addthis_header_color" => "ui_header_color",
							"addthis_header_background" => "ui_header_background", "addthis_services_compact" => "services_compact",
							"addthis_services_exclude" => "services_exclude", "addthis_services_expanded" => "services_expanded",
							"addthis_services_custom" => "services_custom", "addthis_offset_top" => "ui_offset_top",
							"addthis_offset_left" => "ui_offset_left", "addthis_hover_delay" => "ui_delay", "addthis_click" => "ui_click",
							"addthis_hover_direction" => "ui_hover_direction",
							"addthis_data_track_clickback" => "data_track_clickback",
							"addthis_language" => "ui_language",
							"addthis_use_css" => "ui_use_css", "addthis_ga_tracker" => "data_ga_property");

		foreach ( $arrConfigs as $key => $value ) {
		   if(in_array($value, array("pubid", "ui_cobrand", "ui_header_color", "ui_header_background", "services_compact", "services_exclude", "services_expanded", "ui_language", "data_ga_property")) && ($this->arrParamValues[$key] != ""))
		           $configValue .= $value . ":'" . $this->arrParamValues[$key] . "'," . PHP_EOL;
		   elseif(in_array($value, array("ui_offset_top", "ui_offset_left", "ui_delay", "ui_hover_direction", "services_custom")) && ($this->arrParamValues[$key] != ""))
				   $configValue .= $value . ":" . $this->arrParamValues[$key] . "," .  PHP_EOL;
		   elseif(in_array($value, array("ui_click", "data_track_clickback", "ui_use_css", )) && ($this->arrParamValues[$key] != ""))
				   $configValue .= "true" == $this->arrParamValues[$key]? $value . ":true," . PHP_EOL : (("ui_use_css" == $value || "data_track_clickback" == $value) ? $value . ":false," . PHP_EOL : "");
		}
		return $configValue;
    }

	/**
	 *	Gets the current page protocol
	 *
	 * @return void
	 */
    private function setPageProtocol()
    {
    	$arrVals = explode(":", $this->baseURL);
		$this->pageProtocol = $arrVals[0];
    }

	/**
	 * Setting the base url
	 *
	 * @return void
	 */
    private function setBaseURL(){
	    $uri = JURI::getInstance();
        $this->baseURL = $uri->toString(array('scheme', 'host', 'port'));
    }
    
	/**
	 * Appending addthis main script to the head
	 *
	 * @return void
	 */
    private function appendAddThisScript(){
    	
    	$doc = JFactory::getDocument();
		$doc->addCustomTag('<script type="text/javascript" src="'. $this->pageProtocol . '://s7.addthis.com/js/'.$this->arrParamValues["menu_version"].'/addthis_widget.js"></script>');    	
    }
	
	/**
     * ButtonSet
     *
     * Return which style is selected.
     *
     * @return string returns the toolbox html
     */
    private function getButtonSet($article_url="", $article="")
    {
		switch( $this->arrParamValues["button_style"] ){
        	
        	case 'style_1':
        		$buttonValue = '<div class="addthis_toolbox addthis_default_style " '. $article_url .'>
									<a class="addthis_button_preferred_1"></a>
									<a class="addthis_button_preferred_2"></a>
									<a class="addthis_button_preferred_3"></a>
									<a class="addthis_button_preferred_4"></a>
									<a class="addthis_button_compact"></a>
									<a class="addthis_counter addthis_bubble_style"></a>
								</div>';
        		break;
        		
        	case 'style_2':
        		$buttonValue = '<div class="addthis_toolbox addthis_default_style " '. $article_url .'>
									<a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
									<a class="addthis_button_tweet"></a>
									<a class="addthis_button_pinterest_pinit"></a>
									<a class="addthis_counter addthis_pill_style"></a>
								</div>';
				break;
			
        	case 'style_4':
        		$buttonValue = '<div class="addthis_toolbox addthis_default_style " '. $article_url .'>
									<a href="http://www.addthis.com/bookmark.php?v=250&amp;pubid=xa-4f0d95b663c861f6" class="addthis_button_compact">Share</a>
									<span class="addthis_separator">|</span>
									<a class="addthis_button_preferred_1"></a>
									<a class="addthis_button_preferred_2"></a>
									<a class="addthis_button_preferred_3"></a>
									<a class="addthis_button_preferred_4"></a>
								</div>';
        		break;
        		
			case 'style_5':
        		$buttonValue = '<div class="addthis_toolbox addthis_default_style " '. $article_url .'>
									<a href="http://www.addthis.com/bookmark.php?v=250&amp;pubid=xa-4f0d960e09c42ec4" class="addthis_button_compact">Share</a>
								</div>';
        		break;
        		
        	case 'style_6':
        		$buttonValue = '<div class="addthis_toolbox addthis_default_style " '. $article_url .'>
									<a class="addthis_counter addthis_pill_style"></a>
								</div>';
        		break;
        		
			case 'style_7':
        		$buttonValue = '<div class="addthis_toolbox addthis_default_style " '. $article_url .'>
									<a class="addthis_counter"></a>
								</div>';
        		break;
        		        		
			case 'style_8':
        		$buttonValue = $this->prepareCustomCode($article);
        		break;
        					
        	default :
        		$buttonValue = '<div class="addthis_toolbox addthis_default_style addthis_32x32_style "'. $article_url .'>
									<a class="addthis_button_preferred_1"></a>
									<a class="addthis_button_preferred_2"></a>
									<a class="addthis_button_preferred_3"></a>
									<a class="addthis_button_preferred_4"></a>
									<a class="addthis_button_compact"></a>
									<a class="addthis_counter addthis_bubble_style"></a>
								</div> ';
        		break;        		
        }
        
        return $buttonValue;
    }

	/**
	 * Adding inline sharing to custom code
	 *
	 * @param object article
	 * @return string custom code with inline sharing parameters
	 */
    private function prepareCustomCode($article)
    {
		$userEnteredCode = $this->arrParamValues["custom_button_code"];
		$modifiedCode = preg_replace("[<script[^>]*?.*?</script>]", "", $userEnteredCode);
		if (strpos($modifiedCode, 'addthis_toolbox') !== false) {
			$offset = 0;
			do {
				$searchAgain = false;
				$divTagStart = strpos($modifiedCode, '<div', $offset);
				if ($divTagStart !== false) {
					$divTagEnd = strpos($modifiedCode, '>', $divTagStart);
					if ($divTagEnd !==  false) {
						$length = $divTagEnd - $divTagStart + 1;
						$divOpeningTag = substr($modifiedCode, $divTagStart, $length);
						if (strpos($divOpeningTag, 'addthis_toolbox') !== false) {
							$extraAttributes = ' addthis:url="' . urldecode($this->getArticleUrl($article)) . '"';
							$extraAttributes .= ' addthis:title="' . htmlspecialchars($article->title, ENT_QUOTES) . '"';
							$newDivOpeningTag = substr($divOpeningTag, 0, -1) . $extraAttributes . '>';
							$modifiedCode = str_replace($divOpeningTag, $newDivOpeningTag, $modifiedCode);
						}
						else {
							$offset = $divTagEnd;
							$searchAgain = true;
						}
					}
				}
			} while (!empty($searchAgain));
		}
		return $modifiedCode;
    }      
}