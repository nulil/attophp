<?php

/**
 * AttoDbo_Manager
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
class AttoDbo_Manager {

	const TYPE_MYSQL = 'mysql';
	const TYPE_PGSQL = 'pgsql';

	/**
	 * $parametersの値でインスタンス化されたAttoDbo_IConnectionオブジェクトを保持する
	 * @var array 
	 */
	protected $connections = array( );

	/**
	 * set_conection_infoメソッドで設定された値を保持する
	 * @var array 
	 */
	protected $parameters = array( );

	/**
	 * シングルトン用
	 * @var \AttoDbo_Manager
	 */
	static protected $single = null;

	/**
	 * getInstanse
	 * 
	 * 最初にインスタンス化されたAttoDboManagerを返す
	 * まだインスタンスされていなければ、インスタンス化し返す
	 * 
	 * @method getInstanse
	 * @return \AttoDbo_Manager 
	 */
	static public function getInstanse() {
		if ( self::$single === null ) {
			new self;
		}
		return self::$single;
	}

	static public function isPdo() {
		static $val = null;
		if ( $val === null ) {
			$val = class_exists( 'PDO', false );
		}
		return $val;
	}

	static public function isLegacy() {
		return !self::isPdo();
	}

	static public function initializerHandlerRegister( $callback = null, $add = true ) {
		static $handles = array( );
		if ( $callback ) {
			if ( is_callable( $callback ) ) {
				if ( $add ) {
					// add
					if ( !in_array( $callback ) ) {
						$handles[] = $callback;
						return true;
					}
				}
				else {
					// remove
					$key = array_search( $callback, $handles );
					if ( is_int( $key ) ) {
						array_splice( $handles, $key, 1 );
						return true;
					}
				}
			}
		}
		else {
			// get
			return array_reverse( $handles );
		}
		return false;
	}

	/**
	 * __construct
	 * 
	 * シングルトンでの利用を強制する仕様ではないが、getInstanseメソッドによるシングルトンでの利用を推奨
	 */
	public function __construct() {
		foreach ( self::initializerHandlerRegister() as $callback ) {
			call_user_func_array( $callback, array( &$this ) );
		}
		if ( self::$single === null ) {
			self::$single = $this;
		}
	}

	/**
	 * setConnectionInfo
	 * 
	 * @method setConnectionInfo
	 * @param string $connection_name
	 * @param array $params key is 'type',	'host',	'port',	'db', 'user', 'password', 'options'
	 * @return boolean false:$connection_nameが既に設定されている
	 */
	public function setConnectionInfo( $connection_name, array $params ) {

		if ( isset( $this->parameters[$connection_name] ) ) {
			return false;
		}

		if ( $this->isPdo() ) {
			$def_options = array(
				PDO::ATTR_EMULATE_PREPARES => false, // サーバーサイドプリペアドステートメント有効化
				PDO::ATTR_ERRMODE		  => PDO::ERRMODE_EXCEPTION, // エラー時に例外をthrowする設定
			);
		}
		else {
			$def_options = array( );
		}

		$def_params = array(
			'type'	 => self::TYPE_MYSQL,
			'host'	 => null,
			'port'	 => null,
			'db'	   => null,
			'user'	 => '',
			'password' => '',
			'options'  => $def_options,
		);

		$this->parameters[$connection_name] = array_merge( $def_params, $params );
		return true;
	}

	/**
	 * getConnection
	 * 
	 * @method getConnection
	 * @param string $connection_name
	 * @return \AttoDbo_IConnection
	 */
	public function getConnection( $connection_name = null ) {
		if ( is_null( $connection_name ) ) {
			$connection_name = key( $this->parameters );
		}

		if ( isset( $this->parameters[$connection_name] ) ) {
			if ( !isset( $this->connections[$connection_name] ) ) {
				$dbo_class = self::isPdo() ? 'AttoDbo_ForPdo' : 'AttoDbo_ForLegacy';
				$this->connections[$connection_name] = new $dbo_class( $this->parameters[$connection_name] );
			}
			return $this->connections[$connection_name];
		}
		throw new OutOfBoundsException( '"connection name ' . $connection_name . '" is not setting' );
	}

	/**
	 * __destruct
	 * 
	 * @method __destruct
	 */
	public function __destruct() {
		// 接続開放
		foreach ( $this->connections as $con ) {
			unset( $con );
		}
	}

}
