<?php

/**
 * AttoRequestHelper
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
class AttoRequestHelper {
	
	/**
	 * request
	 * 
	 * @method request
	 * @param string $method
	 * @param string $url
	 * @param array $params
	 * @param mixed $content
	 * @return array  header, body
	 */
	static public function request( $method, $url, $content = null, array $params = array( ) ){
		if ($method == 'GET'){
			return self::get( $url, $content, $params );
		}else{
			return self::post( $url, $content, $params );
		}
	}

	/**
	 * get
	 * 
	 * @method get
	 * @param string $url
	 * @param array $params
	 * @param mixed $content
	 * @return array header, body
	 */
	static public function get( $url, $content = null, array $params = array( ) ){
		if ( $content ){
			if (is_string($content)){
				$get = $url . '?' . $content;
			}else{
				$get = $url . '?' . http_build_query($content);
			}
		}else{
			$get = $url;
		}
		
		$context = stream_context_create( array( 'http' => $params ) );
		$body = file_get_contents( $get, false, $context );
		return array( $http_response_header, $body );
	}
	
	/**
	 * post
	 * 
	 * $contentには、http_build_query関数の結果か、makeMultipartContentメソッドで解釈できるarrayを渡すこと
	 * 
	 * array( 'name' => 'content' )
	 * array( 'name' => '@filepath' )
	 * array( 'name' => array( 'file' => 'filepath' [, 'name' => 'filename'][, 'type' => 'Content-Type value'][, 'encoding' => 'Content-Transfer-Encoding value'] ) )
	 * array( 'name' => array( 'content' => 'content' [, 'name' => 'contentname'][, 'type' => 'Content-Type value'][, 'encoding' => 'Content-Transfer-Encoding value'] ) )
	 * 
	 * @method post
	 * @param string $url
	 * @param mixed $content	string(http query) or array(multi part)
	 * @param array $params
	 * @return array header, body
	 */
	static public function post( $url, $content = null, array $params = array( ) ) {
		$params['method'] = 'POST';
		$params['user_agent'] = 'atto/' . Atto::VERSION . ' \\AttoRequestHelper#post';

		// content
		if ( is_array( $content ) ) {
			$boundary = '---------------------' . substr( md5( rand( 0, 32000 ) ), 0, 10 );
			$content = self::_makeMultipartContent( $content, $boundary );
		}
		if ( $content ) {
			$params['content'] = $content;
		}

		// header
		if ( isset( $params['header'] ) ) {
			if ( is_string( $params['header'] ) ) {
				$params['header'] = explode( "\r\n", $params['header'] );
			}
			$headers_kv = array( );
			foreach ( $params['header'] as $key => $value ) {
				if ( is_int( $key ) ) {
					list($k, $v) = explode( ':', $value, 2 );
					if ( trim( $k ) && trim( $v ) ) {
						$headers_kv[trim( $k )] = trim( $v );
					}
					else {
						$headers_kv[] = $value;
					}
				}
				else {
					$headers_kv[$key] = $value;
				}
			}
			if ( isset( $boundary ) ) {
				unset( $headers_kv['Content-Type'] );
				$headers = array( "Content-Type: multipart/form-data; boundary={$boundary}" );
			}
			foreach ( $headers_kv as $key => $value ) {
				if ( is_int( $key ) ) {
					$headers[] = $value;
				}
				else {
					$headers[] = $key . ': ' . $value;
				}
			}
			$params['header'] = implode( "\r\n", $headers );
		}
		else {
			$params['header'] = isset( $boundary ) ? "Content-Type: multipart/form-data; boundary={$boundary}" : '';
		}


		$context = stream_context_create( array( 'http' => $params ) );
		$body = file_get_contents( $url, false, $context );
		return array( $http_response_header, $body );
	}

	/**
	 * _makeMultipartContent
	 * 
	 * @method _makeMultipartContent
	 * @param array $contents
	 * @param string $boundary
	 * @return string 
	 */
	static private function _makeMultipartContent( array $contents, $boundary ) {
		$rows = array( '--' . $boundary );

		foreach ( $contents as $key => $value ) {
			$rows[] = 'Content-Disposition: form-data; name="' . $key . '"';

			if ( (is_array( $value ) && isset( $value['file'] ))
					|| (0 === strpos( $value, '@' ) && file_exists( $file = substr( $value, 1 ) )) ) {

				$type = $encoding = $content = $name = null;
				if ( is_array( $value ) ) {
					if ( isset( $value['file'] ) ) {
						$file = $value['file'];
					}
					else {
						$file = null;
					}
					if ( isset( $value['name'] ) ) {
						$name = $value['name'];
					}
					if ( isset( $value['content'] ) ) {
						$content = $value['content'];
					}
					if ( isset( $value['type'] ) ) {
						$type = $value['type'];
					}
					if ( isset( $value['encoding'] ) ) {
						$encoding = $value['encoding'];
					}
				}
				if ( !$type && $file ) {
					$type = AttoMimeType::getByFileName( $file );
				}
				if ( !$encoding && $type ) {
					if ( !starts_with( 'text', $type )
							&& !ends_with( 'xml', $type )
							&& !ends_with( 'json', $type )
							&& !ends_with( 'script', $type ) ) {
						$encoding = 'binary';
					}
				}
				if ( !$name && $file ) {
					$name = basename( $file );
				}

				if ( $name ) {
					$rows[count( $rows ) - 1] .= '; filename=' . $name;
				}
				if ( $type ) {
					$rows[] = 'Content-Type: ' . $type;
				}
				if ( $encoding ) {
					$rows[] = 'Content-Transfer-Encoding: ' . $encoding;
				}

				if ( $file ) {
					$value = file_get_contents( $file );
				}
				else {
					$value = $content;
				}
			}
			$rows[] = '';
			$rows[] = $value;
			$rows[] = '--' . $boundary;
		}
		$rows[] = '';
		return implode( "\r\n", $rows );
	}

}
