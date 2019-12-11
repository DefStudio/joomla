<?php
/********************************************************************
Product 	: Eyesite
Date		: 27 February 2017
Copyright	: Les Arbres Design 2014-2017
Contact		: http://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

if (class_exists("LAE_model"))
	return;

class LAE_model extends JModelLegacy
{

function __construct()
{
	parent::__construct();
	$this->app = JFactory::getApplication();
}

//-------------------------------------------------------------------------------
// Execute a SQL query and return true if it worked, false if it failed
//
function ladb_execute($query)
{
	try
		{
		$this->_db->setQuery($query);
		$this->_db->execute();
		}
	catch (RuntimeException $e)
		{
	    $this->ladb_error_text = $e->getMessage();
	    $this->ladb_error_code = $e->getCode();
		return false;
		}
	return true;
}

//-------------------------------------------------------------------------------
// Get a single value from the database as an object and return it, or false if it failed
//
function ladb_loadResult($query)
{
	try
		{
		$this->_db->setQuery($query);
		$result = $this->_db->loadResult();
		}
	catch (RuntimeException $e)
		{
	    $this->ladb_error_text = $e->getMessage();
	    $this->ladb_error_code = $e->getCode();
		return false;
		}
	return $result;
}

//-------------------------------------------------------------------------------
// Get a row from the database as an object and return it, or false if it failed
//
function ladb_loadObject($query)
{
	try
		{
		$this->_db->setQuery($query);
		$result = $this->_db->loadObject();
		}
	catch (RuntimeException $e)
		{
	    $this->ladb_error_text = $e->getMessage();
	    $this->ladb_error_code = $e->getCode();
		return false;
		}
	return $result;
}

//-------------------------------------------------------------------------------
// Get an array of rows from the database and return it, or false if it failed
//
function ladb_loadObjectList($query, $limitstart = 0, $limit = 0)
{
	try
		{
		$this->_db->setQuery($query, $limitstart, $limit);
		$result = $this->_db->loadObjectList();
		}
	catch (RuntimeException $e)
		{
	    $this->ladb_error_text = $e->getMessage();
	    $this->ladb_error_code = $e->getCode();
		return false;
		}
	return $result;
}

//-------------------------------------------------------------------------------
// Make this public
//
function ladb_quote($value)
{
    return $this->_db->Quote($value);
}

//-------------------------------------------------------------------------------
// set the database date language
//
function setDbLanguage()
{
	$langObj = JFactory::getLanguage();
	$lang = $langObj->get('tag');
	$lang[2] = '_';
	$this->ladb_execute("SET lc_time_names = '$lang';");
}

//-------------------------------------------------------------------------------
// get the current database time
// can be called from anywhere, not just models
//
static function getDatabaseDateTime()
{
	$db	= JFactory::getDBO();
	$db->setQuery('Select NOW()');
	return $db->loadResult();
}


}




