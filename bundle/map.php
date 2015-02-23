<?php
/*
 *---------------------------------------------------------------
 * Core namespace
 *---------------------------------------------------------------
 * 
 * Add the Core bundle, so we can start using framework resources.
 */
define( 'CCNAMESPACE_CORE', 'ClanCats\\Core' );

\CCFinder::bundle( CCNAMESPACE_CORE, CCPATH_CORE );

/*
 *---------------------------------------------------------------
 * Core shadows
 *---------------------------------------------------------------
 * 
 * Add a core shadow containing a map of our core classes this 
 * way the auoloader will alias core class into the global
 * namespace when they are required.
 * 
 * This allows us to overwrite and extend any core class.
 */
\CCFinder::shadow_package( CCPATH_CORE.CCDIR_SOURCE, CCNAMESPACE_CORE, array(

    'ClanCats'				=> 'ClanCats'.EXT,
    'CCPath'				=> 'CCPath'.EXT,
    'CCConfig'				=> 'CCConfig'.EXT,
    'CCIn'					=> 'CCIn'.EXT,
    'CCIn_Instance'			=> 'CCIn/Instance'.EXT,
    'CCServer'				=> 'CCServer'.EXT,
    'CCContainer'			=> 'CCContainer'.EXT,
    'CCStorage'				=> 'CCStorage'.EXT,
    'CCApp'					=> 'CCApp'.EXT,
    'CCRequest'				=> 'CCRequest'.EXT,
    'CCResponse'			=> 'CCResponse'.EXT,
    'CCRedirect'			=> 'CCRedirect'.EXT,
    'CCStr'					=> 'CCStr'.EXT,
    'CCArr'					=> 'CCArr'.EXT,
    'CCCookie'				=> 'CCCookie'.EXT,
    'CCDate'				=> 'CCDate'.EXT,
    'CCLog'					=> 'CCLog'.EXT,
    'CCUrl'					=> 'CCUrl'.EXT,
    'CCProfiler'			=> 'CCProfiler'.EXT,
    'CCFile'				=> 'CCFile'.EXT,
    'CCCrypter'				=> 'CCCrypter'.EXT,
    'CCError'				=> 'CCError'.EXT,
    'CCException'			=> 'CCException'.EXT,
    'CCEvent'				=> 'CCEvent'.EXT,
    'CCValidator'			=> 'CCValidator'.EXT,
    'CCFileValidator'		=> 'CCFileValidator'.EXT,
    'CCImage'				=> 'CCImage'.EXT,
    'CCColor'				=> 'CCColor'.EXT,
    'CCLang'				=> 'CCLang'.EXT,
    'CCHTTP'				=> 'CCHTTP'.EXT,
    'CCDataObject'			=> 'CCDataObject'.EXT,
    'CCJson'				=> 'CCJson'.EXT,
    'CCForge_Php'			=> 'CCForge/Php'.EXT,
    'CCShipyard'			=> 'CCShipyard'.EXT,

    // Cli
    'CCCli'					=> 'CCCli'.EXT,
    'CCConsoleController'	=> 'CCConsoleController'.EXT,

    // -- Controller Struct
    'CCRoute'				=> 'CCRoute'.EXT,
    'CCRouter'				=> 'CCRouter'.EXT,
    'CCController'			=> 'CCController'.EXT,
    'CCViewController'		=> 'CCViewController'.EXT,

    // -- ORM
    'CCModel'		=> 'CCModel'.EXT,

    // -- View
    'CCView'		=> 'CCView'.EXT,
    'CCTheme'		=> 'CCTheme'.EXT,
    'CCAsset'		=> 'CCAsset'.EXT,
));

/*
 *---------------------------------------------------------------
 * Database Bundle
 *---------------------------------------------------------------
 * 
 * Here we define the database interface shadow and namespace
 */
// namepace
\CCFinder::map( 'DB', COREPATH.CCDIR_CLASS.'Database/' );

// and the shdaow
\CCFinder::shadow( 'DB', 'DB', COREPATH.CCDIR_CLASS.'Database/DB'.EXT );

/*
 *---------------------------------------------------------------
 * UI Bundle
 *---------------------------------------------------------------
 * 
 * The UI Bundle contains some helpers to generate HTML.
 */
// namepace
\CCFinder::map( 'UI', COREPATH.CCDIR_CLASS.'UI/' );

/*
 *---------------------------------------------------------------
 * Session Bundle
 *---------------------------------------------------------------
 * 
 * Session managment bundle
 */
// namepace
\CCFinder::map( 'Session', COREPATH.CCDIR_CLASS.'Session/' );

// and the shdaow
\CCFinder::shadow( 'CCSession', 'Session', COREPATH.CCDIR_CLASS.'Session/CCSession'.EXT );

/*
 *---------------------------------------------------------------
 * Authentication Bundle
 *---------------------------------------------------------------
 * 
 * The Authentication bundle for basic a basic user and login
 */
// namepace
\CCFinder::map( 'Auth', COREPATH.CCDIR_CLASS.'Auth/' );

// and the shdaow
\CCFinder::shadow( 'CCAuth', 'Auth', COREPATH.CCDIR_CLASS.'Auth/CCAuth'.EXT );

/*
 *---------------------------------------------------------------
 * Email Bundle
 *---------------------------------------------------------------
 * 
 * The Email bundle mostly wraps phpmailer
 */
// namepace
\CCFinder::map( 'Mail', COREPATH.CCDIR_CLASS.'Mail/' );
// phpmailer
\CCFinder::bind( array(
    "Mail\\PHPMailer\\PHPMailer" => COREPATH.CCDIR_CLASS.'Mail/PHPMailer/class.phpmailer'.EXT,
    "Mail\\PHPMailer\\POP3" => COREPATH.CCDIR_CLASS.'Mail/PHPMailer/class.php3'.EXT,
    "Mail\\PHPMailer\\SMTP" => COREPATH.CCDIR_CLASS.'Mail/PHPMailer/class.smtp'.EXT,
));

// and the shdaow
\CCFinder::shadow( 'CCMail', 'Mail', COREPATH.CCDIR_CLASS.'Mail/CCMail'.EXT );

/*
 *---------------------------------------------------------------
 * Orbit Bundle
 *---------------------------------------------------------------
 * 
 * Orbit bundle provides simple extesion loading
 */
// namepace
\CCFinder::map( 'Orbit', COREPATH.CCDIR_CLASS.'Orbit/' );

// and the shdaow
\CCFinder::shadow( 'CCOrbit', 'Orbit', COREPATH.CCDIR_CLASS.'Orbit/CCOrbit'.EXT );