#Adds a column for the email hide
#Date: 12/12/2014

ALTER TABLE `users` ADD `emailhide` INT( 1 ) NOT NULL DEFAULT '0' AFTER `redirtype`;