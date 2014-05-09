<?php namespace Auth;
/**
 * Auth instnace handler 
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 */
 
use Core\CCCookie;
 
class Handler
{
	/**
	 * Instance holder
	 *
	 * @var array
	 */
	protected static $_instances = array();

	/**
	 * Default auth instance name
	 *
	 * @var string
	 */
	private static $_default = 'main';

	/**
	 * Get an auth instance or create one
	 *
	 * @param string			$name
	 * @param array 			$conf	You can pass optionally a configuration directly. This will overwrite.
	 * @return Auth_Handler
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
	 * Kill an instance to force the handler to redo the construction
	 *
	 * @return void
	 */
	public static function kill_instance( $name )
	{
		if ( array_key_exists( $name, static::$_instances ) )
		{
			unset( static::$_instances[$name] );
		}
	}
	
	/**
	 * the user object
	 *
	 * @var DB\Model
	 */
	public $user = null;
	
	/**
	 * is the instance authenticated
	 *
	 * @var bool
	 */
	protected $authenticated = false;
	
	/**
	 * The auth handler name
	 *
	 * @var string
	 */
	protected $name = null;
	
	/**
	 * The auth config array
	 *
	 * @var string
	 */
	protected $config = null;
	
	/**
	 * The used session manager
	 *
	 * @var string
	 */
	protected $session = null;
	
	/**
	 * Auth instance constructor
	 *
	 * @param string 		$name
	 * @param array 			$config
	 * @return void
	 */
	public function __construct( $name, $config ) 
	{	
		if ( is_null( $config ) )
		{
			$config = \CCConfig::create( 'auth' )->get( $name );
			
			// check for an alias. If you set a string 
			// in your config file we use the config 
			// with the passed key.
			if ( is_string( $config ) ) 
			{
				$config = \CCConfig::create( 'auth' )->get( $config );
			}
		}
		
		if ( !is_array( $config ) )
		{
			throw new Exception( "Auth\\Handler::create - Invalid auth handler (".$name.")." );
		}
		
		// also don't forget to set the name manager name becaue we need him later.
		$this->name = $name;
		
		// keep the configuration array
		$this->config = $config;
		
		// set the session handler
		$this->session = \CCSession::manager( $config['session_manager'] );
		
		$user_model =\CCArr::get( 'user_model', $this->config, "\\Auth\\User" );
		
		// set a empty default user object to avoid
		// on a non object errors
		$this->user = new $user_model;
		
		// do we already have a user id means are we
		// logged in?
		if ( !is_null( $session_key = $this->session_key() ) )
		{
			if ( $user = $user_model::find( $this->user_key(), $session_key ) )
			{
				$this->user = $user; return $this->authenticated = true;
			}
		}
		
		// When no session key / user id is given try to restore 
		// the login using the login keepers
		else 
		{
			$restore_id_cookie = \CCArr::get( 'restore_id_cookie', $this->config, 'ccauth-restore-id' );
			$restore_token_cookie = \CCArr::get( 'restore_token_cookie', $this->config, 'ccauth-restore-token' );
			
			if 
			(
				CCCookie::has( $restore_id_cookie ) &&
				CCCookie::has( $restore_token_cookie )
			) 
			{
				// get the restore cookies
				$restore_id = CCCookie::get( $restore_id_cookie );
				$restore_token = CCCookie::get( $restore_token_cookie );
	
				// get the restore login
				$login = $this->select_logins()
					->where( 'restore_id', $restore_id )
					->where( 'restore_token', $restore_token )
					->limit( 1 );
	
				// if no login found kill the cookies and return
				if ( !$login = $login->run() ) 
				{
					CCCookie::delete( $restore_id_cookie );
					CCCookie::delete( $restore_token_cookie );
					return $this->authenticated = false;
				}
	
				// Invalid user? kill the cookies and return
				if ( !$user = $user_model::find( $this->user_key(), $restore_id ) )
				{
					CCCookie::delete( $restore_id_cookie );
					CCCookie::delete( $restore_token_cookie );
					return $this->authenticated = false;
				}
	
				// validate the restore key if invalid 
				// once again kill the cookies and return
				if ( $login->restore_token != $this->restore_key( $user ) ) 
				{
					CCCookie::delete( $restore_id_cookie );
					CCCookie::delete( $restore_token_cookie );
					return $this->authenticated = false;
				}
	
				// If everything is fine sign the user in and 
				// update the restore keys
				return $this->sign_in( $user, true );
			}
		}
	
		return $this->authenticated = false;
	}
	
	/**
	 * Is this login valid?
	 *
	 * @return bool
	 */
	public function valid()
	{
		return $this->authenticated;
	}
	
	/**
	 * Get the current user session key 
	 * 
	 * @return mixed
	 */
	public function session_key() 
	{
		return $this->session->get( \CCArr::get( 'session_key', $this->config, 'user_id' ) );
	}
	
	/**
	 * Get the current user session key 
	 * 
	 * @return mixed
	 */
	public function user_key() 
	{
		return \CCArr::get( 'user_key', $this->config, 'id' );
	}
	
	/**
	 * Select from logins
	 *
	 * @return DB\Query_Select
	 */
	private function select_logins()
	{
		return \DB::select( 
			\CCArr::get( 'logins.table', $this->config, 'logins' ), 
			array(),
			\CCArr::get( 'logins.table', $this->config, 'handler' )
		);
	}
	
	/**
	 * Validate an identifier with the password 
	 * In other words is the login correct?
	 *
	 * @param string 	$identifier
	 * @param string 	$password
	 * @return mixed  	false on failure, user object on success
	 */
	public function validate( $identifier, $password ) 
	{
		// our user
		$user = null;
	
		// get the identifiers
		$identifiers = static::$config->read( 'identifiers' );
	
		foreach( $identifiers as $property ) 
		{
			if ( !$user ) 
			{
				$user = \Model_User::find( $property, $identifier );
			} 
		}
	
		// still no result ?
		if ( !$user ) 
		{
			return false;
		}
		//var_dump( CCStr::hash( $password ), $user->password ); die;
		// does the password match
		if ( CCStr::hash( $password ) === $user->password ) 
		{
			return $user;
		}
	
		// does the password in md5
		if ( md5( $password ) === $user->password ) 
		{
			return $user;
		}
	
		return false;
	}
	
	
	
	/**
	 * generate the current restore key
	 *
	 * @param User	$user
	 * @return string
	 */
	public function restore_key( $user ) {
		return CCStr::hash( $user->username.'@'.$user->id.'%'.CCRequest::$clientAgent );
	}
	
	/**
	 * sign in a as user at instance
	 *
	 * @param id  		$user_id	
	 * @param string	$name
	 * @return bool
	 */
	public function sign_in( $user_id, $set_restore_key = true ) {
	
		if ( \Model_User::find( $user_id ) ) {
	
			// set user id int the session
			CCSession::instance( $this->name )->user_id = $user_id; 
	
			// set the user
			$this->user = $this->user();
	
			// pass the user object through all user hooks
			$this->user = CCEvent::pass( 'auth.signin', $this->user );
	
			// save the user object
			$this->user->save();
	
			/*
			 * set the restore key
			 */
			if ( $set_restore_key ) {
	
				// set restore cookies
				CCCookie::set( static::$config->read( 'keep_login_id_cookie' ), $this->user->id, CCDate::Month );
				CCCookie::set( static::$config->read( 'keep_login_cookie' ), $this->restore_key( $this->user ), CCDate::Month );
	
				$login = DB::select( 'logins' )
					->s_where( 'user_id', $this->user->id )
					->s_where( 'restore_key', $this->restore_key( $this->user ) );
	
				if ( !$login->run() ) {
	
					// insert the restore key
					DB::insert( 'logins', array(
						'user_id' 		=> $this->user->id,
						'restore_key'	=> $this->restore_key( $this->user ),
						'last_login'	=> time(),
						'ip'			=> CCRequest::$clientIP,
						'agent'			=> CCRequest::$clientAgent,
					))->run();
				}
				else {
					// update the restore key
					DB::update( 'logins', array(
						'last_login'	=> time(),
						'ip'			=> CCRequest::$clientIP,
						'agent'			=> CCRequest::$clientAgent,
					))
					->s_where( 'user_id', $this->user->id )
					->s_where( 'restore_key', $this->restore_key( $this->user ) )
					->run();
				}
			}
	
			// and finally we are authenticated
			return $this->authenticated = true;
		}
	
		return false;
	}
	
	/**
	 * sign in a as user at instance
	 *
	 * @param id  		$user_id	
	 * @param string	$name
	 * @return bool
	 */
	public function sign_out() {
	
		if ( !$this->authenticated ) 
		{
			return false;
		}
	
		// remove the restore login
		DB::delete( 'logins', $this->restore_key( $this->user ), 'restore_key' )->run();
	
		// logout the user
		CCSession::instance( $this->name )->user_id = 0;
	
		// pass the user object through all user hooks
		$this->user = CCEvent::pass( 'auth.signout', $this->user );
	
		$this->user = $this->user();
	
		return $this->authenticated = false;
	}
	
	/**
	 * get the user object
	 *
	 * @param mixed 			$user_key
	 * @return void
	 */
	protected function find_user( $user_key ) 
	{
		if ( $id = CCSession::instance( $this->name )->user_id ) {
			return \Model_User::find( $id );
		}
		else {
			return \Model_User::assign( array(
				'id'		=> 0,
			));
		}
	}
}