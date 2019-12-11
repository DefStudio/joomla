<?php

// No direct access.
defined('_JEXEC') or die;
?>
          
<!-- social -->
<div id="social_icons">
 
    <ul>
        <?php if ($it_social_facebook == 1) { ?> 
        <li class="social_facebook">
        <a target="_blank" rel="tooltip" data-placement="top" title="" data-original-title="<?php echo JText::_('SOCIAL_FACEBOOK_TITLE'); ?>" href="http://www.facebook.com/<?php echo $it_social_facebook_user; ?>"><span>Facebook</span></a>			
        </li>				
        <?php } ?>	
    
         <?php if ($it_social_twitter == 1) { ?> 
        <li class="social_twitter">
        <a target="_blank" rel="tooltip" data-placement="top" title="" data-original-title="<?php echo JText::_('SOCIAL_TWITTER_TITLE'); ?>" href="http://www.twitter.com/<?php echo $it_social_twitter_user; ?>" ><span>Twitter</span></a>
        </li>
        <?php } ?>
		
		<?php if ($it_social_google == 1) { ?> 
        <li class="social_google">
        <a target="_blank" rel="tooltip" data-placement="top" title="" data-original-title="<?php echo JText::_('SOCIAL_GOOGLE_TITLE'); ?>" href="https://plus.google.com/<?php echo $it_social_google_user; ?>" ><span>Google</span></a>
        </li>
        <?php } ?>
        
         <?php if ($it_social_youtube == 1) { ?> 
        <li class="social_youtube">
        <a target="_blank" rel="tooltip" data-placement="top" title="" data-original-title="<?php echo JText::_('SOCIAL_YOUTUBE_TITLE'); ?>" href="http://www.youtube.com/user/<?php echo $it_social_youtube_user; ?>" ><span>Youtube</span></a>
        </li>
        <?php } ?>

         <?php if ($it_social_linkedin == 1) { ?> 
        <li class="social_linkedin">
        <a target="_blank" rel="tooltip" data-placement="top" title="" data-original-title="<?php echo JText::_('SOCIAL_LINKEDIN_TITLE'); ?>" href="http://www.linkedin.com/company/<?php echo $it_social_linkedin_user; ?>" ><span>Linkedin</span></a>
        </li>
        <?php } ?>
    
        <?php if ($it_social_rss == 1) { ?> 
        <li class="social_rss_feed">
        <a target="_blank" rel="tooltip" data-placement="top" title="" data-original-title="<?php echo JText::_('SOCIAL_RSS_TITLE'); ?>" href="<?php echo $it_social_rss_link; ?>"><span>RSS Feed</span></a>
        </li>				
        <?php } ?>
        
    </ul>

</div><!-- social -->
                 