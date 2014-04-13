<?php
/*
 *---------------------------------------------------------------
 * Database Bundle
 *---------------------------------------------------------------
 * 
 * Here we define the database interface shadow and namespace
 */
// namepace
\CCFinder::map( 'DB', COREPATH.'bundles/Database/' );

// and the shdaow
\CCFinder::shadow( 'DB', 'DB', COREPATH.'bundles/Database/DB'.EXT );

/*
 *---------------------------------------------------------------
 * UI Bundle
 *---------------------------------------------------------------
 * 
 * The UI Bundle contains some helpers to generate HTML.
 */
// namepace
\CCFinder::map( 'UI', COREPATH.'bundles/UI/' );

/*
 *---------------------------------------------------------------
 * Authentication Bundle
 *---------------------------------------------------------------
 * 
 * The Authentication bundle for basic a basic user and login
 */
// namepace
\CCFinder::map( 'Auth', COREPATH.'bundles/Auth/' );

// and the shdaow
\CCFinder::shadow( 'Auth', 'Auth', COREPATH.'bundles/Auth/Auth'.EXT );