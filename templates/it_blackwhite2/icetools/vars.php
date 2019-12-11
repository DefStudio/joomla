<?php
//  @copyright	Copyright (C) 2013 IceTheme. All Rights Reserved
//  @license	Copyrighted Commercial Software 
//  @author     IceTheme (icetheme.com)

// No direct access.
defined('_JEXEC') or die;


// Detecting Joomla Active Variables;
$document = JFactory::getDocument();
$app = JFactory::getApplication();
$sitename = $app->getCfg('sitename');
$menu = $app->getMenu();
$lang = JFactory::getLanguage();
$user = JFactory::getUser();
$pageclass = $app->getParams('com_content');

// Define Constants 
define('IT_THEME', $this->baseurl .'/templates/'. $this->template);
define('IT_THEME_DIR', JPATH_ROOT .'/templates/'. $this->template);


// Define Variables for template paramters
$it_logo 					= $this->params->get('logo');
$it_logo_img				= '<img class="logo" src="'. JURI::root() . $this->params->get('logo') .'" alt="'. $sitename .'" />';
$it_gotop				   = $this->params->get('go2top');
$it_styleswitcher		   = $this->params->get('styleswitcher');
$it_sidebar_pos    		 = $this->params->get('sidebar_position');
$it_cuosom_css			  = $this->params->get('custom_css_code');
$it_template_style		  = $this->params->get('enable_template_style');
$it_responsive        	  = $this->params->get('responsive_template');
$it_comingsoon	       	  = $this->params->get('comingsoon_enable');
$it_comingsoon_newsletter   = $this->params->get('comingsoon_newsletter');
$it_comingsoon_h2		   = $this->params->get('comingsoon_h2');
$it_comingsoon_count	    = $this->params->get('comingsoon_count');
$it_comingsoon_count_time   = $this->params->get('comingsoon_count_time');
$it_comingsoon_p		    = $this->params->get('comingsoon_p');
$it_comingsoon_social       = $this->params->get('comingsoon_social');

// Define Variables for module positions
$it_iceslideshow			= $this->countModules('iceslideshow');
$it_icecarousel    		 = $this->countModules('icecarousel');
$it_language				= $this->countModules('language');
$it_search		          = $this->countModules('search');
$it_topmenu				 = $this->countModules('topmenu');
$it_mainmenu				= $this->countModules('mainmenu');
$it_footermenu			  = $this->countModules('footermenu');
$it_sidebar    			 = $this->countModules('sidebar');
global $it_promo; 			// we use this variable on modules.php functions
$it_promo	 			   = $this->countModules('promo');
global $it_footer; 		   // we use this variable on modules.php functions
$it_footer	 			  = $this->countModules('footer');
$it_copyrightmenu			= $this->countModules('copyrightmenu');

// Define Variables for Social Icons
$it_social				  = $this->params->get('social_icons');
$it_social_facebook		 = $this->params->get('social_icon_facebook');
$it_social_facebook_user    = $this->params->get('social_icon_facebook_user');
$it_social_twitter		  = $this->params->get('social_icon_twitter');
$it_social_twitter_user     = $this->params->get('social_icon_twitter_user');
$it_social_google		   = $this->params->get('social_icon_google');
$it_social_google_user      = $this->params->get('social_icon_google_user');
$it_social_youtube		  = $this->params->get('social_icon_youtube');
$it_social_youtube_user     = $this->params->get('social_icon_youtube_user');
$it_social_linkedin		 = $this->params->get('social_icon_linkedin');
$it_social_linkedin_user    = $this->params->get('social_icon_linkedin_user');
$it_social_rss			  = $this->params->get('social_icon_rss');
$it_social_rss_link         = $this->params->get('social_icon_rss_link');


// Load Bootstrap Frameworks (loads JQuery framework as well)
JHtml::_('bootstrap.framework');

// Load the Template js File
$document->addScript(IT_THEME .'/js/template.js');

// Add sidebar class to left 
if($it_sidebar_pos == 'left') {
	$sidebar_left = 'sidebar_left';	
}

// offline page direct link
if (JRequest::getCmd("tmpl", "index") == "offline") {
	require_once(JPATH_ROOT . "/templates/" . $this->template . "/offline.php");
   
} 

// comming soon page direct link
if ((JRequest::getCmd("tmpl", "index") == "soon") || ($it_comingsoon == 1)) {
    require_once(JPATH_ROOT . "/templates/" . $this->template . "/soon.php");
	$document->addScript(IT_THEME .'/js/countdown.js');
} 


// JS for go to top link
if ($it_gotop != 0) { 
$document->addScriptDeclaration('
    jQuery(document).ready(function(){ 
			
			 jQuery(window).scroll(function(){
				if ( jQuery(this).scrollTop() > 1000) {
					 jQuery(\'#gotop\').addClass(\'gotop_active\');
				} else {
					 jQuery(\'#gotop\').removeClass(\'gotop_active\');
				}
			}); 
			
			jQuery(\'.scrollup\').click(function(){
				jQuery("html, body").animate({ scrollTop: 0 }, 600);
				return false;
			});
			
		});
');
}


// sidebar "normal" or "narrow"
if($this->params->get('sidebar_span') == 'normal') 
{
	$sidebar_span = "span4";
}
else
{
	$sidebar_span = "span3";
}

// Layout Column width		
if ($this->countModules('sidebar'))
{
	$content_span = "span8";
}
else
{
	$content_span = "span12";
}

if ($this->countModules('sidebar') and $this->params->get('sidebar_span') == 'narrow')
{
	$content_span = "span9";
}

?>
