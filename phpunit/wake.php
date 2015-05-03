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
 * @version		3.0
 * @copyright 	2010 - 2015 ClanCats GmbH
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
 * set the boot paths
 *---------------------------------------------------------------
 */
$paths = array(
	'core' => CCFROOT.'../bundle/',
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
 * Require CCF core bundle wake
 *---------------------------------------------------------------
 * 
 * The core bundle wake file initialises the ClanCats Framework.
 */
require $paths['core'].'wake.php';

/*
 *---------------------------------------------------------------
 * Create new CCF Application
 *---------------------------------------------------------------
 * 
 * The core bundle wake file initialises the ClanCats Framework.
 */
CCF::create( '\\PHPUnitApp' );