#Adds fields for enabling/disabling AB1.x number and bar graphics, and the posting toolbar.
#Date: 02/17/2015

ALTER TABLE `users` ADD `numbargfx` INT( 11 ) NOT NULL DEFAULT '0' AFTER `showlevelbar` ,
ADD `posttoolbar` INT( 11 ) NOT NULL DEFAULT '0' AFTER `numbargfx`;