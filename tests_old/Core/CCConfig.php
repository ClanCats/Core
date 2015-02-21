<?php
/**
 * CCF Config Test suite
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2015 ClanCats GmbH
 *
 */
class CCConfig_Test extends \PHPUnit_Framework_TestCase
{
	/**
	 * config create
	 */
	public function testCreate()
	{
		$config = CCConfig::create();
		
		$this->assertTrue( $config instanceof CCConfig );
		
		$this->assertFalse( isset( $config->foo ) );
		
		$config = CCConfig::create( 'CCUnit::test' );
		
		$this->assertTrue( isset( $config->foo ) );
		
		$this->assertEquals( 'bar', $config->foo );
	}
	
	/**
	 * config name
	 */
	public function testName()
	{
		$config = CCConfig::create( 'CCUnit::test' );
		$config->name( 'CCUnit::test2' );
		
		$this->assertEquals( 'CCUnit::test2', $config->name() );
	}
	
	/**
	 * config driver not existing
	 */
	public function testDriverNotExisting()
	{
		$this->setExpectedException( 'InvalidArgumentException' );		
		$config = CCConfig::create( null, 'beep' );
	}
	
	/**
	 * config driver
	 */
	public function testDriverChange()
	{	
		$config = CCConfig::create( 'CCUnit::test' );
		
		$config->driver( 'json' );
	}
	
	/**
	 * config read
	 */
	public function testRead()
	{
		$config = CCConfig::create();
		
		$config->read( 'CCUnit::test' );
		
		$this->assertEquals( 'bar', $config->foo );
		
		$this->assertEquals( 'yay', $config->get('test.me') );
	}
	
	/**
	 * config read
	 */
	public function testWriteNative()
	{
		$this->setExpectedException( 'CCException' );	
		
		$config = CCConfig::create();
		$config->read( 'CCUnit::test' );
		$config->write();
	}
	
	/**
	 * config read
	 */
	public function testWriteJson()
	{
		$config = CCConfig::create( 'CCUnit::test' );
		$config->driver( 'json' );
		$config->name( 'CCUnit::temp' );
		
		$config->write();
		
		$config = CCConfig::create( 'CCUnit::temp', 'json' );
		
		$this->assertEquals( 'bar', $config->foo );
		
		$this->assertEquals( 'yay', $config->get('test.me') );
		
		// delte file
		$config->_delete();
	}
	
	/**
	 *  test write failure without name
	 */
	public function testNoNameWrite()
	{
		$this->setExpectedException( 'CCException' );
		
		$config = CCConfig::create();
		$config->write();
	}
	
	/**
	 * test delete failure without name
	 */
	public function testNoNameDelete()
	{
		$this->setExpectedException( 'CCException' );
		
		$config = CCConfig::create();
		$config->_delete();
	}
}