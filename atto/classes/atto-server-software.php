<?php

/**
 * AttoServerSoftware
 *
 * Checking the server software
 * サーバーソフトの確認
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
class AttoServerSoftware {

	/**
	 * isApache
	 *
	 * @method isApache
	 * @return {boolean}
	 */
	static public function isApache() {
		static $val = null;
		if ( $val === null ) {
			$val = (strpos( $_SERVER['SERVER_SOFTWARE'], 'Apache' ) !== false
					|| strpos( $_SERVER['SERVER_SOFTWARE'], 'LiteSpeed' ) !== false);
		}
		return $val;
	}

	/**
	 * isIIS
	 *
	 * @method isIIS
	 * @return {boolean}
	 */
	static public function isIIS() {
		static $val = null;
		if ( $val === null ) {
			$val = (strpos( $_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS' ) !== false
					|| strpos( $_SERVER['SERVER_SOFTWARE'], 'ExpressionDevServer' ) !== false);
		}
		return $val;
	}

	/**
	 * isIIS7
	 *
	 * @method isIIS7
	 * @return {boolean}
	 */
	static public function isIIS7() {
		static $val = null;
		if ( $val === null ) {
			$val = (strpos( $_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS/7.' ) !== false);
		}
		return $val;
	}

}