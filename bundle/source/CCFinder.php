<?php namespace ClanCats\Core;
/**
 * ClanCats Finder 
 * 
 * The Finder is the CCF autoloader it's a mixture between PSR-2 and PSR-4,
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
 * @copyright 	2010 - 2015 Mario Döring
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
		spl_autoload_register( array( '\\ClanCats\\Core\\CCFinder', 'find' ), true, true );
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
	 *     CCFinder::map( 'Example\MySourceNamespace', '/path/to/the/source/files/' );
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
	 * An alias can overwrite a shadow. This way we can overwrite other classes.
	 *
	 * example:
	 *     CCFinder::alias( 'Foo', '/path/to/my/Foo.php' );
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
	public static function shadow_package( $dir, $namespace, $shadows ) 
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
	 * @param string 	$class
	 * @return bool
	 */
	public static function find( $class ) 
	{	
		// class with or without namespace?
		if ( strpos( $class , '\\' ) !== false ) 
		{
			/*
			 * alias map
			 */
			if ( array_key_exists( $class, static::$aliases ) ) 
			{
				require static::$aliases[$class];
			}
			/* 
			 * normal map
			 */
			elseif ( array_key_exists( $class, static::$classes ) ) 
			{
				require static::$classes[$class];
			}
			/*
			 * try your luck without the map
			 */
			else 
			{	
				$namespace = substr( $class, 0, strrpos( $class, "\\" ) );
				$class_name = substr( $class, strrpos( $class, "\\" )+1 );
				
				if ( !array_key_exists( $namespace, static::$namespaces ) ) 
				{
					return false;
				}
				
				$path = static::$namespaces[$namespace].str_replace( '_', '/', $class_name ).EXT;
				
				if ( !file_exists( $path ) ) 
				{
					return false;
				}
				
				require $path;
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
			/*
			 * alias map
			 */
			if ( array_key_exists( $class, static::$aliases ) ) 
			{
				require static::$aliases[$class];
			}
			/*
			 * check shadows
			 */
			if ( array_key_exists( $class, static::$shadows ) )
			{
				return static::find( static::$shadows[$class] );
			}
			/* 
			 * normal map
			 */
			elseif ( array_key_exists( $class, static::$classes ) ) 
			{
				require static::$classes[$class];
			}
			/*
			 * try your luck without the map
			 */
			else 
			{
				$path = APPPATH.CCDIR_CLASS.str_replace( '_', '/', $class ).EXT;
				
				if ( !file_exists( $path ) ) 
				{
					return false;
				}
				
				require $path ;
			}
		}
		
		/*
		 * run the static init if possible
		 */
		if ( method_exists( $class, '_init' ) ) 
		{
			$class::_init();
		}
		
		return true;
	}
}