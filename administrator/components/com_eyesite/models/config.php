<?php
/********************************************************************
Product		: Eyesite
Date		: 9 June 2017
Copyright	: Les Arbres Design 2010-2017
Contact		: http://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted access');

class EyesiteModelConfig extends LAE_model
{
var $data;
var $_app = null;

function __construct()
{
	parent::__construct();
	$this->_app = JFactory::getApplication();
}

//-------------------------------------------------------------------------------
// Get the component parameters
// Returns a stdClass Object containing all our parameters
//
function &getData()
{
	$component_params = JComponentHelper::getParams(LAE_COMPONENT);		// get the component parameters
	$this->data = $component_params->toObject();

// set defaults for all our parameters so that we have this all in one place

	if (!isset($this->data->emailto))      $this->data->emailto      = '';
	if (!isset($this->data->extensions))   $this->data->extensions   = 'php,js,htm,html,css,ini,sql,jpg';
	if (!isset($this->data->incdirs))      $this->data->incdirs      = JPATH_ROOT.',S';
	if (!isset($this->data->excdirs))      $this->data->excdirs      = JPATH_ROOT."/tmp,\n".JPATH_ROOT."/logs,\n".JPATH_ROOT."/cache";
	if (!isset($this->data->excfiles))     $this->data->excfiles     = '';
	if (!isset($this->data->days_to_keep)) $this->data->days_to_keep = 365;
	if (!isset($this->data->auto_accept))  $this->data->auto_accept  = 0;
	if (!isset($this->data->purchase_id))  $this->data->purchase_id  = '';
	return $this->data;
}

//-------------------------------------------------------------------------------
// Get the post data and return it as an associative array
//
function &getPostData()
{
	$this->data = new stdClass();
	$jinput = JFactory::getApplication()->input;
	$this->data->emailto      = $jinput->get('emailto', '', 'STRING');
	$this->data->extensions   = $jinput->get('extensions', '', 'STRING');
	$this->data->incdirs      = $jinput->get('incdirs', '', 'STRING');
	$this->data->excdirs      = $jinput->get('excdirs', '', 'STRING');
	$this->data->excfiles     = $jinput->get('excfiles', '', 'STRING');
	$this->data->days_to_keep = $jinput->get('days_to_keep', '', 'STRING');
	$this->data->auto_accept  = $jinput->get('auto_accept', '0', 'STRING');
	$this->data->purchase_id  = $jinput->get('purchase_id', '0', 'STRING');
	return $this->data;
}

// ------------------------------------------------------------------------------------
// Validate all the configuration entries
// Return TRUE on success or FALSE if there is any invalid data
//
function check()
{
	$errors = array();
    $warnings = array();

// check that at least one include directory is specified
// each directory must be followed by S (recurse subdirectories) or N (do not recurse)

	$incdirs = LAE_helper::expand_cs_list($this->data->incdirs);		// get array of included directories
	$numdirs = count($incdirs);
	if (($numdirs < 2) or (($numdirs % 2) != 0))	// must be in pairs
		$errors[] = JText::_('COM_EYESITE_DIR_INC_ERROR1');

	if (empty($errors))
		{
		for ($i = 0; $i < $numdirs; $i = $i+2)
			{
			$path = $incdirs[$i];
			$flag = $incdirs[$i+1];
			if (!file_exists($path))
				{
				$errors[] = JText::_('COM_EYESITE_DIR_INC_ERROR2').' '.$incdirs[$i];
				continue;
				}

			if (!is_readable($path))
				$errors[] = JText::_('COM_EYESITE_DIR_INC_ERROR3').' '.$incdirs[$i];

			if (($flag != 'S') and ($flag != 'N'))
				$errors[] = JText::_('COM_EYESITE_DIR_INC_ERROR1');
			}
		}
        
// check the exclude directories

	$excdirs = LAE_helper::expand_cs_list($this->data->excdirs);		// get array of excluded directories

    foreach ($excdirs as $excdir)
        if (!file_exists($excdir))
            {
            $warnings[] = JText::_('COM_EYESITE_DIR_EXC_ERROR').' '.$excdir;
            continue;
            }

// check the email address - it can be a list of email addresses, or blank (in which case no emails are sent)

	if (!empty($this->data->emailto))
		{
		$ret = self::validate_email_list($this->data->emailto);
		if ($ret != '')
			$errors[] = JText::_('COM_EYESITE_INVALID').': '.JText::_('COM_EYESITE_EMAIL_ADRESS').' '.$ret;	      
		}

// days to keep must be a positive integer

	if (!self::is_posint($this->data->days_to_keep))
		$errors[] = JText::_('COM_EYESITE_INVALID').': '.JText::_('COM_EYESITE_DAYS_TO_KEEP');
	
// if any messages were stored in the $warnings array, show them as a single notice message

	if (!empty($warnings))
		$this->_app->enqueueMessage(implode('<br />',$warnings), 'notice');

// if any errors were stored in the $errors array, show them as a single error message

	if (empty($errors))
    	return true;

	$this->_app->enqueueMessage(implode('<br />',$errors), 'error');
	return false;
}

//---------------------------------------------------------------
// Save component parameters
// Returns TRUE on success or FALSE if there is an error
//
function store()
{
	$query = "UPDATE `#__extensions` SET `params` = ".$this->_db->Quote(json_encode($this->data)).
			" WHERE `type` = 'component' AND `element` = 'com_eyesite'";
	
	$result = $this->ladb_execute($query);
	
	if ($result === false)
		{
		$this->app->enqueueMessage($this->ladb_error_text, 'error');
		return false;
		}
        
// clear the cache.

	$this->cleanCache('_system', 0);
	$this->cleanCache('_system', 1);

    if (empty($this->data->purchase_id))
        return true;

// save the extra_query on the update site record

    $extra_query = 'tid='.$this->data->purchase_id;

    $query = "UPDATE `#__update_sites` SET `extra_query` = '$extra_query'".
        " WHERE `type` = 'extension' AND `name` = 'Eyesite Plugin'";

	$result = $this->ladb_execute($query);    
        
	return true;
}

//-------------------------------------------------------------------------------
// Return true if the "Eyesite Plugin" update server is installed
// This is also a convenient place to disable update checks if update caching is set to zero
//
function plugin_update_server()
{
    $component = JComponentHelper::getComponent('com_installer');
    $params = $component->params;
    $cache_timeout = $params->get('cachetimeout', 6, 'int');
    if ($cache_timeout == 0)
        $this->ladb_execute("UPDATE `#__update_sites` SET `enabled` = 0 WHERE `name` LIKE '%Eyesite%'");
        
    $query = "SELECT count(*) FROM `#__update_sites` WHERE `type` = 'extension' AND `name` = 'Eyesite Plugin'";
    $count = $this->ladb_loadResult($query);
    if ($count >= 1)
        return true;
    else
        return false;
}

//-------------------------------------------------------------------------------
// Validate multiple email addresses
// if all are valid, returns an empty string
// if any are invalid, returns the first invalid one
// NOTE: $email_list is altered so that each email address only occurs once 
// 
static function validate_email_list(&$email_list, $allow_blank=true)
{
	$email_list = str_replace(' ', '', $email_list);			// remove spaces
	trim($email_list,',');										// trim off any spare commas
		
	if ($email_list == '')
		{
		if ($allow_blank)
			return '';
		return '( )';
		}

	$email_list = strtolower($email_list);					// make all lower case for array_unique() call
	$email_addresses = explode(',', $email_list);			// make it an array
	$email_addresses = array_unique($email_addresses);		// remove any duplicates
	$email_list = implode(',', $email_addresses);			// recreate the original email list to return
	
	jimport('joomla.mail.helper');

	foreach ($email_addresses as $address)
		if (!JMailHelper::isEmailAddress($address))
			return '('.$address.')';
			
	return '';
}

//-------------------------------------------------------------------------------
// Return true if supplied argument is a positive integer, else false
//
static function is_posint($arg)
{
	if ($arg == '')
		return false;

	if (!is_numeric($arg))
		return false;

	if (preg_match('/[^\d]/', $arg))
		return false;

	return true;
}



}
		
		