<?php

/**
 * attophp
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
/*
 * _atto-gate.php
 *
 * The Front Controller to handle request
 */

require dirname( __FILE__ ) . '/../atto/atto.php';
Atto::cascade( 'app_root', array(
	'admin_teimezone'	 => 'Asia/Tokyo',
	'logging_level'		 => 4, // 0:off/1:error only/2:warning or error/3:more info/4:all
	'logging_MiBbyte'	 => 2,
	'logging_files'		 => array( 'log1.txt', 'log2.txt', 'log3.txt' ),
	'root_uri'	 => '..',
	'htdocs_uri' => '..',
	'app_uri'	 => '',
) );

/**
 * app_root
 * 
 * callback from Atto#cascade
 */
function app_root() {

	class AppDispatcher extends AttoStaticSiteWrapperDispatcher {
		
	}

	$dispatcher = new AppDispatcher();
	$dispatcher->dispatch();
}
