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
class Manager extends \CCDataObject
{
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
	 * Get a session instance manager
	 *
	 * @param string			$name
	 * @param array 			$conf	You can pass optionally a configuration directly. This will overwrite.
	 * @return DB_Handler
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
	 * Some default values for our session
	 *
	 * @return array
	 */
	public static function default_data_provider() 
	{
		return array(
			'last_active'	=> time(),
			'client_agent'	=> CCServer::client( 'agent' ),
			'client_ip'		=> CCServer::client( 'ip' ),
			'client_port'	=> CCServer::client( 'port' ),
			'client_lang'	=> CCServer::client( 'language' )
		);
	}
	
	/**
	 * The session manager name
	 *
	 * @var string
	 */
	protected $_name = null;
	
	/**
	 * The session driver
	 *
	 * @var Manager_Driver
	 */
	protected $_driver = null;
	
	/**
	 * Session ID
	 *
	 * @var	string
	 */
	public $id;
	
	/**
	 * The Fingerprint
	 *
	 * @var string
	 */
	public $fingerprint;
	
	/**
	 * Session constructor
	 *
	 * @param string 		$name
	 * @param array 			$config
	 */
	protected function __construct( $name, $config = null ) 
	{
		if ( is_null( $config ) )
		{
			$config = \CCConfig::create( 'session' )->get( $name );
			
			// check for an alias. If you set a string 
			// in your config file we use the config 
			// with the passed key.
			if ( is_string( $config ) ) 
			{
				$config = \CCConfig::create( 'session' )->get( $config );
			}
		}
		
		// Setup the driver class. We simply use name 
		// from the confif file and make the first letter 
		// capital. example: Handler_Mysql, Handler_Sqlite etc.
		$driver_class = __NAMESPACE__."\\Manager_".ucfirst( $config['driver'] );
		
		if ( !class_exists( $driver_class ) )
		{
			throw new Exception( "Session\\Manager::create - The driver (".$driver_class.") is invalid." );
		}
		
		$this->set_driver( $driver_class );
		
		// also don't forget to set the name manager name becaue we need him later.
		$this->_name = $name;
	}
	
	/**
	 * Get the current driver
	 *
	 * @return DB\Handler_Driver
	 */
	public function driver()
	{
		return $this->_driver;
	}
	
	/**
	 * Set the current driver
	 *
	 * @param string		$driver		The full driver class ( Session\Manager_ExampleDriver )
	 * @return void
	 */
	private function set_driver( $driver )
	{
		$this->_driver = new $driver;
	}
	
	/**
	 * Return the default data for the session
	 *
	 * @return array
	 */
	protected function default_data()
	{
		return call_user_func( ClanCats::$config->get( 'session.default_data_provider' ) );
	}
	
	/**
	 * Read data from the session driver. This overwrite's 
	 * any changes made on runtime.
	 *
	 * @return void 
	 */
	protected function read() 
	{
		// set static data, these get set at loading and saving again 
		$defaultData = array(
			'last_active'	=> time(),
			'user_agent'		=> CCServer::client()->agent,
			'client_ip'		=> CCServer::client()->ip,
			'client_port'	=> CCServer::client()->port,
			'language'		=> CCLang::get_language(),
		);
	
		if ( $this->id ) 
		{
			if ( !$data = $this->driver->load( $this->id ) ) 
			{
				// generate new id
				$this->regenerate();
				// set data
				$data = $defaultData;
			}
	
			if ( !is_array( $data ) ) 
			{
				$data = $defaultData;
			}
	
		} 
		else 
		{
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
	public function gc() 
	{
		$this->driver->gc( static::$config->get( 'lifetime' ) );
	}
}