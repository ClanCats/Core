<?php namespace CC\Core;
/**
 * ClanCats Email class
 *
 * @package 		ClanCats-Framework
 * @author     		Mario Döring <mariodoering@me.com>
 * @version 		0.4
 * @copyright 		2010 - 2013 ClanCats GmbH 
 *
 */
class CCEmail {
	
	/*
	 * mail configuration
	 */
	public static $config = null;
	
	/**
	 * static initialisation
	 */
	public static function _init() {
		
	}
	
	/*
	 * send email to
	 */
	protected $to = array();
	
	/*
	 * email from
	 */
	protected $from = '';
	
}