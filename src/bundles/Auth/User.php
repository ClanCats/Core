<?php namespace Auth;
/**
 * User model
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 */
class User extends \DB\Model
{
	/**
	 * Hidden fields
	 */
	protected static $_hidden = array( 'password' );
	
	/**
	 * Visible fields
	 */
	protected static $_visible = array( 'fullname' );
	
	/**
	 * The user model defaults
	 *
	 * @var array
	 */
	protected static $_defaults = array(
		'id'	,
		'active'			=> array( 'bool', 1 ),
		'username'		=> null,
		'email'			=> null,
		'password'		=> null,
		'storage'		=> array( 'json', array() ),
		'last_login'		=> array( 'timestamp' ),
		'created_at'		=> array( 'timestamp' ),
		'modified_at'	=> array( 'timestamp' ),
	);
	
	/**
	 * Full name
	 */
	protected function _get_modifier_fullname()
	{
		if ( !isset( $this->storage['firstname'] ) )
		{
			return $this->username;
		}
		
		return $this->storage['firstname'].' '.$this->storage['lastname'];
	}
}