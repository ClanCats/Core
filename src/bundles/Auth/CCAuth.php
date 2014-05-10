<?php namespace Auth;
/**
 * Auth interface
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 */
class CCAuth 
{
	/** 
	 * Get an auth handler
	 *
	 * @param string		$name	The auth instance
	 * @return bool
	 */
	public static function handler( $name = null )
	{
		return Handler::create( $name );
	}
	
	/** 
	 * Check if the login is valid
	 *
	 * @param string		$name	The auth instance
	 * @return bool
	 */
	public static function valid( $name = null )
	{
		return Handler::create( $name )->valid();
	}
}