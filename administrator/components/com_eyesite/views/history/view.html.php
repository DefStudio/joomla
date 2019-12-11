<?php
/********************************************************************
Product    : Eyesite
Date       : 15 November 2016
Copyright  : Les Arbres Design 2009-2016
Contact	   : http://www.lesarbresdesign.info
Licence    : GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted access');

class EyesiteViewHistory extends JViewLegacy
{

//-------------------------------------------------------------------------------
// Show the history list
//
function display($tpl = null)
{
	LAE_helper::viewStart();
	JToolBarHelper::title('Eyesite: '.JText::_('COM_EYESITE_HISTORY'), 'eyesite.png');
	JToolBarHelper::deleteList('', 'history_delete');
	JToolBarHelper::cancel('display','JTOOLBAR_CLOSE');

// get the current filters	
		
	$app = JFactory::getApplication();
	$search = $app->getUserStateFromRequest('com_eyesite.history_search','search','','RAW');
    
?>		
	<form action="index.php" method="get" name="adminForm" id="adminForm">
	<input type="hidden" name="option" value="com_eyesite" />
	<input type="hidden" name="task" value="history_list" />
	<input type="hidden" name="boxchecked" value="0" />
<?php

    JHTML::_('bootstrap.tooltip');
    echo "\n".'<div>&nbsp;<div style="float:left">'; 
    $icon = '<img src="'.LAE_ADMIN_ASSETS_URL.'search_16.gif" alt="" style="vertical-align:text-bottom;" />';
    echo '<span class="hasTooltip" title="'.JText::_('COM_EYESITE_SEARCH_DESC').'">'.$icon.'</span>';
    echo ' <input type="text" class="text_area" size="40" name="search" id="search" value="'.$search.'" />';
    echo ' <button class="btn button" onclick="this.form.submit();">'.JText::_('COM_EYESITE_GO').'</button>';
	echo '</div>'; 
	echo "\n".'<div style="float:right">'; 
    echo '<button class="btn button" onclick="'."
            document.getElementById('search').value='';
            this.form.submit();".'">'.JText::_('JSEARCH_RESET').'</button>';
	echo '</div></div>';

// get the history list

	$history_model = $this->getModel('history');
	$rows = $history_model->getList();
	$numrows = count($rows);
	if ($numrows == 0)
		{
		echo '</form>'.JText::_('COM_EYESITE_NO_ITEMS');
		LAE_helper::viewEnd();
		return;
		}
	$pagination = $history_model->getPagination();

?>
	<table class="table table-striped">
	<thead>
	<tr>
		<th style="width:20px; text-align:center;">#</th>
		<th style="width:20px; text-align:center;"><input type="checkbox" name="toggle" value="" onClick="Joomla.checkAll(this);" /></th>
		<th style="width:10%; white-space:nowrap"><?php echo JText::_('COM_EYESITE_DATE_TIME'); ?></th>
		<th colspan="2" style="width:10%; white-space:nowrap; text-align:center;"><?php echo JText::_('COM_EYESITE_EVENT'); ?></th>
		<th style="white-space:nowrap"><?php echo JText::_('COM_EYESITE_SUMMARY'); ?></th>
	</tr>
	</thead>
	
	<tfoot>
	<tr>
		<td colspan="15">
			<?php echo $pagination->getListFooter(); ?>
		</td>
	</tr>
	</tfoot>
	
	<tbody>
	
<?php
	for ($i=0; $i < $numrows; $i++) 
		{
		$row = $rows[$i];
		$state = LAE_helper::state_text($row->state);
        $event = LAE_helper::history_text($row->state);

		switch ($row->state)
			{
			case LAE_HISTORY_SCAN_STARTED:
				$icon = 'blue_spot_16.png';
				break;
			case LAE_HISTORY_SCAN_NO_CHANGES:
				$icon = 'tick_spot_16.png';
				break;
			case LAE_HISTORY_SCAN_CHANGES:
				$icon = 'yellow_spot_16.png';
				break;
			case LAE_HISTORY_SCAN_ERROR:
				$icon = 'red_spot_16.png';
				break;
			case LAE_HISTORY_ACCEPT:
				$icon = 'green_bullet_16.png';
				break;
			case LAE_HISTORY_REJECT:
				$icon = 'red_bullet_16.png';
				break;
			case LAE_HISTORY_ACCEPT_ALL:
				$icon = 'green_bullet_16.png';
				break;
			case LAE_HISTORY_REJECT_ALL:
				$icon = 'red_bullet_16.png';
				break;
			case LAE_HISTORY_SCAN_NOT_STARTED:
				$icon = 'red_spot_16.png';
				break;
			case LAE_HISTORY_EMAIL_FAILED:
				$icon = 'red_spot_16.png';
				break;
			default:
			}
		$icon_img = '<img src="'.LAE_ADMIN_ASSETS_URL.$icon.'" alt="" />';
		$link 	= 'index.php?option=com_eyesite&id='.$row->id;
		if ($row->details != '')
			$event = "<a href=$link&task=history_item>$event</a>";
		$checked = JHTML::_('grid.id', $i, $row->id );
		echo "<tr>
				<td style='text-align:center;'>".$pagination->getRowOffset($i)."</td>
				<td style='text-align:center;'>$checked</td>
				<td style='white-space:nowrap;'>$row->datetime</td>
				<td style='text-align:center;'>$icon_img</td>
				<td style='white-space:nowrap;'>$event</td>
				<td>$row->summary</td>
			</tr>\n";
		}
	echo '<tbody></table></form>';
	LAE_helper::viewEnd();
}

//-------------------------------------------------------------------------------
// Edit a single history record
//
function edit($tpl = null)
{
	LAE_helper::viewStart();
	JToolBarHelper::title('Eyesite: '.JText::_('COM_EYESITE_HISTORY'), 'eyesite.png');
	JToolBarHelper::save('history_save');
	JToolBarHelper::cancel('history_list','JTOOLBAR_CLOSE');

?>		
	<form action="index.php" method="get" name="adminForm" id="adminForm">
	<input type="hidden" name="option" value="com_eyesite" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $this->history_data->id; ?>" />
<?php

    $event = LAE_helper::history_text($this->history_data->state);

	echo '<table class="adminlist table table-striped">';
	echo '<tr>';
	echo '<td>'.JText::_('COM_EYESITE_DATE_TIME').'</td>';
	echo '<td>'.$this->history_data->datetime.'</td>';
	echo '</tr>';

	echo '<tr>';
	echo '<td>'.JText::_('COM_EYESITE_EVENT').'</td>';
	echo '<td>'.$event.'</td>';
	echo '</tr>';

	echo '<tr>';
	echo '<td>'.JText::_('COM_EYESITE_SUMMARY').'</td>';
	echo '<td><input name="summary" type="text" size="100" value="'.$this->history_data->summary.'" /></td>';
	echo '</tr>';

	echo '<tr>';
	echo '<td style="vertical-align:top;">'.JText::_('COM_EYESITE_DETAILS').'</td>';
	echo '<td>'.$this->history_data->details.'</td>';
	echo '</tr>';
	echo '</table>';
	
	echo '</form>';
	LAE_helper::viewEnd();
}


} // class