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
		
		// test rules syntax
		$this->assertTrue( $validator->rules( 'username', 'required' ) );
		
		// test spaces breaks etc.
		$validator = new CCValidator( array( 'name' => '     ' ) );
		
		$this->assertFalse( $validator->required( 'name' ) );
		
		// test not existing
		$this->assertFalse( $validator->required( 'notexisting' ) );
		
		// test numbers
		$validator = new CCValidator( array( 'count' => '0' ) );
		
		$this->assertTrue( $validator->required( 'count' ) );
		
		$validator = new CCValidator( array( 'count' => 0 ) );
		
		$this->assertTrue( $validator->required( 'count' ) );
	}
	
	/**
	 * CCValidator::email tests
	 */
	public function test_email()
	{
		$validator = new CCValidator( array( 
			'email1' => 'info@example.com',
			'email2' => 'info@example',
			'email3' => 'info@@example.com',
			'email4' => 'info..sdf4323fsd@ex-ample.cm',
			'email5' => '',
		));
		
		$this->assertTrue( $validator->email( 'email1' ) );
		$this->assertFalse( $validator->email( 'email2' ) );
		$this->assertFalse( $validator->email( 'email3' ) );
		$this->assertTrue( $validator->email( 'email4' ) );
		$this->assertFalse( $validator->email( 'email5' ) );
	}
	
	/**
	 * CCValidator::ip tests
	 */
	public function test_ip()
	{
		$validator = new CCValidator( array( 
			'ip1' => '127.0.0.1',
			'ip2' => '127.0000.0.1',
			'ip3' => '127.0.0.1.1',
			'ip4' => '266.0.0.2',
			'ip5' => '255.255.255.255',
		));
		
		$this->assertTrue( $validator->ip( 'ip1' ) );
		$this->assertFalse( $validator->ip( 'ip2' ) );
		$this->assertFalse( $validator->ip( 'ip3' ) );
		$this->assertFalse( $validator->ip( 'ip4' ) );
		$this->assertTrue( $validator->ip( 'ip5' ) );
	}
}