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
		DB::connect();
		while( \DB\Migrator::rollback() );
		\DB\Migrator::migrate();
	}
}