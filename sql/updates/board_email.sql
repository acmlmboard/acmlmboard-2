#Creates a table for the email address that is shown to users who cannot register due to the proxy protection.
#Date: 12/14/2014
CREATE TABLE `board_email` (
`field` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
`emailaddress` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
) ENGINE = MYISAM ;

INSERT INTO `board_email` (`field`, `emailaddress`) VALUES ('boardemail', '0');