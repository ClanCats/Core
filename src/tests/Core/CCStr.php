<?php
/**
 * CCF String Test suite
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 */
class CCStr_Test extends \PHPUnit_Framework_TestCase
{
	/**
	 * test charset getter
	 */
	public function testCharset() 
	{
		// test some returns
		$this->assertEquals( CCStr::charset( 'password' ) , CCStr::SECURE );
		$this->assertEquals( CCStr::charset( 'secure' ) , CCStr::SECURE );
		$this->assertEquals( CCStr::charset( 'pass' ) , CCStr::SECURE );
		
		$this->assertEquals( CCStr::charset( 'hex' ) , CCStr::HEX );
		
		$this->assertEquals( CCStr::charset( 'bin' ) , CCStr::BIN );
		
		$this->assertEquals( CCStr::charset( 'pass' ) , CCStr::SECURE );
		
		$this->assertEquals( CCStr::charset( 'alpha_low' ) , CCStr::ALPHA_LOW );
		$this->assertEquals( CCStr::charset( 'lowercase' ) , CCStr::ALPHA_LOW );
		
		// custom
		$this->assertEquals( CCStr::charset( 'customchaset' ) , 'customchaset' );
	}
	
	/**
	 * test random string
	 */
	public function testRandom() 
	{
		$this->assertEquals( strlen( CCStr::random( 5 ) ), 5 );
		$this->assertEquals( strlen( CCStr::random() ), 25 );
		
		// test random charset
		$random_str = CCStr::random( 10, 'lowercase' );
		
		for( $i=0;$i<strlen( $random_str );$i++ )
		{
			$this->assertTrue( ( strpos( CCStr::ALPHA_LOW, $random_str[$i] ) ) !== false );
		}
	}
	
	/**
	 * test string captureing
	 */
	public function testCapture() 
	{
		// callback
		$this->assertEquals( CCStr::capture( function(){
			echo "Test";
		}), 'Test' );
		
		// normal
		$this->assertEquals(  CCStr::capture( 'Test' ), 'Test' );
		
		// params
		$this->assertEquals( CCStr::capture( function( $q ) {
			echo $q;
		}, 'Test2' ), 'Test2' );
		
		// params array
		$this->assertEquals( CCStr::capture( function( $q ) {
			echo $q;
		}, array( 'Test2' ) ), 'Test2' );
	}
	
	/**
	 * test entities
	 */
	public function testHtmlentities() 
	{
		// normal 
		$this->assertEquals( CCStr::htmlentities( '<test>' ), '&lt;test&gt;' );
		
		// array
		$this->assertEquals( CCStr::htmlentities( array( '<test>', '<test2>' ) ), array( '&lt;test&gt;', '&lt;test2&gt;' ) );
				
		// multi dimensional array
		$this->assertEquals( CCStr::htmlentities( array( 
			'<test>', 
			'test'	=> array(
				'<test3>'
			),
		), true ), array( 
			'&lt;test&gt;',
			'test'	=> array(
				'&lt;test3&gt;'
			),
		));
	}
	
	/**
	 * test suffix
	 */
	public function testSuffix() 
	{
		$this->assertEquals( CCStr::suffix( 'test.php', '.' ), 'php' );
		$this->assertEquals( CCStr::suffix( 'Main::Sub', '::' ), 'Sub' );
		$this->assertEquals( CCStr::suffix( 'ControllerMain', 'Controller' ), 'Main' );
	}
	
	/**
	 * test prefix
	 */
	public function testPrefix() 
	{
		$this->assertEquals( CCStr::prefix( 'test.php', '.' ), 'test' );
		$this->assertEquals( CCStr::prefix( 'Main::Sub', '::' ), 'Main' );
		$this->assertEquals( CCStr::prefix( 'ControllerMain', 'Main' ), 'Controller' );
	}
	
	/**
	 * test extentsion
	 */
	public function testExtension() 
	{
		$this->assertEquals( CCStr::extension( 'test.php' ), 'php' );
		$this->assertEquals( CCStr::extension( 'test.sdf.sdf.md' ), 'md' );
		$this->assertEquals( CCStr::extension( 'test.sdf.......pdf' ), 'pdf' );
	}
	
	/**
	 * test extentsion
	 */
	public function testHash() 
	{
		ClanCats::$config->set( 'security.hash', 'sha1' );
		$this->assertEquals( strlen( CCStr::hash( 'testing around' ) ), 40 );
		
		ClanCats::$config->set( 'security.hash', 'md5' );
		$this->assertEquals( strlen( CCStr::hash( 'testing around' ) ), 32 );
	}
	
	/**
	 * test clean
	 */
	public function testClean() 
	{
		$this->assertEquals( CCStr::clean( '<>Hellö World!</>' ), 'Helloe World' );
		$this->assertEquals( CCStr::clean( '&3mk%çäöü' ), '3mkcaeoeue' );
		$this->assertEquals( CCStr::clean( ' Na       Tes t  ' ), 'Na Tes t' );
		$this->assertEquals( CCStr::clean( "/**\n\t* test clean\n*/" ), 'test clean' );
		$this->assertEquals( CCStr::clean( 'a|"bc!@£de^&$f g' ), 'abcdef g' );
	}
	
	/**
	 * test clean url strings
	 */
	public function testCleanUrl() 
	{
		$this->assertEquals( CCStr::clean_url( '<>Hellö World!</>' ), 'helloe-world' );
		$this->assertEquals( CCStr::clean_url( '-- s-a- -as/&EDö__ $' ), 's-a-as-edoe' );
		$this->assertEquals( CCStr::clean_url( ' - Ich bin nüscht   such: Ideal!' ), 'ich-bin-nuescht-such-ideal' );
		$this->assertEquals( CCStr::clean_url( 'Tom&Jerry' ), 'tom-jerry' );
	}
	
	/**
	 * test string replacements
	 */
	public function testStrReplace() 
	{
		$this->assertEquals( CCStr::replace( 'Hello :name', array( ':name' => 'World' ) ), 'Hello World' );
	}
	
	/**
	 * test string replacements
	 */
	public function testUpper() 
	{
		$this->assertEquals( CCStr::upper( 'Hellö Würld' ), 'HELLÖ WÜRLD' );
	}
	
	/**
	 * test string replacements
	 */
	public function testLower() 
	{
		$this->assertEquals( CCStr::lower( 'HELLÖ WÜRLD' ), 'hellö würld' );
	}
	
	/**
	 * test string replacements
	 */
	public function testReplaceAccents() 
	{
		$this->assertEquals( CCStr::replace_accents( 'HèllÖ Wörld ž' ), 'HellOe Woerld z' );
	}
	
	/**
	 * test string cut
	 */
	public function testCut() 
	{
		$this->assertEquals( CCStr::cut( 'some/of/my/url/?with=param', '?' ), 'some/of/my/url/' );
		$this->assertEquals( CCStr::cut( 'some/of/my/url/?with=param', '/' ), 'some' );
	}
	
	/**
	 * test string strip
	 */
	public function testStrip() 
	{
		$this->assertEquals( CCStr::strip( 'hellotestworld', 'test' ), 'helloworld' );
	}
	
	/**
	 * test string kfloor
	 */
	public function testKFloor() 
	{
		$this->assertEquals( CCStr::kfloor( 956 ), 956 );
		$this->assertEquals( CCStr::kfloor( 1000 ), '1K' );
		$this->assertEquals( CCStr::kfloor( 32951 ), '32K' );
	}
	
	/**
	 * test string bytes
	 */
	public function testBytes() 
	{
		$this->assertEquals( '956b', CCStr::bytes( 956 ) );
		$this->assertEquals( '42.4kb', CCStr::bytes( 43413 ) );
		$this->assertEquals( '423.96kb', CCStr::bytes( 434131 ) );
		$this->assertEquals( '41.4mb', CCStr::bytes( 43413313 ) );
		$this->assertEquals( '4.04gb', CCStr::bytes( 4341311313 ) );
		
		$this->assertEquals( '42kb', CCStr::bytes( 43413, 0 ) );
		$this->assertEquals( '423.956kb', CCStr::bytes( 434131, 3 ) );
		$this->assertEquals( '41.4022mb', CCStr::bytes( 43413313, 4 ) );
		$this->assertEquals( '41.4mb', CCStr::bytes( 43434513, 1 ) );
	}
}