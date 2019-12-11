<?php
/********************************************************************
Product		: Eyesite
Date		: 8 June 2017
Copyright	: Les Arbres Design 2009-2017
Contact		: http://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

class EyesiteController extends JControllerLegacy
{

function __construct()
{
	parent::__construct();
	$this->registerTask('config_apply', 'config_save');
	$this->jinput = JFactory::getApplication()->input;
	$this->app = JFactory::getApplication();
}

function display($cachable = false, $urlparams = false)
{
	LAE_helper::addSubMenu('main');
	$data_model = $this->getModel('data');
	$config_model = $this->getModel('config');
	$config_data = $config_model->getData();
    $plugin_update_server = $config_model->plugin_update_server();
    if ($plugin_update_server and (empty($config_data->purchase_id)))
		$this->app->enqueueMessage(JText::_('COM_EYESITE_PURCHASE_ID_CONFIG'), 'notice');
	$view = $this->getView('main', 'html');
	$view->setModel($data_model);
	$view->display();
}

function history_list()
{
	LAE_helper::addSubMenu('history');
	$view = $this->getView('history', 'html');
	$history_model = $this->getModel('history');
	$view->setModel($history_model);
	$view->display();
}

function history_item()
{
	LAE_helper::addSubMenu('history');
	$id = $this->jinput->get('id', '', 'INT');
	$history_model = $this->getModel('history');
	$history_data = $history_model->getOne($id);
	$view = $this->getView('history', 'html');
	$view->history_data = $history_data;
	$view->edit();
}

function history_delete()
{
	$history_model = $this->getModel('history');
	$history_model->delete();
	$this->setRedirect('index.php?option=com_eyesite&task=history_list');
}

function history_save()
{
	$history_model = $this->getModel('history');
	$id = $this->jinput->get('id', '', 'INT');
	$summary = $this->jinput->get('summary', '', 'STRING');
	$history_model->update_summary($id, $summary);
	$this->setRedirect('index.php?option=com_eyesite&task=history_list');
}

function configure()
{
	LAE_helper::addSubMenu('config');
	$config_model = $this->getModel('config');
	$config_data = $config_model->getData();
    $plugin_update_server = $config_model->plugin_update_server();
	$view = $this->getView('config', 'html');
	$view->config_data = $config_data;
    $view->plugin_update_server = $plugin_update_server;
	$view->display();
}

function config_save()		// also config_apply
{
	$task = $this->jinput->get('task', '', 'STRING');		// 'config_save' or 'config_apply'
	$config_model = $this->getModel('config');
	$post_data = $config_model->getPostData();
	$valid = $config_model->check();
	if ($valid)
		{
		$stored = $config_model->store();
		if ($valid and $stored and ($task == 'config_save'))
			{
			$this->setRedirect('index.php?option=com_eyesite');
			return;
			}
		}
	LAE_helper::addSubMenu('config');
    $plugin_update_server = $config_model->plugin_update_server();
	$view = $this->getView('config', 'html');
	$view->config_data = $post_data;
    $view->plugin_update_server = $plugin_update_server;
	$view->display();
}

function accept_one()
{
	$id = $this->jinput->get('id', '', 'INT');
	$data_model = $this->getModel('data');
	$data_model->getOne($id);
	$history_model = $this->getModel('history');
	$history_model->store(LAE_HISTORY_ACCEPT, 
		LAE_helper::state_text($data_model->data->state).': '.$data_model->data->filepath, '' );
	$data_model->accept_one($id);
	$this->setRedirect('index.php?option=com_eyesite');
}

function reject_one()
{
	$id = $this->jinput->get('id', '', 'INT');
	$data_model = $this->getModel('data');
	$data_model->getOne($id);
	$history_model = $this->getModel('history');
	$history_model->store(LAE_HISTORY_REJECT, 
		LAE_helper::state_text($data_model->data->state).': '.$data_model->data->filepath, '' );
	$data_model->reject_one($id);
	$this->setRedirect('index.php?option=com_eyesite');
}

function accept_list()
{
	$cids = $this->jinput->get('cid', array(0), 'ARRAY');
	$data_model = $this->getModel('data');
	$history_model = $this->getModel('history');
	$summary = JText::_('COM_EYESITE_MANUAL_ACCEPT');
	$history_model->store(LAE_HISTORY_ACCEPT,$summary,'');
	$data_model->accept_list($cids);
	$this->setRedirect('index.php?option=com_eyesite');
}

function reject_list()
{
	$cids = $this->jinput->get('cid', array(0), 'ARRAY');
	$data_model = $this->getModel('data');
	$history_model = $this->getModel('history');
	$history_model->store(LAE_HISTORY_REJECT, 
		'',
		'' );	// add list of files with details of old/new dates/sizes, etc
	$data_model->reject_list($cids);
	$this->setRedirect('index.php?option=com_eyesite');
}

function accept_all()
{
	$history_model = $this->getModel('history');
	$summary = JText::_('COM_EYESITE_MANUAL_ACCEPT');
	$history_model->store(LAE_HISTORY_ACCEPT_ALL, $summary, '' );
	$data_model = $this->getModel('data');
	$data_model->accept_all();
	$this->setRedirect('index.php?option=com_eyesite');
}

function reject_all()
{
	$history_model = $this->getModel('history');
	$history_model->store(LAE_HISTORY_REJECT_ALL, '', '' );
	$data_model = $this->getModel('data');
	$data_model->reject_all();
	$this->setRedirect('index.php?option=com_eyesite');
}

function ajax_status()
{
	$data_model = $this->getModel('data');
	$data_model->getInfo();
	$config_model = $this->getModel('config');
	$config_data = $config_model->getData();
	$history_model = $this->getModel('history');
	$running = $history_model->scanning();

	if ($data_model->totalCount == 0)
		echo '<h3>'.JText::_('COM_EYESITE_MONITOR_NOT').'</h3>';
	else
		echo '<h3>'.JText::sprintf('COM_EYESITE_MONITOR',$data_model->totalCount).'</h3>';

	if ($running)
		{
		echo '<div class="eyesite_scanning">'.JText::_('COM_EYESITE_SCANNER_SCANNING');
		if ($data_model->runningCount > 0)
			echo ': '.JText::sprintf('COM_EYESITE_FILES_TO_CHECK',$data_model->runningCount);
        if (version_compare(JVERSION,"3.2",">=") and ($data_model->runningCount > 0))        // in Joomla 3.2 or above, show the progress bar
            {
            $files_processed = $data_model->totalCount - $data_model->runningCount;
            $percent = ($files_processed / $data_model->totalCount) * 100;
            echo '<div class="progress" ><div class="bar" style="width: '.$percent.'%;"></div></div>';
            }
		echo '</div>';
		}
	else
		{
		if ($data_model->latest_date != 0)
			echo '<div>'.JText::sprintf('COM_EYESITE_LAST_SCAN',$data_model->latest_date).'</div>';
		if ($data_model->notOkCount == 0)
			echo '<div class="eyesite_no_changes">'.JText::_('COM_EYESITE_CHANGES_OUT_NO').'</div>';
		else
			{
			echo '<div class="eyesite_changes">'.JText::sprintf('COM_EYESITE_CHANGES_OUT',$data_model->notOkCount).'</div>';
			echo JText::_('COM_EYESITE_SCREEN_REFRESH');
			}
        $tmp_path = $this->app->get('tmp_path');	
        $lock_file_path = $tmp_path.'/eyesite.lock';
        if (file_exists($lock_file_path))
            {
            @clearstatcache();
            $file_age = (time() - filemtime($lock_file_path));
            $minutes_to_expiry = ceil((LAE_MAX_LOCK_SECONDS - $file_age) / 60);
            if ($minutes_to_expiry > 0)     // if it's <= 0 the scanner will delete the lock file
                echo '<div>'.JText::sprintf('COM_EYESITE_SCAN_LOCKED',$minutes_to_expiry).'</div>';
            }
		}
}

function email_test()			// Send a test email to the admin address
{
	$config_model = $this->getModel('config');
	$post_data = $config_model->getPostData();
    $email_to = $post_data->emailto;
	$app = JFactory::getApplication();
    $mailer = $app->get('mailer');
	if (empty($email_to))
		{
		$msg = JText::_('COM_EYESITE_INVALID').' '.JText::_('COM_EYESITE_EMAIL_ADRESS');	      
		$this->setRedirect('index.php?option=com_eyesite&task=configure',$msg,'error');
		return;
		}
	else
		{
        $email_text = JText::sprintf('COM_EYESITE_TEST_EMAIL_TEXT',$this->app->get('sitename'));
		$return_info = LAE_helper::send_email($post_data, $email_text, $email_text);
		if ($return_info == '')
			$this->app->enqueueMessage(JText::sprintf('COM_EYESITE_TEST_EMAIL_SENT_TO_XX', $email_to, $mailer),'message');
		else
			$this->app->enqueueMessage(JText::_('COM_EYESITE_EMAIL_SEND_FAILED').'<br />'.$return_info,'error');
		}	
	LAE_helper::addSubMenu('config');
	$view = $this->getView('config', 'html');
	$view->config_data = $post_data;
	$view->display();
}

function about()
{
	LAE_helper::addSubMenu('about');
	$config_model = $this->getModel('config');
    $config_data = $config_model->getData();
    $plugin_update_server = $config_model->plugin_update_server();
	$view = $this->getView('about', 'html');
	$view->purchase_id = $config_data->purchase_id;
    $view->plugin_update_server = $plugin_update_server;
	$view->display();
}

function save_about()
{
    $purchase_id = $this->jinput->get('purchase_id', '', 'STRING');
   	$config_model = $this->getModel('config');
    $config_data = $config_model->getData();
    if (!empty($purchase_id) and strlen($purchase_id) != 32)
        {
		$this->app->enqueueMessage(JText::_('COM_EYESITE_PURCHASE_ID_32'), 'error');
        LAE_helper::addSubMenu('about');
        $view = $this->getView('about', 'html');
        $plugin_update_server = $config_model->plugin_update_server();
        $view->purchase_id = $purchase_id;
        $view->plugin_update_server = $plugin_update_server;
        $view->display();
        }
    else
        {
        $config_data->purchase_id = $purchase_id;
        $config_model->store($config_data);
    	$this->setRedirect('index.php?option=com_eyesite&task=about');
        }
}
	
function trace_on()
{
	$config_model = $this->getModel('config');
	$config_data = $config_model->getData();
	LAE_trace::init_trace($config_data);
	$this->setRedirect('index.php?option=com_eyesite&task=about');
}

function trace_off()
{
	LAE_trace::delete_trace_file();
	$this->setRedirect('index.php?option=com_eyesite&task=about');
}

} // class