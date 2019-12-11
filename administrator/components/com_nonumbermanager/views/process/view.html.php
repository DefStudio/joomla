<?php
/**
 * @package         NoNumber Extension Manager
 * @version         4.8.7
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View for the install processes
 */
class NoNumberManagerViewProcess extends JViewLegacy
{
	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$action = JFactory::getApplication()->input->get('action');
		if ($action)
		{
			$model = $this->getModel();
			if ($action == 'install')
			{
				echo $model->install(JFactory::getApplication()->input->get('id'), JFactory::getApplication()->input->getString('url'));
				die;
			}
			else if ($action == 'uninstall')
			{
				echo $model->uninstall(JFactory::getApplication()->input->get('id'));
				die;
			}
		}

		$this->items = $this->get('Items');
		$this->getConfig();

		parent::display($tpl);
	}

	/**
	 * Function that gets the config settings
	 */
	protected function getConfig()
	{
		if (!isset($this->config))
		{
			require_once JPATH_PLUGINS . '/system/nnframework/helpers/parameters.php';
			$parameters = nnParameters::getInstance();
			$this->config = $parameters->getComponentParams('nonumbermanager');
		}

		return $this->config;
	}
}
