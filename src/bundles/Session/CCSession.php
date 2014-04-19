<?php namespace Session;
/**
 * Session handler
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 */
class CCSession 
{
	/**
	 * Get an instance of a session manager
	 *
	 * @param string 			$manager
	 * @return Session\Manager
	 */
	public static function manager( $manager = null )
	{
		return Manager::create( $manager );
	}
	
	/**
	 * Get a value from the session
	 *
	 * @param string 			$manager
	 * @return Session\Manager
	 */
	public static function get( $key, $default, $manager = null )
	{
		return Manager::create( $manager )->get( $key, $default );
	}
}