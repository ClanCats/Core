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
class Form {
	
	/*
	 * input id prefix
	 * by default the id is <id_prefix><formkey><name>
	 */
	protected static $_id_prefix = 'input_';
	private static $_default_instance = null;
	
	/**
	 * get an table instance
	 */
	public static function create( $key = 'defaultform', $action = null, $method = 'post', $attr = array() ) {
		return new static( array_merge( array( 'id' => $key, 'action' => $action, 'method' => $method ), $attr ) );	
	}
	
	/**
	 * to call our defult instance
	 */
	public static function __callStatic( $method, $args ) {
		if ( !is_object( static::$_default_instance ) ) {
			static::$_default_instance = static::create( 'defaultform' );
		}
		
		return call_user_func_array( array( static::$_default_instance, $method ), $args );
	}
	
	/*
	 * input id prefix
	 * by default the id is <id_prefix><name>
	 */
	protected $id_prefix;
	
	/*
	 * Form attribute holder
	 */
	public $attr = array(
		'class' => array( 'form' ),	
	);

	/*
	 * the table header
	 */
	public $buffer = array();
	
	/**
	 * Form constructor
	 *
	 * @param array 	$attr
	 */
	public function __construct( $attr = array() ) {
		$this->attr = array_merge( $this->attr, $attr );
		$this->id_prefix = $this->attr['id'].'_'.static::$_id_prefix;
	}
	
	/**
	 * add something to the form 
	 *
	 * @param string 	$buff 
	 */
	public function add( $buff, $key = null ) {
		if ( !is_null( $key ) ) {
			$this->buffer[$key] = $buff;
		} else {
			$this->buffer[] = $buff;
		}
		
		return $this;
	}
	
	/**
	 * capture content and add it to the form
	 *
	 * @param callback 	$callback
	 */
	public function capture( $callback, $key = null ) {
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
	 * remove something from the buffer by key
	 *
	 * @param string	$key
	 */
	public function remove( $key ) {
		if ( array_key_exists( $key, $this->buffer ) ) {
			unset( $this->buffer[$key] );
		}
		
		return $this;
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

		foreach( $this->buffer as $item ) {
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