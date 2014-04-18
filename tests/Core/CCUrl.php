<?php
/**
 * CCF URL tests
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 * @group Core
 * @group CCUrl
 */
class Test_CCUrl extends \PHPUnit_Framework_TestCase
{
	/**
	 * CCUrl::to
	 */
	public function test_to() 
	{
		// simple
		$this->assertEquals( '/user/mario/', CCUrl::to( 'user/mario/' ) );
		
		// with parameters
		$this->assertEquals( '/user/ladina/?raw=true', CCUrl::to( 'user/ladina/', array( 'raw' => 'true' ) ) );
		
		// overwrite parameters
		$this->assertEquals( 
			'/user/ladina/?foo=bar&raw=true', 
			CCUrl::to( 'user/ladina/?foo=bar&raw=false', array( 'raw' => 'true' ) ) 
		);
		
		// uri parameters
		$this->assertEquals( 
			'/user/mario/', 
			CCUrl::to( 'user/:user/', array( 'user' => 'mario' ) ) 
		);
		
		// more complex uri parameters
		$this->assertEquals( 
			'/user/mario/detail/mario/modal', 
			CCUrl::to( 'user/:user/detail/:user/:action', array( 'user' => 'mario', 'action' => 'modal' ) ) 
		);
		
		// uri parameters and normal ones
		$this->assertEquals( 
			'/user/mario/?action=detail', 
			CCUrl::to( 'user/:user/', array( 'user' => 'mario', 'action' => 'detail' ) ) 
		);
		
		// absolute uri
		$this->assertEquals( 
			'/main-page/', 
			CCUrl::to( '/main-page/' ) 
		);
		
		// absolute url
		$this->assertEquals( 
			'http://clancats.com/test/?a=b', 
			CCUrl::to( 'http://clancats.com/test/?a=1', array( 'a' => 'b' ) ) 
		);
		
		/*CCUrl::to( 'user/ladina/', array( 'raw' => 'true' ) );
		
		CCUrl::to( 'user/:user/', array( 'user' => 'mario', 'action' => 'detail' ) );
		
		CCUrl::alias( 'user.detail', array( 'user' => 'mario', 'foo' => 'bar' ) );
		
		CCUrl::to( '@user.detail', array( 'user' => 'mario', 'foo' => 'bar' ) );
		
		CCUrl::full( '@user.detail', array( 'user' => 'mario', 'foo' => 'bar' ) );
		
		CCUrl::action( 'detail', array( 'some/param' ) );
		*/
	}
	
	/**
	 * CCUrl::alias
	 */
	public function test_alias() 
	{
		CCRouter::on( 'url/test/alias', array( 'alias' => 'url.test' ), function() {});
		
		// simple
		$this->assertEquals( '/url/test/alias', CCUrl::alias( 'url.test' ) );
		
		// to shortcut
		$this->assertEquals( '/url/test/alias', CCUrl::to( '@url.test' ) );
		
		// shrot shortcut
		$this->assertEquals( '/url/test/alias', to( '@url.test' ) );
		
		// parameters
		$this->assertEquals( '/url/test/alias?foo=bar', to( '@url.test', array( 'foo' => 'bar' ) ) );
		
		// route parameters
		CCRouter::on( 'url/alias/[any]/[any]/', array( 'alias' => 'url.param' ), function() {});
		
		$this->assertEquals( '/url/alias/foo/bar/', to( '@url.param', array( 'foo', 'bar' ) ) );
		
		// route parameters and normal ones
		$this->assertEquals( '/url/alias/foo/rofl/?foo=bar', to( '@url.param', array( 'foo', 'rofl', 'foo' => 'bar' ) ) );
	}
	
	/**
	 * CCUrl::full
	 */
	public function test_full() 
	{
		// fake some data
		CCIn::instance( new CCIn_Instance( array(), array(), array(), array(), array( 'HTTP_HOST' => 'clancats.com' ) ) );
		
		$this->assertEquals( 'http://clancats.com/test/', CCUrl::full( 'test/' ) );
		
		CCIn::instance( new CCIn_Instance( array(), array(), array(), array(), array( 'HTTP_HOST' => 'clancats.com', 'HTTPS' => 'yes' ) ) );
		
		$this->assertEquals( 'https://clancats.com/test/', CCUrl::full( 'test/' ) );
		
		$this->assertEquals( 'https://clancats.com/url/test/alias', CCUrl::full( '@url.test' ) );
	}
	
	/**
	 * CCUrl::to with retain
	 */
	public function test_to_retain() 
	{
		// fake some data
		CCIn::instance( new CCIn_Instance( array( 'foo' => 'bar', 'view' => 'detail' ), array(), array(), array(), array( 'HTTP_HOST' => 'clancats.com' ) ) );
		
		$this->assertEquals( 'http://clancats.com/test/?foo=batz&view=detail', CCUrl::full( 'test/', array( 'foo' => 'batz' ), true ) );
		
		$this->assertEquals( '/foo/?foo=bar&view=detail', to( 'foo/', array(), true ) );
		
		$this->assertEquals( '/url/test/alias?foo=bar&view=detail', CCUrl::alias( 'url.test', array(), true ) );
	}
	
	/**
	 * CCUrl::secure
	 */
	public function test_secure() 
	{
		// fake some data
		CCIn::instance( new CCIn_Instance( array(), array(), array(), array(), array( 'HTTP_HOST' => 'clancats.com' ) ) );
		
		$this->assertEquals( 'https://clancats.com/test/?foo=batz', CCUrl::secure( 'test/', array( 'foo' => 'batz' ) ) );
		
		$this->assertEquals( 'https://clancats.com/', CCUrl::secure( '/' ) );
	}
	
	/**
	 * CCUrl::to
	 */
	public function test_to_with_offset() 
	{
		// fake the url path
		ClanCats::$config->set( 'url.path', '/forums/' ); CCUrl::_init();
		
		// simple
		$this->assertEquals( '/forums/user/mario/', CCUrl::to( 'user/mario/' ) );
		
		// with parameters
		$this->assertEquals( '/forums/user/ladina/?raw=true', CCUrl::to( 'user/ladina/', array( 'raw' => 'true' ) ) );
		
		// overwrite parameters
		$this->assertEquals( 
			'/forums/user/ladina/?foo=bar&raw=true', 
			CCUrl::to( 'user/ladina/?foo=bar&raw=false', array( 'raw' => 'true' ) ) 
		);
		
		// uri parameters
		$this->assertEquals( 
			'/forums/user/mario/', 
			CCUrl::to( 'user/:user/', array( 'user' => 'mario' ) ) 
		);
		
		// more complex uri parameters
		$this->assertEquals( 
			'/forums/user/mario/detail/mario/modal', 
			CCUrl::to( 'user/:user/detail/:user/:action', array( 'user' => 'mario', 'action' => 'modal' ) ) 
		);
		
		// uri parameters and normal ones
		$this->assertEquals( 
			'/forums/user/mario/?action=detail', 
			CCUrl::to( 'user/:user/', array( 'user' => 'mario', 'action' => 'detail' ) ) 
		);
		
		// absolute uri
		$this->assertEquals( 
			'/main-page/', 
			CCUrl::to( '/main-page/' ) 
		);
		
		// absolute url
		$this->assertEquals( 
			'http://clancats.com/test/?a=b', 
			CCUrl::to( 'http://clancats.com/test/?a=1', array( 'a' => 'b' ) ) 
		);
		
		// reset the url path
		ClanCats::$config->set( 'url.path', '/' ); CCUrl::_init();
	}
}