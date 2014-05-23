<?php
/**
 *---------------------------------------------------------------
 * Framework initialisation
 *---------------------------------------------------------------
 *
 * This is the framework initialisation. Thats the point where
 * all important parts come together and build something 
 * aweomse together.
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 * ###
 *
 *---------------------------------------------------------------
 * Application root 
 *---------------------------------------------------------------
 * 
 * The application root or CCROOT defines the absoulte path to 
 * the framework.
 */
define( 'CCROOT', __DIR__.'/' );

/*
 *---------------------------------------------------------------
 * file extension 
 *---------------------------------------------------------------
 * 
 * This defines the global used file extention of the php files.
 */
define( 'EXT', '.php' );

/*
 *---------------------------------------------------------------
 * get the boot paths
 *---------------------------------------------------------------
 * 
 * You can modify that file, its yours. Its especially useful
 * if you have multiple installations on one server and want 
 * to use just one core or one orbit for them all.
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
 *---------------------------------------------------------------
 * environment setup
 *---------------------------------------------------------------
 *
 * force the environment to phpunit.
 */
$environment = 'phpunit';

/*
 *---------------------------------------------------------------
 * wake CCF
 *---------------------------------------------------------------
 * 
 * Lets require the ClanCatsFramework resources
 */
require $paths['core'].'wake'.EXT;


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