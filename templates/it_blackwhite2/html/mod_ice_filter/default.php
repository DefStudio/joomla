<?php
/* no direct access*/

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
 
defined('_JEXEC') or die;
$uniqueNo=0;
?>

<div id="ice_filter_mod<?php echo $module->id;?>" class="ice_filter clearfix">
	
	<?php if ($params->get('loading_animation',1)) : ?>
    <div id="ice_filter_preload<?php echo $module->id;?>" class="ice_preload"> 
    	<div id="movingBallG">
			<div class="movingBallLineG">
			</div>
            <div id="movingBallG_1" class="movingBallG">
            </div>
		</div>
    </div>    
    <?php endif; ?>
    
	<?php if ($params->get('enable_categoryfilter',1)) : ?>
    <ul id="ice_filter_nav_<?php echo $module->id;?>" class="ice_filter_nav">
        <li class="current all"><a href="#">All</a></li>
        <?php foreach($list as $key=>$item){ ?>     
        <li class="<?php echo $item->catid; ?>"><a href="#"><?php echo $item->category_title; ?></a></li>        
        <?php }?>
    </ul>
    <?php endif; ?>
    
    <div id="ice_filter_items_<?php echo $module->id;?>" class="ice_filter_items">  

        <div class="row-fluid">
    
			<?php foreach($list as $key=>$item){ ?>
            <div data-id="id-<?php echo $uniqueNo+=1; ?>" data-type="<?php echo $item->catid; ?>" class="ice_filter_item <?php echo $layout_span;?>">
            
                <div class="ice_filter_inner">
                    
					<?php if ($params->get('link_titles', 1)) : ?>
                    <a class="ice_filter_img_link" <?php if ($params->get('enable_prettyphoto', 1)){ echo 'rel="prettyPhoto[portfolio]" href="'.$item->imgfull.'"'; } else { echo 'href="'.$item->link.'"';} ?>> 
                    <div class="ice_filter_img">
                    <?php if($item->mainImage): echo $item->mainImage; endif; ?>
                    </div>
                    </a>
                    <?php else: ?>
                    <div class="ice_filter_img">
                    <?php if($item->mainImage): echo $item->mainImage; endif; ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($params->get('display_caption', 1)) : ?>
                    <div class="ice_filter_caption">
                    
                        <?php if ($params->get('show_title', 1)) : ?>
                        <h4>                           
                            <?php if ($params->get('link_titles', 1)) : ?>
                            <a class="ice_filter_title" href="<?php echo $item->link; ?>"><?php echo $item->title; ?></a>                       		<?php else: ?>
                            <?php echo $item->title; ?>
                            <?php endif; ?>
                        </h4>
                        <?php	endif; ?>

                        <?php if ($params->get('show_description') == 1) : ?>
                        <div class="ice_filter_description">
                            <p><?php echo $item->displayIntrotext; ?></p>
                        </div>
                        <?php endif; ?>
                        
                    </div>
                    <?php	endif; ?>
                    
					<?php if ($params->get('show_readmore')) :?>
                    <p class="ice_filter_button">
                        <a class="ice_filter_buttonlink" href="<?php echo $item->link; ?>"><?php echo JText::_('MOD_CAROSUEL_READ_MORE');?></a>
                    </p>
                    <?php endif; ?>
                    
    
             	</div>        
                
            </div>
            <?php }?>
  
  		</div>
        
    </div>

</div>

<script type="text/javascript">
	jQuery(window).load (function () { 
	  jQuery('#ice_filter_preload<?php echo $module->id;?>').removeClass('ice_preload')
	});
</script>