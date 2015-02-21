<?php
/**
 * CCF php forge Test suite
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2015 ClanCats GmbH
 *
 */
class CCForge_Php_Test extends \PHPUnit_Framework_TestCase
{

	/**
	 * Test header
	 */
	public function testHeader()
	{
		// create new forge 
		$forge = CCForge_Php::create( 'Phpunit' );
		
		// test header with namespace
		$this->assertEquals( $forge->header( 'Test1' ), '<?php namespace Test1;' );
		
		// test header without namespace
		$this->assertEquals( $forge->header(), '<?php' );
	}
	
	/**
	 * Test comments
	 */
	public function testComments()
	{
		// create new forge 
		$forge = CCForge_Php::create();
		
		// simpel comment
		$this->assertEquals( $forge->comment( 'Test' ), "/**\n * Test\n */" );
		
		// multi line comment
		$this->assertEquals( $forge->comment( "Foo\nBar" ), "/**\n * Foo\n * Bar\n */" );
		
		// dott line comment
		$this->assertEquals( $forge->comment( "Foo\n*\nBar" ), "/**\n * Foo\n **\n * Bar\n */" );
	}
	
	/**
	 * Test class
	 */
	public function testClass()
	{
		// create new forge 
		$forge = CCForge_Php::create();
		
		// simpel class
		$this->assertEquals( $forge->a_class( 'Test', 'Woosa' ), "class Test\n{\n\tWoosa\n}" );
		
		// class extends 
		$this->assertEquals( $forge->a_class( 'Test', 'Foo', 'Bar' ), "class Test extends Bar\n{\n\tFoo\n}" );
		
		// class implements 
		$this->assertEquals( $forge->a_class( 'Test', 'Foo', null, 'Bar' ), "class Test implements Bar\n{\n\tFoo\n}" );
		
		// simpel class multiline
		$this->assertEquals( $forge->a_class( 'Test', "Foo\nBar" ), "class Test\n{\n\tFoo\n\tBar\n}" );
		
		// class callback
		$this->assertEquals( $forge->a_class( 'Test', function() { echo "Woosa"; } ), "class Test\n{\n\tWoosa\n}" );
		
		// class callback return
		$this->assertEquals( $forge->a_class( 'Test', function() { return "Foo\nBar"; } ), "class Test\n{\n\tFoo\n\tBar\n}" );
	}
	
	/**
	 * Test property
	 */
	public function testProperty()
	{
		// create new forge 
		$forge = CCForge_Php::create();
		
		// simpel property
		$this->assertEquals( $forge->property( 'var $foo' ), 'var $foo;' );
		
		// with default
		$this->assertEquals( $forge->property( 'public static $foo', 100 ), 'public static $foo = 100;' );
		
		// with default string
		$this->assertEquals( $forge->property( 'public $foo', 'bar' ), 'public $foo = \'bar\';' );
		
		// with default and comment
		$this->assertEquals( $forge->property( 'public $foo', 'bar', 'foo var' ), "/**\n * foo var\n */\npublic \$foo = 'bar';" );
	}
	
	/**
	 * Test closure
	 */
	public function testClosure()
	{
		// create new forge 
		$forge = CCForge_Php::create();
		
		// simpel property
		$this->assertEquals( $forge->closure( 'public function test()', '// woosa' ), "public function test()\n{\n\t// woosa\n}" );
		
		// with default
		$this->assertEquals( $forge->property( 'public static $foo', 100 ), 'public static $foo = 100;' );
		
		// with default string
		$this->assertEquals( $forge->property( 'public $foo', 'bar' ), 'public $foo = \'bar\';' );
		
		// with default and comment
		$this->assertEquals( $forge->property( 'public $foo', 'bar', 'foo var' ), "/**\n * foo var\n */\npublic \$foo = 'bar';" );
	}
}