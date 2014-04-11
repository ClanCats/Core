<?php namespace CCConsole; use CCCli;
/**
 * Console Controller 
 * run a application script
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 */
class doctor extends \CCConsoleController {

	/**
	 * return an array with information about the script
	 */
	public function help() 
	{
		return array(
			'name'	=> 'Dr. CCF',
			'desc'	=> 'Can do several operations on the application.',
			'actions'	=> array(
				'permissions'	=> 'checks and repairs the needed permissions.',
			),
		);
	}

	/**
	 * install an orbit module
	 *
	 * @param array 		$params 
	 */
	public function action_permissions( $params ) 
	{
		$folders = \CCEvent::fire( 'ccdoctor.permissions' );
		
		if ( !is_array( $folders ) )
		{
			$folders = array();
		}
		
		// add storage directories
		foreach( \ClanCats::$config->get( 'storage.paths' ) as $folder )
		{
			$folders[] = $folder;
		}
		
		foreach( $folders as $folder )
		{
			$display_folder = \CCStr::replace( $folder, array( CCROOT => '' ) );
			
			// create directory if not existing
			if ( !is_dir( $folder ) ) 
			{
				if ( !mkdir( $folder, 0755, true ) ) 
				{
					CCCli::line( "doctor - could not create folder at: {$display_folder}", 'red' );
				}
			}
			
			// check permissions
			$perm = decoct( fileperms( $folder ) & 0755 );
			
			if ( $perm < 755 )
			{
				CCCli::line( CCCli::color( $perm, 'red' ).
					' - '.
					$display_folder.
					' fixing with '.CCCli::color( '755', 'green' ) 
				);
				if ( !chmod( $folder, 0755 ) )
				{
					CCCli::line( "doctor - is not able to change permissions for: {$display_folder}", 'red' );
				}
			}
			elseif ( $perm == 777 )
			{
				CCCli::line( CCCli::color( $perm, 'yellow' ).
					' - '.
					$display_folder.
					' warning! this can be dangerous.'
				);
			}
			else 
			{
				CCCli::line( CCCli::color( $perm, 'green' ).
					' - '.
					$display_folder
				 );
			}
		}
	}
}