<?php namespace CC\Core; 
/**
 * The Database interface class
 *
 * the namespaces from the database structure is a 
 * little bit confusiing at the moment i have to 
 * clean that up some day.
 *
 * @package 		ClanCats-Framework
 * @author     		Mario Döring <mariodoering@me.com>
 * @version 		0.4
 * @copyright 		2010 - 2013 ClanCats GmbH 
 *
 */
class DB {
	
	/**
	 * Just run a Query
	 *
	 * @param string	$query
	 * @param array 	$params
	 * @return mixed
	 */
	public static function statement( $query, $params = array(), $name = null ) {
		return \CC\Database\CCPDO::instance( $name )->execute( $query, $params );
	}
	
	/**
	 * Connect to a Database
	 * 
 	 * @param string 	$name
 	 * @param boool
	 */
	public static function connect( $name = null ) {
		return \CC\Database\CCPDO::instance( $name )->connected;
	}
	
	/** 
	 * find an record by the id
	 *
	 * @param string	$table
	 * @param int		$id
	 * @param string	$name
	 * @return mixed 
	 */
	public static function find( $table, $id, $key = 'id', $name = null ) {
		return \CC\Database\Pdo_CCQuery::select( $table )->find( $id, $key, $name );
	}
	
	/**
	 * get the first record
	 *
	 * @param string	$table
	 * @param array		$fields
	 * @param string	$key
	 * @return mixed
	 */
	public static function first( $table, $key = 'id', $name = null ) {
		return \CC\Database\Pdo_CCQuery::select( $table )->first( $name );
	}
	
	/**
	 * get the last record
	 *
	 * @param string	$table
	 * @param array		$fields
	 * @param string	$key
	 * @return mixed
	 */
	public static function last( $table, $key = 'id', $name = null ) {
		return \CC\Database\Pdo_CCQuery::select( $table )->last( $name );;
	}
	
	/**
	 * get just one column as result
	 *
	 * @param string	$table
	 * @param string	$column
	 * @return mixed
	 */
	public static function column( $table, $column, $id, $key = 'id', $name = null ) {
		return \CC\Database\Pdo_CCQuery::select( $table )->where( $key, $id )->column( $column, $name );
	}
	
	/**
	 * count records
	 *
	 * @param string	$table
	 * @return mixed
	 */
	public static function count( $table, $name = null) {
		return \CC\Database\Pdo_CCQuery::select( $table )->count( $name );
	}
	
	/**
	 * select data from the database
	 *
	 * @param string	$table
	 * @param array		$fields
	 * @return mixed
	 */
	public static function select( $table, $fields = null ) {
		return \CC\Database\Pdo_CCQuery::select( $table )->fields( $fields );
	}
	
	/**
	 * insert some data
	 *
	 * @param string	$table
	 * @param array		$data
	 * @return mixed
	 */
	public static function insert( $table, $data = null ) {
		return \CC\Database\Pdo_CCQuery::insert( $table )->values( $data, null, true );
	}
	
	/**
	 * update some data
	 *
	 * @param string	$table
	 * @param array		$data
	 * @return mixed
	 */
	public static function update( $table, $data = null ) {
		return \CC\Database\Pdo_CCQuery::update( $table )->values( $data, null, true );
	}
	
	/**
	 * insert or update some data
	 *
	 * @param string	$table
	 * @param array		$data
	 * @param int		$id
	 * @return mixed
	 */
	public static function insert_or_update( $table, $data = null, $id = null ) {
		return \CC\Database\Pdo_CCQuery::insert_or_update( $table )->values( $data, null, true );
	}
	
	/**
	 * delete some data
	 *
	 * @param string	$table
	 * @param array		$data
	 * @param int		$id
	 * @return mixed
	 */
	public static function delete( $table, $id = null, $key = 'id' ) {
		$query = \CC\Database\Pdo_CCQuery::delete( $table );
		if ( !is_null( $id ) ) {
			$query->s_where( $key, $id );
		}
		return $query;
	}
	
	/**
	 * returns the last inserted id
	 *
  	 * @param string	$name
	 * @return int|null
	 */
	public static function last_id( $name = null ) {
		return \CC\Database\CCPDO::instance( $name )->last_insert_id();
	}
}
