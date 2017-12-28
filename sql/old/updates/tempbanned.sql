#Adds a field for a tempban.
#Date: 1/18/2014

ALTER TABLE `users` ADD `tempbanned` INT( 12 ) NOT NULL AFTER `pmblocked`