<?php

/**
 * Option
 *
 * @class 
 */
abstract class Option {

	/**
	 * 引数によりsomeかnone(toへの引数がnull時)を返す
	 *
	 * @method to
	 * @param {mixed} $val
	 * @return {option} Some or None
	 */
	static function to( $val ) {
		if ( $val == null ) {
			return None::to( $val );	
	}
		else {
			return Some::to( $val );
		}
	}

	/**
	 * 実体がsomeならば値を返し、noneならばExceptionをthrowする
	 *
	 * @method get
	 * @return {mixed}
	 */
	abstract function get();

	/**
	 * 実体がSomeならばtrue、Noneならばfalseを返す
	 *
	 * @method isDefined
	 * @return {boolean}
	 */
	abstract function isDefined();

	/**
	 * 実体がsomeならばfalse、noneならばtrueを返す
	 *
	 * @method isEmpty
	 * @return {boolean}
	 */
	abstract function isEmpty();

	/**
	 * 実体がSomeならば$callbackを実行、Noneならばなにもしない
	 *
	 * @method foreach
	 * @param {callable} $callback
	 */
	abstract function each( $callback );

	abstract function toString();

	/**
	 * 実体がSomeならばgetと同等、Noneならば$valを返す
	 *
	 * @method getOrElse
	 * @param {mixed} $val
	 * @return {mixed}
	 */
	abstract function getOrElse( $val );

	/**
	 * 実体がSomeならばgetと同等、Noneならばnullを返す
	 *
	 * @method getOrNull
	 * @return {mixed}
	 */
	abstract function getOrNull();

	/**
	 * 実体がSomeならばthisを返し、Noneならば$valを返す
	 *
	 * @method orElse
	 * @return {mixed}
	 */
	abstract function orElse( $val );

	/**
	 * 実体がSomeならば$callbackに値を渡した戻り値をOptionに入れて返し、NoneならばNoneを返す
	 *
	 * @method map
	 * @return {mixed}
	 */
	abstract function map( $callback );
}

/**
 * Some
 *
 * @class 
 */
class Some extends Option {

	private $val;

	private function Some(){
		$args = func_get_args();
		return call_user_func_array( array( &$this, '__construct' ), $args );
	}

	private function __construct( $val ) {
		$this->val = $val;
	}

	static function to( $val ) {
		if ( $val == null ) {
			return None::to( $val );
		}
		else {
			return new Some( $val );
		}
	}

	function get() {
		return $this->val;
	}

	function isDefined() {
		return true;
	}

	function isEmpty() {
		return false;
	}

	function each( $callback ) {
		call_user_func( $callback, $this->val );
	}

	function toString() {
		return 'Some[' . $this->val . ']';
	}

	function getOrElse( $val ) {
		return $this->get();
	}

	function getOrNull() {
		return $this->get();
	}

	function orElse( $val ) {
		return $this;
	}

	function map( $callback ) {
		return Option::to( call_user_func( $callback, $this->val ) );
	}

}

/**
 * None
 *
 * @class None
 */
class None extends Option {

	private function None(){
		$args = func_get_args();
		return call_user_func_array( array( &$this, '__construct' ), $args );
	}

	private function __construct() {
		
	}

	static function to( $val ) {
		if ( $val == null ) {
			static $that = null;
			if ( $that === null ) {
				$that = new None();
			}
			return $that;
		}
		else {
			return Some::to( $val );
		}
	}

	function get() {
		throw new Excption( 'NoSuchElement' );
	}

	function isDefined() {
		return false;
	}

	function isEmpty() {
		return true;
	}

	function each( $callback ) {
		
	}

	function toString() {
		return 'None';
	}

	function getOrElse( $val ) {
		return $val;
	}

	function getOrNull() {
		return null;
	}

	function orElse( $val ) {
		return $val;
	}

	function map( $callback ) {
		return $this;
	}

}
