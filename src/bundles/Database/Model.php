<?php namespace BD;
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
class Model extends CCModel
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
	 * The relationships
	 */
	// protected static $_relationships = array();

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
	 */
	protected static function _prepare()
	{
		// set the fields
		if ( property_exists( $class, '_fields') ) {
			$cache['fields'] = static::$_fields;
		}
		if ( empty( $cache['fields'] ) ) {
			$cache['fields'] = array_keys( $cache['defaults'] );
		}
		
		
		// set table name
		if ( property_exists( $class, '_table') ) {
			$cache['table'] = static::$_table;
		}
		if ( is_null( $cache['table'] ) ) {
			$cache['table'] = strtolower( substr( get_called_class(), 6 ) ).'s';
		}
		
		// set primary key
		if ( property_exists( $class, '_primary_key') ) {
			$cache['primary_key'] = static::$_primary_key;
		}
		
		// set query defaults
		if ( property_exists( $class, '_query_defaults') ) {
			$cache['query_defaults'] = static::$_query_defaults;
		}
		
		// set relationships
		if ( property_exists( $class, '_relationships') ) {
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
		}
		
		// set timestamps
		if ( property_exists( $class, '_timestamps') ) {
			$cache['timestamps'] = static::$_timestamps;
		}
		
		static::$_static_array[$class] = $cache;
	}
}