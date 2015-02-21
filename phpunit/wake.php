<?php
/**
 *---------------------------------------------------------------
 * Framework initialisation ( PHPUnit )
 *---------------------------------------------------------------
 *
 * This is the framework initialisation for phpunit.
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
 * set the boot paths
 *---------------------------------------------------------------
 */
$paths = array(
	'app'			=> CCFROOT.'app/',
	'orbit'			=> CCFROOT.'orbit/',
	'public'		=> CCFROOT.'public/',
	'vendor'		=> CCFROOT.'vendor/',
	'core'			=> CCFROOT.'../bundle/',
);

/*
 *---------------------------------------------------------------
 * custom directory names
 *---------------------------------------------------------------
 */
$directories = array();

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
//CCFinder::bundle( 'CCUnit', CCFPATH.'CCUnit/' );


// wake the phpunit application class this bypasses a failure 
// of the clancats::runtime unitest
//ClanCats::wake_app( 'PHPUnitApp' );
