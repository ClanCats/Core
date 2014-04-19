<?php namespace Session;
/**
 * Session Manager
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 */
class Manager 
{
	/**
	 * Session configuration
	 *
	 * @var CCConfig
	 */
	public static $config;
	
	/**
	 * Instance holder
	 *
	 * @var array
	 */
	protected static $_instances = array();
	
	/**
	 * Default session instance name
	 *
	 * @var string
	 */
	private static $_default = 'main';
	
	/**
	 * CCSession factory
	 *
	 * @param $name
	 * @return CCSession
	 */
	public static function instance( $name = null ) {
	
		if ( !isset( $name ) ) {
			$name = static::$default;
		}
	
		if ( !isset( static::$instances[$name] ) ) {
	
			if ( !isset( static::$config ) ) {
				static::$config = CCConfig::create( 'session' );
			}
	
			static::$instances[$name] = new static( $name );
		}
	
		return static::$instances[$name];
	}
	
	/**
	 * Check if session exists 
	 * 
	 * @param $id
	 * @return bool
	 */
	public static function check( $id, $name = null ) {
		return static::instance( $name )->driver->check( $id );
	}
	
	/*
	 * cookie name
	 */
	protected $name;
	
	/*
	 * Data
	 */
	protected $data = array();
	protected $static_data = array();
	
	/*
	 * Driver
	 */
	protected $driver = null;
	
	/*
	 * Session ID
	 */
	public $id;
	
	/*
	 * The Fingerprint
	 */
	public $fingerprint;
	
	
	/**
	 * Session constructor
	 */
	protected function __construct( $name ) {
	
		$this->name = $name.static::$config->get('name');
	
		// get session_id from cookie
		$this->id = \CCCookie::get( $this->name );
	
		// set the fingerprint
		$this->fingerprint = CCStr::hash( $this->id );
	
		// Register shutdown
		CCEvent::mind( 'clancats.shutdown', array( $this, 'save' ) );
	
		// load the driver
		switch( static::$config->get( 'driver' ) ) {
	
			case 'cookie':
				$this->driver = CCSession_CookieDriver::factory( $name );
			break;
	
			case 'database':
				$this->driver = CCSession_DatabaseDriver::factory( $name );
			break;
	
			case 'file':
				$this->driver = CCSession_FileDriver::factory( $name );
			break;
		}
	
		// load session data
		$this->load();
	
		CCProfiler::check( "Session initialised: {$name}" );
	
		// I love that thanks Kohana!
		if ( mt_rand( 0, static::$config->get('gc') ) === static::$config->get('gc') ) {
			$this->gc();
		}
	}
	
	/**
	 * Load session data
	 */
	protected function load() {
	
		// set static data, these get set at loading and saving again 
		$defaultData = array(
			'last_active'	=> time(),
			'user_agent'		=> CCServer::client()->agent,
			'client_ip'		=> CCServer::client()->ip,
			'client_port'	=> CCServer::client()->port,
			'language'		=> CCLang::get_language(),
		);
	
		if ( $this->id ) {
	
			if ( !$data = $this->driver->load( $this->id ) ) {
	
				// generate new id
				$this->regenerate();
				// set data
				$data = $defaultData;
			}
	
			if ( !is_array( $data ) ) {
				$data = $defaultData;
			}
	
		} else {
			$this->regenerate();
			$data = $defaultData;
		}
	
		// set data
		$this->data = CCArr::get( 'content', $data, array() ); unset( $data['content'] );
		$this->static_data = $data;
	}
	
	/**
	 * Save session data
	 */
	public function save() { 
	
		// set static data
		$data = array(
			'last_active'	=> time(),
			'user_agent'		=> ( CCServer::$client->agent ) 	? CCServer::$client->agent 	: 'NOT_SET',
			'client_ip'		=> ( CCServer::$client->ip ) 	? CCServer::$client->ip 		: 'NOT_SET',
			'client_port'	=> ( CCServer::$client->port ) 	? CCServer::$client->port 	: 'NOT_SET',
			'language'		=> CCLang::get_language(),
		);
	
		$data = array_merge( $this->static_data, $data );
		$data['content'] = $this->data;
	
		// save the data
		$this->driver->save( $this->id, $data );
	
		// set the cookie
		\CCCookie::set( $this->name, $this->id, static::$config->get('cooike_lifetime') );
	}
	
	/**
	 * Generate a new session_id
	 */
	public function regenerate() {
		// Check if Session ID alredy exist
		do {
			$id = CCStr::random( 32 );
		}
		while ( $this->driver->check( $id ) );
	
		return $this->id = $id;
	}
	
	/**
	 * Generate a new session_id
	 */
	public function destroy() {
	
		// clean all arrays 
		$this->data = array();
		$this->static_data = array();
	
		// regenerate
		return $this->regenerate();
	}
	
	
	/*
	 * Garbage collection, delete old sessions
	 */
	public function gc() {
		$this->driver->gc( static::$config->get( 'lifetime' ) );
	}
	
	/**
	 * Set data
	 */
	public function set( $key, $data ) {
		$this->data[$key] = $data;
	}
	
	/**
	 * Check data
	 */
	public function has( $key ) {
		return isset( $this->data[$key] );
	}
	
	/**
	 * Get data
	 */
	public function get( $key, $default = null ) {
		if ( array_key_exists( $key, $this->data ) )  {
			return $this->data[$key];
		}
		return $default;
	}
	
	/**
	 * Get once
	 */
	public function get_once( $key ) {
		if ( array_key_exists( $key, $this->data ) )  {
			$data = $this->data[$key]; unset( $this->data[$key] ); return $data;
		}
	}
	
	/**
	 * Delete data
	 */
	public function delete( $key ) {
		unset( $this->data[$key] );
	}
	
	/**
	 * get a static value
	 *
	 * @param $key 
	 * @return mixed
	 */
	public function __get( $key ) {
		if ( array_key_exists( $key, $this->static_data ) )  {
			return $this->static_data[$key];
		}
	}
	
	/**
	 * Set a static value
	 *
	 * @param $key
	 * @param $value
	 * @return void
	 */
	public function __set( $key, $value ) {
		$this->static_data[$key] = $value;
	}
	
	/**
	 * check if data isset
	 *
	 * @param $key
	 * @return bool
	 */
	public function __isset( $key ) {
		return isset( $this->static_data[$key] );
	}
}