<?php
/**
 * Plugin Helper File
 *
 * @package         Cache Cleaner
 * @version         3.7.0
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';

nnFrameworkFunctions::loadLanguage('plg_system_cachecleaner');

class plgSystemCacheCleanerHelper
{
	var $helpers = array();
	var $show_message = false;

	public function __construct(&$params)
	{
		$params->size = 0;
		$params->message = '';
		$params->error = false;

		$this->params = $params;

		require_once __DIR__ . '/helpers/helpers.php';
		$this->helpers = plgSystemCacheCleanerHelpers::getInstance($params);
	}

	function clean()
	{
		if (JFactory::getApplication()->input->getString('purge_maxcdn'))
		{
			$this->helpers->get('maxcdn')->purge();

			die($this->params->error ?: 1);
		}

		if (JFactory::getApplication()->input->getString('purge_keycdn'))
		{
			$this->helpers->get('keycdn')->purge();

			die($this->params->error);
		}

		if (!$type = $this->getCleanType())
		{
			return;
		}

		// Load language for messaging
		nnFrameworkFunctions::loadLanguage('mod_cachecleaner');

		$this->purgeCache($type);

		// only handle messages in html
		if (JFactory::getDocument()->getType() != 'html')
		{
			return false;
		}

		$error = $this->helpers->getParams()->error;
		if ($error)
		{
			$message = JText::_('CC_NOT_ALL_CACHE_COULD_BE_REMOVED');
			$message .= $this->helpers->getParams()->error !== true ? '<br />' . $this->helpers->getParams()->error : '';
		}
		else
		{
			$message = $this->helpers->getParams()->message ?: JText::_('CC_CACHE_CLEANED');

			if ($this->params->show_size && $this->helpers->getParams()->size)
			{
				$message .= ' (' . $this->helpers->get('cache')->getSize() . ')';
			}
		}

		if (JFactory::getApplication()->input->getInt('break'))
		{
			echo (!$error ? '+' : '') . str_replace('<br />', ' - ', $message);
			die;
		}

		if ($this->show_message && $message)
		{
			JFactory::getApplication()->enqueueMessage($message, ($error ? 'error' : 'message'));
		}
	}

	function getCleanType()
	{
		$cleancache = JFactory::getApplication()->input->getString('cleancache');

		// Clean via url
		if ($cleancache != '')
		{
			// Return blank if on frontend and no secret url key is given
			if (JFactory::getApplication()->isSite() && ($cleancache == '' || $cleancache != $this->params->frontend_secret))
			{
				return '';
			}

			$this->show_message = true;

			return 'clean';
		}

		// Clean via save task
		if ($this->passTask())
		{
			return 'save';
		}


		return '';
	}

	function passTask()
	{
		if (!$task = JFactory::getApplication()->input->get('task'))
		{
			return false;
		}

		$task = explode('.', $task, 2);
		$task = isset($task['1']) ? $task['1'] : $task['0'];
		if (strpos($task, 'save') === 0)
		{
			$task = 'save';
		}

		$tasks = array_diff(array_map('trim', explode(',', $this->params->auto_save_tasks)), array(''));

		if (empty($tasks) || !in_array($task, $tasks))
		{
			return false;
		}

		if (JFactory::getApplication()->isAdmin() && $this->params->auto_save_admin)
		{
			$this->show_message = $this->params->auto_save_admin_msg;

			return true;
		}

		if (JFactory::getApplication()->isSite() && $this->params->auto_save_front)
		{
			$this->show_message = $this->params->auto_save_front_msg;

			return true;
		}

		return false;
	}

	function purgeCache($type = 'clean')
	{
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		// Joomla cache
		$this->helpers->get('joomla')->purge();


		// Folders
		if ($type == 'clean'
			|| ($type == 'save' && $this->params->auto_save_clean_folders)
		)
		{
			$this->helpers->get('folders')->purge();
		}


		// Purge expired cache
		if ($this->params->purge)
		{
			$this->helpers->get('joomla')->purgeExpired();
		}

		// Purge update cache
		if ($this->params->purge_updates)
		{
			$this->helpers->get('joomla')->purgeUpdates();
		}

	}

}
