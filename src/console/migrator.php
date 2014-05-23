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
class migrator extends \CCConsoleController {

	/**
	 * return an array with information about the script
	 */
	public function help() 
	{
		return array(
			'name'	=> 'CCF Database migrator',
			'desc'	=> 'Helps keeping the database updated when developing in teams.',
			'actions'	=> array(
				'migrate'	=> 'checks for new migrations and runs them.',
				'rollback'	=> 'reverts the last migration.',
			),
		);
	}

	/**
	 * Migrate the database to the lasted version
	 *
	 * @param array 		$params 
	 */
	public function action_migrate( $params ) 
	{
		\DB\Migrator::migrate();
	}
	
	/**
	 * Revert the last revision
	 *
	 * @param array 		$params 
	 */
	public function action_rollback( $params ) 
	{
		\DB\Migrator::rollback();
	}
}