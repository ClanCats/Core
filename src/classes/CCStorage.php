<?php namespace Core;
/**
 * Helper to manage and create files
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 */
class CCStorage {

	/**
	 * get a storage path
	 *
	 * @param string		$file
	 * @param string		$key
	 * @return string
	 */
	public static function path( $file = null, $key = null ) {
		
		// get the storage key
		if ( is_null( $key ) ) {
			$key = ClanCats::$config->get( 'storage.default' );
		}
		
		// get aviable storage paths
		$paths = ClanCats::$config->get( 'storage.paths' );
		
		// check if path exists
		if ( !array_key_exists( $key, $paths ) ) {
			throw new CCException( 'CCStorage - use of undefined storage path '.$key.'.' );
		}
		
		return $paths[$key].$file;
	}
	
	/**
	 * get the public url to a file if aviable
	 *
	 * @param string		$file
	 * @param string		$key
	 * @return string
	 */
	public static function url( $file = null, $key = null ) {
		
		// get the storage key
		if ( is_null( $key ) ) {
			$key = ClanCats::$config->get( 'storage.default' );
		}
		
		// get aviable storage urls
		$paths = ClanCats::$config->get( 'storage.urls' );
		
		// check if url exists
		if ( !array_key_exists( $key, $paths ) ) {
			throw new CCException( 'CCStorage - use of undefined public url '.$key.'.' );
		}
		
		return CCUrl::to( $paths[$key].$file );
	}
	
	/**
	 * write a file to the storage
 	 *
	 * @param string		$file
	 * @param string		$key
	 * @return string
	 */
	public static function write( $file, $content, $key = null ) {
		return CCFile::write( static::path( $file, $key ), $content );
	}
	
	/**
	 * write a file to the storage
	 *
	 * @param string		$file
	 * @param string		$key
	 * @return string
	 */
	public static function touch( $file, $key = null ) {
		return static::write( $file, '', $key );
	}
}