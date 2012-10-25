<?php
/**
 * default layout
 *
 * The default layout
 * 
 * 
 * PHP versions 5
 *
 * attophp (tm) : Tha Small Development Framework
 * Copyright (c) 2012 nulil
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright	Copyright &copy; 2012 nulil
 * @link		
 * @license		MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?><!DOCTYPE html>
<!--[if lte IE 6 ]> <html lang="ja" class="no-js oldie ie v6"> <![endif]-->
<!--[if IE 7 ]> <html lang="ja" class="no-js oldie ie v7"> <![endif]-->
<!--[if IE 8 ]> <html lang="ja" class="no-js ie v8"> <![endif]-->
<!--[if IE 9 ]> <html lang="ja" class="no-js ie v9"> <![endif]-->
<!--[if gt IE 9 ]> <html lang="ja" class="no-js ie gt-v9"> <![endif]-->
<!--[if !(IE)]><!--> <html lang="ja" class="no-js"> <!--<![endif]-->
	<head>
		<!-- atto default layout -->

		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

		<title><?php echo $this->title_for_layout; ?></title>

		<!--[if lt IE 9]>
			<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
			<style>header,footer,nav,section,article,figure,aside { display:block; }</style>
		<![endif]-->

		<script>!function(){var d=document.documentElement;d.className=d.className.replace(/(^ *| +)no-js( +| *$)/,'$1js$2');}()</script>
		<script src="//css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
		<script src="<?php echo Atto::baseUri( true ); ?>js/mm.min.js"></script>

		<?php echo $this->scripts_for_layout; ?>

	</head>
	<body>
		<header>
			<h1><?php echo $this->title_for_layout; ?></h1>
		</header>

		<section>
			<?php echo $this->content_for_layout; ?>

		</section>

		<footer>
		</footer>

	</body>
</html>