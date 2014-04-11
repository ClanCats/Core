<?php namespace Core;
/**
 * Database sessions handler
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 */
class CCSession_DatabaseDriver implements CCSession_Driver {

	/**
	 * Session driver factory
	 *
	 * @param $name
	 */
	public static function factory( $name ) {
		return new static( $name );
	}

	/*
	 * the session key
	 */
	protected $name;

	/*
	 * and lifetime
	 */
	protected $lifetime;

	/*
	 * the table to store
	 */
	protected $table;
	
	/*
	 * the database instance
	 */
	protected $instance;
	
	/*
	 * the id the session startet with
	 */
	protected $load_id;
	
	/**
	 * driver constructor
	 */
	protected function __construct( $name ) {
		$this->name = $name;

		// set session configs
		$this->lifetime = CCSession::$config->get('lifetime');
		$this->table = CCSession::$config->get('database.table');
		$this->instance = CCSession::$config->get('database.instance');
	}

	/**
	 * load a session
	 *
	 * @param $id
	 */
	public function load( $id ) {
		
		$this->load_id = $id;
		
		$data = DB::select( $this->table )
			->s_where( array( 'id' => $id, 'instance' => $this->name ) )
			->run( $this->instance );
		
		if ( isset( $data['content'] ) ) {
			$data['content'] = unserialize( $data['content'] );
		}
		
		return $data;
	}

	/**
	 * save a session
	 *
	 * @param $id
	 * @param $data
	 */
	public function save( $id, $data ) {
		
		if ( isset( $data['content'] ) ) {
			$data['content'] = serialize( $data['content'] );
		}
		
		// update
		if ( $id == $this->load_id ) {
			DB::update( $this->table, $data )
				->s_where( array( 'id' => $id, 'instance' => $this->name ) )
				->run( $this->instance );
		}
		// insert
		else {
			$data['id'] = $id;
			$data['instance'] = $this->name;
			
			DB::insert( $this->table, $data )
				->run( $this->instance );
		}
	}

	/**
	 * check if a session exists
	 *
	 * @param $id
	 */
	public function check( $id ) {
		if ( DB::select( $this->table )->s_where( array( 'id' => $id, 'instance' => $this->name ) )->count( $this->instance ) > 0 ) {
			return true;
		}
		return false;
	}

	/**
	 * kill all outdated sessions
	 *
	 * @param $lifetime
	 */
	public function gc( $lifetime ) {
		
		DB::delete( $this->table )
			->s_where( 'last_active', '<', time() - $lifetime )
			->limit( null )
			->run( $this->instance );
	}
}