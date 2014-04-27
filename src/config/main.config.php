<?php 
/*
 *---------------------------------------------------------------
 * Main CCF configuration
 *---------------------------------------------------------------
 */
return array(
	
	/*
	 * The used charset, default is utf-8
	 */
	'charset'	=> 'utf-8',
	
	/*
	 * Timezone used in this project
	 * if null the default timezone gets used.
	 */
	'timezone'	=> null,
	
	/*
	 * Locale used in this project
	 * if null the default locale gets used.
	 */
	'locale'		=> null,
	
	/*
	 * The force is strong with this one
	 */
	'output_buffering' => true,
	
	/*
	 * If an Application returns an response
	 * should that response be sended to the client?
	 */
	'send_app_wake_response' => true,
	
	/*
	 * error handling configuration
	 */
	'error' => array(
		
		// What error types should interupt the application
		'fatal_levels' => array(
			E_ERROR,
			E_PARSE,
			E_CORE_ERROR,
			E_CORE_WARNING,
			E_COMPILE_ERROR,
			E_COMPILE_WARNING
		),
		
		// the class that should handle 
		// error exceptions
		'handler'		=> "\\".CCCORE_NAMESPACE."\\CCError_Handler_Pretty",
		
		// error, exception on command line
		'cli_handler'		=> null,
		
		// the inspector class that gets 
		// additional information about 
		// the exception
		'inpector'		=> null,
	),
	
	/** 
	 * CCProfiler configuration
	 */
	'profiler' => array(
		
		// You can disable any profiler actions
		'enabled'	=> true,
	),
	
	/**
	 * Database bundle
	 */
	'database' => array(
	
		// The default primary key ( probably 'id' ) is used
		// when you do for example an find opertation
		//
		//     DB::select( 'people' )->find( '4' ); 
		// 
		// Will use the default primary key for the where statement.
		'default_primary_key' => 'id',
	),
	
	/**
	 * Session bundle
	 */
	'session' => array(
	
		// Define a callback for your default session data.
		'default_data_provider' => '\\Session\\Manager::default_data_provider',
	),
	
	/*
	 * URL configuration
	 */
	'url'	=> array(
		// If not in the root directory set the path offset. 
		// Dont forget to set the "RewriteBase" in the .htaccess
		'path'		=> '/',
	),
	
	/*
	 * storage file managment 
	 */
	'storage' => array(
		
		// what is the default storage holder
		'default' => 'main',
		
		// define the aviable storage paths
		'paths' => array(
			'main'		=> CCFPATH.'storage/',
			'public'		=> PUBLICPATH.'storage/',
		),
		/*
		 * define the public url of the paths if aviable
		 * this can be usefull if you store for example your images
		 * in an directory that is aviable over another domain
		 */
		'urls' => array(
			'public'		=> '/storage/',
		),
	),
	
	/*
	 * Orbit
	 */
	'orbit' => array(
	
		/*
		 * where can the orbit save information about installed plugins, repositories ect.
		 */ 
		'data'	=> ORBITPATH.'orbit.json',
	),
	
	/*
	 * Security
	 */
	'security' => array(
		// it is really important that you choose your own one!
		'salt' => 'ThisAintGoodBro',
		
		// you can set a default hashing function like md5, sha1 whatever or define your own one
		'hash' => function( $str ) {
			return sha1( $str );
		}
	),
	
	/* 
	 * Controller
	 */
	'controller' => array(
		// if the json data of an json controller is invalid should the controller still return the output?
		'allow_invalid_json' => false,
		
		// default request arguments
		'default_args' => array(
		
			// forces a view controller to render without the template
			'force_modal' => false,
			
			// force a theme
			'force_theme' => false,
		),
	),
	
	'viewcontroller' => array(
	
		/*
		 * view conrtoller theme defaults
		 */
		'theme' 	=> array(
			
			/*
			 * The default theme
			 */
			'default' => 'Bootstrap',
			
			/*
			 * The defualt layout file
			 */
			'default_layout' => 'layout',	
		),
	),
	
	/*
	 * Routing
	 */
	'router' => array(
	
		// allowed characters to match :any
		'allowed_special_chars' => '-_.:',
	
		// should multidimensional be flattend?
		'flatten_routes' => true,
		
		// default routes map
		'map' => 'router',
	),
	
	/*
	 * Language Configuration
	 */
	'language' => array(
		// default language and fallback
		'default'	=> 'en-US',
		// available languages
		'available'	=> array(),
	),
);