<?php namespace Core;
/**
 * String functions
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 */
class CCStr {
	
	/*
	 * just some chasetes at BIN i was running out of ideas 
	 */
	const KEY 		= 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789----';
	const SECURE 	= 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789äöüÄÖÜ@<({[/=\]})>!?$%&#*-+.,;:_';
	const ALPHA_NUM 	= 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	const ALPHA		= 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	const ALPHA_UP	= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	const ALPHA_LOW	= 'abcdefghijklmnopqrstuvwxyz';
	const NUM		= '0123456789';
	const HEX		= '0123456789ABCDEF';
	const BIN		= '01';
	
	/**
	 * get a charset
	 *
	 * @param string		$charset		use predefined charset or your own
	 * @return string
	 */
	public static function charset( $charset = null ) 
	{	
		switch( $charset ) 
		{	
			case 'pass':
			case 'secure':
			case 'password':
				return static::SECURE;
			break;
			
			case 'key':
				return static::KEY;
			break;
			
			case 'alphanum':
				return static::ALPHA_NUM;
			break;
			
			case 'alpha':
				return static::ALPHA;
			break;
			
			case 'alpha_low':
			case 'lowercase':
				return static::ALPHA_LOW;
			break;
			
			case 'alpha_up':
			case 'uppercase':
				return static::ALPHA_UP;
			break;
			
			case 'numeric':
			case 'num':
				return static::NUM;
			break;
			
			case 'hex':
				return static::HEX;
			break;
			
			case 'bin':
				return static::BIN;
			break;
			
			default:
				if ( !is_null( $charset ) ) 
				{
					return $charset;
				}
				return static::charset( 'alphanum' );
			break;
		}
	}
	
	/**
	 * generate a random string
	 *
	 * @param int		$length
	 * @param string		$charset
	 * @return string
	 */
	public static function random( $length = 25, $charset = null ) 
	{
		$charset = static::charset( $charset );
	
		$count = strlen( $charset ); $string = '';
	
		while ( $length-- ) 
		{
			$string .= $charset[mt_rand(0, $count-1)];
		}
	
		return $string;
	}
	
	/**
	 * try to get a string from an callback
	 *
	 * @param mixed		$callback
	 * @param array 		$params 
	 * @return string
	 */
	public static function capture( $callback, $params = array() )
	{
		if ( is_string( $callback ) )
		{
			return $callback;
		}
		
		if ( !is_closure( $callback ) )
		{
			return "";
		}
		
		if ( !is_array( $params ) ) 
		{
			$params = array( $params );
		}
		
		ob_start();
		$return = call_user_func_array( $callback,  $params );
		$buffer = ob_get_clean();
		
		if ( !is_null( $return ) )
		{
			return $return;
		}
		
		return $buffer;
	}
	
	/**
	 * does the same as PHP natives htmlentities function but you can pass arrays 
	 *
	 * @param string|array		$string
	 * @param bool 				$recursive
	 * @return string|array
	 */
	public static function htmlentities( $string, $recursive = false ) 
	{	
		if ( is_array( $string ) ) 
		{
			foreach( $string as $key => $item ) 
			{	
				if ( $recursive ) 
				{	
					if ( is_array( $item ) ) 
					{
						$string[$key] = static::htmlentities( $item, $recursive );
					}
				}
				
				if ( is_string( $item ) ) 
				{
					$string[$key] = htmlentities( $item );
				}
			}
			
			return $string;
		}
		
		return htmlentities( $string, ENT_QUOTES, ClanCats::$config->charset );
	}
	
	/**
	 * get the last part of a string
	 *
	 * @param string 	$string
	 * @param string		$sep
	 * @return string
	 */
	public static function suffix( $string, $sep = '-' ) 
	{
		return substr( $string, strrpos( $string, $sep )+strlen( $sep ) );
	}
	
	/**
	 * get the first part of a string
	 *
	 * @param string 	$string
	 * @param string		$sep
	 * @return string
	 */
	public static function prefix( $string, $sep = '-' ) 
	{
		return substr( $string, 0, strrpos( $string, $sep ) );
	}
	
	/**
	 * alias of suffix using a dott
	 *
	 * @param string 	$string
	 * @param string		$sep
	 * @return string
	 */
	public static function extension( $string ) 
	{
		return static::suffix( $string, '.' );
	}
	
	/**
	 * hashs a string using a configurable method
	 *
	 * @param string 	$string
	 * @return string
	 */
	public static function hash( $string ) 
	{
		return call_user_func( ClanCats::$config->get( 'security.hash', 'md5' ), $string );
	}
	
	/**
	 * clean an string removes special chars
	 *
	 * @param string		$string
	 * @param string		$allowed
	 * @return string
	 */
	public static function clean( $string, $allowed = "\-\." ) 
	{	
		return trim( preg_replace( array( 
			'/[^A-Za-z0-9\ '.$allowed.']/',
			'/[\ ]+/'
		), array(
			'',
			' ',
		), static::replace_accents( trim( $string ) ) ) );
	}
	
	/**
	 * clean an string remove special chars whitespaces ect.
	 * perfect for creating url strings.
	 *
	 * @param string 	$string
	 * @param string 	$sep
	 * @return string 
	 */
	public static function clean_url( $string, $sep = null ) 
	{
		// basic clean
		$string = strtolower( static::replace_accents( trim( $string ) ) );
		
		// these characters get replaced with our seperator
		$string = str_replace( array( ' ', '&', '\r\n', '\n', '+', ',' ) , '-', $string );
		
		$string = preg_replace( array(
			'/[^a-z0-9\-]/', // remove non alphanumerics
			'/[\-]+/', // only allow one in a row
		), array( '', '-' ), $string );
		
		// custom seperator
		if ( !is_null( $sep ) ) {
			$string = str_replace( '-', $sep, $string );
		}
		
		// trim the result again
		return trim( $string, '-' );
	}
	
	/**
	 * str replace using key => value of an array
	 * 
	 * @param string		$string
	 * @param array 		$arr
	 * @param int		$count 
	 * @return string
	 */
	public static function replace( $string, $arr, $count = null ) 
	{
		return str_replace( array_keys( $arr ), array_values( $arr ), $string, $count );
	}
	
	/**
	 * preg replace using key => value of an array
	 * 
	 * @param string		$string
	 * @param array 		$arr
	 * @param int		$count 
	 * @return string
	 */
	public static function preg_replace( $arr, $string, $count = null ) 
	{
		return preg_replace( array_keys( $arr ), array_values( $arr ), $string, $count );
	}
	
	/**
	 * converts an string to lowercase using the system encoding
	 * 
	 * @param string		$string
	 * @param string 	$encoding
	 * @return string
	 */
	public static function lower( $string, $encoding = null )
	{
		if ( is_null( $encoding ) )
		{
			$encoding = ClanCats::$config->charset;
		}
		return mb_strtolower( $string, $encoding );
	}
	
	/**
	 * converts an string to uppercase using the system encoding
	 * 
	 * @param string		$string
	 * @param string 	$encoding
	 * @return string
	 */
	public static function upper( $string, $encoding = null )
	{
		if ( is_null( $encoding ) )
		{
			$encoding = ClanCats::$config->charset;
		}
		return mb_strtoupper( $string, $encoding );
	}
	
	/**
	 * replace accent characters
	 * 
	 * @param string		$string
	 * @return string
	 */
	public static function replace_accents( $string )
	{
		return strtr( $string, array(
			'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'Ae', 'Å'=>'A', 'Æ'=>'A', 'Ă'=>'A',
			'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'ae', 'å'=>'a', 'ă'=>'a', 'æ'=>'ae',
			'þ'=>'b', 'Þ'=>'B',
			'Ç'=>'C', 'ç'=>'c',
			'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E',
			'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 
			'Ğ'=>'G', 'ğ'=>'g',
			'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'İ'=>'I', 'ı'=>'i', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i',
			'Ñ'=>'N',
			'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'Oe', 'Ø'=>'O', 'ö'=>'oe', 'ø'=>'o',
			'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
			'Š'=>'S', 'š'=>'s', 'Ş'=>'S', 'ș'=>'s', 'Ș'=>'S', 'ş'=>'s', 'ß'=>'ss',
			'ț'=>'t', 'Ț'=>'T',
			'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'Ue',
			'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ü'=>'ue', 
			'Ý'=>'Y',
			'ý'=>'y', 'ý'=>'y', 'ÿ'=>'y',
			'Ž'=>'Z', 'ž'=>'z'
		)); 
	}
	
	/**
	 * cuts a string after another string
	 *
	 * @param string 	$string
	 * @param string		$key
	 * @param bool		$cut_key
	 * @return string
 	 */
	public static function cut( $string, $key, $cut_key = true ) 
	{
		$pos = strpos( $string, $key );
		if ( $pos === false ) 
		{
			return $string;
		}
		if ( !$cut_key ) 
		{
			$pos += strlen( $key );
		}
		return substr( $string, 0, $pos );
	}
	
	/**
	 * removes a string from another one
	 *
	 * @param string 	$string
	 * @param string		$key
	 * @return string
 	 */
	public static function strip( $string, $key ) 
	{
		return str_replace( $key, '', $string );
	}
	
	/**
	 * round big numbers on thousends
	 * 
	 * @param int 		$int
	 * @return string
	 */
	public static function kfloor( $int ) 
	{
		if ( $int >= 1000 ) 
		{
			return floor( $int / 1000 ) . 'K';
		}
		return $int;
	}
	
	/**
	 * Convert memory to a human readable format
	 *
	 * @param int 		$size
	 * @return string
	 */
	public static function bytes( $size, $round = 2 ) 
	{
		$unit = array( 'b', 'kb', 'mb', 'gb', 'tb', 'pb' );
		return @round( $size / pow( 1024, ( $i = floor( log( $size, 1024 ) ) ) ), $round ).$unit[$i];
	}
	
	/**
	 * Convert memory to a human readable format
	 *
	 * @param int 		$size
	 * @return string
	 */
	public static function mircotime( $time, $round = 3 ) 
	{
		return round( $time, $round ).'s';
	}
}