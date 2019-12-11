<?php
//
//  load the css file for a given faq.  faq class selectors, i.e. '.faqclassname'
//	are changed to id specific selectors, i.e. '#faqid.faqclassname' to overcome
//	templates that insist upon imposing their will with id specific selectors.
//	for example, a faqid of accordion1 using the lightnessfaq css class, all rules
//	containing '.lightnessfaq' are changed to '#accordion1.lightnessfaq' when the
//	css file is loaded by the browser.
//
$dir = dirname(__FILE__ );
$id = trim($_GET["id"]);
$id = htmlentities( $id, ENT_QUOTES, 'UTF-8' );
$id = preg_replace( "#[\.\\\\/]#", "", $id );
$faq = trim($_GET["faq"]);
$faq = htmlentities( $faq, ENT_QUOTES, 'UTF-8');
$faq = preg_replace( "#[\.\\\\/]#", "", $faq );
$file = $dir.DIRECTORY_SEPARATOR.$faq.".css";
if (file_exists($file))
{
	$css = file_get_contents( $file );
	$css = preg_replace( "/\.".$faq."/", "#".$id.".".$faq, $css );
	header('content-type: text/css');
	echo $css;
}
?>