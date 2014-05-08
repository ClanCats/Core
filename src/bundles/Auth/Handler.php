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
	
	/*
	 * the user object
	 */
	public $user = null;
	
	/*
	 * is the instance authenticated
	 */
	public $authenticated = false;
	
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
	public function __construct( $name ) 
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
		
		if ( empty( $config ) )
		{
			throw new Exception( "Auth\\Handler::create - Invalid auth handler (".$name.")." );
		}
		
		// also don't forget to set the name manager name becaue we need him later.
		$this->name = $name;
		
		// keep the configuration array
		$this->config = $config;
		
		// set the session handler
		$this->session = CCSession::manager( $config['session_manager	'] );
		
		$user_model = $this->config['user_model'];
	
		// do we already have a user id means are we
		// logged in?
		if ( ( $session_key = $this->session_key() ) > 0 )
		{
			if ( $user = $user_model::find( $this->config['user_key'], $session_key ) )
		}
		
		/*
		 * try to restore the login
		 */
		else {
			if ( CCCookie::has( static::$config->read( 'keep_login_id_cookie' ) ) ) {
	
				// get restore cookies
				$restore_id = CCCookie::get( static::$config->read( 'keep_login_id_cookie' ) );
				$restore_key = CCCookie::get( static::$config->read( 'keep_login_cookie' ) );
	
				// get the restore login
				$login = DB::select( 'logins' )
					->s_where( 'user_id', $restore_id )
					->s_where( 'restore_key', $restore_key );
	
				// check if its exists
				if ( !$login = $login->run() ) {
					CCCookie::delete( static::$config->read( 'keep_login_id_cookie' ) );
					CCCookie::delete( static::$config->read( 'keep_login_cookie' ) );
					return $this->authenticated = false;
				}
	
				// get the user
				$user = \Model_User::find( $restore_id );
	
				// does the old restore key match the old one?
				if ( $login['restore_key'] != $this->restore_key( $user ) ) {
					return $this->authenticated = false;
				}
	
				// sign in
				return $this->sign_in( $restore_id, true );
			}
		}
	
		return $this->authenticated = false;
	}
	
	/**
	 * Get the current user session key 
	 * 
	 * @return mixed
	 */
	public function session_key() 
	{
		return $this->session->get( CCArr::get( 'session_key', $this->config, 'user_id' ) );
	}
	
	/**
	 * validate auth 
	 *
	 * @param string 	$identifier
	 * @param string 	$password
	 * @return mixed  	false if it fails user object on success
	 */
	public function validate( $identifier, $password ) {
	
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
	 * check if the user is authenticated
	 *
	 * @param string 	$name
	 * @return bool
	 */
	public function valid( $name = NULL ) {
		return static::instance( $name )->authenticated;
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