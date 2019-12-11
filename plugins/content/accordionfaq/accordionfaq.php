<?php
// no direct access
defined('_JEXEC') or die;

jimport( 'joomla.plugin.plugin' );
jimport( 'joomla.filesystem.file');
jimport( 'joomla.utilities.string');
jimport( 'joomla.language.helper');
jimport( 'joomla.environment.browser');

/**
 * AccordionFAQ Content Plugin
 *
 */
class plgContentAccordionfaq extends JPlugin
{

	/**
	 * Constructor
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}


	/**
	 * onContentPrepare
	 *
	 * Method is called by the view
	 *
	 * @param	string	The context of the content being passed to the plugin.
	 * @param	object	The article object.  Note $article->text is also available
	 * @param	object	The article params
	 * @param	int		The 'page' number
	 */
	function onContentPrepare( $context, &$article, &$params, $page=0)
	{
		$plugin			= JPluginHelper::getPlugin('content', 'accordionfaq');
		$pluginParams 	= new JRegistry;
		$pluginParams->loadString($plugin->params);

		$this->processContent( $article->text, $pluginParams );
	}


	function processContent( &$content, &$params )
	{

		if ( ! isset($content)
			||! preg_match_all( "/{\s*accordionfaq\s*.*?}/is", $content, $matches ))
		{
			return false;
		}

		$count = count($matches[0]);

		if ($count)
		{
			for ($i = 0; $i < $count; $i++)
			{
				$matchline = str_replace( "&nbsp;", ' ', $matches[0][$i]);
				$matchline = html_entity_decode( $matchline, ENT_QUOTES, 'UTF-8' );
				$match = preg_replace( '/{\s*accordionfaq\s*/i', '', $matchline );
				$match = str_replace( '}', '', $match );
				$match = preg_replace( "/\s/s", " ", $match );
				$match = trim( $match );
				$parray = array();
				while (preg_match( "/([ ]*)([A-Za-z0-9]*)([ ]*)=([ ]*)\"([^\"]+)\"/i", $match, $p ) > 0
					||preg_match( "/([ ]*)([A-Za-z0-9]*)([ ]*)=([ ]*)'([^']+)'/i", $match, $p ) > 0
					||preg_match( "/([ ]*)([A-Za-z0-9]*)([ ]*)=([ ]*)\|([^\|]+)\|/i", $match, $p ) > 0
					||preg_match( "/([ ]*)([A-Za-z0-9]*)([ ]*)=([ ]*)([^ ,]+)([ ,]*)/i", $match, $p ) > 0
					)
				{
					$parray[JString::strtolower($p[2])] = $p[5];
					$match = str_replace( $p[0], '', $match);
				}
				if (!isset($parray['echo']))
				{
					$paramArray = $params->toArray();
					$plgparams = new plgAccordionfaqParams();
					$plgparams->init( $paramArray, $parray );
					$output = $this->_doOutput( $article, $plgparams, $matchline );
					$content = str_replace( $matches[0][$i], $output, $content );
					continue;
				}
				if (preg_match( "/([ ]*)(echo)([ ]*)=([ ]*)([^ ,}]+)([ ,]*)/i", $matchline, $p ) > 0)
				{
					$match = str_replace( $p[0], '', $matchline);
					$content = str_replace( $matches[0][$i], $match, $content );
				}
			}
			return true;
		}
		return false;
	}

	protected function _doOutput( &$article, &$params, &$faqline )
	{
		$html = "";
		$document = JFactory::getDocument();

		if (! $this->_editParamNames( $params, $html))
		{
			return $this->_formattedError( $html, $faqline );
		}
		$cssfile = "plugins/content/accordionfaq/css/accordionfaq.css";
		$faqlinks = $params->get("faqlinks","");
		if (strtolower($faqlinks) == "space")
		{
			$faqlinks = "&nbsp;";
		}
		if (! $this->_editParamValue( "faqid", $params, "accordion1", $faqid, $html))
		{
			return $this->_formattedError( $html, $faqline );
		}
		if (! $this->_editParamValue( "faqclass", $params, "lightnessfaq defaulticon headerbackground headerborder contentbackground contentborder round5", $faqclass, $html))
		{
			return $this->_formattedError( $html, $faqline );
		}
		$header = $params->get("header",'h3');
		if (! $this->_editTrueFalse( "autoheight", $params, "0", $autoheight, $html))
		{
			return $this->_formattedError( $html, $faqline );
		}
		if (! $this->_editTrueFalse( "autonumber", $params, "0", $autonumber, $html))
		{
			return $this->_formattedError( $html, $faqline );
		}
		if (! $this->_editTrueFalse( "alwaysopen", $params, "0", $alwaysopen, $html))
		{
			return $this->_formattedError( $html, $faqline );
		}
		if (! $this->_editTrueFalse( "openmultiple", $params, "0", $openmultiple, $html))
		{
			return $this->_formattedError( $html, $faqline );
		}
		if (! $this->_editTrueFalse( "scrollonopen", $params, "0", $scrollonopen, $html))
		{
			return $this->_formattedError( $html, $faqline );
		}
		if (! $this->_editTrueFalse( "warnings", $params, "1", $warnings, $html))
		{
			return $this->_formattedError( $html, $faqline );
		}
		if (! $this->_editTrueFalse( "usedynamiccssload", $params, "1", $usedynamiccssload, $html))
		{
			return $this->_formattedError( $html, $faqline );
		}
		if (! $this->_editTrueFalse( "keyaccess", $params, "1", $keyaccess, $html))
		{
			return $this->_formattedError( $html, $faqline );
		}
		if (! $this->_editEvent( "event", $params, "click", $event, $html))
		{
			return $this->_formattedError( $html, $faqline );
		}
		if (! $this->_editAnimation( "animation", $params, "none", $animation, $html))
		{
			return $this->_formattedError( $html, $faqline );
		}
		$active = $params->get("active","");
		if (! $this->_editNumeric( "scrolltime", $params, 1000, $scrolltime, $html))
		{
			return $this->_formattedError( $html, $faqline );
		}
		$scrolltime = max( $scrolltime, 1);
		if (! $this->_editNumeric( "scrolloffset", $params, 0, $scrolloffset, $html))
		{
			return $this->_formattedError( $html, $faqline );
		}

		$activearr[0] = "false";
		$faqitem = JRequest::getString( 'faqitem', '' );

		if ( $active != "" && $faqitem == "")
		{
			$activearr = explode(',',$active );
			for( $i = 0; $i < count($activearr); $i++)
			{
				$activearr[$i] = JString::trim( $activearr[$i]);
				if (! is_numeric($activearr[$i]))
				{
					$activearr[$i] = "'#".$activearr[$i]."'";
				}
			}
		}
		if ($faqitem != "")
		{
			$faqitems = explode(',', $faqitem);
			$i = 0;
			do
			{
				$faqitems[$i] = JString::trim( $faqitems[$i] );
				if (JString::strpos( $faqitems[$i], $faqid ) === 0)
				{
					$faqitemtarget = JString::substr( $faqitems[$i], JString::strlen( $faqid ));
					if ($faqitemtarget != "")
					{
						if (is_numeric($faqitemtarget))
						{
							$faqitemid = (int)$faqitemtarget;
							$activearr[$i] = $faqitemid;
							$jumpto = $faqitems[$i];
						}
						else
						{
							$activearr[$i] = "'#".$faqitemtarget."'";
							$jumpto = $faqitemtarget;
						}
					}
					else
					{
						$jumpto = $faqitem;
					}
				}
				$i++;
			}
			while(! isset($jumpto) && $i < count($faqitems) );
		}

		$printfaq = JRequest::getString( "print", 'false');
		if ($printfaq == "1")
		{
			if (isset( $jumpto ))
			{
				unset( $jumpto );
			}
			$animation = 'false';
			$printfaq = 'true';
		}
		else
		{
			$printfaq = 'false';
		}
		$browser	= JBrowser::getInstance();
		$isIE6		= false;

		if ($browser->getBrowser() == "msie" && $browser->getMajor() <= 6)
		{
			$isIE6 = true;
			$ie6css = JPATH_BASE . DIRECTORY_SEPARATOR . $cssfile;
			$ie6css = JPath::clean( $ie6css, DIRECTORY_SEPARATOR );
			if (JString::substr($ie6css, JString::strlen( $ie6css ) - 4, 4) == '.css')
			{
				$ie6css = JString::substr_replace($ie6css, '-ie6.css', JString::strlen( $ie6css ) - 4, 4 );
			}
			if (JFile::exists($ie6css))
			{
				$styledata = JFile::read($ie6css);
				$newtext = preg_replace( "/src=( )*'( )*([^' ]+)'/i", "src='" . JURI::root(true) . "\\3" . "'", $styledata );
				$newtext = preg_replace( "/url\(( )*'( )*([^' ]+)'/i", "url('" . JURI::root(true) . "\\3" . "'", $newtext );
				$document->addStyleDeclaration($newtext);
			}
		}
		$document->addStyleSheet( JURI::root(true)."/".$cssfile );

		$cssbase = JPATH_BASE . DIRECTORY_SEPARATOR . $cssfile;
		$cssbase = JPath::clean( $cssbase, DIRECTORY_SEPARATOR );
		$cssfilename = JFile::getName( $cssbase );
		$cssbase = str_replace( $cssfilename, '', $cssbase );
		$faqbase = str_replace( $cssfilename, '', $cssfile );
		$faqclassarray = preg_split( "/[\s]+/", $faqclass );
		for($i = 0; $i < count($faqclassarray); $i++)
		{
			if (preg_match("/(.*)faq$/i", $faqclassarray[$i], $match ))
			{
				$faqfile = $cssbase.$faqclassarray[$i].".css";
				if (JFile::exists($faqfile))
				{
					if ($usedynamiccssload == 'tru')
					{
						$document->addStyleSheet( JURI::root(true)."/".$faqbase."css.php?id=".$faqid."&amp;faq=".$faqclassarray[$i] );
					}
					else
					{
						$document->addStyleSheet( JURI::root(true)."/".$faqbase.$faqclassarray[$i].".css" );
					}
					if ($isIE6)
					{
						$ie6css = $cssbase.$faqclassarray[$i]."-ie6.css";
						if (JFile::exists($ie6css))
						{
							$styledata = JFile::read($ie6css);
							$newtext = preg_replace( "/src=( )*'( )*([^' ]+)'/i", "src='" . JURI::root(true) . "\\3" . "'", $styledata );
							$newtext = preg_replace( "/url\(( )*'( )*([^' ]+)'/i", "url('" . JURI::root(true) . "\\3" . "'", $newtext );
							$newtext = preg_replace( "/\.".$faqclassarray[$i]."/", "#".$faqid.".".$faqclassarray[$i], $newtext );
							$document->addStyleDeclaration($newtext);
						}
					}
				}
				else
				{
					if ($warnings == 'tru')
					{
						$warntext = "WARNING: CSS file for faqclass ".$faqclassarray[$i]." does not exist (".$faqfile.").";
						$html .= $this->_formattedError( $warntext, $faqline );
					}
				}
			}
			else
			{
				if ($warnings == 'tru')
				{
					if (! $this->_editFaqClass( $faqclassarray[$i], $warntext))
					{
						$html .= $this->_formattedError( $warntext, $faqline );
					}
				}
			}
		}

		$includejquery = $params->get('includejquery', 1);
		if ($includejquery != 0)
		{
			$jquerynoconflict = $params->get('jquerynoconflict', 1);
			if ($jquerynoconflict == 1)
			{
				JHTML::_('jquery.framework' );
			}
			else
			{
				JHTML::_('jquery.framework', false );
			}
		}

		JHTML::_('script', 'plugins/content/accordionfaq/js/preparefaq.js' );
		if ($openmultiple == 'tru' && $animation !== 'false')
		{
			$duration = 300;
			$easing = 'swing';
			if ($animation == "'slide'")
			{
				$duration = 300;
				$easing = 'swing';
			}
			else
			if ($animation == "'easeslide'")
			{
				$duration = 700;
				$easing = 'easeinout';
			}
			else
			if ($animation == "'bounceslide'")
			{
				$duration = 1000;
				$easing = 'bounceout';
			}
		}

		$script  = "// <!--\n";
		$script .= "preparefaq.onFunctionAvailable( 'jQuery', 300, function() {\n";
		$script .= "	preparefaq.setjQuery();\n";
		if ($printfaq === 'false' && $openmultiple == 'fals')
		{
			$script .= "	preparefaq.loadScript( '". JURI::root(true) . "/plugins/content/accordionfaq/js/jquery.accordionfaq.js' );\n";
		}
		if ($animation !== "false")
		{
			$script .= "	preparefaq.loadScript( '". JURI::root(true) . "/plugins/content/accordionfaq/js/jquery.easing.js' );\n";
		}
		$script .= "/***********************************************\n";
		$script .= "* Scrolling HTML bookmarks- © Dynamic Drive DHTML code library (www.dynamicdrive.com)\n";
		$script .= "* This notice MUST stay intact for legal use\n";
		$script .= "* Visit Project Page at http://www.dynamicdrive.com for full source code\n";
		$script .= "***********************************************/\n";
		$script .= "	preparefaq.loadScript( '". JURI::root(true) . "/plugins/content/accordionfaq/js/bookmarkscroll.js' );\n";
		$script .= "	preparefaq.getjQuery()(document).ready(function(){ \n";
		$script .= "		preparefaq.exec( { \n";
        $script .= "		    id: '".$faqid."'\n";
		$script .= "		  , header: '".$header."'\n";
	    $script .= "		  , alwaysopen: ".$alwaysopen."e\n";
		$script .= "		  , autonumber: ".$autonumber."e\n";
		$script .= "		  , keyaccess: ".$keyaccess."e\n";
 		$script .= "		  , print: ".$printfaq."\n";
		$script .= "		  , scrolltime: ".$scrolltime."\n";
		$script .= "		  , scrolloffset: ".$scrolloffset."\n";
		if ($faqlinks != "")
		{
			$script .= "		  , faqlinks: '".$faqlinks."'\n";
		}
		$script .= "		  , scrollonopen: ".$scrollonopen."e\n";
		if ($openmultiple == 'tru')
		{
			$script .= "		  , event: '".$event."'\n";
			$script .= "		  , onevent: function() { \n";
			if ($animation === 'false')
			{
				$script .= "				preparefaq.getjQuery()(this).toggleClass('selected').next().toggle( 1, preparefaq.accordionChange );\n";
			}
			else
			{
				$script .= "				preparefaq.getjQuery()(this).toggleClass('selected').next().slideToggle( ".$duration.", '".$easing."', preparefaq.accordionChange );\n";
			}
			$script .= "				return true;\n";
			$script .= "			}\n";
		}
		$script .= "		} );\n";
		$script .= "		preparefaq.onFunctionAvailable( 'bookmarkscroll.init', 300, function() {\n";
		$script .= "				bookmarkscroll.init();\n";
		$script .= "		});\n";
		if ($openmultiple == 'tru')
		{
			$script .= "		preparefaq.getjQuery()('#".$faqid."').addClass('selected');\n";
			if ($activearr[0] !== 'false' && $printfaq === 'false')
			{
				if (count($activearr) == 1 && $activearr[0] == "'#*'")
				{
					$script .= "		preparefaq.getjQuery()('".$header.".accordionfaqheader.".$faqid."').toggleClass('selected').next().toggle();\n";
				}
				else
				{
					$script .= "		var target;\n";
					for ( $i = 0; $i < count($activearr); $i++)
					{
						if (is_numeric($activearr[$i]))
						{
							$script .= "		target = preparefaq.getjQuery()('#".$faqid.$activearr[$i]."');\n";
						}
						else
						{
							$activeval = str_replace( "'", '', $activearr[$i] );
							$script .= "		target = preparefaq.getjQuery()('".$activeval."');\n";
						}
						$script .= "		if (typeof(target) !== 'undefined') {\n";
						$script .= "			target.toggleClass('selected').next().toggle();\n";
						$script .= "		};\n";
					}
				}
			}
		}
		else
		{
			$script .= "		preparefaq.onFunctionAvailable( 'preparefaq.getjQuery().fn.accordionfaq', 300, function() {\n";
			$script .= "			preparefaq.getjQuery()('#".$faqid."').accordionfaq( { \n";
			$script .= "				  header: '".$header.".accordionfaqheader.".$faqid."'\n";
			$script .= "				, autoheight: ".$autoheight."e\n";
			$script .= "				, alwaysOpen: ".$alwaysopen."e\n";
			$script .= "				, active: ".$activearr[0]."\n";
			$script .= "			 	, animated: ".$animation."\n";
			$script .= "			 	, event: '".$event."'\n";
			$script .= "			});\n";
			$script .= "			preparefaq.getjQuery()('#".$faqid."').bind( 'change.faq-accordion', preparefaq.accordionChangeUI );\n";
			$script .= "		});\n";
		}
		if (isset($jumpto))
		{
			$script .= "		preparefaq.onIdAvailable( '".$jumpto."', 300, function() {\n";
			$script .= "			preparefaq.onFunctionAvailable( 'bookmarkscroll.scrollTo', 300, function() {\n";
			$script .= "				preparefaq.jumpToFaqItem( '".$jumpto."' );\n";
			$script .= "			});\n";
			$script .= "		});\n";
		}
		$script .= "	});\n";
		$script .= "});\n";
		$script .= "// -->\n";
		$document->addScriptDeclaration( $script );

		$html 	.= "<div id=\"".$faqid."\" class=\"accordionfaq ".$faqclass."\">";
		$html 	.= "<p></p>";
		$html 	.= "</div>";
		return $html;
	}

	protected function _editNumeric( $paramname, &$params, $default, &$value, &$errortext )
	{
		$paramvalue = $params->get( $paramname, $default );
		if (is_numeric($paramvalue))
		{
			$value = (int)$paramvalue;
			return true;
		}
		$errortext = "ERROR: Parameter ".$paramname."='".$paramvalue."' is invalid. ";
		$errortext .= "'".$paramvalue."' must be a number.";
		return false;
	}

	protected function _editTrueFalse( $paramname, &$params, $default, &$value, &$errortext )
	{
		$valid['true'] = "tru";
		$valid['false'] = "fals";
		$valid['yes'] = "tru";
		$valid['no'] = "fals";
		$valid['on'] = "tru";
		$valid['off'] = "fals";
		$valid['t'] = "tru";
		$valid['f'] = "fals";
		$valid['y'] = "tru";
		$valid['n'] = "fals";
		$valid['1'] = "tru";
		$valid['0'] = "fals";
		return $this->_editParam( $paramname, $params, $default, $valid, $value, $errortext );
	}

	protected function _editEvent( $paramname, &$params, $default, &$value, &$errortext )
	{
		$valid['click'] = "click";
		$valid['dblclick'] = "dblclick";
		$valid['mouseover'] = "mouseover";
		return $this->_editParam( $paramname, $params, $default, $valid, $value, $errortext );
	}

	protected function _editParamNames( &$params, &$errortext )
	{
		$valid['faqid'] = "1";
		$valid['cssfile'] = "1";
		$valid['includejquery'] = "1";
		$valid['jquerynoconflict'] = "1";
		$valid['faqclass'] = "1";
		$valid['header'] = "1";
		$valid['autonumber'] = "1";
		$valid['autoheight'] = "1";
		$valid['alwaysopen'] = "1";
		$valid['active'] = "1";
		$valid['animation'] = "1";
		$valid['event'] = "1";
		$valid['scrolltime'] = "1";
		$valid['scrolloffset'] = "1";
		$valid['warnings'] = "1";
		$valid['usedynamiccssload'] = "1";
		$valid['faqlinks'] = "1";
		$valid['keyaccess'] = "1";
		$valid['openmultiple'] = "1";
		$valid['scrollonopen'] = "1";
		$paramnamesarray = $params->getArray();
		$paramnames = array_keys( $paramnamesarray );
		foreach( $paramnames as $paramname)
		{
			if (! isset( $valid[strtolower($paramname)]))
			{
				$validvalues = array_keys( $valid );
				$validlist = implode( $validvalues, ", ");
				$errortext = "ERROR: Parameter '".$paramname."' is invalid. ";
				$errortext .= "Acceptable values for '".$paramname."' are ".$validlist.".";
				return false;
			}
		}
		return true;
	}

	protected function _editParamValue( $paramname, &$params, $default, &$value, &$errortext )
	{
		$value = $params->get( $paramname, $default );
		if ( strlen( $value ) != strcspn( $value, "\"|'!~`@#$%^&()+{}[]\\/?<>;:.,?"))
		{
			$errortext = "ERROR: Value for ".$paramname." parameter '".$value."' is invalid.";
			return false;
		}
		return true;
	}

	protected function _editFaqClass( $classname, &$errortext )
	{
		$valid['round3'] = "1";
		$valid['round5'] = "1";
		$valid['round7'] = "1";
		$valid['round9'] = "1";
		$valid['headerbackground'] = "1";
		$valid['headerborder'] = "1";
		$valid['contentbackground'] = "1";
		$valid['contentborder'] = "1";
		$valid['border'] = "1";
		$valid['bcolor'] = "1";
		$valid['defaulticon'] = "1";
		$valid['onoff'] = "1";
		$valid['plus'] = "1";
		$valid['plus2'] = "1";
		$valid['plus3'] = "1";
		$valid['arrow'] = "1";
		$valid['greenarrow'] = "1";
		$valid['orangearrow'] = "1";
		$valid['orangearrow2'] = "1";
		$valid['help'] = "1";
		$valid['help2'] = "1";
		$valid['power'] = "1";
		$valid['check'] = "1";
		$valid['rtl'] = "1";
		$valid['alignright'] = "1";
		$valid['alignleft'] = "1";
		$valid['aligncenter'] = "1";
		if (! isset( $valid[strtolower($classname)]))
		{
			$validvalues = array_keys( $valid );
			$validlist = implode( $validvalues, ", ");
			$errortext = "WARNING: faqclass '".$classname."' is invalid. ";
			$errortext .= "Acceptable values for '".$classname."' are ".$validlist.".";
			return false;
		}
		return true;
	}

	protected function _editAnimation( $paramname, &$params, $default, &$value, &$errortext )
	{
		$valid['none'] = "false";
		$valid['slide'] = "'slide'";
		$valid['easeslide'] = "'easeslide'";
		$valid['bounceslide'] = "'bounceslide'";
		return $this->_editParam( $paramname, $params, $default, $valid, $value, $errortext );
	}

	protected function _editParam( $paramname, &$params, $default, &$valid, &$value, &$errortext )
	{
		$paramvalue = $params->get( $paramname, $default );
		if (isset($valid[strtolower($paramvalue)]))
		{
			$value = $valid[strtolower($paramvalue)];
			return true;
		}
		$validvalues = array_keys( $valid );
		$validlist = implode( $validvalues, ", ");
		$errortext = "ERROR: Parameter ".$paramname."='".$paramvalue."' is invalid. ";
		$errortext .= "Acceptable values for '".$paramvalue."' are ".$validlist.".";
		return false;
	}

	protected function _formattedError( $errortext, $faqline )
	{
		$text = "<p style=\"color: red;background-color: yellow;visibility: visible\">\n";
		$text .= "<b>".htmlspecialchars($faqline)."<br/>";
		$text .= "Accordionfaq plugin,  ".$errortext."</b>";
		$text .= "</p>\n";
		return $text;
	}

}

class plgAccordionfaqParams
{
	var $_params = array();

	function init( &$params1, &$params2 )
	{
		$this->_params = array_merge( $params1, $params2 );
	}

	function get( $name, $default = '' )
	{
		if (isset($this->_params[$name]) && $this->_params[$name] != "")
		{
			return $this->_params[$name];
		}
		return $default;
	}
	function getArray()
	{
		return $this->_params;
	}
}
?>