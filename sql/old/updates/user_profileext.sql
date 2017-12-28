#Adds the user side table for profileext
#Date: 1/28/2014

CREATE TABLE IF NOT EXISTS `user_profileext` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `field_id` varchar(64) NOT NULL,
  `data` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;