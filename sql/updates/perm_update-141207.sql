#Adds support for theme override bypass
#Date 12/7/2014

INSERT INTO `perm` (`id`, `title`, `description`, `permcat_id`, `permbind_id`) VALUES
('bypass-theme-override', 'Bypass Theme Overrides', 'Bypasses any board-wide theme locks.', 3, '');
