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
class Test_Auth_Handler extends \DB\Test_Case
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
		Auth\Handler::kill_instance( 'main' );

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

		// test user_id but user dont exists
		Auth\Handler::kill_instance( 'main' );

		$auth = Auth\Handler::create( 'main' );

		$auth->session->set( 'user_id', 21 );

		Auth\Handler::kill_instance( 'main' );

		$auth = Auth\Handler::create( 'main' );

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

	/**
	 * Handler::sign_in tests
	 */
	public function test_sign_in()
	{
		$example_user = clone static::$current_user;

		Auth\Handler::kill_instance( 'main' );
		Auth\Handler::kill_instance( 'same_session_manager' );
		Auth\Handler::kill_instance( 'diffrent_session_manager' );

		$auth = Auth\Handler::create();

		$auth->sign_in( $example_user, false );

		$this->assertTrue( $auth->user instanceof DB\Model );
		$this->assertEquals( static::$current_user->email, $auth->user->email );

		// this auth instance should be now also logged in
		$auth = Auth\Handler::create( 'same_session_manager' );

		$this->assertEquals( static::$current_user->email, $auth->user->email );

		// this not:
		$auth = Auth\Handler::create( 'diffrent_session_manager' );

		$this->assertNotEquals( static::$current_user->email, $auth->user->email );

		// test an event
		Auth\Handler::kill_instance( 'main' );

		$auth = Auth\Handler::create();

		$this->assertEquals( static::$current_user->email, $auth->user->email );

		CCEvent::mind( 'auth.sign_in', function( $user ) {
			$user->email = 'changed@example.com';
			return $user;
		});

		Auth\Handler::kill_instance( 'main' );

		$auth = Auth\Handler::create();

		$auth->sign_in( $example_user, false );

		$this->assertNotEquals( static::$current_user->email, $auth->user->email );
	}

	public function create_keeper_login()
	{
		$example_user = clone static::$current_user;

		$auth = Auth\Handler::create();

		// test now invalid because of sessions kill
		$auth->session->destroy();

		Auth\Handler::kill_instance( 'main' );

		$auth = Auth\Handler::create();

		$this->assertFalse( $auth->valid() );

		// now login with keeper
		$auth->sign_in( $example_user, true );

		$this->assertTrue( $auth->user instanceof DB\Model );
		$this->assertEquals( static::$current_user->id, $auth->user->id );

		// destroy again this time we should be able to restore the login
		$auth->session->destroy();

		Auth\Handler::kill_instance( 'main' );

		$auth = Auth\Handler::create();

		$this->assertTrue( $auth->valid() );
	}

	public function keeper_login_true()
	{
		$auth = Auth\Handler::create();

		$auth->session->destroy();

		Auth\Handler::kill_instance( 'main' );

		$auth = Auth\Handler::create();

		$this->assertTrue( $auth->valid() );
	}

	public function keeper_login_false()
	{
		$auth = Auth\Handler::create();

		$auth->session->destroy();

		Auth\Handler::kill_instance( 'main' );

		$auth = Auth\Handler::create();

		$this->assertFalse( $auth->valid() );
	}

	/**
	 * Handler::sign_in keep login tests
	 */
	public function test_sign_out()
	{
		$this->create_keeper_login();

		$this->keeper_login_true();

		$auth = Auth\Handler::create();
		$auth->sign_out();

		$this->keeper_login_false();

		$this->assertEquals( null, $auth->user->email );

		$this->assertFalse( $auth->sign_out() );
	}

	/**
	 * Handler::sign_in keep login tests
	 */
	public function test_sign_in_keeper()
	{
		Auth\Handler::kill_instance( 'main' );

		$example_user = clone static::$current_user;

		$auth = Auth\Handler::create();

		$auth->sign_in( $example_user, false );

		$this->assertTrue( $auth->user instanceof DB\Model );
		$this->assertEquals( static::$current_user->id, $auth->user->id );

		// test valid 
		Auth\Handler::kill_instance( 'main' );

		$auth = Auth\Handler::create();

		$this->assertTrue( $auth->valid() );

		// lets create an keeper login now
		$this->create_keeper_login();

		// lets test the login store event
		$this->assertEquals( null, $auth->login()->client_ip );

		$auth->session->destroy();

		CCEvent::mind( 'auth.store_login', function( $data ) 
		{
			$data['client_ip'] = '127.0.0.1';
			return $data;
		});

		Auth\Handler::kill_instance( 'main' );

		$auth = Auth\Handler::create();

		$this->assertTrue( $auth->valid() );

		$this->assertEquals( '127.0.0.1', $auth->login()->client_ip );

		// now lets modify some data to force restore failure

		// changing the the current client ip will force failure
		CCIn::instance( new CCIn_Instance( array(), array(), array(), array(), array( 'REMOTE_ADDR' => '192.168.1.42' ) ) );

		$this->keeper_login_false();

		// next lets modify the users password wich will force a failure
		$this->create_keeper_login();

		$this->keeper_login_true();

		static::$current_user->password = "anotherpassword";
		static::$current_user->save();

		$this->keeper_login_false();

		// modifiy the restore_id
		$this->create_keeper_login();

		$this->keeper_login_true();

		CCCookie::set( 'ccauth-restore-id', '34' );

		$this->keeper_login_false();

		// modifiy the restore_token
		$this->create_keeper_login();

		$this->keeper_login_true();

		CCCookie::set( 'ccauth-restore-token', 'wrong' );

		$this->keeper_login_false();

		// delete the user 
		$this->create_keeper_login();

		$this->keeper_login_true();

		static::$current_user->delete();

		$this->keeper_login_false();

		// create him again
		static::$current_user->save();
	}
}