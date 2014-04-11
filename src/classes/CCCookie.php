<?php namespace Core;
/**
 * Cookie handler
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 */
class CCCookie {
	
	/*
	 * Cookie Path
	 */
	public static $path = '/';
	
	/*
	 * Cookie Domain
	 */
	public static $domain = null;
	
	/*
	 * Secure
	 */
	public static $secure = false;
	
	/*
	 * HTTP only
	 */
	public static $httponly = false;
	
	
	/**
	 * Set a cookie
	 * 
	 * @param string 	$key
	 * @param string 	$value
	 * @param int		$expire
	 * @return bool
	 */
	public static function set( $key, $value, $expire = 0 ) {
		
		// set the expire date 
		if ( $expire > 0 ) {
			
			if ( $expire < time() ) {
				$expire = time() + $expire;
			}
		}
		
		/*
		 * Finally set the cookie
		 * @toDo: just at the cookie to an array an set the set cookie header from the CCResponse
		 */
		return setcookie( 
			$key, 
			$value, 
			$expire, 
			static::$path, 
			static::$domain, 
			static::$secure, 
			static::$httponly 
		);
	}
	
	/**
	 * get a cookie 
	 * 
	 * @param string 	$key
	 * @return mixed
	 */
	public static function get( $key ) {
		if ( isset( CCServer::$_instance->$COOKIE[$key] ) ) {
			return CCServer::$_instance->$COOKIE[$key];
		}
	}
	
	/**
	 * read a cookie 
	 * 
	 * @param string 	$key
	 * @param mixed		$default
	 * @return mixed
	 */
	public static function read( $key, $default = null ) {
		if ( !isset( CCServer::$_instance->$COOKIE[$key] ) ) {
			return $default;
		}
		return CCServer::$_instance->$COOKIE[$key];
	}
	
	/**
	 * eat a cookie, means get it once
	 * 
	 * @param string 	$key
	 * @return mixed
	 */
	public static function eat( $key ) {
		if ( !is_null( static::get( $key ) ) ) {
				
			$cookie = static::get( $key );
			static::delete( $key );
			
			return $cookie;
		}
	}
	
	/**
	 * has a cookie
	 *
	 * @param string 	$key
	 * @return bool
	 */
	public static function has( $key ) {
		return isset( CCServer::$_instance->$COOKIE[$key] );
	}
	
	
	/**
	 * delete a cookie
	 *
	 * @param string 	$key
	 * @return bool
	 */
	public static function delete( $key ) {
		return setcookie( $key, "REMOVED BY CHUCK NORRIS!", time()-1000 );
	}
	
	/*
	 * Damn after writing so often the word cookie i've become hungry! SHOP ALL THE COOKIES!
	 */
}