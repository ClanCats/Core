<?php
/**
 * CCF Arr Test suite
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2015 ClanCats GmbH
 *
 * @group Core
 * @group CCArr
 */
class Test_CCArr extends \PHPUnit_Framework_TestCase
{
	/*
	 * an testing array
	 */
	public $test_array = array(
		'string'		=> 'bar',
		'true'		=> true,
		'false'		=> true,
		'array'		=> array(
			'number'	=> 13,
			'zero'	=> 0,
			'null'	=> null
		),	
	);
	
	/**
	 * CCAr::first
	 */
	public function test_first() 
	{
		$original_array = $this->test_array;
		
		$this->assertEquals( 'bar', CCArr::first( $this->test_array ) );
		
		$this->assertEquals( $original_array, $this->test_array );
	}
	
	/**
	 * test array last
	 */
	public function testArrayLast() 
	{
		$original_array = $this->test_array;
		
		$this->assertEquals( array(
			'number'	=> 13,
			'zero'	=> 0,
			'null'	=> null
		), CCArr::last( $this->test_array ) );
		
		$this->assertEquals( $original_array, $this->test_array );
	}
	
	/**
	 * test array push
	 */
	public function testArrayPush() 
	{	
		$array = array( 'Foo', 'Bar' );
		
		// push 
		CCArr::push( 'Batz', $array );
		
		// test
		$this->assertEquals( $array[2], 'Batz' );
		
		// push 
		CCArr::push( array( 'Test1', 'Test2' ), $array, true );
		
		// test
		$this->assertEquals( 5, count( $array ) );
	}
	
	
	/**
	 * test array add
	 */
	public function testArrayAdd() 
	{	
		$array = array( 'foo' => array( 'bar' => array( 'test' => 'woo' ) ) );
		
		$array = CCArr::add( 'foo.bar.test', 'jep', $array );
		
		$this->assertEquals( array( 'jep' ), CCArr::get( 'foo.bar.test', $array ) );
		
		$array = CCArr::add( 'foo.bar.test', 'jepp', $array );
		
		$this->assertEquals( array( 'jep', 'jepp' ), CCArr::get( 'foo.bar.test', $array ) );
	}
	
	/**
	 * test array push
	 *
	 * @expectedException        InvalidArgumentException
	 */
	public function testArrayPushException() 
	{
		$not_an_array = null;
		
		// push 
		CCArr::push( 'Batz', $not_an_array );
	}
	
	/**
	 * test values by key
	 */
	public function testArrayPick() 
	{	
		$array = array(
			array(
				'item'		=> 'Foo',
				'another'	=> 'value',
				'data' 		=> array(
					'age'	=> 15,
				)
			),
			array(
				'item'		=> 'Bar',
				'nope'		=> 'test',
				'data' 		=> array(
					'age'	=> 32,
				)
			),
		);
		
		// test
		$this->assertEquals( 
			CCArr::pick( 'item', $array ),
			array( 
				0 => 'Foo', 
				1 => 'Bar' 
			)
		);
		
		// test multi
		$this->assertEquals( 
			CCArr::pick( 'data.age', $array ),
			array( 
				0 => 15, 
				1 => 32 
			)
		);
	}
	
	/**
	 * test values by key
	 *
	 * @expectedException        InvalidArgumentException
	 */
	public function testArrayPickException() 
	{	
		CCArr::pick( 'test' );
	}
	
	/**
	 * test if array has multiple dimensions 
	 */
	public function testArrayIsMulti() 
	{	
		// test
		$this->assertTrue( CCArr::is_multi( array(
			array(
				'name' 	=> 'johnson',
				'age'	=> 20
			),
		)));
		
		$this->assertTrue( CCArr::is_multi( array(
			array(
				'name' 	=> 'johnson',
				'age'	=> 20
			),
			array(
				'name' 	=> 'Jack',
				'age'	=> 25
			),
		)));
		
		$this->assertTrue( CCArr::is_multi( array(
			array(
				'name' 	=> 'johnson',
				'age'	=> 20
			),
			array(
				'name' 	=> 'Jack',
				'age'	=> 25
			),
			'no array valie',
			32
		)));
		
		$this->assertFalse( CCArr::is_multi( array(
			'jack',
			'john',
			'johnson'
		)));
		
		$this->assertFalse( CCArr::is_multi( array(
			'jack' => 12,
			'john' => 24,
			'johnson' => 32
		)));
	}
	
	/**
	 * test if array contains other arrays
	 */
	public function testArrayIsCollection() 
	{	
		$this->assertTrue( CCArr::is_collection( array(
			array(
				'name' 	=> 'johnson',
				'age'	=> 20
			),
		)));
		
		$this->assertTrue( CCArr::is_collection( array(
			array(
				'name' 	=> 'johnson',
				'age'	=> 20
			),
			array(
				'name' 	=> 'Jack',
				'age'	=> 25
			),
		)));
		
		$this->assertFalse( CCArr::is_collection( array(
			'no array valie',
			array(
				'name' 	=> 'johnson',
				'age'	=> 20
			),
			array(
				'name' 	=> 'Jack',
				'age'	=> 25
			),
		)));
		
		$this->assertFalse( CCArr::is_collection( array(
			'jack',
			'john',
			'johnson'
		)));
		
		$this->assertFalse( CCArr::is_collection( array(
			'jack' => 12,
			'john' => 24,
			'johnson' => 32
		)));
	}
	
	/**
	 * test sum array values
	 */
	public function testArraySum() 
	{	
		$array = array(
			array(
				'item'		=> 'Foo',
				'another'	=> 'value',
				'data' 		=> array(
					'age'	=> 15,
				)
			),
			array(
				'item'		=> 'Bar',
				'nope'		=> 'test',
				'data' 		=> array(
					'age'	=> 32,
				)
			),
		);
		
		// test
		$this->assertEquals( 
			CCArr::sum( array( 5, 4, 9 ) ),
			18
		);
		
		// test
		$this->assertEquals( 
			CCArr::sum( array( 5, '4', 9.0 ) ),
			18
		);
		
		// test
		$this->assertEquals( 
			CCArr::sum( $array, 'data.age' ),
			47
		);
	}
	
	/**
	 * test values by key
	 *
	 * @expectedException        InvalidArgumentException
	 */
	public function testArraySumException() 
	{	
		CCArr::sum( 'test' );
	}
	
	/**
	 * test get average of array values
	 */
	public function testArrayAverage() 
	{	
		$array = array(
			array(
				'item'		=> 'Foo',
				'another'	=> 'value',
				'data' 		=> array(
					'age'	=> 15,
				)
			),
			array(
				'item'		=> 'Bar',
				'nope'		=> 'test',
				'data' 		=> array(
					'age'	=> 32,
				)
			),
		);
		
		// test
		$this->assertEquals( 
			CCArr::average( array( 5, 4, 9 ) ),
			6
		);
		
		// test
		$this->assertEquals( 
			CCArr::average( array( 5, '4', 9.0 ) ),
			6
		);
		
		// test
		$this->assertEquals( 
			CCArr::average( $array, 'data.age' ),
			23.5
		);
	}
	
	/**
	 * test values by key
	 *
	 * @expectedException        InvalidArgumentException
	 */
	public function testArrayAverageException() 
	{	
		CCArr::average( 'test' );
	}
	
	/**
	 * create an object of an array
	 */
	public function testArrayToObject() 
	{	
		$object = CCArr::object( $this->test_array );
		
		// test if objet
		$this->assertTrue( is_object( $object ) );
		
		// test objcet
		$this->assertEquals( $object->string, 'bar' );
		
		// test recursion objcet
		$this->assertEquals( $object->array->number, 13 );
	}
	
	/**
	 * test values by key
	 *
	 * @expectedException        InvalidArgumentException
	 */
	public function testArrayObjectException() 
	{	
		CCArr::object( 'test' );
	}
	
	/**
	 * test array merge
	 */
	public function testArrayMerge() 
	{	
		$array1 = array( 
			'foo' 	=> 'Foo', 
			'bar'	=> 'Bar',
			'data' 	=> array(
				'item1'	=> array(
					'key1' => 'value1',
					'key2' => 'value2',
				),
				'key1' => 'value1',
				'key2' => 'value2',
			), 
		);
		
		$array2 = array( 
			'foo' 	=> 'new foo',
			'test',
		);
		
		$array3 = array( 
			'data' 	=> array(
				'item1'	=> array(
					'key2' => 'new value2',
				),
				'key1' => 'new value1',
			),
		);
		
		$array4 = array( 
			'bar' 	=> null,
		);
		
		// the needed result
		$expected_result = array( 
			'foo' 	=> 'new foo',
			'bar' 	=> null,
			'data'	=> array(
				'item1'		=> array(
					'key1'	=> 'value1',
					'key2'	=> 'new value2',
				),
				'key1'	=> 'new value1',
				'key2'	=> 'value2',
			),
			0		=> 'test',
		);
		
		// test
		$this->assertEquals( CCArr::merge( $array1, $array2, $array3, $array4 ), $expected_result );
		
		$languages = array(
			'languages' => array(
				'testign' 	=> 'value',
				'aviable'	=> array(
					'DE' => array(
						'DE',
						'de'
					),
					'EN' => array(
						'EN',
						'en'
					),
				),
			),
		);
		
		$languages_only_de = array(
			'languages' => array(
				'aviable'	=> array(
					'SP' => array(
						'SP',
						'sp'
					),
				),
			),
		);
		
		$this->assertEquals(
			CCArr::merge( $languages, $languages_only_de ),
			array(
				'languages' => array(
					'testign' 	=> 'value',
					'aviable'	=> array(
						'DE' => array(
							'DE',
							'de'
						),
						'SP' => array(
							'SP',
							'sp'
						),
						'EN' => array(
							'EN',
							'en'
						),
					),
				),
			)
		);
	}
	
	/**
	 * test values by key
	 *
	 * @expectedException        InvalidArgumentException
	 */
	public function testArrayMergeException() 
	{	
		CCArr::merge( 'test' );
	}
	
	/**
	 * test the CCArr getter
	 */
	public function testArrayGetItem() {
		
		/*
		 * get string
		 */
		$this->assertEquals( 
			CCArr::get( 'string', $this->test_array ),
			'bar'
		);
		
		/*
		 * get number
		 */
		$this->assertEquals( 
			CCArr::get( 'array.number', $this->test_array ),
			13
		);
		
		/*
		 * get null
		 */
		$this->assertEquals( 
			CCArr::get( 'array.null', $this->test_array ),
			null
		);
		
		/*
		 * get default
		 */
		$this->assertEquals( 
			CCArr::get( 'not.existing', $this->test_array, 'default_value' ),
			'default_value'
		);
	}
	
	/**
	 * test the CCArr setter
	 */
	public function testArraySetItem() {
		
		$test_array = $this->test_array;

		/*
		 * set string
		 */
		CCArr::set( 'string', 'batz', $test_array );
		$this->assertEquals( 
			CCArr::get( 'string', $test_array ),
			'batz'
		);
		
		/*
		 * set number
		 */
		CCArr::set( 'array.number', 0, $test_array );
		$this->assertEquals( 
			CCArr::get( 'array.number', $test_array ),
			0
		);
		
		/*
		 * set new value
		 */
		CCArr::set( 'not.existing.item', 'new value', $test_array );
		$this->assertEquals( 
			CCArr::get( 'not.existing.item', $test_array ),
			'new value'
		);
		
		/*
		 * set value in deep field
		 */
		CCArr::set( 'not.existing.item.in.deep.deep.field', 'deep', $test_array );
		$this->assertEquals( 
			CCArr::get( 'not.existing.item.in.deep.deep.field', $test_array ),
			'deep'
		);
	}
	
	/**
	 * test the CCArr isset
	 */
	public function testArrayHasItem() {
		
		/*
		 * get string
		 */
		$this->assertTrue( CCArr::has( 'string', $this->test_array ) );
		
		/*
		 * get not existing
		 */
		$this->assertFalse( CCArr::has( 'not.existing', $this->test_array ) );
	}
}