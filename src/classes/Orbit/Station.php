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
		
		// reload loaded ships
		$this->reload_loaded_ships();
	}
	
	/**
	 *  Reload the installed ships
	 *
	 * @return void
	 */
	protected function reload_loaded_ships()
	{
		$this->ships = array();
		
		// add the ships to the station
		foreach( $this->orbit_config->installed as $path => $ship )
		{
			$this->ships[ $path ] = new Ship( $ship );
		}
	}
	
	/**
	 * Map the ships namespace
	 *
	 * @param Orbit\Ship 			$ship
	 * @return void
	 */
	public function map( $ship )
	{
		if ( $ship->namespace )
		{
			\CCFinder::map( $ship->namespace, CCROOT.$ship->path );
		}
	}
	
	/**
	 * Build the map string
	 *
	 * @param Orbit\Ship			$ship
	 * @return string
	 */
	protected function build_map_item( $ship )
	{
		$buffer = "";
		
		if ( $ship->namespace )
		{
			$buffer = '\\CCFinder::map( "'.$ship->namespace.'", CCROOT."'.$ship->path.'" );'."\n";
		}
		
		
	}
	
	/**
	 * Parse the event type
	 *
	 * @param string 			$string
	 * @return string
	 */ 
	protected function event_type( $string )
	{
		if ( strpos( $string, '::' ) !== false )
		{
			return 'static';
		}
		elseif( strpos( $string, '->' ) !== false )
		{
			return 'instance';
		}
		elseif( strpos( $string, EXT ) !== false )
		{
			return 'file';
		}
		
		return false;
	}
	
	/**
	 * Install a ship
	 *
	 * @param Orbit\Ship 			$ship
	 * @return void
	 */
	public function install( $ship )
	{
		if ( !isset( $ship->path ) || empty( $ship->path ) )
		{
			throw new Exception( "Cannot install ship without a path." );
		}
		
		// we might have an installation proccess to execute
		if ( $ship->install !== null )
		{
			if ( $event = $this->event_type( $ship->install ) === false )
			{
				throw new Exception( "Unkown install event type: ".$ship->install );
			}
			
			if ( $event === 'static' )
			{
				
			}
		}
		
		// we write all ship data in our config file
		$this->orbit_config->set( 'installed.'.$ship->path, $ship->properties() );
		
		// write the configuration
		$this->orbit_config->write();
		
		// regenerate the map file
		$this->create_map();
	}
	
	public function uninstall()
	{
		
	}
	
	/**
	 * Regenerate the orbit mapping file
	 *
	 * @return void
	 */
	public function create_map()
	{
		foreach( $this->orbit_config->installed as $path => $data )
		{}
		
		return "<?php die('foo');";
	}
}