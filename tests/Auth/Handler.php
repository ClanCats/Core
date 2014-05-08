<?php
/**
 * CCF Arr Test suite
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 * @group Auth
 * @group Auth_Handler
 */
class Test_Auth_Handler extends \PHPUnit_Framework_TestCase
{
	/**
	 * prepare the configuration
	 *
	 * @return void
	 */
	public static function setUpBeforeClass() 
	{
		CCConfig::create( 'auth' )->_data = CCConfig::create( 'Core::auth' )->_data;
	}
	
	/**
	 * Handler::create tests
	 */
	public function test_create()
	{
		$auth = Auth\Handler::create();
		
		$this->assertTrue( $auth->user instanceof CCModel );
		$this->assertFalse( $auth->valid() );
		
		$auth2 = Auth\Handler::create( 'other', array(
			''
		));
		
		$this->assertEqals( $auth, Auth\Handler::create() );
		$this->assertEqals( $auth2, Auth\Handler::create( 'other' ) );
		$this->assertEqals( $auth, $auth2 );
		//_dd( $auth );
	}
}