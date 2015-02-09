<?php namespace Orbit;
/**
 * Orbit Ship
 * This is just a data holder
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2015 ClanCats GmbH
 *
 */
class Ship 
{	
	/**
	 * The available ship properties
	 *
	 * @var array
	 */
	protected static $available_properties = array(
		'path',
		'name',
		'version',
		'description',
		'license',
		'authors',
		'homepage',
		'namespace',
		'wake',
		'install',
		'uninstall'
	);
	
	/**
	 * Create an instance from path using the ship inspector
	 *
	 * @param string 			$path
	 * @return Orbit\Ship
	 */
	public static function path( $path )
	{
		$inspector = ShipInspector::path( $path );
		
		$properties = array();
		
		foreach ( static::$available_properties as $key ) 
		{
			$properties[$key] = $inspector->get( $key );
		}
		
		$properties['path'] = substr( $path, strlen( CCROOT ) );
		
		return new static( $properties );
	}
	
	/**
	 * ship file path
	 *
	 * @var string
	 */
	public $path = null;

	/**
	 * ship name
	 *
	 * @var string
	 */
	public $name = null;

	/**
	 * ship version
	 *
	 * @var string
	 */
	public $version = null;

	/**
	 * ship description
	 *
	 * @var string
	 */
	public $description = null;
	
	/**
	 * ship license
	 *
	 * @var string
	 */
	public $license = null;

	/**
	 * ship authors
	 *
	 * @var array
	 */
	public $authors = null;

	/**
	 * ship homepage
	 *
	 * @var string
	 */
	public $homepage = null;

	/**
	 * ship namespace
	 *
	 * @var string
	 */
	public $namespace = true;

	/**
	 * ship wake event
	 *
	 * @var string
	 */
	public $wake = null;

	/**
	 * ship install event
	 *
	 * @var string
	 */
	public $install = null;

	/**
	 * ship uninstall event
	 *
	 * @var string 
	 */
	public $uninstall = null;
	
	/**
	 * Create new ship using properties
	 *
	 * @param array 			$properties
	 */
	public function __construct( array $properties = array() )
	{
		foreach( $properties as $key => $value )
		{
			$this->{$key} = $value;
		}
	}
	
	/**
	 * returns the ships properties as array
	 *
	 * @return array
	 */
	public function properties()
	{
		$properties = array();
		
		foreach( static::$available_properties as $key )
		{
			$properties[$key] = $this->{$key};
		}
		
		return $properties;
	}
}