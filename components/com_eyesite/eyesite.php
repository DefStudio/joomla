<?php
/********************************************************************
Product		: Eyesite
Date		: 8 June 2017
Copyright	: Les Arbres Design 2010-2017
Contact		: http://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

require_once JPATH_ADMINISTRATOR.'/components/com_eyesite/helpers/eyesite_helper.php';
require_once JPATH_ADMINISTRATOR.'/components/com_eyesite/helpers/db_helper.php';
require_once JPATH_ADMINISTRATOR.'/components/com_eyesite/helpers/trace_helper.php';
require_once JPATH_ADMINISTRATOR.'/components/com_eyesite/helpers/eyesite_scanner.php';
require_once JPATH_ADMINISTRATOR.'/components/com_eyesite/models/config.php';
require_once JPATH_ADMINISTRATOR.'/components/com_eyesite/models/history.php';
require_once JPATH_ADMINISTRATOR.'/components/com_eyesite/models/data.php';

    $jinput = JFactory::getApplication()->input;
    $task = $jinput->get('task','', 'STRING');
    $entry = $jinput->get('entry','', 'STRING');

// the front end should only be entered by a call to the scanner
// any other call is redirected to the home page

    if ($task != 'scan')
        {
        LAE_trace::trace("Eyesite front end redirecting to home page, task = [$task]");
        $app->redirect(JURI::root());
        return;
        }
    
// we use a common language file stored only in the back end so must load it explicitly

    $lang = JFactory::getLanguage();
    $lang->load('com_eyesite', JPATH_ADMINISTRATOR.'/components/com_eyesite');

// run the scanner

	$scanner = new LAE_scanner;
	$scanner->scan();


