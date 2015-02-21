#Adds a field for enabling username colors.
#Date: 02/21/2015

ALTER TABLE `users` ADD `enablecolor` INT( 1 ) NOT NULL DEFAULT '0' AFTER `nick_color`;