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
		CCFile::delete( APPPATH.'config/migrator.json' );
		
		DB::connect();
		while( \DB\Migrator::rollback() );
		\DB\Migrator::migrate();
	}
}