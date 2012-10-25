<?php
/**
 * 404
 *
 * displayed when the file is not found
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
//
?><!DOCTYPE html>
<!--[if lte IE 6 ]> <html lang="ja" class="no-js oldie ie6 lte-ie9 lte-ie8 lte-ie7 lte-ie6"> <![endif]-->
<!--[if IE 7 ]> <html lang="ja" class="no-js oldie ie7 lte-ie9 lte-ie8 lte-ie7"> <![endif]-->
<!--[if IE 8 ]> <html lang="ja" class="no-js ie8 lte-ie9 le-ite8"> <![endif]-->
<!--[if IE 9 ]> <html lang="ja" class="no-js ie9 lte-ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="ja" class="no-js"> <!--<![endif]-->
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

		<title>404  Not Found</title>

		<!--[if lt IE 9]>
			<script type="text/javascript" src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
			<style type="text/css">header,footer,nav,section,article,figure,aside { display:block; }</style>
		<![endif]-->
		
		<script type="text/javascript">!function(){document.documentElement.className=document.documentElement.className.replace(/(^ *| +)no-js( +| *$)/,'$1on-js$2');}()</script>
		<script type="text/javascript" src="//css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
		<script type="text/javascript" src="<?php echo Atto::baseUri( true ); ?>js/mm.min.js"></script>
	</head>
	<body>
		<section>
			<h1>404  Not Found</h1>
			<p><?php echo $this->error_message; ?></p>
		</section>
	</body>
</html>