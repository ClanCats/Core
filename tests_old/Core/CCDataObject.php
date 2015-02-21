<?php
/**
 * CCF Arr Test suite
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2015 ClanCats GmbH
 *
 * @group Core
 * @group CCDataObject
 */
class CCDataObject_Test extends \PHPUnit_Framework_TestCase
{

	/*
	 * an testing array
	 */
	public $test_array = array(
		'string'		=> 'bar',
		'true'		=> true,
		'false'		=> true,
		'array'		=> array(
			'number'	=> 13,
			'zero'	=> 0,
			'null'	=> null
		),	
	);
	
	/**
	 * test assignment
	 */
	public function testAssign() {
		
		$object = CCDataObject::assign( $this->test_array );
		
		// check if it matches
		$this->assertEquals( $object->raw(), $this->test_array );
	}
	
	/**
	 * test methods
	 */
	public function testMethods() {
		
		$object = CCDataObject::assign( $this->test_array );
		
		// test normal get 
		$this->assertEquals( $object->get( 'string' ), 'bar' );
		
		// test 2d get 
		$this->assertEquals( $object->get( 'array.number' ), 13 );
		
		// test set
		$object->set( 'test', 'hello' );
		$this->assertEquals( $object->get( 'test' ), 'hello' );
		
		// test set 2d
		$object->set( 'hello.value', 'world' );
		$this->assertEquals( $object->get( 'hello.value' ), 'world' );
		
		// test has
		$this->assertTrue( $object->has( 'test' ) );
		$this->assertTrue( $object->has( 'hello' ) );
		$this->assertFalse( $object->has( 'notexisting' ) );
		
		// test set 2d
		$this->assertTrue( $object->has( 'hello.value' ) );
		$this->assertTrue( $object->has( 'array.zero' ) );
		$this->assertFalse( $object->has( 'array.null' ) );
		
		// test delete
		$object->delete( 'string' );
		$this->assertFalse( $object->has( 'string' ) );
		
		// test 2d delete
		$object->delete( 'hello.value' );
		$this->assertFalse( $object->has( 'hello.value' ) );
	}
	
	/**
	 * CCDataObject::bind tests
	 */
	public function test_bind() 
	{
		$object = CCDataObject::assign( $this->test_array );
		
		$foo = 'Foo';
		
		$object->bind( 'foo', $foo );
		
		$this->assertEquals( 'Foo', $foo );
		$this->assertEquals( 'Foo', $object->foo );
		
		$foo = 'Bar';
		
		$this->assertEquals( 'Bar', $foo );
		$this->assertEquals( 'Bar', $object->foo );
	}
	
	/**
	 * test methods
	 */
	public function testMagicMethods() {
		
		$object = CCDataObject::assign( $this->test_array );
		
		// test isset
		$this->assertTrue( isset( $object->string ) );
		$this->assertFalse( isset( $object->notexisting ) );
		
		// test get 
		$this->assertEquals( $object->string, 'bar' );
		
		// test set
		$object->test = "hello";
		$this->assertEquals( $object->test, 'hello' );
		$this->assertEquals( $object->get( 'test' ), 'hello' );
		
		// test delete
		unset( $object->test );
		$this->assertFalse( isset( $object->test ) );
		$this->assertFalse( $object->has( 'test' ) );
	}
}