<?php namespace Orbit;
/**
 * Orbit Ship
 * Ship Object
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
	 * create new ship with given data
	 *
	 * @param array 					$data
	 * @return CCOrbit_Ship
	 */
	public static function blueprint( $data, $path ) 
	{
		if ( !is_array( $data ) )
		{
			throw new \InvalidArgumentException( "CCOrbit_Ship::blueprint - first argument has to be an array." );
		}

		$ship = new static();

		$name = $data['name'];
		$namespace = $data['namespace'];

		// check if we have a name if not use dir name
		if ( is_null( $name ) ) {
			$name = basename( $path );
		}

		// get ship namespace
		if ( $namespace === true ) {
			$namespace = $name;
		}

		// try to load other ship class
		if ( is_string( $namespace ) ) 
		{
			// register the namespace
			\CCFinder::bundle( $namespace, $path );

			$class = $namespace.'\\Ship';

			if ( class_exists( $class ) ) 
			{
				$ship = new $class();
			}
		}

		// set the path
		$ship->name = $name;
		$ship->path = $path;
		$ship->namespace = $namespace;

		// assign the data
		foreach( $data as $key => $item ) 
		{
			if ( property_exists( $ship, $key ) ) 
			{
				$ship->$key = $item;
			}
		}

		// check the namespace
		if ( $ship->namespace === false ) 
		{
			if ( is_null( $ship->wake ) ) 
			{
				$ship->wake = 'shipyard/wake'.EXT;
			}
			if ( is_null( $ship->install ) ) 
			{
				$ship->install = 'shipyard/install'.EXT;
			}
			if ( is_null( $ship->uninstall ) ) 
			{
				$ship->uninstall = 'shipyard/uninstall'.EXT;
			}
		} 
		elseif ( is_string( $ship->namespace ) ) 
		{
			if ( is_null( $ship->wake ) ) 
			{
				$ship->wake = 'Ship::wake';
			}
			if ( is_null( $ship->install ) ) 
			{
				$ship->install = 'Ship::install';
			}
			if ( is_null( $ship->uninstall ) ) 
			{
				$ship->uninstall = 'Ship::uninstall';
			}
		}

		return $ship;
	}

	/*
	 * ship name
	 */
	public $path = null;

	/*
	 * ship name
	 */
	public $name = null;

	/*
	 * ship version
	 */
	public $version = "";

	/*
	 * ship description
	 */
	public $description = "";

	/*
	 * ship description
	 */
	public $authors = "";

	/*
	 * ship description
	 */
	public $homepage = "";

	/*
	 * ship namespace
	 */
	public $namespace = true;

	/*
	 * ship wake event
	 */
	public $wake = null;

	/*
	 * ship install event
	 */
	public $install = null;

	/*
	 * ship uninstall event
	 */
	public $uninstall = null;
}