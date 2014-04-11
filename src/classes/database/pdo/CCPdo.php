<?php namespace CC\Database;
/**
 * Database, just a PDO Wrapper
 *
 * @package 		ClanCats-Framework
 * @author     		Mario Döring <mariodoering@me.com>
 * @version 		0.4
 * @copyright 		2010 - 2013 ClanCats GmbH 
 *
 */
class CCPDO {
		
	/*
	 * Instance holder
	 */
	public static $instances = array();
	
	/*
	 * default database
	 */
	public static $default = 'main';
	
	/*
	 * Databse log
	 */
	public static $query_log = array();
	
	
	/**
	 * static pdo class init
	 *
	 * @return void
	 */
	public static function _init() {
		if ( !DEV_ENV ) { 
			return;
		}
			
		// add a hook to the main resposne
		\CCEvent::mind( 'clancats.response', function( $response ) {
			if ( strpos( $response->body, '</body>' ) === false ) {
				return $response;
			}
			
			$view = \CCView::create( 'core::database/table', array( 'log' => CCPDO::$query_log ) );
			
			// add the table before the body end
			$response->body = str_replace( '</body>', $view."\n</body>", $response->body );
				
			return $response;
		});
	}
	
	
	/**
	 * Get a Database Instance
	 *
	 * @param string	$name
	 * @return CCDatabase
	 */
	public static function instance( $name = null ) {
		
		// Check name
		if ( is_null( $name ) ) {
			$name = static::$default;
		}
		
		// Check if no Instance exist
		if ( !isset( static::$instances[$name] ) ){
			static::$instances[$name] = new static( $name );
		}
		
		// return instance
		return static::$instances[$name];
	}
	
	
	/*
	 * The current connection
	 */
	protected $driver;
	
	/* 
	 * Are we connected to default Database 
	 */
	public $connected = false;

	/** 
	 * constructor
	 *
	 * @param string	$name
	 * @return void
	 */
	protected function __construct( $name ) {
		$this->connect( $name );
	}
	
	/**
	 * Connect to a Database
	 *
	 * @param string 	$name
	 * @return void
	 */
	protected function connect( $name ) {
		
		// Check if alredy connectet
		if ( $this->connected ) { return true; }
		
		// get the config
		$config = \CCConfig::create( 'database' )->get( $name );
		
		// if it is just an alias
		if ( is_string( $config ) ) {
			$config = \CCConfig::create( 'database' )->get( $config );
		}
		
		// get the driver
		switch( $config['driver'] ) {
			case 'mysql':
			default:
				$this->driver = new Pdo_Driver_CCMysql();
			break;
		}
		
		if ( $this->driver->connect( $config ) ) {
			return $this->connected = true;
		}
		
		return false;
	}
	
	/**
	 * build an SQL query string from a query object
	 * 
	 * @param CCQuery 	$query
	 * @return string
	 */
	public function build( $query ) {
		return $this->driver->build( $query );
	}
	
	/**
	 * get a PDO statement
	 *
	 * @param string	$query
	 * @return mixed
	 */
	public function statement( $query ) {
		return $this->driver->connection->prepare( $query );
	}
	
	/**
	 * just execute an query on PDO
	 *
	 * @param string 	$query
	 * @param array 	$params
	 * @return PDOStatement
	 */
	public function execute( $query, $params = array() ) {
		
		// create a statement
		$sth = $this->statement( $query );
		
		// try to execute
		try {
			$sth->execute( $params );
		}
		catch ( \PDOException $e ) {
			throw new \CC\Core\CCException( "Query: {$query} PDO Exception: {$e->getMessage()}" );
		}
		
		// log
		$this->log( $query, $params );
		
		// return the statement
		return $sth;
	}  
	
	/**
	 * returns the last inserted id
	 *
	 * @return int
	 */
	public function last_insert_id() {
		return $this->driver->connection->lastInsertId();
	}
	
	/**
	 * log a query
	 *
	 * @param string	$query
	 * @param array 	$params
	 * @return void
	 */
	public function log( $query, $params = array() ) {
		if ( DEV_ENV ) {
			
			// log the query	
			static::$query_log[] = array(
				'query' => $query,
				'params' => $params
			);
		}
		
		//\CCLog::add( $query );
	}
}
?>