#Adds a field for enabling/disabling EXP level bars.
#Date: 02/02/2015

ALTER TABLE `users` ADD `showlevelbar` INT( 11 ) NOT NULL DEFAULT '0' AFTER `emailhide`;