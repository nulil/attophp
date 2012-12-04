<?php

require_once dirname( __FILE__ ) . DS . 'statement-pgsql.php';

/**
 * AttoDbo__ConnectionPgsql
 * 
 * wrapper of pgsql functions
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
class AttoDbo__ConnectionPgsql implements AttoDbo__IConnection {

	const BASE = 'postgresql functions';

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

		$con = pg_connect(
				'host=' . $params['host']
				. (isset( $params['port'] ) ? ' port=' . $params['port'] : '')
				. ' dbname=' . $params['db']
				. ' user=' . $params['user']
				. ' password=' . $params['pass'], PGSQL_CONNECT_FORCE_NEW ) or null;
		// データベースを選択
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
		return !!pg_query( $this->_con, 'BEGIN' );
	}

	/**
	 *
	 * @return boolean 
	 */
	public function commit() {
		$this->is_begin = false;
		return !!pg_query( $this->_con, 'COMMIT' );
	}

	/**
	 *
	 * @return int 
	 */
	public function errorCode() {
		return 0; // @todo
	}

	/**
	 *
	 * @return array 
	 */
	public function errorInfo() {
		return array( $this->errorCode(), 0, $this->errorMessage() ); // @todo
	}

	/**
	 *
	 * @return string 
	 */
	public function errorMessage() {
		return pg_last_error( $this->_con );
	}

	/**
	 *
	 * @param string $statement
	 * @return int 
	 */
	public function exec( $statement ) {
		pg_query( $this->_con, $statement );
		return pg_affected_rows( $this->_con );
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
		// PostgreSQL  >= 8.1
		$con = pg_query( $this->_con, 'SELECT LASTVAL() AS LV' ); // @todo no debug
		$row = pg_fetch_array( $con );
		return intval( $row[0] );
	}

	/**
	 *
	 * @staticvar array $statements
	 * @staticvar int $count
	 * @param string $statement
	 * @return \AttoDbo__StatementPgsql 
	 */
	public function prepare( $statement ) {//, array $driver_options = array( ) ) {
		static $statements = array( );
		static $count = 1;

		if ( !isset( $statements[$statement] ) ) {
			$this->params_map = array( );
			$statements[$statement] = array(
				'count' => $count++,
				'sql'   => preg_replace_callback( '/(:[a-zA-Z][a-zA-Z0-9_]*)/u', array( $this, '_replace_param' ), $statement ),
				'params_map' => $this->params_map );
		}
		$name = 'AttoDbo__ConnectionPgsql#prepare_' . $statements[$statement]['count'];
		$sql = $statements[$statement]['sql'];
		$params_map = $statements[$statement]['params_map'];
		return new AttoDbo__StatementPgsql( pg_prepare( $this->_con, $name, $sql ), $name, $params_map );
	}

	/**
	 *
	 * @param string $statement
	 * @return \AttoDbo__StatementPgsql 
	 */
	public function query( $statement ) {
		return new AttoDbo__StatementPgsql( pg_query( $this->_con, $statement ) );
	}

	/**
	 *
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
		return pg_escape_identifier( $string, $this->_con );
	}

	/**
	 *
	 * @return boolean 
	 */
	public function rollBack() {
		$this->is_begin = false;
		return !!pg_query( $this->_con, 'ROLLBACK' );
	}

	public function __destruct() {
		if ( $this->inTransaction() ) {
			$this->rollBack();
		}
		pg_close( $this->_con );
		unset( $this->_con );
	}

	/**
	 * 
	 * @param array $matchs
	 * @return string 
	 */
	private function _replace_param( $matchs ) {
		if ( in_array( $matchs[1], $this->params_map ) ) {
			$i = array_search( $matchs[1], $this->params_map ) + 1;
		}
		else {
			$this->params_map[] = $matchs[1];
			$i = count( $this->params_map );
		}
		return '$' . $i;
	}

	//
	// **************************************************************************************************
	// *************						not supporteds									*************
	// **************************************************************************************************

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