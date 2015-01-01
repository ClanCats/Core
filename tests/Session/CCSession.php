<?php
/**
 * Session tests
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2015 ClanCats GmbH
 *
 * @group Session
 * @group Session_CCSession
 */
class Test_Session_CCSession extends PHPUnit_Framework_TestCase
{
	/**
	 * prepare the configuration
	 *
	 * @return void
	 */
	public static function setUpBeforeClass() 
	{
		CCConfig::create( 'session' )->_data = CCConfig::create( 'Core::phpunit/session' )->_data;
	}

	/**
	 * test CCSession::set 
	 */
	public function test_set()
	{
		CCSession::set( 'a', 'b' );
		CCSession::set( 'a', 'c', 'file' );

		$this->assertEquals( 'b', CCSession::manager()->get( 'a' ) );
		$this->assertEquals( 'c', CCSession::manager( 'file' )->get( 'a' ) );
	}
	
	/**
	 * test CCSession::get 
	 */
	public function test_get()
	{
		CCSession::set( 'a', 'b' );
		CCSession::set( 'a', 'c', 'file' );
		
		$this->assertEquals( 'b', CCSession::get( 'a' ) );
		$this->assertEquals( 'c', CCSession::get( 'a', null, 'file' ) );
		
		$this->assertEquals( 'not', CCSession::get( 'foobar', 'not' ) );
	}
	
	/**
	 * test CCSession::has 
	 */
	public function test_has()
	{
		CCSession::set( 'a', 'b' );
		CCSession::set( 'a', 'c', 'file' );
		
		$this->assertTrue( CCSession::has( 'a' ) );
		$this->assertTrue( CCSession::has( 'a', 'file' ) );
		$this->assertFalse( CCSession::has( 'b' ) );
	}
	
	/**
	 * test CCSession::delete 
	 */
	public function test_delete()
	{
		CCSession::set( 'a', 'b' );
		CCSession::set( 'a', 'c', 'file' );
		
		$this->assertTrue( CCSession::has( 'a' ) );
		$this->assertTrue( CCSession::has( 'a', 'file' ) );
		
		CCSession::delete( 'a' );
		CCSession::delete( 'a', 'file' );
		
		$this->assertFalse( CCSession::has( 'a' ) );
		$this->assertFalse( CCSession::has( 'a', 'file' ) );
	}
}