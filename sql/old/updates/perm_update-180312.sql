INSERT INTO `perm` (`id`, `title`, `description`, `permcat_id`, `permbind_id`) VALUES ('edit-smilies', 'Update Smilies', '', '3', 'users'), ('edit-thread', 'Edit Own Thread/Poll', '', '2', 'users');
INSERT INTO `x_perm` (`id`, `x_id`, `x_type`, `perm_id`, `permbind_id`, `bindvalue`, `revoke`) VALUES (NULL, 2, 'group', 'edit-thread', '', 0, 0);