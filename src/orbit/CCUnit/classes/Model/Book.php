<?php namespace CCUnit;
/**
 * CCUnit Ship
 *
 * @package       CCUnit
 * @author        Mario Döring <mario@clancats.com>
 * @version       1.0.0
 * @copyright     2010 - 2014 ClanCats GmbH
 */
class Model_Book extends \DB\Model
{	
	/*
	 * Current Table 
	 */
	protected static $_table = "books";
	
	/*
	 * Automatic timestamps
	 */
	protected static $_timestamps = true;

	/*
	 * Defaults
	 */
	protected static $_defaults = array(
		'id'	,
		'name'			=> '',
		'pages'			=> array( 'json', array() ),
		'created_at'		=> array( 'int' ),
		'modified_at'	=> array( 'int' )
	);
}
