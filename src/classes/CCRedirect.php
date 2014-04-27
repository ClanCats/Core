<?php namespace Core;
/**
 * ClanCats Redirect
 *
 * @package 			ClanCats-Framework
 * @author     		Mario Döring <mariodoering@me.com>
 * @version 			0.3
 * @copyright 		2010 - 2013 ClanCats GmbH 
 *
 */
class CCRedirect extends CCResponse 
{	
	/**
	 * We forward all CCUrl functions
	 *
	 * @param string 	$name
	 * @param array 		$arguments
	 */
	public static function __callStatic( $name, $arguments )
	{
		// create new response object
		$response = static::create();
		
		// set the response status to 303
		$response->status = 303;
		
		// add the location header
		$response->header( 'Location', call_user_func_array( "\\CCUrl::".$name, $arguments ) );
		
		// return the response object
		return $response;
	}
}