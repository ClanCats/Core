<?php namespace Core;
/**
 * Language / Translations
 *
 * i've tried to write this class as simple 
 * as possible to keep the best possible
 * performence at localization.
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 */
class CCLang {
	
	/*
	 * the current language
	 */
	protected static $lang = null;
	
	/*
	 * the language data
	 */
	private static $data = array();
	
	/**
	 * static init
	 */
	public static function _init() {
		
		// set default
		static::$lang = ClanCats::$config->get('language.default');
	}
	
	/**
	 * set the current language
	 * 
	 * @param string    $lang
	 */
	public static function set_language( $lang ) {
		static::$lang = static::parse_lang( $lang );
		
		if ( !array_key_exists( static::$lang, static::$data ) ) {
			static::$data[static::$lang] = array();
		}
		
		return static::$lang;
	}
	
	/**
	 * get the current language
	 * 
	 * @return string
	 */
	public static function get_language() {
		return static::$lang;
	}
	
	/**
	 * try to validate a lang
	 * 
	 * @param string    $lang
	 */
	public static function parse_lang( $lang ) {
    	
    	$conf = ClanCats::$config->get('language');
    	    
    	if ( isset( $lang ) && strlen( $lang ) > 1 ) {
    	
    	    $httpLang = explode( ',', $lang );
    	        
    	  	$httpLang = explode( '-', $httpLang[0] );
    	        
    	    if ( isset( $httpLang[1] ) ) {
    	        $httpLang[1] = strtoupper( $httpLang[1] );
    	    } else {
    	        $httpLang[1] = strtoupper( $httpLang[0] );
    	    }
    	    
    	    $aviable = $conf['aviable'];
    	    
    	    if ( array_key_exists( $httpLang[0], $aviable ) ) {
	    	    if ( in_array( $httpLang[1], $aviable[$httpLang[0]] ) ) {
		    	    return $httpLang[0].'-'.$httpLang[1];
	    	    }
	    	    // try to return next possible
	    	    else {
		    	    $locales = $aviable[$httpLang[0]];
		    	    return $httpLang[0].'-'.$locales[key($locales)];
	    	    }
    	    }        
    	}
    	 
        return $conf['default'];
	}
	
	/**
	 * return current language data
	 *
	 * @return array
	 */
	public static function _data() {
		return static::$data[static::$lang];
	}
	
	/**
	 * load a language file to a namespace
	 *
	 * @param string	$path
	 * @param string	$namespace
	 */
	public static function load( $path, $namespace = null, $overwrite = false ) {
		
		if ( is_null( $namespace ) ) {
			$namespace = $path;
		}
		
		if ( array_key_exists( $namespace, static::_data() ) && $overwrite === false ) {
			return;
		}
		
		$path = ClanCats::path( $path, LANG_DIR, '/'.static::$lang.EXT );
		
		if ( !file_exists( $path ) ) {
			throw new CCException( "CCLang -- could not load language file: ".$path );
		}
		
		static::$data[static::$lang][$namespace] = require( $path );
	}
	
	/**
	 * get a line 
	 *
	 * @param string	$key
	 * @param array 	$params 
	 */
	public static function line( $key, $params = array() ) {
		$namespace = substr( $key, 0, strpos( $key, '.' ) ); 
		$key = substr( $key, strpos( $key, '.' )+1 );
		
		if ( !is_array( static::$data[static::$lang] )
			|| !array_key_exists( $namespace, static::$data[static::$lang] )
			|| !array_key_exists( $key, static::$data[static::$lang][$namespace] ) ) {
			// try to autoload the namespace
			CCLang::load( $namespace, $namespace );
			
			if ( !array_key_exists( $key, static::$data[static::$lang][$namespace] ) ) {
				CCProfiler::check("CCLang -- could not load with namespace: {$namespace}");
				return $key; 
			}
		}
		
		
		$line = static::$data[static::$lang][$namespace][$key];
		
		if ( is_string( $line ) ) {
			foreach ( $params as $param => $value) {
				$line = str_replace( ':'.$param, $value, $line );
			}
		}
		
		return $line;
	}
}