<?php

/**
 * AttoFilebaseLogger
 * 
 * logger for file-base
 * ファイルベースのロガー
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
class AttoFilebaseLogger {

	/**
	 * logging
	 *
	 * @param {integer} $logLv
	 * @param {string} $header
	 * @param {string} $data
	 */
	static public function logging( $logLv, $header = '', $data = '' ) {
		try {
			$options = Atto::getOptions();

			if ( !$options || $options['logging_level'] < $logLv ) {
				return;
			}

			$files = $options['logging_files'];
			$dir = Atto::dir_var_log();
			$max_byte = $options['logging_MiBbyte'] * 1048576;

			$words = array( );
			$words[] = "\n・";
			$words[] = date( 'Y/m/d H:i:s' );
			$words[] = "\t";
			$words[] = $header;
			$words[] = ' : ';
			$words[] = function_exists('json_encode') ? json_encode( $data ) : var_export( $data, true );

			$file_paths = array( );
			foreach ( $files as $val ) {
				$file_paths[] = $dir . $val;
			}

			$idx = 0;
			$near = 0;
			$fTime = 0;
			for ( $i = 0, $l = count( $file_paths ); $i < $l; $i++ ) {
				if ( file_exists( $file_paths[$i] ) ) {
					$fTime = filemtime( $file_paths[$i] );
				}
				if ( $near < $fTime ) {
					$near = $fTime;
					$idx = $i;
				}
			}

			if ( !file_exists( $file_paths[$idx] )
					|| (filesize( $file_paths[$idx] ) + (strlen( bin2hex( implode( $words ) ) ) / 2)) <= $max_byte ) {
				$file_path = $file_paths[$idx];
				if ( file_exists( $file_path ) ) {
					$mode = 'a';
				}
				else {
					$mode = 'w';
					$words[0] = '・';
				}
			}
			else {
				$file_path = (($idx + 1) < count( $file_paths )) ? $file_paths[$idx + 1] : $file_paths[0];
				$mode = 'w';
				$words[0] = '・';
			}

			$count = 0;
			while ( true ) {
				$fp = fopen( $file_path, $mode );
				if ( $fp == false ) {
					if ( $count < 100 ) {
						usleep( 1000 );
						$count++;
					}
					else {
						return false;
					}
				}
				else {
					break;
				}
			}
			$count = 0;
			while ( flock( $fp, LOCK_EX ) == false ) {
				if ( $count < 100 ) {
					usleep( 1000 );
					$count++;
				}
				else {
					return false;
				}
			}
			fputs( $fp, implode( $words ) );
			flock( $fp, LOCK_UN );
			fclose( $fp );
		}
		catch ( Exception $e ) {
			$e->getMessage();
		}
	}

}
