<?php 
/*
 *---------------------------------------------------------------
 * Auth configuration
 *---------------------------------------------------------------
 */
return array(

	/*
	 * This is the default configuration for the main session
	 */
	'main' => array(

		// Wich session manager should be used?
		// null = default session manager
		'session_manager' => null,

		// On wich field should the current logged in user
		// id be saved in the session?
		'session_key' => 'user_id',

		// On wich field do we select the user for 
		// the authentification
		'user_key' => 'id',

		// The User model
		'user_model' => "\\Auth\\User",

		// Where to store the active logins
		// how long do they stay active etc.
		'logins' => array(

			// the logins db handler
			'handler' => null,

			// the logins db table
			'table' => 'logins',
		),
	),
	
	'diffrent_selector_keys' => array(
		'session_key' => 'user_email',
		'user_key' => 'email',
	),
	
	'other' => array(),
);
