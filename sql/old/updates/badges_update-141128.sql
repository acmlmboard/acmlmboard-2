/*Structual update to expand the badges table to account for more complex effects.
Note: This is an optional table change. It's recommended you keep the original parameters if it workks for you. */
ALTER TABLE `badges` CHANGE `priority` `priority` MEDIUMINT( 4 ) UNSIGNED NOT NULL DEFAULT '1',
CHANGE `effect_variable` `effect_variable` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
