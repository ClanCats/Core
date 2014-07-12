<?php
/**
 * CCF View tests
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 * @group Core
 * @group CCView_Builder
 */
class CCView_Builder_Test extends \PHPUnit_Framework_TestCase
{
	/**
	 * tests Builder echo 
	 */
	public function test_echo()
	{
		$output = CCView::create( 'CCUnit::builder/echo.view', array( 
			'firstname' => 'Zap', 
			'lastname' => 'Hinkins',
		))->render();
	
		$expected_output = "Hello Zap, Hinkins.";
	
		$this->assertEquals( $expected_output, $output );
	}
}
