<?php namespace Core;
/**
 * Session driver interface
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 */
interface CCSession_Driver {
	
	/**
	 * load a session
	 *
	 * @param $id
	 */
    public function load( $id );
	
	/**
	 * save a session
	 *
	 * @param $id
	 * @param $data
	 */
	public function save( $id, $data );
	
	/**
	 * check if a session exists
	 *
	 * @param $id
	 */
	public function check( $id );
	
	/**
	 * kill all outdated sessions
	 *
	 * @param $lifetime
	 */
	public function gc( $lifetime );
}