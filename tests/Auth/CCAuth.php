<?php
/**
 * CCF Auth Test suite
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 * @group Auth
 * @group Auth_CCAuth
 */
class Test_Auth_CCAuth extends DB\TestCase
{
	protected static $current_user = null;

	/**
	 * prepare the configuration
	 *
	 * @return void
	 */
	public static function setUpBeforeClass() 
	{
		parent::setUpBeforeClass();

		CCConfig::create( 'auth' )->_data = CCConfig::create( 'Core::phpunit/auth' )->_data;

		$user = new Auth\User;
		$user->email = "test@example.com";
		$user->password = "phpunit";
		$user->save();

		static::$current_user = $user;
	}

	/**
	 * CCAuth::handler tests
	 */
	public function test_handler()
	{
		$this->assertTrue( CCAuth::handler() instanceof Auth\Handler );
		$this->assertTrue( CCAuth::handler( 'other' ) instanceof Auth\Handler );
	}
	
	/**
	 * CCAuth::valid tests
	 */
	public function test_valid()
	{
		$this->assertFalse( CCAuth::valid() );
		$this->assertFalse( CCAuth::valid( 'other' ) );
	}
}