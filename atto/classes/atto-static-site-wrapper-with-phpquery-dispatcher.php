<?php

/**
 * AttoStaticSiteWrapperWithPhpqueryDispatcher
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
class AttoStaticSiteWrapperWithPhpqueryDispatcher extends AttoStaticSiteWrapperDispatcher {

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

				$before = $this->cache_read === true ? array( ) : array_filter( (array) $this->functions_for_render,
																	create_function( '$v', 'return $v[\'after_cache\'] !== true;' ) );
				$after = array_filter( (array) $this->functions_for_render,
						   create_function( '$v', 'return $v[\'after_cache\'] === true;' ) );
			}
			else {
				$before = $after = array( );
			}

			if ( 0 < count( $before ) || 0 < count( $after ) ) {

				$pq = phpQuery::newDocument( $content );

				//before cache
				foreach ( $before as $fn ) {
					$pq = call_user_func( $fn, $pq );
				}


				if ( $this->cache_file && !$this->cache_read ) {
					file_put_contents( $this->cache_file, $pq );
				}

				//after cache
				foreach ( $before as $fn ) {
					$pq = call_user_func( $fn, $pq );
				}


				$content = $pq;
			}
			else {
				if ( $this->cache_file ) {
					file_put_contents( $this->cache_file, $content );
				}
			}
		}

		echo $content;
	}

}

