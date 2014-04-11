<?php namespace CCConsole; use CCCli;
/**
 * Console Controller 
 * run a application script
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 */
class shipyard extends \CCConsoleController 
{
	/**
	 * return an array with information about the script
	 */
	public function help() 
	{
		return array(
			'name'	=> 'Shipyard',
			'desc'	=> 'The shipyard is an helper that generates classes, configuration files, ships and other stuff for you.',
			'actions'	=> array(
				'class'	=> "class <class>\n   class <namespace>::<class>",
				'ship'	=> 'create <ship name> <namespace>',
			),
		);
	}
	
	/**
	 * generate an class
	 *
	 * exmample:
	 * run shipyard::class <class>
	 * run shipyard::class <namespace>::<class>
	 *
	 * @param array 		$params 
	 * @return void
	 */
	public function action_class( $params ) 
	{	
		// params
		$name = $params[0];
		
		// get name if we dont have one
		while( !$name ) 
		{
			$name = CCCli::read( 'Please enter the class name: ' );
		}
		
		$namespace 	= null;
		$class 		= $name;
		$author 		= \CCConfig::create( 'shipyard' )->get( 'defaults.authors' );
		$package		= \CCConfig::create( 'shipyard' )->get( 'defaults.package' );
		$copyright	= \CCConfig::create( 'shipyard' )->get( 'defaults.copyright' );
		$version		= \CCConfig::create( 'shipyard' )->get( 'defaults.version' );
		
		// get namespace from the param
		if ( strpos( $name, '::' ) !== false )
		{
			$namespace = explode( '::', $name ); $class = $namespace[1]; $namespace = $namespace[0];
			
			// try to get the ship from namespace
			if ( $ship = \CCOrbit::ship_by_namespace( $namespace ) )
			{
				$package = $ship->name;
				$version = $ship->version;
				$author = $ship->authors;
			}
		}
		
		// resolve the path
		if ( !$path = \CCPath::classes( str_replace( '_', '/', $name ), EXT ) )
		{
			CCCli::line( 'Could not resolve the path. Check if the namespace is registered.', 'red' ); return;
		}
		
		// create forge instance
		$forge = new \CCForge_Php( $namespace );
		
		
		// add header
		$forge->comment( $this->make_comment_header( $class, array(
			'package'	=> $package,
			'authors'	=> $author,
			'version'	=> $version,
			'copyright'	=> $copyright
		)));
		
		
		// add class
		$forge->closure( 'class '.$class, function() 
		{	
			$forge = new \CCForge_Php;
			
			// add init function
			echo $forge->closure( 'public static function _init()', '// Do stuff', 
				"static class initialisation\n".
				"@return void"
			);	
		});
		
		// check before
		if ( file_exists( $path ) )
		{
			if ( !CCCli::confirm( "The class already exists. Do you wish to overwrite it?", true ) ) 
			{
				return;
			}
		}
		
		// write file
		\CCFile::write( $path, $forge );
	}
	
	/**
	 * generate an controller
	 *
	 * exmample:
	 * run shipyard::controller <controller>
	 * run shipyard::controller <controller> <parent_class>
	 * run shipyard::controller <namespace>::<controller>
	 *
	 * @param array 		$params 
	 * @return void
	 */
	public function action_controller( $params ) 
	{	
		// params
		$name = $params[0];
		$parent = $params[1];
		
		// get name if we dont have one
		while( !$name ) 
		{
			$name = CCCli::read( 'Please enter the controller name: ' );
		}
		
		// check if there is already controller appended
		if ( substr( $name, ( strlen( 'Controller' ) * -1 ) ) != 'Controller' )
		{
			$name .= 'Controller';
		}
		
		// default parent
		if ( !$parent )
		{
			$parent = "\\CCController";
		}
		
		$namespace 	= null;
		$class 		= $name;
		$author 		= \CCConfig::create( 'shipyard' )->get( 'defaults.authors' );
		$package		= \CCConfig::create( 'shipyard' )->get( 'defaults.package' );
		$copyright	= \CCConfig::create( 'shipyard' )->get( 'defaults.copyright' );
		$version		= \CCConfig::create( 'shipyard' )->get( 'defaults.version' );
		
		// get namespace from the param
		if ( strpos( $name, '::' ) !== false )
		{
			$namespace = explode( '::', $name ); $class = $namespace[1]; $namespace = $namespace[0];
			
			// try to get the ship from namespace
			if ( $ship = \CCOrbit::ship_by_namespace( $namespace ) )
			{
				$package = $ship->name;
				$version = $ship->version;
				$author = $ship->authors;
			}
		}
		
		// resolve the path
		if ( !$path = \CCPath::controllers( str_replace( '_', '/', $name ), EXT ) )
		{
			CCCli::line( 'Could not resolve the path. Check if the namespace is registered.', 'red' ); return;
		}
		
		// create forge instance
		$forge = new \CCForge_Php( $namespace );
		
		
		// add header
		$forge->comment( $this->make_comment_header( $class, array(
			'package'	=> $package,
			'authors'	=> $author,
			'version'	=> $version,
			'copyright'	=> $copyright
		)));
		
		
		// add class
		$forge->closure( 'class '.$class.' extends '.$parent, function() use( $class )
		{	
			$forge = new \CCForge_Php;
			
			// add init function
			echo $forge->closure( 'public function wake()', '// Do stuff', 
				"controller wake\n\n".
				"@return void|CCResponse"
			);
			
			echo $forge->line(2);
			
			// add init function
			echo $forge->closure( 'public function action_index()', 'echo "'.$class.'";', 
				"index action\n\n".
				"@return void|CCResponse"
			);
			
			echo $forge->line(2);
			
			// add init function
			echo $forge->closure( 'public function sleep()', '// Do stuff', 
				"controller sleep\n\n".
				"@return void"
			);
		});
		
		// check before
		if ( file_exists( $path ) )
		{
			if ( !CCCli::confirm( "The controller already exists. Do you wish to overwrite it?", true ) ) 
			{
				return;
			}
		}
		
		// write file
		\CCFile::write( $path, $forge );
	}

	/**
	 * generate ships
	 *
	 * exmample:
	 * run shipyard::ship <name>
	 * run shipyard::ship <name> <namespace>
	 * run shipyard::ship <name> --no-namespace
	 *
	 * @param array 		$params 
	 * @return void
	 */
	public function action_ship( $params ) 
	{	
		// params
		$name = $params[0];
		$namespace = $params[1];
		
		// get name if we dont have one
		while( !$name ) 
		{
			$name = CCCli::read( 'Please enter the ship name: ' );
		}
		
		// set namespace 
		if ( !$namespace ) 
		{
			$namespace = $name;
		}
		
		if ( $params['-no-namespace'] ) 
		{
			$namespace = false;
		}
		
		// create blueprint
		$blueprint = array(
			'name'			=> $name,
			'version'		=> \CCConfig::create( 'shipyard' )->get( 'defaults.version' ),
			'description'	=> \CCConfig::create( 'shipyard' )->get( 'defaults.description' ),
			'homepage'		=> \CCConfig::create( 'shipyard' )->get( 'defaults.homepage' ),
			'keywords'		=> \CCConfig::create( 'shipyard' )->get( 'defaults.keywords' ),
			'license'		=> \CCConfig::create( 'shipyard' )->get( 'defaults.license' ),
			
			'authors'		=> \CCConfig::create( 'shipyard' )->get( 'defaults.authors' ),
						
			'namespace'		=> $namespace,
		);
		
		$target = ORBITPATH.$name.'/';
		
		// check if the module is in our orbit path
		if ( is_dir( $target ) ) 
		{
			if ( !CCCli::confirm( "there is already a ship with this name. do you want to overwrite?", true ) ) 
			{
				return;
			}
		}
		
		// create file
		\CCJson::write( $target.'blueprint.json', $blueprint, true );
		
		$ship = \CCOrbit_Ship::blueprint( $blueprint, $target );
		
		// create event files
		if ( $namespace ) 
		{
			// create forge instance
			$forge = new \CCForge_Php( $namespace );
			
			// add header
			$forge->comment( $this->make_comment_header( $ship->name.' ship', array(
				'package'	=> $ship->name,
				'authors'	=> $ship->authors,
				'version'	=> $ship->version,
				'copyright'	=> \CCConfig::create( 'shipyard' )->get( 'defaults.copyright' ),
			)));
			
			// add class
			$forge->closure( 'class Ship extends \CCOrbit_Ship', function() {
				
				$forge = new \CCForge_Php;
				
				// add init function
				echo $forge->closure( 'public function wake()', '// Do stuff', 
					"initialize the ship\n\n".
					"@return void"
				);
				
				echo $forge->line(2);
				
				// add init function
				echo $forge->closure( 'public function install()', '// Do stuff', 
					"install the ship\n\n".
					"@return void"
				);
				
				echo $forge->line(2);
				
				// add init function
				echo $forge->closure( 'public function unsintall()', '// Do stuff', 
					"uninstall the ship\n\n".
					"@return void"
				);
			});
			
			\CCFile::write( $target.CCDIR_CLASS.'Ship'.EXT, $forge );
			
		} else {
			
			// create forge instance
			$forge = new \CCForge_Php();
			
			// add header
			$forge->comment( $this->make_comment_header( $ship->name, array(
				'package'	=> $ship->name,
				'authors'	=> $ship->authors,
				'version'	=> $ship->version,
				'copyright'	=> \CCConfig::create( 'shipyard' )->get( 'defaults.copyright' ),
			)));
			
			\CCFile::write( $target.'shipyard/wake'.EXT, $forge );
			\CCFile::write( $target.'shipyard/install'.EXT, $forge );
			\CCFile::write( $target.'shipyard/uninstall'.EXT, $forge );
		}
		
		// sucess
		CCCli::line( "'".$name."' succesfully created under: ".$target, 'green' );
	}
	
	/**
	 * generates an file header string
	 *
	 * @param string		$title
	 * @param array 		$data
	 * @return string
	 */
	public function make_comment_header( $title, $data = array() ) {
		
		// get author
		$authors = \CCArr::get( 'authors', $data, \CCConfig::create( 'shipyard' )->get( 'defaults.authors' ) );
		
		// author
		if ( is_array( $authors ) )
		{
			foreach( $authors as $person ) 
			{
				$author_str .= $person['name']." ";
				if ( array_key_exists( 'email', $person ) ) 
				{
					$author_str .= "<".$person['email'].">";
				}
				$author_str .= ", ";
			}
			$author_str = substr( $author_str, 0, -2 );
		}
		
		return "$title\n".
			"*\n".
			"\n".
			"@package       ".\CCArr::get( 'package', $data, \CCConfig::create( 'shipyard' )->get( 'defaults.package' ) )."\n".
			"@author        ".$author_str."\n".
			"@version       ".\CCArr::get( 'version', $data, \CCConfig::create( 'shipyard' )->get( 'defaults.version' ) )."\n".
			"@copyright     ".\CCArr::get( 'copyright', $data, \CCConfig::create( 'shipyard' )->get( 'defaults.copyright' ) )."\n";
	}
}