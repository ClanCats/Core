<?php namespace CC\Database;
/**
 * PDO Database driver
 *
 * @package 		ClanCats-Framework
 * @author     		Mario Döring <mariodoering@me.com>
 * @version 		0.4
 * @copyright 		2010 - 2013 ClanCats GmbH 
 *
 */
class Pdo_Driver_CCMysql extends Pdo_CCDriver 
{
	
	/*
	 * the string used for the connection
	 */
	protected $connection_string = 'mysql:host={host};dbname={db}';

	/**
	 * return the connection attributes
	 *
	 * @param array 	$conf
	 * @return array
	 */
	protected function pdo_attributes( $conf ) {
		return array(
			\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES ".$conf['charset']
		);
	}
}