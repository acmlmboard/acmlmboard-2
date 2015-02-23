#Adds perm for per-user nick color
#Date: 1/18/2014
ALTER TABLE `users` ADD `nick_color` VARCHAR( 6 ) NOT NULL AFTER `group_id` ;
INSERT INTO `perm` (`id`, `title`, `description`, `permcat_id`, `permbind_id`) VALUES ('has-customusercolor', 'Can Edit Custom Username Color', '', '3', '');

INSERT INTO `x_perm` (`id`, `x_id`, `x_type`, `perm_id`, `permbind_id`, `bindvalue`, `revoke`) VALUES (LAST_INSERT_ID(), 4, 'group', 'has-customusercolor', '', 0, 0);
