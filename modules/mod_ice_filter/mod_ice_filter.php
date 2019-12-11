<?php
/**
 * IceFilter Extension for Joomla 3.0 By IceTheme
 * 
 * 
 * @copyright	Copyright (C) 2008 - 2012 IceTheme.com. All rights reserved.
 * @license		GNU General Public License version 2
 * 
 * @Website 	http://www.icetheme.com/joomla-extensions/icefilter
 * @Support 	http://www.icetheme.com/Forums/IceFilter/
 *
 */

/* no direct access*/
defined('_JEXEC') or die;
if(!defined("DS")){
	define("DS", DIRECTORY_SEPARATOR);
}

if( !defined('PhpThumbFactoryLoaded') ) {
  require_once dirname(__FILE__).DS.'libs'.DS.'phpthumb'.DS.'ThumbLib.inc.php';
  define('PhpThumbFactoryLoaded',1);
}

// Include the syndicate functions only once
require_once dirname(__FILE__).DS.'helper.php';

$list = modicefilter::getList( $params );

$themeClass 								= $params->get( 'theme' , '');
$class 									 	= !$params->get( 'navigator_pos', 0 ) ? '':'ice-'.$params->get( 'navigator_pos', 0 );
$theme		   							 	=  $params->get( 'theme', '' );
$style           							= $params->get('style', 'default');
$isThumb       							    = $params->get( 'auto_renderthumb',1);
$itemContent							    = $isThumb==1?'desc-image':'introtext';
$main_width   								= $params->get('main_width', '200');
$item_width   								= $params->get('item_width', '200');
$image_height   							= $params->get('main_height', '200');
$animationspeed   							= $params->get('animationspeed', '600');
$istruncate       							= $params->get( 'istruncate',0);


/*PrettyPhoto Gallery */
$prettytheme           						= $params->get('prettyphoto_theme', 'pp_default');
$prettyautoplay           					= $params->get('prettyphtoo_autoplay', 'true');
$showtitle           						= $params->get('prettyphtoo_showtitle', 'true');
$shownavthumb           					= $params->get('prettyphtoo_thumbnav', 'true');

/* Layout Vars */
$layout_span          						= $params->get('layout_span', 'columns-4');


/*Paging*/
$maxPages									= (int)$params->get( 'max_items_per_page', 3 );
$pages 										= array_chunk( $list, $maxPages  );
$totalPages 								= count($pages);

// calculate width of each row.
$item_heading 								= $params->get('item_heading',"3");
$item_layout 								= "_items";


/*End Paging*/
$itemLayoutPath 							= modicefilter::getLayoutByTheme($module, $theme, $item_layout);



// load custom theme
	if( $theme && $theme != -1 ) {
		require( modicefilter::getLayoutByTheme($module, $theme) );
	} 
	else {
		require( JModuleHelper::getLayoutPath($module->module) );
	}
	
	modicefilter::loadMediaFiles( $params, $module, $theme );
	
?>

<script type="text/javascript">
 
 <?php if ($params->get('loading_animation',1)) : ?>
 jQuery(window).load (function () { 
      jQuery('#ice_filter_overlay').removeClass('ice_filter_overlay')
  });
 <?php	endif; ?>

// Remove Duplicate li from the foreach
var dublicate_title='';jQuery('#ice_filter_nav_<?php echo $module->id;?> li').each(function(){
    var see=jQuery(this).text();
    if(dublicate_title.match(see)){
        jQuery(this).remove();}
    else{
        dublicate_title=dublicate_title+jQuery(this).text();
        }
    });
	
	
/* begin quickstand */	
jQuery(window).load(function(){
	
	<?php if ($params->get('enable_prettyphoto')) : ?>
	// Initialize prettyPhoto plugin
	jQuery("#ice_filter_items_<?php echo $module->id;?> a[rel^='prettyPhoto']").prettyPhoto({
		theme:'<?php echo $prettytheme;?>', 
		autoplay_slideshow: <?php echo $prettyautoplay;?>, 
		overlay_gallery: <?php echo $shownavthumb;?>, 
		show_title: <?php echo $showtitle;?>,
	});
	<?php	endif; ?>

	// Clone portfolio items to get a second collection for Quicksand plugin
	var $portfolioClone = jQuery(".ice_filter_items").clone();
	
	// Attempt to call Quicksand on every click event handler
	jQuery("#ice_filter_nav_<?php echo $module->id;?> a").click(function(e){
		
		jQuery("#ice_filter_nav_<?php echo $module->id;?> li").removeClass("current");	
		
		// Get the class attribute value of the clicked link
		var $filterClass = jQuery(this).parent().attr("class");

		if ( $filterClass == "all" ) {
			var $filteredPortfolio = $portfolioClone.find("div");
		} else {
			var $filteredPortfolio = $portfolioClone.find("div[data-type~=" + $filterClass + "]");
		}
		
		// Call quicksand
		jQuery("#ice_filter_items_<?php echo $module->id;?> .row-fluid").quicksand( $filteredPortfolio, { 
			duration: <?php echo $animationspeed;?>, 
			retainExisting:true,
			useScaling : false,
			easing: 'swing', 
			adjustHeight: 'dynamic',
			adjustWidth: 'auto'
		}, function(){
			
		});
		
			<?php if ($params->get('enable_prettyphoto')) : ?>
			/* pretty photo */
			jQuery("#ice_filter_items_<?php echo $module->id;?> a[rel^='prettyPhoto']").prettyPhoto({
				theme:'<?php echo $prettytheme;?>', 
				autoplay_slideshow: <?php echo $prettyautoplay;?>, 
				overlay_gallery: <?php echo $shownavthumb;?>, 
				show_title: <?php echo $showtitle;?>
			});
			<?php	endif; ?>

		jQuery(this).parent().addClass("current");		

		// Prevent the browser jump to the link anchor
		e.preventDefault();
	})
})
</script>
