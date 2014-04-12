<?php
/**
 * CCF View Test suite
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 */
class CCView_Test extends \PHPUnit_Framework_TestCase
{
	/**
	 * Test View share
	 */
	public function testShare()
	{
		// add a global
		CCView::share( 'foo', 'bar' );
		
		// mutli dimensional
		CCView::share( 'array.item', 'test' );
		
		// do we really have one?
		$this->assertEquals( 'bar', CCView::$_globals['foo'] );
		$this->assertEquals( 'test', CCView::$_globals['array']['item'] );
	}
	
	/**
	 * Test View create
	 */
	public function testCreate()
	{
		// create the view
		$view = CCView::create( 'CCUnit::test' );
		
		// do we really have one?
		$this->assertTrue( $view instanceof CCView );
		
		// create the view
		$view = CCView::create( 'CCUnit::test', array( 'foo' => 'bar', 'escapeme' => '<div>' ), true );
		
		// check data
		$this->assertEquals( 'bar', $view->foo );
		$this->assertEquals( '&lt;div&gt;', $view->escapeme );
	}
	
	/**
	 * Test View file 
	 */
	public function testFile()
	{
		// create the view
		$view = CCView::create( 'CCUnit::example' );
		
		$this->assertEquals( 'CCUnit::example', $view->file() );
		
		$view->file( 'CCUnit::test' );
		
		$this->assertEquals( 'CCUnit::test', $view->file() );
	}
	
	/**
	 * Test View set
	 */
	public function testSet()
	{
		// create the view
		$view = CCView::create( 'CCUnit::test' );
		
		$view->set( 'dont_escape', '<div>' );
		$view->set( 'escapeme', '<div>', true );
			
		$this->assertEquals( '<div>', $view->dont_escape );
		$this->assertEquals( '&lt;div&gt;', $view->escapeme );
	}
	
	/**
	 * Test View capture
	 */
	public function testCapture()
	{
		// create the view
		$view = CCView::create( 'CCUnit::test' );
		
		$view->capture( 'test', function() {
			echo "Lambada";
		});
			
		$this->assertEquals( 'Lambada', $view->test );
	}
	
	/**
	 * Test View capture
	 */
	public function testCaptureAppend()
	{
		// create the view
		$view = CCView::create( 'CCUnit::test' );
		
		$view->capture_append( 'test', function() {
			echo "Lambada";
		});
		
		$view->capture_append( 'test', function() {
			echo "2";
		});
			
		$this->assertEquals( 'Lambada2', $view->test );
	}
	
	/**
	 * Test View capture
	 */
	public function testRender()
	{
		// create the view
		$view = CCView::create( 'CCUnit::test', array( 'foo' => 'bar' ) );
		
		// render
		$this->assertEquals( 'bar', $view->render() );
		
		// try the magic
		$this->assertEquals( 'bar', (string)$view );
	}
}