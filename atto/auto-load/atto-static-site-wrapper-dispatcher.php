<?php

/**
 * AttoStaticSiteWrapperDispatcher
 * 
 * static site wrapper
 * 静的なサイトのラッパー
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
class AttoStaticSiteWrapperDispatcher extends AttoDinamic {

	public function __construct() {
		$this->funcs = new AttoDinamic();
		$this->funcs->before = array( $this, '___before___' );
		$this->funcs->hook = array( $this, '___hook___' );
		$this->funcs->view = array( $this, '___view___' );
		$this->funcs->render = array( $this, '___render___' );
		$this->funcs->huck = array( $this, '___huck___' );
		$this->funcs->after = array( $this, '___after___' );


		$this->files = new AttoDinamic();
	}

	/**
	 * dispatch
	 *
	 * @method dispatch
	 */
	public function dispatch() {

		$pathinfo_blocks = explode( '/', ltrim( Atto::uri(), '\\' ) );

		$i = count( $pathinfo_blocks ) - 1;
		if ( $pathinfo_blocks[$i] === '' ) {
			$file_names = array( 'index.html', 'index.php' );
		}
		else {
			$file_names = array( $pathinfo_blocks[$i] );
			$pathinfo_blocks[$i] = '';
		}
		$request_dir = implode( DS, $pathinfo_blocks );

		$hook_file = Atto::makeAccessPath( Atto::dir_hook() . $request_dir, $file_names, array( '', '.php' ) );
		$htdocs_file = Atto::makeAccessPath( Atto::dir_htdocs() . $request_dir, $file_names );
		$huck_file = Atto::makeAccessPath( Atto::dir_huck() . $request_dir, $file_names, array( '', '.php' ) );
		$view_file = null;

		if ( $hook_file === '' && $htdocs_file === '' && $huck_file === '' ) {
			// not found
			$view_file = Atto::makeAccessPath( array( Atto::dir_error(), Atto::dir_atto_error() ), array( 404, 'etc' ), array( '.html', '.php' ) );
			$this->title = '404  ' . AttoHttpHelper::getTextByResponseCode( 404 );
			$this->error_message = '';
			AttoHttpHelper::setResponseCode( 404 );
			$this->funcs->view = array( $this, '___view___' );
		}
		else {
			if ( $htdocs_file !== '' && is_dir( $htdocs_file ) ) {
				// access to the directory
				$redirect_URIs = explode( '?', Atto::requestUrl() );
				$redirect_URIs[0] .= '/';
				AttoHttpHelper::redirect( implode( '?', $redirect_URIs ), 301 );
			}


			if ( $htdocs_file && is_file( $htdocs_file ) ) {
				$this->files->view = $htdocs_file;
			}
			if ( $hook_file && is_file( $hook_file ) ) {
				$this->files->hook = $hook_file;
			}
			if ( $huck_file && is_file( $huck_file ) ) {
				$this->files->huck = $huck_file;
			}
		}

		$this->before();
		$this->hook();
		$content = $this->view();
		$this->render( $content );
		$this->huck();
		$this->after();
	}

	/**
	 * before
	 *
	 * @method before
	 */
	protected function before() {
		if ( is_key_exists_and_callable( $this->funcs, 'before' ) ) {
			call_user_func( $this->funcs->before );
		}
	}

	/**
	 * hook
	 *
	 * @method hook
	 */
	protected function hook() {
		if ( $this->files->hook && is_file( $this->files->hook ) && is_key_exists_and_callable( $this->funcs, 'hook' ) ) {
			call_user_func( $this->funcs->hook, $this->files->hook );
		}
	}

	/**
	 * view
	 *
	 * @method view
	 * @return {string} content
	 */
	protected function view() {
		if ( $this->files->view && is_file( $this->files->view ) && is_key_exists_and_callable( $this->funcs, 'view' ) ) {
			return call_user_func( $this->funcs->view, $this->files->view );
		}
	}

	/**
	 * render
	 *
	 * @method render
	 * @param {string} $context
	 */
	protected function render( $content ) {
		if ( is_key_exists_and_callable( $this->funcs, 'render' ) ) {
			call_user_func( $this->funcs->render, $content );
		}
	}

	/**
	 * huck
	 *
	 * @method huck
	 */
	protected function huck() {
		if ( $this->files->huck && is_file( $this->files->huck ) && is_key_exists_and_callable( $this->funcs, 'huck' ) ) {
			call_user_func( $this->funcs->huck, $this->files->huck );
		}
	}

	/**
	 * after
	 *
	 * @method after
	 */
	protected function after() {
		if ( is_key_exists_and_callable( $this->funcs, 'after' ) ) {
			call_user_func( $this->funcs->after );
		}
	}

	/**
	 * ___before___
	 *
	 * @method ___before___
	 */
	protected function ___before___() {
		
	}

	/**
	 * ___hook___
	 *
	 * @method ___hook___
	 * @param {string} $hook_file
	 * @return {string} cache file path
	 */
	protected function ___hook___( $hook_file ) {
		include $hook_file;

		$cache_file = null;
		if ( $this->cache_time ) {
			if ( $this->_isCacheRead( $this->cache_time, $hook_file, $this->cache_uri, $cache_file ) ) {
				$this->cache_read = true;
			}
			$this->cache_file = $cache_file;
		}
		return $cache_file;
	}

	/**
	 * ___view___
	 *
	 * @method ___view___
	 * @param {string} $viewFile
	 * @return {string} content
	 */
	protected function ___view___( $viewFile ) {
		ob_start();
		if ( isset( $this->cache_read ) && $this->cache_read ) {
			include $this->cache_file;
		}
		else {
			include $viewFile;
		}

		if ( $this->layout ) {
			$this->content_for_layout = ob_get_clean();
			ob_start();
			include Atto::makeAccessPath( array( Atto::dir_layout(), '', Atto::dir_atto_layout() ), array( $this->layout, 'default' ), array( '', '.php' ) );
		}
		return ob_get_clean();
	}

	/**
	 * ___render___
	 *
	 * @method ___render___
	 * @param {string} $content
	 */
	protected function ___render___( $content ) {
		if ( $content && is_string( $content ) ) {

			if ( $this->functions_for_render
					&& ( is_array( $this->functions_for_render ) || $this->functions_for_render instanceof ArrayObject ) ) {

				$before = $this->cache_read === true ? array( ) : array_filter( (array) $this->functions_for_render, create_function( '$v', 'return $v[\'after_cache\'] !== true;' ) );
				$after = array_filter( (array) $this->functions_for_render, create_function( '$v', 'return $v[\'after_cache\'] === true;' ) );
			}
			else {
				$before = $after = array( );
			}

			if ( 0 < count( $before ) || 0 < count( $after ) ) {

				$doc = new DOMDocument();
				$doc->loadHTML( $content );

				//before cache
				foreach ( $before as $fn ) {
					call_user_func( $fn, $dom );
				}


				if ( $this->cache_file && !$this->cache_read ) {
					$doc->saveHTMLFile( $this->cache_file );
				}

				//after cache
				foreach ( $before as $fn ) {
					call_user_func( $fn, $dom );
				}


				$content = $doc->saveHTML();
			}
			else {
				if ( $this->cache_file ) {
					file_put_contents( $this->cache_file, $content );
				}
			}
		}

		echo $content;
	}

	/**
	 * ___huck___
	 *
	 * @method ___huck___
	 * @param {string} $huck_file
	 */
	protected function ___huck___( $huck_file ) {
		include $huck_file;
	}

	/**
	 * ___after___
	 *
	 * @method ___after___
	 */
	protected function ___after___() {
		
	}

	/**
	 * isCacheRead
	 *
	 * @method isCacheRead
	 * @param {timestamp} $cache
	 * @param {string} $huckFile
	 * @param {string} &$cacheFile
	 * @return {boolean} is use cache file
	 */
	protected function _isCacheRead( $cache, $hook_file, $cache_uri, &$cache_file ) {
		$cache_dir = dirname( Atto::dir_var_cache() . substr( $hook_file, strlen( Atto::dir_hook() ) ) );
		$cache_file = $cache_dir . DS . $cache_uri;

		if ( !file_exists( $cache_dir ) ) {
			mkdir( $cache_dir, 0755, true );
		}
		elseif ( file_exists( $cache_file ) ) {
			$cache_filetime = filemtime( $cache_file );
			if ( $cache <= $cache_filetime && filemtime( $hook_file ) < $cache_filetime ) {
				return true;
			}
		}
		return false;
	}

}

