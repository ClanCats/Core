<?php namespace CC\Core;
/**
 * ClanCats Validator
 *
 * @package 		ClanCats-Framework
 * @author     		Mario Döring <mariodoering@me.com>
 * @version 		0.5
 * @copyright 		2010 - 2013 ClanCats GmbH 
 *
 */
class CCValidator {
	
	/**
	 * validator factory
	 *
	 * @param array 	$data
	 * @return CCValidator
	 */
	public static function factory( $data = array() ) {
		return new static( $data );
	}
	
	/*
	 * data holder
	 */
	public $data = null;

	/*
	 * validation success
	 */
	public $success = null;
	
	/**
	 * validator constructor
	 *
	 * @param array 	$data
	 * @return void
	 */
	public function __construct( $data = array() ) {
		$this->data = $data;
	}
	
	/**
	 * get the string from data or param?
	 *
	 * @param string 	$key
	 * @return string
	 */
	protected function data( $key ) {
		if ( is_string( $key ) && substr( $key, 0, 1 ) == '@' ) {
			return $this->data[substr( $key, 1 )];
		}
		return $key;
	}
	
	/**
	 * did the check fail?
	 */
	protected function success( $bool ) {
		
		if ( $this->success === null || $this->success === true ) {
			$this->success = (bool) $bool;
		}
		
		return $bool;
	}
	
	/**
	 * check in our data array if some properties
	 * are set and not empty
	 *
	 * @param string|array 	$data
	 * @return bool 
	 */
	public function required( $data ) {
		
		if ( is_string( $data ) ) {
			$data = array( $data );
		}
		
		foreach( $data as $item ) {
			
			if ( $this->is_empty( '@'.$item ) ) {
				return $this->success( false );
			}
		}
		return $this->success( true );
	}
	
	
	/*
	 ** --- ALL THE IS FUNCTIONS DOWN HERE
	 */
	 
	/**
	 * is an email address?
	 *
	 * @param string 	$email
	 */
	public function is_email( $email ) {
		return $this->is_valid( $email, "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^" );
	}
	
	/**
	 * check if the regex matches
	 * 
	 * @param mixed		$data
	 * @param string	$regex
	 */ 
	public function is_valid( $data, $regex ) {
		return $this->success( preg_match( $regex, $this->data( $data ) ) );
	}
	
	/**
	 * check if the something is not empty
	 */
	public function is_empty( $data ) {
		$data = trim( $this->data( $data ) ); 
		return $this->success( empty( $data ) );
	}
	/**
	 * just a reverse of is_empty
	 */
	public function is_not_empty( $data ) { 
		$data = trim( $this->data( $data ) ); 
		return $this->success( !empty( $data ) );
	}
	
	/**
	 * is a string bigger then
	 *
	 * @param string	$string
	 * @param int		$min
	 */
	public function is_bigger( $string, $min ) {
		if ( strlen( $this->data( $string ) ) > $min ) {
			return $this->success( true );
		}
		return $this->success( false );
	}
	
	/**
	 * is a string bigger then
	 *
	 * @param string	$string
	 * @param int		$min
	 */
	public function is_smaller( $string, $min ) {
		if ( strlen( $this->data( $string ) ) < $min ) {
			return $this->success( true );
		}
		return $this->success( false );
	}
	
	/**
	 * is the string length between
	 *
	 * @param string 	$string
	 * @param int 		$min
	 * @param int 		$max
	 */
	public function is_between( $string, $min, $max ) {
		$len = strlen( $this->data( $string ) );
	
		if ( $len < $min ) {
			return $this->success( false );
		}
		if ( $len > $max ) {
			return $this->success( false );
		}
	
		return $this->success( true );
	}
	
	/**
	 * check if the string is numeric
	 *
	 * @param string	$string
	 */
	public function is_numeric( $string ) {
		return $this->success( is_numeric( $this->data( $string ) ) );
	}
	
	/**
	 * check if an array contains a string
	 *
	 * @param string	$string
	 */
	public function is_in_array( $string, $array ) {
		return $this->success( in_array( $this->data( $string ), $array ) );
	}
	
	/**
	 * reverse of is in array
	 *
	 * @param string	$string
	 */
	public function is_not_in_array( $string, $array ) {
		return $this->success( !in_array( $this->data( $string ), $array ) );
	}
	
	/**
	 * check if something is true
	 *
	 * @param string	$string
	 */
	public function is_true( $string ) {
		return $this->success( ( $this->data( $string ) === true ) ? true : false );
	}
	
	/**
	 * check if something is false
	 *
	 * @param string	$string
	 */
	public function is_false( $string ) {
		return $this->success( ( $this->data( $string ) === false ) ? true : false );
	}
	
	/**
	 * check if something is positive
	 *
	 * @param string	$string
	 */
	public function is_positive( $string ) {
		return $this->success( ( $this->data( $string ) ) ? true : false );
	}
	
	/**
	 * check if something is negative
	 *
	 * @param string	$string
	 */
	public function is_negative( $string ) {
		return $this->success( ( $this->data( $string ) ) ? false : true );
	}
	
	/**
	 * are the parameters equal?
	 *
	 * @param string	$string
	 * @param mixed		$item
	 */
	public function is_equal( $string, $item ) {
		return $this->success( $this->data( $string ) == $item );
	}
	
	/**
	 * are the parameters equal?
	 *
	 * @param string	$string
	 * @param mixed		$item
	 */
	public function is_not_equal( $string, $item ) {
		return $this->success( $this->data( $string ) != $item );
	}
	
	/**
	 * check if valid date format
	 *
	 * @param string	$string
	 * @param string	$format
	 */
	public function is_valid_date( $string, $format = 'd-m-Y' ) {
		$date = strtotime( trim( $this->data( $string ) ) );
		return $this->success( date( $format, $date ) == trim( $this->data( $string ) ) );
	}
}
