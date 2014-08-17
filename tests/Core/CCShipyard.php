<?php
/**
 * CCF shipyard test suite
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 * @group Core
 * @group CCShipyard
 */
class Test_CCShipyard extends \DB\Test_Case_Database
{
	/** 
	 * test invalid driver
	 *
	 * @expectedException CCException
	 */
	public function test_invalid_builder()
	{
		CCShipyard::create( 'invalid_builder' );
	}
	
	/** 
	 * tests class builder
	 */
	public function test_class()
	{
		$builder = CCShipyard::create( 'class', 'CCUnit::Foo' );
		
		$this->assertRegExp( '/<?php namespace CCUnit;/i', $builder->output() );
		
		$this->assertRegExp( '/class Foo/i', $builder->output() );
		
		$this->assertTrue( strpos( $builder->output(), '<?php namespace' ) !== false );
		
		// no namespace
		$builder = CCShipyard::create( 'class', 'Foo' );
		
		$this->assertTrue( strpos( $builder->output(), '<?php namespace' ) === false );
		
		// superclass
		$builder = CCShipyard::create( 'class', 'Foo', 'Bar' );
		
		$this->assertRegExp( '/class Foo extends Bar/i', $builder->output() );
		
		// one interface
		$builder = CCShipyard::create( 'class', 'Foo', null, 'MyInterface' );
		
		$this->assertRegExp( '/class Foo implements MyInterface/i', $builder->output() );
		
		// 2 interfaces and superclass
		$builder = CCShipyard::create( 'class', 'Foo', 'Bar', array( 'FirstInterface', 'SecoundInterface' ) );
		
		$this->assertRegExp( '/class Foo extends Bar implements FirstInterface, SecoundInterface/i', $builder->output() );
	}
	
	/** 
	 * tests class builder properties
	 */
	public function test_class_properties()
	{
		// simple property
		$builder = CCShipyard::create( 'class', 'Foo' );
		
		$builder->add( 'property', 'a' );
		
		$this->assertRegExp( '/public \$a\;/i', $builder->output() );
		
		// private property
		$builder = CCShipyard::create( 'class', 'Foo' );
		
		$builder->add( 'property', 'a', 'private' );
		
		$this->assertRegExp( '/private \$a\;/i', $builder->output() );
		
		// with default
		$builder = CCShipyard::create( 'class', 'Foo' );
		
		$builder->add( 'property', 'a', 'protected', 50 );
		
		$this->assertRegExp( '/protected \$a = 50\;/i', $builder->output() );
		$this->assertRegExp( '/\@var int/i', $builder->output() );
		
		// with bool
		$builder = CCShipyard::create( 'class', 'Foo' );
		
		$builder->add( 'property', 'a', 'private', false );
		
		$this->assertRegExp( '/private \$a = false\;/i', $builder->output() );
		$this->assertRegExp( '/\@var bool/i', $builder->output() );
		
		// with comment and default string
		$builder = CCShipyard::create( 'class', 'Foo' );
		
		$builder->add( 'property', 'a', 'protected', "array( 'foo' => 'bar' )", "Some comment" );
		
		$this->assertRegExp( '/protected \$a = \'array\(/i', $builder->output() );
		$this->assertRegExp( '/\@var string/i', $builder->output() );
		
		// with not exported default
		$builder = CCShipyard::create( 'class', 'Foo' );
		
		$builder->add( 'property', 'a', 'protected', "array( 'foo' => 'bar' )", "Some array", false );
		
		$this->assertRegExp( '/protected \$a = array\(/i', $builder->output() );
		$this->assertRegExp( '/\Some array/i', $builder->output() );
	}
	
	/** 
	 * tests class builder functions
	 */
	public function test_class_functions()
	{
		// simple property
		$builder = CCShipyard::create( 'class', 'Foo' );
		
		$builder->add( 'function', 'say' );
		
		$this->assertRegExp( '/public function say()/i', $builder->output() );
		$this->assertRegExp( '/\* Say\(\) function/i', $builder->output() );
		
		// other context
		$builder = CCShipyard::create( 'class', 'Foo' );
		
		$builder->add( 'function', 'say', 'private static' );
		
		$this->assertRegExp( '/private static function say()/i', $builder->output() );
		
		// function content
		$builder = CCShipyard::create( 'class', 'Foo' );
		
		$builder->add( 'function', 'say', 'protected static', 'return "hello";' );
		
		$this->assertRegExp( '/return \"hello\"\;/i', $builder->output() );
		
		// params with comment
		$builder = CCShipyard::create( 'class', 'Foo' );
		
		$builder->add( 'function', 'say( $what )', 'protected static', 'return $what;', 'Say something load' );
		
		$this->assertRegExp( '/protected static function say\( \$what \)/i', $builder->output() );
		$this->assertRegExp( '/Say something load/i', $builder->output() );
	}
	
	/** 
	 * tests model builder
	 */
	public function test_builder_model()
	{
		// simple property
		$builder = CCShipyard::create( 'dbmodel', 'People', 'people' );
		
		$this->assertRegExp( '/class People extends/i', $builder->output() );
		$this->assertRegExp( '/protected static \$_table = \'people\'\;/i', $builder->output() );
		$this->assertRegExp( '/protected static \$_defaults = array\(/i', $builder->output() );
		
		$builder->timestamps();
		
		$this->assertRegExp( '/protected static \$_timestamps = true\;/i', $builder->output() );
	}
}