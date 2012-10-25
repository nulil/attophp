<?php

/**
 * AttoDbo__ForLegacy
 * 
 * wrapper of db-api (mysql and postgresql)
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
class AttoDbo__ForLegacy implements AttoDbo__IConnection {

	private $_con;

	public function __construct( array $params ) {
		$params['type'] = strtolower( $params['type'] );
		switch ( $params['type'] ) {
			case 'mysql':
			case 'pgsql':
				break;
			case 'postgresql':
				$params['type'] = 'pgsql';
				break;
			default:
				throw OutOfBoundsException( '想定外のDBです' );
		}

		$class = 'AttoDbo__Connection' . ucfirst( $params['type'] );
		if ( !class_exists( $class, false ) ) {
			require Atto::dir_atto_autoLoad() . 'atto-dao' . DS . 'connection-' . $params['type'] . '.php';
		}

		$this->_con = new $class( $params );
	}

	/**
	 * @return \AttoDbo__IConnection 
	 */
	public function getOriginalConnection() {
		return $this->_con;
	}

	/**
	 * @return boolean 
	 */
	public function beginTransaction() {
		return $this->_con->beginTransaction();
	}

	/**
	 * @return boolean 
	 */
	public function commit() {
		return $this->_con->commit();
	}

	/**
	 * @return int 
	 */
	public function errorCode() {
		return $this->_con->errorCode();
	}

	/**
	 * @return array
	 */
	public function errorInfo() {
		return $this->_con->errorInfo();
	}

	/**
	 * @return string 
	 */
	public function errorMessage() {
		return $this->_con->errorMessage();
	}

	/**
	 * @param string $sql 
	 * @return int 
	 */
	public function exec( $sql ) {
		return $this->_con->exec( $sql );
	}

	/**
	 * @return boolean 
	 */
	public function inTransaction() {
		return $this->_con->inTransaction();
	}

	/**
	 * @return int 
	 */
	public function lastInsertId() {
		return $this->_con->lastInsertId();
	}

	/**
	 * @param string $sql 
	 * @return \AttoDbo__IStatement 
	 */
	public function prepare( $sql ) {
		return $this->_con->prepare( $sql );
	}

	public function query( $sql ) {
		$this->_con->query( $sql );
	}

	/**
	 * @param string $string
	 * @param int $parameter_type 
	 * @return string
	 */
	public function quote( $string, $parameter_type = self::PARAM_STR ) {
		return $this->_con->quote( $string, $parameter_type );
	}

	/**
	 * @return boolean 
	 */
	public function rollBack() {
		$this->_con->rollBack();
	}

	public function __destruct() {
		unset( $this->_con );
	}

}
