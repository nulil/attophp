<?php

/**
 * AttoAbstractHasRenderException
 * 
 * exception class with the "render" method
 * renderメソッドを持った例外クラス
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
abstract class AttoAbstractHasRenderException extends AttoDinamicException {

	abstract public function render();
}