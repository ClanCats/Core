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
		
		// Do we should use a special DB handler?
		if ( property_exists( $class, '_handler') ) 
		{
			$settings['handler'] = static::$_handler;
		}
		else 
		{
			$settings['handler'] = null;
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
		
		return $settings;
	}
	
	/**
	 * Fetch from the databse and created models out of the reults
	 * 
	 * @param DB\Query_Select		$query
	 * @return array
	 */
	public static function _fetch_handler( &$query )
	{
		// because the model is an object we force the fetch
		// arguments to obj so that we can still make use of
		// the group by and forward key functions
		$query->fetch_arguments = array( 'obj' );
		
		// Run the query and assign the reults
		// here we force the fetch arguments to assoc 
		// without this step model::assign will fail
		return static::assign( $query->handler->fetch( $query->build(), $query->handler->builder()->parameters, array( 'assoc' ) ) );
	}
	
	/**
	 * Returns a query and assign's the current model to it
	 *
	 * @return DB\Query_Select
	 */
	public static function select() 
	{
		return DB::model( get_called_class() );
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
			
			if ( !\CCArr::is_collection( $callbacks ) )
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
		$query->forward_key( $settings['primary_key'] );
		
		// and we have to fetch assoc
		$query->fetch_arguments = array( 'assoc' );
		
		// and assign
		return static::assign( $query->run() );
	}

	/**
	 * Has one releationships
	 *
	 * Model Car:
	 *     function engine()
	 *     {
	 *         return $this->has_one( 'Car_Engine', 'car_id', 'id' );	 
	 *     }
	 *
	 * @param Model			$model
	 * @param mixed			$foreign_key
	 * @param mixed			$key
	 * @return array
	 */
	protected function has_one( Model $model, $foreign_key = null, $local_key = null )
	{
		return new Model_Relation_HasOne( $this, $model, $foreign_key, $local_key );
	}
	
	/**
	 * find with an relationship
	 */
	/*public static function with( $with, $params = null, $relcallback = null, $relwith = array() ) 
	{	
		// ToDo rewrite
	}*/
	
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
		
		// after the before save event,
		// do we have to to something with the data type?
		foreach( $data as $key => $value )
		{
			if ( array_key_exists( $key, $settings['types'] ) )
			{
				$data[$key] = $this->_type_assignment_set( $settings['types'][$key], $value );
			}
		}
		
		// check if we have to set the timestamps automatically
		if ( $settings['timestamps'] === true )
		{
			if ( array_key_exists( 'created_at', $data ) )
			{
				// check if created_at should be set
				if ( $data['created_at'] < 1 )
				{
					$this->_data_store['created_at'] = $data['created_at'] = time();
				}
			}
			
			if ( array_key_exists( 'modified_at', $data ) )
			{
				$this->_data_store['modified_at'] =$data['modified_at'] = time();
			}
		}
	
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
	 * Create a copy of the current model
	 *
	 * @return DB\Model
	 */
	public function copy() 
	{
		$clone = clone $this; $clone->_data_store[static::_model('primary_key')] = null; return $clone;
	}
	
	/**
	 * Delete the current model from the database
	 *
	 * @return DB\Model
	 */
	public function delete() 
	{
		$settings = static::_model();
	
		$result = DB::delete( $settings['table'] )
			->where( $settings['primary_key'], $this->_data_store[$settings['primary_key']] )
			->limit(1)
			->run( $settings['handler'] );
			
		$this->_data_store[$cache['primary_key']] = null;
		
		return $result;
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
}