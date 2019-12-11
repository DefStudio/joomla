<?php
/**
 * IceSlideShow Extension for Joomla by IceTheme
 * 
 * 
 * @copyright	Copyright (C) 2008 - 2013 IceTheme.com. All rights reserved.
 * @license		GNU General Public License version 2
 * 
 * @Website 	http://www.icetheme.com/Joomla-Extensions/iceslideshow.html
 * @Support 	http://www.icetheme.com/Forums/iceslideshow/
 *
 */


/* no direct access*/
defined('_JEXEC') or die;
?>

<div id="iceslideshow<?php echo $module->id;?>" class="iceslideshow carousel slide <?php echo $effect ;?> carousel-fade mootools-noconflict">
       
	<div id="ice_slideshow_preload<?php echo $module->id;?>" class="ice_preload">
    	
         <div id="movingBallG">
			<div class="movingBallLineG">
			</div>
            <div id="movingBallG_1" class="movingBallG">
            </div>
		</div>
    
    </div>
    
	<ol class="carousel-indicators">
    	<?php
		$dataslide = 0;
        foreach($list as $key=>$item){ 
			$activeclass = "";
            if($key == 0){
                $activeclass = "active";
            }
		?>
        <li data-target="#iceslideshow<?php echo $module->id;?>" data-slide-to="<?php echo $dataslide++; ?>" class="<?php echo $activeclass; ?>"></li>
       <?php } ?>
	</ol>
            
            
            
	<div class="carousel-inner">
	
		<?php
        foreach($list as $key=>$item){
            
            $activeclass = "";
            if($key == 0){
                $activeclass = "active";
            }
            
        ?>
        <div class="item <?php echo $activeclass; ?>">
   
			<?php echo $item->mainImage; ?>
					
			<?php if($params->get("display_caption", 1)): ?>	
			<div class="carousel-caption">
			 
				<?php if ($params->get('show_title', 1)) : ?>
                <h4>
                <?php if ($params->get('link_titles') == 1) : ?>
                <a href="<?php echo $item->link; ?>">
                <?php echo $item->title; ?></a>
                <?php
                else:
                    echo $item->title;
                endif;
                ?>
                </h4>
                <?php endif; ?>

				<?php if ($params->get('show_description', 1)) : ?>
                <div class="mod-description">
                    <p><?php echo $item->displayIntrotext; ?></p>
                </div>
                <?php endif; ?>
								
				<?php if ($params->get('show_readmore')) :?>
                <p class="mod-articles-category-readmore">
               		 <a class="mod-articles-category-title" href="<?php echo $item->link; ?>"><?php echo JText::_('MOD_CAROSUEL_READ_MORE'); ?></a>
                </p>
                <?php endif; ?>
							  
			</div>
			<?php endif; ?>
       			
                        
        </div>
        <?php } ?>
                    
	</div><!-- .carousel-inner -->
        
        
	<?php if($params->get("display_arrows", 1)): ?>
	<div class="iceslideshow_arrow">
          <a class="carousel-control left" href="#iceslideshow<?php echo $module->id;?>" data-slide="prev">&lsaquo;</a>
          <a class="carousel-control right" href="#iceslideshow<?php echo $module->id;?>" data-slide="next">&rsaquo;</a>
    </div>
    <?php endif; ?>
        
        
</div>

<script type="text/javascript">
	jQuery(window).load (function () { 
	  jQuery('#ice_slideshow_preload<?php echo $module->id;?>').removeClass('ice_preload')
	});
</script>


        


