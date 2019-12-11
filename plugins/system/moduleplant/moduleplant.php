<?php
/*
 * @package		Module Plant
 * @author		http://www.j-plant.com
 * @copyright	Copyright (C) 2010 J-Plant. All rights reserved.
 * @license		GNU/GPL v. 3.0
 * 
 * Module Plant is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * 
 */

defined('_JEXEC') or die('Restricted access');

require_once dirname(__FILE__) . '/moduleplant/loader.php';

JPlantLoader::import('core.joomla.plugin.extcontent');
JPlantLoader::import('core.joomla.application.module.helper');
jimport('joomla.html.parameter');

class plgSystemModuleplant extends JPlantJoomlaExtContentPlugin {	
	function plgSystemModuleplant($subject, $config) {
		$this->_pluginName = 'moduleplant';

		parent::__construct($subject, $config);
	}

	function _contentHandler($params, $text, $originalText) {
		$module = null;
		if (isset($params['id']))
			$module = JPlantModuleHelper::getModuleById(intval($params['id'], 10));

		if (is_null($module)) {
			if (isset($params['virtual']) && ((bool)$params['virtual']) && !empty($params['module'])) {
				$module = JPlantModuleHelper::createModule($params['module']);
			}

			if (is_null($module))
				return '';
		}

		$showTitle = isset($params['mod_showtitle']) ? $params['mod_showtitle'] : null;
		if (is_null($showTitle))
			$showTitle = $this->params->get('mod_showtitle', '');

		if (!is_string($showTitle) || strlen($showTitle) > 0)
			$module->showtitle = (bool)$showTitle;

		$renderAttrs = $this->_getRenderAttrs($this->params, $params);
		$moduleParams = $this->_getModuleParams($params);
		if (count($moduleParams) > 0) {
			$modParams = null;
			if (JPLANT_J15)
				$modParams = new JParameter($module->params);
			else {
				$modParams = new JRegistry();
				$modParams->loadString($module->params);
			}
				
			
			foreach ($moduleParams as $modParamKey => $modParamValue) {
				$modParams->set($modParamKey, $modParamValue);
			}

			$module->params = $modParams->toString();
		}

		return '<!-- This module is loaded by Module Plant plugin: http://www.j-plant.com -->'
			. JPlantModuleHelper::renderModule($module, $renderAttrs);
	}
	
	function _getRenderAttrs($baseParams, $manualParams) {
		$attrs = array();
		
		$style = $baseParams->get('style', !empty($manualParams['style']) ? $manualParams['style'] : 'none');
		
		$attrs['style'] = $style;
		
		return $attrs;
	}
	
	function _getModuleParams($params) {
		unset($params['id']);
		unset($params['style']);
		unset($params['mod_showtitle']);
		
		return $params;
	}
}