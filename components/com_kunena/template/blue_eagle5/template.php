<?php
/**
 * Kunena Component
 *
 * @package     Kunena.Template.BlueEagle5
 * @subpackage  Template
 *
 * @copyright   (C) 2008 - 2017 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Blue Eagle 5 template.
 *
 * @since  K5.0
 */
class KunenaTemplateBlue_Eagle5 extends KunenaTemplate
{
	/**
	 * List of parent template names.
	 *
	 * This template will automatically search for missing files from listed parent templates.
	 * The feature allows you to create one base template and only override changed files.
	 *
	 * @var array
	 */
	protected $default = array('crypsis');

	/**
	 * Template initialization.
	 *
	 * @return void
	 */
	public function initialize()
	{
		KunenaTemplate::getInstance()->loadLanguage();

		// Template requires Bootstrap javascript
		JHtml::_('bootstrap.framework');

		// Template also requires jQuery framework.
		JHtml::_('jquery.framework');
		JHtml::_('stylesheet', 'media/jui/css/bootstrap.min.css');
		JHtml::_('stylesheet', 'media/jui/css/bootstrap-responsive.min.css');
		JHtml::_('stylesheet', 'media/jui/css/bootstrap-extended.css');

		// Load JavaScript.
		$this->addScript('assets/js/main.js');

		$this->ktemplate = KunenaFactory::getTemplate();
		$storage = $this->ktemplate->params->get('storage');

		if ($storage)
		{
			$this->addScript('assets/js/localstorage.js');
		}

		// Compile CSS from LESS files.
		$this->compileLess('assets/less/blue_eagle5.less', 'kunena.css');
		$this->addStyleSheet('kunena.css');

		$filenameless = JPATH_SITE . '/components/com_kunena/template/blue_eagle5/assets/less/custom.less';

		if (file_exists($filenameless) && 0 != filesize($filenameless))
		{
			$this->compileLess('assets/less/custom.less', 'kunena-custom.css');
			$this->addStyleSheet('kunena-custom.css');
		}

		$filename = JPATH_SITE . '/components/com_kunena/template/blue_eagle5/assets/css/custom.css';

		if (file_exists($filename))
		{
			$this->addStyleSheet('assets/css/custom.css');
		}

		$this->ktemplate = KunenaFactory::getTemplate();
		$bootstrap = $this->ktemplate->params->get('bootstrap');
		$doc = JFactory::getDocument();

		if ($bootstrap)
		{
			$doc->addStyleSheet(JUri::base(true) . '/media/jui/css/bootstrap.min.css');
			$doc->addStyleSheet(JUri::base(true) . '/media/jui/css/bootstrap-extended.css');
			$doc->addStyleSheet(JUri::base(true) . '/media/jui/css/bootstrap-responsive.min.css');
			$doc->addStyleSheet(JUri::base(true) . '/media/jui/css/icomoon.css');
		}

		$fontawesome = $this->ktemplate->params->get('fontawesome');

		if ($fontawesome)
		{
			$doc->addStyleSheet("https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css");
		}

		// Load template colors settings
		$skinner = $this->ktemplate->params->get('enableSkinner');
		$styles = <<<EOF
		/* Kunena Custom CSS */
EOF;

		$IconColor = $this->ktemplate->params->get('IconColor');

		if ($IconColor) {
			$styles .= <<<EOF
	.layout#kunena .kcol-ktopicicon a:link,
	.layout#kunena .kcol-ktopicicon a:visited,
	.layout#kunena .kcol-ktopicicon a:active {color: {$IconColor};}
	.layout#kunena .kcol-ktopicicon a:focus {outline: none;}
	.layout#kunena .kcol-ktopicicon a:hover {color: #FF0000;}
	.layout#kunena .fa-big, .layout#kunena .icon-big {color: {$IconColor};}
EOF;
		}

		$IconColorNew = $this->ktemplate->params->get('IconColorNew');

		if ($IconColorNew) {
			$styles .= <<<EOF
			.layout#kunena .knewchar {color: {$IconColorNew};}
EOF;
		}

		$forumHeader = $this->ktemplate->params->get('forumHeadercolor', $skinner ? '' : '#5388B4');

		if ($forumHeader) {
			$styles .= <<<EOF

	.layout#kunena div.kblock > div.kheader,.layout#kunena .kblock div.kheader { background: {$forumHeader} !important; }
	.layout#kunena #ktop { border-color: {$forumHeader}; }
	.layout#kunena #ktop span.ktoggler { background: {$forumHeader}; }
	.layout#kunena #ktab li.Kunena-item-active a	{ background-color: {$forumHeader}; }
	.layout#kunena #ktab ul.menu li.active a { background-color: {$forumHeader}; }
	.layout#kunena .kcol-ktopicicon a:link,
	.layout#kunena .kcol-ktopicicon a:visited,
	.layout#kunena .kcol-ktopicicon a:active {color: {$forumHeader};}
	.layout#kunena .kcol-ktopicicon a:focus {outline: none;}
	.layout#kunena .kcol-ktopicicon a:hover {color: #FF0000;}
EOF;
		}

		$topicicontype = $this->ktemplate->params->get('topicicontype');

		if ($topicicontype == 'image') {
			$styles .= <<<EOF
		[class^='icon-'], [class*=' icon-'] {
			background-image: none !important;
		}
EOF;
		}

		$forumLink = $this->ktemplate->params->get('forumLinkcolor', $skinner ? '' : '#5388B4');

		if ($forumLink) {
			$styles .= <<<EOF
	.layout#kunena a:link,
	.layout#kunena a:visited,
	.layout#kunena a:active {color: {$forumLink};}
	.layout#kunena a:focus {outline: none;}
EOF;
		}

		$announcementHeader = $this->ktemplate->params->get('announcementHeadercolor', $skinner ? '' : '#5388B4');

		if ($announcementHeader) {
			$styles .= <<<EOF
	.layout#kunena div.kannouncement div.kheader { background: {$announcementHeader} !important; }
EOF;
		}

		$announcementBox = $this->ktemplate->params->get('announcementBoxbgcolor', $skinner ? '' : '#FFFFFF');

		if ($announcementBox) {
			$styles .= <<<EOF
	.layout#kunena div#kannouncement .kanndesc { background: {$announcementBox}; }
EOF;
		}

		$frontStatsHeader = $this->ktemplate->params->get('frontstatsHeadercolor', $skinner ? '' : '#5388B4');

		if ($frontStatsHeader) {
			$styles .= <<<EOF
	.layout#kunena div.kfrontstats div.kheader { background: {$frontStatsHeader} !important; }
EOF;
		}

		$onlineHeader = $this->ktemplate->params->get('whoisonlineHeadercolor', $skinner ? '' : '#5388B4');

		if ($onlineHeader) {
			$styles .= <<<EOF
	.layout#kunena div.kwhoisonline div.kheader { background: {$onlineHeader} !important; }
EOF;
		}

		$inactiveTab = $this->ktemplate->params->get('inactiveTabcolor', $skinner ? '' : '#737373');

		if ($inactiveTab) {
			$styles .= <<<EOF
	.layout#kunena #ktab a { background-color: {$inactiveTab} !important; }
EOF;
		}

		$activeTab = $this->ktemplate->params->get('activeTabcolor', $skinner ? '' : '#5388B4');

		if ($activeTab) {
			$styles .= <<<EOF
	.layout#kunena #ktab ul.menu li.active a,.layout#kunena #ktab li#current.selected a { background-color: {$activeTab} !important; }
EOF;
		}

		$hoverTab = $this->ktemplate->params->get('hoverTabcolor', $skinner ? '' : '#5388B4');

		if ($hoverTab) {
			$styles .= <<<EOF
	.layout#kunena #ktab a:hover { background-color: {$hoverTab} !important; }
EOF;
		}

		$topBorder = $this->ktemplate->params->get('topBordercolor', $skinner ? '' : '#5388B4');

		if ($topBorder) {
			$styles .= <<<EOF
	.layout#kunena #ktop { border-color: {$topBorder} !important; }
EOF;
		}

		$inactiveFont = $this->ktemplate->params->get('inactiveFontcolor', $skinner ? '' : '#FFFFFF');

		if ($inactiveFont) {
			$styles .= <<<EOF
	.layout#kunena #ktab a span { color: {$inactiveFont} !important; }
EOF;
		}
		$activeFont = $this->ktemplate->params->get('activeFontcolor', $skinner ? '' : '#FFFFFF');

		if ($activeFont) {
			$styles .= <<<EOF
	.layout#kunena #ktab #current a span { color: {$activeFont} !important; }
EOF;
		}

		$toggleButton = $this->ktemplate->params->get('toggleButtoncolor', $skinner ? '' : '#5388B4');

		if ($toggleButton) {
			$styles .= <<<EOF
	.layout#kunena #ktop span.ktoggler { background-color: {$toggleButton} !important; }
EOF;
		}

		$document = JFactory::getDocument();
		$document->addStyleDeclaration($styles);

		parent::initialize();
	}

	/**
	 * @param        $filename
	 * @param   string $group
	 *
	 * @return JDocument
	 */
	public function addStyleSheet($filename, $group = 'forum')
	{
		$filename = $this->getFile($filename, false, '', "media/kunena/cache/blue_eagle5/css");

		return JFactory::getDocument()->addStyleSheet(JUri::root(true) . "/{$filename}");
	}
}
