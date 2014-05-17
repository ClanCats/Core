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
}