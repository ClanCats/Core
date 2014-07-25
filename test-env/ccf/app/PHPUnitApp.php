<?php 
/*
 *---------------------------------------------------------------
 * Application Object
 *---------------------------------------------------------------
 *
 * This is your default application object.
 */
class PHPUnitApp extends CCApp 
{	
	/**
	 * The application name
	 *
	 * @var string
	 */
	public static $name = 'Core PHPUnit App';
	
	/**
	 * reset and run the migrations
	 */
	public static function wake() 
	{
		// complete overwrite of DB configuration
		CCConfig::create( 'database' )->_data = CCConfig::create( 'Core::phpunit/database' )->_data;
		
		// delete all database table
		DB\Migrator::hard_reset();
		DB\Migrator::hard_reset( 'phpunit' );
		
		// run the migrations
		DB\Migrator::migrate( true );
	}
}