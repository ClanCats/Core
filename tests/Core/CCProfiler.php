<?php
/**
 * CCF Profiler tests
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2015 ClanCats GmbH
 *
 * @group Core
 * @group CCProfiler
 */
class Test_CCProfiler extends \PHPUnit_Framework_TestCase
{
	/**
	 * CCProfiler::check tests
	 */
	public function test_data() 
	{
		// check that there is something
		$this->assertTrue( count( CCProfiler::data() ) > 0 );
		
		// check that is nothing
		CCProfiler::reset();
		$this->assertFalse( count( CCProfiler::data() ) > 0 );
		
		// check that there is something again
		CCProfiler::check( 'item' );
		$this->assertTrue( count( CCProfiler::data() ) > 0 );
	}
	
	/**
	 * CCProfiler::check tests
	 */
	public function test_check() 
	{
		CCProfiler::reset();
		
		$this->assertEquals( count( CCProfiler::data() ), 0 );
		
		CCProfiler::check( 'check 1' );
		
		$this->assertEquals( count( CCProfiler::data() ), 1 );
		
		CCProfiler::check( 'check 2' );
		
		$this->assertEquals( count( CCProfiler::data() ), 2 );
	}
	
	/**
	 * CCProfiler::check tests
	 */
	public function test_enable_disable() 
	{
		$checks = count( CCProfiler::data() );
		
		CCProfiler::check( 'check 1' );
		
		$this->assertEquals( count( CCProfiler::data() ), $checks+1 );
		
		CCProfiler::disable();
		
		CCProfiler::check( 'check 2' );
		
		$this->assertEquals( count( CCProfiler::data() ), $checks+1 );
		
		CCProfiler::enable();
		
		CCProfiler::check( 'check 3' );
		
		$this->assertEquals( count( CCProfiler::data() ), $checks+2 );
	}
}