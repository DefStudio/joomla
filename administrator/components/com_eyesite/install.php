<?php
/********************************************************************
Product		: Eyesite
Date		: 8 June 2017
Copyright	: Les Arbres Design 2009-2017
Contact		: http://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

class com_EyesiteInstallerScript
{
public function preflight($type, $parent) 
{
	$version = new JVersion();  			// get the Joomla version (JVERSION did not exist before Joomla 2.5)
	$joomla_version = $version->RELEASE.'.'.$version->DEV_LEVEL;
	$app = JFactory::getApplication();

	if (version_compare($joomla_version,"3.4.8","<"))
		{
        $app->enqueueMessage("Eyesite requires at least Joomla 3.4.8. Your version is $joomla_version", 'error');
		return false;
		}
		
	if (get_magic_quotes_gpc())
		{
        $app->enqueueMessage("Eyesite cannot run with PHP Magic Quotes ON. Please switch it off and re-install.", 'error');
		return false;
		}

// we only support MySql

	$dbtype = $app->get('dbtype');
	if (!strstr($dbtype,'mysql'))
		{
        $app->enqueueMessage("Eyesite currently only supports MYSQL databases. It cannot run with $dbtype", 'error');
		return false;
		}
        
	$this->_db = JFactory::getDBO();
    $db_version = $this->ladb_loadResult('select version()');
	if (version_compare($db_version,"5.5.3","<"))
		{
        $app->enqueueMessage("Eyesite requires at least MySql 5.5.3. Your version is $db_version", 'error');
		return false;
		}
		
// do not install if an old incompatible version of the plugin is installed

    if (file_exists(JPATH_ROOT.'/plugins/system/eyesite/eyesite.xml'))
        {
        $xml_array = JInstaller::parseXMLInstallFile(JPATH_ROOT.'/plugins/system/eyesite/eyesite.xml');
        $plugin_version = $xml_array['version'];
        $plugin_major_version = substr($plugin_version,0,1);
        if ($plugin_major_version < 5)
            {
            $app->enqueueMessage("Sorry, this version of Eyesite is not compatible with the version of the Eyesite Plugin currently installed ($plugin_version). ".
                "Please uninstall or upgrade the Plugin before you install this version of Eyesite. We apologise for the inconvenience. ".
                "We try not to break compatibility between the component and the plugin but in this case it was necessary. ".
                "The new version has NOT been installed.", 'error');
            return false;
            }
        }
		
	return true;
}

public function uninstall($parent)
{ 
	$this->_db = JFactory::getDBO();
	$this->ladb_execute("DROP TABLE IF EXISTS `#__eye_site`;");
	$this->ladb_execute("DROP TABLE IF EXISTS `#__eye_site_history`;");
    $app = JFactory::getApplication();
    $app->enqueueMessage("Eyesite has been uninstalled", 'message');
}

//-------------------------------------------------------------------------------
// The main install function
//
public function postflight($type, $parent)
{
    $app = JFactory::getApplication();

// check the Joomla version

	if (substr(JVERSION,0,1) > "3")				// if > 3
        $app->enqueueMessage("This version of Eyesite has not been tested on this version of Joomla", 'notice');
	
// get the component version from the component manifest xml file		

	$component_version = $parent->get('manifest')->version;
	
// delete redundant files from older versions

	@unlink(JPATH_ROOT.'/administrator/components/com_eyesite/toolbar.eyesite.html.php'); 
	@unlink(JPATH_ROOT.'/administrator/components/com_eyesite/toolbar.eyesite.php'); 
	@unlink(JPATH_ROOT.'/administrator/components/com_eyesite/admin.eyesite.php'); 
	@unlink(JPATH_ROOT.'/administrator/components/com_eyesite/admin.eyesite.html.php'); 
	@unlink(JPATH_ROOT.'/administrator/components/com_eyesite/install.mysql.sql'); 
	@unlink(JPATH_ROOT.'/administrator/components/com_eyesite/uninstall.mysql.sql'); 
	@unlink(JPATH_ROOT.'/administrator/components/com_eyesite/joomla15.xml'); 
	@unlink(JPATH_ROOT.'/administrator/components/com_eyesite/joomla16.xml'); 
	@unlink(JPATH_ROOT.'/administrator/components/com_eyesite/download.eyesite.php'); 
	@unlink(JPATH_ROOT.'/administrator/components/com_eyesite/common.eyesite.php'); 
	@unlink(JPATH_ROOT.'/administrator/components/com_eyesite/eyesite_helper.php'); 
	@unlink(JPATH_ROOT.'/administrator/components/com_eyesite/config.eyesite.php'); 
	@unlink(JPATH_ROOT.'/administrator/components/com_eyesite/eyesite_log.txt');
	@unlink(JPATH_ROOT.'/administrator/components/com_eyesite/install.eyesite.php');
    
    self::deleteViews(array('help','history_list','history_item'));

// we now install language files in the component directory, so must remove them from the system-wide directory, since those would take precedence
// from version 5 the plugin language files are also installed in the plugin directory

    $dirs = glob(JPATH_ADMINISTRATOR.'/language/*',GLOB_ONLYDIR);
    foreach ($dirs as $dir)
        {
        $sub_dir = basename($dir);
    	@unlink($dir.'/'.$sub_dir.'.com_eyesite.ini');
    	@unlink($dir.'/'.$sub_dir.'.com_eyesite.sys.ini');
    	@unlink($dir.'/'.$sub_dir.'.plg_system_eyesite.ini');
        }

// create our database tables - this will display an error if it fails

	$this->_db = JFactory::getDBO();
	$this->ladb_execute("CREATE TABLE IF NOT EXISTS `#__eye_site` (
		  `id` int(11) NOT NULL auto_increment,
		  `filepath` varchar(255) NOT NULL,
		  `md5` varchar(32) NOT NULL,
		  `state` tinyint(4) NOT NULL,
		  `datetime` datetime NOT NULL,
		  `filesize` bigint(20) NOT NULL,
		  `new_md5` varchar(32) NOT NULL,
		  `new_datetime` datetime NOT NULL,
		  `new_filesize` bigint(20) NOT NULL,
		  `date_added` datetime NOT NULL,
		  `date_checked` datetime NOT NULL,
		  PRIMARY KEY  (`id`),
		  KEY `Filepath` (`filepath`)
          ) ENGINE=MyIsam  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;");	

	$this->ladb_execute("CREATE TABLE IF NOT EXISTS `#__eye_site_history` (
		  `id` int(11) NOT NULL auto_increment,
		  `datetime` datetime NOT NULL,
		  `state` tinyint(4) NOT NULL,
		  `summary` varchar(255) NOT NULL,
		  `details` mediumtext NOT NULL,
		  PRIMARY KEY  (`id`)
          ) ENGINE=MyIsam  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;");
    
// (3.10) fix our component parameters format

    $this->fix_params();
    
// delete the scanner lock file

    $tmp_path = $app->get('tmp_path');
	$lock_file_path = $tmp_path.'/eyesite.lock';
	@unlink($lock_file_path);

// we are done

    $app->enqueueMessage("Eyesite $component_version installed.", 'message');
	return true;
}

//-------------------------------------------------------------------------------
// Fix our component parameters.
// In previous releases we stored the component parameters using PHP serialize()
// Here we change them to use json_encode()
//
function fix_params()
{
	$query = "SELECT `params` from `#__extensions` WHERE `type` = 'component' AND `element` = 'com_eyesite'";
	$params = $this->ladb_loadResult($query);
	if (($params === false) or (empty($params)) or ($params == '{}'))
        return;             // nothing to do
        
    if (!strstr($params,"stdClass"))
        return;             // nothing to do

	$data = unserialize($params);       // retrieve the old config data
        
// save the config data using json_encode()

    $query = "UPDATE `#__extensions` SET `params` = ".$this->_db->quote(json_encode($data))." WHERE `type` = 'component' AND `element` = 'com_eyesite'";
    $this->ladb_execute($query);
}

//-------------------------------------------------------------------------------
// Delete one or more back end views
//
static function deleteViews($views)
{
    foreach ($views as $view)
        {
        @unlink(JPATH_SITE."/administrator/components/com_eyesite/views/$view/index.html");
        @unlink(JPATH_SITE."/administrator/components/com_eyesite/views/$view/view.html.php");
        @rmdir (JPATH_SITE."/administrator/components/com_eyesite/views/$view");
        }
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
        $message = $e->getMessage();
        $this->app->enqueueMessage($message, 'error');
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
        $message = $e->getMessage();
        $this->app->enqueueMessage($message, 'error');
        return false;
		}
	return $result;
}


}
