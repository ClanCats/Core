<?php
/**
 * Database tests
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2015 ClanCats GmbH
 *
 * @group Database
 * @group Database_Query_Update
 */
class Test_Database_Query_Update extends PHPUnit_Framework_TestCase
{		
	/**
	 * Test DB::insert
	 */
	public function test_update()
	{
		$query = DB::update( 'people', array(
			'foo' => 'bar'
		));
		
		$expected = "update `people` set `foo` = ?";
		
		$this->assertEquals( $expected, $query->build() );
		
		// values function
		$query = DB::update( 'people' )
			->set( array( 'foo' => 'bar' ) );
		
		$expected = "update `people` set `foo` = ?";
		
		$this->assertEquals( $expected, $query->build() );
		
		// more
		$query = DB::update( 'people', array(
			'foo' => 'bar',
			'test' => '1'
		));
		
		$expected = "update `people` set `foo` = ?, `test` = ?";
		
		$this->assertEquals( $expected, $query->build() );
		
		// update where
		$query = DB::update( 'people as p', array(
			'foo' => 'bar',
			'test' => '1'
		))->where( 'p.name', 'in', array( 'a', 'b', 'c' ) )->limit(1);
		
		$expected = "update `people` as `p` set `foo` = ?, `test` = ? where `p`.`name` in (?, ?, ?) limit 1";
		
		$this->assertEquals( $expected, $query->build() );
	}
}