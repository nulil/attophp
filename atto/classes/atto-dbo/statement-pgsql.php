<?php

/**
 * AttoDbo_StatementPgsql
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
class AttoDbo_StatementPgsql implements AttoDbo_IStatement {

	private $_con;
	private $_original_con;
	private $_params_map;
	private $_pripare_name;
	static private $_FETCH_TYPES = array(
		AttoDbo_IConnection::FETCH_BOTH  => PGSQL_BOTH,
		AttoDbo_IConnection::FETCH_ASSOC => PGSQL_ASSOC,
		AttoDbo_IConnection::FETCH_NUM   => PGSQL_NUM,
		AttoDbo_IConnection::FETCH_OBJ   => null
	);

	public function __construct( $con, $pripare_name = null, $params_map = null ) {
		$this->_con = $con;
		$this->_original_con = $con;
		$this->_pripare_name = $pripare_name;
		$this->_params_map = $params_map;
	}

	/**
	 * errorCode
	 * 
	 * @method errorCode
	 * @return type 
	 */
	public function errorCode() {
		return pg_result_status( $this->_con, PGSQL_STATUS_LONG );
	}

	/**
	 * errorInfo
	 * 
	 * @method errorInfo
	 * @return string 
	 */
	public function errorInfo() {
		return pg_result_status( $this->_con, PGSQL_STATUS_STRING );
	}

	/**
	 * rowCount
	 * 
	 * @method rowCount
	 * @return int 
	 */
	public function rowCount() {
		return pg_num_rows( $this->_con );
	}

	/**
	 * columnCount
	 * 
	 * @method columnCount
	 * @return int
	 */
	public function columnCount() {
		return pg_num_fields( $this->_con );
	}

	/**
	 * execute
	 * 
	 * プリペアドステートメントを実行します
	 * 
	 * @method execute
	 * @param array $params
	 * @return bool 
	 */
	public function execute( array $params = null ) {
		$args = array( );
		foreach ( $this->_params_map as $param ) {
			$args[] = $params[$param];
		}
		$this->_con = pg_execute( $this->_original_con, $this->_pripare_name, $args );
		return !!$this->_con;
	}

	/**
	 * fetch
	 * 
	 * @method fetch
	 * @param $fetch_type
	 * @return mixed 
	 */
	public function fetch( $fetch_type = AttoDbo_IConnection::FETCH_BOTH ) {
		if ( $fetch_type === AttoDbo_IConnection::FETCH_OBJ ) {
			return pg_fetch_object( $this->_con );
		}
		return pg_fetch_array( $this->_con, null, self::$_FETCH_TYPES[$fetch_type] );
	}

	/**
	 * fetchAll
	 * 
	 * @method fetchAll
	 * @param $fetch_type
	 * @return array 
	 */
	public function fetchAll( $fetch_type = AttoDbo_IConnection::FETCH_BOTH ) {
		if ( $fetch_type === AttoDbo_IConnection::FETCH_ASSOC ) {
			return pg_fetch_all( $this->_con );
		}

		if ( pg_num_rows( $this->_con ) <= 0 ) {
			return false;
		}
		$res = array( );
		pg_result_seek( $this->_con, 0 );
		while ( $row = $this->fetch( $fetch_type ) ) {
			array_push( $res, $row );
		}
		return $res;
	}

	/**
	 * fetchColumn
	 * 
	 * @method fetchColumn
	 * @param int $column_number
	 * @return boolean 
	 */
	public function fetchColumn( $column_number = 0 ) {
		$row = $this->fetch();
		if ( $row ) {
			return $row[$column_number];
		}
		return false;
	}

	/**
	 * fetchObject
	 * 
	 * @method fetchObject
	 * @param string $class_name
	 * @param array $ctor_args
	 * @return mixed 
	 */
	public function fetchObject( $class_name = 'stdClass', array $ctor_args = null ) {
		return pg_fetch_object( $this->_con, null, $class_name, $ctor_args );
	}

	/**
	 * closeCursor
	 * 
	 * @method closeCursor 
	 * @return boolean
	 */
	public function closeCursor() {
		return pg_close( $this->_con );
	}

	public function __destruct() {
		unset( $this->_con );
		unset( $this->_original_con );
	}

	//
	// ******************************************************************************************************
	// *************							not supporteds									*************
	// ******************************************************************************************************

	/**
	 * not supported
	 * @param type $column
	 * @param type $param
	 * @param type $type
	 * @param type $maxlen
	 * @param type $driverdata 
	 */
	public function bindColumn( $column, &$param, $type = null, $maxlen = null, $driverdata = null ) {
		trigger_error( 'not supported', E_USER_WARNING );
	}

	/**
	 * not supported
	 * @param type $parameter
	 * @param type $variable
	 * @param type $data_type
	 * @param type $length
	 * @param type $driver_options 
	 */
	public function bindParam( $parameter, &$variable, $data_type = AttoDbo_ForPdo::PARAM_STR, $length = null, $driver_options = null ) {
		trigger_error( 'not supported', E_USER_WARNING );
	}

	/**
	 * not supported
	 * @param type $parameter
	 * @param type $value
	 * @param type $data_type 
	 */
	public function bindValue( $parameter, $value, $data_type = AttoDbo_ForPdo::PARAM_STR ) {
		trigger_error( 'not supported', E_USER_WARNING );
	}

	/**
	 * not supported 
	 */
	public function debugDumpParams() {
		trigger_error( 'not supported', E_USER_WARNING );
	}

	/**
	 * not supported
	 * @param type $attribute 
	 */
	public function getAttribute( $attribute ) {
		trigger_error( 'not supported', E_USER_WARNING );
	}

	/**
	 * not supported
	 * @param type $column 
	 */
	public function getColumnMeta( $column ) {
		trigger_error( 'not supported', E_USER_WARNING );
	}

	/**
	 * not supported
	 * @return boolean 
	 */
	public function nextRowset() {
		trigger_error( 'not supported', E_USER_WARNING );
		return false;
	}

	/**
	 * not supported
	 * @param type $attribute
	 * @param type $value 
	 */
	public function setAttribute( $attribute, $value ) {
		trigger_error( 'not supported', E_USER_WARNING );
	}

	/**
	 * not supported
	 * @param type $mode 
	 */
	public function setFetchMode( $mode ) {
		trigger_error( 'not supported', E_USER_WARNING );
	}

}
