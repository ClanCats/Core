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
		$this->assertTrue( $validator->not_required( 'firstname' ) );
		
		$this->assertTrue( $validator->success() );
		$this->assertFalse( $validator->failure() );
		
		$this->assertFalse( $validator->required( 'passord' ) );
		$this->assertFalse( $validator->not_required( 'username' ) );
		
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
		$this->assertFalse( $validator->not_email( 'email1' ) );
		$this->assertFalse( $validator->email( 'email2' ) );
		$this->assertFalse( $validator->email( 'email3' ) );
		$this->assertTrue( $validator->email( 'email4' ) );
		$this->assertFalse( $validator->email( 'email5' ) );
		
		// test rules syntax
		$this->assertTrue( $validator->rules( 'email1', 'required', 'email' ) );
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
		$this->assertFalse( $validator->not_ip( 'ip1' ) );
		$this->assertFalse( $validator->ip( 'ip2' ) );
		$this->assertFalse( $validator->ip( 'ip3' ) );
		$this->assertFalse( $validator->ip( 'ip4' ) );
		$this->assertTrue( $validator->ip( 'ip5' ) );
	}
	
	/**
	 * CCValidator::numeric tests
	 */
	public function test_numeric()
	{
		$validator = new CCValidator( array( 
			'1' => '123',
			'2' => 1242,
			'3' => '123.34',
			'4' => '1nope23',
			'5' => '122,0',
			'6' => '12+2'
		));
		
		$this->assertTrue( $validator->numeric( '1' ) );
		$this->assertFalse( $validator->not_numeric( '1' ) );
		$this->assertTrue( $validator->numeric( '2' ) );
		$this->assertTrue( $validator->numeric( '3' ) );
		$this->assertFalse( $validator->numeric( '4' ) );
		$this->assertFalse( $validator->numeric( '5' ) );
		$this->assertFalse( $validator->numeric( '6' ) );
	}
	
	/**
	 * CCValidator::min_num tests
	 */
	public function test_min_num()
	{
		$validator = new CCValidator( array( 
			'1' => '5',
			'2' => 15,
			'3' => '-5',
			'4' => '3x',
		));
		
		$this->assertTrue( $validator->min_num( '1', 5 ) );
		$this->assertTrue( $validator->min_num( '1', 4 ) );
		$this->assertFalse( $validator->min_num( '1', 6 ) );
		
		$this->assertTrue( $validator->min_num( '2', 14 ) );
		$this->assertFalse( $validator->min_num( '2', 16 ) );
		
		$this->assertTrue( $validator->min_num( '3', -6 ) );
		$this->assertFalse( $validator->min_num( '3', -4 ) );
		
		$this->assertFalse( $validator->min_num( '4', 1 ) );
		
		// test rules syntax
		$this->assertTrue( $validator->rules( '1', 'required', 'min_num:4' ) );
		$this->assertFalse( $validator->rules( '1', 'required', 'min_num:6' ) );
	}
	
	/**
	 * CCValidator::max_num tests
	 */
	public function test_max_num()
	{
		$validator = new CCValidator( array( 
			'1' => '5',
			'2' => 15,
			'3' => '-5',
			'4' => '3x',
		));
		
		$this->assertTrue( $validator->max_num( '1', 5 ) );
		$this->assertTrue( $validator->max_num( '1', 6 ) );
		$this->assertFalse( $validator->max_num( '1', 4 ) );
		
		$this->assertTrue( $validator->max_num( '2', 16 ) );
		$this->assertFalse( $validator->max_num( '2', 14 ) );
		
		$this->assertTrue( $validator->max_num( '3', -4 ) );
		$this->assertFalse( $validator->max_num( '3', -6 ) );
		
		$this->assertFalse( $validator->max_num( '4', 1 ) );
		
		// test rules syntax
		$this->assertTrue( $validator->rules( '1', 'required', 'max_num:6' ) );
		$this->assertFalse( $validator->rules( '1', 'required', 'max_num:4' ) );
	}
	
	/**
	 * CCValidator::between_num tests
	 */
	public function test_between_num()
	{
		$validator = new CCValidator( array( 
			'1' => '5',
			'2' => 15,
			'3' => '-5',
			'4' => '3x',
		));
		
		$this->assertTrue( $validator->between_num( '1', 5, 8 ) );
		$this->assertTrue( $validator->between_num( '1', 3, 10 ) );
		$this->assertFalse( $validator->between_num( '1', 6, 12 ) );
		
		$this->assertTrue( $validator->between_num( '2', 13, 20 ) );
		$this->assertFalse( $validator->between_num( '2', 16, 20 ) );
		
		$this->assertTrue( $validator->between_num( '3', -10, 5 ) );
		$this->assertFalse( $validator->between_num( '3', -20, -10 ) );
		
		$this->assertFalse( $validator->between_num( '4', 1, 5 ) );
		
		// test rules syntax
		$this->assertTrue( $validator->rules( '1', 'required', 'between_num:1,10' ) );
		$this->assertFalse( $validator->rules( '1', 'required', 'between_num:10,100' ) );
	}
	
	/**
	 * CCValidator::min, max and between tests
	 */
	public function test_string_size()
	{
		$validator = new CCValidator( array( 
			'1' => 15,
			'2' => 'foo',
			'3' => '',
		));
		
		$this->assertTrue( $validator->min( '1', 2 ) );
		$this->assertFalse( $validator->min( '1', 6 ) );
		$this->assertTrue( $validator->max( '1', 16 ) );
		$this->assertFalse( $validator->max( '1', 1 ) );
		$this->assertTrue( $validator->between( '1', 0, 2 ) );
		$this->assertFalse( $validator->between( '1', 10, 20 ) );
		
		$this->assertTrue( $validator->between( '2', 2, 6 ) );
		$this->assertFalse( $validator->between( '2', 0, 2 ) );
		
		$this->assertTrue( $validator->between( '3', 0, 16 ) );
		$this->assertFalse( $validator->between( '3', 2, 6 ) );
		
		$this->assertTrue( $validator->rules( '2', 'min:2', 'max:6' ) );
		$this->assertFalse( $validator->rules( '2', 'min:4', 'max:6' ) );
	}
	
	/**
	 * CCValidator::in
	 */
	public function test_in()
	{
		$validator = new CCValidator( array( 
			'salutation' => 'mr.',
			'salutation_2' => 'nope',
		));
		
		$salutations = array( 'mr.', 'mrs.' );
		
		$this->assertTrue( $validator->in( 'salutation', $salutations ) );
		$this->assertFalse( $validator->in( 'salutation_2', $salutations ) );
	}
	
	/**
	 * CCValidator::match
	 */
	public function test_match()
	{
		$validator = new CCValidator( array( 
			'password' => 'test',
			'password_2' => 'test',
			'password_3' => 'wrong',
		));
		
		$this->assertTrue( $validator->match( 'password', 'password_2' ) );
		$this->assertFalse( $validator->match( 'password', 'password_3' ) );
		$this->assertFalse( $validator->match( 'password', 'notexisting' ) );
	}
}