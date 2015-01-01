<?php
/**
 * CCF language Test suite
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2015 ClanCats GmbH
 *
 * @group Core
 * @group CCLang
 */
class CCLang_Test extends \PHPUnit_Framework_TestCase
{
	/**
	 * prepare the configuration
	 *
	 * @return void
	 */
	public static function setUpBeforeClass() 
	{
		CCConfig::create( 'main' )->set( 'language.available', array(
			'de' => array(
				   'de',
				   'ch',
			),
			'en' => array(
					'us', 'gb',
			),
			'fr' => array(
					'fr',
			),
		));
	}

	/**
	 * CCLang::parse tests
	 */
	public function test_parse() 
	{  
		$this->assertEquals( 'de-de', CCLang::parse( 'de' ) );
		$this->assertEquals( 'de-de', CCLang::parse( 'DE' ) );
		$this->assertEquals( 'de-de', CCLang::parse( 'de-DE' ) );
		$this->assertEquals( 'de-de', CCLang::parse( 'DE,en' ) );
		$this->assertEquals( 'de-ch', CCLang::parse( 'de-CH' ) );
		$this->assertEquals( 'de-ch', CCLang::parse( 'DE-ch' ) );
		
		$this->assertEquals( 'en-us', CCLang::parse( 'en' ) );
		$this->assertEquals( 'en-gb', CCLang::parse( 'en-GB' ) );
		
		$this->assertEquals( 'fr-fr', CCLang::parse( 'fr' ) );
		$this->assertEquals( 'fr-fr', CCLang::parse( 'FR-DE' ) );
		
		// default
		$this->assertEquals( 'en-us', CCLang::parse( 'BlaBla' ) );
	}

	/**
	* CCLang::set_current tests
	*/
	public function test_set_current() 
	{
		CCLang::set_current( 'fr' );
		
		$this->assertEquals( 'fr-fr', CCLang::current() );
		$this->assertEquals( 'fr', CCLang::current( true ) );
		
		CCLang::set_current( 'en-US' );
		
		$this->assertEquals( 'en-us', CCLang::current() );
		$this->assertEquals( 'en', CCLang::current( true ) );
	}
	
	/**
	 * CCLang::raw tests
	 */
	public function test_raw() 
	{
		$this->assertInternalType( 'array', CCLang::raw() );
	}
	
	/**
	 * CCLang::load tests
	 */
	public function test_load() 
	{
		CCLang::load( 'CCUnit::phpunit' );
		$this->assertTrue( in_array( 'CCUnit::phpunit', array_keys( CCLang::raw() ) ) );
		
		// load again this time shuold be loaded from cache
		CCLang::load( 'CCUnit::phpunit' );
		$this->assertTrue( in_array( 'CCUnit::phpunit', array_keys( CCLang::raw() ) ) );
		
		// overwrite this time
		CCLang::load( 'CCUnit::phpunit', true );
		$this->assertTrue( in_array( 'CCUnit::phpunit', array_keys( CCLang::raw() ) ) );
		
		// only exists in en
		CCLang::set_current( 'de' );
		
		CCLang::load( 'CCUnit::onlyen', true );
		$this->assertTrue( in_array( 'CCUnit::onlyen', array_keys( CCLang::raw() ) ) );
	}
	
	/**
	 * CCLang::wrong tests
	 *
	 * @expectedException        CCException
	 */
	public function test_load_wrong() 
	{
		CCLang::load( 'CCUnit::wrong' );
	}
	
	/**
	 * CCLang::line tests
	 */
	public function test_line() 
	{
		CCLang::set_current( 'en' );
		
		$this->assertEquals( 'Welcome', __( 'CCUnit::phpunit.welcome' ) );
		$this->assertEquals( 'Hello John', __( 'CCUnit::phpunit.hello', array( 'name' => 'John' ) ) );
		
		CCLang::set_current( 'de' );
		
		$this->assertEquals( 'Willkommen', __( 'CCUnit::phpunit.welcome' ) );
		$this->assertEquals( 'hello', __( 'CCUnit::phpunit.hello', array( 'name' => 'John' ) ) );
	}
	
	/**
	 * CCLang::alias tests
	 */
	public function test_alias() 
	{
		CCLang::set_current( 'en' );
		
		CCLang::alias( ':test', 'CCUnit::phpunit' );
		
		$this->assertEquals( 'Welcome', __( ':test.welcome' ) );
		$this->assertEquals( 'Hello John', __( ':test.hello', array( 'name' => 'John' ) ) );
	}
}