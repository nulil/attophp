<?php

/**
 * Seq
 *
 * @class
 */
class Seq implements Iterator {

	// ***** private ver *****

	private $value = null;
	private $next_seq = null;
	private $current = null;
	private $idx = 0;
	private $applied = null;

	// ***** public function *****

	public function Seq( $value /* , $vals... */ ){
		$args = func_get_args();
		return call_user_func_array( array( &$this, '__construct' ), $args );
	}

	/**
	 * __construct
	 *
	 * @constructor
	 * @param mixed $value...
	 */
	public function __construct( $value /* , $vals... */ ) {
		$this->current = $this;

		if ( 0 < func_num_args() ) {
			$this->value = $value;
			$this->next_seq = call_user_func_array( array( self, '_create' ), array_slice( func_get_args(), 1 ) );
		}
	}

	/**
	 * isNil
	 *
	 * @method isNil
	 * @return boolean value is nil
	 */
	public function isNil() {
		if ( $this->_getValue() !== null || $this->_getNext() !== null ) {
			return false;
		}
		return true;
	}

	/**
	 * toArray
	 *
	 * @method toArray
	 * @return array seq to array
	 */
	public function toArray() {
		if ( $this->isNil() ) {
			return array( );
		}
		return array_merge( array( $this->_getValue() ), $this->rest()->toArray() );
	}

	/**
	 * first
	 *
	 * @method first
	 * @return mixed value of the first
	 */
	public function first() {
		if ( is_callable( $this->_getValue() ) ) {
			$val = call_user_func_array( $this->_getValue(), $this->rest()->toArray() );
		}
		elseif ( is_array( $this->_getValue() ) && !$this->_getNext()->isNil() ) {
			$val = $this->_getValue();
			$val = $val[$this->_getNext()->first()];
			$this->next_seq = $this->_getNext()->_getNext();
			$val = $this->first();
		}
		if ( isset( $val ) ) {// && $val instanceof Seq) {
			$this->applied = $val;
		}
		return $this->_getValue();
	}

	/**
	 * second
	 *
	 * @method second
	 * @return mixed value of the second
	 */
	public function second() {
		$this->first();
		if ( $this->_getNext()->isNil() ) {
			return self::nil();
		}
		return $this->_getNext()->first();
	}

	/**
	 * rest
	 *
	 * @method rest
	 * @return Seq 
	 */
	public function rest() {
		//if ($this->get_next()->is_nil()){
		//	return self::nil();
		//}
		return $this->_getNext();
	}

	/**
	 * map
	 *
	 * @method map
	 * @param callable $callback
	 * @return Seq $callback is applied to the seq
	 */
	public function map( $callback ) {
		return $this->isNil() ? self::nil() : new self( call_user_func( $callback, $this->first() ), $this->rest->map( $callback ) );
	}

	/**
	 * flatMap
	 *
	 * @method flatMap
	 * @param callable $callback
	 * @return Seq $callback is applied to the seq, and to flat
	 */
	public function flatMap( $callback ) {
		return $this->isNil() ? self::nil() : $this->map( $callback )->flatten();
	}

	/**
	 * flatten
	 *
	 * @method flatten
	 * @return Seq the flat seq
	 */
	public function flatten() {
		if ( !$this->isNil() ) {
			$that = $this;
			$val = $that->first();
			if ( $val instanceof Seq ) {
				$that = $val->flaten();
			}
			return self::cons( $that, $this->rest()->flaten() );
		}
		else {
			return self::nil();
		}
	}

	/**
	 * length
	 *
	 * @method length
	 * @return int length
	 */
	public function length() {
		if ( !$this->isNil() ) {
			return 1 + $this->rest()->length();
		}
		else {
			return 0;
		}
	}

	// ▽  Iterator method  ▽
	/**
	 * current
	 * 現在の値を得る
	 *
	 * @method current
	 * @return mixed
	 */
	public function current() {
		$this->current->first();
	}

	/**
	 * key
	 * 現在のキー値を得る
	 *
	 * @method key
	 * @return string
	 */
	public function key() {
		return $this->idx;
	}

	/**
	 * next
	 * 次の要素へ移動する。
	 *
	 * @method next
	 */
	public function next() {
		$this->current = $this->current->rest();
		$this->idx++;
	}

	/**
	 * valid
	 * まだ要素があるかどうか。
	 *
	 * @method valid
	 * @return bool
	 */
	public function valid() {
		return (($this->_getNext() instanceof Seq) && !$this->_getNext()->isNil());
	}

	/**
	 * rewind
	 * 先頭に戻す
	 *
	 * @method rewind
	 */
	public function rewind() {
		$this->current = $this;
		$this->idx = 0;
	}

	// △  Iterator method  △
	// ***** private function *****

	private function _getValue() {
		if ( $this->applied !== null ) {
			return $this->applied->_getValue();
		}
		return $this->value;
	}

	private function _getNext() {
		return $this->next_seq;
	}

	// ***** static public function *****

	/**
	 * nil
	 *
	 * @method nil
	 * @return Seq nil
	 */
	static public function nil() {
		static $val = null;
		if ( $val === null ) {
			$val = new self();
		}
		return $val;
	}

	/**
	 * cons
	 *
	 * @method cons
	 * @param mixed $val...
	 * @return Seq a concatenation of all the arguments, the seq. or nil
	 */
	static public function cons( $val /* , $vals... */ ) {
		if ( func_num_args() < 1 ) {
			return self::nil();
		}

		if ( $val instanceof Seq ) {
			if ( $val->isNil() ) {
				return call_user_func_array( array( self, __FUNCTION__ ), array_slice( func_get_args(), 1 ) ); //再帰
			}
			else {
				$args = func_get_args();
				$val = $val->_getValue();
				$args[0] = $val->rest();
			}
		}
		else {
			$args = array_slice( func_get_args(), 1 );
		}
		return call_user_func(
						array( self, '_create' ), $val, call_user_func_array( array( self, __FUNCTION__ ), $args ) ); //再帰
	}

	// ***** static private function *****

	/**
	 * _create
	 *
	 * @method _create
	 * @param mixed $val...
	 * @return Seq new seq object or nil
	 */
	static private function _create( $val /* , $vals... */ ) {
		if ( 0 < func_num_args() ) {
			if ( $val instanceof Seq && $val->isNil() ) {
				return call_user_func_array( array( self, __FUNCTION__ ), array_slice( func_get_args(), 1 ) );
			}
			return new self( $val, call_user_func_array( array( self, __FUNCTION__ ), array_slice( func_get_args(), 1 ) ) );
		}
		else {
			return self::nil();
		}
	}

}
