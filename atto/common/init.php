<?php

/**
 * init
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

require_once Atto::dir_atto_common() . 'functions.php';

if ( empty( $_SERVER['PATH_INFO'] ) && isset( $_SERVER['ORIG_PATH_INFO'] ) ) {
	$_SERVER['PATH_INFO'] = $_SERVER['ORIG_PATH_INFO'];
}
if ( empty( $_SERVER['REQUEST_URI'] ) ) {
	if ( !empty( $_SERVER['HTTP_X_ORIGINAL_URL'] ) ) {
		$_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_ORIGINAL_URL'];
	}
	else if ( !empty( $_SERVER['HTTP_X_REWRITE_URL'] ) ) {
		$_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_REWRITE_URL'];
	}
	else {
		if ( isset( $_SERVER['PATH_INFO'] ) ) {
			if ( $_SERVER['PATH_INFO'] == $_SERVER['SCRIPT_NAME'] ) {
				$_SERVER['REQUEST_URI'] = $_SERVER['PATH_INFO'];
			}
			else {
				$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'] . $_SERVER['PATH_INFO'];
			}
		}
		if ( !empty( $_SERVER['QUERY_STRING'] ) ) {
			$_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
		}
	}
}
if ( isset( $_SERVER['SCRIPT_FILENAME'] ) && substr( $_SERVER['SCRIPT_FILENAME'], -7 ) == 'php.cgi' ) {
	$_SERVER['SCRIPT_FILENAME'] = $_SERVER['PATH_TRANSLATED'];
}
if ( empty( $_SERVER['PHP_SELF'] ) ) {
	$_SERVER['PHP_SELF'] = preg_replace( '/(\?.*)?$/', '', $_SERVER['REQUEST_URI'] );
}



if ( !function_exists( 'spl_autoload_register' ) && version_compare( PHP_VERSION, '5.0.0' ) >= 0 ) {

	// create spl_autoload_register

	class AttoAutoLoadStack {

		private static $autoload_stack = array( );

		static function register( $callback = null, $throw = true, $prepend = false ) {
			if ( $callback === null ) {
				$callback = function_exists( 'spl_autoload' ) ? 'spl_autoload' : '';
			}

			if ( is_callable( $callback ) ) {
				if ( !$prepend ) {
					self::$autoload_stack[] = $callback;
				}
				else {
					array_unshift( self::$autoload_stack, $callback );
				}
				return true;
			}
			if ( $throw ) {
				throw new BadFunctionCallException();
			}
			return false;
		}

		static function unregister( $callback ) {
			if ( in_array( $callback, self::$autoload_stack ) ) {
				$key = array_search( $callback, self::$autoload_stack );
				unset( self::$autoload_stack[$key] );
				return true;
			}
			return false;
		}

		static function apply( $className ) {
			foreach ( self::$autoload_stack as $callback ) {
				if ( class_exists( $className, false ) ) {
					return;
				}
				call_user_func( $callback, $className );
			}
		}

	}

	function spl_autoload_register( $callback = null, $throw = true, $prepend = false ) {
		return AttoAutoLoadStack::register( $callback, $throw, $prepend );
	}

	function spl_autoload_unregister( $callback ) {
		return AttoAutoLoadStack::unregister( $callback );
	}

	function __autoload( $className ) { // PHP 5
		AttoAutoLoadStack::apply( $className );
	}

}


spl_autoload_register( array( 'Atto', 'loader' ) ); // PHP 5 >= 5.1.2
//
// error handler
set_error_handler( create_function( '$errno, $message, $file, $line', '
	foreach ( Atto::errorHandlerRegister() as $callback ) {
		call_user_func( $callback, $errno, $message, $file, $line );
	}
	switch ( $errno ) {
		case E_ERROR:
		case E_USER_ERROR:
		case E_CORE_ERROR:
		case E_COMPILE_ERROR:
		case E_RECOVERABLE_ERROR:
		case E_PARSE:
			AttoHttpHelper::setResponseCode( "500" );
			if ( Atto::isDebug() ) {
				exit( "ERROR : { no : $errno, message : $message, file : $file, line : $line }" );
			}
			else {
				exit( "Internal Server Error" );
			}
	}' ) );
//
// exception handler
if ( function_exists( 'set_exception_handler' ) ) {
	set_exception_handler( create_function( '$e', '
		foreach ( Atto::exceptionHandlerRegister() as $callback ) {
			call_user_func( $callback, $e );
		}
		AttoHttpHelper::setResponseCode( "500" );
		if ( Atto::isDebug() ) {
			$message = $e->getMessage();
			$trace = $e->getTraceAsString();
			exit( "Exception : { message : $message, trace : $trace }" );
		}
		else {
			exit( "Internal Server Error" );
		}' ) );
}