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
		
		// do we have a layout
		$this->assertTrue( $mail->layout instanceof CCView );
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
		$mail->message = "foo my message";
		
		$mail->send();
		
		// check message
		$mail_data = CCArr::last( Mail\Transporter_Array::$store );
		
		$this->assertEquals( 'CCMail:foo my message', $mail_data['message'] );
		
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
	
	/**
	 * CCMail::to tests
	 */
	public function test_to()
	{
		$mail = CCMail::create();
		
		$mail->to( 'email2@example.com' );
		$mail->to( 'email1@example.com', 'name1' );
		$mail->to( array( 'email3@example.com', 'email4@example.com' => 'email4' ) );
		
		$mail->send();
		
		$mail_data = CCArr::last( Mail\Transporter_Array::$store );
		
		$this->assertEquals( $mail_data['to'], array(
			'email2@example.com' => null,
			'email1@example.com' => 'name1',
			'email3@example.com' => null,
			'email4@example.com' => 'email4'
		));
	}
	
	/**
	 * CCMail::bcc tests
	 */
	public function test_bcc()
	{
		$mail = CCMail::create();
		
		$mail->to( 'info@example.com' );
		
		$mail->bcc( 'email2@example.com' );
		$mail->bcc( 'email1@example.com', 'name1' );
		$mail->bcc( array( 'email3@example.com', 'email4@example.com' => 'email4' ) );
		
		$mail->send();
		
		$mail_data = CCArr::last( Mail\Transporter_Array::$store );
		
		$this->assertEquals( $mail_data['bcc'], array(
			'email2@example.com' => null,
			'email1@example.com' => 'name1',
			'email3@example.com' => null,
			'email4@example.com' => 'email4'
		));
	}
	
	/**
	 * CCMail::cc tests
	 */
	public function test_cc()
	{
		$mail = CCMail::create();
		
		$mail->to( 'info@example.com' );
		
		$mail->cc( 'email2@example.com' );
		$mail->cc( 'email1@example.com', 'name1' );
		$mail->cc( array( 'email3@example.com', 'email4@example.com' => 'email4' ) );
		
		$mail->send();
		
		$mail_data = CCArr::last( Mail\Transporter_Array::$store );
		
		$this->assertEquals( $mail_data['cc'], array(
			'email2@example.com' => null,
			'email1@example.com' => 'name1',
			'email3@example.com' => null,
			'email4@example.com' => 'email4'
		));
	}
	
	/**
	 * CCMail::from tests
	 */
	public function test_from()
	{
		$mail = CCMail::create();
		
		$mail->to( 'info@example.com' );
		
		$mail->from( 'email1@example.com' );
		
		$mail->send();
		
		$mail_data = CCArr::last( Mail\Transporter_Array::$store );
		
		list( $email, $name ) = $mail_data['from'];
		
		$this->assertEquals( 'email1@example.com', $email );
		$this->assertEquals( null, $name );
		
		// again with name
		$mail->from( 'email1@example.com', 'Foo' );
		
		$mail->send();
		
		$mail_data = CCArr::last( Mail\Transporter_Array::$store );
		
		list( $email, $name ) = $mail_data['from'];
		
		$this->assertEquals( 'email1@example.com', $email );
		$this->assertEquals( 'Foo', $name );
	}
	
	/**
	 * CCMail::cc tests
	 */
	public function test_attachment()
	{
		$mail = CCMail::create();
		
		$mail->to( 'info@example.com' );
		
		$mail->attachment( 'some/image.jpg' );
		$mail->attachment( 'some/other/image.jpg', 'my_image.jpg' );
		$mail->attachment( array( 'file.zip', '/last/file.zip' => 'compressed.zip' ) );
		
		$mail->send();
		
		$mail_data = CCArr::last( Mail\Transporter_Array::$store );
		
		$this->assertEquals( $mail_data['attachments'], array(
			'some/image.jpg' => null,
			'some/other/image.jpg' => 'my_image.jpg',
			'file.zip' => null,
			'/last/file.zip' => 'compressed.zip'
		));
	}
	
	/**
	 * CCMail::subject tests
	 */
	public function test_subject()
	{
		$mail = CCMail::create();
		
		$mail->to( 'info@example.com' );
		
		$mail->subject( 'phpunit subject' );
		
		$mail->send();
		
		$mail_data = CCArr::last( Mail\Transporter_Array::$store );
		
		$this->assertEquals( 'phpunit subject', $mail_data['subject'] );
	}
	
	/**
	 * CCMail::plaintext tests
	 */
	public function test_plaintext()
	{
		$mail = CCMail::create();
		
		$mail->to( 'info@example.com' );
		
		$mail->plaintext( 'some plain message' );
		
		$mail->send();
		
		$mail_data = CCArr::last( Mail\Transporter_Array::$store );
		
		$this->assertEquals( 'some plain message', $mail_data['plaintext'] );
	}
	
	/**
	 * CCMail::message tests
	 */
	public function test_message()
	{
		$mail = CCMail::create();
		
		$mail->to( 'info@example.com' );
		
		$mail->message( 'some message' );
		
		$mail->send();
		
		$mail_data = CCArr::last( Mail\Transporter_Array::$store );
		
		$this->assertEquals( 'CCMail:some message', $mail_data['message'] );
	}
}