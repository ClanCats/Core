<?php namespace ClanCats\Core\Test;
/**
 * CCF class tests
 * 
 * @group Core
 * @group Core_CCF
 */
class CCF extends \PHPUnit_Framework_TestCase
{
	/**
	 * Test CC version
	 */
	public function testVersion()
	{
		$this->assertTrue( !is_null( \CCF::version() ) );
		$this->assertTrue( ( (int) str_replace('.', '', \CCF::version() ) ) >= 300 );
	}
	
	/**
	 * Test CC environment
	 */
	public function testEnvironment()
	{
		$this->assertEquals( 'phpunit', \CCF::environment() );
	}
}