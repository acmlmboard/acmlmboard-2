#Adds perms to allow access to the admin tools, for editing the show online feature in user profiles, and for using the Deleted Posts Tracker. 
#Date 01/03/2015

INSERT INTO `perm` (`id`, `title`, `description`, `permcat_id`, `permbind_id`) VALUES ('admin-tools-access', 'Access to Admin Tools', '', '3', '');
INSERT INTO `perm` (`id`, `title`, `description`, `permcat_id`, `permbind_id`) VALUES ('deleted-posts-tracker', 'Can Use Deleted Posts Tracker', '', '2', '');
INSERT INTO `perm` (`id`, `title`, `description`, `permcat_id`, `permbind_id`) VALUES ('track-deleted-posts', 'Can Track All Deleted Posts', '', '2', '');
INSERT INTO `perm` (`id`, `title`, `description`, `permcat_id`, `permbind_id`) VALUES ('edit-user-show-online', 'Edit User Show Online', '', '3', '');
INSERT INTO `x_perm` (`id`, `x_id`, `x_type`, `perm_id`, `permbind_id`, `bindvalue`, `revoke`) VALUES (LAST_INSERT_ID(), 2, 'group', 'deleted-posts-tracker', '', 0, 0);
INSERT INTO `x_perm` (`id`, `x_id`, `x_type`, `perm_id`, `permbind_id`, `bindvalue`, `revoke`) VALUES (LAST_INSERT_ID(), 10, 'group', 'track-deleted-posts', '', 0, 0);