<?php
/********************************************************************
Product		: Eyesite
Date		: 8 June 2017
Copyright	: Les Arbres Design 2009-2017
Contact		: http://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

define("LAE_TRACE_FILE_NAME", 'trace.txt');
define("LAE_TRACE_FILE_PATH", JPATH_ROOT.'/components/com_eyesite/trace.txt');
define("LAE_TRACE_FILE_URL", JURI::root().'components/com_eyesite/trace.txt');
define("LAE_MAX_TRACE_SIZE", 2000000);	// about 2Mb
define("LAE_MAX_TRACE_AGE",   21600);		// maximum trace file age in seconds (6 hours)
define("LAE_UTF8_HEADER",     "\xEF\xBB\xBF");	// UTF8 file header

if (class_exists("LAE_trace"))
	return;

class LAE_trace
{

//-------------------------------------------------------------------------------
// Write an entry to the trace file
// Tracing is ON if the trace file exists
// if $no_time is true, the date time is not added
//
static function trace($data, $showtime=false)
{
	if (@!file_exists(LAE_TRACE_FILE_PATH))
		return;
	if (filesize(LAE_TRACE_FILE_PATH) > LAE_MAX_TRACE_SIZE)
		{
		@unlink(LAE_TRACE_FILE_PATH);
		@file_put_contents(LAE_TRACE_FILE_PATH, LAE_UTF8_HEADER.date("d/m/y H:i").' New trace file created'."\n");
		}
    if ($showtime)
    	@file_put_contents(LAE_TRACE_FILE_PATH, date("H:i").' '.$data."\n",FILE_APPEND);
    else
    	@file_put_contents(LAE_TRACE_FILE_PATH, '  '.$data."\n",FILE_APPEND);
}

//-------------------------------------------------------------------------------
// Start a new trace file
//
static function init_trace($config_data)
{
	self::delete_trace_file();
	@file_put_contents(LAE_TRACE_FILE_PATH, LAE_UTF8_HEADER.date("d/m/y H:i").' Tracing Initialised'."\n");
	
	$locale = setlocale(LC_ALL,0);
	$app = JFactory::getApplication();
	$lang_obj = JFactory::getLanguage('JPATH_SITE');
	$languages = $lang_obj->getKnownLanguages();
	$lang_info = count($languages);
	foreach ($languages as $language)
		$lang_info .= ' '.$language['tag'];

	self::trace('Eyesite version  : '.self::getComponentVersion());
	self::trace('Eyesite plugin   : '.self::getPluginStatus());
	self::trace("Joomla version   : ".JVERSION);
	self::trace("PHP version      : ".phpversion());
	self::trace("Server OS        : ".PHP_OS);
	self::trace("PHP locale       : ".print_r($locale, true));
	self::trace("Joomla languages : ".$lang_info);
	self::trace("JPATH_ROOT       : ".JPATH_ROOT);
	self::trace("JURI::root()     : ".JURI::root());
	self::trace("Config live_site : ".$app->get('live_site'));
	if ((function_exists('get_magic_quotes_gpc')) and (get_magic_quotes_gpc()))
		self::trace("Magic quotes     : ON");
	self::trace("Eyesite config   : ".print_r($config_data,true));
}

//-------------------------------------------------------------------------------
// Trace an entry point
// Tracing is ON if the trace file exists
//
static function trace_entry_point($front=false)
{
	if (@!file_exists(LAE_TRACE_FILE_PATH))
		return;
		
// if the trace file is more than 6 hours old, delete it, which will switch tracing off
//  - we don't want trace to be left on accidentally

	$filetime = @filectime(LAE_TRACE_FILE_PATH);
	if (time() > ($filetime + LAE_MAX_TRACE_AGE))
		{
		self::delete_trace_file();
		return;
		}
		
	$date_time = date("d/m/y H:i").' ';	
	
	if ($front)
		self::trace("\n".$date_time.'================================ [Front Entry Point] ================================');
	else
		self::trace("\n".$date_time.'================================ [Admin Entry Point] ================================');
		
	if ($front)
		{
		if (isset($_SERVER["REMOTE_ADDR"]))
			$ip_address = '('.$_SERVER["REMOTE_ADDR"].')';
		else
			$ip_address = '';

		if (isset($_SERVER["HTTP_USER_AGENT"]))
			$user_agent = $_SERVER["HTTP_USER_AGENT"];
		else
			$user_agent = '';

		if (isset($_SERVER["HTTP_REFERER"]))
			$referer = $_SERVER["HTTP_REFERER"];
		else
			$referer = '';
			
		$method = $_SERVER['REQUEST_METHOD'];

		self::trace("$method from $ip_address $user_agent");
		if ($referer != '')
			self::trace('Referer: '.$referer, true);
		}

	if (!empty($_POST))
		self::trace("Post data: ".print_r($_POST,true));
	if (!empty($_GET))
		self::trace("Get data: ".print_r($_GET,true));
}

//-------------------------------------------------------------------------------
// Delete the trace file
//
static function delete_trace_file()
{
	if (@file_exists(LAE_TRACE_FILE_PATH))
		@unlink(LAE_TRACE_FILE_PATH);
}

//-------------------------------------------------------------------------------
// Return true if tracing is currently active
//
static function tracing()
{
	if (@file_exists(LAE_TRACE_FILE_PATH))
		return true;
	else
		return false;
}

//-------------------------------------------------------------------------------
// Make the html for the help and support page
// The controller must contain the trace_on() and trace_off() functions
//
static function make_trace_controls()
{
	$html = '<div>';
	$html .= 'Diagnostic Trace Mode: ';
	$html .= LAE_helper::make_info('Create a trace file to send to support. Please remember to switch off after use.');
    $onclick = ' onclick="document.adminForm.task.value=\'trace_on\'; document.adminForm.submit();"';
    $html .= ' <button class="btn"'.$onclick.'>On</button>';
	$onclick = ' onclick="document.adminForm.task.value=\'trace_off\'; document.adminForm.submit();"';
    $html .= ' <button class="btn"'.$onclick.'>Off</button>';

	if (file_exists(LAE_TRACE_FILE_PATH))
		$html .= ' <a href="'.LAE_TRACE_FILE_URL.'" target="_blank"> Trace File</a>';
	else
		$html .= ' Tracing is currently OFF';

	$html .= '</div>';
	return $html;
}

//-------------------------------------------------------------------------------
// Get the component version from the component manifest XML file
//
static function getComponentVersion()
{
	$xml_array = JInstaller::parseXMLInstallFile(JPATH_ADMINISTRATOR.'/components/com_eyesite/eyesite.xml');
	return $xml_array['version'];
}

//-------------------------------------------------------------------------------
// Get the plugin status
//
static function getPluginStatus()
{
	$plugin_path = '/plugins/system/eyesite/eyesite.xml';

	if (!file_exists(JPATH_ROOT.$plugin_path))
		return 'Not installed';
		
	$xml_array = JInstaller::parseXMLInstallFile(JPATH_ROOT.$plugin_path);
	$version = $xml_array['version'];
		
	if (JPluginHelper::isEnabled('system', 'eyesite'))
		return 'Version '.$version.' installed and enabled';
		
	return 'Version '.$version.' installed but disabled';
}


}