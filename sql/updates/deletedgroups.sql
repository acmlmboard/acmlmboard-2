#Adds a table for storing “deleting” groups to prevent permanent deletion of groups through the board
#Date: 12/22/2014
CREATE TABLE `b6_15432751_squidempress`.`deletedgroups` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
`title` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`nc0` VARCHAR( 6 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`nc1` VARCHAR( 6 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`nc2` VARCHAR( 6 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`inherit_group_id` INT( 11 ) NOT NULL ,
`default` INT( 2 ) NOT NULL ,
`sortorder` INT( 11 ) NOT NULL DEFAULT '0' ,
`visible` INT( 1 ) NOT NULL DEFAULT '0' ,
`primary` INT( 1 ) NOT NULL DEFAULT '0' ,
`description` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ,
PRIMARY KEY ( `id` )
) ENGINE = MYISAM ;

