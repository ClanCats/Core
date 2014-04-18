<?php
/**
 * CCF Input Test suite
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 * @group Core
 * @group CCIn
 */
class CCIn_Test extends \PHPUnit_Framework_TestCase
{
	
	/**
	 * Test input GET
	 */
	public function testGet() 
	{	
		// generate server data
		$this->fakeServerData();
		
		// check get params
		$this->assertEquals( CCIn::get( 'foo' ), 32 );
		
		// check get param default
		$this->assertEquals( CCIn::get( 'not_existing', 'test' ), 'test' );
		
		// check get param has
		$this->assertFalse( CCIn::has_get( 'not_existing' ) );
		
		// check get param has
		$this->assertTrue( CCIn::has_get( 'foo' ) );
	}
	
	/**
	 * Test input POST
	 */
	public function testPost() 
	{	
		// generate server data
		$this->fakeServerData();
		
		// check post params
		$this->assertEquals( CCIn::post( 'hello' ), 'world' );
		
		// check post param default
		$this->assertEquals( CCIn::post( 'not_existing', 'test' ), 'test' );
		
		// check post param has
		$this->assertFalse( CCIn::has_post( 'not_existing' ) );
		
		// check post param has
		$this->assertTrue( CCIn::has_post( 'hello' ) );
	}
	
	/**
	 * Test input SERVER
	 */
	public function testServer() 
	{	
		// generate server data
		$this->fakeServerData();
		
		// check server params
		$this->assertEquals( CCIn::server( 'server_port' ), 80 );
		
		// check server param default
		$this->assertEquals( CCIn::server( 'not_existing', 'test' ), 'test' );
		
		// check post param has
		$this->assertFalse( CCIn::has_server( 'not_existing' ) );
		
		// check post param has
		$this->assertTrue( CCIn::has_server( 'server_port' ) );
	}

	
	/**
	 * test client ip
	 */
	public function testClientIp() 
	{	
		// generate server data
		$this->fakeServerData();
		
		// check if we have an ip address
		$this->assertEquals( CCIn::client()->ip, '123.121.123.121' );
		
		// cloudflare test
		$this->fakeServerData( array(), array(), array( 'HTTP_CF_CONNECTING_IP' => '12.13.14.156' ) );
		$this->assertEquals( CCIn::client()->ip, '12.13.14.156' );
		
		// proxy test
		$this->fakeServerData( array(), array(), array( 'HTTP_X_FORWARDED_FOR' => '12.13.14.157' ) );
		$this->assertEquals( CCIn::client()->ip, '12.13.14.157' );
		
		// proxy2 test
		$this->fakeServerData( array(), array(), array( 'HTTP_CLIENT_IP' => '12.13.14.158' ) );
		$this->assertEquals( CCIn::client()->ip, '12.13.14.158' );
	}
	
	/**
	 * test client user agent
	 */
	public function testClientAgent() 
	{	
		// generate server data
		$this->fakeServerData();
		
		// check if we have an ip address
		$this->assertEquals( CCIn::client()->agent, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9) AppleWebKit/537.71 (KHTML, like Gecko) Version/7.0 Safari/537.71' );
	}
	
	/**
	 * test client fingerprint
	 */
	public function testClientFingerprint() 
	{	
		// generate server data
		$this->fakeServerData();
		
		// check if we have an ip address
		$fingerprint = CCIn::client()->fingerprint;
		
		// generate server data
		$this->fakeServerData();
		
		// check if we have an ip address
		$this->assertEquals( $fingerprint, CCIn::client()->fingerprint );
		
		// generate server data
		$this->fakeServerData( array(), array(), array( 'HTTP_CF_CONNECTING_IP' => '123.121.123.122' ) );
		
		// check if we have an ip address
		$this->assertTrue( $fingerprint != CCIn::client()->fingerprint );
	}
	
	/**
	 * test request method
	 */
	public function testMethod() 
	{	
		// generate server data
		$this->fakeServerData();
		
		$this->assertEquals( CCServer::method(), 'POST' );
		
		$this->fakeServerData( array(), array(), array( 'HTTP_X_HTTP_METHOD_OVERRIDE' => 'put' ) );
		
		$this->assertEquals( CCServer::method(), 'PUT' );
		
		$this->fakeServerData( array(), array(), array( 'REQUEST_METHOD' => 'Get' ) );
		
		$this->assertEquals( CCServer::method(), 'GET' );
	}
	
	/**
	 * test request method
	 */
	public function testProtocol() 
	{	
		// generate server data
		$this->fakeServerData();
		
		$this->assertEquals( CCServer::protocol(), 'http' );
		
		$this->fakeServerData( array(), array(), array( 'SERVER_PORT' => '443' ) );
		
		$this->assertEquals( CCServer::protocol(), 'https' );
		
		$this->fakeServerData( array(), array(), array( 'HTTPS' => 'yes' ) );
		
		$this->assertEquals( CCServer::protocol(), 'https' );
		
		$this->fakeServerData( array(), array(), array( 'HTTPS' => 'off' ) );
		
		$this->assertEquals( CCServer::protocol(), 'http' );
	}
	
	/**
	 * test request method
	 */
	public function testHost() 
	{	
		// generate server data
		$this->fakeServerData();
		
		$this->assertEquals( CCServer::host(), 'local.ccf2.com' );
		
		$this->fakeServerData( array(), array(), array( 'HTTP_X_FORWARDED_HOST' => 'example.com' ) );
		
		$this->assertEquals( CCServer::host(), 'example.com' );
	}
	
	/**
	 * test software
	 */
	public function testSoftware() 
	{	
		// generate server data
		$this->fakeServerData();
		
		$this->assertInternalType('string', CCIn::software() );
	}
	
	/**
	 * test referrer
	 */
	public function testReferrer() 
	{	
		// generate server data
		$this->fakeServerData();
		
		$this->assertEmpty( CCIn::referrer() );
		
		$this->fakeServerData( array(), array(), array( 'HTTP_REFERER' => 'http://clancats.com' ) );
		
		$this->assertInternalType('string', CCIn::referrer() );
	}
	
	/**
	 * test URI
	 */
	public function testUri() 
	{	
		// generate server data
		$this->fakeServerData();
		
		$this->assertEquals( CCIn::uri(), '' );
		
		$this->fakeServerData( array(), array(), array( 'REQUEST_URI' => '//test/?Sdf' ) );
		
		$this->assertEquals( CCIn::uri(), 'test/' );
		
		$this->fakeServerData( array(), array(), array( 'REQUEST_URI' => '/test?Sdf' ) );
		
		$this->assertEquals( CCIn::uri(), 'test' );
		
		$this->fakeServerData( array(), array(), array( 'REQUEST_URI' => '//foo/bar//?param=1&test=tee' ) );
		
		$this->assertEquals( CCIn::uri(), 'foo/bar/' );
		
		$this->fakeServerData( array(), array(), array( 'REQUEST_URI' => '//foo/bar//file.xml?param=1&test=tee' ) );
		
		$this->assertEquals( CCIn::uri(), 'foo/bar/file.xml' );
		
		$this->fakeServerData( array(), array(), array( 'REQUEST_URI' => '//test?Sdf' ) );
		
		$this->assertEquals( CCIn::uri( true ), 'test?Sdf' );
	}
	
	/**
	 * test URI
	 */
	public function testUrl() 
	{	
		// generate server data
		$this->fakeServerData();
		
		$this->assertEquals( CCIn::url(), 'http://local.ccf2.com/' );
		
		$this->fakeServerData( array(), array(), array( 'REQUEST_URI' => '/foo/bar/?param1=yes&param2=no' ) );
		
		$this->assertEquals( CCIn::url(), 'http://local.ccf2.com/foo/bar/?param1=yes&param2=no' );
		
		$this->fakeServerData( array(), array(), array( 'REQUEST_URI' => '/foo/bar/?a=b', 'HTTPS' => 'yes' ) );
		
		$this->assertEquals( 'https://local.ccf2.com/foo/bar/?a=b', CCIn::url() );
	}
	
	/**
	 * test for ajax request
	 */
	public function testIsAjax() 
	{	
		// generate server data
		$this->fakeServerData();
		
		$this->assertFalse( CCIn::is_ajax() );
		
		$this->fakeServerData( array(), array(), array( 'HTTP_X_REQUESTED_WITH' => null ) );
		
		$this->assertTrue( CCIn::is_ajax() );
	}
	
	/**
	 * test assignment
	 * generates an clean Input instance
	 * you can pass custom params for testing
	 */
	public function fakeServerData( $add_get = array(), $add_post = array(), $add_server = array() ) 
	{
		$add_get = array_merge( array(
			'foo'	=> 32,
			'hello'	=> 'world',
			'id'		=> '453'
		), $add_get );
		
		$add_post = array_merge( array(
			'foo'	=> 32,
			'hello'	=> 'world',
			'id'		=> '453',
			'some'	=> 'Other other String'
		), $add_post );
		
		$add_server = array_merge( array ( 
			'HTTP_HOST' => 'local.ccf2.com', 
			'HTTP_ACCEPT_ENCODING' => 'gzip, deflate', 
			'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8', 
			'HTTP_USER_AGENT' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9) AppleWebKit/537.71 (KHTML, like Gecko) Version/7.0 Safari/537.71', 
			'HTTP_ACCEPT_LANGUAGE' => 'en-us', 
			'HTTP_CACHE_CONTROL' => 'max-age=0', 
			'HTTP_CONNECTION' => 'keep-alive', 
			'SERVER_SOFTWARE' => 'Apache/2.4.4 (Unix) PHP/5.4.16 OpenSSL/1.0.1e mod_perl/2.0.8-dev Perl/v5.16.3', 
			'SERVER_NAME' => 'local.ccf2.com', 
			'SERVER_ADDR' => '127.0.0.1', 
			'SERVER_PORT' => '80', 
			'REMOTE_ADDR' => '123.121.123.121', 
			'REQUEST_SCHEME' => 'http',  
			'REMOTE_PORT' => '51749', 
			'GATEWAY_INTERFACE' => 'CGI/1.1', 
			'SERVER_PROTOCOL' => 'HTTP/1.1', 
			'REQUEST_METHOD' => 'POST', 
			'QUERY_STRING' => '',
			'REQUEST_URI' => '/',
			'SCRIPT_NAME' => '/index.php', 
			'PHP_SELF' => '/index.php', 
			'REQUEST_TIME' => time(), 
			'CLANCATS_ENV' => 'development'
		), $add_server );
		
		CCIn::instance( new CCIn_Instance( $add_get, $add_post, array(), array(), $add_server ) );
	}
}