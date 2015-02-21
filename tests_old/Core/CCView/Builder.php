<?php
/**
 * CCF View tests
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2015 ClanCats GmbH
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
	
	/**
	 * tests Builder loop 
	 */
	public function test_loop()
	{
		$output = CCView::create( 'CCUnit::builder/loop.view' )->render();
	
		$expected_output = "0123456789";
	
		$this->assertEquals( $expected_output, $output );
	}
	
	/**
	 * tests Builder loop 
	 */
	public function test_each()
	{
		$output = CCView::create( 'CCUnit::builder/each.view', array(
			'items' => array( 'foo', 'bar', 'jep' ),
		))->render();
	
		$expected_output = "foo, bar, jep, ";
	
		$this->assertEquals( $expected_output, $output );
	}
	
	/**
	 * tests Builder loop 
	 */
	public function test_array_access()
	{
		$output = CCView::create( 'CCUnit::builder/each_user.view', array(
			'users' => array( 
				array( 'name' => 'jeff' ),
				array( 'name' => 'john' ),
				array( 'name' => 'jack' ),
			),
		))->render();
	
		$expected_output = "<ul>\n\t<li>jeff</li>\n\t<li>john</li>\n\t<li>jack</li>\n</ul>";
	
		$this->assertEquals( $expected_output, $output );
	}
}
