<?php
/**
 * CCF Validator Test suite
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 * @group Core
 * @group CCValidator
 */
class CCValidator_Test extends \PHPUnit_Framework_TestCase
{
	/**
	 * CCValidator::create tests
	 */
	public function test_create()
	{
		$validator = CCValidator::create( array( 'username' => 'mario' ) );
		
		$this->assertTrue( $validator instanceof CCValidator );
		
		$this->assertEquals( 'mario', $validator->data( 'username' ) );
	}
	
	/**
	 * CCValidator::post tests
	 */
	public function test_post()
	{
		CCIn::instance( new CCIn_Instance( array(), array( 'agb' => 1 ), array(), array(), array() ) );
		
		$validator = CCValidator::post( array( 'agb' => (bool) CCIn::post( 'agb' ) ) );
		
		$this->assertTrue( $validator instanceof CCValidator );
		
		$this->assertInternalType( 'bool', $validator->data( 'agb' ) );
		
		$this->assertTrue( $validator->data( 'agb' ) );
	}
	
	/**
	 * CCValidator::required tests
	 */
	public function test_required()
	{
		$validator = new CCValidator( array( 'username' => 'mario', 'password' => '' ) );
		
		$this->assertTrue( $validator->required( 'username' ) );
		
		$this->assertTrue( $validator->success() );
		$this->assertFalse( $validator->failure() );
		
		$this->assertFalse( $validator->required( 'passord' ) );
		
		$this->assertTrue( $validator->failure() );
		$this->assertFalse( $validator->success() );
		
		// test spaces breaks etc.
		$validator = new CCValidator( array( 'name' => '     ' ) );
		
		$this->assertFalse( $validator->required( 'name' ) );
		
		// test not existing
		$this->assertFalse( $validator->required( 'notexisting' ) );
		
		// test numbers
		$validator = new CCValidator( array( 'count' => '0' ) );
	}
}