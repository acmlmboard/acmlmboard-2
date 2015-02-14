#Various own and everyone permissions.
#2/13/2015

INSERT INTO `perm` (`id`, `title`, `description`, `permcat_id`, `permbind_id`) VALUES ('edit-titles', 'Edit Titles', '', '3', '');
INSERT INTO `x_perm` (`id`, `x_id`, `x_type`, `perm_id`, `permbind_id`, `bindvalue`, `revoke`) VALUES (LAST_INSERT_ID(), 4, 'group', 'edit-titles', '', 0, 0);
INSERT INTO `perm` (`id`, `title`, `description`, `permcat_id`, `permbind_id`) VALUES ('edit-user-title', 'Edit User Title', '', '3', 'users');
INSERT INTO `x_perm` (`id`, `x_id`, `x_type`, `perm_id`, `permbind_id`, `bindvalue`, `revoke`) VALUES (LAST_INSERT_ID(), 2, 'group', 'edit-title', '', 0, 0);

INSERT INTO `perm` (`id`, `title`, `description`, `permcat_id`, `permbind_id`) VALUES ('edit-customusercolors', 'Edit Custom Username Colors', '', '3', '');
INSERT INTO `x_perm` (`id`, `x_id`, `x_type`, `perm_id`, `permbind_id`, `bindvalue`, `revoke`) VALUES (LAST_INSERT_ID(), 4, 'group', 'edit-customusercolors', '', 0, 0);
INSERT INTO `perm` (`id`, `title`, `description`, `permcat_id`, `permbind_id`) VALUES ('edit-user-customnickcolor', 'Edit User Custom Nick Color', '', '3', 'users');
INSERT INTO `x_perm` (`id`, `x_id`, `x_type`, `perm_id`, `permbind_id`, `bindvalue`, `revoke`) VALUES (LAST_INSERT_ID(), 10, 'group', 'has-customusercolor', '', 0, 0);

INSERT INTO `perm` (`id`, `title`, `description`, `permcat_id`, `permbind_id`) VALUES ('edit-displaynames', 'Edit Displaynames', '', '3', '');
INSERT INTO `x_perm` (`id`, `x_id`, `x_type`, `perm_id`, `permbind_id`, `bindvalue`, `revoke`) VALUES (LAST_INSERT_ID(), 4, 'group', 'edit-displaynames', '', 0, 0);
INSERT INTO `perm` (`id`, `title`, `description`, `permcat_id`, `permbind_id`) VALUES ('edit-user-displayname', 'Edit User Displayname', '', '3', 'users');
INSERT INTO `x_perm` (`id`, `x_id`, `x_type`, `perm_id`, `permbind_id`, `bindvalue`, `revoke`) VALUES (LAST_INSERT_ID(), 2, 'group', 'has-displayname', '', 0, 0);