-- phpMyAdmin SQL Dump
-- version 3.1.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 13, 2011 at 05:03 PM
-- Server version: 5.1.30
-- PHP Version: 5.2.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `kyoufukawa_abtest`
--

-- --------------------------------------------------------

--
-- Table structure for table `acmlmon`
--

CREATE TABLE IF NOT EXISTS `acmlmon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  `franchiseid` int(11) NOT NULL DEFAULT '0',
  `pic` varchar(256) NOT NULL,
  `alt` varchar(256) NOT NULL,
  `anchor` enum('free','left','right','top','bottom','sides','sidepic') NOT NULL,
  `title` varchar(256) NOT NULL,
  `flavor` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `acmlmon`
--

INSERT INTO `acmlmon` (`id`, `name`, `franchiseid`, `pic`, `alt`, `anchor`, `title`, `flavor`) VALUES
(1, 'Dodongo', 2, 'dodongo.png', 'dodongo_left.png', 'free', 'BOMB?', 'Dodongo dislikes smoke.'),
(2, 'Starman', 1, 'starman.png', 'starman.png', 'free', 'Grab it! Grab it!', 'Grab one of these, you''ll feel unstoppable. Also, as if you''re tripping ballz.'),
(3, 'Ika-chan', 0, 'ika.png', 'ika.png', 'free', 'Meep!', 'I got nothing.'),
(4, 'Monkey', 1, 'yi_monkey_right.png', 'yi_monkey_left.png', 'sidepic', 'Ook!', 'Annoying little buggers.'),
(5, 'Goomba', 1, 'goomba_nes.png', 'goomba_nes.png', 'bottom', '', 'Also known as Kuribo, these are the first opponents most video game players of old would think of.'),
(6, 'Goomba', 1, 'goomba3.png', 'goomba3.png', 'bottom', '', 'They looked much better with proper outlines, didn''t they?'),
(7, 'Dalek', 0, 'dalek.png', 'dalek.png', 'free', 'EX-TER-MI-NATE!!!', 'Aim for the eyestalk.');

-- --------------------------------------------------------

--
-- Table structure for table `acmlmon_captures`
--

CREATE TABLE IF NOT EXISTS `acmlmon_captures` (
  `userid` int(11) NOT NULL,
  `monid` int(11) NOT NULL,
  UNIQUE KEY `userid` (`userid`,`monid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


