<?php

/**
 * AttoDbo__StatementMysql
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
class AttoDbo__StatementMysql implements AttoDbo__IStatement {

	private $_con;
	private $_original_con;
	private $_statement;
	private $_connection;
	private $_params;
	static private $_FETCH_TYPES = array(
		AttoDbo__IConnection::FETCH_BOTH  => MYSQL_BOTH,
		AttoDbo__IConnection::FETCH_ASSOC => MYSQL_ASSOC,
		AttoDbo__IConnection::FETCH_NUM   => MYSQL_NUM,
		AttoDbo__IConnection::FETCH_OBJ   => null
	);

	public function __construct( $con, AttoDboConnectionMysql $connection = null, $statement = null ) {
		$this->_con = $con;
		$this->_original_con = $con;
		$this->_connection = $connection;
		$this->_statement = $statement;
	}

	/**
	 *
	 * @return int 
	 */
	public function errorCode() {
		return mysql_errno( $this->_con );
	}

	/**
	 *
	 * @return array 
	 */
	public function errorInfo() {
		return array( mysql_errno( $this->_con ), mysql_errno( $this->_con ), mysql_error( $this->_con ) );
	}

	/**
	 *
	 * @return int 
	 */
	public function rowCount() {
		return mysql_num_rows( $this->_con );
	}

	/**
	 *
	 * @return int 
	 */
	public function columnCount() {
		return mysql_num_fields( $this->_con );
	}

	/**
	 * execute
	 * 
	 * プリペアドステートメントを実行します
	 * 
	 * @method execute
	 * @param array $params
	 * @return boolean 
	 */
	public function execute( array $params = null ) {
		$pattern = '/(:[a-zA-Z][a-zA-Z0-9_])*/u';
		$this->_params = $params;
		$sql = preg_replace( $pattern, array( $this, '_replaceParam' ), $this->_statement );
		$this->_con = mysql_query( $sql, $this->_original_con );
		return !!$this->_con;
	}

	/**
	 * fetch
	 * 
	 * @method fetch
	 * @param $fetch_type
	 * @return mixed 
	 */
	public function fetch( $fetch_type = AttoDbo__IConnection::FETCH_BOTH ) {
		if ( $fetch_type === AttoDbo__IConnection::FETCH_OBJ ) {
			return mysql_fetch_object( $this->_con );
		}
		return mysql_fetch_array( $this->_con, self::$_FETCH_TYPES[$fetch_type] );
	}

	/**
	 * fetchAll
	 * 
	 * @method fetchAll
	 * @param $fetch_type
	 * @return array 
	 */
	public function fetchAll( $fetch_type = AttoDbo__IConnection::FETCH_BOTH ) {
		if ( mysql_num_rows( $this->_con ) <= 0 ) {
			return false;
		}
		$res = array( );
		mysql_data_seek( $this->_con, 0 );
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
		return mysql_fetch_object( $this->_con, $class_name, $ctor_args );
	}

	/**
	 * closeCursor
	 * 
	 * @method closeCursor 
	 * @return boolean
	 */
	public function closeCursor() {
		return mysql_close( $this->_con );
	}

	public function __destruct() {
		unset( $this->_con );
		unset( $this->_original_con );
		unset( $this->_connection );
	}

	private function _replaceParam( $matchs ) {
		if ( array_key_exists( $this->_params, $matchs[1] ) ) {
			$val = $this->_params[$matchs[1]];
			if ( is_null( $val ) ) {
				return $this->connection_mysql->quote( $val, AttoDbo__ForPdo::PARAM_NULL );
			}
			return $this->connection_mysql->quote( $val );
		}
		return $matchs[1];
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
	public function bindParam( $parameter, &$variable, $data_type = AttoDbo__ForPdo::PARAM_STR, $length = null, $driver_options = null ) {
		trigger_error( 'not supported', E_USER_WARNING );
	}

	/**
	 * not supported
	 * @param type $parameter
	 * @param type $value
	 * @param type $data_type 
	 */
	public function bindValue( $parameter, $value, $data_type = AttoDbo__ForPdo::PARAM_STR ) {
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
