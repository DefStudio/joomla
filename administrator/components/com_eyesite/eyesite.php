<?php
/********************************************************************
Product		: Eyesite
Date		: 8 June 2017
Copyright	: Les Arbres Design 2010-2017
Contact		: http://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

// Check for ACL access

if (!JFactory::getUser()->authorise('core.manage', 'com_eyesite'))
    {
	$app = JFactory::getApplication();
    $app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
	return;
    }

require_once JPATH_ADMINISTRATOR.'/components/com_eyesite/helpers/eyesite_helper.php';
require_once JPATH_ADMINISTRATOR.'/components/com_eyesite/helpers/db_helper.php';
require_once JPATH_ADMINISTRATOR.'/components/com_eyesite/helpers/trace_helper.php';

// load our css

$document = JFactory::getDocument();
$document->addStyleSheet('components/com_eyesite/assets/eyesite.css?v=401');

$jinput = JFactory::getApplication()->input;
$task = $jinput->get('task','display', 'STRING');

require_once(JPATH_ADMINISTRATOR.'/components/com_eyesite/controller.php' );
$controller	= new EyesiteController( );
$controller->execute($task);
$controller->redirect();

