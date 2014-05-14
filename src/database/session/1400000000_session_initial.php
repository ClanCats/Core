<?php 
namespace Migrations;
use DB;
class Session_Initial extends \DB\Migrator_Migration
{
	function up()
	{
		DB::run(
		"CREATE TABLE IF NOT EXISTS `sessions` (
		  `id` char(32) NOT NULL,
		  `client_agent` varchar(255) NOT NULL,
		  `client_ip` varchar(16) NOT NULL,
		  `client_lang` varchar(5) NOT NULL,
		  `client_port` int(11) NOT NULL,
		  `current_lang` varchar(5) NOT NULL,
		  `last_active` int(11) NOT NULL,
		  `content` text NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;
		");
	}
	
	function down()
	{
		DB::run( "DROP table `sessions`" );
	}
}


