<?php
/**
 *---------------------------------------------------------------
 * Framework initialisation
 *---------------------------------------------------------------
 *
 * This is the framework initialisation. Thats the point where
 * all important parts come together and build something 
 * aweomse together.
 */

/*
 *---------------------------------------------------------------
 * file extension 
 *---------------------------------------------------------------
 * 
 * This defines the global used file extention of the php files.
 */
define( 'EXT', '.php' );
 
/*
 * The paths have always to be set in the application the 
 * core does not have any defaults.
 * 
 * To be a bit more performance efficent we define 
 * the paths in the system so we can later make use them:
 * 
 *     app => CCPATH_APP
 *     core => CCPATH_CORE
 */
$paths = array_merge( array(

    // The default path to your CCF application
	'app'			=> CCFROOT.'app/',
	
	// the default path the applications orbit
	'orbit'			=> CCFROOT.'orbit/',
	
	// the default path of the application public directory
	'public'		=> CCFROOT.'public/',
	
	// the default path to the composer vendor
	'vendor'		=> CCFROOT.'vendor/',
	
	// the default path of the CCF core bundle
	'core'			=> CCFROOT.'vendor/CCF/core/bundle/',
), $paths ); 

foreach( $paths as $key => $path )
{
	define( 'CCPATH_'.strtoupper( $key ), $path );
}

/*
 * You can add more or overwrite directories directly in 
 * your framework initialisation file.
 * 
 * Also the directories for special resources are defined. Again
 * mostly for performance reasons.
 * 
 *     class => CCDIR_CLASS
 *     view => CCDIR_VIEW
 */
$directories = array_merge( array(
    'language'			=> 'language/',
    'source'			=> 'source/',
    'config'			=> 'config/',
    'view'				=> 'views/',
    'test'				=> 'tests/',
), $directories ); 

foreach( $directories as $key => $path )
{
	define( 'CCDIR_'.strtoupper( $key ), $path );
}

/*
 *---------------------------------------------------------------
 * Profiling framework start snapshots
 *---------------------------------------------------------------
 * 
 * We store the application start time and memory in an define
 * to be able to calculate the execution time later.
 */
define( 'CCF_PROFILER_MICROTIME_START', microtime( true ) );
define( 'CCF_PROFILER_MEMORY_START', memory_get_usage() );

/*
 *---------------------------------------------------------------
 * Require CCFinder & register the autoloader
 *---------------------------------------------------------------
 * 
 * The time has come to load our autoloader. 
 */
require_once CCPATH_CORE.CCDIR_SOURCE."CCFinder".EXT;

CCFinder::register();

/*
 *---------------------------------------------------------------
 * CCFFramework map 
 *---------------------------------------------------------------
 * 
 * include the CCF map file 
 */
require CCPATH_CORE.'map'.EXT;
die;
/*
 *---------------------------------------------------------------
 * shortcuts
 *---------------------------------------------------------------
 * 
 * Load the shortcut functions. This file contains mostly
 * shortcuts for class functions like 
 * CCStr::htmlentities() = _e()
 * but also some mini helpers like _dd() for var_dump and die. 
 */
require COREPATH.'shortcuts'.EXT;

/*
 *---------------------------------------------------------------
 * shutdown handler
 *---------------------------------------------------------------
 * 
 * Register our shutdown handler so we can display custom error
 * messages and run events before shutdown like saving the 
 * session ect..
 */
register_shutdown_function( function() 
{	
	// try to run all shutdown hooks
	try {
		\CCEvent::fire( 'CCF.shutdown' );
	} catch( \Exception $e ) {}

	// run error shutdown to catch possible errors
	if ( class_exists( "\\CCError" ) )
	{
		\CCError::shutdown();
	}
});

/*
 *---------------------------------------------------------------
 * exception handler
 *---------------------------------------------------------------
 * 
 * Register our handler for uncaught exceptions, so we can
 * handle them on our own.
 */
set_exception_handler( function( Exception $exception ) 
{
	if ( class_exists( "\\CCError" ) )
	{
		\CCError::exception( $exception );
	}
});

/*
 *---------------------------------------------------------------
 * exception handler
 *---------------------------------------------------------------
 *  
 * Register our error handler.
 */
set_error_handler( function( $level, $message, $file = null, $line = null ) 
{
	if ( class_exists( "\\CCError" ) )
	{
		\CCError::error( $level, $message, $file, $line );
	}
}); 

/*
 *---------------------------------------------------------------
 * error reporting
 *---------------------------------------------------------------
 * 
 * Because we got now our nice own error handlers we don't wont 
 * that PHP itself prints any errors directly to the user.
 */
error_reporting(-1);

/*
 *---------------------------------------------------------------
 * pass the paths and directories
 *---------------------------------------------------------------
 * 
 * CCF wants to know wich paths and directories are registerd
 * so we pass the initinal param to the CCF object.
 */
CCF::paths( $paths, false );
CCF::directories( $directories, false );

unset( $paths, $directories );

/*
 *---------------------------------------------------------------
 * environment setup
 *---------------------------------------------------------------
 * 
 * In CCF 1.0 we used a $_SERVER environment variable to set the
 * current CCF environment. This works great if you are abel to  
 * change the server conf. But often people dont have access to 
 * the configurations. 
 * So after doing multiple projects came to the 
 * conclusion that the framework itself should detect the 
 * environent using the hostname or other params.
 */
if ( !isset( $environment ) )
{
	$environment = CCF::environment_detector( require CCROOT.'boot/environment'.EXT );
}

/*
 *---------------------------------------------------------------
 * wake ccf
 *---------------------------------------------------------------
 * 
 * Lets wake the ccf and pass the environment.
 */
CCF::wake( $environment );

unset( $environment );

// at this point ccf has completet its own boot
CCProfiler::check( "[CCF] framework wake completed" );

/*
 *---------------------------------------------------------------
 * output buffer
 *---------------------------------------------------------------
 * 
 * Start output buffering if it isn't disabled and we are not 
 * running ccf from the command line interface.
 */
if ( !CCF::is_cli() && CCF::$config->output_buffering ) 
{
	ob_start();
}

/*
 *---------------------------------------------------------------
 * timezone
 *---------------------------------------------------------------
 * 
 * Sets the default timezone used by all php native date/time 
 * functions in the application. 
 */
if ( CCF::$config->timezone ) 
{
	if ( !date_default_timezone_set( CCF::$config->timezone ) ) 
	{
		throw new CCException( "CCF - The given timezone is invalid. check main config -> timezone." );
	}
}

/*
 *---------------------------------------------------------------
 * timezone
 *---------------------------------------------------------------
 * 
 * Sets the default locale.
 */
if ( CCF::$config->locale ) 
{
	if ( !setlocale( LC_ALL, CCF::$config->locale ) ) {
		throw new CCException( "CCF - The given locale is invalid.  check main config -> locale" );
	}
}

/*
 *---------------------------------------------------------------
 * Require the application map
 *---------------------------------------------------------------
 * 
 * The application map can contain additional path information
 */
if ( file_exists( APPPATH.'map'.EXT ) )
{
	require APPPATH.'map'.EXT;
}

/*
 *---------------------------------------------------------------
 * composer / vendor
 *---------------------------------------------------------------
 * 
 * After ccf is done with its own initialisation we implement the
 * composers vendor autoloader.
 */
if ( !file_exists( VENDORPATH."autoload".EXT ) )
{
    throw new CCException( "Cannot find autoload.php under `".VENDORPATH."autoload".EXT."`. Did you run `composer install`?" );
}

require_once VENDORPATH."autoload".EXT;


// at this point vendor autoloader is registered
CCProfiler::check( "[CCF] Vendro autoloader registered." );

/*
 *---------------------------------------------------------------
 * installed ships
 *---------------------------------------------------------------
 * 
 * Load the orbit map.
 * The orbit map is a simple autogenerated php file that includes
 * and wakes / initializes the installed orbit ships.
 */
if ( !file_exists( $path = CCStorage::path( 'orbit/map'.EXT ) ) )
{
    CCStorage::write( 'orbit/map'.EXT, CCOrbit::station()->create_map() );
}

require $path; unset( $path );
