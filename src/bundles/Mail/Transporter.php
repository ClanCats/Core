<?php namespace Mail;
/**
 * CCMail Transporter
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 */
class Transporter 
{
	/**
	 * Instance holder
	 *
	 * @var array
	 */
	protected static $_instances = array();
	
	/**
	 * Default transporter instance name
	 *
	 * @var string
	 */
	private static $_default = 'main';
	
	/**
	 * Get a transporter instance or create one
	 *
	 * @param string			$name
	 * @param array 			$conf	You can pass optionally a configuration directly. This will overwrite.
	 * @return Auth_Handler
	 */
	public static function create( $name = null, $conf = null ) 
	{
		if ( is_null( $name ) ) 
		{
			$name = static::$_default;
		}
		
		if ( !is_null( $conf ) && is_array( $conf ) )
		{
			return static::$_instances[$name] = new static( $name, $conf );
		}
		
		if ( !isset( static::$_instances[$name] ) )
		{
			static::$_instances[$name] = new static( $name );
		}
		
		return static::$_instances[$name];
	}
	
	/**
	 * Kill an instance to force the transporter to redo the construction
	 *
	 * @return void
	 */
	public static function kill_instance( $name )
	{
		if ( array_key_exists( $name, static::$_instances ) )
		{
			unset( static::$_instances[$name] );
		}
	}
}