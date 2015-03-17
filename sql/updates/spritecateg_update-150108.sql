#Changes the ID of the Miscellaneous/Unclassified franchise from 0 to -1 to allow sprite category editing.
#Date 01/08/2015

UPDATE `spritecateg` SET `id` = '-1' WHERE `spritecateg`.`id` =0 AND `spritecateg`.`name` = 'Miscellaneous/Unclassified' LIMIT 1 ;