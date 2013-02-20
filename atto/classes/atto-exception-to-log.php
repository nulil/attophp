<?php

/**
 * AttoExceptionToLog
 *
 * default set to exception handler
 * デフォルトでは、例外ハンドラに設定される
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
class AttoExceptionToLog {

	static public function publish( $e, $tag = 'Uncaught Exception' ) {
		@AttoFilebaseLogger::logging( 4, $tag, array( 'message'   => $e->getMessage(), 'Exception' => get_class( $e ), 'trace'	 => $e->getTraceAsString() ) );
	}

}