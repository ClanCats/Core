<?php
/**
 * CCF Container Test suite
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 */
class CCContainer_Test extends \PHPUnit_Framework_TestCase
{
	/**
	 * Test Container
	 */
	public function testContainer() 
	{
		// default call
		CCContainer::mind( 'user.valid', function() 
		{
			return true;
		});
		
		$this->assertTrue( CCContainer::call( 'user.valid' ) );
		
		// params call
		CCContainer::mind( 'math.double', function( $num, $prefix ) 
		{
			return $prefix.( $num * 2 );
		});
		
		$this->assertEquals( CCContainer::call( 'math.double', 2, 'Zahl: ' ), 'Zahl: 4' );
		
		// callback call
		CCContainer::mind( 'test.callback', array( $this, 'returnTest' ) );
		$this->assertEquals( CCContainer::call( 'test.callback' ), 'Test' );
	}
	
	/**
	 * Test Container
	 */
	public function testHas() 
	{	
		$this->assertTrue( CCContainer::has( 'user.valid' ) );
		$this->assertFalse( CCContainer::has( 'not.existing' ) );
	}
	
	/**
	 * Test Container callable calls
	 */
	public function testCallable()
	{	
		$this->assertTrue( CCContainer::is_callable( 'user.valid' ) );
		$this->assertFalse( CCContainer::is_callable( 'not.existing' ) );
		$this->assertTrue( CCContainer::is_callable( function() {} ) );
		$this->assertTrue( CCContainer::is_callable( array( $this, 'testCall' ) ) );
		$this->assertFalse( CCContainer::is_callable( array( $this, 'nope' ) ) );
	}
	
	/**
	 * Test Container direct calls
	 */
	public function testCall() 
	{	
		$this->assertTrue( CCContainer::call( 'user.valid' ) );
	}
	
	/**
	 * Test Container direct calls
	 */
	public function testShortcut() 
	{	
		$this->assertTrue( _c( 'user.valid' ) );
	}
	
	/**
	 * this is to test the callback
	 */
	public function returnTest() 
	{
		return "Test";
	}
}