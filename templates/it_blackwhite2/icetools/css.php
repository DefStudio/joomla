<?php
	//  @copyright	Copyright (C) 2013 IceTheme. All Rights Reserved
	//  @license	Copyrighted Commercial Software 
	//  @author     IceTheme (icetheme.com)
	
	// No direct access.
	defined('_JEXEC') or die;
	
?>

<link rel="stylesheet" href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" >

<link rel="stylesheet" type="text/css" href="<?php echo IT_THEME; ?>/less/style.css" />
<link rel="stylesheet" type="text/css" href="<?php echo IT_THEME; ?>/less/style2.css" />

<?php if($it_responsive == 1) {  ?>
	<link rel="stylesheet" type="text/css" href="<?php echo IT_THEME; ?>/less/responsive.css" />
<?php } ?>

<link rel="stylesheet" type="text/css" href="<?php echo IT_THEME; ?>/css/custom.css" />

<style type="text/css" media="screen">
	
	<?php if($this->params->get('homepage_content')) {
		/* Hide Content From HOMEPAGE */
		$menu = $app->getMenu();
		$lang = JFactory::getLanguage();
		if ($menu->getActive() == $menu->getDefault($lang->getTag())) {  ?>
		.middlecol, .sidebar { display:none}
	<?php }} ?>
	
	<?php if($it_sidebar_pos == 'left') {  ?>
		/* Sidebar is "left" */
		#middlecol { float:right !important;}
	<?php } ?>
	
	<?php if ($it_promo != 0) { ?> 
		ul.breadcrumb {
		border-bottom: 1px dashed #ccc;
		padding-bottom: 15px;
		margin-bottom: -12px;}
	<?php } ?>
	
	
	/* Custom CSS code through template paramters */
	<?php echo $it_cuosom_css; ?>
	
</style>


<!-- Google Fonts -->
<link href='https://fonts.googleapis.com/css?family=Quicksand|Coming+Soon' rel='stylesheet' type='text/css' />
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,400i,600,700&amp;subset=cyrillic" rel="stylesheet"> 

<!--[if lte IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo IT_THEME; ?>/css/ie8.css" />
	<script src="<?php echo IT_THEME; ?>/js/respond.min.js"></script>
<![endif]-->

<!--[if lt IE 9]>
    <script src="<?php echo $this->baseurl ?>/media/jui/js/html5.js"></script>
	<script src="https://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
<![endif]-->
<!--[if IE]>
    <script type="text/javascript" src="<?php echo IT_THEME; ?>/js/html5shiv.js"></script>
<![endif]-->



<!--[if !IE]><!-->
<?/*script type="text/javascript" src="<?php echo IT_THEME; ?>/js/ssmov.js"></script*/?>
<script>  
	if(Function('/*@cc_on return document.documentMode===10@*/')()){
		document.documentElement.className+=' ie10';
	}
</script>
<!--<![endif]-->  

<style type="text/css">
	
	/* IE10 hacks. add .ie10 before */
	.ie10 #gotop .scrollup {
	right:40px;}
	
</style>



