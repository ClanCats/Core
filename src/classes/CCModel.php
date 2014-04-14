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

	/**
	 * Defaults
	 */
	// protected static $_defaults = array();
	
	/**
	 * Prepare the model
	 *
	 * @param string 	$settings	The model properties
	 * @param string 	$class 		The class name.
	 * @return array
	 */
	protected static function _prepare( $setting, $class )
	{
		// get the default's, fix them and clean them.
		if ( property_exists( $class, '_defaults') ) 
		{
			$setting['defaults'] = static::$_defaults;
		
			foreach( static::$_defaults as $key => $value )
			{
				if ( is_numeric( $key ) ) 
				{
					$setting['defaults'][$value] = null;
					unset( $cache['defaults'][$key] );
				}
			}
		}
	}
	
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

		$cache = array(
			// object defaults
			'defaults' 			=> array(),
			
			// database fields
			'fields'				=> array(),
			
			// database table
			'table'				=> null,
			// the primary key
			'primary_key'		=> 'id',
			// the relationships
			'relationships'		=> array(),
			// the query defaultss
			'query_defaults'	=> null,
			// auto timestmaps
			'timestamps'		=> false,
		);

		

		static::$_static_array[$class] = $cache;
	}
	
	/**
	 * Prepare the model
	 */
	protected static function _prepare()
	{
		
	}

	/**
	 * return a property from the model
	 */
	public static function _model( $key ) {
		return static::$_static_array[get_called_class()][$key];
	}

	/**
	 * return a propertie from propertie store
	 */
	protected static function _cache( $key = null ) {
		if ( $key ) {
			return static::$_static_array[get_called_class()][$key];
		}
		return static::$_static_array[get_called_class()];
	}

	/**
	 * just get an empty instance
	 *
	 * @return CCModel
	 */
	public static function factory() {
		return new static();
	}

	/**
	 * return all 
	 *
	 * @return array
	 */
	public static function all() {
		return static::find( array( 'limit' => null ) );
	}

	/**
	 * find from database
	 *
	 * @param mixed		$param
	 * @param mixed		$param2
	 * @return CCModel
	 */
	public static function find( $param, $param2 = null ) {

		$cache = static::_cache();
		
		$fields = array();
		foreach( $cache['fields'] as $field ) {
			if ( substr( $field, -1 ) != '*' ) {
				$fields[] = '`'.$field.'`';
			} else {
				$fields[] = $field;
			}
		}
		
		$query = DB::select( $cache['table'], $fields );

		// is it a callback? 
		if ( is_callable( $param ) && !is_string( $param )) {
			call_user_func_array( $param, array( &$query ) );
		}
		// is it an array?
		elseif ( is_array( $param ) ) {
			foreach( $param as $key => $action ) {
				if ( !is_array( $action ) ) {
					$action = array( $action );
				}
				call_user_func_array( array( $query, $key ), $action );
			}
		}
		// no param2? like model::find( 42 )
		elseif ( is_null( $param2 ) ) {
			$query->s_where( $cache['table'].'.'.$cache['primary_key'], $param );
		}
		// else do something like: model::find( 'name', 'supermario' )
		else {
			$query->s_where( $param, $param2 );
		}

		// run the qurey defaults
		if ( !is_null( $cache['query_defaults'] ) ) {

			// callback?
			if ( is_callable( $cache['query_defaults'] ) ) {
				call_user_func_array( $cache['query_defaults'], array( &$query ) );
			}
			// or an array?
			elseif ( is_array( $cache['query_defaults'] ) ) {
				foreach( $cache['query_defaults'] as $key => $action ) {
					if ( !is_array( $action ) ) {
						$action = array( $action );
					}

					if ( is_array( $action[key($action)] ) ) {
						foreach( $action as $action_arr ) {
							call_user_func_array( array( $query, $key ), $action_arr );
						}
					}
					else {
						call_user_func_array( array( $query, $key ), $action );
					}
				}
			}
		}

		// alway group the result
		$query->group_result( $cache['primary_key'] );

		return static::assign( $query->run() );
	}

	/**
	 * find with an relationship
	 */
	public static function with( $with, $params = null, $relcallback = null, $relwith = array() ) {

		if ( !is_array( $with ) ) {
			$with = array( $with );
		}

		if ( is_null( $params ) ) {
			$params = array(
				'limit'		=> null,
			);
		}

		$data = static::find( $params );
		$cache = static::_cache();

		foreach( $with as $kkey => $rel_key ) {
			// placeholder
			$placeholer = $rel_key;
			if ( is_string( $kkey ) ) {
				 $rel_key = $kkey;
			}
			
			extract( $cache['relationships'][$rel_key] );

			// switch the type
			if ( $type == 'has_one' ) {

				$keys = array();
				foreach( $data as $item ) {
					$keys[] = $item->{$foreign_key};
				}

				$rel_data = $model::find( function( $query ) use( $key, $keys ) {
					$query->s_where( $key, 'in', $keys );
					$query->limit = null;
				});

				foreach( $data as $pkey => $item ) {
					if ( $item->$foreign_key == 0 ) {
						continue;
					}

					$item->{$placeholer} = $rel_data[$item->$foreign_key];
					$data[$pkey] = $item;
				}
			}
			/*elseif ( $type == 'belongs_to' ) {
				$item = $this->data[$key] = $model_name::find( $relation['foreign_key'], $this->{$relation['key']} );
			}*/
			elseif ( $type == 'has_many' ) {
				$keys = array_keys( $data );
				
				$rel_data = $model::find( function( $query ) use( $foreign_key, $keys, $relcallback, $rel_key ) {
					$query->s_where( $foreign_key, 'in', $keys );
					$query->limit( null );
					
					if ( is_array( $relcallback ) && array_key_exists( $rel_key, $relcallback ) ) {
						call_user_func_array( $relcallback[$rel_key], array( &$query ) );
					}	
				});
			
				foreach( $rel_data as $i_key => $item ) {
					$data[$item->{$foreign_key}]->data[$placeholer][$i_key] = $item;
				}
			}
		}

		return $data;
	}

	/**
	 * assign an model with some data
	 *
	 * @param array 	$data
	 * @return CCModel
	 */
	public static function assign( $data ) {

		// return  null if we have no data
		if ( $data == null ) {
			return null;
		}

		// are multiple items?
		if ( is_array( $data[key($data)] ) ) {

			$return = array();

			foreach( $data as $key => $item ) {
				$return[$key] = static::factory()->_assign( $item );
			}

			return $return;
		}

		return static::factory()->_assign( $data );
	}

	/*
	 * data holder
	 */
	protected $data;

	/**
	 * model constructor
	 */
	public function __construct() {

		// set the defaults first
		$this->data = static::_cache('defaults');
	}

	/**
	 * save an model
	 *
	 * @param mixed		$fields
	 * @return self
	 */
	public function save( $fields = null ) {

		$cache = static::_cache();

		if ( is_null( $fields ) ) {
			$fields = $cache['fields'];
		}
		elseif ( is_string( $fields ) ) {
			$fields = array( $fields );
		}

		// Update
		if ( $this->data[$cache['primary_key']] > 0 ) {
			$query = DB::update( $cache['table'] )
				->s_where( $cache['primary_key'], $this->data[$cache['primary_key']] );
		}
		// Insert
		else {
			$query = DB::insert( $cache['table'] );
		}

		// before save hook
		$data = $this->_before_save( $this->data );

		// remove primary key
		$key = array_search( $cache['primary_key'], $fields );

		if ( $key !== false ) {
			unset( $fields[$key] );
			unset( $data[$cache['primary_key']] );
		}

		// only save some data if fields is not *
		if ( $fields != array( '*' ) ) {
			$old_data = $data; $data = array();
			foreach( $fields as $field ) {
				$data[$field] = $old_data[$field];
			}
		}

		$query->values( $fields, $data, true );

		// we get the new id as result
		if ( $this->{$cache['primary_key']} == 0 ) {
			$this->{$cache['primary_key']} = $query->run();
		}
		else {
			$query->run();
		}

		// after save hookt
		$this->_after_save();

		// return self
		return $this;
	}
	
	/**
	 * returns an array of the current object
	 *
	 * @return array
	 */
	public function as_array() {
		
		$array = $this->data;
		$rels = array_keys( static::_cache( 'relationships' ) );
		
		foreach ( $rels as $rel ) {
			if ( is_array( $array[$rel] ) ) {
				
				foreach( $array[$rel] as $key => $item ) {
					$array[$rel][$key] = $array[$rel][$key]->as_array();
				}
				
			} elseif ( is_object( $array[$rel] ) ) {
				$array[$rel] = $array[$rel]->as_array();
			}
		}
		return $array;
	}
	
	/**
	 * copy an self
	 *
	 * @return CCModel
	 */
	public function copy() {

		$objB = clone $this;
		$objB->{static::_cache('primary_key')} = null;

		return $objB;
	}

	/**
	 * delete the object
	 */
	public function delete() {
		$cache = static::_cache();

		return DB::delete( $cache['table'] )
			->where( $cache['primary_key'], $this->$cache['primary_key'] )
			->run();
	}

	/**
	 * find from database
	 *
	 * @param mixed		$value
	 * @param mixed		$key
	 * @return CCModel
	 */
	public function _find( $value, $key = null ) {

		$cache = static::_cache();

		// prime 
		if ( is_null( $key ) ) {
			$key = $cache['primary_key'];
		}

		// simple query
		$query = DB::select( $cache['table'], $cache['fields'] )
			->s_where( $key, $value )
			->limit( 1 );


		return $this->_assign( $query->run() );
	}

	/**
	 * assign an model with some data
	 *
	 * @param array 	$data
	 */
	public function _assign( $data ) {

		// let the modifyers modify
		$data = $this->_before_assign( $data );

		foreach( $data as $key => $value ) {
			if ( array_key_exists( $key, $this->data ) ) {
				$this->data[$key] = $value;
			}
		}

		return $this;
	}

	/**
	 * Assign data hook 
	 * to modify your data before the object is returnd
	 */
	protected function _before_assign( $data ) { return $data; }

	/**
	 * Save data hook 
	 * to modify your data before they get saved
	 */
	protected function _before_save( $data ) { return $data; }

	/**
	 * After save hook 
	 * to modify your data before they get saved
	 */
	protected function _after_save() {}


	/**
	 * get a value from the view
	 *
	 * @param $key 
	 * @return mixed
	 */
	public function __get( $key ) {

		$item = null;

		// try getting the item
		if ( array_key_exists( $key, $this->data ) )  {
			$item = $this->data[$key];
		}
		// is there a relationship
		elseif ( array_key_exists( $key, static::_cache( 'relationships' ) ) ) {

			$cache = static::_cache();
			$relation = $cache['relationships'][$key];
			$model_name = $relation['model'];

			// switch the type
			if ( $relation['type'] == 'has_one' ) {
				$item = $this->data[$key] = $model_name::find( $relation['key'], $this->{$relation['foreign_key']} );
			}
			elseif ( $relation['type'] == 'belongs_to' ) {
				$item = $this->data[$key] = $model_name::find( $relation['foreign_key'], $this->{$relation['key']} );
			}
			elseif ( $relation['type'] == 'has_many' ) {
				$item = $this->data[$key] = $model_name::find( array(
					's_where' 	=> array( $relation['foreign_key'], $this->{$relation['key']} ),
					'limit'		=> null,
				));
			}
		}

		// let the modifiers modify
		if ( method_exists( $this, '_get_modifier_'.$key ) ) {
			$item = $this->{'_get_modifier_'.$key}( $item );
		}

		return $item;
	}

	/**
	 * Set a view value
	 *
	 * @param $key
	 * @param $value
	 * @return void
	 */
	public function __set( $key, $value ) {

		// check if a set modifier exist
		if ( method_exists( $this, '_modifier_'.$key ) ) {
			$value = $this->{'_modifier_'.$key}( $value );
		}

		$this->data[$key] = $value;
	}

	/**
	 * check if data isset
	 *
	 * @param $key
	 * @return bool
	 */
	public function __isset( $key ) {
		return isset( $this->data[$key] );
	}

	/**
	 * Unset view data 
	 *
	 * @param $key
	 * @return void
	 */
	public function __unset( $key ) {
		unset( $this->data[$key] );
	}
}
?>