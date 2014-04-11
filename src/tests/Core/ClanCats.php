<?php
/**
 * CCF Core Test suite
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 */
class ClanCats_Test extends \PHPUnit_Framework_TestCase
{
	/**
	 * Test CC version
	 */
	public function testVersion()
	{
		$this->assertTrue( !is_null( \ClanCats::version() ) );
	}
	
	/**
	 * Test CC environment
	 */
	public function testEnvironment()
	{
		$this->assertEquals( 'phpunit', \ClanCats::environment() );
	}
	
	/**
	 * Test CC environment is
	 */
	public function testEnvironment_is()
	{
		$this->assertTrue( \ClanCats::environment_is( 'phpunit' ) );
		$this->assertFalse( \ClanCats::environment_is( 'development' ) );
	}
	
	/**
	 * Test CC runtime
	 */
	public function testRuntime()
	{
		$this->assertTrue( !is_null( \ClanCats::runtime() ) );
	}
	
	/**
	 * Test CC cli
	 */
	public function testIs_cli()
	{
		$this->assertTrue( \ClanCats::is_cli() );
	}
	
	/**
	 * Test CC development
	 */
	public function testIn_development()
	{
		$this->assertFalse( \ClanCats::in_development() );
	}
	
	/**
	 * Test CC paths
	 */
	public function testPaths()
	{
		// check return
		$this->assertTrue( is_array( \ClanCats::paths() ) );
		
		// add a path
		ClanCats::paths( array(
			'phpunittest'	=> CCROOT.'phpunit/',
		));
		
		// check define
		$this->assertEquals( CCROOT.'phpunit/', PHPUNITTESTPATH );
		
		// check array
		$this->assertTrue( in_array( 'phpunittest', array_keys( \ClanCats::paths() ) ) );
	}
	
	/**
	 * Test CC directories
	 */
	public function testDirectories()
	{
		// check return
		$this->assertTrue( is_array( \ClanCats::directories() ) );
		
		// add a path
		ClanCats::directories( array(
			'phpunittest'	=> 'phpunit/',
		));
		
		// check define
		$this->assertEquals( 'phpunit/', CCDIR_PHPUNITTEST );
		
		// check array
		$this->assertTrue( in_array( 'phpunittest', array_keys( \ClanCats::directories() ) ) );
	}
	
	/**
	 * Test CC directories
	 */
	public function testEnvironment_detector()
	{
		$original_host = $_SERVER['HTTP_HOST'];
		
		$_SERVER['HTTP_HOST'] = 'localhost';
		
		$this->assertEquals( 'development', \ClanCats::environment_detector( array(
			'local*'		=> 'development',
			':all'		=> 'testenv',
		)));
		
		$_SERVER['HTTP_HOST'] = 'example.com';
		
		$this->assertEquals( 'testenv', \ClanCats::environment_detector( array(
			'local*'		=> 'development',
			':all'		=> 'testenv',
		)));
		
		$this->assertEquals( 'example_env', \ClanCats::environment_detector( array(
			'local*'		=> 'development',
			
			'example.com'	=> 'example_env',
			
			':all'		=> 'testenv',
		)));
		
		$this->assertEquals( 'example_env', \ClanCats::environment_detector( array(
			'local*'		=> 'development',
			
			'example*'	=> 'example_env',
			
			':all'		=> 'testenv',
		)));
		
		$this->assertEquals( 'example_env', \ClanCats::environment_detector( array(
			'local*'		=> 'development',
			
			'*example.com'	=> 'example_env',
			
			':all'		=> 'testenv',
		)));
		
		$_SERVER['HTTP_HOST'] = 'www.example.com';
		
		$this->assertEquals( 'example_env', \ClanCats::environment_detector( array(
			'local*'		=> 'development',
			
			'*example.com'	=> 'example_env',
			
			':all'		=> 'testenv',
		)));
	}
}