<?php namespace Core;
/**
 * Asset handler 
 * This is helper your front end stuff like js file, images etc..
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 */
class CCAsset 
{	
	/**
	 * instance holder
	 *
	 * @var array
	 */
	protected static $instances = array();
	
	/**
	 * default instance
	 * 
	 * @var string
	 */
	protected static $_default = 'main';
	
	/**
	 * The macros holder
	 *
	 * @var array
	 */
	protected static $_macros = array();
	
	/**
	 * Register an assets macro 
	 * 
	 * @param string			$key
	 * @param callback		$callback
	 * @return void
	 */
	public static function macro( $key, $callback )
	{
		static::$_macros[$key] = $callback; 
	}
	
	/**
	 * Static init
	 * Load the inital macros
	 *
	 * @return void
	 */
	public static function _init()
	{
		// default tags
		static::macro( '_', function( $tag ) 
		{
			return $tag;
		});
		
		// stylesheets
		static::macro( 'css', function( $file, $name = null ) 
		{
			return '<link type="text/css" rel="stylesheet" href="'.static::uri( $file, $name ).'" />';
		});
		
		// js scripts
		static::macro( 'js', function( $file, $name = null ) 
		{
			return '<script type="text/javascript" src="'.static::uri( $file, $name ).'"></script>';
		});
		
		// less scripts
		static::macro( 'less', function( $file, $name = null ) 
		{
			return '<link type="text/css" rel="stylesheet/less" href="'.static::uri( $file, $name ).'" />';
		});
		
		// images
		static::macro( 'img', function( $file, $name = null ) 
		{
			return '<img src="'.static::uri( $file, $name ).'" />';
		});
		
		// open graph
		static::macro( 'og', function( $tags, $content = null ) 
		{
			if ( !is_array( $tags ) )
			{
				$tags = array( $tags => $content );
			}
			
			$buffer = "";
			
			foreach( $tags as $key => $content )
			{
				$buffer .= '<meta property="og:'.$key.'" content="'.$content.'" />';
			}
			
			return $buffer;
		});
	}
	
	/**
	 * check for macros and execute them
	 *
	 * @param string 	$name
	 * @param array 		$arguments
	 * @return mixed
	 */
	public static function __callStatic( $name, $arguments )
	{
		if ( !array_key_exists( $name, static::$_macros ) )
		{
			throw new \BadMethodCallException( "CCAsset::".$name." - No method or macro found." );
		}
		
		return call_user_func_array( static::$_macros[$name], $arguments );
	}

	/** 
	 * get the uri to an asset
	 *
	 * @param string		$uri
	 * @param string		$name 
	 * @return string
	 */
	public static function uri( $uri, $name = null ) 
	{
		if ( strpos( $uri, '//' ) === false ) 
		{	
			if ( substr( $uri, 0, 1 ) != '/' ) 
			{
				$uri = CCUrl::to( static::holder( $name )->path.$uri );
			}
			else 
			{
				$uri = CCUrl::to( substr( $uri, 1 ) );
			}
		}
		return $uri;
	}

	/**
	 * Get an asset holder
	 * The assets are splittet by container aka holders you assign them a path.
	 *
	 * @param string			$name
	 * @return CCAsset
	 */
	public static function holder( $name = null ) 
	{	
		if ( !isset( $name ) ) 
		{
			$name = static::$_default;
		}
		
		if ( !isset( static::$instances[$name] ) ) 
		{
			 static::$instances[$name] = new static();
		}
		
		return static::$instances[$name];
	}
	
	/** 
	 * Add an asset to a holder.
	 * This method checks the file extension to order the assets in the
	 * container. 
	 *
	 * @param string			$item	This can be an .js .css file etc.
	 * @param string 		$name
	 * @return void
	 */
	public static function add( $item, $name = null ) 
	{
		// if its just a simple tag
		if ( strpos( $item, "<" ) !== false ) 
		{
			static::holder( $name )->assets['_'][] = $item;
		}
		// a file path
		else 
		{
			static::holder( $name )->assets[CCStr::extension( $item )][] = $item;
		}
	}
	
	/**
	 * Get assets by type from an holder
	 *
	 * @param string		$extension
	 * @param string		$name
	 */
	public static function get( $extension = null, $name = null ) 
	{
		if ( is_null( $extension ) )
		{
			return static::holder( $name )->assets;
		}
		
		$assets = static::holder( $name )->assets;
		
		if ( !array_key_exists( $extension, $assets ) )
		{
			return array();
		}
		
		return $assets[$extension];
	}
	
	/**
	 * Get assets code by type from an holder
	 *
	 * @param string		$extension
	 * @param string		$name
	 */
	public static function code( $extension = null, $name = null ) 
	{
		$buffer = "";
		
		foreach( static::get( $extension, $name ) as $item )
		{
			$buffer .= call_user_func( 'CCAsset::'.$extension, $item, $name );
		}
		
		return $buffer;
	}
	
	/**
	 * Clear an asset holder
	 * Delete all containing assets
	 * 
	 * @param string 		$ext
	 * @param string			$name
	 * @return void
	 */
	public static function clear( $ext, $name = null ) 
	{
		static::holder( $name )->assets[$ext][] = array();
	}
		
	/**
	 * get the entire output from an asset holder
	 *
	 * @param string 		$name
	 * @return string
	 */
	public static function all( $name = null ) 
	{
		return static::code( '_', $name ).static::code( 'css', $name ).static::code( 'js', $name );
	}
	
	/**
	 * path modifier
	 *
	 * @var string
	 */
	public $path = 'assets/';
	
	/**
	 * Content
	 *
	 * @var array
	 */
	public $assets = array();
}