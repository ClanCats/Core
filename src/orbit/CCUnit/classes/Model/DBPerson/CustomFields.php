<?php namespace CCUnit;
/**
 * CCUnit Ship
 *
 * @package       CCUnit
 * @author        Mario Döring <mario@clancats.com>
 * @version       1.0.0
 * @copyright     2010 - 2014 ClanCats GmbH
 */
class Model_DBPerson_CustomFields extends \DB\Model
{
	/**
	 * Custom DB fields
	 */
	protected static $_fields = '*';
	
	/*
	 * Defaults
	 */
	protected static $_defaults = array(
		'id'	,
		'name'			=> '',
		'age'			=> 0,
		'library_id'	
	);
}
