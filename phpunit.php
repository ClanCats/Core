<?php
/**
 *---------------------------------------------------------------
 * Framework initialisation ( PHPUnit )
 *---------------------------------------------------------------
 *
 * This is the framework initialisation for phpunit 
 * using an test environment "test-env/".
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2015 ClanCats GmbH
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
define( 'CCFROOT', __DIR__.'/' );

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
	'public'		=> CCROOT.'test-env/public/',
	'ccf'			=> CCROOT.'test-env/CCF/',
	'app'			=> CCROOT.'test-env/CCF/app/',
	'orbit'			=> CCROOT.'test-env/CCF/orbit/',
	'vendor'		=> CCROOT.'test-env/CCF/vendor/',
	'core'			=> CCROOT.'src/',
);

/*
 *---------------------------------------------------------------
 * the direcotries
 *---------------------------------------------------------------
 * 
 * Here are the module directories defined. 
 */
$directories = array(
	'controller'		=> 'controllers/',
	'language'			=> 'language/',
	'class'				=> 'classes/',
	'console'			=> 'console/',
	'config'			=> 'config/',
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

// write header
ClanCats::write_cli_header();

/*
 *---------------------------------------------------------------
 * CCUnit resources
 *---------------------------------------------------------------
 *
 * For the unit tests we need some additional resources like
 * controllers, views, ect... 
 */
CCFinder::bundle( 'CCUnit', CCFPATH.'CCUnit/' );


// wake the phpunit application class this bypasses a failure 
// of the clancats::runtime unitest
ClanCats::wake_app( 'PHPUnitApp' );
