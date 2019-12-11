<?php
/********************************************************************
Product    : Eyesite
Date       : 9 June 2017
Copyright  : Les Arbres Design 2009-2017
Contact	   : http://www.lesarbresdesign.info
Licence    : GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted access');

class EyesiteViewMain extends JViewLegacy
{
function display($tpl = null)
{
	LAE_helper::viewStart();
	JToolBarHelper::title('Eyesite: '.JText::_('COM_EYESITE_STATUS'), 'eyesite.png');
	JToolBarHelper::custom('accept_list', 'checkmark.png', 'apply_f2.png', JText::_('COM_EYESITE_TOOLBAR_ACCEPT'), true);
	JToolBarHelper::custom('reject_list', 'delete.png', 'cancel_f2.png', JText::_('COM_EYESITE_TOOLBAR_REJECT'), true);
	JToolBarHelper::custom('accept_all', 'ok.png', 'apply_f2.png', JText::_('COM_EYESITE_TOOLBAR_ACCEPT_ALL'), false);
	JToolBarHelper::custom('reject_all', 'cancel.png', 'cancel_f2.png', JText::_('COM_EYESITE_TOOLBAR_REJECT_ALL'), false);
	JToolBarHelper::custom('scan_now', 'search.png', 'search.png', JText::_('COM_EYESITE_TOOLBAR_SCAN_NOW'), false);

?>		
	<form action="index.php" method="get" name="adminForm" id="adminForm">
	<input type="hidden" name="option" value="com_eyesite" />
	<input type="hidden" name="task" value="main" />
	<input type="hidden" name="boxchecked" value="0" />
<?php

// create the state filter list

	$app = JFactory::getApplication();
	$filter_state = $app->getUserStateFromRequest('com_eyesite.filter_state','filter_state',-1);

	$state_array[] = JHTML::_('select.option', -1, JText::_('COM_EYESITE_STATE_FILTER_ALL'));
	$state_array[] = JHTML::_('select.option', LAE_STATE_NEW, JText::_('COM_EYESITE_STATE_FILTER_NEW'));
	$state_array[] = JHTML::_('select.option', LAE_STATE_CHANGED, JText::_('COM_EYESITE_STATE_FILTER_CHANGED'));
	$state_array[] = JHTML::_('select.option', LAE_STATE_DELETED, JText::_('COM_EYESITE_STATE_FILTER_DELETED'));
	$state_list    = JHTML::_('select.genericlist', $state_array, 'filter_state', 'size="1" onchange="submitform( );" style="height:28px; margin-bottom:0;"', 'value', 'text', intval($filter_state));

	echo '<div style=float:right;>'.JText::_('COM_EYESITE_STATE_FILTER_LABEL').' '.$state_list.'</div>';

// make the div for the Ajax data and load the status update Javascript

	echo '<div id="ajax_response" class="eyesite_ajax_data"></div>';
	$document = JFactory::getDocument();
	$document->addScript(JURI::root().'administrator/components/com_eyesite/assets/eyesite.js?502');
	echo '<hr>';
    
// replace the Javascript that Joomla sets on the Scan Now button with our Ajax call to start the scan with Ajax

	$secret = $app->get('secret');
	$url = JURI::root().'index.php?option=com_eyesite&task=scan&entry='.$secret.'&source=admin&format=raw&tmpl=component&lang=en';
    $ajax = "jQuery.ajax({url: '$url', type: 'GET', timeout: 1000 })";     
    $ajax_function = "function() {".$ajax."}";
    $button_js = "jQuery('#toolbar-search').find('button').prop('onclick', null).click(".$ajax_function.")";
    $doc_ready = "jQuery(document).ready(function() {".$button_js.";eyesite_check_for_update();});\n";
    $document->addScriptDeclaration($doc_ready);

// get the list of outstanding changes

	$data_model = $this->getModel('data');
	$rows = $data_model->getList();
	$numrows = count($rows);
	if ($numrows == 0)
		{
		echo '</form>';
		LAE_helper::viewEnd();
		return;
		}
	$pagination = $data_model->getPagination();

	echo '<table class="table table-striped">';
	echo '<thead><tr>';
	echo '<th style="width:20px; text-align:center;">#</th>';
	echo '<th style="width:20px; text-align:center;"><input type="checkbox" name="toggle" value="" onClick="Joomla.checkAll(this);" /></th>';
	echo '<th style="width:5%; white-space:nowrap">'.JText::_('COM_EYESITE_CHANGES_STATE').'</th>';
	echo '<th style="width:5%; white-space:nowrap">'.JText::_('COM_EYESITE_CHANGES_ACCEPT').'</th>';
	echo '<th style="width:5%; white-space:nowrap">'.JText::_('COM_EYESITE_CHANGES_REJECT').'</th>';
	echo '<th style="width:5%;"></th>';
    echo '<th style="white-space:nowrap">'.JText::_('COM_EYESITE_CHANGES_FILE').'</th>';
	echo '</tr></thead>';
    
    echo '<tfoot><tr><td colspan="10">'.$pagination->getListFooter().'</td></tr></tfoot>';

	echo '<tbody>';
    
    $root_len = strlen(JPATH_ROOT);

	for ($i=0; $i < $numrows; $i++) 
		{
		$row = $rows[$i];
		$state = LAE_helper::state_text($row->state);
		switch ($row->state)
			{
			case LAE_STATE_NEW:
				$text = JText::_('COM_EYESITE_SCANNER_NEW_DATE').': '.$row->datetime.', '.JText::_('COM_EYESITE_SCANNER_SIZE').': '.self::format_size($row->filesize);
				$info = LAE_helper::make_info($text);
				break;
			case LAE_STATE_CHANGED:
				$text = JText::_('COM_EYESITE_SCANNER_ORIG_DATE').': '.$row->datetime.', '.JText::_('COM_EYESITE_SCANNER_SIZE').': '.self::format_size($row->filesize).'<br />'.
					JText::_('COM_EYESITE_SCANNER_NEW_DATE').': '.$row->new_datetime.', '.JText::_('COM_EYESITE_SCANNER_SIZE').': '.self::format_size($row->new_filesize);
				$info = LAE_helper::make_info($text);
				break;
			case LAE_STATE_DELETED:
				$text = JText::_('COM_EYESITE_SCANNER_ORIG_DATE').': '.$row->datetime.', '.JText::_('COM_EYESITE_SCANNER_SIZE').': '.self::format_size($row->filesize);
				$info = LAE_helper::make_info($text);
				break;
			default:
				$info = '';
			}
			
		$link 	= 'index.php?option=com_eyesite&id='.$row->id;
		$tick_img = '<img src="components/com_eyesite/assets/accept.png" alt="Accept" />';
		$cross_img = '<img src="components/com_eyesite/assets/reject.png" alt="Reject" />';
		$checked = JHTML::_('grid.id', $i, $row->id );
        $filepath = $row->filepath;
        if (substr($filepath,0,$root_len) == JPATH_ROOT)
            $filepath = substr($filepath, $root_len);
		echo "<tr>
            <td style='text-align:center;'>".$pagination->getRowOffset($i)."</td>
            <td style='text-align:center;'>$checked</td>
            <td>$state</td>
            <td style='text-align:center;'><a href=$link&task=accept_one title='".JText::_('COM_EYESITE_CHANGES_ACCEPT')."'>$tick_img</a></td>
            <td style='text-align:center;'><a href=$link&task=reject_one title='".JText::_('COM_EYESITE_CHANGES_REJECT')."'>$cross_img</a></td>
            <td>$info</td>
            <td>$filepath</td>
			</tr>\n";
	}
	echo '</tbody></table></form>';
	LAE_helper::viewEnd();
}

//-------------------------------------------------------------------------------
// Format a file size
//
static function format_size($bytes)
{
	if ($bytes >= 1073741824)
		return number_format($bytes / 1073741824, 2).' GB';
	if ($bytes >= 1048576)
		return number_format($bytes / 1048576, 2).' MB';
	if ($bytes >= 1024)
		return number_format($bytes / 1024, 2).' KB';
	return $bytes;
}


} // class