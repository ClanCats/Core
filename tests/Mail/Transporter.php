<?php
/**
 * CCF Mail Test suite
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 * @group Mail
 * @group Mail_Transporter
 */

use Mail\Transporter;
 
class Test_Mail_Transporter extends \PHPUnit_Framework_TestCase
{  
	/**
	 * Transporter::create tests
	 */
	public function test_create()
	{
		$transporter = Transporter::create();
		
		$this->assertTrue( $transporter instanceof Transporter );
		
		$transporter = Transporter::create( 'alias' );
		
		$this->assertTrue( $transporter instanceof Transporter );
		
		// reload that thing from store
		$transporter = Transporter::create( 'alias' );
		
		$this->assertTrue( $transporter instanceof Transporter );
		
		// kill and load again
		Transporter::kill_instance( 'alias' );
		
		$transporter = Transporter::create( 'alias' );
		
		$this->assertTrue( $transporter instanceof Transporter );
		
		// custom transporter
		$transporter = Transporter::create( 'custom', array( 'driver' => 'array' ) );
		
		$this->assertTrue( $transporter instanceof Transporter );
	}
	
	/**
	 * Transporter::create tests
	 *
	 * @expectedException Mail\Exception
	 */
	public function test_create_invalid()
	{
		$transporter = Transporter::create( 'invalid' );
	}
}