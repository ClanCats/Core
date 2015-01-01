<?php
/**
 * CCF Arr Test suite
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2015 ClanCats GmbH
 *
 * @group Core
 * @group CCAsset
 */
class CCAsset_Test extends \PHPUnit_Framework_TestCase
{
	/**
	 * CCAsset::uri tests
	 */
	public function test_uri()
	{
		$this->assertEquals( '/assets/images/foo.jpg', CCAsset::uri( 'images/foo.jpg' ) );
		
		// lets define a holder
		CCAsset::holder( 'phpunit' )->path = "phpunit/images/";
		
		$this->assertEquals( '/phpunit/images/foo.jpg', CCAsset::uri( 'foo.jpg', 'phpunit' ) );
		
		// url
		$this->assertEquals( 'http://foo.bar', CCAsset::uri( 'http://foo.bar' ) );
		$this->assertEquals( 'http://foo.bar', CCAsset::uri( 'http://foo.bar', 'phpunit' ) );
		
		// absolute path
		$this->assertEquals( '/foo/bar', CCAsset::uri( '/foo/bar' ) );
		$this->assertEquals( '/foo/bar', CCAsset::uri( '/foo/bar', 'phpunit' ) );
	}
	
	/**
	 * CCAsset::og tests
	 */
	public function test_open_graph()
	{
		$this->assertEquals( '<meta property="og:type" content="video" />', CCAsset::og( 'type', 'video' ) );
		
		// multiple
		$this->assertEquals( '<meta property="og:type" content="image" /><meta property="og:size" content="512px" />', CCAsset::og( array(
			'type' => 'image',
			'size' => '512px',
		)) );
	}
	
	/**
	 * CCAsset::css tests
	 */
	public function test_css()
	{
		$this->assertEquals( '<link type="text/css" rel="stylesheet" href="/assets/foo.css" />', CCAsset::css( 'foo.css' ) );
	}
	
	/**
	 * CCAsset::js tests
	 */
	public function test_js()
	{
		$this->assertEquals( '<script type="text/javascript" src="/assets/foo.js"></script>', CCAsset::js( 'foo.js' ) );
	}
	
	/**
	 * CCAsset::less tests
	 */
	public function test_less()
	{
		$this->assertEquals( '<link type="text/css" rel="stylesheet/less" href="/assets/foo.less" />', CCAsset::less( 'foo.less' ) );
		
		// lets define a holder
		CCAsset::holder( 'phpunit-less' )->path = "assets/phpunit/less/";
		
		$this->assertEquals( '<link type="text/css" rel="stylesheet/less" href="/assets/phpunit/less/foo.less" />', CCAsset::less( 'foo.less', 'phpunit-less' ) );
	}
	
	/**
	 * CCAsset::img tests
	 */
	public function test_img()
	{
		$this->assertEquals( '<img src="/assets/logo.png" />', CCAsset::img( 'logo.png' ) );
	}
	
	/**
	 * CCAsset invalid macro test
	 *
	 * @expectedException BadMethodCallException
	 */
	public function test_bad_macro()
	{
		CCAsset::this_does_not_exist();
	}
	
	/**
	 * CCAsset::uri tests
	 */
	public function test_macro()
	{
		$this->assertEquals( 'foo', CCAsset::_( 'foo' ) );
		
		// create new macro
		CCAsset::macro( 'phpunit', function( $to ) 
		{
			return 'Hello '.$to;
		});
		
		$this->assertEquals( 'Hello Spock', CCAsset::phpunit( 'Spock' ) );
		
		// file macro
		CCAsset::macro( 'bg', '<div style="background-image: url(:uri);"></div>' );
		
		CCAsset::holder( 'phpunit-images' )->path = "assets/phpunit/images/";
		
		$expected = '<div style="background-image: url(/assets/phpunit/images/background.jpg);"></div>';
		
		$this->assertEquals( $expected, CCAsset::bg( 'background.jpg', 'phpunit-images' ) );
	}
	
	/**
	 * CCAsset::add, get & clear tests
	 */
	public function test_add_get_clear()
	{
		$assets = array(
			'style.css',
			'bootstrap.css',
			'application.js',
			'jquery.js',
			'underscore.js',
		);
		
		foreach( $assets as $asset )
		{
			CCAsset::add( $asset );
			CCAsset::add( $asset, 'second' );
		}
		
		$this->assertEquals( 2, count( CCAsset::get() ) );
		$this->assertEquals( 2, count( CCAsset::get( null, 'second' ) ) );
		$this->assertEquals( 0, count( CCAsset::get( 'foo' ) ) );
		
		$this->assertEquals( 3, count( CCAsset::get( 'js' ) ) );
		$this->assertEquals( 3, count( CCAsset::get( 'js', 'second' ) ) );
		$this->assertEquals( 0, count( CCAsset::get( 'js', 'footer' ) ) );
		
		$this->assertEquals( 2, count( CCAsset::get( 'css' ) ) );
		$this->assertEquals( 2, count( CCAsset::get( 'css', 'second' ) ) );
		$this->assertEquals( 0, count( CCAsset::get( 'css', 'other' ) ) );
		
		CCAsset::clear( 'js' );
		
		$this->assertEquals( 0, count( CCAsset::get( 'js' ) ) );
		$this->assertEquals( 3, count( CCAsset::get( 'js', 'second' ) ) );
		
		CCAsset::clear();
		
		$this->assertEquals( 0, count( CCAsset::get() ) );
		$this->assertEquals( 2, count( CCAsset::get( null, 'second' ) ) );
		
		// test adding plain tag
		CCAsset::add( '<style></style>' );
		
		$this->assertEquals( 1, count( CCAsset::get( '_' ) ) );
	}
	
	/**
	 * CCAsset::code tests
	 */
	public function test_code_gen()
	{	
		CCAsset::macro( 'test', '<:uri>' );
		
		CCAsset::holder( 'root' )->path = '/';
		
		CCAsset::add( 'foo.test', 'root' );
		CCAsset::add( 'bar.test', 'root' );
		
		$this->assertEquals( '</foo.test></bar.test>', CCAsset::code( 'test', 'root' ) );
	}
}