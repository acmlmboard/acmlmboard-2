ALTER TABLE `ranks` ADD `id` INT(32) NOT NULL AUTO_INCREMENT , ADD PRIMARY KEY (`id`) , ADD UNIQUE (`id`) ; 
ALTER TABLE `ranksets` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `id` (`id`);
ALTER TABLE `ranksets` CHANGE `id` `id` INT(10) NOT NULL AUTO_INCREMENT;
UPDATE `ranksets` SET `id` = '0' WHERE `name` = "None";
INSERT INTO `perm` (`id`, `title`, `description`, `permcat_id`, `permbind_id`) VALUES ('edit-ranks', 'Edit Ranks', '', '3', '');