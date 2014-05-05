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
	 * CCModel::$types
	 */
	public function test_types() 
	{
		$this->assertEquals( array(
			'age' 			=> 'int',
			'library_id' 	=> 'int',
		), CCUnit\Model_DBPerson::_model( 'types' ) );
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
		
		$this->assertEquals( 'something', CCUnit\Model_DBPerson_Pkey::_model( 'primary_key' ) );
	}
	
	/**
	 * CCModel::$_handler
	 */
	public function test_handler() 
	{
		// assigned by defaults
		$this->assertEquals( null, CCUnit\Model_DBPerson::_model( 'handler' ) );
		
		$this->assertEquals( 'other', CCUnit\Model_DBPerson_Handler::_model( 'handler' ) );
	}
	
	/**
	 * CCModel::$_find_mofifier
	 */
	public function test_find_modifier() 
	{
		// assigned by defaults
		$this->assertEquals( null, CCUnit\Model_DBPerson::_model( 'find_modifier' ) );
		
		$this->assertInternalType( 'array', CCUnit\Model_DBPerson_FindModifier::_model( 'find_modifier' ) );
	}
	
	/**
	 * CCModel::$_timestamps
	 */
	public function test_timestamps() 
	{
		// assigned by defaults
		$this->assertEquals( false, CCUnit\Model_DBPerson::_model( 'timestamps' ) );
		
		$this->assertEquals( true, CCUnit\Model_DBPerson_Timestamps::_model( 'timestamps' ) );
	}
	
	/**
	 * CCModel::select
	 */
	public function test_select() 
	{
		// assigned by defaults
		$this->assertTrue( CCUnit\Model_DBPerson::select() instanceof \DB\Query_Select );
		
		$this->assertTrue( CCUnit\Model_DBPerson::select() instanceof \DB\Query_Select );
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
		
		$this->after_save_find_test( $model );
	}
	
	/**
	 * CCModel::find after save
	 */
	public function after_save_find_test( $person ) 
	{
		$model = CCUnit\Model_DBPerson::find( $person->id );
		
		foreach( $person->raw() as $key => $value )
		{
			$this->assertEquals( $value, $model->{$key} );
		}
	}
	
	/**
	 * CCModel::find
	 */
	public function test_find() 
	{
		// find by primary key
		$model = CCUnit\Model_DBPerson::assign( array(
			'name'			=> 'peter_fox',
			'age'			=> '30',
			'library_id' 	=> 0,
		))->save();
		
		$this->assertEquals( $model->name, CCUnit\Model_DBPerson::find( $model->id )->name );
		
		// find by diffrent key
		$this->assertEquals( $model->id, CCUnit\Model_DBPerson::find( 'name', $model->name )->id );
		
		// find with callback
		$person = CCUnit\Model_DBPerson::find( function($q) 
		{
			$q->where( 'name', 'peter_fox' );
			$q->where( 'age', 30 );
			$q->limit( 1 );
		});
		
		$this->assertEquals( $model->id, $person->id );
		
		// find no limit
		$person = CCUnit\Model_DBPerson::find( function($q) 
		{
			$q->where( 'name', 'peter_fox' );
			$q->where( 'age', 30 );
		});
		
		$this->assertEquals( $model->id, reset( $person )->id );
		
		// find all
		$people = CCUnit\Model_DBPerson::find();
		
		$this->assertTrue( reset( $people ) instanceof CCUnit\Model_DBPerson );

		// did we got the primary key
		foreach( $people as $id => $person )
		{
			$this->assertEquals( $id, $person->id );
		}
	}
	
}