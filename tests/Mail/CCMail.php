<?php
/**
 * CCF Mail Test suite
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 * @group Mail
 * @group Mail_CCMail
 */
class Test_Mail_CCMail extends \PHPUnit_Framework_TestCase
{
	/**
	 * CCMail::create tests
	 */
	public function test_create()
	{
		$mail = CCMail::create();
		
		$this->assertTrue( $mail instanceof CCMail );
		
		$mail = new CCMail;
		
		// another Transporter than default
		$mail = new CCMail( 'alias' );
		
		$mail = new CCMail( 'woops', array( 'driver' => 'array' ) );
	}
	
	/**
	 * CCMail::create tests invalid transporter
	 *
	 * @expectedException Mail\Exception
	 */
	public function test_create_invalid_transporter()
	{
		$mail = CCMail::create( 'wrong' );
		$mail = new CCMail( 'fail' );
	}
	
	/**
	 * CCMail::send tests
	 */
	public function test_send()
	{
		$send_mails_count = count( Mail\Transporter_Array::$store);
		
		$mail = CCMail::create();
		
		$mail->to( 'info@example.com' );
		
		$mail->send();
		
		// count should go up now
		$this->assertEquals( $send_mails_count+1, count( Mail\Transporter_Array::$store ) );
		
		$mail->send();
		
		$this->assertEquals( $send_mails_count+2, count( Mail\Transporter_Array::$store ) );
		
		// disable mailing
		CCConfig::create( 'mail' )->set( 'disabled', true );
		
		$mail->send();
		
		// the count should still be +2
		$this->assertEquals( $send_mails_count+2, count( Mail\Transporter_Array::$store ) );
		
		// enable it again
		CCConfig::create( 'mail' )->set( 'disabled', false );
	}
	
	/**
	 * CCMail::send catch all setting tests
	 */
	public function test_catch_all()
	{
		// configure catch all
		CCConfig::create( 'mail' )->set( 'catch_all.enabled', true );
		
		CCConfig::create( 'mail' )->set( 'catch_all.addresses', array(
			'archive@clancats.com' => 'ClanCats',
			'info@someother.com' => 'Some Other',
		));
		
		CCConfig::create( 'mail' )->set( 'catch_all.transporter', 'alias' );
		
		// create a new mail
		$mail = CCMail::create();
		
		$mail->to( 'info@example.com' );
		$mail->bcc( 'foo@bar.com' );
		
		$mail->send();
		
		// check data
		$mail_data = CCArr::last( Mail\Transporter_Array::$store );
		
		$this->assertEquals( $mail_data['to'], array(
			'archive@clancats.com' => 'ClanCats',
			'info@someother.com' => 'Some Other',
		));
		
		$this->assertEquals( $mail_data['bcc'], array() );
		
		CCConfig::create( 'mail' )->set( 'catch_all.enabled', false );
	}
	
	/**
	 * CCMail::send tests
	 *
	 * @expectedException Mail\Exception
	 */
	public function test_send_no_recipient()
	{
		$mail = CCMail::create();
		$mail->send();
	}
}