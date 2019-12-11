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

<body class="offline_page">

	<?php if ($it_logo != "") { ?>
    <div id="logo_page">	
        <a href="<?php echo $this->baseurl ?>"><?php echo $it_logo_img; ?></a>	
    </div>
    <?php } ?>   
    

    <div id="content_page">
    
 		<jdoc:include type="message" />
    
         <p class="alert alert-info alert_custom">
            <?php echo JText::_('JOFFLINE_MESSAGE'); ?><br />
        </p>
    
        <form action="<?php echo JRoute::_('index.php', true); ?>" method="post" id="form-login" class="form-horizontal">
            
            <fieldset class="input">
            
             <div class="control-group">
              <label class="control-label" for="inputEmail"><?php echo JText::_('JGLOBAL_USERNAME') ?></label>
              <div class="controls">
                <input name="username" id="username" type="text" class="inputbox" alt="<?php echo JText::_('JGLOBAL_USERNAME') ?>" size="18" style="position: relative; font-size: 14px; line-height: 20px; " />
                 </div>  
              </div>
              
              <div class="control-group">
              <label class="control-label" for="inputEmail"><?php echo JText::_('JGLOBAL_PASSWORD') ?></label>
              <div class="controls">
                <input name="password" id="passwd" type="password" class="inputbox" alt="<?php echo JText::_('JGLOBAL_PASSWORD') ?>" size="18" style="position: relative; font-size: 14px; line-height: 20px; " />
                  </div>
              </div>
          
          <div class="control-group">
              <div class="controls">
                <label for="remember" class="checkbox"> <input type="checkbox" name="remember" value="yes" id="remember" /> <?php echo JText::_('JGLOBAL_REMEMBER_ME') ?>  </label>
                
              </div>
            </div>
            
             <div class="control-group">
              <div class="controls">
                                  <input type="submit" name="Submit" class="btn btn-inverse"  value="<?php echo JText::_('JLOGIN') ?>" />

              </div>
            </div>
            
        
            <input type="hidden" name="option" value="com_users" />
            <input type="hidden" name="task" value="user.login" />
            <input type="hidden" name="return" value="<?php echo base64_encode(JURI::base()) ?>" />
            <?php echo JHtml::_('form.token'); ?>
        </fieldset>
        </form>
        
      
    </div>
      
     
</body>

</html>
