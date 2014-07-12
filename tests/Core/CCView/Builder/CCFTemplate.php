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
		
		// each
		$this->assertEquals( 
			'<?php foreach ( $users as $user ) : ?>', 
			$this->compile( '{% each $users as $user %}' ) 
		);
		
		// for
		$this->assertEquals( 
			'<?php for ( $i=0;$i<10;$i++ ) : ?>', 
			$this->compile( '{% for $i=0;$i<10;$i++ %}' ) 
		);
		
		// for
		$this->assertEquals( 
			'<?php for ( $i=0;$i<10;$i++ ) : ?>', 
			$this->compile( '{% loop 10 %}' ) 
		);
	}
}