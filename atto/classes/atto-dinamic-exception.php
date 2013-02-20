<?php

/**
 * AttoDinamicException
 *
 * Dynamic exception class
 * 動的例外クラス
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
class AttoDinamicException extends Exception {

	private $_dinamic; // atto_dinamic;

	public function __construct( $message = '', $code = 0, $previous = null ) {
		parent::__construct( $message, $code, $previous );
		$this->_dinamic = new AttoDinamic;
	}

	/**
	 * 未定義プロパティの設定用マジックメソッド
	 *
	 * @param {string} $name
	 * @param {mixed} $value
	 */
	public function __set( $name, $value ) {
		$this->_dinamic[$name] = $value;
	}

	/**
	 * 未定義プロパティの取得用マジックメソッド
	 *
	 * @param {string} $name
	 * @return {mixed}
	 */
	public function __get( $name ) {
		return $this->_dinamic[$name];
	}

	/**
	 * 未設定メソッドが呼び出された場合にコールされるマジックメソッド
	 *
	 * @method __call
	 * @param $fname 
	 * @param $args
	 * @return {mixed}
	 */
	public function __call( $fname, $args ) {
		return call_user_func_array( array( $this->_dinamic, $fname ), $args );
	}

}
