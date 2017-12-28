#Allows longer badge names 
#Date: 6/2/2015

ALTER TABLE `badges` CHANGE `name` `name` VARCHAR( 48 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';