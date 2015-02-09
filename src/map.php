<?php
/*
 *---------------------------------------------------------------
 * Database Bundle
 *---------------------------------------------------------------
 * 
 * Here we define the database interface shadow and namespace
 */
// namepace
\CCFinder::map( 'DB', COREPATH.'Database/' );

// and the shdaow
\CCFinder::shadow( 'DB', 'DB', COREPATH.'Database/DB'.EXT );

/*
 *---------------------------------------------------------------
 * UI Bundle
 *---------------------------------------------------------------
 * 
 * The UI Bundle contains some helpers to generate HTML.
 */
// namepace
\CCFinder::map( 'UI', COREPATH.'UI/' );

/*
 *---------------------------------------------------------------
 * Session Bundle
 *---------------------------------------------------------------
 * 
 * Session managment bundle
 */
// namepace
\CCFinder::map( 'Session', COREPATH.'Session/' );

// and the shdaow
\CCFinder::shadow( 'CCSession', 'Session', COREPATH.'Session/CCSession'.EXT );

/*
 *---------------------------------------------------------------
 * Authentication Bundle
 *---------------------------------------------------------------
 * 
 * The Authentication bundle for basic a basic user and login
 */
// namepace
\CCFinder::map( 'Auth', COREPATH.'Auth/' );

// and the shdaow
\CCFinder::shadow( 'CCAuth', 'Auth', COREPATH.'Auth/CCAuth'.EXT );

/*
 *---------------------------------------------------------------
 * Email Bundle
 *---------------------------------------------------------------
 * 
 * The Email bundle mostly wraps phpmailer
 */
// namepace
\CCFinder::map( 'Mail', COREPATH.'Mail/' );
// phpmailer
\CCFinder::bind( array(
    "Mail\\PHPMailer\\PHPMailer" => COREPATH.'Mail/PHPMailer/class.phpmailer'.EXT,
    "Mail\\PHPMailer\\POP3" => COREPATH.'Mail/PHPMailer/class.php3'.EXT,
    "Mail\\PHPMailer\\SMTP" => COREPATH.'Mail/PHPMailer/class.smtp'.EXT,
));

// and the shdaow
\CCFinder::shadow( 'CCMail', 'Mail', COREPATH.'Mail/CCMail'.EXT );

/*
 *---------------------------------------------------------------
 * Orbit Bundle
 *---------------------------------------------------------------
 * 
 * Orbit bundle provides simple extesion loading
 */
// namepace
\CCFinder::map( 'Orbit', COREPATH.'Orbit/' );

// and the shdaow
\CCFinder::shadow( 'CCOrbit', 'Orbit', COREPATH.'Orbit/CCOrbit'.EXT );