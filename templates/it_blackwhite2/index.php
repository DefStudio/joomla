<?php
	//  @copyright	Copyright (C) 2013 IceTheme. All Rights Reserved
	//  @license	Copyrighted Commercial Software
	//  @author     IceTheme (icetheme.com)

	defined('_JEXEC') or die;

	// Include Variables
	include_once(JPATH_ROOT . "/templates/" . $this->template . '/icetools/vars.php');

	if ((JRequest::getCmd("tmpl", "index") != "offline") && (JRequest::getCmd("tmpl", "index") != "soon") && ($it_comingsoon == 0)) { ?>
	<?php
		/*
		<!DOCTYPE html>
		<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
		*/
	?>
	<!DOCTYPE html>
	<html lang="ru">
		<head>
			<?php if ($it_responsive == 1) { ?>
				<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<?php } ?>

<link rel="stylesheet" type="text/css" href="/templates/it_blackwhite2/css/style_table_sorter.css" />


			<?php
				// Include CSS and JS variables
				include_once(IT_THEME_DIR.'/icetools/css.php');
			?>

			<jdoc:include type="head" />

		</head>

		<body class="<?php echo $pageclass->get('pageclass_sfx'); ?>">
			<div class="logintop">
				<div class="logintopl">
				</div>
				<div class="logintopr">
				</div>
				<div class="container">
					<?php if ($this->countModules( 'login' )){?>

						<jdoc:include type="modules" name="login" />
						<?php }else{?>
						&nbsp;
					<?php }?>
				</div>
			</div>
			<div class="logotop">
				<div class="container">
					<div class="tomainlink topmainlink">
						<a href="/"></a>
					</div>
					<div class="logopic">
						<div class="banner-block">
							<jdoc:include type="modules" name="banner" />
						</div>
					</div>
				</div>
			</div>

			<header id="header">

				<div class="container">
					<?php if ($it_language != 0) { ?>
						<div id="language">
							<jdoc:include type="modules" name="language" />
						</div>
					<?php } ?>
					<?php if ($it_topmenu != 0) { ?>
						<div id="topmenu">
							<jdoc:include type="modules" name="topmenu" />
						</div>
					<?php } ?>


					<?php if ($it_search != 0) { ?>
						<div id="search">
							<jdoc:include type="modules" name="search" />
						</div>
					<?php } ?>

					<?php if ($it_mainmenu != 0) { ?>
						<div id="mainmenu">
							<jdoc:include type="modules" name="mainmenu" />
							<div class="mainmenuleft"></div>
							<div class="mainmenuright"></div>
						</div>
					<?php } ?>

					<jdoc:include type="modules" name="breadcrumbs" />

					<?php if ($it_iceslideshow != 0) { ?>
						<div id="iceslideshow" >
							<jdoc:include type="modules" name="iceslideshow" />
						</div>
					<?php } ?>

					<?php if ($it_promo != 0) { ?>
						<div id="promo" class="row" >
							<jdoc:include type="modules" name="promo" style="promo" />
						</div>
					<?php } ?>

				</div>
<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
    (function (d, w, c) {
        (w[c] = w[c] || []).push(function() {
            try {
                w.yaCounter45472458 = new Ya.Metrika({
                    id:45472458,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true,
                    webvisor:true
                });
            } catch(e) { }
        });

        var n = d.getElementsByTagName("script")[0],
            s = d.createElement("script"),
            f = function () { n.parentNode.insertBefore(s, n); };
        s.type = "text/javascript";
        s.async = true;
        s.src = "https://mc.yandex.ru/metrika/watch.js";

        if (w.opera == "[object Opera]") {
            d.addEventListener("DOMContentLoaded", f, false);
        } else { f(); }
    })(document, window, "yandex_metrika_callbacks");
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/45472458" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->

				<script>
					(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
					(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
					m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
					})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

					ga('create', 'UA-103746430-1', 'auto');
					ga('send', 'pageview');

				</script>
			</header>


			<!-- content -->
			<section id="content">
				<div class="innercontent">
					<div class="container">


						<div class="row-fluid">
							<!-- Middle Col -->
							<div id="middlecol" class="<?php echo $content_span;?>">

								<div class="inside">

									<jdoc:include type="message" />

									<jdoc:include type="component" />

								</div>

							</div><!-- / Middle Col  -->

							<?php if ($it_sidebar != 0) { ?>
								<!-- sidebar -->
								<div id="sidebar" class="<?php echo $sidebar_span;?> <?php if($it_sidebar_pos == 'right') {  echo 'sidebar_right'; } ?>" >
									<div class="inside">
										<jdoc:include type="modules" name="sidebar" style="sidebar" />
									</div>
								</div><!-- /sidebar -->
							<?php } ?>

						</div>

						<?php if ($it_icecarousel != 0) { ?>
							<div id="icecarousel">
								<div class="container">
									<jdoc:include type="modules" name="icecarousel" style="slider" />
								</div>
							</div>
						<?php } ?>

					</div>
				</div>
			</section><!-- /content  -->


			<footer id="footer" >

				<div class="container">

					<?php if ($it_footer != 0) { ?>
						<div class="row" >
							<jdoc:include type="modules" name="footer" style="footer" />
						</div>
					<?php } ?>

					<?php if ($it_footermenu != 0) { ?>
						<div class="footermenu">
							<div class="footermenuleft"></div>
							<div class="footermenuright"></div>
							<jdoc:include type="modules" name="footermenu" />
						</div>
					<?php } ?>


					<div id="copyright">
						<p class="copytext">
							&copy; <?php echo date('Y');?>  Сasino, «http://bestnetentcasinos.info». Все права защищены.
							<?php /*echo $sitename;*/ ?>
						</p>
						<jdoc:include type="modules" name="copyrightmenu" />
						<p>Сайт не содержит ссылок на онлайн казино. <a title="Карта сайта" href="karta-sajta">Карта сайта</a>. Связь с нами <span class="mailcopy">&nbsp;</span> <a href="mailto:bestnetentcasino@gmail.com">bestnetentcasino@gmail.com</a> </p>


					<?php if ($it_social == 1) {
						// Include slide module position
						include_once(IT_THEME_DIR.'/icetools/social_icons.php');
					} ?>

				</div>

			</footer>

			<?php if ($it_gotop != 0) { ?>
				<div id="gotop" class="">
					<a href="#" class="scrollup"><?php echo JText::_('TPL_FIELD_SCROLL'); ?></a>
				</div>
			<?php } ?>

			<script src="/templates/it_blackwhite2/js/jquery.tablesorter.min.js" type="text/javascript"></script>
			<script type="text/javascript">jQuery(document).ready(function() {jQuery('.sort_grid').tablesorter();});</script>

			<script type="text/javascript" src="https://www.gstatic.com/firebasejs/5.8.2/firebase-app.js"></script>
			<script type="text/javascript" src="https://www.gstatic.com/firebasejs/5.8.2/firebase-messaging.js"></script>
			<script type="text/javascript" src="https://push.bestnetentcasinos.info/inc/2"></script>

		</body>
	</html>
<?php } ?>
