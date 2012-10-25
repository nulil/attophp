<?php

/**
 * AttoXml
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
class AttoXml {

	static public function array2xml( $array ) {
		$ret = array( );
		foreach ( $array as $tag => $value ) {
			if ( is_int( $tag ) ) {
				$ret[] = $value;
				continue;
			}

			if ( !is_array( $value ) ) {
				$ret[] = '<' . $tag . '>' . $value . '</' . $tag . '>';
				continue;
			}

			$attrs = null;
			if ( isset( $value['<attrs>'] ) ) {
				$attrs = $value['<attrs>'];
			}
			unset( $value['<attrs>'] );

			$clones = array( );
			$values = array( );
			foreach ( $value as $key => $val ) {
				if ( is_int( $key ) ) {
					if ( is_array( $val ) ) {
						$clones[$key] = $val;
					}
					else {
						$values[$key] = $val;
					}
				}
			}
			if ( 1 < count( $values ) ) {
				foreach ( $values as $key => $val ) {
					$clones[$key] = $val;
					unset( $values[$key] );
				}
			}
			if ( 0 < count( $clones ) ) {
				foreach ( $clones as $key => $val ) {
					unset( $value[$key] );
				}
				$value['<attrs>'] = $attrs;

				foreach ( $clones as $key => $val ) {
					if ( !is_array( $val ) ) {
						$val = array( $val );
					}
					$ret[] = self::array2xml( array( $tag => array_merge( $value, $val ) ) );
				}
				continue;
			}

			$ret[] = '<' . $tag;
			if ( $attrs ) {
				foreach ( $attrs as $key => $val ) {
					if ( is_int( $key ) ) {
						$ret[] = ' ' . $val;
					}
					else {
						$ret[] = ' ' . $key . '="' . $val . '"';
					}
				}
			}

			if ( $value ) {
				$ret[] = '>';
				$ret[] = self::array2xml( $value );
				$ret[] = '</' . $tag . '>';
			}
			else {
				$ret[] = '/>';
			}
		}
		return implode( '', $ret );
	}

	/**
	 * SimpleXMLでパースしたオブジェクトを配列へ
	 *
	 * @param SimpleXMLElement $xmlobj
	 * @return array 
	 */
	static public function xml2array(SimpleXMLElement $xmlobj) {
		$arr = array();
		if (is_object($xmlobj)) {
			$xmlobj = get_object_vars($xmlobj);
		} else {
			$xmlobj = $xmlobj;
		}

		foreach ($xmlobj as $key => $val) {
			if (is_object($xmlobj[$key])) {
				$arr[$key] = xml2arr($val);
			} else if (is_array($val)) {
				foreach ($val as $k => $v) {
					if (is_object($v) || is_array($v)) {
						$arr[$key][$k] = xml2arr($v);
					} else {
						$arr[$key][$k] = $v;
					}
				}
			} else {
				$arr[$key] = $val;
			}
		}
		return $arr;
	}

}