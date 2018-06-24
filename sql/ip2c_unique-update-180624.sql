
ALTER TABLE `ip2c`
 ADD UNIQUE KEY `ip_from` (`ip_from`,`ip_to`), ADD UNIQUE KEY `ip_from_2` (`ip_from`,`ip_to`);

