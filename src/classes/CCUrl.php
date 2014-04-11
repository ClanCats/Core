<?php namespace Core;
/**
 * ClanCats Url
 * @todo if have to rewrite this shitty class some day..
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 */
class CCUrl {

	/*
	 * URL Vars
	 */
	private static $path;
	private static $domain;
	private static $cdns;
	private static $https;

	/*
	 * default prefixes
	 */	
	private static $default_full = '';
	private static $default_path = '';

	/*
	 * use by default full urls
	 */
	private static $full_urls = false;

	/*
	 * current uri
	 */
	public static $current;

	/**
	 * static CCUrl initialisation
	 */
	public static function _init() {

		$config = ClanCats::$config->get( 'url' );

		if ( !isset( $config['path'] ) || $config['path'] == '' ) {
			$config['path'] = '/';
		}

		if ( !isset( $config['domain'] ) ) {
			$config['domain'] = CCServer::server( 'HTTP_HOST' );
		}

		/*
		 * set the config
		 */
		static::$path 	= $config['path'];
		static::$domain = $config['domain'];
		static::$cdns 	= $config['cdn'];
		static::$https 	= $config['https'];

		// use full urls by default?
		static::$full_urls = $config['full_url'];

		// default prefixes
		static::$default_full = (( static::$https ) ? 'https://' : 'http://').static::$domain.static::$path;
		static::$default_path = static::$path;

		// get the current URI
		if ( CCServer::has_server('REDIRECT_URL' ) ) {
			$uri = CCServer::server('REDIRECT_URL');
		} 
		elseif ( CCServer::has_server('REQUEST_URI' ) ) {
			$uri = explode( '?', CCServer::server('REQUEST_URI') ); $uri = $uri[0];
		}
		else {
			if ( !CLI ) {
				throw new CCException( 'Could not mach the requested URI!' );
			}
		}

		if ( substr( $uri, -1 ) != '/' ) {
			$uri .= '/';
		}

		// set current
		static::$current = substr( $uri, strlen( static::$path ) );
	}

	/**
	 * Generate an url
	 *
	 * @param string		$uri
	 * @param array|null	$params
	 */
	public static function to( $uri = '', $params = null, $full_url = null ) {

		// use full urls for this one?
		if ( $full_url === null ) {
			$full_url = static::$full_urls;
		}

		// fix when you make url:to('/')
		if ( $uri == '/' ) {
			$uri = '';
		}

		if ( substr( $uri, 0, 1 ) == '@' ) {
			if ( array_key_exists( $uri , CCRouter::$aliases ) ) {
				$uri = CCRouter::$aliases[$uri];
			}
		}

		if ( substr( $uri, 0, 7 ) != 'http://' && substr( $uri, 0, 8 ) != 'https://' ) {
			if ( $full_url ) {
				$uri = static::$default_full.$uri;
			}
			else {
				if ( substr( $uri, 0, 1 ) != '/' ) {
					$uri = static::$default_path.$uri;
				}
			}
		}

		if ( is_array( $params ) ) {
			if ( strpos( $uri, '?' ) !== false ) {
				$uri = $uri.'&'.http_build_query( $params );
			} else {
				$uri = $uri.'?'.http_build_query( $params );
			}
		}

		return $uri;
	}

	/**
	 * generate an url w
	 */
	public static function to_full( $uri = '', $params = null ) {
		return static::to( $uri, $params, true );
	}

	/**
	 * get the current uri
	 *
	 * @param array|null	$params
	 */
	public static function current( $params = null ) {
		return static::to( static::$current, $params );
	}

	/**
	 * generate a uri to cdn
	 */
	public static function cdn( $cdn, $uri ) {
		return static::$cdns[$cdn].$uri;
	}

	/**
	 * Check if running on Domain Root
	 */
	public static function runningOnRoot() {
		return ( static::$path == '/' ) ? true : false;
	}

	/**
	 * get the Router offset
	 */
	public static function routingPath( $domain = false ) {
		if ( $domain ) {
			return static::$domain.static::$path;
		}
		return static::$path;
	}

	/**
	 * create a URL
	 */
	public static function create( $path, $domain = false ) {
		return static::routingPath( $domain ).$path;
	}


}