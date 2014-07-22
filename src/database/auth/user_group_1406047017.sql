# ---> up

ALTER TABLE `auth_users` ADD `group_id` INT NOT NULL AFTER  `active`, 
ADD INDEX (  `group_id` ) ;

INSERT INTO `auth_groups` (`id`, `name`, `created_at`, `modified_at`) VALUES ('100', 'Admins', '', '');


# ---> down

ALTER TABLE `auth_users` DROP `group_id`;

DELETE FROM `auth_groups` WHERE `auth_groups`.`id` = 100";