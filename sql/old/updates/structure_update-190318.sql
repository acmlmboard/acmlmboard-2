ALTER TABLE `smilies` ADD `id` INT(16) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;
ALTER TABLE `robots` ADD `id` INT(16) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;

# This is an alteration for future-proofing purposes.
ALTER TABLE `profileext` CHANGE `id` `service` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';