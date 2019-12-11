<?php
/********************************************************************
Product    : Eyesite
Date       : 8 June 2017
Copyright  : Les Arbres Design 2009-2017
Contact	   : http://www.lesarbresdesign.info
Licence    : GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted access');

class EyesiteViewAbout extends JViewLegacy
{
function display($tpl = null)
{
	LAE_helper::viewStart();
	JToolBarHelper::title('Eyesite: '.JText::_('COM_EYESITE_ABOUT'), 'eyesite.png');
	JToolBarHelper::apply('save_about');
	JToolBarHelper::cancel();

?>
	<form action="index.php" method="get" name="adminForm" id="adminForm" class="form-horizontal form-inline">
	<input type="hidden" name="option" value="com_eyesite" />
	<input type="hidden" name="task" value="" />
<?php
	
// get the component version

	$xml_array = JInstaller::parseXMLInstallFile(JPATH_ADMINISTRATOR.'/components/com_eyesite/eyesite.xml');
	$component_version = $xml_array['version'];

// get the latest version info

    $this->latest_version = '';
    $this->transaction_status = '';
    if (!isset($this->purchase_id))
        $this->purchase_id = '';
	$this->get_version('eyesite', $this->purchase_id);
    
// build the help screen

	$about['name'] = 'Eyesite';
	$about['prefix'] = 'COM_EYESITE';
	$about['current_version'] = $component_version;
    $about['latest_version'] = $this->latest_version;
	$about['reference'] = 'eyesite';
	$about['link_version'] = "http://www.lesarbresdesign.info/version-history/eyesite";
	$about['link_doc'] = "http://www.lesarbresdesign.info/extensions/eyesite";
	$about['link_rating'] = "http://extensions.joomla.org/extensions/access-a-security/site-security/site-monitoring/26479";
	$this->draw_about($about);
    
	echo '<p></p>';
    
// If the Eyesite Plugin update server is installed, show the Purchase ID field

	if ($this->plugin_update_server)
        {
        echo '<div class="form-group">';
        echo '<label for="purchase_id">'.JText::_('COM_EYESITE_PURCHASE_ID').'</label>';
        echo '<input type="text" size="35" name="purchase_id" class="form-group" value = "'.$this->purchase_id.'" /> ';
        echo LAE_helper::make_info(JText::_('COM_EYESITE_PURCHASE_ID_DESC'));
        echo '</div>';
        if (!empty($this->transaction_status))
            echo $this->transaction_status;
        }
        
	echo '</form>';    
    
	echo LAE_trace::make_trace_controls();
	echo '<p></p>';
	LAE_helper::viewEnd();
}

//------------------------------------------------------------------------------
// draw the about screen - this is the same in all our components
//
function draw_about($about)
{
	echo '<h3>'.$about['name'].': '.JText::_($about['prefix'].'_HELP_TITLE').'</h3>';

	if ($about['link_rating'] != '')
		{
		echo '<p><span style="font-size:120%;font-weight:bold;">'.JText::_($about['prefix'].'_HELP_RATING').' ';
		echo JHTML::link($about['link_rating'], 'Joomla Extensions Directory', 'target="_blank"').'</span></p>';
		}

	echo '<table class="table table-striped table-bordered width-auto">';
	
	echo '<tr><td>'.JText::_($about['prefix'].'_VERSION').'</td>';
	echo '<td>'.$about['current_version'].'</td></tr>';
	
	if ($about['latest_version'] != '')
		echo '<tr><td>'.JText::_($about['prefix'].'_LATEST_VERSION').'</td><td>'.$about['latest_version'].'</td></tr>';

	echo '<tr><td>'.JText::_($about['prefix'].'_HELP_CHECK').'</td>';
	echo '<td>'.JHTML::link($about['link_version'], 'Les Arbres Design - '.$about['name'], 'target="_blank"').'</td></tr>';

	$pdf_icon = JHTML::_('image', JURI::root().'administrator/components/com_'.$about['reference'].'/assets/pdf_16.gif','');
	echo '<tr><td>'.$pdf_icon.' '.JText::_($about['prefix'].'_HELP_DOC').'</td>';
	echo '<td>'.JHTML::link($about['link_doc'], "www.lesarbresdesign.info", 'target="_blank"').'</td></tr>';

	$link_jed = "http://extensions.joomla.org/extensions/owner/chrisguk";
	$link_ext = "http://www.lesarbresdesign.info/";

	echo '<tr><td>'.JText::_($about['prefix'].'_HELP_LES_ARBRES').'</td>';
	echo '<td>'.JHTML::link("http://www.lesarbresdesign.info/", 'Les Arbres Design', 'target="_blank"').'</td></tr>';
		
	if (!empty($about['extra']))
		foreach($about['extra'] as $row)
			echo '<tr><td>'.$row['left'].'</td><td>'.$row['right'].'</td></tr>';

	echo '</table>';
}
	
//------------------------------------------------------------------------------
// get the latest version info
//
function get_version($product, $purchase_id)
{
    $url = 'http://www.lesarbresdesign.info/jupdate?product='.$product.'&src=about';
    if (strlen($purchase_id) == 32)
        $url .= '&tid='.$purchase_id;
    try
        {
        $http = JHttpFactory::getHttp();
        $response = $http->get($url, array(), 20);
        }
    catch (RuntimeException $e)
        {
        return;
        }
    $this->latest_version = self::str_between($response->body,'<version>','</version>');
    $this->transaction_status = self::str_between($response->body,'<lad_transaction_status><![CDATA[',']]></lad_transaction_status>');
}
				
function str_between($string, $start, $end)
{
    $string = ' '.$string;
    $pos = strpos($string, $start);
    if ($pos == 0)
        return '';
    $pos += strlen($start);
    $len = strpos($string, $end, $pos) - $pos;
    return substr($string, $pos, $len);
}

}