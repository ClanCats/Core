<?php namespace Orbit;
/**
 * Orbit handler
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2015 ClanCats GmbH
 *
 */
class CCOrbit 
{
	/**
	 * We support at the momen only one orbit station. 
	 *
	 * @var Orbit\Station
	 */
	private static $station = null;
	
	/**
	 * Get an instance of the orbit station
	 *
	 * @param string 				$manager
	 * @return Orbit\Station
	 */
	public static function station( $manager = null )
	{
		if ( !is_null( static::$station ) )
		{
			return static::$station;
		}
		
		return static::$station = new Station;
	}
}