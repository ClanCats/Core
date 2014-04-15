<?php
/**
 * Database tests
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 * @group Database
 * @group Database_Model
 */
class Test_Database_Model extends \DB\TestCase
{
	/**
	 * CCModel::$defaults
	 */
	public function test_defaults() 
	{
		$this->assertEquals( array(
			'id' 			=> null,
			'name'			=> '',
			'age'			=> 0,
			'library_id'		=> null,
		), CCUnit\Model_DBPerson::_model( 'defaults' ) );
	}
	
	/**
	 * CCModel::$fields
	 */
	public function test_fields() 
	{
		// assigned by defaults
		$this->assertEquals( array( 
			'id', 'name'	, 'age', 'library_id' 
		), CCUnit\Model_DBPerson::_model( 'fields' ) );
		
		// custom assignment
		$this->assertEquals( '*', CCUnit\Model_DBPerson_CustomFields::_model( 'fields' ) );
	}
}