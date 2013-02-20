<?php

/**
 * AttoDinamic
 *
 * Dynamic object class
 * 動的オブジェクトクラス
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
class AttoDinamic extends ArrayObject {

	function __construct( $arg = null ) {
		if ( func_num_args() !== 0 ) {
			if ( func_num_args() !== 1 ) {
				$arg = func_get_args();
			}
			elseif ( !is_array( $arg ) && !($arg instanceof ArrayObject) ) {
				$arg = array( $arg );
			}

			parent::__construct( $arg, ArrayObject::ARRAY_AS_PROPS );
		}
	}

	/**
	 * 未定義プロパティの設定用マジックメソッド
	 *
	 * @param {string} $name
	 * @param {mixed} $value
	 */
	public function __set( $name, $value ) {
		$this[ $name ] = $value;
	}

	/**
	 * 未定義プロパティの取得用マジックメソッド
	 *
	 * @param {string} $name
	 * @return {mixed}
	 */
	public function __get( $name ) {
		if ( !$this->keyExists( $name ) ) {
			return null;
		}
		return $this[ $name ];
	}

	/**
	 * map
	 * array_mapの引数$arr1を$thisで実行
	 *
	 * @method map
	 * @param {callable} $callback
	 * @param {array} $arr2...
	 * @return {self}
	 */
	public function map( $callback ) {
		static $array_object_to_array = array( self, 'arrayObjectToArray' );
		if ( 1 < func_num_args() ) {
			$args = array_map( $array_object_to_array, array_slice( func_get_args(), 1 ) );
			array_unshift( $args, (array) $this );
		}
		else {
			$args = array( (array) $this );
		}
		return new self( call_user_func_array( $callback, $args ) );
	}

	/**
	 * keyExists
	 * array_key_existsの引数$searchを$thisで実行
	 *
	 * @method keyExists
	 * @param {mixed} $key
	 * @return {boolean}
	 */
	public function keyExists( $key ) {
		return array_key_exists( $key, (array) $this );
	}

	/**
	 * search
	 * array_searchの引数$haystackを$thisで実行
	 *
	 * @method search
	 * @param {mixed} $needle
	 * @param {boolean} $strict
	 * @return {mixed}
	 */
	public function search( $needle, $strict = false ) {
		return array_search( $needle, (array) $this, $strict );
	}

	/**
	 * 未設定メソッドが呼び出された場合にコールされるマジックメソッド
	 * array_○○関数の呼び出しに変換して、第一引数に$thisを設定しています
	 * 引数にArrayObjectが指定された場合、arrayにキャストして関数を呼び出します
	 *
	 * @method __call
	 * @param $fname 
	 * @param $args
	 * @return {mixed}
	 */
	public function __call( $fname, $args ) {
		static $func = array(
	'arsort'  => 'arsort',
	'krsort'  => 'krsort',
	'rsort'   => 'rsort',
	'sort'	=> 'sort',
	'usort'   => 'usort',
	'shuffle' => 'shuffle'
		);

		static $array_object_to_array = array( self, 'arrayObjectToArray' );

		if ( isset( $func[ $fname ] ) ) {
			$exists = true;
		}
		else {
			$fn = 'array_' . $fname;
			if ( function_exists( $fn ) ) {
				$func[ $fname ] = $fn;
				$exists	   = true;
			}
			else {
				$exists = false;
			}
		}
		if ( $exists ) {
			$args = array_map( $array_object_to_array, $args );
			$arr  = call_user_func_array( $func[ $fname ], array_merge( array( (array) $this ), $args ) );
			if ( is_array( $arr ) ) {
				return new self( $arr );
			}
			else {
				return $arr;
			}
		}
	}

	/**
	 * arrayObjectToArray
	 * 
	 * $valueがArrayObjectを継承していたら、arrayにキャストして返す
	 * $valueがArrayだったら、そのまま返す
	 * それ以外の場合は$ifNoArrayReturnを返す
	 *
	 * @method arrayObjectToArray
	 * @param {mixed} $value
	 * @param {mixed} $default
	 * @return {mixed}
	 */
	static public function arrayObjectToArray( $value, $default = null ) {
		if ( $value instanceof ArrayObject ) {
			return (array) $value;
		}
		else if ( is_array( $value ) ) {
			return $value;
		}
		else {
			return $default;
		}
	}

}
