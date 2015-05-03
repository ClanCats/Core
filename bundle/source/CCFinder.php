<?php
/**
 * ClanCats Finder 
 * 
 * The Finder is the CCF autoloader it's a mixture between PSR-0 and PSR-4,
 * that might sound really freaking strange and bad in first place but makes
 * development a bit more comfortable.
 * 
 * A php namespace defines a path to source files inside that namespace are 
 * loaded using the `underscore` seperator.
 * 
 *     \Example\MyBundle\Driver_Interface 
 * 
 * My Philosophy is to use static classes where it makes sense and I think
 * the autoloader is something that is and stays a singleton. 
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		3.0
 * @copyright 	2010 - 2015 ClanCats GmbH
 *
 */
class CCFinder 
{	
	/**
	 * The mapped classes
	 *
	 * @var array
	 */
	private static $classes = array();
	
	/** 
	 * The mapped aliases
	 * 
	 * @var array
	 */
	private static $aliases = array();
	
	/** 
	 * The mapped shadows
	 * 
	 * @var array
	 */
	private static $shadows = array();
	
	/**
	 * The mapped namespaces
	 *
	 * @var array
	 */
	private static $namespaces = array();
	
	/**
	 * The mapped bundles
	 *
	 * @var array
	 */
	private static $bundles = array();
	
	/**
	 * Register the autoloader
	 *
	 * @return void
	 */
	public static function register() 
	{
		spl_autoload_register( array( '\\CCFinder', 'find' ), true, true );
	}
	
	/**
	 * Register a CCF bundle
	 * A bundle is a CCF style package with sources, configuration, views etc.
	 *
	 *     CCFinder::bundle( 'Example\MyPackage', 'path/to/my/package/' );
	 *
	 * @param string 			$name
	 * @param path 				$path
	 * @return void
	 */
	public static function bundle( $name, $path ) 
	{
		static::$bundles[$name] = $path;
		static::map( $name, $path.CCDIR_SOURCE );
	}
	
	/**
	 * Register a source namespace
	 * A source namespace is the PSR-4 part of the autoloader.
	 *
	 *     CCFinder::map( 'Example\MySourceNamespace', 'path/to/the/source/files/' );
	 *
	 * @param string|array 		$name
	 * @param path 				$path
	 * @return void
	 */
	public static function map( $name, $path = null ) 
	{
		if ( is_null( $path ) && is_array( $name ) )
		{
			foreach( $name as $namepsace => $path )
			{
				static::map( $namespace, $path );
			}
		}
		else { static::$namespaces[$name] = $path; }
	}
	
	/**
	 * Register a shadow class 
	 * A shadow class is a global class and gets liftet to the global namespace.
	 * 
	 * Some\Longer\Namespace\Foo::bar() -> Foo::bar()
	 *
	 * exmpale:
	 *     CCFinder::shadow( 'Foo', 'Some\Longer\Namespace', 'my/path/to/Foo.php' );
	 *
	 * @param string			$name		The shadow
	 * @param string			$namespace	The real class namespace
	 * @param string 			$path		The path of the php file
	 * @return void
	 */
	public static function shadow( $name, $namespace, $path = null ) 
	{
		static::$shadows[$name] = $class = $namespace."\\".$name;
		
		if ( !is_null( $path ) )
		{
			static::bind( $name, $class );
		}
	}
	
	/**
	 * Register one or more aliases
	 * An alias can overwrite a shadow. This way we can overwrite and extend 
	 * Core classes from our app.
	 *
	 * example:
	 *     // When requesting the CCSession shadow the autoloader
	 *     // is now going to load your custom class instead.
	 *     // Inside your class you can extend the original CCSession class.
	 *     CCFinder::alias( 'CCSession', 'path/to/my/custom/CCSession.php' );
	 *
	 * @param string|array 		$name
	 * @param path 				$path
	 * @return void
	 */
	public static function alias( $name, $path = null ) 
	{
		if ( is_null( $path ) && is_array( $name ) )
		{
			foreach( $name as $alias => $path )
			{
				static::alias( $alias, $path );
			}
		}
		else { static::$aliases[$name] = $path; }
	}
	
	/**
	 * Bind one or more classes to the autoloader
	 *
	 * Sometimes you need to bind a class manually:
	 *     CCFinder::bind( 'OldPHPClass', 'path/to/old.class.php' );
	 *
	 * @param string|array 		$name
	 * @param path 				$path
	 * @return void
	 */
	public static function bind( $name, $path = null ) 
	{
		if ( is_null( $path ) && is_array( $name ) )
		{
			foreach( $name as $class => $path )
			{
				static::bind( $class, $path );
			}
		}
		else { static::$classes[$name] = $path; }
	}
		
	
	/** 
	 * This simply adds some classes with a prefix 
	 * 
	 * @param string 	$name
	 * @param path 		$path
	 * @return void
	 */
	public static function package( $dir, $classes ) 
	{
		foreach( $classes as $name => $path ) 
		{
			static::$classes[$name] = $dir.$path;
		}
	}
	
	/** 
	 * This simply adds some shadows with a prefix 
	 * 
	 * @param string 	$name
	 * @param path 		$path
	 * @return void
	 */
	public static function shadowPackage( $dir, $namespace, $shadows ) 
	{
		foreach( $shadows as $name => $path ) 
		{
			static::$shadows[$name] = $class = $namespace."\\".$name;
			static::$classes[$class] = $dir.$path;
		}
	}
	
	/**
	 * Autoloading handler
	 *
	 * @param string 			$class
	 * @return bool
	 */
	public static function find( $class ) 
	{	
		// to safe a really small amount of performance we split 
		// the autoloading between classes with and without namespace.
		if ( strpos( $class , '\\' ) !== false ) 
		{
			// we have to check if there is manual bind for defined
			if ( isset( static::$classes[$class] ) )
			{
				require static::$classes[$class];
			}
			// otherwise we load the class automatically
			else 
			{	
				$namespace = substr( $class, 0, strrpos( $class, "\\" ) );
				$className = substr( $class, strlen( $namespace )+1 );
				
				// if the namepsace is not mapped return false
				if ( !isset( static::$namespaces[$namespace] ) ) 
				{
					return false;
				}
				
				// build the path string and require the file 
				if ( !file_exists( $file = static::$namespaces[$namespace].str_replace( '_', '/', $class_name ).EXT ) )
				{
					return false;
				}
				
				require $file;
			}
			
			/*
			 * check if we need to create a shadow aka an alias
			 */
			if ( in_array( $class, static::$shadows ) )
			{
				$shadow = array_search( $class, static::$shadows );
				
				if ( !class_exists( $shadow, false ) && !array_key_exists( $shadow, static::$aliases ) )
				{
					class_alias( $class, $shadow ); 
				}
			}
		}
		else 
		{
			// check the alias map for the class
			if ( isset( static::$aliases[$class] ) ) 
			{
				require static::$aliases[$class];
			}
			// check the if a shadow exists
			elseif ( isset( static::$shadows[$class] ) )
			{
				return static::find( static::$shadows[$class] );
			}
			// check the normal binding
			elseif ( isset( static::$classes[$class] ) ) 
			{
				require static::$classes[$class];
			}
			// otherwise we load the class from the app namespace
			else 
			{
				if ( !file_exists( $file = CCPATH_APP.CCDIR_SOURCE.str_replace( '_', '/', $class ).EXT ) )
				{
					return false;
				}
				
				require $file;
			}
		}
		
		/*
		 * run the static init if possible
		 */
		if ( method_exists( $class, '__staticConstruct' ) ) 
		{
			$class::__staticConstruct();
		}
		
		return true;
	}
}