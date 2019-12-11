<?php
/********************************************************************
Product		: Eyesite
Date		: 8 June 2017
Copyright	: Les Arbres Design 2010-2017
Contact		: http://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted access');

class EyesiteModelData extends LAE_model
{
var $_pagination = null;

//-------------------------------------------------------------------------------
// Get the current state
//
function getInfo() 
{
	$this->totalCount = 0;		// initialise all properties in case of a db error
	$this->runningCount = 0;
	$this->notOkCount = 0;
	$this->latest_date = 0;
	
// get the total number of rows we have
	
	$this->totalCount = $this->ladb_loadResult("Select Count(*) From #__eye_site ");
	if ($this->totalCount === false)
		{
		$this->app->enqueueMessage($this->ladb_error_text, 'error');
		return $this->totalCount;
		}

// get the number of rows in state running
// of course, for the initial scan, there are none
	
	$this->runningCount = $this->ladb_loadResult("Select Count(*) From #__eye_site Where state = ".LAE_STATE_RUNNING);
	if ($this->runningCount === false)
		{
		$this->app->enqueueMessage($this->ladb_error_text, 'error');
		return $this->totalCount;
		}

// get the number of rows in not-OK state
	
	$this->notOkCount = $this->ladb_loadResult("Select Count(*) From #__eye_site Where state != ".LAE_STATE_OK);
	if ($this->notOkCount === false)
		{
		$this->app->enqueueMessage($this->ladb_error_text, 'error');
		return $this->totalCount;
		}

// get the latest run date
	
	$this->latest_date = $this->ladb_loadResult("Select Max(datetime) From #__eye_site_history 
		Where `state` IN (".LAE_HISTORY_SCAN_NO_CHANGES.", ".LAE_HISTORY_SCAN_CHANGES.", ".LAE_HISTORY_SCAN_ERROR.")");
	if ($this->latest_date === false)
		{
		$this->app->enqueueMessage($this->ladb_error_text, 'error');
		return $this->totalCount;
		}

	return true;
}

//-------------------------------------------------------------------------------
// Build a list of all the outstanding changes 
//
function &getList() 
{
	$filter_state = $this->app->getUserStateFromRequest('com_eyesite.filter_state','filter_state',-1);
	$limit		= $this->app->getUserStateFromRequest('global.list.limit', 'limit', $this->app->get('list_limit'), 'int');
	$limitstart	= $this->app->getUserStateFromRequest('com_eyesite.limitstart', 'limitstart', 0, 'int');
	$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0); // In case limit has been changed

// build the queries

	$query_count = "Select count(*) ";
	$query_cols  = "Select `id`, `state`, `filepath`, `datetime`, `filesize`, `new_datetime`, `new_filesize` ";
	$query_from  = "From `#__eye_site` ";
	$query_where = "Where `state` != ".LAE_STATE_OK." And `state` != ".LAE_STATE_RUNNING.' ';
	$query_order = "Order By `filepath` ";

// if the state filter is not set to "all", change the where clause

	if ($filter_state != -1)
		$query_where = 'Where `state` = '.$filter_state.' ';
		
// get the total row count

	$total = $this->ladb_loadResult($query_count.$query_from.$query_where);
	if ($total === false)
		{
		$this->app->enqueueMessage($this->ladb_error_text, 'error');
		$total = array();
		return $total;
		}
		
// setup the pagination object

	jimport('joomla.html.pagination');
	$this->_pagination = new JPagination($total, $limitstart, $limit);

// get the subset (based on limits) of required records
	
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
// Accept changes for list array $cid
//
function accept_list($cid)
{
	foreach ($cid as $id)
		$this->accept_one($id);
}

//-------------------------------------------------------------------------------
// Accept changes for $id
//
function accept_one($id)
{
	$query = "SELECT `id`, `state`, `filepath` FROM `#__eye_site` WHERE `id` = $id";
	$row = $this->ladb_loadObject($query);

	if (empty($row))
		{
		$this->app->enqueueMessage(JText::_('COM_EYESITE_ERROR_ROW_NOT_FOUND'), 'error');
		return;
		}

	switch ($row->state)
		{
		case LAE_STATE_NEW:				// just set the state to OK
			$query = "update `#__eye_site` set `state` = ".LAE_STATE_OK." where `id` = $id";
			break;
		case LAE_STATE_CHANGED:			// set state to OK and move new details to reference set
			$query = "update `#__eye_site` set `state` = ".LAE_STATE_OK.", `md5` = `new_md5`, `datetime` = `new_datetime`, `filesize` = `new_filesize` where `id`=$id";
			break;
		case LAE_STATE_DELETED:			// delete the row
			$query = "delete from `#__eye_site` where `id` = $id";
			break;
		}
		
	LAE_trace::trace($query);
	$result = $this->ladb_execute($query);
	if ($result === false)
		{
		$this->app->enqueueMessage($this->ladb_error_text, 'error');
		return;
		}
}

//-------------------------------------------------------------------------------
// Accept all changes
//
function accept_all()
{
	$query = "update `#__eye_site` set state = ".LAE_STATE_OK." where `state` = ".LAE_STATE_NEW;
	LAE_trace::trace($query);
	$result = $this->ladb_execute($query);
	if ($result === false)
		{
		$this->app->enqueueMessage($this->ladb_error_text, 'error');
		return;
		}
		
	$query = "delete from `#__eye_site` where `state` = ".LAE_STATE_DELETED;
	LAE_trace::trace($query);
	$result = $this->ladb_execute($query);
	if ($result === false)
		{
		$this->app->enqueueMessage($this->ladb_error_text, 'error');
		return;
		}
		
	$query = "update `#__eye_site` set `state` = ".LAE_STATE_OK.", `md5` = `new_md5`, `datetime` = `new_datetime`, `filesize` = `new_filesize` where `state` = ".LAE_STATE_CHANGED;
	LAE_trace::trace($query);
	$result = $this->ladb_execute($query);
	if ($result === false)
		{
		$this->app->enqueueMessage($this->ladb_error_text, 'error');
		return;
		}
}

//-------------------------------------------------------------------------------
// Reject changes for list array $cid
//
function reject_list($cid)
{
	foreach ($cid as $id)
		$this->reject_one($id);
}

//-------------------------------------------------------------------------------
// Reject changes for $id
//
function reject_one($id)
{
	$query = "SELECT id, state, filepath FROM `#__eye_site` WHERE id = $id";
	$row = $this->ladb_loadObject($query);
	if (empty($row))
		{
		$this->app->enqueueMessage(JText::_('COM_EYESITE_ERROR_ROW_NOT_FOUND'), 'error');
		return;
		}
		
	switch ($row->state)
		{
		case LAE_STATE_NEW:				// delete the row
			$query = "delete from `#__eye_site` where `id` = $id";
			break;
		case LAE_STATE_CHANGED:			// set state to OK and delete new details
			$query = "update `#__eye_site` set `state` = ".LAE_STATE_OK.", `md5` = '', datetime = '', `filesize` = 0 where `id`=$id";
			break;
		case LAE_STATE_DELETED:			// just set the state to OK
			$query = "update `#__eye_site` set `state` = ".LAE_STATE_OK." where `id` = $id";
			break;
		}
		
	LAE_trace::trace($query);
	$result = $this->ladb_execute($query);
	if ($result === false)
		{
		$this->app->enqueueMessage($this->ladb_error_text, 'error');
		return;
		}
}

//-------------------------------------------------------------------------------
// Reject all changes
//
function reject_all()
{
	$query = "delete from `#__eye_site` where `state` = ".LAE_STATE_NEW;
	LAE_trace::trace($query);
	$result = $this->ladb_execute($query);
	if ($result === false)
		{
		$this->app->enqueueMessage($this->ladb_error_text, 'error');
		return;
		}

	$query = "update `#__eye_site` set `state` = ".LAE_STATE_OK." where `state` = ".LAE_STATE_DELETED;
	LAE_trace::trace($query);
	$result = $this->ladb_execute($query);
	if ($result === false)
		{
		$this->app->enqueueMessage($this->ladb_error_text, 'error');
		return;
		}

	$query = "update `#__eye_site` set `state` = ".LAE_STATE_OK.", `new_md5` = '', `new_datetime` = '', `new_filesize` = 0 where `state` = ".LAE_STATE_CHANGED;
	LAE_trace::trace($query);
	$result = $this->ladb_execute($query);
	if ($result === false)
		{
		$this->app->enqueueMessage($this->ladb_error_text, 'error');
		return;
		}
}

//-------------------------------------------------------------------------------
// Get one row
//
function getOne($id)
{
	$query = "SELECT * FROM `#__eye_site` WHERE `id` = '$id'";
	$this->data = $this->ladb_loadObject($query);
	return $this->data;
}

//-------------------------------------------------------------------------------
// Return a pointer to our pagination object
// - should be called after getList() has created the pagination object
//
function &getPagination()
{
	if ($this->_pagination == Null)
		$this->_pagination = new JPagination(0,0,0);
	return $this->_pagination;
}


}
		
		