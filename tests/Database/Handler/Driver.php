<?php
/**
 * Database tests
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2015 ClanCats GmbH
 *
 * @group Database
 * @group Database_Handler_Driver
 */
class Test_Database_Handler_Driver extends PHPUnit_Framework_TestCase
{
	/**
	 * DB\Handler_Driver::connect test
	 *
	 * @expectedException PDOException
	 */
	public function test_connect()
	{
		$driver = new DB\Handler_Driver;
		$driver->connect();
	}
}