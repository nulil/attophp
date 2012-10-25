<?php

define( 'DS', DIRECTORY_SEPARATOR );

/**
 * atto
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
class Atto {

	const VERSION = '0.2.10';

	/**
	 * @var array 
	 */
	static private $_options = array(
//		'base_encode' => 'UTF-8',
		'admin_teimezone' => 'Asia/Tokyo',
		'logging_level'   => 2, // 0:off/1:error only/2:warning or error/3:more info/4:all
		'logging_MiBbyte' => 2,
		'logging_files'   => array( 'log1.txt', 'log2.txt', 'log3.txt' ),
		'error_log'	  => true,
		'exception_log'  => true,
		'app_uri'		=> '..',
		'root_uri'	   => '../..',
		'htdocs_uri'	 => '',
		'gate_param_key' => null,
		'debug'		  => false,
		'hide_gate'	  => true,
		'ssl_base_uri'   => '',
	);

	private function __construct() {
		
	}

	//
	//
	// ********************************************************************************************************
	// *************                            static private function                           *************
	// ********************************************************************************************************

	/**
	 * _plugsInsertion
	 * 
	 * @method _plugsInsertion
	 */
	static private function _plugsInsertion() {
		$patterns = array( self::dir_plugin() . '*' );
		while ( $patterns ) {
			$pattern = array_shift( $patterns );
			foreach ( glob( $pattern ) as $item ) {
				if ( is_dir( $item ) ) {
					$patterns[] = $item . DS . '*';
				}
				elseif ( is_file( $item ) ) {
					if ( '__plug__.php' === basename( $item ) ) {
						require_once $item;
					}
				}
			}
		}
	}

	/**
	 * _handlerRegister
	 * 
	 * @method _handlerRegister
	 * @param array $handles
	 * @param callable $callback
	 * @param boolean $add
	 * @return mixed 
	 */
	static private function _handlerRegister( array &$handles, &$callback, &$add ) {
		if ( $callback ) {
			if ( is_callable( $callback ) ) {
				if ( $add ) {
					// add
					if ( !in_array( $callback, $handles ) ) {
						$handles[] = $callback;
						return true;
					}
				}
				else {
					// remove
					$key = array_search( $callback, $handles );
					if ( is_int( $key ) ) {
						array_splice( $handles, $key, 1 );
						return true;
					}
				}
			}
		}
		else {
			// get
			return array_reverse( $handles );
		}
		return false;
	}

	/**
	 * _deleteSslUri
	 * 
	 * @method _deleteSslUri
	 * @param string $value
	 * @return string 
	 */
	static private function _deleteSslUri( $value ) {
		if ( self::getSslBaseUri() && self::isHttps() && starts_with( self::getSslBaseUri(), $value ) ) {
			$value = '/' . substr( $value, strlen( self::getSslBaseUri() ) );
		}
		return $value;
	}

	//
	//
	// ********************************************************************************************************
	// *************                            static public function                            *************
	// ********************************************************************************************************

	/**
	 * cascade
	 *
	 * @method cascade
	 * @param mixed $callback
	 * @param array $options
	 * @throws Exception 
	 */
	static public function cascade( $callback, array $options = array( ) ) {

		// options init
		self::$_options = array_merge( self::$_options, $options );


		$init = create_function( '', '
			require Atto::dir_atto_common() . \'init.php\';
			require Atto::dir_common() . \'init.php\';' );
		$init();


		if ( self::$_options ['hide_gate'] ) {
			if ( starts_with( Atto::gateScriptUri(), Atto::requestUri() ) ) {
				AttoHttpHelper::redirect( Atto::uri() );
			}
		}


		// timezone
		if ( function_exists( 'date_default_timezone_set' ) ) {
			date_default_timezone_set( self::$_options['admin_teimezone'] );
		}


		// error_log
		if ( self::$_options['error_log'] ) {
			self::errorHandlerRegister( array( 'AttoErrorToLog', 'publish' ) );
		}
		set_error_handler( create_function( '$errno , $errstr, $errfile, $errline, $errcontext', '
				// callback
				foreach ( Atto::errorHandlerRegister() as $callback ) {
					$res = call_user_func( $callback, $errno , $errstr, $errfile, $errline, $errcontext );
					if ( $res === false ) {
						return $res;
					}
				}' ) );

		if ( function_exists( 'set_exception_handler' ) ) {
			// exception_log
			if ( self::$_options['exception_log'] ) {
				self::exceptionHandlerRegister( array( 'AttoExceptionToLog', 'publish' ) );
			}
			set_exception_handler( create_function( '$e', '
					// callback
					foreach ( Atto::exceptionHandlerRegister() as $callback ) {
						$res = call_user_func( $callback, $e );
						if ( $res === false ) {
							return $res;
						}
					}' ) );
		}


		// gzip
		if ( function_exists( 'ob_gzhandler' ) ) {
			self::publishHandlerRegister( create_function( '$content, $mode', '
					$zlab_output_compression = ini_get( "zlib.output_compression" );
					if ( !$zlab_output_compression
							|| $zlab_output_compression == "0"
							|| strtolower( $zlab_output_compression ) == "off" ) {

						$content_encoding = Atto::serchHeader( "/^Content-Encoding:/iu" );
						if ( $content_encoding === false && Atto::isGzipContentType() ) {
							return ob_gzhandler( $content, $mode );
						}
					}
					return $content;' ) );
		}


		set_include_path( get_include_path() . PATH_SEPARATOR . Atto::dir_app() );


		// callback
		if ( !is_callable( $callback ) ) {
			$callback = array( $callback, '__invoke' );  // PHP < 5.3
			if ( !is_callable( $callback ) ) {
				throw new Exception( 'Callees of the callback is unknown' );
			}
		}

		// publishHandler
		ob_start( create_function( '$content, $mode', '
				// header
				Atto::overrideXpowerdBy();
				if ( Atto::isDebug() ) {
					header( "ElapsedMicrotime: " . Atto::getElapsedMicrotime() );
				}
				if ( class_exists( "AttoHttpHelper", false ) ) {
					AttoHttpHelper::outputHeaders();
				}
				// callback
				foreach ( Atto::publishHandlerRegister() as $callback ) {
					$res = call_user_func( $callback, $content, $mode );
					if ( $res !== false ) {
						$content = $res;
					}
				}
				return $content;' ) );
		ob_implicit_flush( false );

		try {
			self::_plugsInsertion();
			call_user_func( $callback, self::uri() );
		}
		catch ( AttoAbstractHasRenderException $e ) {
			$content = ob_get_contents();
			if ( !$content ) {
				$content = '';
			}
			else {
				ob_clean();
			}
			$e->render( $content );
		}
		catch ( Exception $e ) {
			$render = array( $e, 'render' );
			if ( is_callable( $render ) ) {
				$content = ob_get_contents();
				if ( !$content ) {
					$content = '';
				}
				else {
					ob_clean();
				}
				call_user_func( $render, $content );
			}
			else {
				throw $e;
			}
		}
		ob_end_flush();
	}

	/**
	 * setOptions
	 * 
	 * @method setOptions
	 * @param string $key
	 * @param mixed $value
	 */
	static public function setOptions( $key, $value ) {
		self::$_options[$key] = $value;
	}

	/**
	 * getSslBaseUri
	 * 
	 * @method getSslBaseUri
	 * @return string 
	 */
	static public function getSslBaseUri() {
		return self::$_options['ssl_base_uri'];
	}

	/**
	 * overrideXpowerdBy
	 * 
	 * @method overrideXpowerdBy
	 */
	static public function overrideXpowerdBy() {
		if ( !headers_sent() ) {
			$sign = ' atto/' . self::VERSION;
			$powered_by = self::serchHeader( '/^X-Powered-By:(.*)/i' );
			$powered_by = $powered_by ? $powered_by[0] : '';
			header( 'X-Powered-By:' . $powered_by . $sign );
		}
	}

	/**
	 * serchHeader
	 * 
	 * @method serchHeader
	 * @param string $pattern
	 * @return mixed 
	 */
	static public function serchHeader( $pattern ) {
		foreach ( headers_list() as $http_header ) {
			if ( 0 < preg_match( $pattern, $http_header, $matchs ) ) {
				return $matchs;
			}
		}
		return false;
	}

	/**
	 * isGzipContentType
	 * 
	 * @method isGzipContentType
	 * @return boolean
	 */
	static public function isGzipContentType() {
		$content_type = self::serchHeader( '/^Content-Type: *(.*)/i' );
		if ( !$content_type ) {
			$content_type = 'text';
		}
		else {
			$content_type = $content_type[1];
		}

		return starts_with( 'text', $content_type )
				|| ends_with( 'script', $content_type )
				|| ends_with( 'xml', $content_type )
				|| ends_with( 'xml-dtd', $content_type );
	}

	/**
	 * isDebug
	 * 
	 * @method isDebug
	 * @return boolean 
	 */
	static public function isDebug() {
		return !!self::$_options['debug'];
	}

	/**
	 * errorHandlerRegister
	 * 
	 * errorHandlerで呼び出される関数・メソッドの登録、削除、取得
	 * 
	 * @method errorHandlerRegister
	 * @staticvar array $handles
	 * @param type $callback
	 * @param type $add
	 * @return type 
	 */
	static public function errorHandlerRegister( $callback = null, $add = true ) {
		static $handles = array( );
		return self::_handlerRegister( $handles, $callback, $add );
	}

	/**
	 * exceptionHandlerRegister
	 * 
	 * exceptionHandlerで呼び出される関数・メソッドの登録、削除、取得
	 * 
	 * @method exceptionHandlerRegister
	 * @staticvar array $handles
	 * @param type $callback
	 * @param type $add
	 * @return type 
	 */
	static public function exceptionHandlerRegister( $callback = null, $add = true ) {
		static $handles = array( );
		return self::_handlerRegister( $handles, $callback, $add );
	}

	/**
	 * publishHandlerRegister
	 *
	 * publishHandlerで呼び出される関数・メソッドの登録、削除、取得
	 * 
	 * @method publishHandlerRegister
	 * @staticvar array $handles
	 * @param mixed $callback
	 * @param boolean $add add:true and $callback is callable / remove:false and $callback is callable
	 * @return mixed 
	 */
	static public function publishHandlerRegister( $callback = null, $add = true ) {
		static $handles = array( );
		return self::_handlerRegister( $handles, $callback, $add );
	}

	/**
	 * getOptions
	 * 
	 * @method getOptions
	 * @return array 
	 */
	static public function getOptions() {
		return array_merge( array( ), self::$_options );
	}

	/**
	 * loader
	 *
	 * @method loader
	 * @param $class_name
	 */
	static public function loader( $class_name ) {

		static $fn = null;
		if ( $fn === null ) {
			$fn = create_function( '$v', 'return camel_case_to_hyphen_case( $v );' );
		}
		$file_uri = args_join( DS, array_map( $fn, explode( DS
								, str_replace( array( '\\', '__' ), DS, ltrim( $class_name, '\\' ) ) ) ) );

		if ( starts_with( 'atto-', $file_uri ) ) {
			$path = self::makeAccessPath( array( self::dir_atto_autoLoad() ), $file_uri, array( '.php' ) );
			if ( $path ) {
				require $path;
				return true;
			}
		}

		// app auto loder
		if ( function_exists( 'app_auto_loder' ) ) {
			call_user_func( 'app_auto_loder', $class_name, $file_uri );
			if ( class_exists( $class_name, false ) || interface_exists( $class_name, false ) ) {
				return true;
			}
		}

		// other search
		static $loadeds = array( );

		$extends = array( '.php', '.inc' );

		$dirs = array(
			self::dir_autoLoad(),
			self::dir_atto_autoLoad(),
		);
		foreach ( $dirs as $dir ) {
			$path = self::makeAccessPath( $dir, $file_uri, $extends );
			if ( $path && $path !== '' && !in_array( $path, $loadeds ) ) {
				include_once $path;
				$loadeds[] = $path;
				if ( class_exists( $class_name, false ) || interface_exists( $class_name, false ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * makeAccessPath
	 *
	 * @meyhod makeAccessPath
	 * @param string or array $bases
	 * @param string or array $files
	 * @param array $extends
	 * @return string or false file path
	 */
	static public function makeAccessPath( $bases, $files, array $extends = array( '' ) ) {

		if ( !(is_array( $bases )) ) {
			$bases = array( $bases ? $bases : '' );
		}
		if ( !(is_array( $files )) ) {
			$files = array( $files ? $files : '' );
		}
		if ( count( $extends ) <= 0 ) {
			$extends[] = '';
		}

		foreach ( $bases as $base ) {
			foreach ( $files as $file ) {
				foreach ( $extends as $extend ) {
					$file_path = $base . $file . $extend;
					if ( file_exists( $file_path ) && is_file( $file_path ) ) {
						return $file_path;
					}
				}
			}
		}
		return false;
	}

	/**
	 * uri
	 *
	 * @method uri
	 * @return string
	 */
	static public function uri() {
		static $val = null;
		if ( $val === null ) {
			if ( self::$_options['gate_param_key'] ) {
				$pathInfo = $_REQUEST[self::$_options['gate_param_key']];
				unset( $_REQUEST[self::$_options['gate_param_key']] );
				unset( $_GET[self::$_options['gate_param_key']] );
			}
			else if ( isset( $_SERVER['PATH_INFO'] ) ) {
				$pathInfo = $_SERVER['PATH_INFO'];
			}
			else {
				$request_uri = self::requestUri();
				if ( false !== ($pos = strpos( $request_uri, '?' )) ) {
					$request_uri = substr( $request_uri, 0, $pos );
				}
				$pathInfo = substr( $request_uri, strlen( self::baseUri() ) );
			}

			if ( starts_with( '/', $pathInfo ) ) {
				$pathInfo = substr( $pathInfo, 1 );
			}
			$val = $pathInfo ? $pathInfo : '';
		}
		return $val;
	}

	/**
	 * isHttps
	 *
	 * @method isHttps
	 * @return boolean
	 */
	static public function isHttps() {
		static $val = null;
		if ( $val === null ) {
			if ( isset( $_SERVER['HTTPS'] ) && ($_SERVER['HTTPS'] == '1' || strtolower( $_SERVER['HTTPS'] ) == 'on')
					|| isset( $_SERVER['SERVER_PORT'] ) && $_SERVER['SERVER_PORT'] == '443'
					|| isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && strtolower( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) == 'https' ) {
				$val = true;
			}
			else {
				$val = false;
			}
		}
		return $val;
	}

	/**
	 * protocol
	 *
	 * @method protocol
	 * @return string
	 */
	static public function protocol() {
		static $val = null;
		if ( $val === null ) {
			$val = self::isHttps() ? 'https://' : 'http://';
		}
		return $val;
	}

	/**
	 * requestUrl
	 *
	 * @method requestUrl
	 * @param boolean $concat_protocol
	 * @return string request url
	 */
	static public function requestUrl( $concat_protocol = true ) {
		static $val = array( );
		if ( array_key_exists( $concat_protocol ? 't' : 'f', $val ) ) {
			return $val[$concat_protocol ? 't' : 'f'];
		}
		$val[$concat_protocol] = ($concat_protocol ? self::protocol() : '')
				. self::domain()
				. (self::isHttps() && self::getSslBaseUri() ? rtrim( self::getSslBaseUri(), '/' ) : '')
				. self::requestUri();
		return $val[$concat_protocol];
	}

	/**
	 * requestUri
	 *
	 * @method requestUri
	 * @return string	$_SERVER['REQUEST_URI'] 
	 */
	static public function requestUri() {
		static $val = null;
		if ( $val === null ) {
			$val = self::_deleteSslUri( $_SERVER['REQUEST_URI'] );
		}
		return $val;
	}

	/**
	 * domain
	 *
	 * @method domain
	 * @return string	$_SERVER['SERVER_NAME'] 
	 */
	static public function domain() {
		return $_SERVER['SERVER_NAME'];
	}

	/**
	 * gateScriptUri
	 *
	 * @method gateScriptUri
	 * @return string	$_SERVER['SCRIPT_NAME']
	 */
	static public function gateScriptUri() {
		static $val = null;
		if ( $val === null ) {
			$val = self::_deleteSslUri( $_SERVER['SCRIPT_NAME'] );
		}
		return $val;
	}

	/**
	 * baseUri
	 * 
	 * @method baseUri
	 * @staticvar null $val
	 * @param boolean $is_shared_ssl
	 * @return string 
	 */
	static public function baseUri( $is_shared_ssl = false ) {
		static $val = array( );
		$key = $is_shared_ssl ? 't' : 'f';
		if ( !isset( $val[$key] ) ) {
			$uri = virtual_realpath( dirname( self::gateScriptUri() ) . '/' . self::$_options['root_uri'], '/' );
			if ( !ends_with( '/', $uri ) ) {
				$uri = $uri . '/';
			}

			if ( $is_shared_ssl && Atto::isHttps() && self::getSslBaseUri() ) {
				$uri = rtrim( self::getSslBaseUri(), '/' ) . $uri;
			}
			$val[$key] = $uri;
		}
		return $val[$key];
	}

	/**
	 * baseUrl
	 *
	 * @method baseUrl
	 * @staticvar array $val
	 * @param boolean $concat_protocol
	 * @param boolean $force_ssl
	 * @return string app url
	 */
	static public function baseUrl( $concat_protocol = true, $force_ssl = null ) {
		static $val = array( );
		$key = ($concat_protocol ? 't' : 'f') . ($force_ssl === null ? ' ' : ($force_ssl ? 't' : 'f'));
		if ( !array_key_exists( $key, $val ) ) {
			if ( $force_ssl === null ) {
				$is_https = self::isHttps();
				$domain = self::domain();
			}
			elseif ( $force_ssl ) {
				$is_https = true;
				$domain = self::$_options['ssl_domain'];
			}
			else {
				$is_https = false;
				$domain = self::$_options['domain'];
			}

			if ( !$domain ) {
				$domain = self::domain();
			}
			$val[$key] = ($concat_protocol ? ($is_https ? 'https://' : 'http://') : '')
					. $domain
					. ($is_https && self::getSslBaseUri() ? rtrim( self::getSslBaseUri(), '/' ) : '')
					. self::baseUri();
		}
		return $val[$key];
	}

	/**
	 * dir_gateScript
	 *
	 * @method dir_gateScript
	 * @return string	
	 */
	static public function dir_gateScript() {
		static $val = null;
		if ( $val === null ) {
			$val = dirname( $_SERVER['SCRIPT_FILENAME'] ) . DS;
		}
		return $val;
	}

	/**
	 * dir_htdocs
	 *
	 * @method dir_htdocs
	 * @return string htdocs directry path
	 */
	static public function dir_htdocs() {
		static $val = null;
		if ( $val === null ) {
			$val = realpath( Atto::dir_gateScript() . str_replace( '/', DS, self::$_options['htdocs_uri'] ) ) . DS;
		}
		return $val;
	}

	/**
	 * dir_atto
	 *
	 * @method dir_atto
	 * @return string atto directry path
	 */
	static public function dir_atto() {
		static $val = null;
		if ( $val === null ) {
			$val = dirname( __FILE__ ) . DS;
		}
		return $val;
	}

	/**
	 * dir_atto_common
	 *
	 * @method dir_atto_common
	 * @return string atto/common directry path
	 */
	static public function dir_atto_common() {
		static $val = null;
		if ( $val === null ) {
			$val = self::dir_atto() . 'common' . DS;
		}
		return $val;
	}

	/**
	 * dir_atto_util
	 *
	 * @method dir_atto_util
	 * @return string atto/etc directry path
	 */
	static public function dir_atto_autoLoad() {
		static $val = null;
		if ( $val === null ) {
			$val = self::dir_atto() . 'auto-load' . DS;
		}
		return $val;
	}

	/**
	 * dir_atto_error
	 *
	 * @method dir_atto_error
	 * @return string atto/views/error directry path
	 */
	static public function dir_atto_error() {
		static $val = null;
		if ( $val === null ) {
			$val = self::dir_atto() . 'views' . DS . 'error' . DS;
		}
		return $val;
	}

	/**
	 * dir_atto_layout
	 *
	 * @method dir_atto_layout
	 * @return string atto/views/layout directry path
	 */
	static public function dir_atto_layout() {
		static $val = null;
		if ( $val === null ) {
			$val = self::dir_atto() . 'views' . DS . 'layout' . DS;
		}
		return $val;
	}

	/**
	 * dir_atto_lib
	 *
	 * @method dir_atto_lib
	 * @return string atto/lib directry path
	 */
	static public function dir_atto_lib() {
		static $val = null;
		if ( $val === null ) {
			$val = self::dir_atto() . 'lib' . DS;
		}
		return $val;
	}

	/**
	 * dir_app
	 *
	 * @method dir_app
	 * @return string app directry path
	 */
	static public function dir_app() {
		static $val = null;
		if ( $val === null ) {
			if ( starts_with( '/', self::$_options['app_uri'] ) ) {
				$val = realpath( self::$_options['app_uri'] ) . DS;
			}
			else {
				$val = realpath( self::dir_htdocs() . self::$_options['app_uri'] ) . DS;
			}
			$val = realpath( Atto::dir_gateScript() . str_replace( '/', DS, self::$_options['app_uri'] ) ) . DS;
		}
		return $val;
	}

	/**
	 * dir_common
	 *
	 * @method dir_common
	 * @return string app/common directry path
	 */
	static public function dir_common() {
		static $val = null;
		if ( $val === null ) {
			$val = self::dir_app() . 'common' . DS;
		}
		return $val;
	}

	/**
	 * dir_error
	 *
	 * @method dir_error
	 * @return string app/views/error directry path
	 */
	static public function dir_error() {
		static $val = null;
		if ( $val === null ) {
			$val = self::dir_views() . 'error' . DS;
		}
		return $val;
	}

	/**
	 * dir_layout
	 *
	 * @method dir_layout
	 * @return string app/views/layout directry path
	 */
	static public function dir_layout() {
		static $val = null;
		if ( $val === null ) {
			$val = self::dir_views() . 'layout' . DS;
		}
		return $val;
	}

	/**
	 * dir_piece
	 *
	 * @method dir_piece
	 * @return string app/views/piece directry path
	 */
	static public function dir_piece() {
		static $val = null;
		if ( $val === null ) {
			$val = self::dir_views() . 'piece' . DS;
		}
		return $val;
	}

	/**
	 * dir_lib
	 *
	 * @method dir_lib
	 * @return string app/lib directry path
	 */
	static public function dir_lib() {
		static $val = null;
		if ( $val === null ) {
			$val = self::dir_app() . 'lib' . DS;
		}
		return $val;
	}

	/**
	 * dir_autoLoad
	 *
	 * @method dir_autoLoad
	 * @return string app/auto-load directry path
	 */
	static public function dir_autoLoad() {
		static $val = null;
		if ( $val === null ) {
			$val = self::dir_app() . 'auto-load' . DS;
		}
		return $val;
	}

	/**
	 * dir_plugin
	 *
	 * @method dir_plugin
	 * @return string app/plugin directry path
	 */
	static public function dir_plugin() {
		static $val = null;
		if ( $val === null ) {
			$val = self::dir_app() . 'plugin' . DS;
		}
		return $val;
	}

	/**
	 * dir_hook
	 *
	 * @method dir_hook
	 * @return string app/hook directry path
	 */
	static public function dir_hook() {
		static $val = null;
		if ( $val === null ) {
			$val = self::dir_app() . 'hook' . DS;
		}
		return $val;
	}

	/**
	 * dir_huck
	 *
	 * @method dir_huck
	 * @return string app/huck directry path
	 */
	static public function dir_huck() {
		static $val = null;
		if ( $val === null ) {
			$val = self::dir_app() . 'huck' . DS;
		}
		return $val;
	}

	/**
	 * dir_controller
	 *
	 * @method dir_controller
	 * @return string app/controller directry path
	 */
	static public function dir_controller() {
		static $val = null;
		if ( $val === null ) {
			$val = self::dir_app() . 'controller' . DS;
		}
		return $val;
	}

	/**
	 * dir_views
	 *
	 * @method dir_views
	 * @return string app/views directry path
	 */
	static public function dir_views() {
		static $val = null;
		if ( $val === null ) {
			$val = self::dir_app() . 'views' . DS;
		}
		return $val;
	}

	/**
	 * dir_view
	 *
	 * @method dir_views
	 * @return string app/views/view directry path
	 */
	static public function dir_view() {
		static $val = null;
		if ( $val === null ) {
			$val = self::dir_views() . 'view' . DS;
		}
		return $val;
	}

	/**
	 * dir_model
	 *
	 * @method dir_model
	 * @return string app/model directry path
	 */
	static public function dir_model() {
		static $val = null;
		if ( $val === null ) {
			$val = self::dir_app() . 'model' . DS;
		}
		return $val;
	}

	/**
	 * dir_var
	 *
	 * @method dir_var
	 * @return string app/var directry path
	 */
	static public function dir_var() {
		static $val = null;
		if ( $val === null ) {
			$val = self::dir_app() . 'var' . DS;
		}
		return $val;
	}

	/**
	 * dir_var_log
	 *
	 * @method dir_var_log
	 * @return string app/var/log directry path
	 */
	static public function dir_var_log() {
		static $val = null;
		if ( $val === null ) {
			$val = self::dir_var() . 'log' . DS;
		}
		return $val;
	}

	/**
	 * dir_var_cache
	 *
	 * @method dir_var_cache
	 * @return string app/var/cache directry path
	 */
	static public function dir_var_cache() {
		static $val = null;
		if ( $val === null ) {
			$val = self::dir_var() . 'cache' . DS;
		}
		return $val;
	}

	/**
	 * getElapsedMicrotime
	 * attoを開始してからの経過時間を返す
	 * 
	 * @method getElapsedMicrotime
	 * @return float 
	 */
	static public function getElapsedMicrotime() {
		static $val = null;
		if ( $val === null ) {
			$val = microtime( true );
			return 0;
		}
		return microtime( true ) - $val;
	}

}