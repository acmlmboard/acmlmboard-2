#Adds a hidesmilies user option, and a nosmilies field to posts and private messages.
#Date: 4/25/2015

ALTER TABLE `users` ADD `hidesmilies` INT( 11 ) NOT NULL DEFAULT '0' AFTER `blocksprites`;