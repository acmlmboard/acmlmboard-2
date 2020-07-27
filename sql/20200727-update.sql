ALTER TABLE `threads` CHANGE `tags` `tags` BIGINT(12) NOT NULL;
UPDATE `misc` SET `sqlversion`=20200727;