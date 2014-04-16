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
	 * CCModel::$_fields
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
	
	/**
	 * CCModel::$_table
	 */
	public function test_table() 
	{
		// assigned by defaults
		$this->assertEquals( 'people', CCUnit\Model_DBPerson::_model( 'table' ) );
		
		// custom assignment
		$this->assertEquals( 'ccunit_dbperson_autotables', CCUnit\Model_DBPerson_AutoTable::_model( 'table' ) );
	}
	
	/**
	 * CCModel::$_primary_key
	 */
	public function test_primary_key() 
	{
		// assigned by defaults
		$this->assertEquals( 
			\ClanCats::$config->get( 'database.default_primary_key', 'id' ), 
			CCUnit\Model_DBPerson::_model( 'primary_key' )
		);
	}
	
	/**
	 * CCModel::$_find_mofifier
	 */
	public function test_find_modifier() 
	{
		// assigned by defaults
		$this->assertEquals( null, CCUnit\Model_DBPerson::_model( 'find_modifier' ) );
	}
	
	/**
	 * CCModel::$_timestamps
	 */
	public function test_timestamps() 
	{
		// assigned by defaults
		$this->assertEquals( false, CCUnit\Model_DBPerson::_model( 'timestamps' ) );
	}
	
	/**
	 * CCModel::save
	 *
	 * @dataProvider people_provider
	 */
	public function test_save( $person ) 
	{
		$model = CCUnit\Model_DBPerson::assign( $person );
		$model->save();
		
		foreach( $person as $key => $value )
		{
			$this->assertEquals( $value, $model->{$key} );
		}
		
		//$this->after_save_find_test( $model );
	}
	
	/**
	 * CCModel::find
	 */
	public function after_save_find_test( $person ) 
	{
		$model = CCUnit\Model_DBPerson::find( $person->id );
		$model->save();
		
		foreach( $person->raw() as $key => $value )
		{
			$this->assertEquals( $value, $model->{$key} );
		}
	}
}