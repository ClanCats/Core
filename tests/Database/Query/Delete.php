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
 * @group Database_Query_Delete
 */
class Test_Database_Query_Delete extends PHPUnit_Framework_TestCase
{		
	/**
	 * Test DB::delete
	 */
	public function test_delete()
	{
		$query = DB::delete( 'people' );
		
		$expected = "delete from `people`";
		
		$this->assertEquals( $expected, $query->build() );
		
		// with where
		$query = DB::delete( 'people' )->where( 'id', 12 );
		
		$expected = "delete from `people` where `id` = ?";
		
		$this->assertEquals( $expected, $query->build() );
		
		// and a limit
		$query = DB::delete( 'people' )->limit( 5 );
		
		$expected = "delete from `people` limit 5";
		
		$this->assertEquals( $expected, $query->build() );
	}
}