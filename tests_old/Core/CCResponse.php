<?php
/**
 * CCF Response Test suite
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2015 ClanCats GmbH
 *
 */
class CCResponse_Test extends \PHPUnit_Framework_TestCase
{

	/**
	 * Test response create event
	 */
	public function testCreate()
	{
		// create an instance
		$response = CCResponse::create( 'Hello World' );
		
		// did it work?
		$this->assertTrue( $response instanceof CCResponse );
		
		// does it contain hello world
		$this->assertEquals( 'Hello World', $response->body );
		
		// create an instance
		$response = CCResponse::create( 'Hello World', 400 );
		
		// did it work?
		$this->assertTrue( $response instanceof CCResponse );
		
		// does it contain hello world and 404
		$this->assertEquals( 'Hello World', $response->body );
		$this->assertEquals( 400, $response->status );
	}
	
	/**
	 * Test CCRouter event
	 */
	public function testJson()
	{
		// create an instance
		$response = CCResponse::json( array( 'test1', 'test2' ), 200, false );
		
		// did it work?
		$this->assertTrue( $response instanceof CCResponse );
		$this->assertEquals( '["test1","test2"]', $response->body );
	}
	
	/**
	 * Test CCRouter event
	 */
	public function testBody()
	{
		// create an instance
		$response = CCResponse::create();
		
		$response->body = "Test1";
		$this->assertEquals( 'Test1', $response->body );
		
		$response->body( "Test2" );
		$this->assertEquals( 'Test2', $response->body() );
	}
	
	/**
	 * Test CCRouter event
	 */
	public function testStatus()
	{
		// create an instance
		$response = CCResponse::create();
		
		$response->status = 400;
		$this->assertEquals( 400, $response->status );
		
		$response->status( 300 );
		$this->assertEquals( 300, $response->status() );
	}
	
	/**
	 * Test set headers
	 */
	public function testHeader()
	{
		// create an instance
		$response = CCResponse::create();
		
		$response->header( 'Test', 'Foo' );
		$this->assertEquals( 'Foo', $response->header( 'Test' ) );
	}
	
	/**
	 * Test response as download
	 */
	public function testAsDownload()
	{
		// create an instance
		$response = CCResponse::create();
		
		// just run the method to check for errors
		$response->as_download();
		
		// just run the method to check for errors
		$response->as_download( 'myfile.xml' );
		
		// just run the method to check for errors
		CCResponse::download( "Hello File" );
		
		// just run the method to check for errors
		CCResponse::download( "Hello File", 'myfile.txt' );
		
		// just run the method to check for errors
		CCResponse::download( "Hello File", 'myfile.txt', 201 );
	}
	
	/**
	 * Test response send
	 */
	public function testSend()
	{
		// create an instance
		$response = CCResponse::create();
		
		// just run the method to check for errors
		$response->send();
		
		// just run the method to check for errors
		$response->send( true );
	}
}