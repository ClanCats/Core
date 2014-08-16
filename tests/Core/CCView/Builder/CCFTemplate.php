<?php
/**
 * CCF View tests
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 * @group Core
 * @group CCView_Builder_View
 */
class CCView_Builder_CCFTemplate_Test extends \PHPUnit_Framework_TestCase
{
	public function compile( $template )
	{
		$compiler = new Core\CCView_Builder_CCFTemplate( $template );
		return $compiler->compile();
	}

	/**
	 * tests Builder echo 
	 */
	public function test_echo()
	{
		$this->assertEquals( '<?php echo $foo; ?>', $this->compile( '{{$foo}}' ) );
		$this->assertEquals( '<?php echo $count + 1; ?>', $this->compile( '{{$count + 1}}' ) );

		$this->assertEquals( 
			'<?php echo $name; ?> of <?php echo $other; ?>', 
			$this->compile( '{{$name}} of {{$other}}' 
		) );

		// functions
		$this->assertEquals( 
			'<?php echo _e( $input ); ?>', 
			$this->compile( '{{_e( $input )}}' 
		) );

		// if
		$this->assertEquals( 
			'<?php echo empty($items) ? \'There are no items.\' : count($items).\' items found.\'; ?>', 
			$this->compile( '{{empty($items) ? \'There are no items.\' : count($items).\' items found.\'}}' ) 
		);


	}

	/**
	 * tests Builder if
	 */
	public function test_if()
	{
		// simple if correction
		$this->assertEquals( 
			'<?php if ( $foo > 0 ) : ?>', 
			$this->compile( '{% if $foo > 0 %}' ) 
		);

		// endif
		$this->assertEquals( 
			'<?php endif; ?>', 
			$this->compile( '{% endif %}' ) 
		);

		// endif with semi
		$this->assertEquals( 
			'<?php endif; ?>', 
			$this->compile( '{% endif; %}' ) 
		);

		// else
		$this->assertEquals( 
			'<?php else : ?>', 
			$this->compile( '{% else %}' ) 
		);

		// else with opening
		$this->assertEquals( 
			'<?php else : ?>', 
			$this->compile( '{% else : %}' ) 
		);

		// brackets already there
		$this->assertEquals( 
			'<?php if ( $foo > 0 ) : ?>', 
			$this->compile( '{% if ( $foo > 0 ) %}' ) 
		);

		// opening already tehere
		$this->assertEquals( 
			'<?php if ( $foo > 0 ) : ?>', 
			$this->compile( '{% if $foo > 0 : %}' ) 
		);

		// both already there
		$this->assertEquals( 
			'<?php if ( $foo > 0 ) : ?>', 
			$this->compile( '{% if ( $foo > 0 ) : %}' ) 
		);

		// more levels
		$this->assertEquals( 
			'<?php if ( $foo > ( 5 + 5 ) ) : ?>', 
			$this->compile( '{% if $foo > ( 5 + 5 ) %}' ) 
		);

		// more levels
		$this->assertEquals( 
			'<?php elseif ( $foo > ( 5 + 5 ) ) : ?>', 
			$this->compile( '{% elseif $foo > ( 5 + 5 ) %}' ) 
		);
	}

	/**
	 * tests Builder loops
	 */
	public function test_loops()
	{	
		// foreach
		$this->assertEquals( 
			'<?php foreach ( $users as $user ) : ?>', 
			$this->compile( '{% foreach $users as $user %}' ) 
		);

		// end foreach
		$this->assertEquals( 
			'<?php endforeach; ?>', 
			$this->compile( '{% endforeach %}' ) 
		);

		// each
		$this->assertEquals( 
			'<?php foreach ( $users as $user ) : ?>', 
			$this->compile( '{% each $users as $user %}' ) 
		);

		// end foreach
		$this->assertEquals( 
			'<?php endforeach; ?>', 
			$this->compile( '{% endeach %}' ) 
		);

		// for
		$this->assertEquals( 
			'<?php for ( $i=0;$i<10;$i++ ) : ?>', 
			$this->compile( '{% for $i=0;$i<10;$i++ %}' ) 
		);

		// end for
		$this->assertEquals( 
			'<?php endfor; ?>', 
			$this->compile( '{% endfor %}' ) 
		);

		// loop
		$this->assertEquals( 
			'<?php for ( $i=0;$i<10;$i++ ) : ?>', 
			$this->compile( '{% loop 10 %}' ) 
		);

		// end loop
		$this->assertEquals( 
			'<?php endfor; ?>', 
			$this->compile( '{% endloop %}' ) 
		);
	}

	/**
	 * tests Builder switch
	 */
	public function test_switch()
	{
		// switch
		$this->assertEquals( 
			'<?php switch ( $category ) : ?>', 
			$this->compile( '{% switch $category %}' ) 
		);

		// case
		$this->assertEquals( 
			'<?php case "foo": ?>', 
			$this->compile( '{% case "foo": %}' ) 
		);

		// break
		$this->assertEquals( 
			'<?php break; ?>', 
			$this->compile( '{% break %}' ) 
		);

		// endswitch
		$this->assertEquals( 
			'<?php endswitch; ?>', 
			$this->compile( '{% endswitch %}' ) 
		);
	}

	/**
	 * tests Builder switch
	 */
	public function test_array_access()
	{
		// simple
		$this->assertEquals( 
			'<?php echo $foo[\'bar\']; ?>', 
			$this->compile( '{{ $foo.bar }}' ) 
		);

		// multidimension
		$this->assertEquals( 
			'<?php echo $foo[\'bar\'][\'yay\']; ?>', 
			$this->compile( '{{ $foo.bar.yay }}' ) 
		);

		// more multidimension
		$this->assertEquals( 
			'<?php echo $foo[\'bar\'][\'yay\'][\'yes\']; ?>', 
			$this->compile( '{{ $foo.bar.yay.yes }}' ) 
		);

		// objects
		$this->assertEquals( 
			'<?php echo $user->profile[\'name\']; ?>', 
			$this->compile( '{{ $user->profile.name }}' ) 
		);

		// special case
		$this->assertEquals( 
			'<?php echo $page->__("type_".$type); ?>', 
			$this->compile( '{{$page->__("type_".$type)}}' ) 
		);
	}
}