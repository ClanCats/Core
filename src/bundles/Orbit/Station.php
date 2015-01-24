<?php namespace Orbit;
/**
 * Station Manager
 * The station handles all plugins / ships. From the installation to the initialisation.
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.1
 * @copyright 	2010 - 2015 ClanCats GmbH
 *
 */
class Station
{	
	/**
	 * The writable orbit config file
	 *
	 * @var CCConfig
	 */
	protected $orbit_config = null;
	
	/**
	 * Array of currently loaded ships
	 *
	 * @var array[Orbit\Ship]
	 */
	protected $ships = array();
	
	/**
	 * The station constructor
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->orbit_config = \CCConfig::create( 'orbit', 'json' );
	}
	
	/**
	 * Wakes the entire station
	 *
	 * @param Ship			$ship
	 * @return void
	 */
	public function wake( $ship )
	{
		
	}
	
	public function enter(  )
	{
		
	}
	
	public function install()
	{
		
	}
	
	public function uninstall()
	{
		
	}
}