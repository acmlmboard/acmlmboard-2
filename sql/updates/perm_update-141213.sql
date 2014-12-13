#Adds the manage-shop-items perm and support for logo override bypass
#Date 12/13/2014

INSERT INTO `perm` (`id`, `title`, `description`, `permcat_id`, `permbind_id`) VALUES ('manage-shop-items', 'Manage Shop Items', '', '3', '');
INSERT INTO `perm` (`id`, `title`, `description`, `permcat_id`, `permbind_id`) VALUES
('bypass-logo-override', 'Bypass Logo Overrides', 'Bypasses any board-wide logo locks.', 3, '');