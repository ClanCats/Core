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
 * @group Orbit_ShipInspector
 */

use Orbit\ShipInspector;

class Test_Orbit_ShipInspector extends PHPUnit_Framework_TestCase
{
	/**
	 * Ship inspector provider
	 */
	public function shipInspectorProvider()
	{
		return array( array( ShipInspector::path( ORBITPATH.'testship/' ) ) );
	}
	
	/**
	 * test ShipInspector
	 *
	 * @dataProvider 	shipInspectorProvider
	 */
	public function test_createInstance( $inspector )
	{
		$this->assertInstanceOf( "Orbit\\ShipInspector", $inspector );
		$this->assertEquals( array( 'README.md', 'blueprint.hip', 'composer.json' ), $inspector->files() );
	}
	
	/**
	 * test ShipInspector
	 *
	 * @dataProvider 	shipInspectorProvider
	 */
	public function test_inspectorInfo( $inspector )
	{
		$this->assertEquals( 'Test Ship', $inspector->get( 'name' ) );	 // comes from blueprint.hip
		$this->assertEquals( 'ClanCats\\TestShip', $inspector->get( 'namespace' ) ); // come from README.md
		$this->assertEquals( 'MIT', $inspector->get( 'license' ) ); // comes from composer.json
	}
}