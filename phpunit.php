<?php 
/*
 *---------------------------------------------------------------
 * Core PHPUnit runner
 *---------------------------------------------------------------
 *
 * This phpunit script is a simplefied framework.php wich should
 * initalise ccf for unitesting without an application.
 */
define( 'CCROOT', __DIR__.'/' );

define( 'EXT', '.php' );

/*
 *---------------------------------------------------------------
 * paths
 *---------------------------------------------------------------
 * 
 * Because we dont have an app link here we use an test env.
 */
$paths = array(
	'public'			=> CCROOT.'test-env/public/',
	'ccf'			=> CCROOT.'test-env/ccf/',
	'app'			=> CCROOT.'test-env/ccf/app/',
	'orbit'			=> CCROOT.'test-env/ccf/orbit/',
	'vendor'			=> CCROOT.'test-env/vendor/',
	
	'core'			=> CCROOT.'src/',
);

/*
 * define the boot paths to our application with the PATH suffix
 * like app = APPPATH ect...
 */
foreach( $paths as $key => $path )
{
	define( strtoupper( $key ).'PATH', $path );
}

/*
 *---------------------------------------------------------------
 * the direcotries
 *---------------------------------------------------------------
 * 
 * Here are the module directories defined. 
 * @ToDo: move them to the classes that use that direcotries. 
 *        that way the you could subclass a class and define 
 *        a custom direcotry.
 */
$directories = array(
	'controller'			=> 'controllers/',
	'language'			=> 'language/',
	'class'				=> 'classes/',
	'console'			=> 'console/',
	'config'				=> 'config/',
	'view'				=> 'views/',
	'test'				=> 'tests/',
);

/*
 * define the module direcotries with the CCDIR prefix
 * like class = CCDIR_CLASS ect...
 */
foreach( $directories as $key => $path )
{
	define( 'CCDIR_'.strtoupper( $key ), $path );
}

/*
 *---------------------------------------------------------------
 * profiling framework start snapshots
 *---------------------------------------------------------------
 * 
 * We store the application start time and memory in an define
 * to be able to calculate the execution time later.
 */
define( 'CCF_PROFILER_TME', microtime( true ) );
define( 'CCF_PROFILER_MEM', memory_get_usage() );

/*
 *---------------------------------------------------------------
 * require autoloader
 *---------------------------------------------------------------
 * 
 * The time has come to load our autoloader. 
 */
require_once COREPATH.CCDIR_CLASS."CCFinder".EXT;

/*
 *---------------------------------------------------------------
 * register autoloader
 *---------------------------------------------------------------
 * 
 * Register the autoloader with spl_autoload_register. And define
 * the core namespace ( CCCORE_NAMESPACE ) for global use.
 */
\CCFinder::register();

define( 'CCCORE_NAMESPACE', 'Core' );

/*
 *---------------------------------------------------------------
 * core namespace
 *---------------------------------------------------------------
 * 
 * Add the core namespace so the autoloader knows where he finds
 * core classes.
 */
\CCFinder::bundle( CCCORE_NAMESPACE, COREPATH );

/*
 *---------------------------------------------------------------
 * core package
 *---------------------------------------------------------------
 * 
 * Add a core shadow containing a map of our core classes this 
 * way the auoloader wil alias core class into the global
 * namespace when they are required.
 */
\CCFinder::shadow_package( COREPATH.CCDIR_CLASS, CCCORE_NAMESPACE, require( COREPATH."coremap".EXT ) );

/*
 *---------------------------------------------------------------
 * core bundles
 *---------------------------------------------------------------
 * 
 * There are some bundles we seperated on another namespace these
 * are defined in the this map
 */
require COREPATH."bundles/map".EXT;

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
ClanCats::paths( $paths, false );
ClanCats::directories( $directories, false );

unset( $paths, $directories );


ClanCats::wake( 'phpunit' );

/*
 *---------------------------------------------------------------
 * timezone
 *---------------------------------------------------------------
 * 
 * Sets the default timezone used by all php native date/time 
 * functions in the application. 
 */
if ( ClanCats::$config->timezone ) 
{
	if ( !date_default_timezone_set( ClanCats::$config->timezone ) ) 
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
if ( ClanCats::$config->locale ) 
{
	if ( !setlocale( LC_ALL, ClanCats::$config->locale ) ) {
		throw new CCException( "CCF - The given locale is invalid.  check main config -> locale" );
	}
}

// wake the phpunit application class this bypasses a failure 
// of the clancats::runtime unitest
ClanCats::wake_app( 'PHPUnitApp' );

/*
 *---------------------------------------------------------------
 * CCUnit resources
 *---------------------------------------------------------------
 *
 * For the unit tests we need some additional resources like
 * controllers, views, ect... 
 */
CCOrbit::enter( COREPATH.'orbit/CCUnit' );

// writ header
CCCli::line("==============================
    _____ _____ ______ 
   / ____/ ____|  ____|
  | |   | |    | |__   
  | |   | |    |  __|  
  | |___| |____| |     
   \_____\_____|_| ramework
==============================
", 'cyan');