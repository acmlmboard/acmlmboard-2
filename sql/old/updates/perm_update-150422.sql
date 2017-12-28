#Adds various permissions that needed to be assigned.
#Date: 4/22/2015

INSERT INTO `perm` (`id`, `title`, `description`, `permcat_id`, `permbind_id`) VALUES
('manage-board', 'Administration Management Panel', '', 3, '');
INSERT INTO `x_perm` (`id`, `x_id`, `x_type`, `perm_id`, `permbind_id`, `bindvalue`, `revoke`) VALUES (LAST_INSERT_ID(), 4, 'group', 'manage-board', '', 0, 0);
INSERT INTO `x_perm` (`id`, `x_id`, `x_type`, `perm_id`, `permbind_id`, `bindvalue`, `revoke`) VALUES (LAST_INSERT_ID(), 2, 'group', 'show-online', '', 0, 0);
INSERT INTO `x_perm` (`id`, `x_id`, `x_type`, `perm_id`, `permbind_id`, `bindvalue`, `revoke`) VALUES (LAST_INSERT_ID(), 4, 'group', 'admin-tools-access', '', 0, 0);

INSERT INTO `perm` (`id`, `title`, `description`, `permcat_id`, `permbind_id`) VALUES ('edit-post-icons', 'Edit Post Icons', '', '3', '');
INSERT INTO `x_perm` (`id`, `x_id`, `x_type`, `perm_id`, `permbind_id`, `bindvalue`, `revoke`) VALUES (LAST_INSERT_ID(), 4, 'group', 'edit-calendar-events', '', 0, 0);
INSERT INTO `x_perm` (`id`, `x_id`, `x_type`, `perm_id`, `permbind_id`, `bindvalue`, `revoke`) VALUES (LAST_INSERT_ID(), 4, 'group', 'edit-post-icons', '', 0, 0);
INSERT INTO `x_perm` (`id`, `x_id`, `x_type`, `perm_id`, `permbind_id`, `bindvalue`, `revoke`) VALUES (LAST_INSERT_ID(), 4, 'group', 'manage-shop-items', '', 0, 0);