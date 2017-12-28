#Adds an editing calendar events perm and another extended profile fields perm.
#2/14/2015

INSERT INTO `perm` (`id` , `title` , `description` , `permcat_id` , `permbind_id`)VALUES ('edit-calendar-events', 'Edit Calendar Events', '', '3', '');
INSERT INTO `perm` (`id`, `title`, `description`, `permcat_id`, `permbind_id`) VALUES ('update-user-extended-profile', 'Update User Extended Profile', '', '3', 'users');