<?php
/**
 * CCF Arr Test suite
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 * @group Core
 * @group CCModel
 */
class Test_CCModel extends \PHPUnit_Framework_TestCase
{
	/**
	 * people data
	 */
	public static function people_provider() 
	{
		return array(
			array(
				array( 'name' => 'Mario', 'age' => 20, 'library_id' => 1 ),
			),
			array(
				array( 'name' => 'Ladina', 'library_id' => 2, 'age' => 20 ),
			),
			array(
				array( 'name' => 'Johanna', 'age' => -18, 'library_id' => 1 ),
			),
			array(
				array( 'age' => 22, 'library_id' => null,  'name' => 'Jenny' ),
			),
		);
	}

	/**
	 * people data bulk
	 */
	public static function people_provider_bulk() 
	{
		return array(
			array(
				array(
					array( 'name' => 'Mario', 'age' => 20, 'library_id' => 1 ),
					array( 'name' => 'Ladina', 'age' => 20, 'library_id' => 2 ),
					array( 'name' => 'Johanna', 'age' => 18, 'library_id' => 1 ),
					array( 'name' => 'Jenny', 'age' => 22, 'library_id' => 0 ),
					array( 'name' => 'Melanie', 'age' => 19, 'library_id' => 0 ),
					array( 'name' => 'Tarek', 'age' => 20, 'library_id' => 3 ),
					array( 'name' => 'John', 'age' => 42, 'library_id' => 4 ),
				),
			),
		);
	}

	/**
	 * CCModel::$defaults
	 */
	public function test_defaults( ) 
	{
		$this->assertEquals( array(
			'name'			=> '',
			'age'			=> 0,
			'library_id'		=> null,
		), CCUnit\Model_Person::_model( 'defaults' ) );
	}

	/**
	 * CCModel::create, construction and assign
	 *
	 * @dataProvider people_provider
	 */
	public function test_assign( $person ) 
	{
		$person_create = CCUnit\Model_Person::create( $person );
		$person_assign = CCUnit\Model_Person::assign( $person );
		$person_construct = new CCUnit\Model_Person( $person );

		$this->assertEquals( $person, $person_create->raw() );
		$this->assertEquals( $person, $person_construct->raw() );
		$this->assertEquals( $person, $person_assign->raw() );
	}

	/**
	 * CCModel::assing
	 *
	 * @dataProvider people_provider_bulk
	 */
	public function test_bulk_assign( $people ) 
	{
		$people_assigned = CCUnit\Model_Person::assign( $people );

		foreach( $people_assigned as $key => $person )
		{
			$this->assertEquals( $people[$key], $person->raw() );
		}
	}

	/**
	 * CCModel::__get
	 *
	 * @dataProvider people_provider
	 */
	public function test_get( $person ) 
	{
		$person_model = CCUnit\Model_Person::create( $person );

		$this->assertEquals( $person['name'], $person_model->name );
		$this->assertTrue( $person_model->age >= 18 );
		$this->assertEquals( $person_model->name.' '.$person_model->age, $person_model->line );
	}

	/**
	 * CCModel::__get by reference
	 *
	 * @dataProvider people_provider
	 */
	public function test_get_reference( $person ) 
	{
		$person_model = CCUnit\Model_Person::create( $person );

		$person_model->array = array();

		$person_model->array[] = 'foo';
		$person_model->array[] = 'Bar';
		
		$this->assertEquals( array( 'foo', 'Bar' ), $person_model->array );
		
		$person_model->a = 1;
		
		$person_model->a++;
		$person_model->a++;
		
		$this->assertEquals( 3, $person_model->a );
	}

	/**
	 * CCModel::__set
	 *
	 * @dataProvider people_provider
	 */
	public function test_set( $person ) 
	{
		$person_model = CCUnit\Model_Person::create( $person );

		$person_model->age = mt_rand( -20, 20 );

		$this->assertTrue( $person_model->age >= 18 );
		$this->assertTrue( CCArr::get( 'age', $person_model->raw() ) >= 18 );
	}

	/**
	 * CCModel::__isset
	 *
	 * @dataProvider people_provider
	 */
	public function test_isset( $person ) 
	{
		$person_model = CCUnit\Model_Person::create( $person );

		$this->assertTrue( isset( $person_model->age ) );
		$this->assertTrue( isset( $person_model->line ) );
	}
}