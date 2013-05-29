-- MySQL dump 10.13  Distrib 5.1.54, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: acmlmboard2
-- ------------------------------------------------------
-- Server version	5.1.54-1ubuntu4

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `annoucenickprefix`
--

DROP TABLE IF EXISTS `annoucenickprefix`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `annoucenickprefix` (
  `group_id` int(16) NOT NULL,
  `char` varchar(1) NOT NULL,
  `color` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `annoucenickprefix`
--

LOCK TABLES `annoucenickprefix` WRITE;
/*!40000 ALTER TABLE `annoucenickprefix` DISABLE KEYS */;
INSERT INTO `annoucenickprefix` VALUES (6,'~','red'),(8,'+','lt_blue'),(3,'%','lt_green'),(4,'@','orange');
/*!40000 ALTER TABLE `annoucenickprefix` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `announcechans`
--

DROP TABLE IF EXISTS `announcechans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `announcechans` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `chan` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `announcechans`
--

LOCK TABLES `announcechans` WRITE;
/*!40000 ALTER TABLE `announcechans` DISABLE KEYS */;
/*!40000 ALTER TABLE `announcechans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `badgecateg`
--

DROP TABLE IF EXISTS `badgecateg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `badgecateg` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `order` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(20) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `badgecateg`
--

LOCK TABLES `badgecateg` WRITE;
/*!40000 ALTER TABLE `badgecateg` DISABLE KEYS */;
INSERT INTO `badgecateg` VALUES (1,1,'Basic Badge','This is a decorative badge assignable only by staff.'),(2,2,'Shop Badge','This badge can be purchased in the Badge Shop'),(3,3,'Achievement Badge','This badge can only be earned. This badge is automatically assigned by the board.');
/*!40000 ALTER TABLE `badgecateg` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `badges`
--

DROP TABLE IF EXISTS `badges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `effect_variable` varchar(32) DEFAULT NULL,
  `coins` mediumint(8) DEFAULT NULL,
  `coins2` mediumint(8) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `priority` (`priority`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `badges`
--

LOCK TABLES `badges` WRITE;
/*!40000 ALTER TABLE `badges` DISABLE KEYS */;
INSERT INTO `badges` VALUES (1,'img/badges/pmbadge.png',100,1,'P! Badge','P! Power badge. This is given by Emuz to show thanks.',NULL,NULL,NULL,NULL,NULL,NULL),(2,'img/badges/glasses.png',50,1,'X-Ray Resistance Glasses','Ahh hardened for X-Rays? I bet it\'s to see those HTML comments..',NULL,NULL,'show-html-comments',NULL,NULL,NULL),(3,'img/badges/quatloo.png',15,1,'Quatloo Challenge Winner!','Given upon completion of some silly challenge of Emuz\'s',NULL,NULL,NULL,NULL,NULL,NULL),(4,'img/badges/1milthview.png',15,1,'Got X,000,000th view','Got X,000,000th board view',NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `badges` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blockedlayouts`
--

DROP TABLE IF EXISTS `blockedlayouts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blockedlayouts` (
  `user` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `blockee` mediumint(8) unsigned NOT NULL DEFAULT '0',
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blockedlayouts`
--

LOCK TABLES `blockedlayouts` WRITE;
/*!40000 ALTER TABLE `blockedlayouts` DISABLE KEYS */;
/*!40000 ALTER TABLE `blockedlayouts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `ord` tinyint(4) NOT NULL,
  `minpower` tinyint(4) NOT NULL,
  `private` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'General Forums',2,0,0),(2,'Staff Forums',0,1,1);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dailystats`
--

DROP TABLE IF EXISTS `dailystats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dailystats` (
  `date` char(8) NOT NULL,
  `users` int(11) DEFAULT '0',
  `threads` int(11) DEFAULT '0',
  `posts` int(11) DEFAULT '0',
  `views` int(11) DEFAULT '0',
  PRIMARY KEY (`date`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dailystats`
--

LOCK TABLES `dailystats` WRITE;
/*!40000 ALTER TABLE `dailystats` DISABLE KEYS */;
/*!40000 ALTER TABLE `dailystats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `month` tinyint(4) NOT NULL,
  `day` tinyint(4) NOT NULL,
  `year` smallint(6) NOT NULL,
  `user` mediumint(9) NOT NULL,
  `private` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forummods`
--

DROP TABLE IF EXISTS `forummods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forummods` (
  `uid` int(12) NOT NULL,
  `fid` int(12) NOT NULL,
  UNIQUE KEY `uid_2` (`uid`,`fid`),
  KEY `uid` (`uid`,`fid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forummods`
--

LOCK TABLES `forummods` WRITE;
/*!40000 ALTER TABLE `forummods` DISABLE KEYS */;
/*!40000 ALTER TABLE `forummods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forums`
--

DROP TABLE IF EXISTS `forums`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forums` (
  `id` int(5) NOT NULL DEFAULT '0',
  `cat` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `ord` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `descr` varchar(255) NOT NULL,
  `threads` mediumint(8) NOT NULL DEFAULT '0',
  `posts` mediumint(8) NOT NULL DEFAULT '0',
  `lastdate` int(11) NOT NULL DEFAULT '0',
  `lastuser` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `lastid` int(11) NOT NULL,
  `minpower` tinyint(4) NOT NULL DEFAULT '-1',
  `minpowerthread` tinyint(4) NOT NULL DEFAULT '0',
  `minpowerreply` tinyint(4) NOT NULL DEFAULT '0',
  `private` int(1) NOT NULL,
  `trash` int(1) NOT NULL,
  `announcechan_id` int(11) NOT NULL DEFAULT '0',
  `readonly` int(1) NOT NULL DEFAULT '0',
  `announce` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forums`
--

LOCK TABLES `forums` WRITE;
/*!40000 ALTER TABLE `forums` DISABLE KEYS */;
INSERT INTO `forums` VALUES (1,1,1,'General Forum','General topics forum',0,0,0,0,0,0,0,0,0,0,2,0,0),(2,2,1,'General Staff Forum','Generic Staff Forum					',0,0,0,0,0,1,1,1,1,0,1,0,0);
/*!40000 ALTER TABLE `forums` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forumsread`
--

DROP TABLE IF EXISTS `forumsread`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forumsread` (
  `uid` mediumint(9) NOT NULL,
  `fid` int(5) NOT NULL,
  `time` int(11) NOT NULL,
  UNIQUE KEY `uid` (`uid`,`fid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forumsread`
--

LOCK TABLES `forumsread` WRITE;
/*!40000 ALTER TABLE `forumsread` DISABLE KEYS */;
/*!40000 ALTER TABLE `forumsread` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `group`
--

DROP TABLE IF EXISTS `group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `nc0` varchar(6) NOT NULL,
  `nc1` varchar(6) NOT NULL,
  `nc2` varchar(6) NOT NULL,
  `inherit_group_id` int(11) NOT NULL,
  `default` int(2) NOT NULL,
  `sortorder` int(11) NOT NULL DEFAULT '0',
  `visible` int(1) NOT NULL DEFAULT '0',
  `primary` int(1) NOT NULL DEFAULT '0',
  `description` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `group`
--

LOCK TABLES `group` WRITE;
/*!40000 ALTER TABLE `group` DISABLE KEYS */;
INSERT INTO `group` VALUES (1,'Base User','','','',0,0,100,0,0,''),(2,'Normal User','97ACEF','F185C9','7C60B0',1,1,200,1,1,'Normal Registered User'),(3,'Global Moderator','AFFABE','C762F2','47B53C',8,0,600,1,1,''),(4,'Administrator','FFEA95','C53A9E','F0C413',3,0,700,1,1,''),(6,'Root Administrator','EE4444','E63282','AA3C3C',0,-1,800,1,1,''),(7,'NotRO Moderator','','','',0,0,500,1,0,'Allows moderation of the NotRO forum'),(8,'Local Moderator','D8E8FE','FFB3F3','EEB9BA',10,0,400,1,1,''),(9,'Banned','888888','888888','888888',2,0,0,1,1,''),(10,'Staff','','','',2,0,300,0,0,''),(11,'Disable PM Activity','','','',0,0,1000,1,0,'Disallows all Private Message activity (viewing, creation, deletion)'),(12,'Moogle Participants','','','',0,0,2000,1,0,'Allows viewing/posting the Moogle forum'),(13,'General Forum Moderation','','','',0,0,450,1,0,'Allows moderation of the General Forum'),(15,'Bot','','','',1,0,50,0,0,'');
/*!40000 ALTER TABLE `group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `guests`
--

DROP TABLE IF EXISTS `guests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `guests` (
  `date` int(11) NOT NULL DEFAULT '0',
  `ip` varchar(15) NOT NULL,
  `url` varchar(255) NOT NULL,
  `ipbanned` tinyint(4) NOT NULL DEFAULT '0',
  `useragent` varchar(255) NOT NULL,
  `bot` int(11) NOT NULL,
  `lastforum` int(10) NOT NULL,
  UNIQUE KEY `ip` (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `guests`
--

LOCK TABLES `guests` WRITE;
/*!40000 ALTER TABLE `guests` DISABLE KEYS */;
/*!40000 ALTER TABLE `guests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hourlyviews`
--

DROP TABLE IF EXISTS `hourlyviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hourlyviews` (
  `hour` mediumint(9) NOT NULL,
  `views` int(11) NOT NULL,
  UNIQUE KEY `hour` (`hour`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hourlyviews`
--

LOCK TABLES `hourlyviews` WRITE;
/*!40000 ALTER TABLE `hourlyviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `hourlyviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ignoredforums`
--

DROP TABLE IF EXISTS `ignoredforums`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ignoredforums` (
  `uid` int(11) NOT NULL,
  `fid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ignoredforums`
--

LOCK TABLES `ignoredforums` WRITE;
/*!40000 ALTER TABLE `ignoredforums` DISABLE KEYS */;
/*!40000 ALTER TABLE `ignoredforums` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ip2c`
--

DROP TABLE IF EXISTS `ip2c`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ip2c` (
  `ip_from` bigint(12) NOT NULL,
  `ip_to` bigint(12) NOT NULL,
  `registrar` varchar(50) NOT NULL,
  `assigned` int(12) NOT NULL,
  `cc2` varchar(2) NOT NULL,
  `cc3` varchar(3) NOT NULL,
  `cname` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ip2c`
--

LOCK TABLES `ip2c` WRITE;
/*!40000 ALTER TABLE `ip2c` DISABLE KEYS */;
/*!40000 ALTER TABLE `ip2c` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ipbans`
--

DROP TABLE IF EXISTS `ipbans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ipbans` (
  `ipmask` varchar(15) NOT NULL,
  `hard` tinyint(1) NOT NULL,
  `expires` int(12) NOT NULL,
  `banner` varchar(25) NOT NULL,
  `reason` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ipbans`
--

LOCK TABLES `ipbans` WRITE;
/*!40000 ALTER TABLE `ipbans` DISABLE KEYS */;
/*!40000 ALTER TABLE `ipbans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `itemcateg`
--

DROP TABLE IF EXISTS `itemcateg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `itemcateg` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `corder` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(20) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `itemcateg`
--

LOCK TABLES `itemcateg` WRITE;
/*!40000 ALTER TABLE `itemcateg` DISABLE KEYS */;
INSERT INTO `itemcateg` VALUES (1,1,'Weapons','boom boom boom'),(2,2,'Armor','Bling! Well, until I think of a better description to put here, or something ...'),(3,3,'Shields','More bling, or something'),(4,4,'Helms','Bling again, but on the head this time'),(5,5,'Boots','Vroom! But without a motor'),(6,6,'Accessories','Notepad, Paint, Calculator, DOS prompt, Wordpad');
/*!40000 ALTER TABLE `itemcateg` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `items` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `cat` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `type` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `name` varchar(30) NOT NULL,
  `desc` varchar(255) NOT NULL DEFAULT 'No description.',
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  `stype` varchar(9) NOT NULL DEFAULT 'mmaaaaaaa',
  `sHP` smallint(5) NOT NULL DEFAULT '100',
  `sMP` smallint(5) NOT NULL DEFAULT '100',
  `sAtk` smallint(5) NOT NULL DEFAULT '0',
  `sDef` smallint(5) NOT NULL DEFAULT '0',
  `sInt` smallint(5) NOT NULL DEFAULT '0',
  `sMDf` smallint(5) NOT NULL DEFAULT '0',
  `sDex` smallint(5) NOT NULL DEFAULT '0',
  `sLck` smallint(5) NOT NULL DEFAULT '0',
  `sSpd` smallint(5) NOT NULL DEFAULT '0',
  `coins` mediumint(8) NOT NULL DEFAULT '0',
  `coins2` mediumint(9) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `cat` (`cat`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `items`
--

LOCK TABLES `items` WRITE;
/*!40000 ALTER TABLE `items` DISABLE KEYS */;
INSERT INTO `items` VALUES (0,0,0,'Nothing','Nothing.  At All.',0,'aaaaaaaaa',0,0,0,0,0,0,0,0,0,0,0);
/*!40000 ALTER TABLE `items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log` (
  `t` int(12) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `uid` int(11) NOT NULL,
  `request` varchar(255) NOT NULL,
  KEY `t` (`t`),
  KEY `ip` (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log`
--

LOCK TABLES `log` WRITE;
/*!40000 ALTER TABLE `log` DISABLE KEYS */;
/*!40000 ALTER TABLE `log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mcache`
--

DROP TABLE IF EXISTS `mcache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mcache` (
  `hash` varchar(32) NOT NULL,
  `file` varchar(32) NOT NULL,
  KEY `hash` (`hash`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mcache`
--

LOCK TABLES `mcache` WRITE;
/*!40000 ALTER TABLE `mcache` DISABLE KEYS */;
/*!40000 ALTER TABLE `mcache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `misc`
--

DROP TABLE IF EXISTS `misc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `misc` (
  `field` varchar(255) NOT NULL,
  `intval` int(11) NOT NULL DEFAULT '0',
  `txtval` text NOT NULL,
  PRIMARY KEY (`field`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `misc`
--

LOCK TABLES `misc` WRITE;
/*!40000 ALTER TABLE `misc` DISABLE KEYS */;
INSERT INTO `misc` VALUES ('views',0,''),('maxpostsday',0,''),('maxpostsdaydate',0,''),('maxpostshour',0,''),('maxpostshourdate',0,''),('maxusers',0,''),('maxusersdate',0,''),('maxuserstext',0,''),('botviews',0,''),('lockdown',0,''),('attention',0,''),('regdisable',0,''),('hacksnews',0,'');
/*!40000 ALTER TABLE `misc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mood`
--

DROP TABLE IF EXISTS `mood`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mood` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `label` varchar(127) NOT NULL DEFAULT '',
  `local` tinyint(1) NOT NULL DEFAULT '0',
  `url` varchar(255) NOT NULL DEFAULT '',
  UNIQUE KEY `id` (`id`,`user`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mood`
--

LOCK TABLES `mood` WRITE;
/*!40000 ALTER TABLE `mood` DISABLE KEYS */;
/*!40000 ALTER TABLE `mood` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `perm`
--

DROP TABLE IF EXISTS `perm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `perm` (
  `id` varchar(64) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `permcat_id` int(11) NOT NULL,
  `permbind_id` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `perm`
--

LOCK TABLES `perm` WRITE;
/*!40000 ALTER TABLE `perm` DISABLE KEYS */;
INSERT INTO `perm` VALUES ('banned','Is Banned','',2,''),('block-layout','Enable Layout Blocking','Enables per-user layout blocking',3,''),('capture-sprites','Capture Sprites','',1,''),('consecutive-posts','Consecutive Posts','',2,''),('create-all-forums-announcement','Create All Forums Announcement','',4,''),('create-all-private-forum-posts','Create All Private Forum Posts','',3,''),('create-all-private-forum-threads','Create All Private Forum Threads','',3,''),('create-forum-announcement','Create Forum Announcement','',3,'forum'),('create-pms','Create PMs','',1,''),('create-private-forum-post','Create Private Forum Post','',2,'forums'),('create-private-forum-thread','Create Private Forum Thread','',2,'forums'),('create-public-post','Create Public Post','',4,''),('create-public-thread','Create Public Thread','',4,''),('delete-forum-post','Delete Forum Post','',2,'forums'),('delete-forum-thread','Delete Forum Thread','',2,'forums'),('delete-own-pms','Delete Own PMs','',1,''),('delete-post','Delete Post','',2,''),('delete-thread','Delete Thread','',2,''),('delete-user-pms','Delete User PMs','',3,''),('edit-attentions-box','Edit Attentions Box','',3,''),('edit-categories','Edit Categories','',3,''),('edit-forum-post','Edit Forum Post','',2,'forums'),('edit-forum-thread','Edit Forum Thread','',2,'forums'),('edit-forums','Edit Forums','',3,''),('edit-ip-bans','Edit IP Bans','',0,''),('edit-moods','Edit Moods','',3,''),('edit-permissions','Edit Permissions','',3,''),('edit-sprites','Edit Sprites','',3,''),('edit-title','Edit Title','',3,''),('edit-users','Edit Users','',3,''),('has-displayname','Can Use Displayname','',3,''),('ignore-thread-time-limit','Ignore Thread Time Limit','',0,''),('login','Login','',1,''),('mark-read','Mark Read','',1,''),('no-restrictions','No Restrictions','',3,''),('override-readonly-forums','Override Read Only Forums','',3,''),('post-radar','Post Radar','Can use Post Radar',2,''),('rate-thread','Rate Thread','',1,''),('register','Register','',1,''),('rename-own-thread','Rename Own Thread','',1,''),('show-as-staff','Listed Publicly as Staff','',3,'users'),('staff','Is Staff','',2,''),('track-ip-change','Track IP Changes in IRC','Add this to a group or user to have their IP change reported to the staff channel.',3,''),('update-own-moods','Update Own Moods','',1,''),('update-own-post','Update Own Post','',4,''),('update-own-profile','Update Own Profile','',1,''),('update-post','Update Post','',2,''),('update-profiles','Update Profiles','',3,''),('update-thread','Update Thread','',2,''),('update-user-moods','Update User Moods','',3,'users'),('update-user-profile','Update User Profile','',3,'users'),('use-item-shop','Use Item Shop','',1,''),('use-post-layout','Use Post Layout','',4,''),('use-test-bed','Use Test Bed','',3,''),('use-uploader','Use Uploader','',1,''),('view-acs-calendar','View ACS Rankings Calendar','',2,''),('view-all-private-categories','View All Private Categories','',3,''),('view-all-private-forums','View All Private Forums','',3,''),('view-all-private-posts','View All Private Posts','',3,''),('view-all-private-threads','View All Private Threads','',3,''),('view-all-sprites','View All Sprites','',3,''),('view-allranks','Show Hidden Ranks','',2,''),('view-calendar','View Calendar','',1,''),('view-errors','View PHP Errors','',0,''),('view-forum-post-history','View Forum Post History','',2,'forums'),('view-hidden-users','View Hidden Users','',3,''),('view-own-pms','View Own PMs','',1,''),('view-own-sprites','View Own Sprites','',1,''),('view-permissions','View Permissions','',3,''),('view-post-history','View Post History','',2,''),('view-post-ips','View Post IP Addresses','',3,''),('view-private-category','View Private Category','',2,'categories'),('view-private-forum','View Private Forum','',2,'forums'),('view-private-post','View Private Post','',2,'posts'),('view-private-thread','View Private Thread','',2,'threads'),('view-profile-page','View Profile Page','',1,''),('view-public-categories','View Public Categories','',1,''),('view-public-forums','View Public Forums','',1,''),('view-public-posts','View Public Posts','',1,''),('view-public-threads','View Public Threads','',1,''),('view-user-pms','View User PMs','',3,''),('view-user-urls','View User URLs','',3,'');
/*!40000 ALTER TABLE `perm` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `perm_permbind`
--

DROP TABLE IF EXISTS `perm_permbind`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `perm_permbind` (
  `perm_id` varchar(64) NOT NULL,
  `permbind_id` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `perm_permbind`
--

LOCK TABLES `perm_permbind` WRITE;
/*!40000 ALTER TABLE `perm_permbind` DISABLE KEYS */;
INSERT INTO `perm_permbind` VALUES ('view-private-category','categories'),('view-private-forum','forums'),('view-private-thread','threads'),('view-private-post','posts');
/*!40000 ALTER TABLE `perm_permbind` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permbind`
--

DROP TABLE IF EXISTS `permbind`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permbind` (
  `id` varchar(64) NOT NULL,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permbind`
--

LOCK TABLES `permbind` WRITE;
/*!40000 ALTER TABLE `permbind` DISABLE KEYS */;
INSERT INTO `permbind` VALUES ('categories','Category'),('forums','Forum'),('group','Group'),('posts','Post'),('threads','Thread'),('users','User');
/*!40000 ALTER TABLE `permbind` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permcat`
--

DROP TABLE IF EXISTS `permcat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permcat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `sortorder` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permcat`
--

LOCK TABLES `permcat` WRITE;
/*!40000 ALTER TABLE `permcat` DISABLE KEYS */;
INSERT INTO `permcat` VALUES (1,'Basic',100),(2,'Moderator',200),(3,'Administrative',300),(4,'Posting',150);
/*!40000 ALTER TABLE `permcat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pmsgs`
--

DROP TABLE IF EXISTS `pmsgs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pmsgs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL DEFAULT '0',
  `ip` char(15) NOT NULL,
  `userto` mediumint(9) unsigned NOT NULL,
  `userfrom` mediumint(9) unsigned NOT NULL,
  `unread` tinyint(4) NOT NULL,
  `del_from` tinyint(1) NOT NULL DEFAULT '0',
  `del_to` tinyint(1) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pmsgs`
--

LOCK TABLES `pmsgs` WRITE;
/*!40000 ALTER TABLE `pmsgs` DISABLE KEYS */;
/*!40000 ALTER TABLE `pmsgs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pmsgstext`
--

DROP TABLE IF EXISTS `pmsgstext`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pmsgstext` (
  `id` int(11) unsigned NOT NULL DEFAULT '0',
  `text` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pmsgstext`
--

LOCK TABLES `pmsgstext` WRITE;
/*!40000 ALTER TABLE `pmsgstext` DISABLE KEYS */;
/*!40000 ALTER TABLE `pmsgstext` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `polloptions`
--

DROP TABLE IF EXISTS `polloptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `polloptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `poll` int(11) NOT NULL,
  `option` varchar(255) NOT NULL,
  `r` smallint(3) NOT NULL,
  `g` smallint(3) NOT NULL,
  `b` smallint(3) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `poll` (`poll`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `polloptions`
--

LOCK TABLES `polloptions` WRITE;
/*!40000 ALTER TABLE `polloptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `polloptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `polls`
--

DROP TABLE IF EXISTS `polls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `polls` (
  `id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL,
  `multivote` int(1) NOT NULL DEFAULT '0',
  `changeable` int(1) NOT NULL DEFAULT '0',
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `polls`
--

LOCK TABLES `polls` WRITE;
/*!40000 ALTER TABLE `polls` DISABLE KEYS */;
/*!40000 ALTER TABLE `polls` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pollvotes`
--

DROP TABLE IF EXISTS `pollvotes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pollvotes` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  UNIQUE KEY `id_2` (`id`,`user`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pollvotes`
--

LOCK TABLES `pollvotes` WRITE;
/*!40000 ALTER TABLE `pollvotes` DISABLE KEYS */;
/*!40000 ALTER TABLE `pollvotes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `post_radar`
--

DROP TABLE IF EXISTS `post_radar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `post_radar` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL,
  `user2_id` mediumint(8) unsigned NOT NULL,
  `ctime` bigint(20) unsigned NOT NULL,
  `dtime` bigint(20) unsigned DEFAULT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `user_id` (`user_id`,`user2_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post_radar`
--

LOCK TABLES `post_radar` WRITE;
/*!40000 ALTER TABLE `post_radar` DISABLE KEYS */;
/*!40000 ALTER TABLE `post_radar` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posticons`
--

DROP TABLE IF EXISTS `posticons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posticons` (
  `id` tinyint(4) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posticons`
--

LOCK TABLES `posticons` WRITE;
/*!40000 ALTER TABLE `posticons` DISABLE KEYS */;
INSERT INTO `posticons` VALUES (1,'img/icons/icon1.gif'),(2,'img/icons/icon2.gif'),(3,'img/icons/icon3.gif'),(4,'img/icons/icon4.gif'),(5,'img/icons/icon5.gif'),(6,'img/icons/icon6.gif'),(7,'img/icons/icon7.gif'),(8,'img/coin.gif'),(9,'img/coin2.gif'),(10,'img/smilies/baby.gif'),(11,'img/smilies/smile.gif'),(12,'img/smilies/wink.gif'),(13,'img/smilies/biggrin.gif'),(14,'img/smilies/cute.gif'),(15,'img/smilies/glasses.gif'),(16,'img/smilies/mad.gif'),(17,'img/smilies/frown.gif'),(18,'img/smilies/yuck.gif'),(19,'img/smilies/sick.gif'),(20,'img/smilies/wobbly.gif'),(21,'img/smilies/eek.gif'),(22,'img/smilies/blank.gif'),(23,'img/smilies/jawdrop.gif'),(24,'img/smilies/bigeyes.gif'),(25,'img/smilies/tongue.gif'),(26,'img/smilies/vamp.gif'),(27,'img/smilies/dizzy.gif'),(28,'img/smilies/eyeshift.gif'),(29,'img/smilies/shiftleft.gif'),(30,'img/smilies/shiftright.gif');
/*!40000 ALTER TABLE `posticons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user` mediumint(9) unsigned NOT NULL DEFAULT '0',
  `thread` mediumint(9) unsigned NOT NULL DEFAULT '0',
  `date` int(11) NOT NULL DEFAULT '0',
  `mood` int(11) NOT NULL DEFAULT '-1',
  `nolayout` int(1) NOT NULL,
  `ip` char(15) NOT NULL,
  `num` mediumint(9) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `announce` int(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `threadid` (`thread`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `poststext`
--

DROP TABLE IF EXISTS `poststext`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `poststext` (
  `id` int(11) unsigned NOT NULL DEFAULT '0',
  `text` text NOT NULL,
  `revision` int(5) NOT NULL DEFAULT '1',
  `date` int(11) NOT NULL,
  `user` mediumint(9) NOT NULL,
  PRIMARY KEY (`id`,`revision`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `poststext`
--

LOCK TABLES `poststext` WRITE;
/*!40000 ALTER TABLE `poststext` DISABLE KEYS */;
/*!40000 ALTER TABLE `poststext` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profileext`
--

DROP TABLE IF EXISTS `profileext`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `profileext` (
  `id` varchar(64) NOT NULL DEFAULT '',
  `title` varchar(256) NOT NULL DEFAULT '',
  `sortorder` int(11) NOT NULL DEFAULT '0',
  `fmt` varchar(256) NOT NULL DEFAULT '%s',
  `description` varchar(256) NOT NULL DEFAULT '',
  `icon` varchar(256) NOT NULL DEFAULT '',
  `validation` varchar(256) NOT NULL DEFAULT '',
  `example` varchar(256) NOT NULL DEFAULT '',
  `extrafield` int(1) NOT NULL DEFAULT '0',
  `parser` varchar(256) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profileext`
--

LOCK TABLES `profileext` WRITE;
/*!40000 ALTER TABLE `profileext` DISABLE KEYS */;
INSERT INTO `profileext` VALUES ('3ds','3DS Friend Code',0,'$2-$4-$6','Your 3DS Friend Code (hyphens are optional)','','(([0-9]){4,4}(-?)){3,3}','1234-5678-9012',0,''),('aim','AIM Screen Name',0,'$0','Your AIM Screen Name (or email)','','[A-Za-z.%+-_@]+','SmarterChild',0,'email'),('ds','DS Game Friend Code',0,'$2-$4-$6','Your DS Game Friend Code (hyphens are optional)','','(([0-9]){4,4}(-?)){3,3}','1234-5678-9012',1,''),('facebook','Facebook',0,'<a href=http://www.facebook.com/$0>$0</a>','Your Facebook ID number or username','','[\\.0-9a-zA-Z]+','john.smith',0,''),('gplus','Google+',0,'<a href=http://plus.google.com/$0>$0</a>','Your Google+ ID (the long ass number)','','[0-9]+','110393731121066107376',0,''),('gtalk','Google Talk',0,'$0','Your Google Talk email address','','[A-Z0-9._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,4}','eric.schmidt@gmail.com',0,'email'),('jabber','Jabber',0,'$0','Your Jabber email address','','[A-Z0-9._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,4}','linus.torvalds@linux.org',0,'email'),('msn','Windows Live! ID',0,'$0','Your Windows Live! ID','','[A-Z0-9._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,4}','bill.gates@hotmail.com',0,'email'),('psn','PSN',0,'$0','Your PlayStation Network username','','[0-9a-zA-Z]+','sonyrul3s',0,''),('soundcloud','Soundcloud',0,'<a href=http://soundcloud.com/$0>$0</a>','Your Soundcloud username (as it appears on a URL)','','[_\\-0-9a-zA-Z]+','britney-spears',0,''),('twitter','Twitter',0,'<a href=http://twitter.com/$0>@$0</a>','Your Twitter username (without the leading @)','','[_0-9a-zA-Z]+','jack',0,''),('wii','Wii Game Friend Code',0,'$2-$4-$6','Your Wii Game Friend Code (hyphens are optional)','','(([0-9]){4,4}(-?)){3,3}','1234-5678-9012',1,''),('wii-system','Wii Friend Code',0,'$2-$4-$6','Your Wii Friend Code (hyphens are optional)','','(([0-9]){4,4}(-?)){3,3}','1234-5678-9012',0,''),('xbl','XBOX Live',0,'$0','Your XBOX Live username','','[0-9a-zA-Z]+','n00bpwner',0,''),('yahoo','Yahoo! ID',0,'$email','Your Yahoo! ID','','[A-Z0-9._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,4}','carol.bartz@rocketmail.com',0,'email'),('youtube','YouTube',0,'<a href=http://www.youtube.com/$0>$0</a>','Your YouTube username','','[0-9a-zA-Z]+','spudd',0,'');
/*!40000 ALTER TABLE `profileext` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ranks`
--

DROP TABLE IF EXISTS `ranks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ranks` (
  `rs` int(10) NOT NULL,
  `p` int(10) NOT NULL DEFAULT '0',
  `str` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ranks`
--

LOCK TABLES `ranks` WRITE;
/*!40000 ALTER TABLE `ranks` DISABLE KEYS */;
INSERT INTO `ranks` VALUES (1,0,'Non-poster'),(1,1,'Newcomer'),(1,20,'<img src=img/ranks/goomba.gif width=16 height=16><br>Goomba'),(1,10,'<img src=img/ranks/microgoomba.gif width=8 height=9><br>Micro-Goomba'),(1,35,'<img src=img/ranks/redgoomba.gif width=16 height=16><br>Red Goomba'),(1,50,'<img src=img/ranks/redparagoomba.gif width=20 height=24><br>Red Paragoomba'),(1,65,'<img src=img/ranks/paragoomba.gif width=20 height=24><br>Paragoomba'),(1,80,'<img src=img/ranks/shyguy.gif width=16 height=16><br>Shyguy'),(1,100,'<img src=img/ranks/koopa.gif width=16 height=27><br>Koopa'),(1,120,'<img src=img/ranks/redkoopa.gif width=16 height=27><br>Red Koopa'),(1,140,'<img src=img/ranks/paratroopa.gif width=16 height=28><br>Paratroopa'),(1,160,'<img src=img/ranks/redparatroopa.gif width=16 height=28><br>Red Paratroopa'),(1,180,'<img src=img/ranks/cheepcheep.gif width=16 height=16><br>Cheep-cheep'),(1,200,'<img src=img/ranks/redcheepcheep.gif width=16 height=16><br>Red Cheep-cheep'),(1,225,'<img src=img/ranks/ninji.gif width=16 height=16><br>Ninji'),(1,250,'<img src=img/ranks/flurry.gif width=16 height=16><br>Flurry'),(1,275,'<img src=img/ranks/snifit.gif width=16 height=16><br>Snifit'),(1,300,'<img src=img/ranks/porcupo.gif width=16 height=16><br>Porcupo'),(1,325,'<img src=img/ranks/panser.gif width=16 height=16><br>Panser'),(1,350,'<img src=img/ranks/mole.gif width=16 height=16><br>Mole'),(1,375,'<img src=img/ranks/beetle.gif width=16 height=16><br>Buzzy Beetle'),(1,400,'<img src=img/ranks/nipperplant.gif width=16 height=16><br>Nipper Plant'),(1,425,'<img src=img/ranks/bloober.gif width=16 height=16><br>Bloober'),(1,450,'<img src=img/ranks/busterbeetle.gif width=16 height=15><br>Buster Beetle'),(1,475,'<img src=img/ranks/beezo.gif width=16 height=16><br>Beezo'),(1,500,'<img src=img/ranks/bulletbill.gif width=16 height=14><br>Bullet Bill'),(1,525,'<img src=img/ranks/rex.gif width=20 height=32><br>Rex'),(1,550,'<img src=img/ranks/lakitu.gif width=16 height=24><br>Lakitu'),(1,575,'<img src=img/ranks/spiny.gif width=16 height=16><br>Spiny'),(1,600,'<img src=img/ranks/bobomb.gif width=16 height=16><br>Bob-Omb'),(1,700,'<img src=img/ranks/spike.gif width=32 height=32><br>Spike'),(1,675,'<img src=img/ranks/pokey.gif width=18 height=64><br>Pokey'),(1,650,'<img src=img/ranks/cobrat.gif width=16 height=32><br>Cobrat'),(1,725,'<img src=img/ranks/hedgehog.gif width=16 height=24><br>Melon Bug'),(1,750,'<img src=img/ranks/lanternghost.gif width=26 height=19><br>Lantern Ghost'),(1,775,'<img src=img/ranks/fuzzy.gif width=32 height=31><br>Fuzzy'),(1,800,'<img src=img/ranks/bandit.gif width=23 height=28><br>Bandit'),(1,830,'<img src=img/ranks/superkoopa.gif width=23 height=13><br>Super Koopa'),(1,860,'<img src=img/ranks/redsuperkoopa.gif width=23 height=13><br>Red Super Koopa'),(1,900,'<img src=img/ranks/boo.gif width=16 height=16><br>Boo'),(1,925,'<img src=img/ranks/boo2.gif width=16 height=16><br>Boo'),(1,950,'<img src=img/ranks/fuzzball.gif width=16 height=16><br>Fuzz Ball'),(1,1000,'<img src=img/ranks/boomerangbrother.gif width=60 height=40><br>Boomerang Brother'),(1,1050,'<img src=img/ranks/hammerbrother.gif width=60 height=40><br>Hammer Brother'),(1,1100,'<img src=img/ranks/firebrother.gif width=60 height=24><br>Fire Brother'),(1,1150,'<img src=img/ranks/firesnake.gif width=45 height=36><br>Fire Snake'),(1,1200,'<img src=img/ranks/giantgoomba.gif width=24 height=23><br>Giant Goomba'),(1,1250,'<img src=img/ranks/giantkoopa.gif width=24 height=31><br>Giant Koopa'),(1,1300,'<img src=img/ranks/giantredkoopa.gif width=24 height=31><br>Giant Red Koopa'),(1,1350,'<img src=img/ranks/giantparatroopa.gif width=24 height=31><br>Giant Paratroopa'),(1,1400,'<img src=img/ranks/giantredparatroopa.gif width=24 height=31><br>Giant Red Paratroopa'),(1,1450,'<img src=img/ranks/chuck.gif width=28 height=27><br>Chuck'),(1,1500,'<img src=img/ranks/thwomp.gif width=44 height=32><br>Thwomp'),(1,1550,'<img src=img/ranks/bigcheepcheep.gif width=24 height=32><br>Boss Bass'),(1,1600,'<img src=img/ranks/volcanolotus.gif width=32 height=30><br>Volcano Lotus'),(1,1650,'<img src=img/ranks/lavalotus.gif width=24 height=32><br>Lava Lotus'),(1,1700,'<img src=img/ranks/ptooie2.gif width=16 height=43><br>Ptooie'),(1,1800,'<img src=img/ranks/sledgebrother.gif width=60 height=50><br>Sledge Brother'),(1,1900,'<img src=img/ranks/boomboom.gif width=28 height=26><br>Boomboom'),(1,2000,'<img src=img/ranks/birdopink.gif width=60 height=36><br>Birdo'),(1,2100,'<img src=img/ranks/birdored.gif width=60 height=36><br>Red Birdo'),(1,2200,'<img src=img/ranks/birdogreen.gif width=60 height=36><br>Green Birdo'),(1,2300,'<img src=img/ranks/iggy.gif width=28><br>Larry Koopa'),(1,2400,'<img src=img/ranks/morton.gif width=34><br>Morton Koopa'),(1,2500,'<img src=img/ranks/wendy.gif width=28><br>Wendy Koopa'),(1,2600,'<img src=img/ranks/larry.gif width=28><br>Iggy Koopa'),(1,2700,'<img src=img/ranks/roy.gif width=34><br>Roy Koopa'),(1,2800,'<img src=img/ranks/lemmy.gif width=28><br>Lemmy Koopa'),(1,2900,'<img src=img/ranks/ludwig.gif width=33><br>Ludwig Von Koopa'),(1,3000,'<img src=img/ranks/triclyde.gif width=40 height=48><br>Triclyde'),(1,3100,'<img src=img/ranks/kamek.gif width=45 height=34><br>Magikoopa'),(1,3200,'<img src=img/ranks/wart.gif width=40 height=47><br>Wart'),(1,3300,'<img src=img/ranks/babybowser.gif width=36 height=36><br>Baby Bowser'),(1,3400,'<img src=img/ranks/bowser.gif width=52 height=49><br>King Bowser Koopa'),(1,3500,'<img src=img/ranks/yoshi.gif width=31 height=33><br>Yoshi'),(1,3600,'<img src=img/ranks/yoshiyellow.gif width=31 height=32><br>Yellow Yoshi'),(1,3700,'<img src=img/ranks/yoshiblue.gif width=36 height=35><br>Blue Yoshi'),(1,3800,'<img src=img/ranks/yoshired.gif width=33 height=36><br>Red Yoshi'),(1,3900,'<img src=img/ranks/kingyoshi.gif width=24 height=34><br>King Yoshi'),(1,4000,'<img src=img/ranks/babymario.gif width=28 height=24><br>Baby Mario'),(1,4100,'<img src=img/ranks/luigismall.gif width=15 height=22><br>Luigi'),(1,4200,'<img src=img/ranks/mariosmall.gif width=15 height=20><br>Mario'),(1,4300,'<img src=img/ranks/luigibig.gif width=16 height=30><br>Super Luigi'),(1,4400,'<img src=img/ranks/mariobig.gif width=16 height=28><br>Super Mario'),(1,4500,'<img src=img/ranks/luigifire.gif width=16 height=30><br>Fire Luigi'),(1,4600,'<img src=img/ranks/mariofire.gif width=16 height=28><br>Fire Mario'),(1,4700,'<img src=img/ranks/luigicape.gif width=26 height=30><br>Cape Luigi'),(1,4800,'<img src=img/ranks/mariocape.gif width=26 height=28><br>Cape Mario'),(1,4900,'<img src=img/ranks/luigistar.gif width=16 height=30><br>Star Luigi'),(1,5000,'<img src=img/ranks/mariostar.gif width=16 height=28><br>Star Mario'),(1,625,'<img src=img/ranks/drybones.gif><br>Dry Bones'),(1,10000,'Climbing the ranks again!');
/*!40000 ALTER TABLE `ranks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ranksets`
--

DROP TABLE IF EXISTS `ranksets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ranksets` (
  `id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ranksets`
--

LOCK TABLES `ranksets` WRITE;
/*!40000 ALTER TABLE `ranksets` DISABLE KEYS */;
INSERT INTO `ranksets` VALUES (1,'Mario'),(0,'None'),(-1,'Dots (by Xkeeper)');
/*!40000 ALTER TABLE `ranksets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ref`
--

DROP TABLE IF EXISTS `ref`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ref` (
  `time` int(11) NOT NULL,
  `urlfrom` varchar(255) NOT NULL,
  `urlto` varchar(255) NOT NULL,
  `userid` int(11) NOT NULL,
  `ipaddr` varchar(15) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ref`
--

LOCK TABLES `ref` WRITE;
/*!40000 ALTER TABLE `ref` DISABLE KEYS */;
/*!40000 ALTER TABLE `ref` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `robots`
--

DROP TABLE IF EXISTS `robots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `robots` (
  `bot_name` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `bot_agent` varchar(100) COLLATE latin1_general_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `robots`
--

LOCK TABLES `robots` WRITE;
/*!40000 ALTER TABLE `robots` DISABLE KEYS */;
/*!40000 ALTER TABLE `robots` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rpgchat`
--

DROP TABLE IF EXISTS `rpgchat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rpgchat` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `chan` tinyint(4) NOT NULL DEFAULT '0',
  `date` int(11) NOT NULL DEFAULT '0',
  `user` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `text` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `chan` (`chan`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rpgchat`
--

LOCK TABLES `rpgchat` WRITE;
/*!40000 ALTER TABLE `rpgchat` DISABLE KEYS */;
/*!40000 ALTER TABLE `rpgchat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rpgrooms`
--

DROP TABLE IF EXISTS `rpgrooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rpgrooms` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `lvmin` smallint(5) unsigned NOT NULL DEFAULT '0',
  `lvmax` smallint(5) unsigned NOT NULL DEFAULT '0',
  `users` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `usermax` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `title` varchar(32) NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '0',
  `turn` mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rpgrooms`
--

LOCK TABLES `rpgrooms` WRITE;
/*!40000 ALTER TABLE `rpgrooms` DISABLE KEYS */;
/*!40000 ALTER TABLE `rpgrooms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `smilies`
--

DROP TABLE IF EXISTS `smilies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smilies` (
  `text` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `smilies`
--

LOCK TABLES `smilies` WRITE;
/*!40000 ALTER TABLE `smilies` DISABLE KEYS */;
INSERT INTO `smilies` VALUES ('-_-','img/smilies/annoyed.gif'),('~:o','img/smilies/baby.gif'),('o_O','img/smilies/bigeyes.gif'),(':D','img/smilies/biggrin.gif'),('o_o','img/smilies/blank.gif'),(';_;','img/smilies/cry.gif'),('^^;;;','img/smilies/cute2.gif'),('^_^','img/smilies/cute.gif'),('@_@','img/smilies/dizzy.gif'),('O_O','img/smilies/eek.gif'),('>:]','img/smilies/evil.gif'),(':eyeshift:','img/smilies/eyeshift.gif'),(':(','img/smilies/frown.gif'),('8-)','img/smilies/glasses.gif'),(':LOL:','img/smilies/lol.gif'),('>:[','img/smilies/mad.gif'),('<_<','img/smilies/shiftleft.gif'),('>_>','img/smilies/shiftright.gif'),('x_x','img/smilies/sick.gif'),(':)','img/smilies/smile.gif'),(':P','img/smilies/tongue.gif'),(':B','img/smilies/vamp.gif'),(';)','img/smilies/wink.gif'),(':S','img/smilies/wobbly.gif'),('>_<','img/smilies/yuck.gif'),(':yes:','img/smilies/yes.png'),(':no:','img/smilies/no.png'),(':heart:','img/smilies/heart.gif'),('w00t','img/smilies/woot.gif'),(':x','img/smilies/crossmouth.gif'),(':|','img/smilies/slidemouth.gif'),(':@','img/smilies/dropsmile.gif'),(':-3','img/smilies/wobble.gif'),('X-P','img/smilies/xp.gif'),('X-3','img/smilies/x3.gif'),('X-D','img/smilies/xd.gif'),(':o','img/smilies/dramatic.gif');
/*!40000 ALTER TABLE `smilies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `spambotlog`
--

DROP TABLE IF EXISTS `spambotlog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `spambotlog` (
  `ip` varchar(15) NOT NULL,
  `user` varchar(255) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `title` text NOT NULL,
  `text` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spambotlog`
--

LOCK TABLES `spambotlog` WRITE;
/*!40000 ALTER TABLE `spambotlog` DISABLE KEYS */;
/*!40000 ALTER TABLE `spambotlog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sprite_captures`
--

DROP TABLE IF EXISTS `sprite_captures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sprite_captures` (
  `userid` int(11) NOT NULL,
  `monid` int(11) NOT NULL,
  UNIQUE KEY `userid` (`userid`,`monid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sprite_captures`
--

LOCK TABLES `sprite_captures` WRITE;
/*!40000 ALTER TABLE `sprite_captures` DISABLE KEYS */;
/*!40000 ALTER TABLE `sprite_captures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sprites`
--

DROP TABLE IF EXISTS `sprites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sprites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  `franchiseid` int(11) NOT NULL DEFAULT '0',
  `pic` varchar(256) NOT NULL,
  `alt` varchar(256) NOT NULL,
  `anchor` enum('free','left','right','top','bottom','sides','sidepic') NOT NULL,
  `title` varchar(256) NOT NULL,
  `flavor` text NOT NULL,
  `rarity` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sprites`
--

LOCK TABLES `sprites` WRITE;
/*!40000 ALTER TABLE `sprites` DISABLE KEYS */;
/*!40000 ALTER TABLE `sprites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tags` (
  `bit` int(8) NOT NULL,
  `fid` int(10) NOT NULL,
  `name` varchar(64) NOT NULL,
  `tag` varchar(20) NOT NULL,
  `color` varchar(6) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tags`
--

LOCK TABLES `tags` WRITE;
/*!40000 ALTER TABLE `tags` DISABLE KEYS */;
/*!40000 ALTER TABLE `tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `threads`
--

DROP TABLE IF EXISTS `threads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `threads` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `replies` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `views` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `closed` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `sticky` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `forum` int(5) NOT NULL DEFAULT '0',
  `user` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `lastdate` int(11) NOT NULL DEFAULT '0',
  `lastuser` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `lastid` int(11) NOT NULL DEFAULT '0',
  `icon` varchar(100) NOT NULL,
  `tags` int(12) NOT NULL,
  `announce` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `threads`
--

LOCK TABLES `threads` WRITE;
/*!40000 ALTER TABLE `threads` DISABLE KEYS */;
/*!40000 ALTER TABLE `threads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `threadsread`
--

DROP TABLE IF EXISTS `threadsread`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `threadsread` (
  `uid` mediumint(9) NOT NULL,
  `tid` mediumint(9) NOT NULL,
  `time` int(11) NOT NULL,
  UNIQUE KEY `uid` (`uid`,`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `threadsread`
--

LOCK TABLES `threadsread` WRITE;
/*!40000 ALTER TABLE `threadsread` DISABLE KEYS */;
/*!40000 ALTER TABLE `threadsread` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `threadthumbs`
--

DROP TABLE IF EXISTS `threadthumbs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `threadthumbs` (
  `uid` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  UNIQUE KEY `uid` (`uid`,`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `threadthumbs`
--

LOCK TABLES `threadthumbs` WRITE;
/*!40000 ALTER TABLE `threadthumbs` DISABLE KEYS */;
/*!40000 ALTER TABLE `threadthumbs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `timezones`
--

DROP TABLE IF EXISTS `timezones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `timezones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL DEFAULT '',
  `offset` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=466 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `timezones`
--

LOCK TABLES `timezones` WRITE;
/*!40000 ALTER TABLE `timezones` DISABLE KEYS */;
INSERT INTO `timezones` VALUES (1,'UTC',0),(2,'Africa/Abidjan',0),(3,'Africa/Accra',0),(4,'Africa/Addis_Ababa',10800),(5,'Africa/Algiers',3600),(6,'Africa/Asmara',10800),(7,'Africa/Asmera',10800),(8,'Africa/Bamako',0),(9,'Africa/Bangui',3600),(10,'Africa/Banjul',0),(11,'Africa/Bissau',0),(12,'Africa/Blantyre',7200),(13,'Africa/Brazzaville',3600),(14,'Africa/Bujumbura',7200),(15,'Africa/Cairo',7200),(16,'Africa/Casablanca',0),(17,'Africa/Ceuta',3600),(18,'Africa/Conakry',0),(19,'Africa/Dakar',0),(20,'Africa/Dar_es_Salaam',10800),(21,'Africa/Djibouti',10800),(22,'Africa/Douala',3600),(23,'Africa/El_Aaiun',0),(24,'Africa/Freetown',0),(25,'Africa/Gaborone',7200),(26,'Africa/Harare',7200),(27,'Africa/Johannesburg',7200),(28,'Africa/Juba',0),(29,'Africa/Kampala',0),(30,'Africa/Khartoum',0),(31,'Africa/Kigali',0),(32,'Africa/Kinshasa',0),(33,'Africa/Lagos',0),(34,'Africa/Libreville',0),(35,'Africa/Lome',0),(36,'Africa/Luanda',0),(37,'Africa/Lubumbashi',0),(38,'Africa/Lusaka',0),(39,'Africa/Malabo',0),(40,'Africa/Maputo',0),(41,'Africa/Maseru',0),(42,'Africa/Mbabane',0),(43,'Africa/Mogadishu',0),(44,'Africa/Monrovia',0),(45,'Africa/Nairobi',0),(46,'Africa/Ndjamena',0),(47,'Africa/Niamey',0),(48,'Africa/Nouakchott',0),(49,'Africa/Ouagadougou',0),(50,'Africa/Porto-Novo',0),(51,'Africa/Sao_Tome',0),(52,'Africa/Timbuktu',0),(53,'Africa/Tripoli',0),(54,'Africa/Tunis',0),(55,'Africa/Windhoek',0),(56,'America/Adak',0),(57,'America/Anchorage',0),(58,'America/Anguilla',0),(59,'America/Antigua',0),(60,'America/Araguaina',0),(61,'America/Argentina/Buenos_Aires',0),(62,'America/Argentina/Catamarca',0),(63,'America/Argentina/ComodRivadavia',0),(64,'America/Argentina/Cordoba',0),(65,'America/Argentina/Jujuy',0),(66,'America/Argentina/La_Rioja',0),(67,'America/Argentina/Mendoza',0),(68,'America/Argentina/Rio_Gallegos',0),(69,'America/Argentina/Salta',0),(70,'America/Argentina/San_Juan',0),(71,'America/Argentina/San_Luis',0),(72,'America/Argentina/Tucuman',0),(73,'America/Argentina/Ushuaia',0),(74,'America/Aruba',0),(75,'America/Asuncion',0),(76,'America/Atikokan',0),(77,'America/Atka',0),(78,'America/Bahia',0),(79,'America/Bahia_Banderas',0),(80,'America/Barbados',0),(81,'America/Belem',0),(82,'America/Belize',0),(83,'America/Blanc-Sablon',0),(84,'America/Boa_Vista',0),(85,'America/Bogota',0),(86,'America/Boise',0),(87,'America/Buenos_Aires',0),(88,'America/Cambridge_Bay',0),(89,'America/Campo_Grande',0),(90,'America/Cancun',0),(91,'America/Caracas',0),(92,'America/Catamarca',0),(93,'America/Cayenne',0),(94,'America/Cayman',0),(95,'America/Chicago',0),(96,'America/Chihuahua',0),(97,'America/Coral_Harbour',0),(98,'America/Cordoba',0),(99,'America/Costa_Rica',0),(100,'America/Cuiaba',0),(101,'America/Curacao',0),(102,'America/Danmarkshavn',0),(103,'America/Dawson',0),(104,'America/Dawson_Creek',0),(105,'America/Denver',0),(106,'America/Detroit',0),(107,'America/Dominica',0),(108,'America/Edmonton',0),(109,'America/Eirunepe',0),(110,'America/El_Salvador',0),(111,'America/Ensenada',0),(112,'America/Fort_Wayne',0),(113,'America/Fortaleza',0),(114,'America/Glace_Bay',0),(115,'America/Godthab',0),(116,'America/Goose_Bay',0),(117,'America/Grand_Turk',0),(118,'America/Grenada',0),(119,'America/Guadeloupe',0),(120,'America/Guatemala',0),(121,'America/Guayaquil',0),(122,'America/Guyana',0),(123,'America/Halifax',0),(124,'America/Havana',0),(125,'America/Hermosillo',0),(126,'America/Indiana/Indianapolis',0),(127,'America/Indiana/Knox',0),(128,'America/Indiana/Marengo',0),(129,'America/Indiana/Petersburg',0),(130,'America/Indiana/Tell_City',0),(131,'America/Indiana/Vevay',0),(132,'America/Indiana/Vincennes',0),(133,'America/Indiana/Winamac',0),(134,'America/Indianapolis',0),(135,'America/Inuvik',0),(136,'America/Iqaluit',0),(137,'America/Jamaica',0),(138,'America/Jujuy',0),(139,'America/Juneau',0),(140,'America/Kentucky/Louisville',0),(141,'America/Kentucky/Monticello',0),(142,'America/Knox_IN',0),(143,'America/Kralendijk',0),(144,'America/La_Paz',0),(145,'America/Lima',0),(146,'America/Los_Angeles',0),(147,'America/Louisville',0),(148,'America/Lower_Princes',0),(149,'America/Maceio',0),(150,'America/Managua',0),(151,'America/Manaus',0),(152,'America/Marigot',0),(153,'America/Martinique',0),(154,'America/Matamoros',0),(155,'America/Mazatlan',0),(156,'America/Mendoza',0),(157,'America/Menominee',0),(158,'America/Merida',0),(159,'America/Metlakatla',0),(160,'America/Mexico_City',0),(161,'America/Miquelon',0),(162,'America/Moncton',0),(163,'America/Monterrey',0),(164,'America/Montevideo',0),(165,'America/Montreal',0),(166,'America/Montserrat',0),(167,'America/Nassau',0),(168,'America/New_York',0),(169,'America/Nipigon',0),(170,'America/Nome',0),(171,'America/Noronha',0),(172,'America/North_Dakota/Beulah',0),(173,'America/North_Dakota/Center',0),(174,'America/North_Dakota/New_Salem',0),(175,'America/Ojinaga',0),(176,'America/Panama',0),(177,'America/Pangnirtung',0),(178,'America/Paramaribo',0),(179,'America/Phoenix',0),(180,'America/Port-au-Prince',0),(181,'America/Port_of_Spain',0),(182,'America/Porto_Acre',0),(183,'America/Porto_Velho',0),(184,'America/Puerto_Rico',0),(185,'America/Rainy_River',0),(186,'America/Rankin_Inlet',0),(187,'America/Recife',0),(188,'America/Regina',0),(189,'America/Resolute',0),(190,'America/Rio_Branco',0),(191,'America/Rosario',0),(192,'America/Santa_Isabel',0),(193,'America/Santarem',0),(194,'America/Santiago',0),(195,'America/Santo_Domingo',0),(196,'America/Sao_Paulo',0),(197,'America/Scoresbysund',0),(198,'America/Shiprock',0),(199,'America/Sitka',0),(200,'America/St_Barthelemy',0),(201,'America/St_Johns',0),(202,'America/St_Kitts',0),(203,'America/St_Lucia',0),(204,'America/St_Thomas',0),(205,'America/St_Vincent',0),(206,'America/Swift_Current',0),(207,'America/Tegucigalpa',0),(208,'America/Thule',0),(209,'America/Thunder_Bay',0),(210,'America/Tijuana',0),(211,'America/Toronto',0),(212,'America/Tortola',0),(213,'America/Vancouver',0),(214,'America/Virgin',0),(215,'America/Whitehorse',0),(216,'America/Winnipeg',0),(217,'America/Yakutat',0),(218,'America/Yellowknife',0),(219,'Antarctica/Casey',0),(220,'Antarctica/Davis',0),(221,'Antarctica/DumontDUrville',0),(222,'Antarctica/Macquarie',0),(223,'Antarctica/Mawson',0),(224,'Antarctica/McMurdo',0),(225,'Antarctica/Palmer',0),(226,'Antarctica/Rothera',0),(227,'Antarctica/South_Pole',0),(228,'Antarctica/Syowa',0),(229,'Antarctica/Vostok',0),(230,'Arctic/Longyearbyen',0),(231,'Asia/Aden',0),(232,'Asia/Almaty',0),(233,'Asia/Amman',0),(234,'Asia/Anadyr',0),(235,'Asia/Aqtau',0),(236,'Asia/Aqtobe',0),(237,'Asia/Ashgabat',0),(238,'Asia/Ashkhabad',0),(239,'Asia/Baghdad',0),(240,'Asia/Bahrain',0),(241,'Asia/Baku',0),(242,'Asia/Bangkok',0),(243,'Asia/Beirut',0),(244,'Asia/Bishkek',0),(245,'Asia/Brunei',0),(246,'Asia/Calcutta',0),(247,'Asia/Choibalsan',0),(248,'Asia/Chongqing',0),(249,'Asia/Chungking',0),(250,'Asia/Colombo',0),(251,'Asia/Dacca',0),(252,'Asia/Damascus',0),(253,'Asia/Dhaka',0),(254,'Asia/Dili',0),(255,'Asia/Dubai',0),(256,'Asia/Dushanbe',0),(257,'Asia/Gaza',0),(258,'Asia/Harbin',0),(259,'Asia/Hebron',0),(260,'Asia/Ho_Chi_Minh',0),(261,'Asia/Hong_Kong',0),(262,'Asia/Hovd',0),(263,'Asia/Irkutsk',0),(264,'Asia/Istanbul',0),(265,'Asia/Jakarta',0),(266,'Asia/Jayapura',0),(267,'Asia/Jerusalem',0),(268,'Asia/Kabul',0),(269,'Asia/Kamchatka',0),(270,'Asia/Karachi',0),(271,'Asia/Kashgar',0),(272,'Asia/Kathmandu',0),(273,'Asia/Katmandu',0),(274,'Asia/Kolkata',0),(275,'Asia/Krasnoyarsk',0),(276,'Asia/Kuala_Lumpur',0),(277,'Asia/Kuching',0),(278,'Asia/Kuwait',0),(279,'Asia/Macao',0),(280,'Asia/Macau',0),(281,'Asia/Magadan',0),(282,'Asia/Makassar',0),(283,'Asia/Manila',0),(284,'Asia/Muscat',0),(285,'Asia/Nicosia',0),(286,'Asia/Novokuznetsk',0),(287,'Asia/Novosibirsk',0),(288,'Asia/Omsk',0),(289,'Asia/Oral',0),(290,'Asia/Phnom_Penh',0),(291,'Asia/Pontianak',0),(292,'Asia/Pyongyang',0),(293,'Asia/Qatar',0),(294,'Asia/Qyzylorda',0),(295,'Asia/Rangoon',0),(296,'Asia/Riyadh',0),(297,'Asia/Saigon',0),(298,'Asia/Sakhalin',0),(299,'Asia/Samarkand',0),(300,'Asia/Seoul',0),(301,'Asia/Shanghai',0),(302,'Asia/Singapore',0),(303,'Asia/Taipei',0),(304,'Asia/Tashkent',0),(305,'Asia/Tbilisi',0),(306,'Asia/Tehran',0),(307,'Asia/Tel_Aviv',0),(308,'Asia/Thimbu',0),(309,'Asia/Thimphu',0),(310,'Asia/Tokyo',0),(311,'Asia/Ujung_Pandang',0),(312,'Asia/Ulaanbaatar',0),(313,'Asia/Ulan_Bator',0),(314,'Asia/Urumqi',0),(315,'Asia/Vientiane',0),(316,'Asia/Vladivostok',0),(317,'Asia/Yakutsk',0),(318,'Asia/Yekaterinburg',0),(319,'Asia/Yerevan',0),(320,'Atlantic/Azores',0),(321,'Atlantic/Bermuda',0),(322,'Atlantic/Canary',0),(323,'Atlantic/Cape_Verde',0),(324,'Atlantic/Faeroe',0),(325,'Atlantic/Faroe',0),(326,'Atlantic/Jan_Mayen',0),(327,'Atlantic/Madeira',0),(328,'Atlantic/Reykjavik',0),(329,'Atlantic/South_Georgia',0),(330,'Atlantic/St_Helena',0),(331,'Atlantic/Stanley',0),(332,'Australia/ACT',0),(333,'Australia/Adelaide',0),(334,'Australia/Brisbane',0),(335,'Australia/Broken_Hill',0),(336,'Australia/Canberra',0),(337,'Australia/Currie',0),(338,'Australia/Darwin',0),(339,'Australia/Eucla',0),(340,'Australia/Hobart',0),(341,'Australia/LHI',0),(342,'Australia/Lindeman',0),(343,'Australia/Lord_Howe',0),(344,'Australia/Melbourne',0),(345,'Australia/North',0),(346,'Australia/NSW',0),(347,'Australia/Perth',0),(348,'Australia/Queensland',0),(349,'Australia/South',0),(350,'Australia/Sydney',0),(351,'Australia/Tasmania',0),(352,'Australia/Victoria',0),(353,'Australia/West',0),(354,'Australia/Yancowinna',0),(355,'Europe/Amsterdam',0),(356,'Europe/Andorra',0),(357,'Europe/Athens',0),(358,'Europe/Belfast',0),(359,'Europe/Belgrade',0),(360,'Europe/Berlin',0),(361,'Europe/Bratislava',0),(362,'Europe/Brussels',0),(363,'Europe/Bucharest',0),(364,'Europe/Budapest',0),(365,'Europe/Chisinau',0),(366,'Europe/Copenhagen',0),(367,'Europe/Dublin',0),(368,'Europe/Gibraltar',0),(369,'Europe/Guernsey',0),(370,'Europe/Helsinki',0),(371,'Europe/Isle_of_Man',0),(372,'Europe/Istanbul',0),(373,'Europe/Jersey',0),(374,'Europe/Kaliningrad',0),(375,'Europe/Kiev',0),(376,'Europe/Lisbon',0),(377,'Europe/Ljubljana',0),(378,'Europe/London',0),(379,'Europe/Luxembourg',0),(380,'Europe/Madrid',0),(381,'Europe/Malta',0),(382,'Europe/Mariehamn',0),(383,'Europe/Minsk',0),(384,'Europe/Monaco',0),(385,'Europe/Moscow',0),(386,'Europe/Nicosia',0),(387,'Europe/Oslo',0),(388,'Europe/Paris',0),(389,'Europe/Podgorica',0),(390,'Europe/Prague',0),(391,'Europe/Riga',0),(392,'Europe/Rome',0),(393,'Europe/Samara',0),(394,'Europe/San_Marino',0),(395,'Europe/Sarajevo',0),(396,'Europe/Simferopol',0),(397,'Europe/Skopje',0),(398,'Europe/Sofia',0),(399,'Europe/Stockholm',0),(400,'Europe/Tallinn',0),(401,'Europe/Tirane',0),(402,'Europe/Tiraspol',0),(403,'Europe/Uzhgorod',0),(404,'Europe/Vaduz',0),(405,'Europe/Vatican',0),(406,'Europe/Vienna',0),(407,'Europe/Vilnius',0),(408,'Europe/Volgograd',0),(409,'Europe/Warsaw',0),(410,'Europe/Zagreb',0),(411,'Europe/Zaporozhye',0),(412,'Europe/Zurich',0),(413,'Indian/Antananarivo',0),(414,'Indian/Chagos',0),(415,'Indian/Christmas',0),(416,'Indian/Cocos',0),(417,'Indian/Comoro',0),(418,'Indian/Kerguelen',0),(419,'Indian/Mahe',0),(420,'Indian/Maldives',0),(421,'Indian/Mauritius',0),(422,'Indian/Mayotte',0),(423,'Indian/Reunion',0),(424,'Pacific/Apia',0),(425,'Pacific/Auckland',0),(426,'Pacific/Chatham',0),(427,'Pacific/Chuuk',0),(428,'Pacific/Easter',0),(429,'Pacific/Efate',0),(430,'Pacific/Enderbury',0),(431,'Pacific/Fakaofo',0),(432,'Pacific/Fiji',0),(433,'Pacific/Funafuti',0),(434,'Pacific/Galapagos',0),(435,'Pacific/Gambier',0),(436,'Pacific/Guadalcanal',0),(437,'Pacific/Guam',0),(438,'Pacific/Honolulu',0),(439,'Pacific/Johnston',0),(440,'Pacific/Kiritimati',0),(441,'Pacific/Kosrae',0),(442,'Pacific/Kwajalein',0),(443,'Pacific/Majuro',0),(444,'Pacific/Marquesas',0),(445,'Pacific/Midway',0),(446,'Pacific/Nauru',0),(447,'Pacific/Niue',0),(448,'Pacific/Norfolk',0),(449,'Pacific/Noumea',0),(450,'Pacific/Pago_Pago',0),(451,'Pacific/Palau',0),(452,'Pacific/Pitcairn',0),(453,'Pacific/Pohnpei',0),(454,'Pacific/Ponape',0),(455,'Pacific/Port_Moresby',0),(456,'Pacific/Rarotonga',0),(457,'Pacific/Saipan',0),(458,'Pacific/Samoa',0),(459,'Pacific/Tahiti',0),(460,'Pacific/Tarawa',0),(461,'Pacific/Tongatapu',0),(462,'Pacific/Truk',0),(463,'Pacific/Wake',0),(464,'Pacific/Wallis',0),(465,'Pacific/Yap',0);
/*!40000 ALTER TABLE `timezones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_badges`
--

DROP TABLE IF EXISTS `user_badges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_badges` (
  `user_id` int(11) NOT NULL,
  `badge_id` int(11) NOT NULL,
  `badge_var` varchar(32) DEFAULT NULL,
  KEY `user_id` (`user_id`),
  KEY `badge_id` (`badge_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_badges`
--

LOCK TABLES `user_badges` WRITE;
/*!40000 ALTER TABLE `user_badges` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_badges` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_group`
--

DROP TABLE IF EXISTS `user_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_group` (
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `sortorder` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_group`
--

LOCK TABLES `user_group` WRITE;
/*!40000 ALTER TABLE `user_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `displayname` varchar(32) NOT NULL,
  `pass` varchar(32) NOT NULL,
  `posts` mediumint(9) NOT NULL DEFAULT '0',
  `threads` mediumint(9) NOT NULL DEFAULT '0',
  `regdate` int(11) NOT NULL DEFAULT '0',
  `lastpost` int(11) NOT NULL DEFAULT '0',
  `lastview` int(11) NOT NULL DEFAULT '0',
  `lastforum` int(10) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `ipfwd` varchar(64) NOT NULL,
  `url` varchar(255) NOT NULL,
  `ipbanned` tinyint(4) NOT NULL DEFAULT '0',
  `pmblocked` tinyint(1) NOT NULL DEFAULT '0',
  `canreport` tinyint(4) NOT NULL DEFAULT '1',
  `renamethread` tinyint(4) NOT NULL DEFAULT '1',
  `sex` tinyint(4) NOT NULL DEFAULT '2',
  `power` tinyint(4) NOT NULL DEFAULT '0',
  `tzoff` float NOT NULL DEFAULT '0',
  `dateformat` varchar(15) NOT NULL DEFAULT 'm-d-y',
  `timeformat` varchar(15) NOT NULL DEFAULT 'h:i A',
  `ppp` smallint(3) unsigned NOT NULL DEFAULT '20',
  `tpp` smallint(3) unsigned NOT NULL DEFAULT '20',
  `longpages` int(1) NOT NULL DEFAULT '0',
  `fontsize` smallint(5) unsigned NOT NULL DEFAULT '68',
  `theme` varchar(32) NOT NULL DEFAULT 'bmatrix',
  `birth` varchar(10) NOT NULL DEFAULT '-1',
  `rankset` int(10) NOT NULL DEFAULT '1',
  `title` varchar(255) NOT NULL,
  `realname` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `homeurl` varchar(255) NOT NULL,
  `homename` varchar(255) NOT NULL,
  `usepic` tinyint(4) NOT NULL DEFAULT '0',
  `head` text NOT NULL,
  `sign` text NOT NULL,
  `signsep` int(1) NOT NULL DEFAULT '0',
  `bio` text NOT NULL,
  `minipic` text NOT NULL,
  `etc` int(11) NOT NULL,
  `group_id` int(11) NOT NULL DEFAULT '1',
  `hidden` int(1) NOT NULL DEFAULT '0',
  `blocklayouts` int(11) NOT NULL DEFAULT '0',
  `blocksprites` int(11) NOT NULL DEFAULT '0',
  `timezone` varchar(128) NOT NULL DEFAULT 'UTC',
  `hidequickreply` int(1) NOT NULL DEFAULT '0',
  `adinfo` text NOT NULL,
  `redirtype` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usersrpg`
--

DROP TABLE IF EXISTS `usersrpg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usersrpg` (
  `id` mediumint(9) unsigned NOT NULL DEFAULT '0',
  `spent` int(11) NOT NULL DEFAULT '0',
  `gcoins` int(11) NOT NULL DEFAULT '0',
  `eq1` int(5) unsigned NOT NULL DEFAULT '0',
  `eq2` int(5) unsigned NOT NULL DEFAULT '0',
  `eq3` int(5) unsigned NOT NULL DEFAULT '0',
  `eq4` int(5) unsigned NOT NULL DEFAULT '0',
  `eq5` int(5) unsigned NOT NULL DEFAULT '0',
  `eq6` int(5) unsigned NOT NULL DEFAULT '0',
  `lastact` int(11) NOT NULL DEFAULT '0',
  `room` smallint(6) NOT NULL DEFAULT '0',
  `side` tinyint(4) NOT NULL DEFAULT '0',
  `ready` tinyint(4) NOT NULL DEFAULT '0',
  `hp` mediumint(8) NOT NULL DEFAULT '0',
  `mp` mediumint(8) NOT NULL DEFAULT '0',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usersrpg`
--

LOCK TABLES `usersrpg` WRITE;
/*!40000 ALTER TABLE `usersrpg` DISABLE KEYS */;
/*!40000 ALTER TABLE `usersrpg` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `views`
--

DROP TABLE IF EXISTS `views`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `views` (
  `view` int(11) NOT NULL,
  `user` mediumint(9) NOT NULL,
  `time` int(11) NOT NULL,
  UNIQUE KEY `view` (`view`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `views`
--

LOCK TABLES `views` WRITE;
/*!40000 ALTER TABLE `views` DISABLE KEYS */;
/*!40000 ALTER TABLE `views` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `x_perm`
--

DROP TABLE IF EXISTS `x_perm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x_perm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `x_id` int(11) NOT NULL,
  `x_type` varchar(64) NOT NULL,
  `perm_id` varchar(64) NOT NULL,
  `permbind_id` varchar(64) NOT NULL,
  `bindvalue` int(11) NOT NULL,
  `revoke` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=254 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x_perm`
--

LOCK TABLES `x_perm` WRITE;
/*!40000 ALTER TABLE `x_perm` DISABLE KEYS */;
INSERT INTO `x_perm` VALUES (1,2,'group','capture-sprites','',0,0),(2,2,'group','login','',0,0),(3,2,'group','update-own-profile','',0,0),(4,1,'group','view-profile-page','',0,0),(5,1,'group','view-public-categories','',0,0),(6,1,'group','view-public-forums','',0,0),(7,1,'group','view-public-posts','',0,0),(8,1,'group','view-public-threads','',0,0),(9,2,'group','create-public-post','',0,0),(10,2,'group','create-public-thread','',0,0),(11,2,'group','update-own-post','',0,0),(12,2,'group','use-post-layout','',0,0),(23,8,'group','consecutive-posts','',0,0),(24,3,'group','delete-post','',0,0),(25,3,'group','delete-thread','',0,0),(26,3,'group','update-post','',0,0),(27,3,'group','update-thread','',0,0),(28,3,'group','view-post-history','',0,0),(30,4,'group','edit-attentions-box','',0,0),(31,4,'group','edit-categories','',0,0),(32,4,'group','edit-forums','',0,0),(33,4,'group','edit-moods','',0,0),(34,4,'group','edit-permissions','',0,0),(36,4,'group','view-all-private-categories','',0,0),(37,4,'group','view-all-private-forums','',0,0),(38,4,'group','view-all-private-posts','',0,0),(39,4,'group','view-all-private-threads','',0,0),(40,4,'group','view-all-sprites','',0,0),(41,4,'group','view-permissions','',0,0),(45,6,'group','no-restrictions','',0,0),(46,7,'group','view-private-forum','forum',11,0),(47,7,'group','edit-forum-thread','forum',11,0),(48,7,'group','delete-forum-thread','forum',11,0),(49,7,'group','edit-forum-post','forum',11,0),(50,7,'group','delete-forum-post','forum',11,0),(54,7,'group','view-private-forum','forum',12,0),(55,7,'group','edit-forum-thread','forum',12,0),(56,7,'group','delete-forum-thread','forum',12,0),(57,7,'group','edit-forum-post','forum',12,0),(58,7,'group','delete-forum-post','forum',12,0),(59,7,'group','edit-forum-thread','forum',13,0),(60,7,'group','delete-forum-thread','forum',13,0),(61,7,'group','edit-forum-post','forum',13,0),(62,7,'group','delete-forum-post','forum',13,0),(63,7,'group','view-private-forum','forum',13,0),(64,4,'group','create-all-private-forum-threads','',0,0),(65,4,'group','create-all-private-forum-posts','',0,0),(66,10,'group','view-private-category','categories',2,0),(67,10,'group','view-private-forum','forum',2,0),(68,10,'group','view-private-forum','forum',3,0),(69,10,'group','create-private-forum-thread','forum',2,0),(70,10,'group','create-private-forum-post','forum',2,0),(71,10,'group','create-private-forum-thread','forum',3,0),(72,10,'group','create-private-forum-post','forum',3,0),(73,9,'group','create-public-thread','',0,1),(74,9,'group','create-public-post','',0,1),(75,9,'group','update-own-post','',0,1),(76,9,'group','update-own-profile','',0,1),(77,4,'group','update-profiles','',0,0),(78,9,'group','rate-thread','',0,1),(79,2,'group','rate-thread','',0,0),(80,1,'group','register','',0,0),(81,2,'group','register','',0,1),(82,2,'group','logout','',0,0),(83,1,'group','view-login','',0,0),(84,2,'group','view-login','',0,1),(85,2,'group','mark-read','',0,0),(86,8,'group','staff','',0,0),(87,9,'group','banned','',0,0),(88,8,'group','ignore-thread-time-limit','',0,0),(89,2,'group','rename-own-thread','',0,0),(90,7,'group','view-forum-post-history','forum',11,0),(91,7,'group','view-forum-post-history','forum',12,0),(92,7,'group','view-forum-post-history','forum',13,0),(93,7,'group','create-private-forum-thread','forum',11,0),(94,7,'group','create-private-forum-thread','forum',12,0),(95,7,'group','create-private-forum-thread','forum',13,0),(96,7,'group','create-private-forum-post','forum',11,0),(97,7,'group','create-private-forum-post','forum',12,0),(98,7,'group','create-private-forum-post','forum',13,0),(99,4,'group','view-post-ips','',0,0),(100,4,'group','edit-sprites','',0,0),(101,2,'group','update-own-moods','',0,0),(102,2,'group','view-user-urls','',0,0),(103,4,'group','view-hidden-users','',0,0),(104,4,'group','edit-users','',0,0),(105,3,'user','use-test-bed','',0,0),(126,2,'group','create-pms','',0,0),(127,2,'group','delete-own-pms','',0,0),(128,2,'group','view-own-pms','',0,0),(129,8,'group','edit-title','',0,0),(130,11,'group','create-pms','',0,1),(131,11,'group','delete-own-pms','',0,1),(132,11,'group','view-own-pms','',0,1),(133,2,'group','view-own-sprites','',0,0),(134,2,'group','create-public-post','forum',16,1),(135,2,'group','create-public-thread','forum',16,1),(136,12,'group','view-private-forum','forum',15,0),(137,12,'group','create-private-forum-post','forum',15,0),(138,12,'group','create-private-forum-thread','forum',15,0),(139,4,'group','override-readonly-forums','',0,0),(160,13,'group','edit-forum-thread','forum',1,0),(161,13,'group','delete-forum-thread','',1,0),(162,13,'group','edit-forum-post','',1,0),(163,13,'group','delete-forum-post','',1,0),(164,13,'group','view-forum-post-history','',1,0),(170,10,'group','edit-forum-thread','',2,0),(171,10,'group','delete-forum-thread','',2,0),(172,10,'group','edit-forum-post','',2,0),(173,10,'group','delete-forum-post','',2,0),(174,10,'group','view-forum-post-history','',2,0),(175,10,'group','edit-forum-thread','',3,0),(176,10,'group','delete-forum-thread','',3,0),(177,10,'group','edit-forum-post','',3,0),(178,10,'group','delete-forum-post','',3,0),(179,10,'group','view-forum-post-history','',3,0),(180,14,'group','edit-forum-thread','',2,0),(181,14,'group','delete-forum-thread','',2,0),(182,14,'group','edit-forum-post','',2,0),(183,14,'group','delete-forum-post','',2,0),(184,14,'group','view-forum-post-history','',2,0),(185,9,'group','rename-own-thread','',0,1),(186,4,'group','view-errors','',0,0),(187,4,'group','edit-ip-bans','',0,0),(188,1,'group','view-calendar','',0,0),(189,15,'group','view-calendar','',0,1),(190,10,'group','view-private-forum','',17,0),(191,10,'group','create-private-forum-post','',17,0),(192,10,'group','create-private-forum-thread','',17,0),(193,10,'group','edit-forum-thread','',17,0),(194,10,'group','delete-forum-thread','',17,0),(195,10,'group','edit-forum-post','',17,0),(196,10,'group','delete-forum-post','',17,0),(197,10,'group','view-forum-post-history','',17,0),(198,3,'group','create-all-forums-announcement','',0,0),(199,10,'group','create-forum-announcement','',2,0),(200,10,'group','create-forum-announcement','',3,0),(201,16,'group','view-private-forum','',21,0),(202,16,'group','create-private-forum-post','',21,0),(203,16,'group','create-private-forum-thread','',21,0),(204,16,'group','view-private-category','',9,0),(205,5,'group','view-private-forum','',21,0),(206,5,'group','create-private-forum-post','',21,0),(207,5,'group','create-private-forum-thread','',21,0),(208,16,'group','edit-forum-thread','',21,0),(209,16,'group','delete-forum-thread','',21,0),(210,16,'group','edit-forum-post','',21,0),(211,16,'group','delete-forum-post','',21,0),(212,16,'group','view-forum-post-history','',21,0),(213,7,'user','edit-sprites','',0,0),(214,10,'group','has-displayname','',0,1),(215,10,'group','view-acs-calendar','',0,0),(216,49,'user','view-acs-calendar','',0,0),(217,2,'group','post-radar','',0,0),(218,3,'group','show-as-staff','',0,0),(219,4,'group','show-as-staff','',0,0),(220,8,'group','show-as-staff','',0,0),(221,6,'group','show-as-staff','',0,0),(222,10,'group','track-ip-change','',0,0),(223,2,'group','use-item-shop','',0,0),(224,2,'group','block-layout','',0,0),(226,12,'user','edit-forum-post','',18,0),(227,12,'user','edit-forum-thread','',18,0),(228,12,'user','delete-forum-post','',18,0),(229,12,'user','delete-forum-thread','',18,0),(230,12,'user','view-forum-post-history','',18,0),(232,9,'group','edit-title','',0,1),(234,7,'user','view-user-pms','',0,0),(235,116,'user','create-pms','',0,1),(236,16,'group','edit-forum-thread','',23,0),(237,16,'group','delete-forum-thread','',23,0),(238,16,'group','edit-forum-post','',23,0),(239,16,'group','delete-forum-post','',23,0),(240,16,'group','view-forum-post-history','',23,0),(241,102,'user','view-acs-calendar','',0,0),(243,17,'group','view-post-history','',22,0),(244,16,'group','edit-forum-thread','forum',6,0),(245,102,'user','consecutive-posts','',0,0),(246,7,'user','has-displayname','',0,0),(247,13,'user','edit-forum-post','',18,0),(248,13,'user','delete-post','',18,0),(249,13,'user','view-post-history','',18,0),(250,13,'user','edit-forum-thread','',18,0),(251,13,'user','delete-forum-thread','',18,0),(252,13,'user','consecutive-posts','',0,0),(253,10,'group','view-allranks','',0,0);
/*!40000 ALTER TABLE `x_perm` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-05-25 20:57:57
