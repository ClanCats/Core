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
		
		
		// diffrent keys
		CCSession::set( 'user_email', static::$current_user->email );
		
		$auth = Auth\Handler::create( 'diffrent_selector_keys' );
		
		$this->assertTrue( $auth->user instanceof CCModel );
		$this->assertTrue( $auth->valid() );
		
		// another auth same session manager
		$auth = Auth\Handler::create( 'same_session_manager' );
		
		$this->assertTrue( $auth->user instanceof CCModel );
		$this->assertTrue( $auth->valid() );
		
		// another auth diffrent session manager
		$auth = Auth\Handler::create( 'diffrent_session_manager' );
		
		$this->assertTrue( $auth->user instanceof CCModel );
		$this->assertFalse( $auth->valid() );
		
		// using an config alias
		$auth = Auth\Handler::create( 'alias' );
		
		$this->assertTrue( $auth->user instanceof CCModel );
		$this->assertTrue( $auth->valid() );
		
		// overwrite config
		$auth = Auth\Handler::create( 'main', array( 'session_manager' => 'array' ) );
		
		$this->assertTrue( $auth->user instanceof CCModel );
		$this->assertFalse( $auth->valid() );
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
	
	/**
	 * Handler::create tests
	 */
	public function test_validate()
	{
		$auth = Auth\Handler::create();
		
		$user = $auth->validate( 'not_existing', 'nothing' );
		
		$this->assertFalse( $user );
		
		$user = $auth->validate( static::$current_user->email, 'wrong' );
		
		$this->assertFalse( $user );
		$this->assertFalse( $user !== false );
		
		// right login
		$user = $auth->validate( static::$current_user->email, 'phpunit' );
		
		$this->assertTrue( $user !== false );
		
		// other driver
		$auth = Auth\Handler::create( 'diffrent_selector_keys' );
		
		$user = $auth->validate( static::$current_user->email, 'phpunit' );
		
		$this->assertTrue( $user !== false );
		
		// other identifiers
		$auth = Auth\Handler::create( 'diffrent_identifiers' );
		
		$user = $auth->validate( static::$current_user->email, 'phpunit' );
		
		$this->assertTrue( $user === false );
		
		$user = $auth->validate( static::$current_user->id, 'phpunit' );
		
		$this->assertTrue( $user !== false );
		
		// more identifiers
		$auth = Auth\Handler::create( 'multiple_identifiers' );
		
		$user = $auth->validate( static::$current_user->email, 'phpunit' );
		
		$this->assertTrue( $user !== false );
		
		$user = $auth->validate( static::$current_user->id, 'phpunit' );
		
		$this->assertTrue( $user !== false );
	}
}