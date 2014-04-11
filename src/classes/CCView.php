<?php namespace Core;
/**
 * View
 * rendering html pages 
 ** 
 *
 * @package		ClanCatsFramework
 * @author		Mario Döring <mario@clancats.com>
 * @version		2.0
 * @copyright 	2010 - 2014 ClanCats GmbH
 *
 */
class CCView extends CCDataObject {
	
	/*
	 * global data holder
	 */
	public static $_globals = array();
	
	/**
	 * set a global var
	 */
	public static function share( $key, $value )
	{
		CCArr::set( $key, $value, static::$_globals );
	}
	
	/**
	 * view creator
	 * returns a new view instance
	 *
	 * @param string		$file
	 * @param array 		$data
	 * @param bool		$encode
	 * @return CCView
	 */
	public static function create( $file = null, $data = array(), $encode = false ) 
	{
		return new static( $file, $data, $encode );
	}
	
	/**
	 * check if the view exists
	 *
	 * @param string 	$file
	 * @return bool
	 */
	public static function exists( $file )
	{
		return file_exists( CCPath::get( $file, CCDIR_VIEW, EXT ) ); 
	}
	
	/*
	 * view file
	 */
	protected $_file = null;
	
	/**
	 * View contructor
	 * assign the view file and inital data
	 *
	 * @param string		$file
	 * @param array 		$data
	 * @param bool		$encode
	 * @return CCView
	 */
	public function __construct( $file, $data = array(), $encode = false )
	{
		if ( !is_null( $file ) )
		{
			$this->_file = $file;
		}
		
		if ( !is_array( $data ) )
		{
			$data = array();
		}
		
		foreach( $data as $key => $value )
		{
			$this->set( $key, $value, $encode );
		}
	}
	
	/**
	 * set or get the current file
	 *
	 * @param string		$file
	 * @return string
	 */
	public function file( $file = null )
	{
		if ( !is_null( $file ) )
		{
			return $this->_file = $file;
		}
		
		return $this->_file;
	}
	
	/**
	 * custom setter with encode ability
	 *
	 * @param string 	$key
	 * @param mixed		$value
	 * @param mixed 		$param
	 * @return void
	 */
	public function set( $key, $value, $param = null ) 
	{
		if ( $param === true )
		{
			$value = CCStr::htmlentities( $value );
		}
		
		return CCArr::set( $key, $value, $this->_data );
	}

	/**
	 * just like set but it can
	 * captures all output in a closure and set that.
	 *
	 * @param string			$key
	 * @param callback		$callback
	 * @return void
	 */
	public function capture( $key, $callback ) 
	{
		return $this->set( $key, CCStr::capture( $callback, $this ) );
	}
	
	/**
	 * just like capture but it appends
	 *
	 * @param string			$key
	 * @param callback		$callback
	 * @return void
	 */
	public function capture_append( $key, $callback ) 
	{
		return $this->set( $key, $this->get( $key, '' ).CCStr::capture( $callback, $this ) );
	}
	
	/**
	 * magic to string method
	 * this way a view can be directly printed
	 * 
	 * @return string
	 */
	public function __toString() 
	{
		return $this->render();
	}
	
	/**
	 * render the view
	 *
	 * @param string		$file
	 * @return string
	 */
	public function render( $file = null ) 
	{
		// set new file
		if ( !is_null( $file ) )
		{
			$this->file( $file );
		}
		
		// view is empty?
		if ( is_null( $this->file() ) ) 
		{
			throw new CCException( "CCView::render - cannot render view without view file." );
		}
			
		// extract the view data
		extract( $this->_data );
			
		// extract globals if they exists
		if ( !empty( static::$_globals ) ) 
		{
			extract( static::$_globals, EXTR_PREFIX_SAME, 'global' );
		}
			
		// start capturing output buffer
		ob_start();
		
		// generate the views path
		$path = CCPath::get( $this->file(), CCDIR_VIEW, EXT );
		
		if ( !file_exists( $path ) ) 
		{
			throw new CCException( "CCView::render - could not find view: ".$this->file()." at: {$path}." );
		}
		
		// require the file
		require( $path );
		
		// return the output
		return ob_get_clean();
	}
}