#Reverses the revoke set on displayname's perm for staff.
UPDATE `x_perm` SET `revoke` = '0' WHERE `x_perm`.`id` =214;
