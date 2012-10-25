<?php

require_once dirname( __FILE__ ) . DS . 'statement-mysql.php';

/**
 * AttoDbo__ConnectionMysql
 * 
 * wrapper of mysql functions
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
class AttoDbo__ConnectionMysql implements AttoDbo__IConnection {

	const BASE = 'mysql functions';

	private $_con;

	public function __construct( array $params ) {
		$def_params = array(
			'host'	 => null,
			'port'	 => null,
			'db'	   => null,
			'user'	 => '',
			'password' => '',
		);

		$params = array_merge( $def_params, $params );

		$con = mysql_connect( $params['host'] . (isset( $params['port'] ) ? ':' . $params['port'] : ''), $params['user'],
												  $params['password'] ) or null;
		// データベースを選択
		if ( $con ) {
			mysql_select_db( $params['db'], $con ) or ($con = null);
		}
		if ( is_null( $con ) ) {
			throw new AttoDbo__Exception( 'DBへの接続に失敗しました' );
		}
		$this->_con = $con;
	}

	public function getOriginalConnection() {
		return $this->_con;
	}

	/**
	 *
	 * @return boolean 
	 */
	public function beginTransaction() {
		$this->is_begin = true;
		mysql_query( 'BEGIN', $this->_con );
		return true; // TODO
	}

	/**
	 *
	 * @return boolean 
	 */
	public function commit() {
		$this->is_begin = false;
		mysql_query( 'COMMIT', $this->_con );
		return true; // TODO
	}

	/**
	 *
	 * @return int 
	 */
	public function errorCode() {
		return mysql_errno( $this->_con ); // @todo
	}

	/**
	 *
	 * @return array 
	 */
	public function errorInfo() {
		return array( $this->errorCode(), mysql_errno( $this->_con ), $this->errorMessage() ); // @todo
	}

	/**
	 *
	 * @return string 
	 */
	public function errorMessage() {
		return mysql_error( $this->_con );
	}

	/**
	 *
	 * @param string $statement
	 * @return int 
	 */
	public function exec( $statement ) {
		mysql_query( $statement, $this->_con );
		return mysql_affected_rows( $this->_con );
	}

	/**
	 *
	 * @return boolean 
	 */
	public function inTransaction() {
		return $this->is_begin;
	}

	/**
	 *
	 * @return int 
	 */
	public function lastInsertId() {//$name = null ) {
		return mysql_insert_id( $this->_con );
	}

	/**
	 *
	 * @param string $statement
	 * @return \AttoDbo__StatementMysql 
	 */
	public function prepare( $statement ) {//, array $driver_options = array( ) ) {
		return new AttoDbo__StatementMysql( $this->_con, $this, $statement );
	}

	/**
	 *
	 * @param string $statement
	 * @return \AttoDbo__StatementMysql 
	 */
	public function query( $statement ) {
		return new AttoDbo__StatementMysql( mysql_query( $statement, $this->_con ) );
	}

	/**
	 * @param string $string
	 * @param int $parameter_type
	 * @return string 
	 */
	public function quote( $string, $parameter_type = AttoDbo__IConnection::PARAM_STR ) {
		if ( $parameter_type === AttoDbo__IConnection::PARAM_NULL ) {
			return 'NULL';
		}
		$upper = trim( strtoupper( $string ) );
		if ( $parameter_type === AttoDbo__IConnection::PARAM_BOOL && ($upper == 'TRUE' || $upper == 'FALSE') ) {
			return $upper;
		}
		if ( $parameter_type !== AttoDbo__IConnection::PARAM_STR && (is_int( $string ) || is_numeric( $string )) ) {
			return $upper;
		}
		return '\'' . mysql_real_escape_string( $string, $this->_con ) . '\'';
	}

	/**
	 *
	 * @return boolean 
	 */
	public function rollBack() {
		$this->is_begin = false;
		mysql_query( 'ROLLBACK', $this->_con );
		return true; // TODO
	}

	public function __destruct() {
		if ( $this->inTransaction() ) {
			$this->rollBack();
		}
		mysql_close( $this->_con );
		unset( $this->_con );
	}

	//
	// ******************************************************************************************************
	// *************							not supporteds									*************
	// ******************************************************************************************************

	/**
	 * not supported
	 * 
	 * @param type $attribute 
	 */
	public function getAttribute( $attribute ) {
		trigger_error( 'not supported', E_USER_WARNING );
	}

	/**
	 * not supported
	 * 
	 * @param type $attribute
	 * @param mixed $value 
	 */
	public function setAttribute( $attribute, $value ) {
		trigger_error( 'not supported', E_USER_WARNING );
	}

}