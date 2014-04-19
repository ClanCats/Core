<?php 
/*
 *---------------------------------------------------------------
 * Session configuration
 *---------------------------------------------------------------
 */
return array(

	/*
	 * This is the default configuration for the main session
	 */
	'main' => array(

		// Choose the driver to store the sessions with.
		// We hopefully ship with:
		//     * array
		//     * cookie
		//     * file
		//     * database
		'driver'	 => 'array',
	),
	
	/*
	 * Test the file driver
	 */
	'file' => array(
		'driver'	 => 'file',
	),
);
