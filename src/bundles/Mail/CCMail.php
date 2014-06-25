<?php namespace Mail;
/**
 * CCMail 
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 */
class CCMail 
{	
	/**
	 * Create a new Mail instance
	 *
	 * @param string 			$transporter
	 * @return CCMail
	 */
	public static function create( $transporter = null, $config = null ) 
	{
		return new static( $transporter, $config );
	}
	
	/**
	 * The mail transporter
	 *
	 * @var Mail\Transporter
	 */
	protected $transporter = null;
	
	/*
	 * is this an plaintext mail
	 */ 
	protected $is_plaintext = false;
	
	/*
	 * send email to
	 */
	protected $to = array();
	
	/*
	 * send email bcc
	 */
	protected $bcc = array();
	
	/*
	 * send email bcc
	 */
	protected $ccc = array();
	
	/*
	 * email from
	 */
	protected $from = array();
	
	/*
	 * email from
	 */
	protected $attachments = array();
	
	/*
	 * subject 
	 */ 
	protected $subject = "";
	
	/* 
	 * content
	 */
	protected $message = "";
	protected $plaintext = "";
	
	/*
	 * the theme
	 */
	protected $layout = null;
	
	/**
	 * Mail constructor
	 *
	 * @param string			$transporter
	 * @return void
	 */
	public function __construct( $transporter = null, $config = null ) 
	{
		$this->transporter = Transporter::create( $transporter, $config );
	}
	
	/**
	 * add mail recipients
	 *
	 * @param array 		$mail
	 */
	public function to( $mail, $name = null ) {
		
		if ( !is_array( $mail ) ) {
			$mail = array( $mail => $name );
		}
		
		$this->to = array_merge( $this->to, $mail );
		
		return $this;
	}
	
	/**
	 * set email from
	 *
	 * @param string 		$mail
	 * @param string 		$name
	 */
	public function from( $mail, $name = null ) {
		$this->from = array( 'mail' => $mail, 'name' => $name );
		return $this;
	}
	
	/**
	 * set email bcc
	 *
	 * @param string 		$mail
	 * @param string 		$name
	 */
	public function bcc( $mail ) {
		$this->bcc[] = $mail;
		return $this;
	}
	
	/**
	 * set subject
	 *
	 * @param string 	$subject
	 */
	public function subject( $subject ) {
		$this->subject = $subject;
		return $this;
	}
	
	/**
	 * render the mail
	 */
	public function render() {
		
		$message = $this->message;
		
		// if the message is a view
		if ( $message instanceof CCView ) {
			
			// first recipient
			reset( $this->to );
			$message->to_mail = key($this->to);
			$message->to_name = $this->to[ key($this->to) ];
			
			$message->render();
		}
		
		if ( $this->theme ) {
			$this->theme->content = $message;
			$this->theme->mail = $this;
			
			// first recipient
			reset( $this->to );
			$this->theme->to_mail = key($this->to);
			$this->theme->to_name = $this->to[ key($this->to) ];
			
			$message = $this->theme->render();
		}
		
		return $message;
	}
	
	/**
	 * Prepare the message for sending to the transport
	 *
	 * @return void 
	 */
	public function send()
	{
		// load the mail configuration
		$config = \CCConfig::create( 'mail' );
		
		// when mailing is disabled do nothing just return
		if ( $config->disabled === true )
		{
			return;
		}
		
		// we cannot send a mail without recipients
		if ( empty( $this->to ) )
		{
			throw new Exception( "Cannot send mail without recipients." );
		}
		
		// is a catch all enabled?
		if ( $config->get( 'catch_all.enabled' ) === true )
		{
			// to be able to modify the mail without removing
			// the user options we have to clone the mail
			$mail = clone $this;
			
			// we have to remove all recipients ( to, ccc, bcc ) and set them
			// to our catch all recipients
			$mail->to = array();
			$mail->ccc = array();
			$mail->bcc = array();
			
			$mail->to( $config->get( 'catch_all.addresses' ) );
			
			// transport the cloned mail
			return $mail->transport( $config->get( 'catch_all.transporter' ) );
		}
		
		// transport the mail
		$this->transport();
	}
	
	/**
	 * Transport the message
	 *
	 * @param string 		$transport 		Use a diffrent transporter
	 * @return void
	 */
	protected function transport( $transporter = null )
	{
		if ( !is_null( $transporter ) )
		{
			$transporter = Transporter::create( $transporter );
		}
		else
		{
			$transporter = $this->transporter;
		}
		
		// pass the current mail to the transporter
		$transporter->send( $this );
	}
	
	/**
	 * Export the mail data 
	 *
	 * @return array
	 */	
	public function export_data()
	{
		$data = get_object_vars( $this ); unset( $data['transporter'] );return $data;
	}
	
	/**
	 * send that email
	 */
	public function send_old() 
	{
		// fix to 
		if ( !is_array( $this->to ) ) {
			$this->to( $this->to );
		}
		
		// fix from
		if ( !is_array( $this->from ) ) {
			$this->from( $this->from );
		}
		
		// create new phpmailer instance
		$driver = new \PHPMailer\PHPMailer();
		$driver->CharSet = 'utf-8'; 
		
		// SMTP Mode
		if ( static::$config->read( 'driver' ) == 'smtp' ) {
			$driver->IsSMTP(); 
			$driver->Host = static::$config->read( 'smtp.host' );
			
			// smtp auth?
			if ( static::$config->read( 'smtp.auth' ) ) {
				$driver->SMTPAuth = static::$config->read( 'smtp.auth' );
				$driver->Username = static::$config->read( 'smtp.user' );
				$driver->Password = static::$config->read( 'smtp.pass' );
			}
			
			$driver->SMTPSecure = static::$config->read( 'smtp.encryption' );
			$driver->Port = static::$config->read( 'smtp.port' );
		}
		
		$driver->XMailer = "CCEmail CCF (1.0)";
		
		$driver->From = $this->from['mail'];
		$driver->FromName = $this->from['name'];
		
		// replace all to addresses when catch all is enabled
		if ( !static::$config->read( 'catch_all.enabled' ) )
		{
			foreach( $this->to as $mail => $name ) 
			{
				$driver->AddAddress( $mail, $name );
			}
		}
		else {
			foreach( static::$config->read( 'catch_all.addresses' ) as $address )
			{
				$driver->AddAddress( $address );
			}
		}
		
		
		$driver->Subject = static::$config->read( 'subject_prefix' ).$this->subject;
		$driver->Body = $this->render();
		$driver->AltBody = $this->plaintext;
		
		// only add bcc when catch_all is disabled
		if ( !static::$config->read( 'catch_all.enabled' ) )
		{
			foreach( static::$config->read( 'bcc' ) as $address ) 
			{
				$driver->addBCC( $address );
			}
			
			foreach( $this->bcc as $bcc_email ) {
				$driver->addBCC( $bcc_email );
			}
		}
		
		// add attachments
		foreach( $this->attachments as $path => $name )
		{
			$driver->addAttachment( $path, $name );
		}
		
		// plain text email?
		if ( !$this->is_plaintext ) {
			$driver->IsHTML( true );
		}
		
		if( !$driver->Send() ) {
			throw new CCException( "CCEmail - Message faild: ". $driver->ErrorInfo );
		}
	}
}