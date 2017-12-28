-- phpMyAdmin SQL Dump
-- version 3.3.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 12, 2012 at 09:40 PM
-- Server version: 5.1.54
-- PHP Version: 5.3.5-1ubuntu7.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `acmlmboard25`
--

-- --------------------------------------------------------

--
-- Table structure for table `badgecateg`
--

DROP TABLE IF EXISTS `badgecateg`;
CREATE TABLE `badgecateg` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `order` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(20) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `badgecateg`
--

INSERT INTO `badgecateg` (`id`, `order`, `name`, `description`) VALUES
(1, 1, 'Basic Badge', 'This is a decorative badge assignable only by staff.'),
(2, 2, 'Shop Badge', 'This badge can be purchased in the Badge Shop'),
(3, 3, 'Achievement Badge', 'This badge can only be earned. This badge is automatically assigned by the board.');

-- --------------------------------------------------------

--
-- Table structure for table `badges`
--

DROP TABLE IF EXISTS `badges`;
CREATE TABLE `badges` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `image` varchar(48) NOT NULL,
  `priority` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `name` varchar(30) NOT NULL DEFAULT '',
  `desc` varchar(100) NOT NULL DEFAULT '',
  `inherit` int(11) DEFAULT NULL,
  `posttext` varchar(10) DEFAULT NULL,
  `effect` varchar(64) DEFAULT NULL,
  `coins` mediumint(8) DEFAULT NULL,
  `coins2` mediumint(8) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `priority` (`priority`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `badges`
--

INSERT INTO `badges` (`id`, `image`, `priority`, `type`, `name`, `desc`, `inherit`, `posttext`, `effect`, `coins`, `coins2`) VALUES
(1, 'img/badges/pmbadge.png', 1, 1, 'P! Badge', 'P! Power badge. This is given by Emuz to show thanks.', NULL, NULL, NULL, NULL, NULL),
(2, 'img/badges/glasses.png', 1, 1, 'X-Ray Resistance Glasses', 'Ahh hardened for X-Rays? I bet it''s to see those HTML comments..', NULL, NULL, 'show-html-comments', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_badges`
--

DROP TABLE IF EXISTS `user_badges`;
CREATE TABLE `user_badges` (
  `user_id` int(11) NOT NULL,
  `badge_id` int(11) NOT NULL,
  KEY `u` (`user_id`),
  KEY `t` (`badge_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;