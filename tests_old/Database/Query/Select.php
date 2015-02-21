<?php
/**
 * Database Select tests
 * We Also unit test the DB\Query functions here because the Query 
 * itself cannot be builded.
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2015 ClanCats GmbH
 *
 * @group Database
 * @group Database_Query_Select
 */
class Test_Database_Query_Select extends \DB\Test_Case_Database
{
	/**
	 * Test select
	 */
	public function test_select( $people )
	{
		$query = DB::select( 'people' );
		
		$expected = "select * from `people`";
		
		$this->assertEquals( $expected, $query->build() );
		
		// with alias
		$query = DB::select( 'people as p' );
		
		$expected = "select * from `people` as `p`";
		
		$this->assertEquals( $expected, $query->build() );
		
		// diffrent syntax
		$query = DB::select( array( 'people' => 'p' ) );
		
		$expected = "select * from `people` as `p`";
		
		$this->assertEquals( $expected, $query->build() );
	}
	
	/**
	 * Test fields
	 */
	public function test_fields()
	{
		$query = DB::select( 'people', '*' );
		
		$expected = "select * from `people`";
		
		$this->assertEquals( $expected, $query->build() );
		
		// single field
		$query = DB::select( 'people', 'name' );
		
		$expected = "select `name` from `people`";
		
		$this->assertEquals( $expected, $query->build() );
		
		// DB raw
		$query = DB::select( 'people', DB::raw( 'COUNT(*)' ) );
		
		$expected = "select COUNT(*) from `people`";
		
		$this->assertEquals( $expected, $query->build() );
		
		// multiple fields
		$query = DB::select( 'people', array( 'name', 'id' ) );
		
		$expected = "select `name`, `id` from `people`";
		
		$this->assertEquals( $expected, $query->build() );
		
		// multiple fields with alias
		$query = DB::select( 'people', array( 'name' => 'n', 'id' => 'i' ) );
		
		$expected = "select `name` as `n`, `id` as `i` from `people`";
		
		$this->assertEquals( $expected, $query->build() );
		
		// multiple diffrent syntax and raw
		$query = DB::select( 'people', array( array( DB::raw( 'COUNT(*)' ), 'count' ) ) );
		
		$expected = "select COUNT(*) as `count` from `people`";
		
		$this->assertEquals( $expected, $query->build() );
		
		// reseting fields
		$query->fields( '*' );
		
		$expected = "select * from `people`";
		
		$this->assertEquals( $expected, $query->build() );
		
		// adding fields
		$query->add_fields( 'id' );
		$query->add_fields( 'name as n' );
		
		$expected = "select `id`, `name` as `n` from `people`";
		
		$this->assertEquals( $expected, $query->build() );
	}
	
	/**
	 * Test distinct
	 */
	public function test_distinct()
	{
		// simple where
		$query = DB::select( 'people' )
			->distinct();
		
		$expected = "select distinct * from `people`";
		
		$this->assertEquals( $expected, $query->build() );
		
		// disable it
		$query->distinct( false );
		
		$expected = "select * from `people`";
		
		$this->assertEquals( $expected, $query->build() );
	}
	
	/**
	 * Test order
	 */
	public function test_order()
	{
		// simple order
		$query = DB::select( 'people' )
			->order_by( 'name' );
		
		$expected = "select * from `people` order by `name` asc";
		
		$this->assertEquals( $expected, $query->build() );
		
		// desc order
		$query = DB::select( 'people' )
			->order_by( 'name', 'desc' );
		
		$expected = "select * from `people` order by `name` desc";
		
		$this->assertEquals( $expected, $query->build() );
		
		// multiple
		$query = DB::select( 'people' )
			->order_by( 'name', 'desc' )
			->order_by( 'age' );
		
		$expected = "select * from `people` order by `name` desc, `age` asc";
		
		$this->assertEquals( $expected, $query->build() );
		
		// multiple in array
		$query = DB::select( 'people' )
			->order_by( array( 'name', 'age' => 'desc' ) );
		
		$expected = "select * from `people` order by `name` asc, `age` desc";
		
		$this->assertEquals( $expected, $query->build() );
	}
	
	/**
	 * Test groups
	 */
	public function test_group()
	{
		// simple group
		$query = DB::select( 'people' )
			->group_by( 'name' );
		
		$expected = "select * from `people` group by `name`";
		
		$this->assertEquals( $expected, $query->build() );
		
		// multiple
		$query = DB::select( 'people' )
			->group_by( 'p.name' )
			->group_by( 'age' );
		
		$expected = "select * from `people` group by `p`.`name`, `age`";
		
		$this->assertEquals( $expected, $query->build() );
		
		// multiple as array
		$query = DB::select( 'people' )
			->group_by( array( 'p.name', 'a.age' ) );
		
		$expected = "select * from `people` group by `p`.`name`, `a`.`age`";
		
		$this->assertEquals( $expected, $query->build() );
	}
	
	/**
	 * Test where
	 */
	public function test_where()
	{
		// simple where
		$query = DB::select( 'people' )
			->where( 'foo', 'bar' );
		
		$expected = "select * from `people` where `foo` = ?";
		
		$this->assertEquals( $expected, $query->build() );
		
		// where and
		$query = DB::select( 'people' )
			->where( 'foo', 'bar' )
			->where( 'a', 'b' );
		
		$expected = "select * from `people` where `foo` = ? and `a` = ?";
		
		$this->assertEquals( $expected, $query->build() );
		
		// where and with and method
		$query = DB::select( 'people' )
			->where( 'foo', 'bar' )
			->and_where( 'a', 'b' );
		
		$expected = "select * from `people` where `foo` = ? and `a` = ?";
		
		$this->assertEquals( $expected, $query->build() );
		
		// or where
		$query = DB::select( 'people' )
			->where( 'foo', 'bar' )
			->or_where( 'a', 'b' );
		
		$expected = "select * from `people` where `foo` = ? or `a` = ?";
		
		$this->assertEquals( $expected, $query->build() );
		
		// table selector
		$query = DB::select( 'people' )
			->where( 'p.foo', 'bar' )
			->or_where( 'b.a', 'b' );
		
		$expected = "select * from `people` where `p`.`foo` = ? or `b`.`a` = ?";
		
		$this->assertEquals( $expected, $query->build() );
		
		// where custom operator
		$query = DB::select( 'people' )
			->where( 'age', '>', 20 );
		
		$expected = "select * from `people` where `age` > ?";
		
		$this->assertEquals( $expected, $query->build() );
		
		// where in
		$query = DB::select( 'people' )
			->where( 'name', 'in', array( 'foo', 'bar' ) );
		
		$expected = "select * from `people` where `name` in (?, ?)";
		
		$this->assertEquals( $expected, $query->build() );
		
		// where nested
		$query = DB::select( 'people' )
			->where( function( $q )
			{
				$q->where( 'age', '>', 18 );
				$q->where( 'age', '<', 30 );
			});
		
		$expected = "select * from `people` where ( `age` > ? and `age` < ? )";
		
		// more where nested
		$query = DB::select( 'people' )
			->where( function( $q )
			{
				$q->where( 'age', '>', 18 );
				$q->where( function( $q ) {
					$q->where( 'name', 'A' );
					$q->or_where( 'name', 'B' );
				});
			});
		
		$expected = "select * from `people` where ( `age` > ? and ( `name` = ? or `name` = ? ) )";
		
		$this->assertEquals( $expected, $query->build() );
	}
	
	/**
	 * Test limit
	 */
	public function test_limit()
	{
		// simple limit
		$query = DB::select( 'people' )
			->limit( 1 );
		
		$expected = "select * from `people` limit 0, 1";
		
		$this->assertEquals( $expected, $query->build() );
		
		// remove the limit again
		$query->limit( null );
		
		$expected = "select * from `people`";
		
		$this->assertEquals( $expected, $query->build() );
		
		// limit with offset
		$query = DB::select( 'people' )
			->limit( 10, 5 );
		
		$expected = "select * from `people` limit 10, 5";
		
		$this->assertEquals( $expected, $query->build() );
		
		// string
		$query = DB::select( 'people' )
			->limit( 'nope' );
		
		$expected = "select * from `people` limit 0, 0";
		
		$this->assertEquals( $expected, $query->build() );
	}
	
	/**
	 * Test page limit
	 */
	public function test_page()
	{
		// page one
		$query = DB::select( 'people' )
			->page( 1, 10 );
		
		$expected = "select * from `people` limit 0, 10";
		
		$this->assertEquals( $expected, $query->build() );
		
		// page two 
		$query = DB::select( 'people' )
			->page( 2, 10 );
		
		$expected = "select * from `people` limit 10, 10";
		
		$this->assertEquals( $expected, $query->build() );
		
		// another paging
		$query = DB::select( 'people' )
			->page( 23, 5 );
		
		$expected = "select * from `people` limit 110, 5";
		
		$this->assertEquals( $expected, $query->build() );
		
		// numeric string 
		$query = DB::select( 'people' )
			->page( "5", 10 );
		
		$expected = "select * from `people` limit 40, 10";
		
		$this->assertEquals( $expected, $query->build() );
		
		// string 
		$query = DB::select( 'people' )
			->page( "foo", 10 );
		
		$expected = "select * from `people` limit 0, 10";
		
		$this->assertEquals( $expected, $query->build() );
	}
	
	/**
	 * Test Query_Select::run
	 *
	 * @dataProvider people_provider
	 */
	public function test_run( $person )
	{
		$response = DB::insert( 'people', $person )->run();
		$this->assertTrue( is_numeric( $response ) && $response > 0 );
		
		$result = DB::select( 'people', array_keys( $person ) )
			->where( 'id', $response )
			->limit( 1 )
			->run();
	
		foreach( $person as $key => $value )
		{
			$this->assertEquals( $value, $result->$key );
		}
	}
	
	/**
	 * Test Query_Select::run
	 *
	 * @dataProvider people_provider
	 */
	public function test_run_handler( $person )
	{
		$response = DB::insert( 'people', $person )->run();
		$this->assertTrue( is_numeric( $response ) && $response > 0 );
		
		$result = DB::select( 'people', array_keys( $person ) )
			->where( 'id', $response )
			->limit( 1 )
			->run( 'phpunit' );
	
		foreach( $person as $key => $value )
		{
			$this->assertEquals( $value, $result->$key );
		}
	}
	
	/**
	 * Test Query_Select::run other handler
	 *
	 * @dataProvider people_provider
	 */
	public function test_run_no_limit( $person )
	{
		$response = DB::insert( 'people', $person )->run();
		$this->assertTrue( is_numeric( $response ) && $response > 0 );
		
		$result = DB::select( 'people', array_keys( $person ) )
			->where( 'id', $response )
			->run();
	
		foreach( $person as $key => $value )
		{
			$this->assertEquals( $value, reset( $result )->$key );
		}
	}
	
	/**
	 * Test Query_Select::one
	 */
	public function test_one( $person )
	{
		$result = DB::select( 'people' )->one();
		$this->assertTrue( !is_null( $result ) );
		$this->assertTrue( !empty( $result ) );
	}
	
	/**
	 * Test Query_Select::run other handler
	 *
	 * @dataProvider people_provider
	 */
	public function test_find( $person )
	{
		$response = DB::insert( 'people', $person )->run();
		$this->assertTrue( is_numeric( $response ) && $response > 0 );
		
		$result = DB::select( 'people', array_keys( $person ) )->find( $response );
	
		foreach( $person as $key => $value )
		{
			$this->assertEquals( $value, $result->$key );
		}
		
		// another key
		$result = DB::select( 'people', array_keys( $person ) )->find( $person['name'], 'name' );
		
		foreach( $person as $key => $value )
		{
			$this->assertEquals( $value, $result->$key );
		}
	}
	
	/**
	 * Test Query_Select::first
	 */
	public function test_first()
	{
		$result = DB::select( 'people' )->first();
		
		$person = static::people_provider();
		$person = $person[0][0];
		
		foreach( $person as $key => $value )
		{
			$this->assertEquals( $value, $result->$key );
		}
	}
	
	/**
	 * Test Query_Select::last
	 */
	public function test_last()
	{
		$result = DB::select( 'people' )->last();
		
		$person = CCArr::last( static::people_provider() );
		$person = $person[0];
		
		foreach( $person as $key => $value )
		{
			$this->assertEquals( $value, $result->$key );
		}
	}
	
	/**
	 * Test Query_Select::column
	 *
	 * @dataProvider people_provider
	 */
	public function test_column( $person )
	{
		$response = DB::insert( 'people', $person )->run();
		$this->assertTrue( is_numeric( $response ) && $response > 0 );
		
		$result = DB::select( 'people' )
			->where( 'id', $response )
			->column( 'name' );
	
		$this->assertEquals( $person['name'], $result );
	}
	
	/**
	 * Test Query_Select::count
	 *
	 * @dataProvider people_provider_bulk
	 */
	public function test_count( $people )
	{
		// lets kill the db
		DB::run( 'delete from people' );
		
		DB::insert( 'people', $people )->run();
			
		$this->assertEquals( count( $people ), DB::select( 'people' )->count() );
	}
	
	/**
	 * Test Query_Select::forward_key
	 *
	 * @dataProvider people_provider_bulk
	 */
	public function test_forward_key( $people )
	{
		// lets kill the db
		DB::run( 'delete from people' );
		
		DB::insert( 'people', $people )->run();
		
		$people = DB::select( 'people' )
			->forward_key()
			->run();
		
		foreach( $people as $key => $person )
		{
			$this->assertEquals( $person->id, $key );
		}
		
		// diffrent key
		$people = DB::select( 'people' )
			->forward_key( 'name' )
			->run();
			
		foreach( $people as $key => $person )
		{
			$this->assertEquals( $person->name, $key );
		}
	}
	
	/**
	 * Test Query_Select::group_result
	 *
	 * @dataProvider people_provider_bulk
	 */
	public function test_group_result( $people )
	{
		$people_array = $people;
		
		// lets kill the db
		DB::run( 'delete from people' );
		
		DB::insert( 'people', $people )->run();
		
		$people = DB::select( 'people' )
			->group_result( 'age' )
			->forward_key( 'name' )
			->run();
			
			
		foreach( $people_array as $key => $person )
		{
			$this->assertEquals( $person['name'], $people[$person['age']][$person['name']]->name );
		}
	}
}