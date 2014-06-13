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
 * @group Database_Relations
 */
class Test_Database_Relations extends \DB\TestCase
{	
	/**
	 * DB\Model::has_one
	 */
	public function test_has_one() 
	{
		// find by primary key
		$library = CCUnit\Model_Library::assign( array(
			'name'			=> 'Sci-Fi',
		))->save();
		
		$person = CCUnit\Model_DBPerson::assign( array(
			'name'			=> 'Mario',
			'age'			=> '20',
			'library_id' 	=> $library->id,
		))->save();
		
		$person_from_libaray = $library->person(); 
		
		$this->assertTrue( $person_from_libaray instanceof DB\Model_Relation_HasOne );
		
		$person_from_libaray = $person_from_libaray->run();
		
		$this->assertTrue( $person_from_libaray instanceof CCUnit\Model_DBPerson );
		
		$this->assertEquals( $person_from_libaray, $person );
		
		// fast selector	
		$this->assertTrue( $library->person instanceof CCUnit\Model_DBPerson );
		
		$query_count = count( \DB::query_log() );
		
		$this->assertEquals( $library->person, $person );
		
		// make sure the query were executed just once
		$this->assertEquals( $query_count, count( \DB::query_log() ) );
	}
	
	/**
	 * DB\Model::has_one
	 */
	public function test_with_has_one() 
	{
		// find by primary key
		$library = CCUnit\Model_Library::assign( array(
			'name'			=> 'Fantasy',
		))->save();
		
		$person = CCUnit\Model_DBPerson::assign( array(
			'name'			=> 'Tarek',
			'age'			=> '20',
			'library_id' 	=> $library->id,
		))->save();
		
		$query_count = count( \DB::query_log() );
		
		$libraries = CCUnit\Model_Library::with( array( 'person', 'person.library' ) );
		
		// check if only 3 queries where executed
		$this->assertEquals( $query_count+3, count( \DB::query_log() ) );
		
		// check if the result is correct
		$this->assertEquals( $library->id, $libraries[$library->id]->id );
		
		$this->assertEquals( $person->id, $libraries[$library->id]->person->id );
		
		$this->assertEquals( $library->id, $libraries[$library->id]->person->library->id );
		
		// there still should not be more quries..
		$this->assertEquals( $query_count+3, count( \DB::query_log() ) );
		
		
		$libraries = CCUnit\Model_Library::with( array( 'person' => function ( $q ) 
		{
			$q->where( 'name', 'in', array( 'Mario', 'John' ) );
		}));
		
		// check the last query
		$this->assertEquals( 'select * from `people` where `library_id` in (1, 2) and `name` in (Mario, John)', \DB::last_query() );
	}
	
	/**
	 * DB\Model::belongs_to
	 */
	public function test_belongs_to() 
	{
		// find by primary key
		$library = CCUnit\Model_Library::assign( array(
			'name'			=> 'Sci-Fi',
		))->save();
		
		$person = CCUnit\Model_DBPerson::assign( array(
			'name'			=> 'Mario',
			'age'			=> '20',
			'library_id' 	=> $library->id,
		))->save();
		
		$library_from_person = $person->library(); 
		
		$this->assertTrue( $library_from_person instanceof DB\Model_Relation_BelongsTo );
		
		$library_from_person = $library_from_person->run();
		
		$this->assertTrue( $library_from_person instanceof CCUnit\Model_Library );
		
		$this->assertEquals( $library_from_person, $library );
	}
	
	/**
	 * DB\Model::has_many
	 */
	public function test_has_many() 
	{
		// find by primary key
		$library = CCUnit\Model_Library::assign( array(
			'name'			=> 'Sci-Fi',
		))->save();
		
		$book = CCUnit\Model_Book::assign( array(
			'name'			=> 'Book #1',
			'pages'			=> 364,
			'library_id' 	=> $library->id,
		))->save();
		
		$book = CCUnit\Model_Book::assign( array(
			'name'			=> 'Book #2',
			'pages'			=> 464,
			'library_id' 	=> $library->id,
		))->save();
		
		$books_from_library = $library->books();
		
		$this->assertTrue( $books_from_library instanceof DB\Model_Relation_HasMany );
		
		$books_from_library = $books_from_library->run();
		
		$this->assertInternalType( 'array', $books_from_library );
		
		$this->assertEquals( 2, count( $books_from_library ) );
	}
	
	
}