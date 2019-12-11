<?php
/********************************************************************
Product		: Eyesite
Date		: 8 June 2017
Copyright	: Les Arbres Design 2009-2017
Contact		: http://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

define ("LAE_COMPONENT",      "com_eyesite");
define ("LAE_COMPONENT_NAME", "Eyesite");
define ("LAE_ADMIN_ASSETS_URL", JURI::root(true).'/administrator/components/com_eyesite/assets/');
define ("LAE_STATE_OK", 0);
define ("LAE_STATE_RUNNING", 1); 
define ("LAE_STATE_NEW", 2);
define ("LAE_STATE_CHANGED", 3);
define ("LAE_STATE_DELETED", 4);
define ("LAE_SQL_DATE_FORMAT", "%Y-%m-%d %H:%M:%S");	// strftime() format 
define ("LAE_HISTORY_SCAN_STARTED", 1);
define ("LAE_HISTORY_SCAN_NO_CHANGES", 2);
define ("LAE_HISTORY_SCAN_CHANGES", 3);
define ("LAE_HISTORY_SCAN_ERROR", 4);
define ("LAE_HISTORY_ACCEPT", 5);
define ("LAE_HISTORY_REJECT", 6);
define ("LAE_HISTORY_ACCEPT_ALL", 7);
define ("LAE_HISTORY_REJECT_ALL", 8);
define ("LAE_HISTORY_SCAN_NOT_STARTED", 9);
define ("LAE_HISTORY_EMAIL_FAILED", 10);
define ("LAE_MAX_LOCK_SECONDS", 900);

if (class_exists("LAE_helper"))
	return;

class LAE_helper
{

//-------------------------------------------------------------------------------
// Get the text for a state
//
static function state_text($state)
{
	switch ($state)
		{
		case LAE_STATE_OK:      return JText::_('COM_EYESITE_STATE_OK');
		case LAE_STATE_RUNNING: return JText::_('COM_EYESITE_STATE_RUNNING');
		case LAE_STATE_NEW:     return JText::_('COM_EYESITE_STATE_NEW');
		case LAE_STATE_CHANGED: return JText::_('COM_EYESITE_STATE_CHANGED');
		case LAE_STATE_DELETED: return JText::_('COM_EYESITE_STATE_DELETED');
		}
	return 'bad state';
}

//-------------------------------------------------------------------------------
// Get the text for a history item
//
static function history_text($state)
{
	switch ($state)
		{
		case LAE_HISTORY_SCAN_STARTED:     return JText::_('COM_EYESITE_SCANNER_STARTING');
		case LAE_HISTORY_SCAN_NO_CHANGES:  return JText::_('COM_EYESITE_SCANNER_COMPLETE');
		case LAE_HISTORY_SCAN_CHANGES:     return JText::_('COM_EYESITE_SCANNER_COMPLETE');
		case LAE_HISTORY_SCAN_ERROR:       return JText::_('COM_EYESITE_SCANNER_COMPLETE');
		case LAE_HISTORY_ACCEPT:           return JText::_('COM_EYESITE_TOOLBAR_ACCEPT');
		case LAE_HISTORY_REJECT:           return JText::_('COM_EYESITE_TOOLBAR_REJECT');
		case LAE_HISTORY_ACCEPT_ALL:       return JText::_('COM_EYESITE_TOOLBAR_ACCEPT_ALL');
		case LAE_HISTORY_REJECT_ALL:       return JText::_('COM_EYESITE_TOOLBAR_REJECT_ALL');
        case LAE_HISTORY_SCAN_NOT_STARTED: return JText::_('COM_EYESITE_SCANNER_NOT_STARTED');
        case LAE_HISTORY_EMAIL_FAILED:     return JText::_('COM_EYESITE_EMAIL_SEND_FAILED');
		}
	return '';
}

//-------------------------------------------------------------------------------
// Send an email
//
static function send_email($config_data, $subject, $body_text)
{
	$app = JFactory::getApplication();
	$mailer = JFactory::getMailer();
	$mailer->IsHTML(true);
	$mailer->setSender(array($app->get('mailfrom'), 'Eyesite'));
	$mailer->setSubject($subject);
	$mailer->setBody($body_text);
	$addresses = explode(',', $config_data->emailto);
	foreach ($addresses as $address)
		$mailer->addRecipient($address);
	$ret = $mailer->Send();
	if ($ret === true)
		{
		LAE_trace::trace("mailer->Send() returned true");
		return '';
		}
	else
		{
		LAE_trace::trace("mailer->Send() returned error: ".$mailer->ErrorInfo);
		return $mailer->ErrorInfo;
		}
}

//-------------------------------------------------------------------------------
// Expand a comma delimited list to an array
//
static function expand_cs_list($cs_list)
{
	$a = explode(",",$cs_list);
	$a = array_map('trim',$a);					// trim white space
	if ((count($a) == 1) and ($a[0] == ''))
		return array();
	return $a;
}

// -------------------------------------------------------------------------------
// Create the component menu and make the current item active
//
static function addSubMenu($submenu = '')
{
    JHtmlSidebar::addEntry(JText::_('COM_EYESITE_STATUS'), 'index.php?option='.LAE_COMPONENT.'&task=display', $submenu == 'main');
    JHtmlSidebar::addEntry(JText::_('COM_EYESITE_HISTORY'), 'index.php?option='.LAE_COMPONENT.'&task=history_list', $submenu == 'history');
    JHtmlSidebar::addEntry(JText::_('COM_EYESITE_TOOLBAR_CONFIGURE'), 'index.php?option='.LAE_COMPONENT.'&task=configure', $submenu == 'config');
    JHtmlSidebar::addEntry(JText::_('COM_EYESITE_ABOUT'), 'index.php?option='.LAE_COMPONENT.'&task=about', $submenu == 'about');
}

//-------------------------------------------------------------------------------
// Make an info button
//
static function make_info($title, $link='')
{
	JHTML::_('bootstrap.tooltip');
	if ($link == '')
		{
		$icon_name = 'info-16.png';
		$html = '';
		}
	else
		{
		$icon_name = 'link-16.png';
		$html = '<a href="'.$link.'" target="_blank">';
		}

	$icon = JHTML::_('image', JURI::root().'administrator/components/com_eyesite/assets/'.$icon_name,'', array('style' => 'vertical-align:text-bottom;'));
	$html .= '<span class="hasTooltip" title="'.htmlspecialchars($title, ENT_COMPAT, 'UTF-8').'">'.$icon.'</span>';
		
	if ($link != '')
		$html .= '</a>';
		
	return $html;
}
  
// -------------------------------------------------------------------------------
// Draw the component menu for >= Joomla 3.x
// - this must be called at the start of every view
//
static function viewStart()
{
	$sidebar = JHtmlSidebar::render();
	if (empty($sidebar))
		echo '<div id="j-main-container">';
	else
		{
		echo '<div id="j-sidebar-container" class="span2">';
		echo "$sidebar";
		echo "</div>";
		echo '<div id="j-main-container" class="span10">';
		}
}

// -------------------------------------------------------------------------------
// This must be called at the end of every view that calls viewStart()
//
static function viewEnd()
{
	echo "</div>";
}

} // class