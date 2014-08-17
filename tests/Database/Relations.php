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
class Test_Database_Relations extends \DB\Test_Case_Database
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
	 * DB\Model::has_one with
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
	 * DB\Model::has_one with
	 */
	public function test_with_belongs_to_and_has_many_and_other_with_relation_stuff() 
	{
		$library_fantasy = CCUnit\Model_Library::assign( array(
			'name'			=> 'Fantasy',
		))->save();
		
		$library_scifi = CCUnit\Model_Library::assign( array(
			'name'			=> 'Sci-Fi',
		))->save();
		
		// create some fantasy books for testing
		$books = array(
		 	array( 'name' => 'Die Stadt der Träumenden Bücher', 'pages' => 425, 'library_id' => $library_fantasy->id ),
		 	array( 'name' => 'Das Labyrinth der Träumenden Bücher', 'pages' => 380, 'library_id' => $library_fantasy->id ),
		 	array( 'name' => 'Rumo', 'pages' => 469, 'library_id' => $library_fantasy->id ),
		);
		
		$books = CCUnit\Model_Book::assign( $books );
		
		// save them
		foreach( $books as $book )
		{
			$book->save();
		}
		
		// create some sci fi books for testing
		$books = array(
		 	array( 'name' => 'Dune', 'pages' => 341, 'library_id' => $library_scifi->id ),
		 	array( 'name' => 'The last generation', 'pages' => 480, 'library_id' => $library_scifi->id ),
		);
		
		$books = CCUnit\Model_Book::assign( $books );
		
		// save them
		foreach( $books as $book )
		{
			$book->save();
		}
		
		$query_count = count( \DB::query_log() );
		
		$books = CCUnit\Model_Book::with( array( 'library' ) );
		
		// check if only 3 queries where executed
		$this->assertEquals( $query_count+2, count( \DB::query_log() ) );
		
		// check 
		foreach( $books as $book )
		{
			$this->assertTrue( $book->library instanceof CCUnit\Model_Library );
		}
		
		// now lets test if the has many works together with with
		$libraries = CCUnit\Model_Library::with( 'books' );
		
		foreach( $libraries as $library )
		{
			if ( is_array( $library->books ) && count( $library->books ) >= 1 )
			{
				$this->assertTrue( reset( $library->books ) instanceof CCUnit\Model_Book );
			}
		}
		
		// well what happens if we expecto only one result
		$library = CCUnit\Model_Library::with( 'books', function( $q ) use( $library_fantasy ) 
		{
			$q->limit(1);
			$q->where( 'id', $library_fantasy->id );	
		});
		
		$this->assertTrue( $library instanceof CCUnit\Model_Library );
		
		foreach( $library->books as $book )
		{
			$this->assertTrue( $book instanceof CCUnit\Model_Book );
		}
		
		// im not even sure why this stuff works...
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