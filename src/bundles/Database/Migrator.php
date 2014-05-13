<?php namespace DB;
/**
 * Database mirgrations
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 */
class Migrator 
{
	/**
	 * Init the migrator directory
	 */
	public static function _init()
	{
		\ClanCats::directories( array( 'migration' => 'database/' ) );
	}
	/**
	 * Create new migration class
	 * 
	 * @param string			$name
	 * @return void
	 */
	public static function shipyard( $name )
	{
		$file = \CCPath::get( $name, \ClanCats::directory( 'migration' ), EXT );
		
		$directory = dirname( $file ).'/';
		$file = basename( $file );
		
		_d( $file );
	}
}