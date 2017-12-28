#Renames color to irc_color in the groups table and renames Edit Title to Edit Own Title.
#Date: 4/22/2015


ALTER TABLE `group` CHANGE  `color`  `irc_color` VARCHAR( 16 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ;

ALTER TABLE `deletedgroups` CHANGE `color` `irc_color` VARCHAR( 16 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ;

UPDATE `perm` SET  `id` =  'edit-own-title',
`title` =  'Edit Own Title' WHERE  `perm`.`id` =  'edit-title';