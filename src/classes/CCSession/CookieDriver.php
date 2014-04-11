<?php namespace Core;
/**
 * Session cookie handler
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 */
class CCSession_CookieDriver implements CCSession_Driver {
	
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
	 * security salt
	 */
	protected $salt;
	
	/**
	 * driver constructor
	 */
	protected function __construct( $name ) {
		$this->name = $name;
		
		// set session configs
		$this->lifetime = CCSession::$config->get('lifetime');
		$this->salt = CCSession::$config->get('cookie.salt');
	}
	
	/**
	 * load a session
	 *
	 * @param $id
	 */
    public function load( $id ) {
		return unserialize( \CCCrypter::decode( \CCCookie::get( $id ), $this->salt ) );
	}
	
	/**
	 * save a session
	 *
	 * @param $id
	 * @param $data
	 */
	public function save( $id, $data ) {
		\CCCookie::set( $id, \CCCrypter::encode( serialize( $data ), $this->salt ), $this->lifetime );
	}
	
	/**
	 * check if a session exists
	 *
	 * @param $id
	 */
	public function check( $id ) {
		return \CCCookie::has( $this->name.'_'.$id );
	}
	
	/**
	 * kill all outdated sessions
	 *
	 * @param $lifetime
	 */
	public function gc( $lifetime ) {}
}