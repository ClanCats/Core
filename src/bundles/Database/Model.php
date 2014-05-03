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
	protected static function _prepare( $settings, $class )
	{
		$settings = parent::_prepare( $settings, $class );
		
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
			
			$settings['table'] = explode( "\\", $settings['table'] );
			
			$last = array_pop( $settings['table'] );
			
			// Often we have these model's in the Model folder, we 
			// don't want this in the table name so we cut it out.
			if ( substr( $last, 0, strlen( 'model_' ) ) == 'model_' )
			{
				$last = substr( $last, strlen( 'model_' ) );
			}
			
			$settings['table'][] = $last;
			
			$settings['table'] = implode( '_', $settings['table'] ).'s';
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
	 * Model finder 
	 * This function allows you direct access to your records.
	 *
	 * @param mixed		$param
	 * @param mixed		$param2
	 * @return CCModel
	 */
	public static function find( $param = null, $param2 = null ) 
	{
		$settings = static::_model();
		
		$query = DB::select( $settings['table'] );
		
		// Do we have a find modifier?
		if ( !is_null( $settings['find_modifier'] ) ) 
		{
			$callbacks = $settings['find_modifier'];
			
			if ( !CCArr::is_collection( $callbacks ) )
			{
				$callbacks = array( $callbacks );
			}
			
			foreach( $callbacks as $call )
			{
				if ( is_callable( $call ) ) 
				{
					call_user_func_array( $call, array( &$query ) );
				}
				else 
				{
					throw new ModelException( "Invalid Callback given to find modifiers." );
				}
			}
		}
	
		if ( !is_null( $param ) )
		{
			// Check if paramert 1 is a valid callback and not a string.
			// Strings as function callback are not possible because
			// the user might want to search by key like:
			// Model::find( 'key', 'type' );
			if ( is_callable( $param ) && !is_string( $param ) ) 
			{
				call_user_func_array( $param, array( &$query ) );
			}
			// When no param 2 isset we try to find the record by primary key
			elseif ( is_null( $param2 ) ) 
			{
				$query->where( $settings['table'].'.'.$settings['primary_key'], $param )
					->limit(1);
			}
			// When param one and two isset we try to find the record by
			// the given key and value.
			elseif ( !is_null( $param2 ) )
			{
				$query->where( $param, $param2 )
					->limit(1);
			}
		}
		
		// alway group the result
		$query->group_result( $settings['primary_key'] );
		
		// and we have to fetch assoc
		$query->fetch_arguments = array( 'assoc' );
		
		// and assign
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
	public function save( $fields = null ) 
	{
		$settings = static::_model();
		
		// check if we should save just some fields
		if ( is_null( $fields ) ) 
		{
			$fields = array_keys( $settings['defaults'] );
		}
		elseif ( !is_array( $fields ) ) 
		{
			$fields = array( $fields );
		}
		
		$pkey = $this->_data_store[$settings['primary_key']];
		$data = array();
		
		// Now we have to filter the data to the save g
		foreach( $fields as $field )
		{
			$data[$field] = $this->_data_store[$field];
		}
		
		// We have to remove the primary key from our data
		if ( array_key_exists( $settings['primary_key'], $data ) )
		{
			unset( $data[$settings['primary_key']] );
		}
		
		// We pass the data trough the before save callback.
		// This is a local callback for performence reasons.
		$data = $this->_before_save( $data );
	
		// When we already have a primary key we are going to 
		// update our record instead of inserting a new one.
		if ( !is_null( $pkey ) && $pkey > 0 ) 
		{
			$query = DB::update( $settings['table'], $data )
				->where( $settings['primary_key'], $pkey );
		}
		// No primary key? Smells like an insert query. 
		else 
		{
			$query = DB::insert( $settings['table'], $data );
		}
	
		// We check the query type to handle the response right
		if ( $query instanceof Query_Insert ) 
		{
			$this->_data_store[$settings['primary_key']] = $query->run();
		}
		else 
		{
			$query->run();
		}
	
		// after save hookt
		$this->_after_save();
	
		// return self
		return $this;
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
	public function &__get( $key ) 
	{
		return parent::__get( $key );
		$item = null;
	
		// try getting the item
		if ( array_key_exists( $key, $this->_data_store ) )  
		{
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