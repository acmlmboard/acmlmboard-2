#Adds permissions for editing users' profile fields, editing smilies, and editing extended profile fields.
#Date: 5/8/2015

INSERT INTO `x_perm` (`id`, `x_id`, `x_type`, `perm_id`, `permbind_id`, `bindvalue`, `revoke`) VALUES (LAST_INSERT_ID(), 4, 'group', 'update-extended-profiles', '', 0, 0);

INSERT INTO `perm` (`id`, `title`, `description`, `permcat_id`, `permbind_id`) VALUES ('edit-smilies', 'Edit Smilies', '', '3', '');
INSERT INTO `x_perm` (`id`, `x_id`, `x_type`, `perm_id`, `permbind_id`, `bindvalue`, `revoke`) VALUES (LAST_INSERT_ID(), 4, 'group', 'edit-smilies', '', 0, 0);

INSERT INTO `perm` (`id`, `title`, `description`, `permcat_id`, `permbind_id`) VALUES ('edit-profileext', 'Edit Extended Profile Fields', '', '3', '');
INSERT INTO `x_perm` (`id`, `x_id`, `x_type`, `perm_id`, `permbind_id`, `bindvalue`, `revoke`) VALUES (LAST_INSERT_ID(), 4, 'group', 'edit-profileext', '', 0, 0);

#Adds a perm for editing spiders.
#Date: 5/9/2015

INSERT INTO `perm` (`id`, `title`, `description`, `permcat_id`, `permbind_id`) VALUES ('edit-spiders', 'Edit Spiders', '', '3', '');
INSERT INTO `x_perm` (`id`, `x_id`, `x_type`, `perm_id`, `permbind_id`, `bindvalue`, `revoke`) VALUES (LAST_INSERT_ID(), 4, 'group', 'edit-spiders', '', 0, 0);