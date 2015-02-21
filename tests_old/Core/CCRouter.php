<?php
/**
 * CCF Router Test suite
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2015 ClanCats GmbH
 *
 * @group Core
 * @group CCRouter
 */
class CCRouter_Test extends \PHPUnit_Framework_TestCase
{
	
	/**
	 * Test CCRouter event
	 */
	public function test_event()
	{
		CCRouter::on( 'never/reach/me', function(){
			echo "I Do";
		});
		
		CCRouter::on( 'never/reach/me/to', function(){
			echo "Ohu";
		});
		
		$called = false;
		
		// register to similar events
		CCRouter::event( 'sleep', 'never/reach/me/to', function() use ( &$called ) {
			$called = true;
		});
		CCRouter::event( 'sleep', 'never/reach/me/to', function() {} );
		
		// check if they got called
		$this->assertEquals( CCRequest::uri( 'never/reach/me/to' )->perform()->response()->body , "Ohu" );
		$this->assertTrue( $called );
		
		// check if the route does what it should
		$this->assertEquals( CCRequest::uri( 'never/reach/me' )->perform()->response()->body , "I Do" );
		
		// add a wake event
		CCRouter::event( 'wake', 'never/reach*', function() {
			return CCResponse::create( ":(" );
		});
		
		// should be not called anymore
		$this->assertEquals( CCRequest::uri( 'never/reach/me' )->perform()->response()->body , ":(" );
		$this->assertEquals( CCRequest::uri( 'never/reach/me/to' )->perform()->response()->body , ":(" );
		
	}
	
	/**
	 * Test CCRouter event
	 */
	public function test_event_matching()
	{
		CCRouter::event( 'wake', 'never*', function() {
			return CCResponse::create( "Whoop" );
		});
		
		// get events matching a route
		$this->assertEquals( count( CCRouter::events_matching( 'wake', 'never/reach/me/never/eva/eva' ) ), 2 );
		
		// get not existing sleep event
		$this->assertEquals( count( CCRouter::events_matching( 'sleep', 'never/reach/me/never/eva/eva' ) ), 0 );
		
		// get existing sleep event
		$this->assertEquals( count( CCRouter::events_matching( 'sleep', 'never/reach/me/to' ) ), 2 );
	}
	
	/**
	 * Test CCRouter alias
	 */
	public function test_alias()
	{
		
		CCRouter::on( 'phpunit/test-alias1', array( 'alias' => 'phpunit.case.1' ), function()
		{
			echo "Tests are Awesome!";
		});
		
		// check the alias
		$this->assertEquals( CCRouter::alias( 'phpunit.case.1' ), 'phpunit/test-alias1' );
		
		// not existing
		$this->assertFalse( CCRouter::alias( 'phpunit.case.notexisting' ) );
		
		// set manually
		CCRouter::alias( 'phpunit.case.2', 'go/to/this/uri' );
		
		// check the alias
		$this->assertEquals( CCRouter::alias( 'phpunit.case.2' ), 'go/to/this/uri' );
		
		// dynamics
		CCRouter::on( 'phpunit/test2-[num]-alias-[any]', array( 'alias' => 'phpunit.case.3' ), function( $num ){
			echo "Tests are Awesome!";
		});
		
		// check the alias
		$this->assertEquals( CCRouter::alias( 'phpunit.case.3' ), 'phpunit/test2-[num]-alias-[any]' );
		
		// parameterize
		$this->assertEquals( CCRouter::alias( 'phpunit.case.3', array( 21, 'woo' ) ), 'phpunit/test2-21-alias-woo' );
		
		// alternate alias definition
		CCRouter::on( array(
			'aliasshortcut@alicut' => function() { echo 'foobar'; },
		));
		
		$this->assertEquals( 'aliasshortcut', CCRouter::alias( 'alicut' ) );
		
	}
	
	/**
	 * Test CCRouter on
	 */
	public function test_on() 
	{
		CCRouter::on( 'phpunit', function(){
			echo "Tests are Awesome!";
		});
		
		CCRouter::on( 'phpunit/test1', array(), function(){
			echo "Tests are Awesome!";
		});
		
		CCRouter::on( 'phpunit/test2-[num]', function( $num ){
			echo "Tests are Awesome!";
		});
		
		CCRouter::on( 'phpunit/test3', array(
			'alias' => 'phpunit.case.3',
			'wake'	=> function() {},
			'sleep'	=> function() {},
		), function()
		{
			echo "Tests are Awesome!";
		});
		
		// default callback
		CCRouter::on( 'phpunit/test4', array( $this, 'test_response' ) );
		
		// return callback
		CCRouter::on( 'phpunit/test5', array( $this, 'test_return' ) );
		
		$response = CCRequest::uri( 'phpunit' )->perform()->response()->body;
		$this->assertEquals( $response , "Tests are Awesome!" );
		
		$response = CCRequest::uri( 'notexisting' )->perform()->response()->status;
		$this->assertEquals( $response , 404 );
		
		$response = CCRequest::uri( 'phpunit/test1' )->perform()->response()->body;
		$this->assertEquals( $response , "Tests are Awesome!" );
		
		$response = CCRequest::uri( 'phpunit/test2-'.mt_rand(0,9999) )->perform()->response()->body;
		$this->assertEquals( $response , "Tests are Awesome!" );
		
		$response = CCRequest::uri( 'phpunit/test3' )->perform()->response()->body;
		$this->assertEquals( $response , "Tests are Awesome!" );
		
		$response = CCRequest::uri( 'phpunit/test4' )->perform()->response()->body;
		$this->assertEquals( $response , "Callbacks are pretty cool" );
		
		$response = CCRequest::uri( 'phpunit/test5' )->perform()->response()->body;
		$this->assertEquals( $response , "Callback returns are also pretty cool" );
		
		/*
		 * Test on with controllers
		 */
		CCRouter::on( 'controller_test', 'CCUnit::Test' );
		$response = CCRequest::uri( 'controller_test' )->perform()->response()->body;
		$this->assertEquals( "Hello World", $response );
		
		CCRouter::on( 'controller_action_test', 'CCUnit::Test@detail' );
		$response = CCRequest::uri( 'controller_action_test' )->perform()->response()->body;
		$this->assertEquals( "Test Action", $response );
	}
	
	/**
	 * Tests the CCRouter filter escaping
	 */
	public function test_escaping() 
	{
		CCRouter::on( 'this/u$l/conta([num])n+special/[any]', function(){
			echo "Escaping works";
		});
		
		$response = CCRequest::uri( 'this/u$l/conta(1)n+special/chars' )->perform()->response()->body;
		$this->assertEquals( $response , "Escaping works" );
	}
	
	/**
	 * Returns an test CCResposne
	 */
	public function test_response() 
	{
		return CCResponse::create( 'Callbacks are pretty cool' );
	}
	
	/**
	 * Returns an test text
	 */
	public function test_return() 
	{
		return 'Callback returns are also pretty cool';
	}
}