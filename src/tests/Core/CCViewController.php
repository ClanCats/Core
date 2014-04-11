<?php
/**
 * CCF ViewController Test suite
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 */
class CCViewController_Test extends \PHPUnit_Framework_TestCase
{
	/**
	 * test controller execution
	 */
	public function testExecute() 
	{
		// create an instance
		$controller = CCViewController::create( 'CCUnit::TestView' );
		
		// check the reuslt
		$this->assertEquals( "PHPUnitLay:Index", $controller->execute()->body );
		
		// check the reuslt
		$this->assertEquals( "PHPUnitLay:Return", $controller->execute( 'return' )->body );
		
		// check the reuslt
		$this->assertEquals( "Response", $controller->execute( 'response' )->body );
		
		// check the reuslt
		$this->assertEquals( "PHPUnitLay:Bar", $controller->execute( 'view' )->body );
		
		// check the reuslt
		$this->assertEquals( "Bar", $controller->execute( 'modal' )->body );
	}
}