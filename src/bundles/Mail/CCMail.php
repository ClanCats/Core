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
	
	/**
	 * Is this a plaintext email
	 *
	 * @var bool
	 */ 
	protected $is_plaintext = false;
	
	/**
	 * Mail recipients
	 *
	 * @var array
	 */
	protected $to = array();
	
	/**
	 * Blind carbon copies
	 *
	 * @var array
	 */
	protected $bcc = array();
	
	/**
	 * Carbon copies
	 *
	 * @var array
	 */
	protected $cc = array();
	
	/**
	 * From email and name
	 *
	 * @var array[email => name]
	 */
	protected $from = array();
	
	/**
	 * Attachemnts
	 *
	 * @var array
	 */
	protected $attachments = array();
	
	/**
	 * Mail subject
	 *
	 * @var string
	 */ 
	protected $subject = "";
	
	/**
	 * Plaintext message
	 *
	 * @var string
	 */
	protected $plaintext = null;
	
	/** 
	 * HTML Message
	 *
	 * @var string|CCView
	 */
	public $message = "";
	
	/**
	 * Mail layout view
	 *
	 * @var CCView
	 */
	public $layout = null;
	
	/**
	 * Mail constructor
	 *
	 * @param string			$transporter
	 * @return void
	 */
	public function __construct( $transporter = null, $config = null ) 
	{
		// prepare the transporter
		$this->transporter = Transporter::create( $transporter, $config );
		
		// prepare the layout if we have one
		if ( $layout = \CCConfig::create( 'mail' )->layout )
		{
			$this->layout = \CCView::create( $layout );
		}
	}
	
	/**
	 * Add an recipient
	 *
	 *     $mail->to( 'mario@foobar.com' );
	 *     $mail->to( 'some@email.com', 'Jennifer Rostock' )
	 *     $mail->to( array( 'some@emailaddress.com' => 'Han Solo' ) );
	 * 
	 * @param string 		$email
	 * @param string 		$name
	 * @return self
	 */
	public function to( $email, $name = null ) 
	{	
		if ( !is_array( $email ) ) 
		{
			$email = array( $email => $name );
		}
		
		foreach( $email as $address => $name )
		{
			if ( is_numeric( $address ) && is_string( $name ) )
			{
				$this->to[$name] = null;
			}
			else
			{
				$this->to[$address] = $name;
			}
		}
		
		return $this;
	}
	
	/**
	 * Add Blind carbon copies
	 * 
	 * Works like the 'to' function.
	 * 
	 * @param string 		$email
	 * @param string 		$name
	 * @return self
	 */
	public function bcc( $email, $name = null ) 
	{	
		if ( !is_array( $email ) ) 
		{
			$email = array( $email => $name );
		}
		
		foreach( $email as $address => $name )
		{
			if ( is_numeric( $address ) && is_string( $name ) )
			{
				$this->bcc[$name] = null;
			}
			else
			{
				$this->bcc[$address] = $name;
			}
		}
		
		return $this;
	}
	
	/**
	 * Add Carbon copies
	 * 
	 * Works like the 'to' function.
	 * 
	 * @param string 		$email
	 * @param string 		$name
	 * @return self
	 */
	public function cc( $email, $name = null ) 
	{	
		if ( !is_array( $email ) ) 
		{
			$email = array( $email => $name );
		}
		
		foreach( $email as $address => $name )
		{
			if ( is_numeric( $address ) && is_string( $name ) )
			{
				$this->cc[$name] = null;
			}
			else
			{
				$this->cc[$address] = $name;
			}
		}
		
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
	 * set subject
	 *
	 * @param string 	$subject
	 */
	public function subject( $subject ) {
		$this->subject = $subject;
		return $this;
	}
	
	/**
	 * Render the message
	 *
	 * @return string
	 */
	public function render() 
	{	
		$message = $this->message;
		
		reset( $this->to );
		
		// default view parameters for the message and the layout
		$params = array(
			'mail' => $this,
			'to_email' => key($this->to),
			'to_name' => $this->to[ key($this->to) ],
		);
		
		// if the message is a view
		if ( $message instanceof \CCView ) 
		{	
			$message->_data = $message->_data + $params;
			$message = $message->render();
		}
		
		// prepare the layout
		if ( $this->layout ) 
		{
			$this->layout->content = $message;
			$this->layout->_data = $this->layout->_data + $params;
			
			$message = $this->layout->render();
		}
		
		// return the renderd message
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
			$mail->cc = array();
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
		$data = get_object_vars( $this ); 
		
		// render the message
		$data['message'] = $this->render();
		
		unset( $data['transporter'] );
		
		return $data;
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