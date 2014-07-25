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
class Test_Auth_CCAuth extends \PHPUnit_Framework_TestCase
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
	
	/**
	 * CCAuth::validate tests
	 */
	public function test_validate()
	{
		$this->assertFalse( CCAuth::validate() );
		$this->assertFalse( CCAuth::validate( 'somthing', 'wrong' ) );
		$this->assertFalse( CCAuth::validate( 'test@example.com', 'wrongpass' ) );
		
		$this->assertTrue( CCAuth::validate( 'test@example.com', 'phpunit' ) instanceof DB\Model );
	}
	
	/**
	 * CCAuth::sign_in and sign_out tests
	 */
	public function test_sign()
	{
		$this->assertFalse( CCAuth::valid() );
		$this->assertTrue( CCAuth::sign_in( static::$current_user ) );
		$this->assertTrue( CCAuth::valid() );
		$this->assertFalse( CCAuth::sign_out() );
	}
}