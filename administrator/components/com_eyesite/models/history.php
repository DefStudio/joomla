<?php
/********************************************************************
Product		: Eyesite
Date		: 8 June 2017
Copyright	: Les Arbres Design 2010-2017
Contact		: http://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted access');

class EyesiteModelHistory extends LAE_model
{
var $data;

//-------------------------------------------------------------------------------
// Get one row
//
function getOne($id)
{
	$query = "SELECT * FROM `#__eye_site_history` WHERE `id` = '$id'";
	$this->data = $this->ladb_loadObject($query);
	return $this->data;
}

//-------------------------------------------------------------------------------
// Return a pointer to our pagination object
// This should normally be called after getList()
//
function &getPagination()
{
	if ($this->_pagination == Null)
		$this->_pagination = new JPagination(0,0,0);
	return $this->_pagination;
}

//-------------------------------------------------------------------------------
// Get the list of history records
//
function &getList()
{
	$limit		= $this->app->getUserStateFromRequest('global.list.limit', 'limit', $this->app->get('list_limit'), 'int');
	$limitstart	= $this->app->getUserStateFromRequest('com_eyesite.history_limitstart', 'limitstart', 0, 'int');
	$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0); // In case limit has been changed
	$search     = $this->app->getUserStateFromRequest('com_eyesite.history_search','search','','RAW');

// build the query

	$query_count = "Select count(*) ";
	$query_cols  = "Select * ";
	$query_from  = "From #__eye_site_history ";
	$query_where = "Where 1 ";
	$query_order = " Order by `id` DESC";

// search

	if ($search != '')
		$query_where .= $this->make_search($search);

// get the total row count

	$count_query = $query_count.$query_from.$query_where;
	$total = $this->ladb_loadResult($count_query);
	
	if ($total === false)
		{
		$this->app->enqueueMessage($this->ladb_error_text, 'error');
		return $total;
		}

// setup the pagination object

	jimport('joomla.html.pagination');
	$this->_pagination = new JPagination($total, $limitstart, $limit);

// get the data, within the limits required

	$main_query = $query_cols.$query_from.$query_where.$query_order;
	$rows = $this->ladb_loadObjectList($main_query, $this->_pagination->limitstart, $this->_pagination->limit);
	if ($rows === false)
		{
		$this->app->enqueueMessage($this->ladb_error_text, 'error');
		$rows = array();
		return $rows;
		}
	
	return $rows;
}

//-------------------------------------------------------------------------------
// Make the where clause for the search string
//
function make_search($search)
{

// YYYY-MM-DD or YYYY-MM - search by date

	if ((strlen($search) == 10) and ($search{4} == '-') and ($search{7} == '-'))
		return " AND DATE(`datetime`) = '$search' ";

	if ((strlen($search) == 7) and ($search{4} == '-'))
		{
		$search_year = substr($search,0,4);
		$search_month = substr($search,5,2);
		return " AND YEAR(`datetime`) = $search_year AND MONTH(`datetime`) = $search_month ";
		}
		
// any other string searches details

	$search_like = $this->_db->Quote('%'.$this->_db->escape($search,true).'%',false);
	
	return " AND LOWER(`details`) LIKE LOWER($search_like) ";
}

//-------------------------------------------------------------------------------
// delete one or more history entries
//
function delete()
{
	$jinput = JFactory::getApplication()->input;
	$cids = $jinput->get('cid', array(0), 'ARRAY');

	$message = '';

	foreach ($cids as $cid)
		{
		$query = "delete from #__eye_site_history where id = $cid";
		$result = $this->ladb_execute($query);
		if ($result === false)
			$message = $this->ladb_error_text;
		}

	if ($message != '')
		{
		$this->app->enqueueMessage($message);
		return false;
		}

	return true;
}

//---------------------------------------------------------------
// Save a new history record
// Returns TRUE on success or FALSE if there is an error
//
function store($state, $summary, $details)
{
	$query = 'INSERT INTO `#__eye_site_history` (`datetime`, `state`, `summary`, `details`) VALUES
			( NOW(), '.$this->_db->Quote($state).','.$this->_db->Quote($summary).','.$this->_db->Quote($details).')';
			
	$result = $this->ladb_execute($query);
	
	if ($result === false)
		{
		LAE_trace::trace($this->ladb_error_text);
		return false;
		}
		
	return true;
}

//---------------------------------------------------------------
// Update the summary on a history record
// Returns TRUE on success or FALSE if there is an error
//
function update_summary($id, $summary)
{
	$query = "UPDATE `#__eye_site_history` SET `summary` = ".$this->_db->Quote($summary)." WHERE `id` = '$id'";
			
	$result = $this->ladb_execute($query);

	if ($result === false)
		{
		$this->app->enqueueMessage($this->ladb_error_text, 'error');
		return false;
		}
		
	return true;
}

//-------------------------------------------------------------------------------
// Determine if a scanner instance is running
// Returns false if one is not running, true if one is
//
function scanning()
{
// get the last scan start record that is less than 10 minutes old
// (if there isn't one, we assume that no scanner is running)

	$query = "SELECT MAX(`datetime`) FROM `#__eye_site_history` 
		WHERE `state` = ".LAE_HISTORY_SCAN_STARTED." AND TIMESTAMPDIFF(MINUTE, `datetime`, NOW()) < 10";
	$datetime = $this->ladb_loadResult($query);
	if (empty($datetime))
		return false;

// we got a start record less than 10 minutes old
// is there a stop record newer than it?
	
	$query = "SELECT count(*) FROM `#__eye_site_history` 
		WHERE `state` IN (".LAE_HISTORY_SCAN_NO_CHANGES.", ".LAE_HISTORY_SCAN_CHANGES.", ".LAE_HISTORY_SCAN_ERROR.")
		AND `datetime` >= '$datetime'";
	$result = $this->ladb_loadResult($query);
	
	if ($result == 0)
		return true;		// we couldn't find a newer stop record
	else
		return false;		// we found a newer stop record
}

//-------------------------------------------------------------------------------
// Return true if the scanner started in the last 20 seconds
// (20 seconds in case it takes a while before we call this)
//
function started()
{
	$query = "SELECT MAX(`datetime`) FROM `#__eye_site_history` 
		WHERE `state` = ".LAE_HISTORY_SCAN_STARTED." AND TIMESTAMPDIFF(SECOND, `datetime`, NOW()) < 20";
	$datetime = $this->ladb_loadResult($query);
	if (empty($datetime))
		return false;
	else
		return true;
}

} // class
		
		