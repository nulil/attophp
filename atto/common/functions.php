<?php

/**
 * functions
 *
 * Functions provided by attophp ,and the function used in the core
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

/**
 * args_join
 *
 * @param {String} $glue
 * @param {String} $arg...
 * @return {String}
 */
function args_join( $glue, $arg1/* , $arg2 , $arg3 ... */ ) {
	if ( func_num_args() === 2 && is_array( $arg1 ) ) {
		return implode( $glue, $arg1 );
	}
	return implode( $glue, array_slice( func_get_args(), 1 ) );
}

/**
 * start_with
 * 
 * @param {string} $search
 * @param {string} $subject
 * @return {boolean} 
 */
function starts_with( $search, $subject ) {
	return (0 === strpos( $subject, $search ));
}

/**
 * end_with
 * 
 * @param {string} $search
 * @param {string} $subject
 * @return {boolean} 
 */
function ends_with( $search, $subject ) {
	$l = strlen( $search );
	return ($l <= strlen( $subject ) && $search == substr( $subject, -1 * $l ));
}

/**
 * no_null_string
 *
 * @param {string} $string
 * @return {string}
 */
function no_null_string( $string ) {
	return preg_replace( '/(\\\\0)+/', '', preg_replace( '/\0+/', '', $string ) );
}

/**
 * invoke
 * 
 * @param {callable} $callback
 * @param {Array} $args
 * @return {mixed} 
 */
function invoke( $callback, $args ) {
	if ( is_callable( $callback ) ) {
		return call_user_func_array( $callback, $args );
	}
}

/**
 * call
 * 
 * @param {callable} $callback
 * @return {mixed} 
 */
function call( $callback /* , $arg... */ ) {
	return invoke( $callback, array_slice( func_get_args(), 1 ) );
}

/**
 * is_key_exists_and_callable
 * 
 * @param {Array or ArrayObject} $arr
 * @param {string} $key
 * @return {boolean} 
 */
function is_key_exists_and_callable( $arr, $key ) {
	return array_key_exists( $key, $arr ) && (is_callable( array( $arr, $key ) ) || is_callable( $arr[$key] ));
}

/**
 * snake_case_to_camel_case
 * 
 * @staticvar {lambda} $callback
 * @param {string} $value	snake case string
 * @param {boolean} $isUcfirst
 * @return {string} 
 */
function snake_case_to_camel_case( $value, $isUcfirst = false ) {
	static $callback = null;
	if ( $callback == null ) {
		$callback = create_function( '$matchs', 'return strtoupper($matchs[1]);' );
	}

	$value = preg_replace_callback( '/_(.)/u', $callback, $value );
	if ( $isUcfirst ) {
		$value = ucfirst( $value );
	}
	return $value;
}

/**
 * camel_case_to_snake_case
 * 
 * @staticvar {lambda} $callback
 * @param {string} $value	camel case string
 * @param {boolean} $isLcfirst
 * @return {string} 
 */
function camel_case_to_snake_case( $value, $isLcfirst = true ) {
	static $callback = null;
	if ( $callback == null ) {
		$callback = create_function( '$matchs', 'return $matchs[1] . \'_\' . strtolower($matchs[2]);' );
	}

	$value = preg_replace_callback( '/(.)([A-Z])/u', $callback, $value );
	if ( $isLcfirst ) {
		$value = lcfirst( $value );
	}
	return $value;
}

/**
 * camel_case_to_hyphen_case
 * 
 * @staticvar {lambda} $callback
 * @param {string} $value	camel case string
 * @param {boolean} $isLcfirst
 * @return {string} 
 */
function camel_case_to_hyphen_case( $value, $isLcfirst = true ) {
	static $callback = null;
	if ( $callback == null ) {
		$callback = create_function( '$matchs', 'return $matchs[1] . \'-\' . strtolower($matchs[2]);' );
	}

	$value = preg_replace_callback( '/(.)([A-Z])/u', $callback, $value );
	if ( $isLcfirst ) {
		$value = lcfirst( $value );
	}
	return $value;
}

/**
 * hyphen_case_to_camel_case
 * 
 * @staticvar {lambda} $callback
 * @param {string} $value	hyphen case string
 * @param {boolean} $isUcfirst
 * @return {string} 
 */
function hyphen_case_to_camel_case( $value, $isUcfirst = false ) {
	static $callback = null;
	if ( $callback == null ) {
		$callback = create_function( '$matchs', 'return strtoupper($matchs[1]);' );
	}

	$value = preg_replace_callback( '/-(.)/u', $callback, $value );
	if ( $isUcfirst ) {
		$value = ucfirst( $value );
	}
	return $value;
}

/**
 * virtual_realpath
 * 
 * @param string $value
 * @param string $ds default is DIRECTORY_SEPARATOR
 * @return string 
 */
function virtual_realpath( $value, $ds = DIRECTORY_SEPARATOR ) {
	$values = explode( $ds, str_replace( array( $ds . '.' . $ds, $ds . $ds ), $ds, $value ) );
	$uris = array( );
	foreach ( $values as $uri ) {
		switch ( $uri ) {
			case '..':
				array_pop( $uris );
				break;
			default:
				$uris[] = $uri;
				break;
		}
	}
	return implode( $ds, $uris );
}

/**
 * assoc_filter
 * 
 * @param array $arr
 * @param callback $callback
 * @return array|boolean 
 */
function assoc_filter( array $arr, $callback = null ) {
	static $fn = null;
	if ( !$callback ) {
		if ( !$fn ) {
			$fn = create_function( '$k,$v', 'return !!($k && $v);' );
		}
		$callback = $fn;
	}
	if ( is_callable( $callback ) ) {
		$res = array( );
		foreach ( $arr as $key => $value ) {
			if ( call_user_func( $callback, $key, $value ) ) {
				$res[$key] = $value;
			}
		}
		return $res;
	}
	return false;
}

/**
 * scan_dir
 * 
 * @param string $dir
 * @param boolean $omitted
 * @return array 
 */
function scan_dir( $dir, $omitted = true, $sort_callback = null ) {
	$opendir = @opendir( $dir );
	$files = array( );

	if ( $opendir !== FALSE ) {
		if ( $omitted ) {
			while ( ($file = readdir( $opendir )) !== FALSE ) {
				if ( $file !== '.' && $file !== '..' ) {
					$files[] = $file;
				}
			}
		}
		else {
			while ( ($file = readdir( $opendir )) !== FALSE ) {
				$files[] = $file;
			}
		}
		closedir( $opendir );

		if ( $files && $sort_callback && is_callable( $sort_callback ) ) {
			usort( $files, $sort_callback );
		}
	}
	return $files;
}

if ( !function_exists( 'lcfirst' ) ) {

	function lcfirst( $str ) {
		return strlen( $str ) ? strtolower( $str[0] ) . substr( $str, 1 ) : '';
	}

}

/**
 * debug_function
 *
 * @param {Function} $callback
 * @param {Boolean} $flag
 */
function debug_function( $callback, $flag = null ) {
	if ( !(false === $flag) && is_callable( $callback ) ) {
		$callback();
	}
}

if ( !function_exists( 'debug_print' ) ) {

	/**
	 * debug_print
	 *
	 * @param {String} $message
	 * @param {Boolean} $flag
	 */
	function debug_print( $message, $flag = null ) {
		if ( !(false === $flag) ) {
			echo $message;
		}
	}

}