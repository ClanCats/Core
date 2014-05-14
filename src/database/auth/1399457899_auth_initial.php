<?php 
namespace Migrations;
use DB;
class Auth_Initial extends \DB\Migrator_Migration
{
	function up()
	{
		DB::run(
		"CREATE TABLE IF NOT EXISTS `auth_logins` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `restore_id` int(255) NOT NULL,
		  `restore_token` varchar(255) NOT NULL,
		  `last_login` int(11) NOT NULL,
		  `client_agent` varchar(255) NOT NULL,
		  PRIMARY KEY (`id`),
		  KEY `restore_id` (`restore_id`,`restore_token`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
		");
		
		DB::run(
		"CREATE TABLE IF NOT EXISTS `auth_users` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `active` tinyint(1) NOT NULL,
		  `username` varchar(255) NOT NULL,
		  `email` varchar(255) NOT NULL,
		  `password` varchar(255) NOT NULL,
		  `storage` text NOT NULL,
		  `last_login` int(11) NOT NULL,
		  `created_at` int(11) NOT NULL,
		  `modified_at` int(11) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
		");
	}
	
	function down()
	{
		DB::run( "DROP table `auth_logins`" );
		DB::run( "DROP table `auth_users`" );
	}
}


