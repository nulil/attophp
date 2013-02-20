<?php

/**
 * AttoLazy
 *
 * delayed execution
 * 遅延実行のためのクラス
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
class AttoLazy {

	private $_value = null;
	private $_is_called = false;
	private $_callback = null;
	private $_args = null;

	/**
	 * __construct
	 *
	 * @constructor
	 * @param {callable} $callback
	 * @param {mixed} $arg...
	 */
	public function __construct( $callback /* , $arg... */ ) {
		$this->_callback = $callback;
		if ( 1 < func_num_args() ) {
			$this->_args = array_slice( func_get_args(), 1 );
		}
		else {
			$this->_args = array( );
		}
	}

	/**
	 * 最初に実行したとき、$callbackを実行した結果を返す
	 * 2度目以降は、最初の実行結果を返す
	 * 
	 * @return {mixed} 
	 */
	public function get() {
		if ( !$this->_is_called ) {
			if ( is_callable( $this->_callback ) ) {
				$val = call_user_func_array( $this->_callback, $this->_args );
			}
			else {
				$val = $this->_callback;
			}
		}
		else {
			$val = $this->_value;
		}

		while ( $val instanceof AttoLazy ) {
			$val = $val->get();
		}
		$this->_value = $val;
		$this->_is_called = true;

		return $this->_value;
	}

	public function __invoke() {
		return get();
	}

}
