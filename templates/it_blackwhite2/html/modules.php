<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  Templates.protostar
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * This is a file to add template specific chrome to module rendering.  To use it you would
 * set the style attribute for the given module(s) include in your template to use the style
 * for each given modChrome function.
 *
 * eg.  To render a module mod_test in the submenu style, you would use the following include:
 * <jdoc:include type="module" name="test" style="submenu" />
 *
 * This gives template designers ultimate control over how modules are rendered.
 *
 * NOTICE: All chrome wrapping methods should be named: modChrome_{STYLE} and take the same
 * two arguments.
 */


/*
 * "general" module
 */

function modChrome_general($module, &$params, &$attribs)
{
	if ($module->content) {
		
		echo "<div class=\"general_module general_module_" . htmlspecialchars($params->get('moduleclass_sfx')). " \">";
		
		if ($module->showtitle)
		{
			echo "<h3 class=\"general_module_heading\"><span>" . $module->title . "</span></h3>";
		}
		echo $module->content;
		echo "</div>";
	}
}


function modChrome_block($module, &$params, &$attribs)
{
	if(strpos($module->title,"|") !== false){
		$titleArray = explode("|",$module->title);		
		$module->title = "";
		for($i=0;$i<count($titleArray);$i++){
			if($i){
				$module->title .= "<span>".$titleArray[$i]."</span>";
			}else{
				$module->title .= $titleArray[$i];
			}
		}		
	}
	if (!empty ($module->content)) : ?>
         
       <div class="moduletable<?php echo $params->get('moduleclass_sfx'); ?>">
        
			<?php if ($module->showtitle) : ?>
				<h3 class="mod-title"><?php echo $module->title; ?></h3>
			<?php endif; ?>
        	
             <div class="moduletable_content clearfix">
			 <?php echo $module->content; ?>
             </div>
                
		</div>
	<?php endif;
}

/*
 * "Footer Menu" position
 */
function modChrome_footermenu($module, &$params, &$attribs)
{
	if(strpos($module->title,"|") !== false){
		$titleArray = explode("|",$module->title);		
		$module->title = "";
		for($i=0;$i<count($titleArray);$i++){
			if($i){
				$module->title .= "<span>".$titleArray[$i]."</span>";
			}else{
				$module->title .= $titleArray[$i];
			}
		}		
	}
	if (!empty ($module->content)) : ?>
         
       <div class="footermenu_module <?php echo $params->get('moduleclass_sfx'); ?>">
        
			<?php if ($module->showtitle) : ?>
            <div class="footermenu_title"><h3 class="mod-title"><?php echo $module->title; ?></h3></div>
            <?php endif; ?>
            
            <div class="footermenu_content">
            <?php echo $module->content; ?>
            </div>
                
		</div>
	<?php endif;
}

/*
 * "left" position
 */

function modChrome_sidebar($module, &$params, &$attribs)
{
	if(strpos($module->title,"|") !== false){
		$titleArray = explode("|",$module->title);		
		$module->title = "";
		for($i=0;$i<count($titleArray);$i++){
			if($i){
				$module->title .= "<span>".$titleArray[$i]."</span>";
			}else{
				$module->title .= $titleArray[$i];
			}
		}		
	}	
	if ($module->content) { ?>
		
		<div class="sidebar_module sidebar_module_<?php echo htmlspecialchars($params->get('moduleclass_sfx')) ?>">
        	
			     <?php if ($module->showtitle) { ?>
                  
                    <?php if ($params->get('moduleclass_sfx') == "style2" || $params->get('moduleclass_sfx') == "style3") { ?>
                    <div class="sidebar_module_heading_outter">
                    <?php } ?>
                    
                    <h3 class="sidebar_module_heading"><?php echo $module->title ?></h3>
                    
                    <?php if ($params->get('moduleclass_sfx') == "style2" || $params->get('moduleclass_sfx') == "style3") { ?>
                    </div>
                    <?php } ?>
                     
                <?php } ?>
			
                <div class="sidebar_module_content"><?php echo $module->content ?></div>
		
          </div>
	
    <?php } } 



/*
 * "iceslider" position
 */


function modChrome_slider($module, &$params, &$attribs)
{
	if(strpos($module->title,"|") !== false){
		$titleArray = explode("|",$module->title);		
		$module->title = "";
		for($i=0;$i<count($titleArray);$i++){
			if($i){
				$module->title .= "<span>".$titleArray[$i]."</span>";
			}else{
				$module->title .= $titleArray[$i];
			}
		}		
	}
	
	if ($module->content) {
		
		
		if ($module->showtitle)
		{
			echo "<div class=\"slider_heading_div\"><h3 class=\"slider_heading\">" . $module->title . "</h3></div>";
		}
		echo $module->content;

	}
}


// Promo Modules
function modChrome_promo($module, &$params, &$attribs)
{
	global $it_promo;
	
	if ($it_promo == 1) {
		$promo_width = "span12";
			
	} elseif ($it_promo == 2) {
		$promo_width = "span6";
		
	} elseif ($it_promo == 3) {
		$promo_width = "span4";
		
	} elseif ($it_promo == 4) {
		$promo_width = "span3";
		
	} elseif ($it_promo == 6) {	
		$promo_width = "span2";	
		
	} else {
		$promo_width = "span";	
	}
	
	if(strpos($module->title,"|") !== false){
		$titleArray = explode("|",$module->title);		
		$module->title = "";
		for($i=0;$i<count($titleArray);$i++){
			if($i){
				$module->title .= "<span>".$titleArray[$i]."</span>";
			}else{
				$module->title .= $titleArray[$i];
			}
		}		
	}
	if (!empty ($module->content)) : ?>
         
       <div class="moduletable <?php echo $promo_width; ?>">
        
			<?php if ($module->showtitle) : ?>
				<div class="moduletable_heading"><h3 class="mod-title"><?php echo $module->title; ?></h3></div>
			<?php endif; ?>
        	
             <div class="moduletable_content clearfix">
			 <?php echo $module->content; ?>
             </div>
                
		</div>
	<?php endif;
}



// Footer Modules
function modChrome_footer($module, &$params, &$attribs)
{
	global $it_footer;
	
	if ($it_footer == 1) {
		$footer_width = "span12";
			
	} elseif ($it_footer == 2) {
		$footer_width = "span6";
		
	} elseif ($it_footer == 3) {
		$footer_width = "span4";
		
	} elseif ($it_footer == 4) {
		$footer_width = "span3";
		
	} elseif ($it_footer == 6) {	
		$footer_width = "span2";	
		
	} else {
		$footer_width = "span";	
	}
	
	if(strpos($module->title,"|") !== false){
		$titleArray = explode("|",$module->title);		
		$module->title = "";
		for($i=0;$i<count($titleArray);$i++){
			if($i){
				$module->title .= "<span>".$titleArray[$i]."</span>";
			}else{
				$module->title .= $titleArray[$i];
			}
		}		
	}
	if (!empty ($module->content)) : ?>
         
       <div class="moduletable <?php echo $footer_width; ?>">
        
			<?php if ($module->showtitle) : ?>
				<div class="moduletable_heading"><h3 class="mod-title"><?php echo $module->title; ?></h3></div>
			<?php endif; ?>
        	
             <div class="moduletable_content clearfix">
			 <?php echo $module->content; ?>
             </div>
                
		</div>
	<?php endif;
}



?>
