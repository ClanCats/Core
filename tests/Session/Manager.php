<?php
/**
 * Session tests
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 * @group Session
 * @group Session_Manager
 */
class Test_Session_Manager extends PHPUnit_Framework_TestCase
{
	/**
	 * test the handler instance
	 */
	public function test_create()
	{
		$manager = Session\Manager::create( 'file' );
	
		$this->assertTrue( $manager instanceof \Session\Manager );
		
		// got a driver?
		$this->assertTrue( $manager->driver() instanceof \Session\Manager_Driver );
		$this->assertTrue( $manager->driver() instanceof \Session\Manager_File );
		
		// test CCSession getter
		$this->assertEquals( $manager, CCSession::manager( 'file' ) );
		
		// get another session
		$this->assertTrue( CCSession::manager() instanceof \Session\Manager );
		$this->assertTrue( CCSession::manager()->driver() instanceof \Session\Manager_Array );
	}
}