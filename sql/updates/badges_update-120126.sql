#Adds unique id table for easier manipulating. Adds new perm for editing system badges, and for assigning badges 
#Date: 1/26/2014

ALTER TABLE `badges` CHANGE `desc` `description` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `user_badges` ADD `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST; 
INSERT INTO `perm` (`id`, `title`, `description`, `permcat_id`, `permbind_id`) VALUES ('edit-user-badges', 'Assign User Badges', '', '3', '');
INSERT INTO `perm` (`id`, `title`, `description`, `permcat_id`, `permbind_id`) VALUES ('edit-badges', 'Edit Badges', '', '3', '');