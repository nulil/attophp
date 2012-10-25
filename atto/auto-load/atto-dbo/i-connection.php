<?php

if ( class_exists( 'PDO' ) ) {

	/**
	 * AttoDbo__IConnection
	 * 
	 * @interface
	 */
	interface AttoDbo__IConnection {

		const BASE = 'PDO';

		/**
		 * Represents a boolean data type.
		 * @link http://php.net/manual/en/pdo.constants.php
		 */
		const PARAM_BOOL = PDO::PARAM_BOOL;

		/**
		 * Represents the SQL NULL data type.
		 * @link http://php.net/manual/en/pdo.constants.php
		 */
		const PARAM_NULL = PDO::PARAM_NULL;

		/**
		 * Represents the SQL INTEGER data type.
		 * @link http://php.net/manual/en/pdo.constants.php
		 */
		const PARAM_INT = PDO::PARAM_INT;

		/**
		 * Represents the SQL CHAR, VARCHAR, or other string data type.
		 * @link http://php.net/manual/en/pdo.constants.php
		 */
		const PARAM_STR = PDO::PARAM_STR;

		/**
		 * Represents the SQL large object data type.
		 * @link http://php.net/manual/en/pdo.constants.php
		 */
		const PARAM_LOB = PDO::PARAM_LOB;


		/**
		 * Specifies that the fetch method shall return each row as an array indexed
		 * by both column name and number as returned in the corresponding result set,
		 * starting at column 0.
		 * @link http://php.net/manual/en/pdo.constants.php
		 */
		const FETCH_BOTH = PDO::FETCH_BOTH;

		/**
		 * Specifies that the fetch method shall return each row as an array indexed
		 * by column name as returned in the corresponding result set. If the result
		 * set contains multiple columns with the same name,
		 * <b>PDO::FETCH_ASSOC</b> returns
		 * only a single value per column name.
		 * @link http://php.net/manual/en/pdo.constants.php
		 */
		const FETCH_ASSOC = PDO::FETCH_ASSOC;

		/**
		 * Specifies that the fetch method shall return each row as an array indexed
		 * by column number as returned in the corresponding result set, starting at
		 * column 0.
		 * @link http://php.net/manual/en/pdo.constants.php
		 */
		const FETCH_NUM = PDO::FETCH_NUM;

		/**
		 * Specifies that the fetch method shall return each row as an object with
		 * property names that correspond to the column names returned in the result
		 * set.
		 * @link http://php.net/manual/en/pdo.constants.php
		 */
		const FETCH_OBJ = PDO::FETCH_OBJ;

		public function __construct( array $params );

		/**
		 * @return \PDO 
		 */
		public function getOriginalConnection();

		/**
		 * @return boolean 
		 */
		public function beginTransaction();

		/**
		 * @return boolean 
		 */
		public function commit();

		/**
		 * @return mixed 
		 */
		public function errorCode();

		/**
		 * @return array 
		 */
		public function errorInfo();

		/**
		 * @param string $sql
		 * @return int 
		 */
		public function exec( $sql );

		/**
		 * @return boolean 
		 */
		public function inTransaction();

		/**
		 * @return string 
		 */
		public function lastInsertId();

		/**
		 * @param string $sql
		 * @return PDOStatement 
		 */
		public function prepare( $sql );

		/**
		 * @param string $sql
		 * @return PDOStatement 
		 */
		public function query( $sql );

		/**
		 * @param string $string
		 * @param int $parameter_type
		 * @return string 
		 */
		public function quote( $string, $parameter_type = self::PARAM_STR );

		/**
		 * @return boolean 
		 */
		public function rollBack();

//		//
//		// ******************************************************************************************************
//		// *************							not supporteds									*************
//		// ******************************************************************************************************
//		/**
//		 * not supported
//		 * 
//		 * @param type $attribute 
//		 */
//		public function getAttribute( $attribute );
//
//		/**
//		 * not supported
//		 * 
//		 * @param type $attribute
//		 * @param mixed $value 
//		 */
//		public function setAttribute( $attribute, $value );
	}

}
else {

	/**
	 * AttoDbo__IConnection
	 * 
	 * @interface 
	 */
	interface AttoDbo__IConnection {

		const BASE = 'legacy';

		/**
		 * Represents a boolean data type.
		 */
		const PARAM_BOOL = 5;

		/**
		 * Represents the SQL NULL data type.
		 */
		const PARAM_NULL = 0;

		/**
		 * Represents the SQL INTEGER data type.
		 */
		const PARAM_INT = 1;

		/**
		 * Represents the SQL CHAR, VARCHAR, or other string data type.
		 */
		const PARAM_STR = 2;

		/**
		 * Represents the SQL large object data type.
		 */
		const PARAM_LOB = 3;


		/**
		 * Specifies that the fetch method shall return each row as an array indexed
		 * by both column name and number as returned in the corresponding result set,
		 * starting at column 0.
		 */
		const FETCH_BOTH = 1;

		/**
		 * Specifies that the fetch method shall return each row as an array indexed
		 * by column name as returned in the corresponding result set. If the result
		 * set contains multiple columns with the same name,
		 * only a single value per column name.
		 */
		const FETCH_ASSOC = 2;

		/**
		 * Specifies that the fetch method shall return each row as an array indexed
		 * by column number as returned in the corresponding result set, starting at
		 * column 0.
		 */
		const FETCH_NUM = 3;

		/**
		 * Specifies that the fetch method shall return each row as an object with
		 * property names that correspond to the column names returned in the result
		 * set.
		 */
		const FETCH_OBJ = 4;

		public function __construct( array $params );

		public function getOriginalConnection();

		/**
		 * @return boolean 
		 */
		public function beginTransaction();

		/**
		 * @return boolean 
		 */
		public function commit();

		/**
		 * @return mixed 
		 */
		public function errorCode();

		/**
		 * @return array 
		 */
		public function errorInfo();

		/**
		 * @param string $sql
		 * @return int 
		 */
		public function exec( $sql );

		/**
		 * @return boolean 
		 */
		public function inTransaction();

		/**
		 * @return int 
		 */
		public function lastInsertId(); //$name = null )

		/**
		 * @param string $sql
		 * @return \AttoDbo__IStatement 
		 */
		public function prepare( $sql ); //, array $driver_options = array( ) )

		/**
		 * @param string $sql
		 * @return \AttoDbo__IStatement 
		 */
		public function query( $sql );

		/**
		 * @return string 
		 */
		public function quote( $string, $parameter_type = self::PARAM_STR );

		public function rollBack();

//		//
//		// ******************************************************************************************************
//		// *************							not supporteds									*************
//		// ******************************************************************************************************
//		/**
//		 * not supported
//		 * 
//		 * @param type $attribute 
//		 */
//		public function getAttribute( $attribute );
//
//		/**
//		 * not supported
//		 * 
//		 * @param type $attribute
//		 * @param mixed $value 
//		 */
//		public function setAttribute( $attribute, $value );
	}

}