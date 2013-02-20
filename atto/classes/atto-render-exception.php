<?php

/**
 * AttoRenderException
 * 
 * by "renbder" method to view output
 * "renbder"メソッドにより、viewを表示する
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
class AttoRenderException extends AttoAbstractHasRenderException {

	public function __construct( $view = '', $message = '', $code = 0, Exception $previous = NULL ) {
		parent::__construct( $message, $code, $previous );
		if ( !starts_with( Atto::dir_app(), $view )
				&& !starts_with( Atto::dir_htdocs(), $view ) ) {
			$view = Atto::makeAccessPath( array( Atto::dir_error(), Atto::dir_atto_error() ), array( $view ) );
		}
		$this->view = $view;
	}

	/**
	 * @Override 
	 */
	public function render() {
		if ( is_file( $this->view ) ) {
			ob_start();
			include $this->view;

			if ( $this->layout ) {
				$this->content_for_layout = ob_get_clean();
				ob_start();
				include Atto::makeAccessPath( array( Atto::dir_layout(), '', Atto::dir_atto_layout() ), array( $this->layout, 'default' ), array( '', '.php' ) );
			}
			ob_end_flush();
		}
	}

}