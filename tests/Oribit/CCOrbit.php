<?php
/**
 * Session tests
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.1
 * @copyright 	2010 - 2015 ClanCats GmbH
 *
 * @group Orbit
 * @group Orbit_CCOrbit
 */
class Test_Orbit_CCOrbit extends PHPUnit_Framework_TestCase
{
	/**
	 * test CCSession::set 
	 */
	public function test_station()
	{
		$this->assertInstanceOf( 'Orbit\\Station', CCOrbit::station() );
	}
}