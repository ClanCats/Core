<?php namespace Orbit;
/**
 * Station Manager
 * 
 * The station manager handles installed ships and the orbit
 * map file.
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
	 * Array of currently loaded ships
	 *
	 * @var array[Orbit\Ship]
	 */
	private $ships = null;
	
	/**
	 * Get the orbit store 
	 *
	 * @return CCConfig
	 */
	protected function orbit_store()
	{
		return \CCConfig::create( 'orbit', 'json' );
	}
	
	/**
	 * Reload the installed ships
	 *
	 * @return void
	 */
	protected function reload_installed()
	{
		$this->ships = array();
		
		// add the ships to the station
		foreach( $this->orbit_store()->installed as $name => $ship )
		{
			$this->ships[$name] = new Ship( $ship );
		}
	}
	
	/**
	 * Get all installed orbit ships
	 *
	 * @return array[Orbit\Ship]
	 */
	public function installed()
	{
		if ( is_null( $this->ships ) )
		{
			$this->reload_installed();
		}
		
		return $this->ships;
	}
	
	/**
	 * Check if the given ship is already installed
	 *
	 * @param Orbit\Ship 			$ship
	 * @return bool
	 */
	public function is_installed( Ship $ship )
	{
		return (bool) isset( $this->ships[$ship->name] );
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
	public function install( Ship $ship )
	{
		if ( !isset( $ship->path ) || empty( $ship->path ) )
		{
			throw new Exception( "Cannot install ship without a path." );
		}
		
		// check if the ship is already installed
		if ( $this->is_installed( $ship ) )
		{
			throw new Exception( "The given ship is alredy installed." );
		}
		
		// we might have an installation proccess to execute
		if ( $ship->install !== null )
		{
			if ( $event = $this->event_type( $ship->install ) === false )
			{
				throw new Exception( "Unkown install event type: ".$ship->install );
			}
			
			// we might have to load the namespace to be able 
			// to execute the installation script
			if ( $ship->bundle )
			{
				\CCFinder::bundle( $ship->bundle, $ship->path );
			}
			
			if ( $event === 'static' )
			{
				list( $class, $method ) = explode( '::', $ship->install );
				call_user_func( array( $ship->bundle.$class, $method ), $ship );
			}
			elseif ( $event === 'instance' )
			{
				list( $class, $method ) = explode( '->', $ship->install );
				$instance = new $ship->bundle.$class;
				call_user_func( array( $instance, $method ), $ship );
			}
			elseif ( $event === 'file' )
			{
				require $ship->path.$this->install;	
			}
		}
		
		// we write all ship data in our config file
		$this->orbit_config->set( 'installed.'.$ship->name, $ship->properties() );
		
		// write the configuration
		$this->orbit_config->write();
		
		// regenerate the map file
		$this->create_map();
	}
	
	public function uninstall()
	{
		
	}
	
	/**
	 * Write the orbit map down to the storage
	 *
	 * @return void
	 */
	public function write_map()
	{
		CCStorage::write( 'orbit/map'.EXT, $this->create_map() );
	}
	
	/**
	 * Regenerate the orbit mapping file
	 *
	 * @return void
	 */
	public function create_map()
	{
		foreach( $this->orbit_config->installed as $path => $data )
		{
			
		}
		
		return "<?php die('foo');";
	}
}