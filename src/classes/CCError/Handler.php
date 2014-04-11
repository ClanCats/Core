<?php namespace Core;
/**
 * Base error handler
 * This an basic error handler wich does not more than
 * logging the error and printing out the inspector
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 * 
 */
class CCError_Handler
{
	/**
	 * The current error inspector
	 *
	 * @var CCError_Inspector
	 */
	protected $inspector = null;
	
	/**
	 * instance contrucor
	 *
	 * @param CCError_Inspector		$inspector
	 * @return void
	 */
	public function __construct( CCError_Inspector $inspector )
	{
		$this->inspector = $inspector;
	}
	
	/**
	 * Try to log that something went wrong
	 *
	 * @return void
	 */
	protected function log()
	{
		if ( class_exists( 'CCLog' ) )
		{
			try {
				\CCLog::add( 
					$this->inspector->exception()->getMessage()." - ".
					str_replace( CCROOT, '', $this->inspector->exception()->getFile() ).":".
					$this->inspector->exception()->getLine(), 'exception'
				);
				\CCLog::write();
			} catch( \Exception $e ) {}
		}
	}
	
	/**
	 * trigger the handler
	 *
	 * @return void
	 */
	public function handle()
	{
		$this->log();
		$this->respond();
	}
	
	/**
	 * respond information to the user
	 * 
	 * @return void
	 */
	public function respond()
	{
		echo "<h1>".$this->inspector->message()." <small>( ".$this->inspector->exception_name()." )</small></h1>";
		echo "<pre>".$this->inspector->exception()->getTraceAsString()."</pre>";
	}
}