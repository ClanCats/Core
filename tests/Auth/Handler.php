<?php
/**
 * CCF Arr Test suite
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 * @group Auth
 * @group Auth_Handler
 */
class Test_Auth_Handler extends DB\TestCase
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
	 * Handler::create tests
	 */
	public function test_create()
	{
		$auth = Auth\Handler::create();
		
		$this->assertTrue( $auth->user instanceof CCModel );
		$this->assertFalse( $auth->valid() );
		
		$auth2 = Auth\Handler::create( 'other' );
		
		$this->assertEquals( $auth, Auth\Handler::create() );
		$this->assertEquals( $auth2, Auth\Handler::create( 'other' ) );
		
		$this->assertNotEquals( $auth, $auth2 );
		
		// create with existing one
		CCSession::set( 'user_id', static::$current_user->id );
		
		// kill the old instance
		$auth = Auth\Handler::kill_instance( 'main' );
		
		// redo
		$auth = Auth\Handler::create();
		
		$this->assertTrue( $auth->user instanceof CCModel );
		$this->assertTrue( $auth->valid() );
		
	}
	
	/**
	 * Handler::create not existing tests
	 *
	 * @expectedException        Auth\Exception
	 */
	public function test_create_unknown()
	{
		Auth\Handler::create( 'nopenotinghere' );
	}
}