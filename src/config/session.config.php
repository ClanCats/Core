<?php 
/*
 *---------------------------------------------------------------
 * Session configuration
 *---------------------------------------------------------------
 */
return array(
	/*
	 * Garbage collection
	 * define how often the system removes old sessions
	 */
	'gc'		=> 5,
	/*
	 * the name of the cookie session identifier
	 */
	'name'		=> '_cattoken',
	/*
	 * the session lifetime
	 */
	'lifetime'	=> 300,
	/*
	 * the cookie lifetime
	 */
	'cooike_lifetime' => 0,
	/*
	 * the session driver
	 */
	'driver'	=> 'cookie',
	
	/*
	 * cookie driver settings
	 */
	'cookie' => array(
		// the salt the cookie get cryptet with
		'salt' => 'ch4ng3Th1sToS0meth!ngS3cur3',
	),
	
	/*
	 * databse driver settings
	 */
	'database' => array(
		// what database instance
		'instance' => null,
		
		// what table
		'table' => 'sessions',
	),
);
