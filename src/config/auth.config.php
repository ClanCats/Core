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
		
		// The default user model
		'user_model' => "\\Auth\\User",
	),
);
