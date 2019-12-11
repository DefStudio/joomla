<?php
/*
 * @package		JPlant.Framework
 * @author		http://www.j-plant.com
 * @copyright	Copyright (C) 2010 J-Plant. All rights reserved.
 * @license		GNU/GPL v. 3.0
 * 
 * JPlant Framework is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * 
 */
defined('_JEXEC') or die('Restricted access');

if (function_exists('t3import'))
	t3import('core.joomla.modulehelper');
else
	jimport('joomla.application.module.helper');

JPlantLoader::import('core.joomla.application.helper');

class JPlantModuleHelper extends JModuleHelper {
	static function getModuleById($id) {
		$module	= null;
		$modules = JPlantModuleHelper::getModules();
		if (isset($modules[$id]))
			$module =& $modules[$id]; 

		return $module;		
	}
	
	static function createModule($modName) {
		$module = null;
		jimport('joomla.filter.filterinput');
		$modName = JFilterInput::clean($modName, 'CMD');
		if (empty($modName) || substr($modName, 0, 4) != 'mod_')
			return $module;

		$modPath = JPATH_ROOT . '/modules/' . $modName . '/' . $modName . '.xml';
		if (!file_exists($modPath) || !is_file($modPath))
			return $module;

		$langbase = JPATH_ROOT;
		$lang = JFactory::getLanguage();

		$module	= new stdClass();
		$module->id = uniqid('mod_', false);
		$module->file = $modPath;
		$module->module	= $modName;
		$module->path = dirname($modPath);
		$module->user = 0;
		$module->style = null;
		$module->position = null;
		$module->title = null;
		$module->showtitle = false;
		$module->control = null;
		$module->content = '';
		$module->params = '';

		$ver = new JVersion();
		$ver30 = version_compare($ver->getShortVersion(), '3.0.0', '>=');
		
		$data = null;
		if ($ver30) {
			jimport('joomla.installer.installer');
		
			$data = JInstaller::parseXMLInstallFile($modPath);
		} else {		
			$data = JApplicationHelper::parseXMLInstallFile($modPath);
		}

		if ($data)
			foreach($data as $key => $value)
				$module->$key = $value;

		$lang->load($module->module, $langbase);

		return $module;
	}

	static function &getModules($position = null) {
		static $modules;

		if (!is_null($modules))
			return $modules;

		$mainframe = JFactory::getApplication();
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$groups	= implode(',', $user->getAuthorisedViewLevels());

		$query = $db->getQuery(true);
		$query->select('id, title, module, position, content, showtitle, params, mm.menuid');
		$query->from('#__modules AS m');
		$query->join('LEFT','#__modules_menu AS mm ON mm.moduleid = m.id');
		$query->where('m.published = 1');

		$clientid = (int) $mainframe->getClientId();
		if (!$user->authorise('core.admin',1)) {
			$query->where('m.access IN ('.$groups.')');
		}
		$query->where('m.client_id = '. $clientid);
		$query->order('position, ordering');
		
		$db->setQuery($query);
		$modules = $db->loadObjectList('id');
		if ($db->getErrorNum()) {
			$modules = array();
		}

		foreach ($modules as $key => $mod) {
			$module =& $modules[$key];
			$file = $module->module;
			$custom = substr($file, 0, 4) == 'mod_' ? 0 : 1;
			$module->user = $custom;
			$module->name = $custom ? $module->title : substr($file, 4);
			$module->style = null;
			$module->position = strtolower($module->position);
		}

		return $modules;
	}
}