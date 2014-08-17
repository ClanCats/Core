<?php
/**
 * Database tests
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 * @group Database
 * @group DB
 */
class Test_Database_DB extends \DB\Test_Case_Database
{
	/**
	 * DB::connect test
	 */
	public function test_connect()
	{
		$this->assertInternalType( 'bool', DB::connect() );
		$this->assertInternalType( 'bool', DB::connect( 'phpunit' ) );
	}
	
	/**
	 * DB::handler tests
	 */
	public function test_handler()
	{
		$this->assertTrue( DB::handler() instanceof \DB\Handler );
		$this->assertTrue( DB::handler( 'phpunit' ) instanceof \DB\Handler );
	}
	
	/**
	 * DB::raw tests
	 */
	public function test_raw()
	{
		$this->assertTrue( DB::raw() instanceof \DB\Expression );
		$this->assertTrue( DB::raw( 'damit' ) instanceof \DB\Expression );
	}
	
	/**
	 * DB::is_expression tests
	 */
	public function test_is_expression()
	{
		$this->assertFalse( DB::is_expression( 'nope' ) );
		$this->assertTrue( DB::is_expression( DB::raw( 'jep' ) ) );
	}
	
	/**
	 * DB::query_log tests
	 */
	public function test_query_log()
	{
		$this->assertInternalType( 'array', DB::query_log() );
	}
	
	/**
	 * DB::last_query tests
	 */
	public function test_last_query()
	{
		$this->assertInternalType( 'string', DB::last_query() );
	}
	
	/**
	 * DB::fetch tests
	 *
	 * @dataProvider people_provider_bulk
	 */
	public function test_fetch( $people )
	{	
		DB::run( 'delete from people' );
		DB::insert( 'people', $people )->run();
		
		$this->assertInternalType( 'array', DB::fetch( 'select * from people' ) );
		$this->assertInternalType( 'array', DB::fetch( 'select * from people where age > ?', array( 0 ), 'phpunit' ) );
	}
	
	/**
	 * DB::model tests
	 *
	 * @dataProvider people_provider_bulk
	 */
	public function test_model( $people )
	{	
		DB::run( 'delete from people' );
		DB::insert( 'people', $people )->run();
		
		$query = DB::model( "CCUnit\\Model_DBPerson" );
		
		$this->assertEquals( 'select * from `people`', $query->build() );
		$this->assertInternalType( 'array', $query->run() );
	}
	
	/**
	 * DB::find tests
	 *
	 * @dataProvider people_provider_bulk
	 */
	public function test_find( $people )
	{	
		DB::run( 'delete from people' );
		
		$this->assertFalse( DB::find( 'people', 1 ) );
		
		DB::insert( 'people', $people )->run();
		
		$this->assertTrue( DB::find( 'people', 1 ) !== null );
	}
	
	/**
	 * DB::first tests
	 *
	 * @dataProvider people_provider_bulk
	 */
	public function test_first( $people )
	{	
		DB::run( 'delete from people' );
		
		$this->assertFalse( DB::first( 'people' ) );
		
		DB::insert( 'people', $people )->run();
		
		$this->assertTrue( DB::first( 'people' ) !== null );
	}
	
	/**
	 * DB::last tests
	 *
	 * @dataProvider people_provider_bulk
	 */
	public function test_last( $people )
	{	
		DB::run( 'delete from people' );
		
		$this->assertFalse( DB::last( 'people' ) );
		
		DB::insert( 'people', $people )->run();
		
		$this->assertTrue( DB::last( 'people' ) !== null );
	}
	
	/**
	 * DB::truncate tests
	 *
	 * @dataProvider people_provider_bulk
	 */
	public function test_truncate( $people )
	{	
		DB::run( 'delete from people' );
		DB::insert( 'people', $people )->run();
		
		$this->assertTrue( DB::count( 'people' ) > 0 );
		
		DB::truncate( 'people' );
		
		$this->assertTrue( DB::count( 'people' ) === 0 );
	}
}