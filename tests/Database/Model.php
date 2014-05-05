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
	 * DB\Model::$defaults
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
	 * DB\Model::$types
	 */
	public function test_types() 
	{
		$this->assertEquals( array(
			'age' 			=> 'int',
			'library_id' 	=> 'int',
		), CCUnit\Model_DBPerson::_model( 'types' ) );
	}
	
	/**
	 * DB\Model::$_table
	 */
	public function test_table() 
	{
		// assigned by defaults
		$this->assertEquals( 'people', CCUnit\Model_DBPerson::_model( 'table' ) );
		
		// custom assignment
		$this->assertEquals( 'ccunit_dbperson_autotables', CCUnit\Model_DBPerson_AutoTable::_model( 'table' ) );
	}
	
	/**
	 * DB\Model::$_primary_key
	 */
	public function test_primary_key() 
	{
		// assigned by defaults
		$this->assertEquals( 
			\ClanCats::$config->get( 'database.default_primary_key', 'id' ), 
			CCUnit\Model_DBPerson::_model( 'primary_key' )
		);
		
		$this->assertEquals( 'something', CCUnit\Model_DBPerson_PKey::_model( 'primary_key' ) );
	}
	
	/**
	 * DB\Model::$_handler
	 */
	public function test_handler() 
	{
		// assigned by defaults
		$this->assertEquals( null, CCUnit\Model_DBPerson::_model( 'handler' ) );
		
		$this->assertEquals( 'other', CCUnit\Model_DBPerson_Handler::_model( 'handler' ) );
	}
	
	/**
	 * DB\Model::$_find_mofifier
	 *
	 * @expectedException        DB\Exception
	 */
	public function test_find_modifier() 
	{
		// assigned by defaults
		$this->assertEquals( null, CCUnit\Model_DBPerson::_model( 'find_modifier' ) );
		
		$this->assertInternalType( 'array', CCUnit\Model_DBPerson_FindModifier::_model( 'find_modifier' ) );
		
		$orders = null;
		
		$results = CCUnit\Model_DBPerson_FindModifier::find( function( $q ) use( &$orders ) {
			$orders = reset( reset( $q->orders ) );
		});
		
		$this->assertEquals( 'age', $orders );
	
		
		CCUnit\Model_DBPerson_FindModifier::$_static_array["CCUnit\\Model_DBPerson_FindModifier"]['find_modifier'][] = array( 'nope not going to work' );

		CCUnit\Model_DBPerson_FindModifier::find( function( $q ) {});
	}
	
	/**
	 * DB\Model::$_timestamps
	 */
	public function test_timestamps() 
	{
		// assigned by defaults
		$this->assertEquals( false, CCUnit\Model_DBPerson::_model( 'timestamps' ) );
		
		$this->assertEquals( true, CCUnit\Model_DBPerson_Timestamps::_model( 'timestamps' ) );
	}
	
	/**
	 * DB\Model::select
	 */
	public function test_select() 
	{
		// assigned by defaults
		$this->assertTrue( CCUnit\Model_DBPerson::select() instanceof \DB\Query_Select );
		
		$this->assertEquals( 'people', CCUnit\Model_DBPerson::select()->table );
	}
	
	/**
	 * DB\Model::save
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
	 * DB\Model::save book
	 *
	 */
	public function test_save_book( ) 
	{
		// Book test
		$book = new CCUnit\Model_Book;
		
		$book->name = "The Swarm";
		
		$book->pages = array(
			1 => 'Vorwort',
			219 => 'Ende'
		);
		
		$this->assertTrue( $book->save() > 0 );
		
		$this->assertTrue( $book->created_at > 0 );
		$this->assertTrue( $book->modified_at > 0 );
		$this->assertInternalType( 'array', $book->pages );
		
		$book->name = "Der Schwarm";
		
		$id = $book->id;
		
		$book->modified_at -= 1;
		
		$last_modified = $book->modified_at;
		
		$book->save( array( 'name', 'modified_at' ) );
		$book->save( 'name' );
		
		$this->assertTrue( $last_modified !== $book->modified_at );
		
		$this->assertEquals( $id, $book->id );
		
		$this->assertInternalType( 'array', $book->pages );
		
		// get the book again
		$book = CCUnit\Model_Book::find( $id );
		
		$this->assertInternalType( 'array', $book->pages );
	}
	
	/**
	 * DB\Model::find after save
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
	 * DB\Model::find
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
	
	/**
	 * DB\Model::copy
	 */
	public function test_copy() 
	{
		$book = new CCUnit\Model_Book;
		
		$book->name = "The Swarm";
		
		$book->pages = array(
			1 => 'Vorwort',
			219 => 'Ende'
		);
		
		$book->save();
		
		$book_2 = $book->copy();
		
		$book_2->name .= ' 2';
		
		$this->assertTrue( $book_2->id == null );
		
		$book_2->save();
		
		$this->assertFalse( $book->id == $book_2->id );
	}
	
	/**
	 * DB\Model::delete
	 */
	public function test_delete() 
	{
		$book = new CCUnit\Model_Book;
		
		$book->name = "The Swarm";
		
		$book->pages = array(
			1 => 'Vorwort',
			219 => 'Ende'
		);
		
		$book->save();
		
		$this->assertTrue( CCUnit\Model_Book::find( $book->id ) instanceof CCUnit\Model_Book );
		
		$id = $book->id;
		
		$book->delete();
		
		$this->assertFalse( CCUnit\Model_Book::find( $id ) instanceof CCUnit\Model_Book );
	}
}