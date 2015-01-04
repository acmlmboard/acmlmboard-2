#Adds perm to allow access to the admin tools and for editing the show online feature in user profiles. 
#Date 01/03/2015

INSERT INTO `perm` (`id`, `title`, `description`, `permcat_id`, `permbind_id`) VALUES ('admin-tools-access', 'Access to Admin Tools', '', '3', '');
INSERT INTO `perm` (`id`, `title`, `description`, `permcat_id`, `permbind_id`) VALUES ('edit-user-show-online', 'Edit User Show Online', '', '3', '');