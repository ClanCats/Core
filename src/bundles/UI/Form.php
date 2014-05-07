<?php namespace UI;
/**
 * Uiify Form Generator
 *
 * @package 		Uiify
 * @author     	Mario Döring <mario@clancats.com>
 * @version 		0.1
 * @copyright 	2013 ClanCats GmbH
 *
 */
class Form 
{	
	/**
	 * Current form object
	 *
	 * @var UI\Form
	 */
	private static $current = null;
	
	/**
	 * Open a new form
	 * This will set the current form to this one
	 * 
	 * @param string			$key
	 * @param array 			$attr
	 * @return string
	 */
	public static function start( $key, $attr = array() )
	{
		$form = static::create( $key, $attr );
		return '<form'.HTML::attr( $form->attr ).'>';
	}
	
	/**
	 * Closes the form and resest the current form
	 * 
	 * @param string			$key
	 * @param array 			$attr
	 * @return string
	 */
	public static function end()
	{
		static::$current = null; return "</form>";
	}
	
	/**
	 * Create a new from instance
	 *
	 * @param string			$key			The form key used for identification.
	 * @param array 			$attr		The form dom attributes.
	 * @param callback		$callback	
	 * @return UI\Form
	 */
	public static function create( $key, $attr = array(), $callback = null ) 
	{	
		$form = new static( $attr );
		
		if ( !is_null( $callback ) )
		{
			return $form->capture( $callback );
		}
		
		return $form;	
	}
	
	/**
	 * Forward intance functions to static using the current instance
	 *
	 * @param string 		$method
	 * @param array 			$args
	 * @return mixed
	 */
	public static function __callStatic( $method, $args ) 
	{
		if ( is_null( static::$current ) ) 
		{
			static::$current = static::create( 'ui' );
		}
		
		return call_user_func_array( array( static::$current, $method ), $args );
	}
	
	/**
	 * Form attribute holder
	 *
	 * @var array
	 */
	public $attr = array();
	
	/**
	 * Form constructor
	 *
	 * @param array 	$attr
	 */
	public function __construct( $attr = array() ) 
	{
		$this->attr = array_merge( array(
			'role' => 'form',
			'id' => $this->_get_id( 'form' , $key ),
		), $attr );
		
		// set this instance as the current unsed form
		static::$current = $this;
	}
	
	/**
	 * Capture data from callback and return the output
	 *
	 * @param callback 		$callback
	 * @return string
	 */
	public function capture( $callback, $key = null ) 
	{
		ob_start();
		call_user_func( $callback, $this );
		if ( !is_null( $key ) ) {
			$this->buffer[$key] = ob_get_clean();
		} else {
			$this->buffer[] = ob_get_clean();
		}
		
		return $this;
	}
	
	/**
	 * Format an id by configartion
	 *
	 * @param string 		$type 	element, form etc..
	 * @param strgin			$name
	 * @return string
	 */
	protected function _get_id( $type, $name )
	{
		return sprintf( Builder::$config->get( 'form.'.$type.'_id_format' ), $name );
	}
	
	/**
	 * Format an id by configartion with the current form prefix
	 *
	 * @param string 		$type 	element, form etc..
	 * @param strgin			$name
	 * @return string
	 */
	protected function _build_id( $type, $name )
	{
		return $this->attr['id'].'-'.$this->_get_id( $type, $name );
	}

	/**
	 * magic to string
	 */
	public function __toString() {
		return $this->render();
	}

	/**
	 * generate the output
	 */
	public function render() {

		$buffer = '<form'.HTML::attr( $this->attr ).'>';

		foreach( $this->buffer as $item ) 
		{
			$buffer .= $item;
		}

		return $buffer.'</form>';
	}
	
	/**
	 * damn calling conflicts this is my fix
	 */
	public function __call( $method, $args ){
		return call_user_func_array( array( $this, '_'.$method ), $args );
	}
	
	/**
	 * generate an input
	 *
	 * @param string 	$key | This is the name 
	 * @param string	$type
	 * @param array 	$attr
	 */
	public function _button( $type, $text, $attr = array() ) {
		return HTML::tag('button', $text, array_merge( array( 'id' => $this->id_prefix.$type.'_button', 'type' => $type ), $attr ) );
	}
	
	/**
	 * generate an input
	 *
	 * @param string 	$key | This is the name 
	 * @param string	$type
	 * @param array 	$attr
	 */
	public function _label( $key, $text, $attr = array() ) {
		return HTML::tag('label', $text, array_merge( array( 'id' => $this->id_prefix.$key.'_label', 'for' => $this->id_prefix.$key ), $attr ));
	}
	
	/**
	 * generate an input
	 *
	 * @param string 	$key | This is the name 
	 * @param string	$type
	 * @param array 	$attr
	 */
	public function _input( $key, $type = 'text', $attr = array() ) {
		return HTML::tag('input', array_merge( array( 'id' => $this->id_prefix.$key, 'name' => $key, 'type' => $type ), $attr ));
	}
	
	/**
	 * generate a textarea
	 *
	 * @param string 	$key | This is the name 
	 * @param string	$value
	 * @param array 	$attr
	 */
	public function _textarea( $key, $value = '', $attr = array() ) {
		return HTML::tag('textarea', $value, array_merge( array( 'id' => $this->id_prefix.$key, 'name' => $key ), $attr ));
	}
	
	/**
	 * generate an select
	 *
	 * @param string 	$key | This is the name 
	 * @param string	$type
	 * @param array 	$attr
	 */
	public function _select( $key, $data, $selected = array(), $size = 1, $attr = array() ) {
		
		if ( !is_array( $selected ) ) {
			$selected = array( $selected );
		}
		
		return HTML::tag( 'select', function() use( $data, $selected ){
			foreach( $data as $key => $item ) {
				if ( in_array( $key, $selected ) ) {
					echo HTML::tag( 'option', $item, array( 'value' => $key, 'selected' => 'selected' ) );
				} else {
					echo HTML::tag( 'option', $item, array( 'value' => $key ) );
				}
			}
		}, array_merge( array( 'id' => $this->id_prefix.$key, 'name' => $key, 'size' => $size ), $attr ));
	}
}