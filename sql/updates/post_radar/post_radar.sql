-- phpMyAdmin SQL Dump
-- version 2.9.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Mar 07, 2012 at 12:34 PM
-- Server version: 5.0.27
-- PHP Version: 5.2.5
-- 
-- Database: `kafubak_dev`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `post_radar`
-- 

DROP TABLE IF EXISTS `post_radar`;
CREATE TABLE IF NOT EXISTS `post_radar` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` mediumint(8) unsigned NOT NULL,
  `user2_id` mediumint(8) unsigned NOT NULL,
  `ctime` bigint(20) unsigned NOT NULL,
  `dtime` bigint(20) unsigned default NULL,
  UNIQUE KEY `id` (`id`),
  KEY `user_id` (`user_id`,`user2_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
