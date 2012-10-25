<?php

/**
 * AttoHttpErrorRenderException
 * 
 * by "renbder" method to view the error status message
 * "renbder"メソッドにより、エラーステータスのメッセージを表示する
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
class AttoHttpErrorRenderException extends AttoAbstractHasRenderException {

	public function __construct( $status, $message = '', $code = 0, Exception $previous = NULL ) {
		parent::__construct( $message, $code, $previous );
		$this->status = intval( $status );
	}

	/**
	 * @Override 
	 */
	public function render() {
		$file = Atto::makeAccessPath( array( Atto::dir_error(), Atto::dir_atto_error() ), array( $this->status, 'etc' ), array( '.html', '.php' ) );
		$this->error_message = $this->getMessage();
		$this->title = $this->status . '  ' . AttoHttpHelper::getTextByResponseCode( $this->status );

		AttoHttpHelper::setResponseCode( $this->status );

		include $file;
	}

}