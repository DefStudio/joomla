<?php
/********************************************************************
Product    : Eyesite
Date       : 15 November 2016
Copyright  : Les Arbres Design 2009-2016
Contact	   : http://www.lesarbresdesign.info
Licence    : GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted access');

class EyesiteViewConfig extends JViewLegacy
{

//-------------------------------------------------------------------------------
// Show the configuration form
//
function display($tpl = null)
{
	LAE_helper::viewStart();
	JToolBarHelper::title('Eyesite: '.JText::_('COM_EYESITE_CONFIGURATION'), 'eyesite.png');
	
	if (JFactory::getUser()->authorise('core.admin', 'com_eyesite'))	// Check for ACL access
		JToolBarHelper::preferences('com_eyesite');
	else
		{
		echo JText::_('JERROR_ALERTNOAUTHOR');
		LAE_helper::viewEnd();
		return;
		}
	
	JToolBarHelper::custom('email_test', 'mail.png', '', 'COM_EYESITE_TEST_EMAIL', false);
	JToolBarHelper::apply('config_apply');
	JToolBarHelper::save('config_save');
	JToolBarHelper::cancel('display','JTOOLBAR_CLOSE');

	$exclude_dir_example = JPATH_SITE.'/tmp,'.JPATH_SITE.'/log,'.JPATH_SITE.'/cache"';
	$exclude_dir_example = str_replace('/','/&#8203;',$exclude_dir_example);	// &#8203; is a zero-width space
	
	$exclude_file_example = JPATH_SITE.'/tmp,'.JPATH_SITE.'/log,'.JPATH_SITE.'/cache"';
	$exclude_file_example = str_replace('/','/&#8203;',$exclude_file_example);
	
	echo '<h3>'.JText::_('COM_EYESITE_SITE_PATH').' '.JPATH_SITE. '</h3>';

    echo '<form action="" method="post" id="adminForm">';
	echo '<input type="hidden" name="option" value="com_eyesite" />';
	echo '<input type="hidden" name="task" value="" />';
	echo '<input type="hidden" name="purchase_id" value="'.$this->config_data->purchase_id.'" />';

    echo $this->config_row(JText::_('COM_EYESITE_EMAIL_ADRESS'), JText::_('COM_EYESITE_EMAIL_ADRESS_DESC'), 'emailto', 1, 30);
    echo $this->config_row(JText::_('COM_EYESITE_FILE_EXT'), JText::_('COM_EYESITE_FILE_EXT_DESC'), 'extensions', 1, 80);
    echo $this->config_row(JText::_('COM_EYESITE_DIR_INC'), JText::_('COM_EYESITE_DIR_INC_DESC').' "'.JPATH_SITE.',S"', 'incdirs', 6, 80);
    echo $this->config_row(JText::_('COM_EYESITE_DIR_EXC'), JText::_('COM_EYESITE_DIR_EXC_DESC').' "'.$exclude_dir_example, 'excdirs', 5, 80);
    echo $this->config_row(JText::_('COM_EYESITE_FILE_EXC'), JText::_('COM_EYESITE_FILE_EXC_DESC').'"'.$exclude_file_example, 'excfiles', 5, 80);
    echo $this->config_row(JText::_('COM_EYESITE_DAYS_TO_KEEP'), JText::_('COM_EYESITE_DAYS_TO_KEEP_DESC'), 'days_to_keep', 1, 10);

    echo '<div class="config_row"><label for="auto_accept"><strong>'.JText::_('COM_EYESITE_AUTO_ACCEPT').'</strong>';
    echo '<br />'.JText::_('COM_EYESITE_AUTO_ACCEPT_DESC').'</label>';
    if ($this->config_data->auto_accept)
        echo '<input type="checkbox" name="auto_accept" id="auto_accept" value="1" checked="checked" />';
    else
        echo '<input type="checkbox" name="auto_accept" id="auto_accept" value="1" />';
    echo '</div>';
        
    echo '</form>';

	LAE_helper::viewEnd();
}

function config_row($prompt, $description, $field_name, $rows, $cols)
{
    $html = '<div class="config_row"><label for="'.$field_name.'"><strong>'.$prompt.'</strong>';
    $html .= '<br />'.$description.'</label>';
    $html .= '<textarea name="'.$field_name.'" id="'.$field_name.'" rows="'.$rows.'" cols="'.$cols.'">'.$this->config_data->$field_name.'</textarea>';
    $html .= '</div>';
    return $html;
}
			
} // class