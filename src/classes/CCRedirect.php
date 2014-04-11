<?php namespace Core;
/**
 * ClanCats Redirect
 *
 * @package 		ClanCats-Framework
 * @author     		Mario Döring <mariodoering@me.com>
 * @version 		0.3
 * @copyright 		2010 - 2013 ClanCats GmbH 
 *
 */
class CCRedirect extends CCResponse {
	
	/**
	 * Redirect to
	 */
	public static function to( $uri, $temp = true ) {
	
		if ( $uri == '/' ) {
			$uri = '';
		}
		
		$res = static::create();
		
		$res->status = ( $temp ) ? 303 : 301;		
		$res->header( 'Location', CCUrl::to( $uri ) );
		
		return $res;
	}
	
}