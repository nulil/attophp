<?php

/**
 * AttoControllerDispatcher
 * 
 * controller dispatcher
 * コントローラーディスパッチャ
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
class AttoControllerDispatcher {

	/**
	 * @var {array}  
	 */
	protected $_map;

	/**
	 * @var {array} 
	 */
	protected $_options = array(
		'controller_prefix'	  => '',
		'controller_suffix'	  => 'Controller',
		'action_prefix'		  => 'requestBy_',
		'action_suffix'		  => '',
		'convert_for_controller' => array( 'function' => 'snake_case_to_camel_case', 'args'	 => array( true ) ),
		'convert_for_action' => array( 'function' => 'snake_case_to_camel_case', 'args'	 => array( ) ),
	);

	/**
	 * __construct
	 * 
	 * インスタンス化の際にマッピング情報を渡してください。
	 * key値が文字列の場合はkey値、
	 * key値が文字列以外の場合でvalue値がarrayの場合はvalue['pattern']、
	 * key値が文字列以外の場合でvalue値がarrayの以外場合はvalue値を正規表現として、
	 * $pathinfoを対象にpreg_matchを実行し結果を得て
	 * その結果とvalue値をマージしたarrayの'controller'をコントローラ、'action'をメソッドの名称のベースにします。
	 * 
	 * 
	 * 標準の値
	 * map
	 * 	array(
	 * 		'/^(?:index)?(?:\?.*)?$/u' => array( 'controller' => 'Root' ),
	 * 		'#^(?P<controller>[^/]+)/(?:\?.*)?$#u',
	 * 		'#^(?P<controller>[^/]+)/(?P<action>[^/?]+)#u',
	 * 	);
	 * 
	 * options
	 * 	array(
	 * 		'controller_prefix' => '',
	 * 		'controller_suffix' => 'Controller',
	 * 		'action_prefix'	 => 'requestBy_',
	 * 		'action_suffix'	 => '',
	 * 		'convert_for_controller' => array( 'function' => 'snake_case_to_camel_case', 'args'	 => array( true ) ),
	 * 		'convert_for_action' => array( 'function' => 'snake_case_to_camel_case', 'args'	 => array( ) ),
	 * 	);
	 * 
	 * @param {array} $map
	 * @param {array} $options 
	 */
	public function __construct( array $map = null, array $options = array( ) ) {
		if ( $map == null ) {
			$map = array(
				'/^(?:index)?(?:\?.*)?$/u' => array( 'controller' => 'Root' ),
				'#^(?P<controller>[^/]+)/(?:\?.*)?$#u',
				'#^(?P<controller>[^/]+)/(?P<action>[^/?]+)#u',
			);
		}
		else if ( !is_array( $map ) ) {
			$map = array( $map );
		}

		$this->_map = $map;
		$this->_options = array_merge( $this->_options, $options );
	}

	/**
	 * dispatch
	 * 
	 * @throws HttpRequestException 
	 */
	public function dispatch( $uri ) {

		$def = array(
			'controller'			 => 'root',
			'action'				 => 'index',
			'controller_prefix'	  => '',
			'controller_suffix'	  => '',
			'action_prefix'		  => '',
			'convert_for_controller' => array( 'function' => null, 'args'	 => array( ) ),
			'convert_for_action' => array( 'function' => null, 'args'	 => array( ) ),
		);

		// マッピング
		$controller = null;
		foreach ( $this->_map as $key => $value ) {
			if ( is_string( $key ) ) {
				$pattern = $key;
			}
			else {
				if ( is_array( $value ) ) {
					$pattern = $value['pattern'];
				}
				else {
					$pattern = $value;
				}
			}

			if ( 0 < preg_match( $key, $uri, $matchs ) ) {
				// マッチ
				if ( is_array( $value ) ) {
					$arr = array_merge( $def, $matchs, $value );
				}
				else {
					$arr = array_merge( $def, $matchs );
				}

				$convert_for_controller = array_merge( $arr['convert_for_controller'], $this->_options['convert_for_controller'] );
				$convert_for_action = array_merge( $arr['convert_for_action'], $this->_options['convert_for_action'] );

				$controller_prefix = $arr['controller_prefix'] ? $arr['controller_prefix'] : $this->_options['controller_prefix'];
				$controller_suffix = $arr['controller_suffix'] ? $arr['controller_suffix'] : $this->_options['controller_suffix'];
				$action_prefix = $arr['action_prefix'] ? $arr['action_prefix'] : $this->_options['action_prefix'];
				$action_suffix = $arr['action_suffix'] ? $arr['action_suffix'] : $this->_options['action_suffix'];

				// controller name
				$controller = $controller_prefix . $arr['controller'] . $controller_suffix;
				if ( is_callable( $convert_for_controller['function'] ) ) {
					$controller = call_user_func_array( $convert_for_controller['function'], array_merge( array( $controller ), $convert_for_controller['args'] ) );
				}

				// action name
				$action = $action_prefix . $arr['action'] . $action_suffix;
				if ( is_callable( $convert_for_action['function'] ) ) {
					$action = call_user_func_array( $convert_for_action['function'], array_merge( array( $action ), $convert_for_action['args'] ) );
				}
				break;
			}
		}

		if ( is_null( $controller ) ) {
			throw new AttoNotFoundHtmlRenderException( 'Has not been mapped' );
		}

		if ( !class_exists( $controller ) ) {
			throw new AttoNotFoundHtmlRenderException( 'Controller cannot be found' );
		}

		$fn = array( new $class, $action );
		if ( !is_callable( $fn ) ) {
			throw new AttoNotFoundHtmlRenderException( 'Action cannot be found' );
		}
		call_user_func( $fn );
	}

}