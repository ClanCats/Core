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
 * @group Database_Handler_Sqlite
 */
class Test_Database_Handler_Sqlite extends \DB\TestCase
{
	/**
	 * our data
	 */
	public static function people_provider() 
	{
		return array(
			array(
				array(
					array( 'name' => 'Mario', 'age' => 20 ),
					array( 'name' => 'Ladina', 'age' => 20 ),
					array( 'name' => 'Johanna', 'age' => 18 ),
					array( 'name' => 'Jenny', 'age' => 22 ),
					array( 'name' => 'Melanie', 'age' => 19 ),
					array( 'name' => 'Tarek', 'age' => 20 ),
					array( 'name' => 'John', 'age' => 42 ),
				),
			),
		);
	}
	
	/**
	 * test if both connections return true
	 */
	public function test_connect()
	{
		$this->assertTrue( DB::connect( 'phpunit_sqlite' ) );
	}

	/**
	 * test the handler instance
	 */
	public function test_handler()
	{
		$handler = DB::handler( 'phpunit_sqlite' );

		$this->assertTrue( $handler instanceof \DB\Handler );

		$this->assertTrue( $handler->connected() );

		$this->assertTrue( $handler->driver() instanceof \DB\Handler_Driver );
		
		$this->assertTrue( $handler->driver() instanceof \DB\Handler_Sqlite );

		$this->assertTrue( $handler->statement( 'select * from people' ) instanceof \PDOStatement );
	}
	
	/**
	 * Try to create the sql tables
	 */
	public function test_create_table()
	{
		$handler = DB::handler( 'phpunit_sqlite' );
		
		$handler->run( "DROP TABLE IF EXISTS people" );
		
		$handler->run( 
		"CREATE TABLE people ( 
			id INTEGER PRIMARY KEY AUTOINCREMENT, 
			name VARCHAR, 
			age INTEGER
		);");
	}
	
	/**
	 * Try to insert the data
	 * 
	 * @dataProvider people_provider
	 */
	public function test_insert( $people )
	{
		$handler = DB::handler( 'phpunit_sqlite' );
		
		$query = 'insert into people ( name, age ) VALUES ( ?, ? )';
		
		foreach( $people as $key => $person )
		{	
			$this->assertEquals( $key+1, $handler->run( $query, array( $person['name'], $person['age'] ) ) );
		}
		
		// check the count
		$this->assertEquals( count( $people ), count( $handler->fetch( 'select * from people', array() ) ) );
		
		// check the data
		foreach( $handler->fetch( 'select * from people', array() ) as $record )
		{
			$person = $people[ $record->id -1 ];
			
			// check the attributes
			foreach( $person as $key => $attr )
			{
				$this->assertEquals( $attr, $record->$key );
			}
		}
	}
	
	/**
	 * Try to update the data
	 * 
	 * @dataProvider people_provider
	 */
	public function test_update( $people )
	{
		// randomize the peoples age
		foreach( $people as $key => $person )
		{
			$people[$key]['age'] = mt_rand( 16, 40 );
		}
		
		$handler = DB::handler( 'phpunit_sqlite' );
		
		// run the updaes
		$query = 'update people set age = ? where id = ?';
		
		foreach( $people as $key => $person )
		{	
			$this->assertEquals( 1, $handler->run( $query, array( $person['age'], $key+1 ) ) );
		}
		
		// check the data
		foreach( $handler->fetch( 'select * from people', array() ) as $record )
		{
			$person = $people[ $record->id -1 ];
			
			// check the attributes
			foreach( $person as $key => $attr )
			{
				$this->assertEquals( $attr, $record->$key );
			}
		}
	}
	
	/**
	 * Try to create the sql tables
	 *
	 * @dataProvider people_provider
	 */
	public function test_delete( $people )
	{
		$handler = DB::handler( 'phpunit_sqlite' );
		
		$handler->run( 'delete from people where id = ?', array( 1 ) );
		
		// check the count
		$this->assertEquals( count( $people ) -1, count( $handler->fetch( 'select * from people', array() ) ) );
	}
}