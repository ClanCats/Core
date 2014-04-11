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
 * @group Database_Query_Insert
 */
class Test_Database_Query_Insert extends PHPUnit_Framework_TestCase
{	
	/**
	 * Check if DB test are possible
	 *
	 * @return void
	 */
	/*public static function setUpBeforeClass() 
	{
		// first of all run the parent
		parent::setUpBeforeClass();
		
		DB::insert( 'people', CCArr::first( CCArr::first( static::people_provider() ) ) )->run( 'phpunit' );
	}*/
	
	/**
	 * Test DB::insert
	 */
	public function test_insert()
	{
		$query = DB::insert( 'people', array(
			'foo' => 'bar'
		));
		
		$expected = "insert into `people` (`foo`) values (?)";
		
		$this->assertEquals( $expected, $query->build() );
		
		// values function
		$query = DB::insert( 'people' )
			->values( array( 'foo' => 'bar' ) );
		
		$expected = "insert into `people` (`foo`) values (?)";
		
		$this->assertEquals( $expected, $query->build() );
		
		// more
		$query = DB::insert( 'people', array(
			'foo' => 'bar',
			'test' => '1'
		));
		
		$expected = "insert into `people` (`foo`, `test`) values (?, ?)";
		
		$this->assertEquals( $expected, $query->build() );
		
		// bulk
		$query = DB::insert( 'people', array(
			array( 'foo' => 'bar', 'test' => 'a' ),
			array( 'test' => 'a', 'foo' => 'bar' ),
		));
		
		$expected = "insert into `people` (`foo`, `test`) values (?, ?), (?, ?)";
		
		$this->assertEquals( $expected, $query->build() );
	}
	
	/**
	 * Test DB::insert()->ignore()
	 */
	public function test_ignore()
	{
		$query = DB::insert( 'people', array( 'foo' => 'bar' ) )
			->ignore();
		
		$expected = "insert ignore into `people` (`foo`) values (?)";
		
		$this->assertEquals( $expected, $query->build() );
	}
}