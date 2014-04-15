<?php namespace Core;
/**
 * Base Model 
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 */
class CCModel 
{
	/**
	 * The static data array
	 *
	 * Because we don't want to generate the defauts etc. on every
	 * created model again we save them static. But because of the 
	 * behavior of php and static vars we cannot just set these in 
	 * static vars, so we add them to a static array with our class name.
	 *
	 * @var array
	 */
	public static $_static_array = array();

	/*
	 * Defaults
	 */
	// protected static $_defaults = array();
	
	/**
	 * Static init
	 * 
	 * Here we prepare the model properties and settings for future use.
	 */
	public static function _init() 
	{
		if ( ( $class = get_called_class() ) == get_class() ) 
		{
			return;
		}
		
		// lets prepare the settings
		static::$_static_array[$class] = static::_prepare( static::$_static_array, $class );
	}
	
	/**
	 * Prepare the model
	 *
	 * @param string 	$settings	The model properties
	 * @param string 	$class 		The class name.
	 * @return array
	 */
	protected static function _prepare( $setting, $class )
	{
		$settings['defaults'] = array();
		
		// get the default's, fix them and clean them.
		if ( property_exists( $class, '_defaults') ) 
		{
			foreach( static::$_defaults as $key => $value )
			{
				if ( is_numeric( $key ) ) 
				{
					$settings['defaults'][$value] = null;
				}
				else
				{
					$settings['defaults'][$key] = $value;
				}
			}
			
			static::$_defaults = $settings['defaults'];
		}
		
		return $settings;
	}

	/**
	 * Access to model's properties
	 *
	 * @param string 			$key
	 * @return array|miexed
	 */
	public static function _model( $key = null ) 
	{
		if ( $key ) 
		{
			return static::$_static_array[get_called_class()][$key];
		}
		return static::$_static_array[get_called_class()];
	}

	/**
	 * just get an empty instance
	 *
	 * @param array 	$data
	 * @return CCModel
	 */
	public static function create( $data ) 
	{
		return new static( $data );
	}

	/**
	 * Assign an model with some data
	 *
	 * @param array 			$data
	 * @return CCModel
	 */
	public static function assign( $data ) 
	{
		// return null if we have no data
		if ( $data === null ) 
		{
			return null;
		}

		// do we have a collection or a single result?
		if ( CCArr::is_collection( $data ) ) 
		{
			$return = array();

			foreach( $data as $key => $item ) 
			{
				$return[$key] = static::create( $item );
			}
			return $return;
		}

		return static::create( $data );
	}

	/**
	 * The model data store
	 *
	 * @var array
	 */
	protected $_data_store = array();

	/**
	 * model constructor
	 *
	 * @param array 		$data
	 * @return void
	 */
	public function __construct( $data = null ) 
	{
		// set the defaults first
		$this->_data_store = static::_model( 'defaults' );
		
		if ( !is_null( $data ) )
		{
			$this->_assign( $data );
		}
	}

	/**
	 * Assign an model with some data
	 *
	 * @param array 		$data
	 * @return self
	 */
	public function _assign( $data ) 
	{
		$data = $this->_before_assign( $data );

		foreach( $data as $key => $value ) 
		{
			if ( array_key_exists( $key, $this->_data_store ) ) 
			{
				$this->_data_store[$key] = $value;
			}
		}

		return $this;
	}

	/**
	 * Assign data hook 
	 * This function lets you modify passed data before gettings assignt
	 *
	 * @param array 			$data
	 * @return array
	 */
	protected function _before_assign( $data ) { return $data; }

	/**
	 * Get a value by key from the model data
	 *
	 * @param string			$key 
	 * @return mixed
	 */
	public function &__get( $key ) 
	{
		$value = null;
		
		// check if the modifier exists
		$has_modifier = method_exists( $this, '_get_modifier_'.$key );

		// try getting the item
		if ( array_key_exists( $key, $this->_data_store ) ) 
		{
			if ( !$has_modifier )
			{
				return $this->_data_store[$key];
			}
			else
			{
				return $this->{'_get_modifier_'.$key}( $item );
			}
		}

		if ( $has_modifier )
		{
			return $this->{'_get_modifier_'.$key}();
		}
		
		throw new \InvalidArgumentException( "CCModel - Invalid or undefined model property '".$key."'." );
	}

	/**
	 * Set a value by key in the model data
	 *
	 * @param string			$key
	 * @param mixed			$value
	 * @return void
	 */
	public function __set( $key, $value ) 
	{
		if ( method_exists( $this, '_set_modifier_'.$key ) ) 
		{
			$value = $this->{'_set_modifier_'.$key}( $value );
		}

		$this->_data_store[$key] = $value;
	}

	/**
	 * Check if model data isset
	 * This also checks if a get modifier function exists which will also count as ture.
	 *
	 * @param $key
	 * @return bool
	 */
	public function __isset( $key ) 
	{
		return ( array_key_exists( $key, $this->_data_store ) || method_exists( $this, '_get_modifier_'.$key ) ) ? true : false;
	}
	
	/**
	 * return the raw data of the object
	 *
	 * @return array
	 */
	public function raw() 
	{
		return $this->_data_store;
	}
}