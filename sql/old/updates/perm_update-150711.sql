#Adds permissions for editing ranks.
#Date: 7/11/2015

INSERT INTO `perm` (`id`, `title`, `description`, `permcat_id`, `permbind_id`) VALUES ('edit-ranks', 'Edit Ranks', '', '3', '');
INSERT INTO `x_perm` (`id`, `x_id`, `x_type`, `perm_id`, `permbind_id`, `bindvalue`, `revoke`) VALUES (LAST_INSERT_ID(), 4, 'group', 'edit-ranks', '', 0, 0);