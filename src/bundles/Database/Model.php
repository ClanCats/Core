<?php namespace DB;
/**
 * DB Model 
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 */
class Model extends \CCModel
{
	/*
	 * The fields
	 */
	// protected static $_fields = array();

	/*
	 * The table
	 */
	// public static $_table = null;

	/*
	 * The primary key
	 */
	// public static $_primary_key = null;

	/*
	 * The find mofifier
	 */
	// protected static $_find_modifier = null;

	/*
	 * Let the model automatically set created_at and modified_at 
	 */
	// protected static $_timestamps = false;
	
	/*
	 * The relationships
	 */
	// protected static $_relationships = array();
	
	/**
 	 * Prepare the model
	 *
	 * @param string 	$settings	The model properties
	 * @param string 	$class 		The class name.
	 * @return array
	 */
	protected static function _prepare( $setting, $class )
	{
		$settings = parent::_prepare( $settings, $class );
		
		// Set the select fields. If not set we simply use 
		// the array keys of our defaults
		if ( property_exists( $class, '_fields') ) 
		{
			$settings['fields'] = static::$_fields;
		}
		else 
		{
			$settings['fields'] = array_keys( $settings['defaults'] );
		}
		
		// Next step the table name. If not set we use the 
		// class name appending an 's' in the hope that it 
		// makes sense like Model_User = users
		if ( property_exists( $class, '_table') ) 
		{
			$settings['table'] = static::$_table;
		}
		else 
		{
			$settings['table'] = strtolower( $class );
			
			// Often we have these model's in the Model folder, we 
			// don't want this in the table name so we cut it out.
			if ( substr( $settings['table'], 0, strlen( 'model_' ) ) == 'model_' )
			{
				$settings['table'] = substr( $settings['table'], strlen( 'model_' ) );
			}
			
			$settings['table'].'s';
		}
		
		// Next we would like to know the primary key used
		// in this model for saving, finding etc.. if not set
		// we use the on configured in the main configuration
		if ( property_exists( $class, '_primary_key') ) 
		{
			$settings['primary_key'] = static::$_primary_key;
		}
		else 
		{
			$settings['primary_key'] = \ClanCats::$config->get( 'database.default_primary_key', 'id' );
		}
		
		// The find modifier allows you hijack every find executed
		// on your model an pass setting's to the query. This allows
		// you for example to defaultly order by something etc.
		if ( property_exists( $class, '_find_modifier') ) 
		{
			$settings['find_modifier'] = static::$_find_modifier;
		}
		else
		{
			$settings['find_modifier'] = null;
		}
		
		// Enabling this options will set the created_at
		// and modified at property on save
		if ( property_exists( $class, '_timestamps') ) 
		{
			$settings['timestamps'] = (bool) static::$_timestamps;
		}
		else
		{
			$settings['timestamps'] = false;
		}
		
		// set relationships
		/*if ( property_exists( $class, '_relationships') ) {
			$cache['relationships'] = static::$_relationships;
		}
		foreach( $cache['relationships'] as $key => $relation ) {
			// do we have an model name?
			if ( !array_key_exists( 'model', $relation ) ) {
				$relation['model'] = 'Model_'.ucfirst( $key );
			}
			// is the foreign key set?
			if ( !array_key_exists( 'foreign_key', $relation ) ) {
				if ( $relation['type'] == 'has_many' ) {
					$relation['foreign_key'] = substr( $cache['table'], 0, -1 ).'_id';	
				} else {
					$relation['foreign_key'] = $key.'_id';
				}
			}
			// is the current key set?
			if ( !array_key_exists( 'key', $relation ) ) {
				$relation['key'] = $cache['primary_key'];
			}
		
			$cache['relationships'][$key] = $relation;
		}*/
		
		return $settings;
	}
	
	/**
	 * return all 
	 *
	 * @return array
	 */
	public static function all() 
	{
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
		if ( $this->_data_store[$cache['primary_key']] > 0 ) {
			$query = DB::update( $cache['table'] )
				->s_where( $cache['primary_key'], $this->_data_store[$cache['primary_key']] );
		}
		// Insert
		else {
			$query = DB::insert( $cache['table'] );
		}
	
		// before save hook
		$data = $this->_before_save( $this->_data_store );
	
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
		
		$array = $this->_data_store;
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
	public function &__get( $key ) {
	
		$item = null;
	
		// try getting the item
		if ( array_key_exists( $key, $this->_data_store ) )  {
			$item = $this->_data_store[$key];
		}
		// is there a relationship
		elseif ( array_key_exists( $key, static::_cache( 'relationships' ) ) ) {
	
			$cache = static::_cache();
			$relation = $cache['relationships'][$key];
			$model_name = $relation['model'];
	
			// switch the type
			if ( $relation['type'] == 'has_one' ) {
				$item = $this->_data_store[$key] = $model_name::find( $relation['key'], $this->{$relation['foreign_key']} );
			}
			elseif ( $relation['type'] == 'belongs_to' ) {
				$item = $this->_data_store[$key] = $model_name::find( $relation['foreign_key'], $this->{$relation['key']} );
			}
			elseif ( $relation['type'] == 'has_many' ) {
				$item = $this->_data_store[$key] = $model_name::find( array(
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
}