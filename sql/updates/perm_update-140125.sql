#Adds new permissions reflecting adjustments to edit permissons
#Date: 1/25/2014
INSERT INTO `perm` (`id` , `title` , `description` , `permcat_id` , `permbind_id`)VALUES ('edit-own-permissions', 'Edit Own Permissions', '', '3', '');
INSERT INTO `perm` (`id`, `title`, `description`, `permcat_id`, `permbind_id`) VALUES ('edit-groups', 'Edit Groups', '', '3', '');