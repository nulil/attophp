<?php

/**
 * AttoMimeType
 *
 * has mime-type data
 * MIMEタイプのデータを持っている
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
class AttoMimeType {

	static private $_ext2mime = array(
		'ez'	   => 'application/andrew-inset',
		'atom'	 => 'application/atom+xml',
		'oda'	  => 'application/oda',
		'ogg'	  => 'application/ogg',
		'pdf'	  => 'application/pdf',
		'ai'	   => 'application/postscript',
		'eps'	  => 'application/postscript',
		'ps'	   => 'application/postscript',
		'rdf'	  => 'application/rdf+xml',
		'rtf'	  => 'application/rtf',
		'smi'	  => 'application/smil',
		'smil'	 => 'application/smil',
		'gram'	 => 'application/srgs',
		'grxml'	=> 'application/srgs+xml',
		'apk'	  => 'application/vnd.android.package-archive',
		'kml'	  => 'application/vnd.google-earth.kml+xml',
		'kmz'	  => 'application/vnd.google-earth.kmz',
		'xul'	  => 'application/vnd.mozilla.xul+xml',
		'xls'	  => 'application/vnd.ms-excel',
		'ppt'	  => 'application/vnd.ms-powerpoint',
		'wbxml'	=> 'application/vnd.wap.wbxml',
		'wmlc'	 => 'application/vnd.wap.wmlc',
		'wmlsc'	=> 'application/vnd.wap.wmlscriptc',
		'vxml'	 => 'application/voicexml+xml',
		'bcpio'	=> 'application/x-bcpio',
		'vcd'	  => 'application/x-cdlink',
		'pgn'	  => 'application/x-chess-pgn',
		'cpio'	 => 'application/x-cpio',
		'csh'	  => 'application/x-csh',
		'dcr'	  => 'application/x-director',
		'dir'	  => 'application/x-director',
		'dxr'	  => 'application/x-director',
		'dvi'	  => 'application/x-dvi',
		'ebk'	  => 'application/x-expandedbook',
		'spl'	  => 'application/x-futuresplash',
		'gtar'	 => 'application/x-gtar',
		'hdf'	  => 'application/x-hdf',
		'php'	  => 'application/x-httpd-php',
		'jam'	  => 'application/x-jam',
		'js'	   => 'text/javascript',
		'kjx'	  => 'application/x-kj',
		'skp'	  => 'application/x-koan',
		'skd'	  => 'application/x-koan',
		'skt'	  => 'application/x-koan',
		'skm'	  => 'application/x-koan',
		'latex'	=> 'application/x-latex',
		'amc'	  => 'application/x-mpeg',
		'nc'	   => 'application/x-netcdf',
		'cdf'	  => 'application/x-netcdf',
		'sh'	   => 'application/x-sh',
		'shar'	 => 'application/x-shar',
		'swf'	  => 'application/x-shockwave-flash',
		'mmf'	  => 'application/x-smaf',
		'sit'	  => 'application/x-stuffit',
		'sv4cpio'  => 'application/x-sv4cpio',
		'sv4crc'   => 'application/x-sv4crc',
		'tar'	  => 'application/x-tar',
		'tcl'	  => 'application/x-tcl',
		'tex'	  => 'application/x-tex',
		'texinfo'  => 'application/x-texinfo',
		'texi'	 => 'application/x-texinfo',
		't'		=> 'application/x-troff',
		'tr'	   => 'application/x-troff',
		'roff'	 => 'application/x-troff',
		'man'	  => 'application/x-troff-man',
		'me'	   => 'application/x-troff-me',
		'ms'	   => 'application/x-troff-ms',
		'ustar'	=> 'application/x-ustar',
		'src'	  => 'application/x-wais-source',
		'zac'	  => 'application/x-zaurus-zac',
		'xhtml'	=> 'application/xhtml+xml',
		'xht'	  => 'application/xhtml+xml',
		'dtd'	  => 'application/xml-dtd',
		'xslt'	 => 'application/xslt+xml',
		'zip'	  => 'application/zip',
		'au'	   => 'audio/basic',
		'snd'	  => 'audio/basic',
		'mid'	  => 'audio/midi',
		'midi'	 => 'audio/midi',
		'kar'	  => 'audio/midi',
		'mpga'	 => 'audio/mpeg',
		'mp2'	  => 'audio/mpeg',
		'mp3'	  => 'audio/mpeg',
		'qcp'	  => 'audio/vnd.qcelp',
		'aif'	  => 'audio/x-aiff',
		'aiff'	 => 'audio/x-aiff',
		'aifc'	 => 'audio/x-aiff',
		'm3u'	  => 'audio/x-mpegurl',
		'wax'	  => 'audio/x-ms-wax',
		'wma'	  => 'audio/x-ms-wma',
		'ram'	  => 'audio/x-pn-realaudio',
		'rm'	   => 'audio/x-pn-realaudio',
		'rpm'	  => 'audio/x-pn-realaudio-plugin',
		'ra'	   => 'audio/x-realaudio',
		'vqf'	  => 'audio/x-twinvq',
		'vql'	  => 'audio/x-twinvq',
		'vqe'	  => 'audio/x-twinvq-plugin',
		'wav'	  => 'audio/x-wav',
		'igs'	  => 'model/iges',
		'iges'	 => 'model/iges',
		'msh'	  => 'model/mesh',
		'mesh'	 => 'model/mesh',
		'silo'	 => 'model/mesh',
		'wrl'	  => 'model/vrml',
		'vrml'	 => 'model/vrml',
		'ics'	  => 'text/calendar',
		'ifb'	  => 'text/calendar',
		'css'	  => 'text/css',
		'html'	 => 'text/html',
		'htm'	  => 'text/html',
		'asc'	  => 'text/plain',
		'txt'	  => 'text/plain',
		'rtx'	  => 'text/richtext',
		'sgml'	 => 'text/sgml',
		'sgm'	  => 'text/sgml',
		'tsv'	  => 'text/tab-separated-values',
		'rt'	   => 'text/vnd.rn-realtext',
		'jad'	  => 'text/vnd.sun.j2me.app-descriptor',
		'wml'	  => 'text/vnd.wap.wml',
		'wmls'	 => 'text/vnd.wap.wmlscript',
		'hdml'	 => 'text/x-hdml;charset=Shift_JIS',
		'etx'	  => 'text/x-setext',
		'xml'	  => 'text/xml',
		'xsl'	  => 'text/xml',
		'mpeg'	 => 'video/mpeg',
		'mpg'	  => 'video/mpeg',
		'mpe'	  => 'video/mpeg',
		'qt'	   => 'video/quicktime',
		'mov'	  => 'video/quicktime',
		'mxu'	  => 'video/vnd.mpegurl',
		'm4u'	  => 'video/vnd.mpegurl',
		'rv'	   => 'video/vnd.rn-realvideo',
		'mng'	  => 'video/x-mng',
		'asf'	  => 'video/x-ms-asf',
		'asx'	  => 'video/x-ms-asf',
		'avi'	  => 'video/x-msvideo',
		'movie'	=> 'video/x-sgi-movie',
		'ice'	  => 'x-conference/x-cooltalk',
		'd96'	  => 'x-world/x-d96',
		'mus'	  => 'x-world/x-d96',
		'download' => 'application/force-download',
		'json'	 => 'application/json',
		'jpg' => 'image/jpeg',
		'jpeg' => 'image/jpeg',
		'png' => 'image/png',
		'gif' => 'image/gif',
	);

	/**
	 * getByExtension
	 *
	 * @method getByExtension
	 * @param {string} $ext Extension
	 * @return {string} MIME type
	 */
	static public function getByExtension( $ext ) {
		if ( isset( self::$_ext2mime[$ext] ) ) {
			return self::$_ext2mime[$ext];
		}
		else {
			return '';
		}
	}

	/**
	 * getByFileName
	 *
	 * @method getByFileName
	 * @param {string} $file_name file name
	 * @return {string} MIME type
	 */
	static public function getByFileName( $file_name ) {
		$paths = explode( DS, $file_name );
		$file = array_pop( $paths );
		if ( $file && ($pos = strrpos( '.', $file )) !== false ) {
			$ext = substr( $file, $pos + 1 );
		}
		else {
			$ext = '';
		}
		return self::getByExtension( $ext );
	}

	/**
	 * getExtension
	 * 
	 * @method getExtension
	 * @param string $mime
	 * @return string 
	 */
	static public function getExtension( $mime ) {
		return array_search( strtolower( $mime ), self::$_ext2mime );
	}

}