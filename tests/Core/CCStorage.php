<?php
/**
 * CCF Storage Test suite
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 * @group Core
 * @group CCStorage
 */
class Test_CCStorage extends \PHPUnit_Framework_TestCase
{
	/**
	 * CCStorage::path tests
	 */ 
	public function test_path()
	{
		$this->assertInternalType( 'string', CCStorage::path( 'test' ) );
		
		$this->assertTrue( strpos( CCStorage::path( 'to/some/path.txt' ), 'to/some/path.txt' ) !== false );
		
		// add replacements
		$this->assertTrue( strpos( CCStorage::path( 'to/some/:time/path.txt' ), 'to/some/'.time().'/path.txt' ) !== false );
	}
	
	/**
	 * CCStorage::path tests
	 *
	 * @expectedException CCException
	 */ 
	public function test_path_wrong()
	{
		CCStorage::path( 'somefile.pdf', 'wrong' );
	}
	
	/**
	 * CCStorage::url tests
	 *
	 * @expectedException CCException
	 */ 
	public function test_url_wrong()
	{
		CCStorage::url( 'somefile.pdf', 'wrong' );
	}
	
	/**
	 * CCStorage::param tests
	 */ 
	public function test_param()
	{
		CCStorage::param( 'foo', 'phpunit' );
		$this->assertTrue( strpos( CCStorage::path( 'to/:foo/path.txt' ), 'to/phpunit/path.txt' ) !== false );
	}
	
	/**
	 * CCStorage::add tests
	 */ 
	public function test_add()
	{
		CCStorage::add( 'cdn', CCROOT.'cdn/', 'http://cdn01.clancats.com/' );
		
		$this->assertTrue( strpos( CCStorage::path( 'path.txt', 'cdn' ), 'cdn/path.txt' ) !== false );
		
		$this->assertEquals( CCStorage::url( 'path.txt', 'cdn' ), 'http://cdn01.clancats.com/path.txt' );
	}
}