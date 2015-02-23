#Adds a banned field to deletedgroups and group tables.
#Date: 1/10/2015

ALTER TABLE `group` ADD `banned` INT( 2 ) NOT NULL AFTER `default`
UPDATE `group` SET `banned` = '1' WHERE `group`.`id` =9;
ALTER TABLE `deletedgroups` ADD `banned` INT( 2 ) NOT NULL AFTER `default`