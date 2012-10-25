<?php

/**
 * AttoErrorToLog
 * 
 * default set to error handler
 * デフォルトでは、エラーハンドラに設定される
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
 * 
 * @class
 */
class AttoErrorToLog {

	static public function publish( $errno, $message, $file, $line ) {
		$err_names = array(
			E_USER_ERROR => array( 'name'  => 'ERROR', 'level' => 1 ),
			E_ERROR => array( 'name'		 => 'ERROR', 'level'		=> 1 ),
			E_USER_WARNING => array( 'name'	=> 'WARNING', 'level'   => 2 ),
			E_WARNING => array( 'name'		=> 'WARNING', 'level'	   => 2 ),
			E_USER_NOTICE => array( 'name'   => 'INFO', 'level'  => 3 ),
			E_NOTICE => array( 'name'   => 'INFO', 'level'  => 3 ),
			E_STRICT => array( 'name'  => 'DEBUG', 'level' => 4 )
		);
		if ( isset( $err_names[$errno] ) ) {
			$err_name = $err_names[$errno]['name'];
			$log_Lv = $err_names[$errno]['level'];
		}
		else {
			$err_name = 'unknown error';
			$log_Lv = 4;
		}
		@AttoFilebaseLogger::logging( $log_Lv, $err_name,
								  array( 'errno'   => $errno, 'message' => $message, 'file'	=> $file, 'line'	=> $line ) );

		switch ( $errno ) {
			case E_ERROR:
			case E_USER_ERROR:
			case E_CORE_ERROR:
			case E_COMPILE_ERROR:
				exit( 'ERROR : ' . $message );
		}
	}

}
