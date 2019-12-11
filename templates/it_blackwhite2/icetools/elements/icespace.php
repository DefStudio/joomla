<?php 
/**
 * $ModDesc
 * 
 * @version   $Id: $file.php $Revision
 * @package   modules
 * @subpackage  $Subpackage.
 * @copyright Copyright (C) November 2010 LandOfCoder.com <@emai:landofcoder@gmail.com>.All rights reserved.
 * @license   GNU General Public License version 2
 */
 
// no direct access
defined('_JEXEC') or die;
/**
 * Get a collection of categories
 */
class JFormFieldIcespace extends JFormField {
	
	/*
	 * Category name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $type = 'icespace'; 	
	
	/**
	 * fetch Element 
	 */
	protected function getInput(){		
			if (!defined ('ICE_LOADMEDIACONTROL')) {
				define ('ICE_LOADMEDIACONTROL', 1);
				$uri = str_replace(DIRECTORY_SEPARATOR,"/",str_replace( JPATH_SITE, JURI::base (), dirname(__FILE__) ));
				$uri = str_replace("/administrator/", "", $uri);		
				$document = &JFactory::getDocument();
				$document->addCustomTag('<link type="text/css" rel="stylesheet" href="'.$uri."/media/".'form.css'.'"/>');
			}
		JFactory::getApplication()->setUserState('editor.source.syntax',"css");
	}		
	function getLabel(){
		return ;	
	}
}

?>
