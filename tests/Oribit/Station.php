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
 * @group Orbit_Station
 */

use Orbit\Station;
use Orbit\Ship;

class Test_Orbit_Station extends PHPUnit_Framework_TestCase
{
	/**
	 * tests installing a ship
	 */
	public function test_install()
	{
		$station = new Station;
		
		$ship = Ship::path( ORBITPATH.'testship/' );
		
		$station->install( $ship );
		
		var_dump( $ship );
	}
}