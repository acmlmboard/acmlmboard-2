#Creates and populates a table used to show franchise names on sprites.
#Date: 1/27/2014
CREATE TABLE `spritecateg` (
`id` INT NOT NULL,
`name` VARCHAR( 32 ) NOT NULL
) ENGINE = MYISAM ;

INSERT INTO `spritecateg` (`id`, `name`) VALUES ('0', 'Miscellaneous/Unclassified'), ('1', 'Super Mario Brothers Series'), ('2', 'Legend of Zelda Series'), ('3', 'Metroid Series'), ('4', 'Pok&#233;mon Series'), ('5', 'Kirby Series'), ('6', 'Legend of the Evil PLACEHOLDER'), ('7', 'Sonic the Hedgehog Series');
