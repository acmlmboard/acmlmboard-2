#Adds ICQ, Instagram, Nintendo ID, and Tumblr, and fixes regexp issues with 3DS, DS, Wii, and Wii System Friend Codes.
#Date: 5/6/2015

UPDATE `profileext` SET `fmt` = '$1-$2-$3',
`validation` = '([0-9]{4})-?([0-9]{4})-?([0-9]{4})' WHERE `profileext`.`id` = '3ds';
UPDATE `profileext` SET `fmt` = '$1-$2-$3',
`validation` = '([0-9]{4})-?([0-9]{4})-?([0-9]{4})' WHERE `profileext`.`id` = 'ds';
UPDATE `profileext` SET `fmt` = '$1-$2-$3',
`validation` = '([0-9]{4})-?([0-9]{4})-?([0-9]{4})' WHERE `profileext`.`id` = 'wii';
UPDATE `profileext` SET `fmt` = '$1-$2-$3',
`validation` = '([0-9]{4})-?([0-9]{4})-?([0-9]{4})' WHERE `profileext`.`id` = 'wii-system';

REPLACE INTO `profileext` ( `id` , `title` , `sortorder` , `fmt` , `description` , `icon` , `validation` , `example` , `extrafield` , `parser` ) 
VALUES (
'icq', 'ICQ number', 0, '<a href="http://wwp.icq.com/$0#pager">$0 <img src="http://wwp.icq.com/scripts/online.dll?icq=$0&amp;img=5" border=0></a>', 'Your ICQ Number', '', '[0-9]+', '91235781', 0, ''
);
REPLACE INTO `profileext` ( `id` , `title` , `sortorder` , `fmt` , `description` , `icon` , `validation` , `example` , `extrafield` , `parser` ) 
VALUES (
'nintendoid', 'Nintendo ID', 0, '<a href="https://miiverse.nintendo.net/users/$0">$0</a>', 'Your Nintendo ID', '', '[_\\-0-9a-zA-Z]+', 'mariobros.', 0, ''
);

INSERT INTO `profileext` ( `id` , `title` , `sortorder` , `fmt` , `description` , `icon` , `validation` , `example` , `extrafield` , `parser` )
VALUES (
'tumblr', 'Tumblr', '0', '<a href=http://$0.tumblr.com/>$0</a>', 'Your Tumblr username (as it appears on a URL)', '', '[_\\-0-9a-zA-Z]+', 'supermariosunshinebeta', '0', ''
);
INSERT INTO `profileext` ( `id` , `title` , `sortorder` , `fmt` , `description` , `icon` , `validation` , `example` , `extrafield` , `parser`
)
VALUES (
'instagram', 'Instagram', '0', '<a href=http://instagram.com/$0/>$0</a>', 'Your Instagram username (as it appears on a URL)', '', '[_\\.-0-9a-zA-Z]+', 'soviet.russia', '0', ''
);
