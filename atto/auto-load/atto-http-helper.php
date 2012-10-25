<?php

/**
 * AttoHttpHelper
 *
 * http processing helper
 * http処理のヘルパー
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
class AttoHttpHelper {

	static private $_headers = null;

	/**
	 * isPost
	 * 
	 * @method isPost
	 * @return {boolean} 
	 */
	static public function isPost() {
		return $_SERVER['REQUEST_METHOD'] == 'POST';
	}

	/**
	 * getHost
	 * 
	 * @method getHost
	 * @return {string} 
	 */
	static public function getHost() {
		if ( empty( $_SERVER['HTTP_HOST'] ) ) {
			return $_SERVER['SERVER_NAME'];
		}
		else {
			return htmlentities( $_SERVER['HTTP_HOST'] );
		}
	}

	/**
	 * getRemoteHost
	 *
	 * @method getRemoteHost
	 * @return {string}
	 */
	static public function getRemoteHost() {
		static $val = null;
		if ( $val === null ) {
			$val = gethostbyaddr( $_SERVER['REMOTE_ADDR'] );
		}
		return $val;
	}

	/**
	 * redirect
	 *
	 * @method redirect
	 * @param {string} $url
	 * @param {int} $response_code
	 * @param {boolean} $is_exit	exit()を実行するか
	 */
	static public function redirect( $url, $response_code = 302, $is_exit = true ) {

		if ( preg_match( '#^https?://#iu', $url ) <= 0 ) {
			$url = Atto::baseUrl() . $url;
		}

		$url = preg_replace( '|[^a-z0-9-~+_.?#=&;,/:%!]|iu', '', $url );
		$url = str_replace( array( '%0d', '%0a' ), '', $url );

		if ( AttoServerSoftware::isIIS() ) {
			Atto::setHeader( 'Refresh', '0;url=' . $url );
		}
		else {
			if ( php_sapi_name() != 'cgi-fcgi' ) {
				self::setResponseCode( $response_code );
			}
			self::setHeader( 'Location', $url );
		}
		if ( $is_exit ) {
			exit();
		}
	}

	/**
	 * setHeader
	 * 
	 * @method setHeader
	 * @param {string} $head
	 * @param {string} $value 
	 */
	static public function setHeader( $head, $value, $replace = true ) {
		if ( self::$_headers !== null ) {
			self::$_headers = array( );
		}
		if ( $replace || !array_key_exists( self::$_headers, $head ) ) {
			self::$_headers[$head] = array( 'value'   => $value, 'replace' => $replace );
		}
	}

	/**
	 * setResponseCode
	 * 
	 * @method setResponseCode
	 * @param {int} $response_code 
	 */
	static public function setResponseCode( $response_code ) {
		if ( self::$_headers !== null ) {
			self::$_headers = array( );
		}
		$text = self::getTextByResponseCode( $response_code );

		$protocol = $_SERVER['SERVER_PROTOCOL'];
		if ( 'HTTP/1.1' != $protocol && 'HTTP/1.0' != $protocol ) {
			$protocol = 'HTTP/1.1';
		}
		$value = $protocol . ' ' . $response_code . ' ' . $text;
		self::$_headers[] = array( 'value'		 => $value, 'response_code' => $response_code );
	}

	/**
	 * setContentType
	 * 
	 * @method setContentType
	 * @param {string} $typealue defaulet is text/html
	 * @param {string} $charset defaulet is utf-8
	 */
	static public function setContentType( $type = 'text/html', $charset = 'utf-8' ) {
		if ( strpos( $type, '/' ) === false ) {
			$byext = AttoMimeType::getByExtension( $type );
			$type = $byext ? $byext : $type;
		}
		self::setHeader( 'Content-Type', $type . ($charset ? ';charset=' . $charset : '') );
	}

	/**
	 * getUserAgent
	 * 
	 * @method getUserAgent
	 * @return {string} 
	 */
	static public function getUserAgent() {
		return $_SERVER['HTTP_USER_AGENT'];
	}

	/**
	 * getAcceptLanguage
	 * 
	 * @method getAcceptLanguage
	 * @return {string} 
	 */
	static public function getAcceptLanguage() {
		return $_SERVER['HTTP_ACCEPT_LANGUAGE']; //ja,en-us;q=0.7,en;q=0.3
	}

	/**
	 * getCookie
	 * 
	 * @method getCookie
	 * @param {boolean} $parse
	 * @return {stdClass or false or string} 
	 */
	static public function getCookie( $parse = true ) {
		return $parse ? http_parse_cookie( $_SERVER['HTTP_COOKIE'] ) : $_SERVER['HTTP_COOKIE'];
	}

	/**
	 * getTextByResponseCode
	 *
	 * @method getTextByResponseCode
	 * @param {int} $response_code
	 * @return {string} response code text
	 */
	static public function getTextByResponseCode( $response_code ) {
		static $headers = null;
		if ( $headers === null ) {
			$headers = array(
				'100' => 'Continue',
				'101' => 'Switching Protocols',
				'102' => 'Processing',
				'200' => 'OK',
				'201' => 'Created',
				'202' => 'Accepted',
				'203' => 'Non-Authoritative Information',
				'204' => 'No Content',
				'205' => 'Reset Content',
				'206' => 'Partial Content',
				'207' => 'Multi-Status',
				'226' => 'IM Used',
				'300' => 'Multiple Choices',
				'301' => 'Moved Permanently',
				'302' => 'Found',
				'303' => 'See Other',
				'304' => 'Not Modified',
				'305' => 'Use Proxy',
				'306' => '(Unused)',
				'307' => 'Temporary Redirect',
				'400' => 'Bad Request',
				'401' => 'Unauthorized',
				'402' => 'Payment Required',
				'403' => 'Forbidden',
				'404' => 'Not Found',
				'405' => 'Method Not Allowed',
				'406' => 'Not Acceptable',
				'407' => 'Proxy Authentication Required',
				'408' => 'Request Timeout',
				'409' => 'Conflict',
				'410' => 'Gone',
				'411' => 'Length Required',
				'412' => 'Precondition Failed',
				'413' => 'Request Entity Too Large',
				'414' => 'Request-URI Too Long',
				'415' => 'Unsupported Media Type',
				'416' => 'Requested Range Not Satisfiable',
				'417' => 'Expectation Failed',
				'418' => 'I\'m a teapot',
				'422' => 'Unprocessable Entity',
				'423' => 'Locked',
				'424' => 'Failed Dependency',
				'426' => 'Upgrade Required',
				'500' => 'Internal Server Error',
				'501' => 'Not Implemented',
				'502' => 'Bad Gateway',
				'503' => 'Service Unavailable',
				'504' => 'Gateway Timeout',
				'505' => 'HTTP Version Not Supported',
				'506' => 'Variant Also Negotiates',
				'507' => 'Insufficient Storage',
				'509' => 'Bandwidth Limit Exceeded',
				'510' => 'Not Extended',
			);
		}
		if ( isset( $headers[$response_code] ) ) {
			return $headers[$response_code];
		}
		return 'unknown response code';
	}

	/**
	 * outputHeaders
	 * 
	 * @method outputHeaders
	 */
	static public function outputHeaders() {
		if ( !headers_sent() && is_array( self::$_headers ) ) {
			foreach ( self::$_headers as $key => $arr ) {
				if ( isset( $arr['response_code'] ) ) {
					header( no_null_string( $arr['value'], true, $arr['response_code'] ) );
				}
				else {
					header( no_null_string( $key . ': ' . $arr['value'], $arr['replace'] ) );
				}
			}
		}
	}

}

