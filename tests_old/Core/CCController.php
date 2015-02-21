<?php
/**
 * CCF Controller Test suite
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2015 ClanCats GmbH
 *
 */
class CCController_Test extends \PHPUnit_Framework_TestCase
{
	/**
	 * test controller finder
	 */
	public function testFind() 
	{
		// simple
		$this->assertEquals( 'UserController', CCController::find( 'User' ) );
		
		// folder underscored
		$this->assertEquals( 'User_SettingsController', CCController::find( 'User/Settings' ) );
		
		// folder underscored and namespaced
		$this->assertEquals( 'CCUnit\\User_SettingsController', CCController::find( 'CCUnit::User/Settings' ) );
	}
	
	/**
	 * test controller creator
	 */
	public function testCreate() 
	{
		// create an instance
		$controller = CCController::create( 'CCUnit::Test' );
		
		// did it work?
		$this->assertTrue( $controller instanceof CCController );
	}
	
	/**
	 * test controller execution
	 */
	public function testExecute() 
	{
		// create an instance
		$controller = CCController::create( 'CCUnit::Test' );
		
		// did it work?
		$this->assertTrue( $controller->execute() instanceof CCResponse );
			
		// check the reuslt
		$this->assertEquals( "Hello World", $controller->execute()->body );
			
		// check the reuslt
		$this->assertEquals( "Test Action", $controller->execute( 'detail' )->body );
		
		// check the reuslt
		$this->assertEquals( "Test Param", $controller->execute( 'param', array( 'Param' ) )->body );
		
		// check the reuslt
		$this->assertEquals( "Test Echo", $controller->execute( 'print' )->body );
		
		// check the reuslt
		$this->assertEquals( "Test Return", $controller->execute( 'return' )->body );
		
		// turn on wake event
		$controller->wake_has_response = true;
		
		// now wake should respond
		$this->assertEquals( "Hello Wake", $controller->execute()->body );
		
		// another one
		$this->assertEquals( "Hello Wake", $controller->execute( 'param', array( 'Param' ) )->body );
	}
}