<?php namespace DB;
/**
 * Database test case
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 */

use CCConfig;
use CCPath;

class TestCase extends \PHPUnit_Framework_TestCase
{
	/**
	 * Is a database configured?
	 *
	 * @var bool
	 */
	public static $dbtests = false;
	
	/**
	 * people data bulk
	 */
	public static function people_provider_bulk() 
	{
		return array(
			array(
				array(
					array( 'name' => 'Mario', 'age' => 20, 'library_id' => 1 ),
					array( 'name' => 'Ladina', 'age' => 20, 'library_id' => 2 ),
					array( 'name' => 'Johanna', 'age' => 18, 'library_id' => 1 ),
					array( 'name' => 'Jenny', 'age' => 22, 'library_id' => 0 ),
					array( 'name' => 'Melanie', 'age' => 19, 'library_id' => 0 ),
					array( 'name' => 'Tarek', 'age' => 20, 'library_id' => 3 ),
					array( 'name' => 'John', 'age' => 42, 'library_id' => 4 ),
				),
			),
		);
	}
	
	/**
	 * people data
	 */
	public static function people_provider() 
	{
		return array(
			array(
				array( 'name' => 'Mario', 'age' => 20, 'library_id' => 1 ),
			),
			array(
				array( 'name' => 'Ladina', 'library_id' => 2, 'age' => 20 ),
			),
			array(
				array( 'name' => 'Johanna', 'age' => -18, 'library_id' => 1 ),
			),
			array(
				array( 'age' => 22, 'library_id' => 0,  'name' => 'Jenny' ),
			),
		);
	}
	
	/**
	 * Check if DB test are possible
	 *
	 * @return void
	 */
	public static function setUpBeforeClass() 
	{	
		// lets make sure that we have an db configuration for phpunit
		if ( CCConfig::create( 'database' )->has( 'phpunit' ) )
		{	
			// lets try to connect to that database if the conection
			// fails we just return and continue the other tests
			try { DB::connect( 'phpunit' ); }
			catch ( \PDOException $e ) { return; }
			
			// connection succeeded?
			static::$dbtests = true;
			
			// overwrite the main db
			CCConfig::create( 'database' )->set( 'main', 'phpunit' );
			
			// kill all connections
			Handler::kill_all_connections();
			
			// in the CCUnit bundle there is a little sql file containing 
			// the needed tables to run our tests. Lets load that file
			// and create the phpunit tables
			$queries = explode( ';', file_get_contents( CCPath::get( 'CCUnit::phpunit.sql' ) ) );
			
			foreach( $queries as $query )
			{
				$query = trim( $query );

				if ( !empty( $query ) )
				{
					DB::run( $query, array(), 'phpunit' );
				}
			}
		}
	}
	
	public static function tearDownAfterClass() 
	{	
		// write the main database back to app
		CCConfig::create( 'database' )->set( 'main', 'app' );
		// kill all connections
		Handler::kill_all_connections();
	}

	/**
	 * Check if we can execute db tests
	 * And add a warning to phpunit that we skipped the test.
	 *
	 * @return void
	 */
	protected function setUp()
	{
		if ( !static::$dbtests )
		{
			$this->markTestSkipped( "Warning! Could not connect to phpunit DB. skipping DB unit test." );
		}
	}
}