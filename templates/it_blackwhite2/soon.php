<?php
//  @copyright	Copyright (C) 2013 IceTheme. All Rights Reserved
//  @license	Copyrighted Commercial Software 
//  @author     IceTheme (icetheme.com)

defined('_JEXEC') or die;

include_once(JPATH_ROOT . "/templates/" . $this->template . '/icetools/vars.php'); ?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>

	<jdoc:include type="head" />
    
    <?php if ($it_responsive ==1) { ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php } ?>

    <?php
    // Include CSS and JS variables 
    include_once(JPATH_ROOT . "/templates/" . $this->template . '/icetools/css.php');
    ?>
    
</head>

<body class="comingsoon_page">

	<?php if ($it_logo != "") { ?>
    <div id="logo_page">	
        <a href="<?php echo $this->baseurl ?>"><?php echo $it_logo_img; ?></a>	
    </div>
    <?php } ?>   
    

    <div id="content_page">
    
    	<h2><?php echo $it_comingsoon_h2; ?></h2>
        
        
        <?php if ($it_comingsoon_count == 1) { ?> 
		<script type="text/javascript">
        jQuery(document).ready(function(){
            jQuery("#countdown").countdown({
                date: "<?php echo $it_comingsoon_count_time; ?>",
                format: "on"
            },
            
            function() {
                // callback function
            });
        });
        
        </script>
        
         <ul id="countdown">
            <li>
                <span class="days">00</span>
                <p class="timeRefDays">days</p>
            </li>
            <li>
                <span class="hours">00</span>
                <p class="timeRefHours">hours</p>
            </li>
            <li>
                <span class="minutes">00</span>
                <p class="timeRefMinutes">minutes</p>
            </li>
            <li>
                <span class="seconds">00</span>
                <p class="timeRefSeconds">seconds</p>
            </li>
        </ul>
        <?php } ?> 
         
         
         <?php if ($it_comingsoon_p !="") { ?>
         <div class="alert alert-info">
            <?php echo $it_comingsoon_p; ?>
        </div>
   		<?php } ?> 
        
		<?php echo $it_comingsoon_newsletter; ?>
        
        <?php if ($it_comingsoon_social !="") { ?>
        <hr>
        <?php echo $it_comingsoon_social; ?>
        <?php } ?> 
      
    </div>
      
     
</body>

</html>
