<?php 
/*
 *---------------------------------------------------------------
 * Database configuration for phpunit
 *---------------------------------------------------------------
 */
return array(
	
	'main' => 'phpunit',
	
	'phpunit' => array(
		// selected database
		'db'	 => 'db_ccf2_phpunit',

		// driver
		'driver' => 'mysql',

		// auth
		'host'		=> '127.0.0.1',
		'user' 		=> 'root',
		'pass'		=> '',
		'charset'	=> 'utf8'
	),


	'phpunit_sqlite' => array(

		// driver
		'driver' => 'sqlite',
		'path' => CCPath::get( 'CCUnit::test.db' ),

		'charset'	=> 'utf8'
	),
);