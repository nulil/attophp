<?php

/**
 * AttoDbo_ForPdo
 * 
 * wrapper of PDO
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
class AttoDbo_ForPdo implements AttoDbo_IConnection {

	private $_con;
	private $_trans = false;

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
//				throw OutOfBoundsException( '想定外のDBです' );
		}

		$def_params = array(
			'type'	 => 'mysql',
			'host'	 => null,
			'port'	 => null,
			'db'	   => null,
			'user'	 => '',
			'password' => '',
			'options'  => array(
				PDO::ATTR_EMULATE_PREPARES => false, // サーバーサイドプリペアドステートメント有効化
				PDO::ATTR_ERRMODE		  => PDO::ERRMODE_EXCEPTION, // エラー時に例外をthrowする設定
			),
		);

		$params = array_merge( $def_params, $params );

		switch ( $params['type'] ) {
//			case AttoDbo_Manager::TYPE_SQLITE:
//			case AttoDbo_Manager::TYPE_SQLITE2:
//				$params['dsn'] = $params['type'] . ':' . $params['db'];
//				break;
			case 'mysql':
			case 'pgsql':
			default :
				$params['dsn'] = $params['type'] . ':dbname=' . $params['db']
						. ';host=' . $params['host']
						. ( $params['port'] ? ';port=' . $params['port'] : '' );
				break;
		}

		$this->_con = new PDO( $params['dsn'], $params['user'], $params['password'], $params['options'] );
//		$this->con->query( "SET CHARACTER SET 'utf8'" );
	}

	/**
	 *
	 * @return \PDO 
	 */
	public function getOriginalConnection() {
		return $this->_con;
	}

	/**
	 *
	 * @return boolean 
	 */
	public function beginTransaction() {
		$this->_trans = true;
		return $this->_con->beginTransaction();
	}

	/**
	 *
	 * @return boolean 
	 */
	public function commit() {
		$this->_trans = false;
		return $this->_con->commit();
	}

	/**
	 *
	 * @return mixed 
	 */
	public function errorCode() {
		return $this->_con->errorCode();
	}

	/**
	 * 
	 * @return array 
	 */
	public function errorInfo() {
		return $this->_con->errorInfo();
	}

	/**
	 *
	 * @param string $sql
	 * @return int 
	 */
	public function exec( $sql ) {
		return $this->_con->exec( $sql );
	}

	/**
	 *
	 * @staticvar null $flag
	 * @return boolean 
	 */
	public function inTransaction() {
		static $flag = null;
		if ( $flag === null ) {
			$con = $this->_con;
			$flag = method_exists( $con, 'inTransaction' );
		}
		if ( $flag ) {
			return $this->_con->inTransaction();
		}
		else {
			return $this->_trans;
		}
	}

	/**
	 *
	 * @return string 
	 */
	public function lastInsertId() {//$name = null ) {
		return $this->_con->lastInsertId();
	}

	/**
	 *
	 * @param string $sql
	 * @return PDOStatement 
	 */
	public function prepare( $sql ) {//, array $driver_options = array( ) ) {
		return $this->_con->prepare( $sql );
	}

	/**
	 *
	 * @param string $sql
	 * @return PDOStatement 
	 */
	public function query( $sql ) {
		return $this->_con->query( $sql );
	}

	/**
	 *
	 * @param string $string
	 * @param int $parameter_type
	 * @return string 
	 */
	public function quote( $string, $parameter_type = self::PARAM_STR ) {
		return $this->_con->quote( $string, $parameter_type );
	}

	/**
	 *
	 * @return boolean 
	 */
	public function rollBack() {
		$this->_trans = false;
		return $this->_con->rollBack();
	}

	public function __destruct() {
		if ( $this->inTransaction() ) {
			$this->rollBack();
		}

		unset( $this->_con );
	}

}
