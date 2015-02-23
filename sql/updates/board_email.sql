#Adds a column for the email address that is shown to users who cannot register due to the proxy protection.
#Date: 12/15/2014
ALTER TABLE `misc` ADD `emailaddress` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0' AFTER `txtval`;
INSERT INTO `misc` (`field`, `emailaddress`) VALUES ('boardemail', '0');