#Merge announcenickprefix table into deletedgroups and group tables. 
#Date 01/03/2015

ALTER TABLE `group` ADD `char` VARCHAR( 1 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL AFTER `title`, ADD `color` VARCHAR( 16 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL AFTER `char`;
UPDATE `group` SET `char` = '~', `color` = 'red' WHERE `group`.`id` = '6';
UPDATE `group` SET `char` = '+', `color` = 'lt_blue' WHERE `group`.`id` = '8';
UPDATE `group` SET `char` = '%', `color` = 'lt_green' WHERE `group`.`id` = '3';
UPDATE `group` SET `char` = '@', `color` = 'orange' WHERE `group`.`id` = '4';
ALTER TABLE `deletedgroups` ADD `char` VARCHAR( 1 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL AFTER `title`, ADD `color` VARCHAR( 16 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL AFTER `char`;