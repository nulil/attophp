<?php

if ( function_exists( 'app_session_start' ) ) {
	app_session_start();
}
else {
	session_start();
}

/**
 * AttoSessionHelper
 *
 * session helper 
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
class AttoSessionHelper {

	const FINGER_PRINT_KEY = '__finger_print__';

	/**
	 * regenerateId
	 * 
	 * session idを変更する
	 * 
	 * @method regenerateId
	 * @staticvar boolean $first
	 * @param boolean $force true:初回移行でもid変更を行う / false:初回のみid変更を行う
	 */
	static public function regenerateId( $force = false ) {
		static $first = true;

		if ( $force || $first ) {
			$first = false;
			$session_id = session_id();
			if ( function_exists( 'session_regenerate_id' ) ) {
				session_regenerate_id( true );

				if ( version_compare( phpversion(), '5.1', '<=' ) ) {
					$file = session_save_path() . DS . 'sess_' . $session_id;
					if ( file_exists( $file ) ) {
						unlink( $file );
					}
				}
			}
			else {
				$old_session = $_SESSION;
				session_write_close();
				session_id( sha1( mt_rand() ) );
				session_start();
				$_SESSION = $old_session;
				unlink( session_save_path() . DS . 'sess_' . $session_id );
			}
		}
	}

	/**
	 * regenerateFingerprint
	 * 
	 * @method regenerateFingerprint
	 * @param string $salt 
	 */
	static public function regenerateFingerprint( $salt ) {
		self::regenerateId( true );
		self::registerFingerprint( $salt );
	}

	/**
	 * registerFingerprint
	 * 
	 * @method registerFingerprint
	 * @param {string} $salt 
	 */
	static public function registerFingerprint( $salt ) {
		$_SESSION[self::FINGER_PRINT_KEY] = self::_generateFingerprint( $salt, session_id() );
	}

	/**
	 * _generateFingerprint
	 * 
	 * @method _generateFingerprint
	 * @param string $salt
	 * @param string $session_id
	 * @return string 
	 */
	static private function _generateFingerprint( $salt, $session_id ) {
		$fingerprint = $salt;

		if ( !empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
			$fingerprint .= $_SERVER['HTTP_USER_AGENT'];
		}
		if ( !empty( $_SERVER['HTTP_ACCEPT_CHARSET'] ) ) {
			$fingerprint .= $_SERVER['HTTP_ACCEPT_CHARSET'];
		}
		$fingerprint .= $session_id;
		return sha1( $fingerprint );
	}

	/**
	 * isExistsForFingerprint
	 * 
	 * @method isExistsForFingerprint
	 * @return boolean
	 */
	static public function isExistsForFingerprint() {
		return !self::isExistsForFingerprint();
	}

	/**
	 * isNotExistsForFingerprint
	 * 
	 * @method isNotExistsForFingerprint
	 * @return boolean
	 */
	static public function isNotExistsForFingerprint() {
		return empty( $_SESSION[self::FINGER_PRINT_KEY] );
	}

	/**
	 * isApproveFingerprint
	 * 
	 * @method isApproveFingerprint
	 * @param string $salt 
	 * @return boolean
	 */
	static public function isApproveFingerprint( $salt ) {
		return !self::isDisapproveFingerprint( $salt );
	}

	/**
	 * isDisapproveFingerprint
	 * 
	 * @method isDisapproveFingerprint
	 * @param string $salt 
	 * @return boolean
	 */
	static public function isDisapproveFingerprint( $salt ) {
		if ( self::isNotExistsForFingerprint() ) {
			return true;
		}

		$session_id = session_id();
		$fingerprint = self::_generateFingerprint( $salt, $session_id );

		if ( $fingerprint !== $_SESSION[self::FINGER_PRINT_KEY] ) {
			AttoFilebaseLogger::logging( 3, 'session hijack !?', array( 'host'	  => AttoHttpHelper::getRemoteHost(), '$_SERVER'  => $_SERVER, '$_REQUEST' => $_REQUEST, '$_SESSION' => $_SESSION ) );
			unset( $_SESSION[self::FINGER_PRINT_KEY] );
			return true;
		}

		return false;
	}

	/**
	 * checkAndRegenerateFingerprint
	 * 
	 * Fingerprintを確認、変更する
	 * 
	 * @method checkAndRegenerateFingerprint
	 * @param string $salt
	 * @return boolean 
	 */
	static public function checkAndRegenerateFingerprint( $salt ) {
		if ( self::isApproveFingerprint( $salt ) ) {
			self::regenerateFingerprint( $salt );
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * registerCsrfToken
	 * 
	 * @method registerCsrfToken
	 * @param string $form_name
	 * @return string 
	 */
	static public function registerCsrfToken( $form_name ) {
		$key = 'csrf_tokens/' . $form_name;
		$tokens = ($tokens = $_SESSION[$key]) ? $tokens : array( );

		if ( 30 < count( $tokens ) ) {
			$tokens = array_slice( $tokens, -30 );
		}

		$token = sha1( $form_name . session_id() . microtime() );
		$tokens[] = $token;

		$_SESSION[$key] = $tokens;
		return $token;
	}

	/**
	 * checkCsrfToken
	 * 
	 * @method checkCsrfToken
	 * @param string $form_name
	 * @param string $token
	 * @return boolean 
	 */
	static public function checkCsrfToken( $form_name, $token ) {
		$key = 'csrf_tokens/' . $form_name;
		$tokens = ($tokens = $_SESSION[$key]) ? $tokens : array( );

		$pos = array_search( $token, $tokens, true );
		if ( false !== $pos ) {
			unset( $tokens[$pos] );
			$_SESSION[$key] = $tokens;
			return true;
		}
		return false;
	}

}