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
	 * The migration conifig file
	 *
	 * @var CCConfig
	 */
	protected static $config = null;
	
	/**
	 * Init the migrator directory
	 */
	public static function _init()
	{
		\ClanCats::directories( array( 'migration' => 'database/' ) );
		
		// read the migration configuration
		static::$config = \CCConfig::create( 'migrator', 'json' );
	}
	
	/**
	 * Run all new migration
	 *
	 * @return void
	 */
	public static function migrate()
	{
		foreach( static::unstaged() as $key => $value )
		{
			if ( empty( $value ) )
			{
				continue;
			}
			
			if ( \ClanCats::is_cli() )
			{
				\CCCli::info( 'found new "'.$key.'" migrations.' );
			}
			
			foreach( $value as $time => $path )
			{
				$migration = new static( $path );
				
				// run the migration
				$migration->up();
				
				if ( \ClanCats::is_cli() )
				{
					\CCCli::success( 'migrated '.$migration->name() );
				}
			}
			
			static::$config->set( $key.'.revision', $time );
		}
		
		static::$config->write();
	}
	
	/**
	 * Returns the available migrations
	 *
	 * @return array
	 */
	public static function available()
	{
		$bundles = array_merge( \CCFinder::$bundles, array( 
			'app' => \CCPath::get( '', null, \ClanCats::directory( 'migration' ) ) 
		));
		
		$available = array();
		
		foreach( \CCFinder::$bundles as $name => $path )
		{
			$directory = $path.\ClanCats::directory( 'migration' );
			
			if ( is_dir( $directory ) )
			{
				$available[strtolower($name)] = static::get_migrations( $directory );
			}
		}
		
		return $available;
	}
	
	/**
	 * Returns the available migrations
	 *
	 * @param string 		$path
	 * @return array
	 */
	public static function get_migrations( $path )
	{
		$objects = new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator( $path ), \RecursiveIteratorIterator::SELF_FIRST );
		
		$files = array();
		
		foreach( $objects as $name => $object )
		{
			if ( \CCStr::extension( $name ) == 'php' )
			{
				$files[substr( basename( $name ), 0, strpos( basename( $name ),  '_' ) )] = $name;
			}
		}
		
		return $files;
	}
	
	/**
	 * Returns the unstaged migrations based on the configuration
	 *
	 * @return array
	 */
	public static function unstaged()
	{
		$available = static::available();
		
		foreach( $available as $key => $migrations )
		{
			foreach( $migrations as $time => $migration )
			{
				if ( $time <= static::$config->get( $key.'.revision', 0 ) )
				{
					unset( $available[$key][$time] );
				}
			}
		}
		
		return $available;
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
	}
	
	/**
	 * An migration object
	 *
	 * @var \DB\Migrator_Migration
	 */
	protected $migration = null;
	
	/**
	 * The migration name
	 */
	protected $name = null;
	
	/**
	 * Creates a new migrator instance 
	 *
	 * @param string 		$path 	The path of the migration
	 * @return void
	 */
	protected function __construct( $path )
	{
		$name = \CCStr::cut( substr( basename( $path ), strpos( basename( $path ), '_' )+1 ), '.' );
		$this->name = $name;
		
		$class_name = explode( '_', $name );
		
		foreach( $class_name as $key => $value )
		{
			$class_name[$key] = ucfirst( $value );
		}
		
		$class_name = "Migrations\\".implode( '_', $class_name );
		
		// bind the class to the finder
		\CCFinder::bind( $class_name, $path );
		
		$this->migration = new $class_name;
		
		if ( !$this->migration instanceof Migrator_Migration )
		{
			throw new Exception( 'Invalid migration at path: '.$path );
		}
	}
	
	/**
	 * Returns the name of the migration
	 *
	 * @return string
	 */
	public function name()
	{
		return $this->name;
	}
	
	/**
	 * Migrates the current migration up
	 *
	 * @return void
	 */
	public function up()
	{
		$this->migration->up();
	}
	
	/**
	 * Migrates the current migration down
	 *
	 * @return void
 	 */
	public function down()
	{
		$this->migration->down();
	}
}