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

class JPlantApplicationHelper {	
	static function getParamsINIFromFile($manifestFile) {
		if (!file_exists($manifestFile) || !is_file($manifestFile))
			return null;

		$manifest = JFactory::getXMLParser('Simple');
		if (!$manifest->loadFile($manifestFile)) {
			$manifest = null; 
		}
		
		$root = $manifest->document;
		if (!is_object($root) || ($root->name() != 'install' && $root->name() != 'mosinstall' && $root->name() != 'extension')) {
			$manifest = null; 
		}
		
		return JPlantApplicationHelper::getParamsINI($root);
	}

	static function getParamsINI($xml) {
		if (empty($xml))
			return null;
		
		$element =& $xml->getElementByPath('params');
		if (!is_a($element, 'JSimpleXMLElement') || !count($element->children())) {
			return null;
		}

		$params = $element->children();
		if (count($params) == 0) {
			return null;
		}

		$ini = $name = $value = null;
		foreach ($params as $param) {
			if (!($name = $param->attributes('name')) ||
				!($value = $param->attributes('default'))) {
				continue;
			}

			$ini .= $name . '=' . $value . "\n";
		}

		return $ini; 
	}

	static function getPluginPath($plugin, $type = 'content', $separator = DIRECTORY_SEPARATOR) {
		return 'plugins' . $separator . $type . $separator . (!JPLANT_J15 ? $plugin : '') . $separator; 
	}
	
	static function getPluginUri($plugin, $type = 'content') {
		return JURI::root() . '/' . JPlantApplicationHelper::getPluginPath($plugin, $type, '/');
	}
}