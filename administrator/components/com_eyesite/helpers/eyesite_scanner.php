<?php
/********************************************************************
Product		: Eyesite
Date		: 8 June 2017
Copyright	: Les Arbres Design 2009-2017
Contact		: http://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

if (class_exists("LAE_scanner"))
	return;

class LAE_scanner
{

//-------------------------------------------------------------------------------
// Initialise and call the main scanning function
//
function scan()
{
	$this->app = JFactory::getApplication();
	$jinput = JFactory::getApplication()->input;
	$this->source = $jinput->get('source','', 'STRING');
	$this->entry = $jinput->get('entry','', 'STRING');
	$this->db_model = new LAE_model;
   
// check the entry requirements

    if (!$this->check_entry_requirements())
        return;
                     
// try to avoid time-outs

	ignore_user_abort(true);
	@set_time_limit(0);                 
    @ini_set('max_execution_time', 0);
	$time_limit = ini_get('max_execution_time'); 
	LAE_trace::trace('Scan started by '.$this->source.', working directory = '.getcwd().", time limit = $time_limit",true);

// more initialisation

	$this->sitename = $this->app->get('sitename');
	$this->error_count = 0;
	$this->config_model = new EyesiteModelConfig;
	$this->config_data = $this->config_model->getData();
	$this->history_model = new EyesiteModelHistory;
		
	$this->extensions = LAE_helper::expand_cs_list($this->config_data->extensions);		// get array of included file extensions
	$this->incDirs    = LAE_helper::expand_cs_list($this->config_data->incdirs);		// get array of included directories
	$this->excDirs    = LAE_helper::expand_cs_list($this->config_data->excdirs);		// get array of excluded directories
	$this->excFiles   = LAE_helper::expand_cs_list($this->config_data->excfiles);		// get array of excluded files

// These traces are not normally needed
//	LAE_trace::trace("extensions: ".print_r($this->extensions,true));
//	LAE_trace::trace("incDirs: ".print_r($this->incDirs,true));
//	LAE_trace::trace("excDirs: ".print_r($this->excDirs,true));
//	LAE_trace::trace("excFiles: ".print_r($this->excFiles,true));

// purge the history table

	$this->db_model->ladb_execute("DELETE FROM `#__eye_site_history` WHERE DATEDIFF(CURDATE(),`datetime`) > ".$this->config_data->days_to_keep);

// get the number of rows before the scan

	$this->initial_count = $this->db_model->ladb_loadResult("Select Count(*) From `#__eye_site`");

// get the number of rows not in status OK before the scan

	$this->changes_at_start = $this->db_model->ladb_loadResult("Select Count(*) As count From `#__eye_site` Where `state` != ".LAE_STATE_OK);

// create a scan start history record

	$this->history_details = '';
	if ($this->initial_count === false)
		{
		LAE_trace::trace($this->db_model->ladb_error_text);
		self::add_history($this->db_model->ladb_error_text);
		self::add_history(JText::_('COM_EYESITE_SCANNER_ABANDONING'));
		}

	$this->history_summary = '';
	if ($this->source == 'plugin')
		$this->history_summary .= JText::_('COM_EYESITE_STARTED_BY_PLUGIN');
	if ($this->source == 'admin')
		$this->history_summary .= JText::_('COM_EYESITE_STARTED_BY_ADMIN');
	if ($this->initial_count == 0)
		$this->history_summary .= ' ('.JText::_('COM_EYESITE_INITIAL_SCAN').')';
	if (!empty($this->initial_count))
		$this->history_summary .= '. '.JText::_('COM_EYESITE_TOTAL_FILES').': '.$this->initial_count;
	$this->history_summary .= '. '.JText::_('COM_EYESITE_SCANNER_TOTAL').': '.$this->changes_at_start;

	$this->history_model->store(LAE_HISTORY_SCAN_STARTED, $this->history_summary, $this->history_details);
	if ($this->initial_count === false)
		return;
	$this->history_details = '';

// call the main scanner function

	if ($this->main_scan() == 2)    // database error
		{
		LAE_trace::trace($this->db_model->ladb_error_text);
		self::add_history($this->db_model->ladb_error_text);
		self::add_history(JText::_('COM_EYESITE_SCANNER_ABANDONING'));
		$this->error_count ++;
		}

// get the number of rows after the scan

	$this->total_at_end = $this->db_model->ladb_loadResult("Select Count(*) From `#__eye_site`");

// get the number of rows not in status OK after the scan

	$this->changes_at_end = $this->db_model->ladb_loadResult("Select Count(*) As count From `#__eye_site` Where `state` != ".LAE_STATE_OK);
	
// send an email if we have new changes or any errors

	if (($this->config_data->emailto != '') and (($this->changes_at_end > $this->changes_at_start) or ($this->error_count > 0)))
		{
		if ($this->changes_at_end > $this->changes_at_start)
			$subject = JText::_('COM_EYESITE_EMAIL_SUBECT_CHANGES').' ('.$this->sitename.')';
		if ($this->error_count > 0)
			$subject = JText::_('COM_EYESITE_EMAIL_SUBECT_ERRORS').' ('.$this->sitename.')';
		$body_text = JText::_('COM_EYESITE_SCANNER_TOTAL').': '.$this->changes_at_end.'<br />';
		$body_text .= $this->history_details;
		if ($this->config_data->auto_accept)
			$body_text .= '<br />'.JText::_('COM_EYESITE_AUTOMATIC_ACCEPT');
		$email_status = LAE_helper::send_email($this->config_data, $subject, $body_text);
		self::add_history(JText::_('COM_EYESITE_EMAIL_SENT_TO').' '.$this->config_data->emailto);
		if ($email_status != '')
            {
            $this->history_model->store(LAE_HISTORY_EMAIL_FAILED, $subject, $body_text.'<br />'.$email_status);
			self::add_history($email_status);
            }
		}
		
// create a scan completion history record

	$this->history_state = LAE_HISTORY_SCAN_NO_CHANGES;
	$this->history_summary = JText::_('COM_EYESITE_SCANNER_NO_NEW');
	
	if ($this->changes_at_end > $this->changes_at_start)
		{
		$this->history_state = LAE_HISTORY_SCAN_CHANGES;
		$this->history_summary = JText::_('COM_EYESITE_SCANNER_CHANGES');
		}
		
	if ($this->error_count > 0)
		{
		$this->history_state = LAE_HISTORY_SCAN_ERROR;
		$this->history_summary = JText::_('COM_EYESITE_SCANNER_ERRORS');
		}

	if (!empty($this->total_at_end))
		$this->history_summary .= '. '.JText::_('COM_EYESITE_TOTAL_FILES').': '.$this->total_at_end;

	$this->history_summary .= '. '.JText::_('COM_EYESITE_SCANNER_TOTAL').': '.$this->changes_at_end;

	$this->history_model->store($this->history_state, $this->history_summary, $this->history_details);
	
// if auto-accept is configured, accept all the changes
// v5.00 - but only if we were called by the plugin, and only if there were any changes

	if ( ($this->config_data->auto_accept) and ($this->source == 'plugin') and ($this->changes_at_end > 0) )
		{
		LAE_trace::trace('Automatically accepting changes',true);
		$data_model = new EyesiteModelData;
		$data_model->accept_all();
		$summary = JText::_('COM_EYESITE_AUTOMATIC_ACCEPT');
		$this->history_model->store(LAE_HISTORY_ACCEPT_ALL, $summary, '' );
		}

	LAE_trace::trace('Scan complete, '.$this->changes_at_end.' changes',true);
    self::scan_unlock();
}

//-------------------------------------------------------------------------------
// Scan the configured directory structures
// Returns 0 if no errors, or 2 for a database error
//
function main_scan()
{
// set all states to "RUNNING"

	$query = "Update `#__eye_site` Set `state` = ".LAE_STATE_RUNNING." Where `state` = ".LAE_STATE_OK;
	LAE_trace::trace("$query");
	$result = $this->db_model->ladb_execute($query);
	if ($result === false)
		return 2;

// process all the directories
// this should set all the states to either OK or CHANGED
// and could create some with state NEW

	@clearstatcache();
	$numIncDirs = count($this->incDirs);
	for ($i = 0; $i < $numIncDirs; $i = $i+2)
		{
		LAE_trace::trace('Scanning directory: '.$this->incDirs[$i]);
		$filelist = self::dirList($this->incDirs[$i],$this->incDirs[$i+1]);
		$query = "Start Transaction";
		LAE_trace::trace("$query");
		$this->db_model->ladb_execute($query);
		foreach ($filelist as $file)
			{
			$ret = $this->process_file($file);
			if ($ret == 2)				// if we have a database problem, don't continue
				return 2;
			if ($ret == 1)				// if we have a file error, count it and continue - some files could be locked
				$this->error_count ++;
			}
		$query = "Commit";
		LAE_trace::trace("$query");
		$this->db_model->ladb_execute($query);
		}

// any records still in state "RUNNING" were not found in the file system and are newly deleted
// log the newly deleted files

	$query = "Select `filepath` From `#__eye_site` Where `state` = ".LAE_STATE_RUNNING;
	LAE_trace::trace("$query");
	$rows = $this->db_model->ladb_loadObjectList($query);
	if ($rows === false)
		return 2;
	$numrows = count($rows);
	for ($i = 0; $i < $numrows; $i++)
		{
		$row = $rows[$i];
		$filepath = $row->filepath;
		self::add_history(JText::_('COM_EYESITE_STATE_DELETED').': '.$filepath);
		}

// set the newly deleted files to LAE_STATE_DELETED

	$query = "Update `#__eye_site` Set `state` = ".LAE_STATE_DELETED.", `date_checked` = NOW() Where `state` = ".LAE_STATE_RUNNING;
	LAE_trace::trace("$query");
	$result = $this->db_model->ladb_execute($query);
	if ($result === false)
		return 2;

	return 0;
}

//-------------------------------------------------------------------------------
// Process one file
// Returns 0 if no errors, 1 for a file error, or 2 for a database error
//
function process_file($filepath)
{
	if (self::_in_arrayi($filepath, $this->excFiles))
		{
		LAE_trace::trace("Excluding file: $filepath");
		return 0;							// this file is excluded
		}
	
	$utf8_filepath = utf8_encode($filepath);
	
	if (!is_readable($filepath))
		{
		LAE_trace::trace("Unreadable file: $filepath");
		self::add_history(JText::_('COM_EYESITE_SCANNER_ERROR_READ').' '.$utf8_filepath);
		return 1;
		}

	$hash = md5_file($filepath); 
	if (!$hash)
		{
		LAE_trace::trace("Unable to calculate md5 for: $filepath");
		self::add_history(JText::_('COM_EYESITE_SCANNER_ERROR_MD5').' '.$utf8_filepath);
		return 1;
		}
		
	$query = "Select `md5`, `state`, UNIX_TIMESTAMP(`datetime`) as unix_datetime, `filesize`
				From `#__eye_site` Where `filepath` = '".addslashes($filepath)."'";
//	LAE_trace::trace("$query");		// Creates a huge trace and not usually required
	$row = $this->db_model->ladb_loadObject($query);
	if ($row === false)
		return 2;

// if the file is not registered in the database, insert it in state NEW

	if (empty($row))
		{
		self::add_history(JText::_('COM_EYESITE_STATE_NEW').":\t".$utf8_filepath);
		$filesize = filesize($filepath);		// allow zero length files
		$unix_filetime = filemtime($filepath);	// but not zero time files
		if (!$unix_filetime)
			{
			LAE_trace::trace("Unable to get modification date for $utf8_filepath");
			self::add_history(JText::_('COM_EYESITE_SCANNER_ERROR_DATE').' '.$utf8_filepath);
			return 1;
			}
		$sql_filetime = strftime(LAE_SQL_DATE_FORMAT, $unix_filetime);
		$query = "Insert into `#__eye_site` (`filepath`, `md5`, `state`, `datetime`, `filesize`, `new_md5`, `new_datetime`, `new_filesize`, `date_added`, `date_checked`)
			values ('".addslashes($filepath)."', '$hash', ".LAE_STATE_NEW.", '$sql_filetime', ".intval($filesize).", '', 0, 0, NOW(), 0)";
		LAE_trace::trace("$query");
		$result = $this->db_model->ladb_execute($query);
		if ($result === false)
			return 2;
		return 0;	// we're done for this file
		}
		
// here when the file is in the database
// if it already has a problem flagged, leave it alone

	if ($row->state != LAE_STATE_RUNNING)
		return 0;

// if the hash value matches, set the state to OK

	if ($row->md5 == $hash)				// compare file hash with database hash
		{
		$query = "Update `#__eye_site` Set `state` = ".LAE_STATE_OK.", `date_checked` = NOW() Where `filepath` = '".addslashes($filepath)."'";
		// LAE_trace::trace("$query");		// not normally needed and creates a huge trace file
		$result = $this->db_model->ladb_execute($query);
		if ($result === false)
			return 2;
		return 0;	// we're done for this file
		}
		
// the hash value doesn't match, so set the state to CHANGED
// and note the new file details

	$oldfilesize = $row->filesize;
	$newfilesize = filesize($filepath);
	$new_unix_filetime = filemtime($filepath);
	$new_sql_filetime = strftime(LAE_SQL_DATE_FORMAT, $new_unix_filetime);
	$change1 = JText::_('COM_EYESITE_STATE_CHANGED').' '.$utf8_filepath;
	$change2 = "&nbsp;&nbsp;&nbsp;".JText::_('COM_EYESITE_SCANNER_ORIG_DATE').' '.strftime(LAE_SQL_DATE_FORMAT, $row->unix_datetime).', '.JText::_('COM_EYESITE_SCANNER_SIZE').': '.$oldfilesize;
	$change3 = "&nbsp;&nbsp;&nbsp;".JText::_('COM_EYESITE_SCANNER_NEW_DATE').' '.strftime(LAE_SQL_DATE_FORMAT, $new_unix_filetime).', '.JText::_('COM_EYESITE_SCANNER_SIZE').': '.$newfilesize;
	self::add_history($change1);
	self::add_history($change2);
	self::add_history($change3);
	$query = "Update `#__eye_site` Set `state` = ".LAE_STATE_CHANGED.",
		`new_md5` = '$hash', `new_datetime` = '$new_sql_filetime', `new_filesize` = ".intval($newfilesize)."
		Where `filepath` = '".addslashes($filepath)."'";
	LAE_trace::trace("$query");
	$result = $this->db_model->ladb_execute($query);
	if ($result === false)
		return 2;
	return 0;
}

//-------------------------------------------------------------------------------
// list matching files in a directory and return as an array
//
function dirList($directory, $recurse)
{
//	LAE_trace::trace($directory.', '.$recurse); // Creates a huge trace and not usually required
	$results = array();
	if (self::_in_arrayi($directory, $this->excDirs))
		{
		LAE_trace::trace("Excluding directory: $directory");
		return $results;							// this directory is excluded
		}
	if (!is_readable($directory))                   // v5.00
        {
		self::add_history(JText::_('COM_EYESITE_DIR_INC_ERROR3').' '.$directory);
		$this->error_count ++;
        return $results;
        }

	if ($handle = opendir($directory)) 
		{
		while ($filename = readdir($handle))
			{
			if ($filename != "." && $filename != "..") 
				{
				if (is_dir($directory."/".$filename)) 
					{
					if ($recurse == "S") 
						$results = array_merge($results, self::dirList($directory."/".$filename, $recurse));
					} 
				else 
					{
					$ext = pathinfo($filename, PATHINFO_EXTENSION);// get the file extension
					if ((empty($this->extensions)) or (self::_in_arrayi($ext,$this->extensions)))
						{										// yes...
						$filename = $directory."/".$filename;	// make full pathname
						$results[] = $filename;					// and store in results array
						}
					}
				}
			}
		closedir($handle);
		}
	return $results;
}

//-------------------------------------------------------------------------------
// Add text to the history buffer
//
function add_history($text)
{
	if (!empty($this->history_details))
		$this->history_details .= '<br />';
	$this->history_details .= $text;
}

//-------------------------------------------------------------------------------
// Case insensitive in_array
//
static function _in_arrayi($needle, $haystack) 
{
    foreach($haystack as $value)
        if (strtolower($value) == strtolower($needle)) 
			return true;
    return false;
}

//-------------------------------------------------------------------------------
// Check that the correct entry parameter was specified
// for admin scans it should be the site secret
// for plugin scans it must be the last runtime
// (the plugin Ajax path is potentially visible externally so the entry parameter must be hard to guess)
//
function check_entry_requirements()
{
    if (!$this::scan_lock())
		{
		LAE_trace::trace('Scanner stopping because lock file exists',true);
		return;
		}
    switch ($this->source)
        {
        case 'admin':
            if ($this->entry == $this->app->get('secret'))
                return true;
            LAE_trace::trace('Scanner stopping because admin entry parameter incorrect: '.$this->entry,true);
            return false;

        case 'plugin':
            $query = "SELECT `params` FROM `#__extensions` WHERE `type` = 'plugin' AND `folder` = 'system' AND `element` = 'eyesite'";
            $plugin_params = $this->db_model->ladb_loadResult($query);
            if ($plugin_params === false)
                {
                LAE_trace::trace("Scanner stopping because plugin entry parameter cannot be verified: ".$this->db_model->ladb_error_text, true);
                return false;
                }
            $plugin_params_object = json_decode($plugin_params);
            if (!empty($plugin_params_object->entry_code))
                $entry_code = $plugin_params_object->entry_code;
            else
                $entry_code = 0;
            if ($this->entry == $entry_code)
                {                                                           // ok, we are going to run so update the entry_code and last_run_time
                $plugin_params_object->last_run_time = time();
                $plugin_params_object->entry_code = uniqid(rand());
                $plugin_params = $this->db_model->ladb_quote(json_encode($plugin_params_object));
                $query = "UPDATE `#__extensions` SET `params` = $plugin_params WHERE `type` = 'plugin' AND `folder` = 'system' AND `element` = 'eyesite'";
                $this->db_model->ladb_execute($query);
                LAE_trace::trace("Scanner updating plugin last_run_time to ".$plugin_params_object->last_run_time." and entry_code to ".$plugin_params_object->entry_code,true);
                return true;
                }
            LAE_trace::trace('Scanner stopping because plugin entry parameter incorrect: '.$this->entry,true);
            return false;
        default:
            LAE_trace::trace('Scanner stopping because plugin source parameter incorrect: '.$this->source,true);
            return false;
        }
    return false;   // cannot get here
}

//-------------------------------------------------------------------------------
// check or create the lock file so that only one scan can run at a time
// returns true if the scan can run, false if not
//
function scan_lock()
{
	$tmp_path = $this->app->get('tmp_path');	
	$lock_file_path = $tmp_path.'/eyesite.lock';
    
// if the lock file is more than 15 minutes old, delete it (a scan shouldn't take that long)
// if it's less than 15 minutes old, don't run

	if (file_exists($lock_file_path))
		{
		@clearstatcache();
		if ((time() - filemtime($lock_file_path)) > LAE_MAX_LOCK_SECONDS)
			@unlink($lock_file_path);	
		else
			return false;
		}

// if we get here the lock file doesn't exist so we'll try to create it
// with mode 'x', the open will fail if another process got there first

    LAE_trace::trace("Scanner creating lock file",true);
	$lock_file = @fopen($lock_file_path,'x');       
	if ($lock_file == false)
		return false;
        
// this thread created the lock file so we can run

    LAE_trace::trace("Scanner ok to run",true);
    return true;
}

//-------------------------------------------------------------------------------
// delete the scan lock file
//
static function scan_unlock()
{
	$app = JFactory::getApplication();
	$tmp_path = $app->get('tmp_path');	
	$lock_file_path = $tmp_path.'/eyesite.lock';
    LAE_trace::trace("Scanner deleting lock file",true);
	@unlink($lock_file_path);
}


} // class


