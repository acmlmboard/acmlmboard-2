#Adds a field for event titles on the event table.
#Date: 2/17/2014
ALTER TABLE `events` ADD `event_title` VARCHAR( 32 ) NOT NULL AFTER `private`;
