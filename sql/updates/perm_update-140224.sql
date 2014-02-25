#Adds new permissions reflecting adjustments to edit permissons
#Date: 2/24/2014
INSERT INTO `perm` (`id`, `title`, `description`, `permcat_id`, `permbind_id`) VALUES ('override-closed-all', 'Post in All Closed Threads', '', '2', '');
INSERT INTO `perm` (`id`, `title`, `description`, `permcat_id`, `permbind_id`) VALUES ('override-closed-forum', 'Post in Closed Threads in Forum', '', '2', 'forums');
INSERT INTO `perm` (`id`, `title`, `description`, `permcat_id`, `permbind_id`) VALUES ('override-closed-thread', 'Post in Closed Thread', '', '2', 'threads');
#Enable for Global Moderator and up for all threads. If you wish to not have this enabled comment out, or remove in editgroups. 
INSERT INTO `x_perm` (`id`, `x_id`, `x_type`, `perm_id`, `permbind_id`, `bindvalue`, `revoke`) VALUES (LAST_INSERT_ID(), 3, 'group', 'override-closed-all', '', 0, 0);
