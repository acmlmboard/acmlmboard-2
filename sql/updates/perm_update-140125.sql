#Adds new permissions reflecting adjustments to edit permissons
#Date: 1/25/2014
INSERT INTO `perm` (`id` , `title` , `description` , `permcat_id` , `permbind_id`)VALUES ('edit-own-permissions', 'Edit Own Permissions', '', '3', '');
INSERT INTO `perm` (`id`, `title`, `description`, `permcat_id`, `permbind_id`) VALUES ('edit-groups', 'Edit Groups', '', '3', '');

#1/28/2012
INSERT INTO `perm` (`id`, `title`, `description`, `permcat_id`, `permbind_id`) VALUES ('assign-secondary-groups', 'Assign Secondary Groups', '', '3', '');
INSERT INTO `perm` (`id`, `title`, `description`, `permcat_id`, `permbind_id`) VALUES ('update-extended-profiles', 'Update Extended Profiles', '', '3', '');
INSERT INTO `perm` (`id`, `title`, `description`, `permcat_id`, `permbind_id`) VALUES ('update-own-extended-profile', 'Update Own Extended Profile', '', '1', '');

#1/29/2014
INSERT INTO `perm` (`id`, `title`, `description`, `permcat_id`, `permbind_id`) VALUES ('bypass-lockdown', 'View Board Under Lockdown', '', '3', '');

#2/2/2014
UPDATE `perm` SET `id` = 'view-favorites', `title` = 'View Favorite Threads' WHERE `perm`.`id` = 'view-calendar';
INSERT INTO `x_perm` (`id`, `x_id`, `x_type`, `perm_id`, `permbind_id`, `bindvalue`, `revoke`) VALUES (LAST_INSERT_ID(), 2, 'group', 'view-favorites', '', 0, 0);
