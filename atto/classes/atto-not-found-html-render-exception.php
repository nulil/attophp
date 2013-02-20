<?php

/**
 * AttoNotFoundHtmlRenderException
 * 
 * by "renbder" method to view the 404 error message
 * "renbder"メソッドにより、404エラーメッセージを表示する
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
class AttoNotFoundHtmlRenderException extends AttoHttpErrorRenderException {

	function __construct( $message = '', $code = 0, Exception $previous = NULL ) {
		parent::__construct( 404, $message, $code, $previous );
	}

	/**
	 *
	 * @Override 
	 */
	public function render() {
		parent::render();
	}

}