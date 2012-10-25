<?php

/**
 * AttoDbo__IStatement
 */
interface AttoDbo__IStatement {

	public function __construct( $con, AttoDbo__IConnection $connection = null, $statement = null );

	/**
	 * @return int 
	 */
	public function errorCode();

	/**
	 * @return array 
	 */
	public function errorInfo();

	/**
	 * @return int 
	 */
	public function rowCount();

	/**
	 * @return int 
	 */
	public function columnCount();

	/**
	 * execute
	 * 
	 * プリペアドステートメントを実行します
	 * 
	 * @method execute
	 * @param {array} $params
	 * @return type 
	 */
	public function execute( array $params = null );

	/**
	 * fetch
	 * 
	 * @method fetch
	 * @param $fetch_type
	 * @return {mixed} 
	 */
	public function fetch( $fetch_type = AttoDbo__IConnection::FETCH_BOTH );

	/**
	 * fetchAll
	 * 
	 * @method fetchAll
	 * @param $fetch_type
	 * @return {array} 
	 */
	public function fetchAll( $fetch_type = AttoDbo__IConnection::FETCH_BOTH );

	/**
	 * fetchColumn
	 * 
	 * @method fetchColumn
	 * @param {int} $column_number
	 * @return {boolean} 
	 */
	public function fetchColumn( $column_number = 0 );

	/**
	 * fetchObject
	 * 
	 * @method fetchObject
	 * @param {string} $class_name
	 * @param {array} $ctor_args
	 * @return {mixed} 
	 */
	public function fetchObject( $class_name = 'stdClass', array $ctor_args = null );

	/**
	 * closeCursor
	 * 
	 * @method closeCursor
	 * @return boolean
	 */
	public function closeCursor();


//	//
//	// ******************************************************************************************************
//	// *************							not supporteds									*************
//	// ******************************************************************************************************
//
//	/**
//	 * not supported
//	 * @param type $column
//	 * @param type $param
//	 * @param type $type
//	 * @param type $maxlen
//	 * @param type $driverdata 
//	 */
//	public function bindColumn( $column, &$param, $type = null, $maxlen = null, $driverdata = null );
//
//	/**
//	 * not supported
//	 * @param type $parameter
//	 * @param type $variable
//	 * @param type $data_type
//	 * @param type $length
//	 * @param type $driver_options 
//	 */
//	public function bindParam( $parameter, &$variable, $data_type = AttoDbo__Interface::PARAM_STR, $length = null,
//							$driver_options = null );
//
//	/**
//	 * not supported
//	 * @param type $parameter
//	 * @param type $value
//	 * @param type $data_type 
//	 */
//	public function bindValue( $parameter, $value, $data_type = AttoDbo__Interface::PARAM_STR );
//
//	/**
//	 * not supported 
//	 */
//	public function debugDumpParams();
//
//	/**
//	 * not supported
//	 * @param type $attribute 
//	 */
//	public function getAttribute( $attribute );
//
//	/**
//	 * not supported
//	 * @param type $column 
//	 */
//	public function getColumnMeta( $column );
//
//	/**
//	 * not supported
//	 * @return boolean 
//	 */
//	public function nextRowset();
//
//	/**
//	 * not supported
//	 * @param type $attribute
//	 * @param type $value 
//	 */
//	public function setAttribute( $attribute, $value );
//
//	/**
//	 * not supported
//	 * @param type $mode 
//	 */
//	public function setFetchMode( $mode );
}
