-- MySQL dump 10.13  Distrib 5.5.21, for Linux (x86_64)
--
-- Host: localhost    Database: acmlmboard25
-- ------------------------------------------------------
-- Server version	5.5.21-log

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
-- Table structure for table `badges`
--

DROP TABLE IF EXISTS `badges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `badges` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `priority` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `name` varchar(30) NOT NULL DEFAULT '',
  `desc` varchar(30) NOT NULL DEFAULT '',
  `inherit` int(11) DEFAULT NULL,
  `posttext` varchar(10) DEFAULT NULL,
  `effect` varchar(10) DEFAULT NULL,
  `coins` mediumint(8) DEFAULT NULL,
  `coins2` mediumint(8) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `priority` (`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `badges`
--

LOCK TABLES `badges` WRITE;
/*!40000 ALTER TABLE `badges` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `group`
--

LOCK TABLES `group` WRITE;
/*!40000 ALTER TABLE `group` DISABLE KEYS */;
INSERT INTO `group` VALUES (1,'Base User','','','',0,0,100,0,0,''),(2,'Normal User','97ACEF','F185C9','7C60B0',1,1,200,1,1,'Normal Registered User'),(3,'Global Moderator','AFFABE','C762F2','47B53C',8,0,600,1,1,''),(4,'Administrator','FFEA95','C53A9E','F0C413',3,0,700,1,1,''),(6,'Root Administrator','EE4444','E63282','AA3C3C',0,-1,800,1,1,''),(7,'NotRO Moderator','','','',0,0,500,1,0,'Allows moderation of the NotRO forum'),(8,'Local Moderator','D8E8FE','FFB3F3','EEB9BA',10,0,400,1,1,''),(9,'Banned','888888','888888','888888',2,0,0,1,1,''),(10,'Staff','','','',2,0,300,0,0,''),(11,'Disable PM Activity','','','',0,0,1000,1,0,'Disallows all Private Message activity (viewing, creation, deletion)'),(12,'Moogle Participants','','','',0,0,2000,1,0,'Allows viewing/posting the Moogle forum'),(13,'General Forum Moderation','','','',0,0,450,1,0,'Allows moderation of the General Forum'),(15,'Bot','','','',1,0,50,0,0,''),(16,'Developer','33EDCB','FF3399','BCDE9A',2,0,250,1,1,'Board Developer');
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
) ENGINE=MyISAM AUTO_INCREMENT=217 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `items`
--

LOCK TABLES `items` WRITE;
/*!40000 ALTER TABLE `items` DISABLE KEYS */;
INSERT INTO `items` VALUES (0,0,0,'Nothing','Nothing.  At All.',0,'aaaaaaaaa',0,0,0,0,0,0,0,0,0,0,0),(2,1,1,'Kitchen knife','Stabbity Stabbity.',0,'aaaaaaaaa',0,0,11,1,0,0,1,0,1,200,0),(1,1,1,'Plastic knife','Stabbity Stabbity, but much less painful.',0,'aaaaaaaaa',0,0,5,0,0,0,0,0,0,50,0),(3,1,1,'Butcher knife','Now with red paint coating!',0,'aaaaaaaaa',5,0,21,2,0,0,2,0,1,500,0),(4,1,1,'Short sword','Gets the Urist McDwarf Seal of Approval!',0,'aaaaaaaaa',13,-1,32,5,-1,0,0,0,-1,1000,0),(5,1,1,'Broad sword','Long flat pain.',0,'aaaaaaaaa',21,-2,54,8,-2,0,0,0,-1,4000,0),(7,1,1,'Iron sword','Guaranteed to cut almost anything.',0,'aaaaaaaaa',48,-3,88,11,-3,0,-1,0,-3,12000,0),(8,1,1,'Bronze sword','THIS! IS! BRONZE!',0,'aaaaaaaaa',65,-5,136,16,-5,0,-2,0,-5,21500,0),(9,1,1,'Silver sword','Shiny.',0,'aaaaaaaaa',93,-8,182,34,-8,0,-5,10,-8,46000,0),(10,1,1,'Gold sword','Shinier.',0,'aaaaaaaaa',126,-10,267,53,-5,5,-10,50,-15,90000,0),(12,1,1,'Dragon sword','You won\'t have any trouble \'dragon\' this one around.',0,'aaaaaaaaa',259,0,726,120,0,33,-38,80,-75,380000,0),(11,1,1,'Crystal sword','Refractive goodness',0,'aaaaaaaaa',139,5,409,86,5,12,-12,65,-23,150000,0),(14,1,2,'Short axe','Short, stout, and sharp.',0,'mmaaaaaam',105,95,66,5,-8,-8,-11,0,92,2500,0),(15,1,2,'Large axe','Bigger.  Sharper.  Meaner.',0,'mmaaaaaam',110,88,102,8,-21,-18,-16,0,80,8000,0),(16,1,2,'Bronze axe','I\'d like to place an order for 300, please.',0,'mmaaaaaam',116,82,197,15,-39,-36,-45,0,72,32000,0),(17,1,2,'Silver axe','Shiny slicer',0,'mmaaaaaam',119,79,302,25,-68,-52,-71,5,69,66000,0),(18,1,2,'Gold axe','Shinier Slicier',0,'mmaaaaaam',136,75,487,46,-108,-101,-126,12,55,120000,0),(20,1,2,'Dragon axe','...Is this thing even legal?',0,'mmaaaaaam',152,67,1339,70,-161,-150,-192,35,47,500000,0),(13,1,2,'Wooden axe','Not going to cut much with this',0,'mmaaaaaam',102,98,35,2,-4,-3,-5,0,95,600,0),(21,1,3,'Small bow','whif whif whif',0,'aaaaaaaaa',0,0,19,0,0,0,5,0,3,400,0),(22,1,3,'Wooden bow','Useless.',0,'aaaaaaaaa',0,0,33,0,1,0,9,0,5,1500,0),(23,1,3,'Elven bow','Now with 3x firing!',0,'aaaaaaaaa',0,0,52,0,2,0,15,0,8,6000,0),(24,1,3,'Bronze crossbow','Heavy.',0,'aaaaaaaaa',0,0,121,0,5,0,26,0,13,20000,0),(25,1,3,'Silver crossbow','Shiny Heavy',0,'aaaaaaaaa',0,0,166,0,11,0,39,10,18,48000,0),(26,1,3,'Gold crossbow','Shinier Heavier',0,'aaaaaaaaa',0,0,238,0,20,0,61,25,32,100000,0),(27,1,3,'Dragon crossbow','OH SHI-',0,'aaaaaaaaa',0,0,650,0,36,0,159,46,64,400000,0),(28,1,4,'Shiny stick','It\'s stuck.',0,'mmaaaaaaa',95,105,4,0,5,3,1,5,0,250,0),(29,1,4,'Rainbow stick','Multicoloured fun!',0,'mmaaaaaaa',93,107,10,0,16,11,2,15,0,1390,0),(30,1,4,'Wooden staff','Ow, my back..',0,'mmaaaaaaa',88,115,27,3,36,29,0,0,-2,5200,0),(31,1,4,'Light staff','Yagami?',0,'mmaaaaaaa',85,121,67,5,77,59,0,0,-3,18000,0),(32,1,4,'Sapphire staff','Water Magic',0,'mmaaaaaaa',81,130,97,6,126,103,0,0,-4,45000,0),(33,1,4,'Ruby staff','Fire Magic',0,'mmaaaaaaa',79,146,119,9,179,152,0,0,-6,95000,0),(34,1,4,'Crystal staff','Easily Shattered Magic',0,'mmaaaaaaa',75,180,161,12,260,239,0,0,-8,210000,0),(35,1,4,'Dragon staff','Dead Dragon.',0,'mmaaaaaaa',70,229,216,15,438,423,0,0,-15,480000,0),(36,2,1,'Clothes','No Please, keep them on.',0,'aaaaaaaaa',0,0,0,3,0,0,0,0,0,45,0),(37,2,1,'Fur coat','PETA is going to have a field day with this',0,'aaaaaaaaa',0,0,0,8,0,1,0,1,0,180,0),(38,2,1,'Leather armor','Dwarven Sized',0,'aaaaaaaaa',0,0,0,15,0,1,0,0,0,650,0),(39,2,1,'Iron armor','Metal. Heavy. Strong.',0,'aaaaaaaaa',0,0,0,56,0,2,-2,0,-3,9500,0),(40,2,1,'Heavy armor','Only Stravich could ever wear this.',0,'aaaaaaaaa',0,0,0,152,-5,3,-10,-5,-13,26500,0),(41,2,1,'Stone armor','Is this some kind of joke?!',0,'aaaaaammm',0,0,-13,446,-21,35,46,53,55,85000,0),(42,2,1,'Gold armor','Perfect for target practice...  and you\'d be the target',0,'aaaaaaaaa',0,0,0,373,0,23,0,12,-15,129000,0),(43,2,1,'Dragon armor','..ew.',0,'maaaaaaaa',133,0,25,669,0,165,0,54,0,490000,0),(44,2,1,'Spiked armor','DON\'T HIT THIS',0,'aaaaaaaaa',0,0,84,127,-17,0,-5,0,-24,32000,0),(45,2,1,'Copper armor','Electrically conductive',0,'aaaaaaaaa',0,0,0,34,0,1,-1,0,-1,2300,0),(46,2,1,'Silver armor','Shiny Protection',0,'aaaaaaaaa',0,0,0,247,0,14,0,5,-15,47000,0),(47,2,2,'Light robe','Not heavy at all.',0,'aaaaaaaaa',0,8,0,17,13,12,-3,0,0,3500,0),(48,2,2,'Magician robe','Magically enhanced for superior casting',0,'mmaaaaaaa',96,112,0,39,44,36,-5,0,-1,15200,0),(49,2,2,'Shining robe','MY EYES',0,'mmaaaaaaa',93,126,0,95,103,97,-5,20,-2,39000,0),(50,2,2,'Rainbow robe','Straight from the 60\'s',0,'mmaaaaama',95,138,0,167,182,176,0,200,0,109000,0),(51,2,1,'Shell armor','100% Pure Red Koopa',0,'maaaaamam',160,0,0,795,0,326,33,121,33,280000,0),(52,2,2,'Crystal robe','I would not want to wear this when it shatters',0,'mmaaaaaam',90,166,0,276,280,269,0,24,90,220000,0),(53,2,2,'Dragon robe','Dragon Lovers Lament',0,'mmaaaaaaa',83,202,5,363,380,376,0,46,0,480000,0),(54,3,1,'Plastic plate','Good for eating off of.  Not so good as a shield.',0,'aaaaaaaaa',0,0,0,2,0,0,0,0,0,40,0),(55,3,1,'Wooden plank','I\'m board',0,'aaaaaaaaa',0,0,4,5,0,0,-1,-1,-1,200,0),(56,3,1,'Wood shield','Good for defending against a wooden axe.',0,'aaaaaaaaa',0,0,2,12,0,0,-2,0,-2,850,0),(57,3,1,'Iron shield','Decent Protecting for a decent price',0,'aaaaaaaaa',0,0,7,43,0,0,-4,0,-5,4800,0),(58,3,1,'Light shield','Blindingly good.',0,'aaaaaaaaa',3,3,2,45,2,0,0,3,0,6000,0),(59,3,1,'Spiked shield','Also good as a weapon.',0,'aaaaaaaaa',0,0,86,75,-16,0,-8,-6,-9,23000,0),(60,3,1,'Silver shield','Shiny Shield',0,'aaaaaaaaa',0,0,13,145,0,12,-5,8,-9,30000,0),(61,3,1,'Gold shield','Shinier shield',0,'aaaaaaaaa',0,0,20,263,0,34,-10,29,-15,97000,0),(62,3,1,'Dragon shield','Certainly strong enough',0,'mmaaaaaaa',120,110,126,409,33,169,0,102,0,460000,0),(64,6,6,'Goggles','THEY DO NOTHING',0,'aaaaaaaaa',0,0,0,0,0,0,0,0,0,25,0),(63,1,1,'Rubber Spatula','Flap Flap Smack',0,'aaaaaaaaa',0,0,5,0,-2,0,2,0,0,100,0),(65,5,5,'Steel-toed Flip-flops','Click, Click',0,'aaaaaaaaa',0,0,0,5,0,0,0,0,-1,75,0),(-1,99,0,'New Item','Desc Here',0,'mmaaaaaaa',100,100,0,0,0,0,0,0,0,0,0),(187,99,98,'Semi-automated IRC bot system+','Power! At the distance of merely a few keystrokes, UNLIMITED POWERRRRR!!!!',0,'aaaaaaaaa',1,2,256,64,256,128,16,-64,64,0,999),(76,1,5,'Dagger','Short Stabbity',0,'aaaaaaaaa',0,3,25,0,2,0,5,1,5,800,0),(77,1,5,'Dirk','Pitt',0,'aaaaaaaaa',0,10,49,0,7,0,13,2,13,3400,0),(78,1,5,'Stilleto','...oh...',0,'aaaaaaaaa',0,27,77,0,19,0,28,4,28,10900,0),(81,1,5,'Kris','Wicked looking blade there, chap',0,'aaaaaaaaa',100,100,200,50,50,0,40,0,40,44500,0),(80,1,5,'Thief\'s Dagger','What, does it grant +4 Sneak or something?',0,'aaaaaaaaa',0,53,113,0,43,0,51,9,51,21300,0),(82,1,5,'Fairy\'s Dagger','Fair(l)y good.',0,'aaaaaaaaa',0,145,220,0,183,133,113,35,113,100000,0),(83,1,5,'Demon Fangs','Fangs for the memories~',0,'aaaaaaaaa',30,222,430,-30,233,0,127,15,127,220000,0),(84,1,5,'Orichalcon','Give that back, Isaac!',0,'aaaaaaaaa',0,400,700,100,300,100,150,25,150,475000,0),(85,1,6,'Short Spear','Short.',0,'aaaaaaaaa',0,0,40,0,0,0,0,0,0,1200,0),(86,1,6,'Pike','Long.',0,'aaaaaaaaa',0,0,73,0,0,0,0,0,0,4800,0),(87,1,6,'Enchanted Rake','Strong.',0,'aaaaaaaaa',10,10,190,10,10,10,10,10,10,33000,0),(88,1,6,'Soldier Spear','TONIGHT...',0,'aaaaaaaaa',10,0,136,10,-5,-5,0,0,-5,12000,0),(89,1,6,'Heavy Spear','WE DINE...',0,'aaaaaaaaa',0,0,323,0,0,0,0,0,-5,68000,0),(90,1,6,'Gugnir Lance','IN HELL!',0,'aaaaaaaaa',0,0,707,0,0,0,0,0,-10,330000,0),(91,1,6,'Holy Longinus','Painful',0,'aaaaaaaaa',50,50,1000,50,0,50,0,0,0,600000,0),(92,1,7,'Mumei','Sharp',0,'maaaaaaaa',98,1,43,-5,0,0,1,0,2,1100,0),(93,1,7,'Kunishige','Long, Sharp, Painful',0,'maaaaaaaa',95,2,80,-12,0,0,2,0,5,3800,0),(94,1,7,'Kotetsu','Long, Sharp, Painful',0,'maaaaaaaa',92,5,152,-27,0,0,5,0,10,11500,0),(95,1,7,'Osafune','Long, Sharp, Painful',0,'maaaaaaaa',88,10,257,-43,0,0,10,0,20,29800,0),(96,1,7,'Magaroku','Long, Sharp, Painful',0,'maaaaaaaa',82,15,413,-72,0,0,20,0,40,57000,0),(97,1,7,'Masamune','Long, Sharp, Painful',0,'maaaaaaaa',80,20,665,-38,0,0,40,0,80,125000,0),(98,1,7,'Muramasa','Long, Sharp, Painful',0,'maaaaaaaa',82,25,878,-50,0,0,64,0,128,310000,0),(99,1,7,'Amenohabakiri','Long, Sharp, Painful',0,'maaaaaaaa',85,30,1250,-100,0,100,100,0,200,550000,0),(100,99,99,'Master\'s Armor','',0,'mmamaaaaa',500,500,0,500,0,0,0,0,0,999999,378),(101,99,99,'Master\'s Sword','',0,'aamaaamam',0,0,500,0,0,0,500,0,500,999999,378),(102,99,99,'Master\'s Shield','',0,'aaamamaaa',0,0,0,500,0,500,0,0,0,999999,378),(108,99,98,'Metallic Ash','Yeah.  <i>perfect</i> sense.',0,'mmmmmmmmm',600,800,600,400,800,600,400,400,800,999999,0),(105,99,99,'Master\'s Boots','',0,'aaaaaammm',0,0,0,0,0,0,500,500,500,999999,378),(106,99,99,'Master\'s Helm','',0,'aaamaamaa',0,0,0,500,0,0,500,0,0,999999,378),(107,99,99,'Master\'s Stylish Timepiece','',0,'mmmmmmmmm',500,500,500,500,500,500,500,500,500,999999,378),(109,99,98,'Dimensional Rain Blades','',0,'aaaaaaaaa',1000,2000,5000,1000,2000,1000,1000,1000,2000,999999,0),(110,99,98,'Elysian Aura Shield','',0,'aaaaaaaaa',1000,1000,0,1000,0,1000,0,0,0,999999,0),(111,99,98,'Demoness Overlord Attire','Put your clothes back on, Etna',0,'aaaaaaaaa',0,0,0,2000,0,1000,0,0,500,999999,0),(112,99,98,'Demoness Overlord Crown','Etna\'s favorite',0,'aaaaaaaaa',0,2000,0,0,1000,1000,0,0,0,999999,0),(113,99,98,'Demoness Overlord High Heels','Etna kicked these at me a few days ago when she got called \'flat-chested\'',0,'aaaaaaaaa',0,0,0,500,0,500,0,0,1000,999999,0),(114,99,98,'Beauty Queen Title Hack','',0,'aaaaaaaaa',5000,5000,5000,5000,5000,5000,5000,5000,5000,999999,0),(119,99,99,'Grammar Hammer&trade;','',0,'mmaaaaaaa',100,300,176,34,795,273,0,0,-50,999999,9001),(120,100,254,'<b>SUPER CATGIRL BEAM</b>','',0,'aamaaaaaa',0,0,200,0,0,0,0,0,0,1337357,1337357),(121,100,1,'GIGANTIC CATGIRL RIDE-ARMOR','',0,'aaamaaaaa',0,0,0,200,0,0,0,0,0,1337357,1337357),(123,4,99,'Jedi training helmet','Shields from distractive optical sensations (like post layouts) and allows the aspiring forum knight to focus his mind on the actual post contents.',0,'aaaaaaaaa',0,10,0,4,0,15,-5,0,0,991,0),(137,128,98,'Cosplay Fursuit','Smells like wet fur.',0,'mmaaaaama',50,50,-200,-200,-50,-50,-50,1,-200,-50,0),(135,128,98,'Catgirl ears','So cute they\'re guaranteed to make people go \"KAWAII! ^_^\", or your money back!',0,'mmaaaaama',100,100,0,0,0,0,0,200,0,9987,0),(136,128,98,'Furry tail','\"Costume sold separately\"',0,'mmaaaaaaa',100,100,0,0,-1,0,0,-1,0,-1,0),(134,128,98,'Spare Brain','A spare brain in a jar, in case your main one dies from too much *chan.',0,'mmaamaaaa',100,100,0,0,200,0,0,0,0,0,75),(133,128,98,'Aleph-null ','Able to bore even the highest minds to sleep with maths that make no sense.',0,'mmaaaaaaa',999,999,9999,9999,9999,9999,9999,9999,9999,0,1024768),(132,128,98,'Keg of Booze','1.5 bushels of the finest brew. Don\'t drink it all at once, NSNick :P',0,'mmaaaaaaa',400,400,151,151,-75,151,-150,151,151,0,1510),(131,128,98,'401 Beer Mug','An empty beer mug.',0,'mmaaaaaaa',100,100,0,401,0,401,0,0,0,0,401),(130,128,98,'Alfa Romeo','This car is AWESOME',0,'mmaaaaaaa',100,100,0,0,0,0,1337,0,13373,0,31337),(115,4,1,'Paper Hat','Made out of newspaper. Used in elementary school playground games.',0,'aaaaaaaaa',0,0,0,0,0,0,0,1,0,50,0),(116,4,1,'Tinfoil Hat','Shields you from government mind control. Also shields you from being regarded as a sane person.',0,'aaaaaaaaa',0,0,0,2,-1,1,0,0,0,460,0),(117,4,1,'Fedora Hat','Runs Linux.',0,'aaaaaaaaa',0,0,0,3,0,0,0,0,1,840,0),(118,4,1,'Steel-plated Party Hat','The best precaution you can take if you want to party hard.',0,'aaaaaaaaa',0,0,4,7,0,1,0,2,0,3100,0),(126,5,6,'Nuclear Rocket Boots','Einstein was right, oh shi-',0,'aaamaaaam',0,0,0,50,0,0,0,0,300,15860,0),(139,99,98,'The Database','Don\'t mess with the DB.',0,'mammmammm',1400,900,800,3000,500,5000,500,500,500,999999,0),(140,99,98,'Kasa\'s Sword','Because every evil shadowy figure needs a good weapon.',0,'mmmaaaaaa',500,500,12000,6000,32767,32767,32767,5000,6000,999999,0),(138,99,98,'Admin\'s Invincibility','You don\'t get this.',0,'amamamama',0,810,0,500,0,450,0,575,0,999999,0),(142,4,1,'Gurren Lagann\'s Helmet','Gurren-Lagann\'s helmet: Just make sure you stay away from Viral whilst wearing this.',0,'mmaaaaaaa',150,100,0,0,0,0,0,0,0,12000,0),(143,4,1,'Marisa\'s Hat','She stole the precious thing.',0,'mmaaaaaaa',100,110,0,0,0,0,0,0,0,7777,0),(144,4,1,'Matter-Less Hat','Super cool hat. Now 100% matter free for those allergic to anything but nothingness.  No idea how it raises MP, but it does.',0,'maaaaaaaa',100,75,0,0,0,0,0,0,0,3975,0),(145,4,1,'Captain Falcon\'s Helmet','Lots of speed, but watch out for Goroh!  Oh, and this won\'t give you the punch, either.',0,'mmaaaaaam',100,100,0,0,0,0,0,0,200,4580,0),(146,4,1,'Plumber\'s Hat','What the @#$%, you took this off of Acmlm didn\'t you?!',0,'mmaaaaaaa',100,100,0,0,0,0,0,0,0,1985,0),(147,4,2,'Kamina\'s Hatglasses','There\'s something you don\'t see every day',0,'aaaaaaaaa',0,0,50,5,-120,0,0,0,0,5510,0),(163,3,5,'Holoshield','No idea where the Technology came from, but it\'s a worthwhile investment if you can afford it.',0,'maamamaaa',140,0,0,500,0,450,0,0,50,1025000,0),(148,1,5,'Tsukasa\'s Wand','Ludicrous World we live in. Using this item gives you great power, but traps you in the board. You may be able to call upon your guardian for assistance.',0,'mmaaaaaaa',100,100,0,0,40,0,120,0,10,20020,0),(149,1,5,'Abracadabra','Generic Wand.  Does magical stuff.  Will not accidentally backfire on you like Ron\'s does.',0,'mmaaaaaaa',100,116,0,0,60,0,0,0,0,7845,0),(150,1,5,'Kamek\'s Wand','Useful for enlarging little things so they can devour babies',0,'mmaaaaaaa',100,180,0,0,200,0,0,0,0,23000,0),(151,1,5,'Generification Wand','What the hell, this isn\'t even magical!  Increases code complexity, headaches, and attractiveness of alcohol.',0,'mmaaaaaaa',117,100,165,0,-265,0,0,-5,0,15400,0),(152,99,98,'Kasa\'s baseball cap','Sporting the latest great design, this cap screams designer.  And it\'s made of woven adamantine, too!',0,'maamamaaa',110,18000,6000,500,9000,500,9000,9000,9000,999999,0),(153,4,1,'Baseball Cap','A regular baseball cap, with an unintelligible logo on the back.',0,'aaaaaaaaa',0,0,0,50,-5,0,0,25,0,560,0),(154,5,1,'Rollerskates','No brakes, just (bone) breaks.',0,'mmaaaaaam',110,90,0,0,0,0,0,0,125,9850,0),(155,5,1,'Sneakers','Squeaky.',0,'aaaaaaaam',0,0,50,0,0,0,160,0,98,12500,0),(156,5,1,'Plumber\'s Boots','Boi-oi-oing!',0,'aaaaaaaaa',50,0,800,0,0,0,0,333,50,29850,0),(157,5,1,'Boots ME','I heard they were made in a week',0,'maaaaaaaa',110,0,0,0,-900,0,0,-1000,0,50,0),(158,5,1,'Socks','Great for DDR, not so great considering the needle you just stepped in',0,'aaaaaaaaa',0,0,0,0,10,0,7,0,0,3400,0),(159,6,1,'Ancient Tome','Increases magical ability, but it\'s pretty darned big...',0,'aaaaaaaaa',0,375,0,0,0,0,-225,0,0,12450,0),(160,6,1,'Boom Box','For when you <i>really</i> like your music.  Heavy, too.',0,'aaaaaaaaa',25,0,65,0,-15,0,50,-25,-50,23500,0),(161,6,1,'Laptop Computer','Because we all like to look at our \"image\" collections on the go.',0,'aaaaaaaaa',0,0,0,0,50,0,0,5,0,17500,0),(162,6,1,'Scouter','What does the scouter say about his powerlevel?',0,'aaaaaaaaa',10,50,0,0,-5,0,0,-20,0,9001,0),(164,1,1,'Beamsabre','Lightweight and powerful, but very expensive',0,'aamaaaaaa',0,0,150,0,0,0,-5,0,20,1065000,0),(165,2,1,'Cleric\'s robe','Sorry, I don\'t think the Geneva Convention covers clerics...',0,'aaaaaaaaa',0,110,0,20,5,60,0,0,-20,57800,0),(166,2,1,'Ragged Cloak','Great if you\'re stuck in a desert, out of place in hgih society.',0,'aaaaaaaam',0,0,0,230,0,0,0,0,94,38900,0),(167,2,1,'Biker\'s Leather Jacket','Yeah, because you\'re so <i>infinitely</i> tough.',0,'aaaaaaamm',0,0,5,453,-30,0,0,75,95,75800,0),(168,2,1,'Semtex Vest','On the plus side, nobody is <i>ever</i> going to come anywhere NEAR you.',0,'maaaaaama',5,0,0,500,0,0,0,5,0,25000,0),(169,2,1,'Ghoulish Rags','Complete with ball and chains',0,'aaaaaaamm',0,0,0,156,0,0,0,66,33,14900,0),(170,5,1,'Slippers','Warm and Comfy',0,'aaaaaaaaa',0,0,5,0,0,0,10,0,-5,4500,0),(6,5,1,'High-Heels','Anklesnappers',0,'aaaaaaaaa',0,0,25,0,5,0,0,-50,0,14890,0),(171,5,6,'Plated Low Boots','Fancy futuristic boots, but apart from the price tag they\'re not all that great.',0,'aaaaaaaaa',0,0,235,45,0,0,35,0,90,1078000,0),(172,3,1,'Woven Steel Teardrop','A strong, relatively lightweight shield that also folds up into an airplane-friendly carrying bag!',0,'aaaaaaaaa',0,0,0,155,0,0,0,0,0,37500,0),(173,3,1,'Tower Shield','Might be strong, but what, you thought you were going somewhere?',0,'aaaaaaaaa',0,0,0,375,0,0,0,0,-600,28950,0),(174,6,1,'Keyboard','TAKA TAKA TAKA',0,'aaaaaaaaa',-20,10,0,0,50,0,0,0,15,17500,0),(175,6,1,'Mountain Dew','Coding Fuel',0,'aaaaaaaaa',25,0,0,0,25,0,0,0,0,8500,0),(176,6,1,'Booze','Hit the Ballmer\'s Peak and you\'ll be a super coder.  Miss it and you\'re not driving yourself home tonight.',0,'aaaaaaaaa',-25,-25,0,-15,50,0,0,100,0,24000,0),(177,6,1,'PHP','The language of Champions',0,'aaaaaaaaa',0,0,0,0,175,0,0,0,0,45000,0),(178,6,1,'Carpenter\'s Belt','Because sometimes you just want to look busy.',0,'aaaaaaaaa',0,0,20,0,0,0,35,0,0,12500,0),(179,6,1,'Yamaha','Because everyone likes to make crappy music!',0,'aaaaaaaaa',0,0,0,0,15,0,0,0,-5,11500,0),(185,1,10,'Crowbar','Does that say Black Mesa on the side?',0,'aaaaaaaaa',0,0,80,0,0,0,0,0,0,8500,0),(189,1,0,'Medigun','Wait, what happened to <i>hurting</i> your enemies?',0,'mmaaaaaam',100,100,0,0,30,0,0,0,80,64500,0),(216,4,0,'Big Red Pimp Hat','Desc Here',1,'mmaaaaaaa',100,120,0,0,3,0,0,0,0,0,0),(201,5,0,'High Jump Boots','Sexy Chozo Technology',0,'mmmamamaa',150,150,300,0,115,0,200,130,60,85000,0),(202,4,0,'French Beret','Zut alors?!',0,'mmaaaaaaa',100,100,0,5,2,0,0,50,0,4500,0),(203,4,0,'Green Cap','Wait up, Bro!',0,'mmaaaaaam',100,120,0,0,50,0,20,0,85,64000,0),(204,5,0,'Spike Stompers','I feel sorry for anyone <i>stuck</i> under you',0,'mmmmaaaaa',100,100,125,105,0,0,0,0,75,400,2),(205,3,0,'Wsithengamot','Durable, but heavy',0,'mmamaaaam',100,100,0,120,0,0,-150,0,85,15000,0),(208,1,0,'Hyper Beam','A weapon wrought from stolen evil.',0,'aaaaaaaaa',0,0,32767,0,0,0,0,275,100,8388607,558),(214,4,0,'Top Hat','The ultimate in exquisite fashion.  Sure to improve your confidence and luck with the ladies',0,'mmaaaaaaa',100,110,0,0,0,0,0,3,0,12500,0),(215,6,0,'Cane','Nothing quite like it.  Sure to make you look as distinquished and old as you aren\'t.',0,'mmaaaaaaa',100,100,15,0,0,0,0,0,5,12500,0);
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
INSERT INTO `perm` VALUES ('banned','Is Banned','',2,''),('capture-sprites','Capture Sprites','',1,''),('consecutive-posts','Consecutive Posts','',2,''),('create-all-forums-announcement','Create All Forums Announcement','',4,''),('create-all-private-forum-posts','Create All Private Forum Posts','',3,''),('create-all-private-forum-threads','Create All Private Forum Threads','',3,''),('create-forum-announcement','Create Forum Announcement','',3,'forum'),('create-pms','Create PMs','',1,''),('create-private-forum-post','Create Private Forum Post','',2,'forums'),('create-private-forum-thread','Create Private Forum Thread','',2,'forums'),('create-public-post','Create Public Post','',4,''),('create-public-thread','Create Public Thread','',4,''),('delete-forum-post','Delete Forum Post','',2,'forums'),('delete-forum-thread','Delete Forum Thread','',2,'forums'),('delete-own-pms','Delete Own PMs','',1,''),('delete-post','Delete Post','',2,''),('delete-thread','Delete Thread','',2,''),('delete-user-pms','Delete User PMs','',3,''),('edit-attentions-box','Edit Attentions Box','',3,''),('edit-categories','Edit Categories','',3,''),('edit-forum-post','Edit Forum Post','',2,'forums'),('edit-forum-thread','Edit Forum Thread','',2,'forums'),('edit-forums','Edit Forums','',3,''),('edit-ip-bans','Edit IP Bans','',0,''),('edit-moods','Edit Moods','',3,''),('edit-permissions','Edit Permissions','',3,''),('edit-sprites','Edit Sprites','',3,''),('edit-title','Edit Title','',3,''),('edit-users','Edit Users','',3,''),('has-displayname','Can Use Displayname','',3,''),('ignore-thread-time-limit','Ignore Thread Time Limit','',0,''),('login','Login','',1,''),('mark-read','Mark Read','',1,''),('no-restrictions','No Restrictions','',3,''),('override-readonly-forums','Override Read Only Forums','',3,''),('post-radar','Post Radar','Can use Post Radar',2,''),('rate-thread','Rate Thread','',1,''),('register','Register','',1,''),('rename-own-thread','Rename Own Thread','',1,''),('show-as-staff','Listed Publicly as Staff','',3,'users'),('staff','Is Staff','',2,''),('update-own-moods','Update Own Moods','',1,''),('update-own-post','Update Own Post','',4,''),('update-own-profile','Update Own Profile','',1,''),('update-post','Update Post','',2,''),('update-profiles','Update Profiles','',3,''),('update-thread','Update Thread','',2,''),('update-user-moods','Update User Moods','',3,'users'),('update-user-profile','Update User Profile','',3,'users'),('use-item-shop','Use Item Shop','',1,''),('use-post-layout','Use Post Layout','',4,''),('use-test-bed','Use Test Bed','',3,''),('use-uploader','Use Uploader','',1,''),('view-acs-calendar','View ACS Rankings Calendar','',2,''),('view-all-private-categories','View All Private Categories','',3,''),('view-all-private-forums','View All Private Forums','',3,''),('view-all-private-posts','View All Private Posts','',3,''),('view-all-private-threads','View All Private Threads','',3,''),('view-all-sprites','View All Sprites','',3,''),('view-calendar','View Calendar','',1,''),('view-errors','View PHP Errors','',0,''),('view-forum-post-history','View Forum Post History','',2,'forums'),('view-hidden-users','View Hidden Users','',3,''),('view-own-pms','View Own PMs','',1,''),('view-own-sprites','View Own Sprites','',1,''),('view-permissions','View Permissions','',3,''),('view-post-history','View Post History','',2,''),('view-post-ips','View Post IP Addresses','',3,''),('view-private-category','View Private Category','',2,'categories'),('view-private-forum','View Private Forum','',2,'forums'),('view-private-post','View Private Post','',2,'posts'),('view-private-thread','View Private Thread','',2,'threads'),('view-profile-page','View Profile Page','',1,''),('view-public-categories','View Public Categories','',1,''),('view-public-forums','View Public Forums','',1,''),('view-public-posts','View Public Posts','',1,''),('view-public-threads','View Public Threads','',1,''),('view-user-pms','View User PMs','',3,''),('view-user-urls','View User URLs','',3,'');
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
INSERT INTO `ranks` VALUES (1,0,'Non-poster'),(1,1,'Newcomer'),(1,20,'<img src=img/ranks/goomba.gif width=16 height=16><br>Goomba'),(1,10,'<img src=img/ranks/microgoomba.gif width=8 height=9><br>Micro-Goomba'),(1,35,'<img src=img/ranks/redgoomba.gif width=16 height=16><br>Red Goomba'),(1,50,'<img src=img/ranks/redparagoomba.gif width=20 height=24><br>Red Paragoomba'),(1,65,'<img src=img/ranks/paragoomba.gif width=20 height=24><br>Paragoomba'),(1,80,'<img src=img/ranks/shyguy.gif width=16 height=16><br>Shyguy'),(1,100,'<img src=img/ranks/koopa.gif width=16 height=27><br>Koopa'),(1,120,'<img src=img/ranks/redkoopa.gif width=16 height=27><br>Red Koopa'),(1,140,'<img src=img/ranks/paratroopa.gif width=16 height=28><br>Paratroopa'),(1,160,'<img src=img/ranks/redparatroopa.gif width=16 height=28><br>Red Paratroopa'),(1,180,'<img src=img/ranks/cheepcheep.gif width=16 height=16><br>Cheep-cheep'),(1,200,'<img src=img/ranks/redcheepcheep.gif width=16 height=16><br>Red Cheep-cheep'),(1,225,'<img src=img/ranks/ninji.gif width=16 height=16><br>Ninji'),(1,250,'<img src=img/ranks/flurry.gif width=16 height=16><br>Flurry'),(1,275,'<img src=img/ranks/snifit.gif width=16 height=16><br>Snifit'),(1,300,'<img src=img/ranks/porcupo.gif width=16 height=16><br>Porcupo'),(1,325,'<img src=img/ranks/panser.gif width=16 height=16><br>Panser'),(1,350,'<img src=img/ranks/mole.gif width=16 height=16><br>Mole'),(1,375,'<img src=img/ranks/beetle.gif width=16 height=16><br>Buzzy Beetle'),(1,400,'<img src=img/ranks/nipperplant.gif width=16 height=16><br>Nipper Plant'),(1,425,'<img src=img/ranks/bloober.gif width=16 height=16><br>Bloober'),(1,450,'<img src=img/ranks/busterbeetle.gif width=16 height=15><br>Buster Beetle'),(1,475,'<img src=img/ranks/beezo.gif width=16 height=16><br>Beezo'),(1,500,'<img src=img/ranks/bulletbill.gif width=16 height=14><br>Bullet Bill'),(1,525,'<img src=img/ranks/rex.gif width=20 height=32><br>Rex'),(1,550,'<img src=img/ranks/lakitu.gif width=16 height=24><br>Lakitu'),(1,575,'<img src=img/ranks/spiny.gif width=16 height=16><br>Spiny'),(1,600,'<img src=img/ranks/bobomb.gif width=16 height=16><br>Bob-Omb'),(1,700,'<img src=img/ranks/spike.gif width=32 height=32><br>Spike'),(1,675,'<img src=img/ranks/pokey.gif width=18 height=64><br>Pokey'),(1,650,'<img src=img/ranks/cobrat.gif width=16 height=32><br>Cobrat'),(1,725,'<img src=img/ranks/hedgehog.gif width=16 height=24><br>Melon Bug'),(1,750,'<img src=img/ranks/lanternghost.gif width=26 height=19><br>Lantern Ghost'),(1,775,'<img src=img/ranks/fuzzy.gif width=32 height=31><br>Fuzzy'),(1,800,'<img src=img/ranks/bandit.gif width=23 height=28><br>Bandit'),(1,830,'<img src=img/ranks/superkoopa.gif width=23 height=13><br>Super Koopa'),(1,860,'<img src=img/ranks/redsuperkoopa.gif width=23 height=13><br>Red Super Koopa'),(1,900,'<img src=img/ranks/boo.gif width=16 height=16><br>Boo'),(1,925,'<img src=img/ranks/boo2.gif width=16 height=16><br>Boo'),(1,950,'<img src=img/ranks/fuzzball.gif width=16 height=16><br>Fuzz Ball'),(1,1000,'<img src=img/ranks/boomerangbrother.gif width=60 height=40><br>Boomerang Brother'),(1,1050,'<img src=img/ranks/hammerbrother.gif width=60 height=40><br>Hammer Brother'),(1,1100,'<img src=img/ranks/firebrother.gif width=60 height=24><br>Fire Brother'),(1,1150,'<img src=img/ranks/firesnake.gif width=45 height=36><br>Fire Snake'),(1,1200,'<img src=img/ranks/giantgoomba.gif width=24 height=23><br>Giant Goomba'),(1,1250,'<img src=img/ranks/giantkoopa.gif width=24 height=31><br>Giant Koopa'),(1,1300,'<img src=img/ranks/giantredkoopa.gif width=24 height=31><br>Giant Red Koopa'),(1,1350,'<img src=img/ranks/giantparatroopa.gif width=24 height=31><br>Giant Paratroopa'),(1,1400,'<img src=img/ranks/giantredparatroopa.gif width=24 height=31><br>Giant Red Paratroopa'),(1,1450,'<img src=img/ranks/chuck.gif width=28 height=27><br>Chuck'),(1,1500,'<img src=img/ranks/thwomp.gif width=44 height=32><br>Thwomp'),(1,1550,'<img src=img/ranks/bigcheepcheep.gif width=24 height=32><br>Boss Bass'),(1,1600,'<img src=img/ranks/volcanolotus.gif width=32 height=30><br>Volcano Lotus'),(1,1650,'<img src=img/ranks/lavalotus.gif width=24 height=32><br>Lava Lotus'),(1,1700,'<img src=img/ranks/ptooie2.gif width=16 height=43><br>Ptooie'),(1,1800,'<img src=img/ranks/sledgebrother.gif width=60 height=50><br>Sledge Brother'),(1,1900,'<img src=img/ranks/boomboom.gif width=28 height=26><br>Boomboom'),(1,2000,'<img src=img/ranks/birdopink.gif width=60 height=36><br>Birdo'),(1,2100,'<img src=img/ranks/birdored.gif width=60 height=36><br>Red Birdo'),(1,2200,'<img src=img/ranks/birdogreen.gif width=60 height=36><br>Green Birdo'),(1,2300,'<img src=img/ranks/iggy.gif width=28><br>Larry Koopa'),(1,2400,'<img src=img/ranks/morton.gif width=34><br>Morton Koopa'),(1,2500,'<img src=img/ranks/wendy.gif width=28><br>Wendy Koopa'),(1,2600,'<img src=img/ranks/larry.gif width=28><br>Iggy Koopa'),(1,2700,'<img src=img/ranks/roy.gif width=34><br>Roy Koopa'),(1,2800,'<img src=img/ranks/lemmy.gif width=28><br>Lemmy Koopa'),(1,2900,'<img src=img/ranks/ludwig.gif width=33><br>Ludwig Von Koopa'),(1,3000,'<img src=img/ranks/triclyde.gif width=40 height=48><br>Triclyde'),(1,3100,'<img src=img/ranks/kamek.gif width=45 height=34><br>Magikoopa'),(1,3200,'<img src=img/ranks/wart.gif width=40 height=47><br>Wart'),(1,3300,'<img src=img/ranks/babybowser.gif width=36 height=36><br>Baby Bowser'),(1,3400,'<img src=img/ranks/bowser.gif width=52 height=49><br>King Bowser Koopa'),(1,3500,'<img src=img/ranks/yoshi.gif width=31 height=33><br>Yoshi'),(1,3600,'<img src=img/ranks/yoshiyellow.gif width=31 height=32><br>Yellow Yoshi'),(1,3700,'<img src=img/ranks/yoshiblue.gif width=36 height=35><br>Blue Yoshi'),(1,3800,'<img src=img/ranks/yoshired.gif width=33 height=36><br>Red Yoshi'),(1,3900,'<img src=img/ranks/kingyoshi.gif width=24 height=34><br>King Yoshi'),(1,4000,'<img src=img/ranks/babymario.gif width=28 height=24><br>Baby Mario'),(1,4100,'<img src=img/ranks/luigismall.gif width=15 height=22><br>Luigi'),(1,4200,'<img src=img/ranks/mariosmall.gif width=15 height=20><br>Mario'),(1,4300,'<img src=img/ranks/luigibig.gif width=16 height=30><br>Super Luigi'),(1,4400,'<img src=img/ranks/mariobig.gif width=16 height=28><br>Super Mario'),(1,4500,'<img src=img/ranks/luigifire.gif width=16 height=30><br>Fire Luigi'),(1,4600,'<img src=img/ranks/mariofire.gif width=16 height=28><br>Fire Mario'),(1,4700,'<img src=img/ranks/luigicape.gif width=26 height=30><br>Cape Luigi'),(1,4800,'<img src=img/ranks/mariocape.gif width=26 height=28><br>Cape Mario'),(1,4900,'<img src=img/ranks/luigistar.gif width=16 height=30><br>Star Luigi'),(1,5000,'<img src=img/ranks/mariostar.gif width=16 height=28><br>Star Mario'),(1,625,'<img src=img/ranks/drybones.gif><br>Dry Bones'),(1,10000,'Climbing the ranks again!'),(2,0,'Non-poster'),(2,1,'Newcomer'),(2,20,'<img src=img/ranksz/Octorok.gif><br>Octorok'),(2,10,'<img src=img/ranksz/MiniOctorok.gif><br>Mini Octorok'),(2,35,'<img src=img/ranksz/BlueOctorok.gif><br>Blue Octorok'),(2,50,'<img src=img/ranksz/Tektite.gif><br>Tektite'),(2,65,'<img src=img/ranksz/RedTektite.gif><br>Red Tektite'),(2,80,'<img src=img/ranksz/Rat.gif><br>Rat'),(2,100,'<img src=img/ranksz/Rope.gif><br>Rope'),(2,120,'<img src=img/ranksz/Keese.gif><br>Keese'),(2,140,'<img src=img/ranksz/Bee.gif><br>Bee'),(2,160,'<img src=img/ranksz/Octoballoon.gif><br>Octoballoon'),(2,180,'<img src=img/ranksz/Leever.gif><br>Leever'),(2,200,'<img src=img/ranksz/PurpleLeever.gif><br>Purple Leever'),(2,220,'<img src=img/ranksz/SandCrab.gif><br>Sand Crab'),(2,240,'<img src=img/ranksz/Bit.gif><br>Bit'),(2,260,'<img src=img/ranksz/Bot.gif><br>Bot'),(2,300,'<img src=img/ranksz/Cukeman.gif><br>Cukeman'),(2,325,'<img src=img/ranksz/Slime.gif><br>Slime'),(2,350,'<img src=img/ranksz/Hoarder.gif><br>Hoarder'),(2,375,'<img src=img/ranksz/Crow.gif><br>Crow'),(2,400,'<img src=img/ranksz/Tendoru.gif><br>Tendoru'),(2,425,'<img src=img/ranksz/Deddorokku.gif><br>Deddorokku'),(2,450,'<img src=img/ranksz/Geldman.gif><br>Geldman'),(2,475,'<img src=img/ranksz/Armos.gif><br>Armos'),(2,500,'<img src=img/ranksz/Zora.gif><br>Zora'),(2,525,'<img src=img/ranksz/Popo.gif><br>Popo'),(2,550,'<img src=img/ranksz/HardhatBeetle.gif><br>Hardhat Beetle'),(2,575,'<img src=img/ranksz/Kodondo.gif><br>Kodondo'),(2,600,'<img src=img/ranksz/Surarok.gif><br>Surarok'),(2,700,'<img src=img/ranksz/Raven.gif><br>Raven'),(2,675,'<img src=img/ranksz/Chasupa.gif><br>Chasupa'),(2,650,'<img src=img/ranksz/Sukarurope.gif><br>Sukarurope'),(2,725,'<img src=img/ranksz/Ropa.gif><br>Ropa'),(2,750,'<img src=img/ranksz/Zirro.gif><br>Zirro'),(2,775,'<img src=img/ranksz/SnapDragon.gif><br>Snap Dragon'),(2,800,'<img src=img/ranksz/LikeLike.gif><br>Like Like'),(2,830,'<img src=img/ranksz/Poe.gif><br>Poe'),(2,860,'<img src=img/ranksz/Moblin.gif><br>Moblin'),(2,900,'<img src=img/ranksz/Helmasaur.gif><br>Helmasaur'),(2,925,'<img src=img/ranksz/Onoff.gif><br>Onoff'),(2,950,'<img src=img/ranksz/Bubble.gif><br>Bubble'),(2,1000,'<img src=img/ranksz/Stalfos.gif><br>Stalfos'),(2,1050,'<img src=img/ranksz/RedStalfos.gif><br>Red Stalfos'),(2,1100,'<img src=img/ranksz/YellowStalfos.gif><br>Yellow Stalfos'),(2,1150,'<img src=img/ranksz/Torosu.gif><br>Torosu'),(2,1200,'<img src=img/ranksz/RedTorosu.gif><br>Red Torosu'),(2,1250,'<img src=img/ranksz/Darknut.gif><br>Darknut'),(2,1300,'<img src=img/ranksz/IronKnuckle.gif><br>Iron Knuckle'),(2,1350,'<img src=img/ranksz/Vire.gif><br>Vire'),(2,1400,'<img src=img/ranksz/Rocklops.gif><br>Rocklops'),(2,1450,'<img src=img/ranksz/Beamos.gif><br>Beamos'),(2,1500,'<img src=img/ranksz/Wallmaster.gif><br>Wallmaster'),(2,1550,'<img src=img/ranksz/Gibdos.gif><br>Gibdo'),(2,1600,'<img src=img/ranksz/Wizzrobe.gif><br>Wizzrobe'),(2,1650,'<img src=img/ranksz/Lynel.gif><br>Lynel'),(2,1700,'<img src=img/ranksz/BallNChainTrooper.gif><br>Ball and Chain Trooper'),(2,1800,'<img src=img/ranksz/HinoxBoss.gif><br>Hinox'),(2,1900,'<img src=img/ranksz/MasterStalfosTrimmed.gif><br>Master Stalfos'),(2,2000,'<img src=img/ranksz/Aquamentus.gif><br>Aquamentus'),(2,2100,'<img src=img/ranksz/Dodongo.gif><br>Dodongo'),(2,2200,'<img src=img/ranksz/Gohma.gif><br>Gohma'),(2,2300,'<img src=img/ranksz/Gleeok.gif><br>Gleeok'),(2,2400,'<img src=img/ranksz/Digdogger.gif><br>Digdogger'),(2,2500,'<img src=img/ranksz/Manhandla.gif><br>Manhandla'),(2,2600,'<img src=img/ranksz/ArmosKnight.gif><br>Armos Knight'),(2,2700,'<img src=img/ranksz/Moldorm.gif><br>Moldorm'),(2,2800,'<img src=img/ranksz/Arrghus.gif><br>Arrghus'),(2,2900,'<img src=img/ranksz/Mothula.gif><br>Mothula'),(2,3000,'<img src=img/ranksz/Blind.gif><br>Blind'),(2,3100,'<img src=img/ranksz/Kholdstare.gif><br>Kholdstare'),(2,3200,'<img src=img/ranksz/Vitreous.gif><br>Vitreous'),(2,3300,'<img src=img/ranksz/DarkLink.gif><br>Dark Link'),(2,3400,'<img src=img/ranksz/DeathI.gif><br>DethI'),(2,3500,'<img src=img/ranksz/Agahnim.gif><br>Agahnim'),(2,3600,'<img src=img/ranksz/Ganon.gif><br>Ganon'),(2,3700,'<img src=img/ranksz/Zelda.gif><br>Zelda'),(2,3800,'<img src=img/ranksz/Link.gif><br>Link'),(2,3900,'<img src=img/ranksz/TheAdventureOfLink.gif><br>The Adventure of Link'),(2,4000,'<img src=img/ranksz/Link\'sAwakening.gif><br>Link\'s Awakening'),(2,4100,'<img src=img/ranksz/ALinkToThePast.gif><br>A Link to the Past'),(2,4200,'<img src=img/ranksz/FighterLink.gif><br>Fighter Link'),(2,4300,'<img src=img/ranksz/BlueMailLink.gif><br>Blue Mail Link'),(2,4400,'<img src=img/ranksz/RedMailLink.gif><br>Red Mail Link'),(2,4500,'<img src=img/ranksz/RabbitLink.gif><br>Rabbit Link'),(2,4600,'<img src=img/ranksz/MagicHammer.gif><br>Magic Hammer'),(2,4700,'<img src=img/ranksz/CaneOfByrna.gif><br>Cane of Byrna'),(2,4800,'<img src=img/ranksz/HeroOfTime.gif><br>Hero of Time'),(2,4900,'<img src=img/ranksz/HeroOfWinds.gif><br>Hero of Winds'),(2,5000,'<img src=img/ranksz/HeroOfHyrule.gif><br>Hero of Hyrule'),(2,625,'<img src=img/ranksz/Bazu.gif><br>Bazu'),(2,10000,'Climbing the ranks again!'),(2,280,'<img src=img/ranksz/BuzzBlob.gif><br>Buzz Blob');
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
INSERT INTO `ranksets` VALUES (1,'Mario'),(0,'None'),(2,'Zelda (by Fyxe)');
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
-- Table structure for table `rights`
--

DROP TABLE IF EXISTS `rights`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rights` (
  `r` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`r`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rights`
--

LOCK TABLES `rights` WRITE;
/*!40000 ALTER TABLE `rights` DISABLE KEYS */;
INSERT INTO `rights` VALUES ('edit-sprites','Edit Sprites','Can edit sprites and associated metadata.'),('edit-tokens','Edit Tokens','Can edit token names and associated rights.'),('edit-user','Edit User','Can edit other users. (u)'),('list','List threads','Can list threads in a forum or other collection. (fc)'),('see-history','See History','Can view past revisions of posts. (tfc)'),('show-ips','Show IP Addresses','See IP addresses in threads and profiles. (u)');
/*!40000 ALTER TABLE `rights` ENABLE KEYS */;
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
) ENGINE=MyISAM AUTO_INCREMENT=244 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sprites`
--

LOCK TABLES `sprites` WRITE;
/*!40000 ALTER TABLE `sprites` DISABLE KEYS */;
INSERT INTO `sprites` VALUES (1,'Dodongo',2,'dodongo.png','dodongo_left.png','free','BOMB?','Dodongo dislikes smoke.',0),(7,'Missile',3,'Metroid/MissilePack.gif','Metroid/MissilePack.gif','free','','Increases maximum missile capacity by 5',0),(2,'Ika-chan',0,'ika.png','ika.png','free','Meep!','I got nothing.',0),(3,'Monkey',1,'yi_monkey_right.png','yi_monkey_left.png','sidepic','Ook!','Annoying little buggers.',0),(6,'Energy Tank',3,'Metroid/EnergyTank.gif','Metroid/EnergyTank.gif','free','','Increases maximum energy capacity by 100',0),(4,'Goomba',1,'goomba3.png','goomba3.png','bottom','','They looked much better with proper outlines, didn\'t they?',0),(5,'Dalek',0,'dalek.png','dalek.png','free','EX-TER-MI-NATE!!!','Aim for the eyestalk.',0),(8,'Morph Ball',3,'Metroid/MorphBall.gif','Metroid/MorphBall.gif','free','','Maru-Mari',0),(9,'Bombs',3,'Metroid/Bombs.gif','Metroid/Bombs.gif','free','','Altitude-increasing aparratus',0),(10,'Long Beam',3,'Metroid/LongBeam.gif','Metroid/LongBeam.gif','free','','Increases length, \'and\'... virility.',0),(11,'Ice Beam (Brinstar)',3,'Metroid/IceBeamBrinstar.gif','Metroid/IceBeamBrinstar.gif','free','','A modular beam weapon designed by the Chozo.',0),(12,'Ice Beam (Norfair)',3,'Metroid/IceBeamNorfair.gif','Metroid/IceBeamNorfair.gif','free','','The cold concentration of Samus\' beating heart.',0),(13,'High Jump Boots',3,'Metroid/HighJumpBoots.gif','Metroid/HighJumpBoots.gif','free','','The result of the Chozo\'s observations in the Mushroom Kingdom.',0),(14,'Wave Beam',3,'Metroid/WaveBeam.gif','Metroid/WaveBeam.gif','free','','No-clipping mode activated!',0),(15,'Varia Suit',3,'Metroid/VariaSuit.gif','Metroid/VariaSuit.gif','free','','Power Suit Service Pack 1',0),(16,'Screw Attack',3,'Metroid/ScrewAttack.gif','Metroid/ScrewAttack.gif','free','','I wonder why they called it that?',0),(17,'Energy Pickup',3,'Metroid/EnergyPickup.gif','Metroid/EnergyPickup.gif','free','','Replenishes 5 or 20 units of energy',0),(18,'Missile Pickup',3,'Metroid/MissilePickup.gif','Metroid/MissilePickup.gif','free','','Replenishes 2 missile units',0),(19,'Zoomer',3,'Metroid/Zoomer1.gif','Metroid/Zoomer1.gif','free','','This is the first enemy that Samus encounters.',0),(20,'Nova',3,'Metroid/Nova1.gif','Metroid/Nova1.gif','free','','Fiberglass insulation at its complete worst.',0),(21,'Zeela',3,'Metroid/Zeela1.gif','Metroid/Zeela1.gif','free','','Creeping eyeballs trying to steal a look under Samus\' Power Suit.',0),(22,'Viola',3,'Metroid/Viola1.gif','Metroid/Viola1.gif','free','','Smoky the Bear\'s Mortal Enemy',0),(23,'Zeb',3,'Metroid/Zeb1.gif','Metroid/Zeb1.gif','free','','Never ending fountain of Energy and Missiles',0),(24,'Gamet',3,'Metroid/Gamet1.gif','Metroid/Gamet1.gif','free','','Flying spiked baseball helmet',0),(25,'Geega',3,'Metroid/Geega1.gif','Metroid/Geega1.gif','free','','That\'s the last damn time you get me while I\'m inside the door.',0),(26,'Zebbo',3,'Metroid/Zebbos1.gif','Metroid/Zebbos1.gif','free','','Hey, I don\'t blame it for throwing itself at Samus. I would too.',0),(27,'Mellow',3,'Metroid/Mellow.gif','Metroid/Mellow.gif','free','','The house fly of Brinstar',0),(28,'Melia',3,'Metroid/Melia.gif','Metroid/Melia.gif','free','','The house fly of Norfair',0),(29,'Memu',3,'Metroid/Memu.gif','Metroid/Memu.gif','free','','The house fly of Kraid\'s Lair',0),(30,'Rio',3,'Metroid/Rio1.gif','Metroid/Rio1.gif','free','','Obligatory Step Stool for Energy Tank',0),(31,'Geruta',3,'Metroid/Geruta1.gif','Metroid/Geruta1.gif','free','','These usually drop large energy pickups.',0),(32,'Holtz',3,'Metroid/Holtz1.gif','Metroid/Holtz1.gif','free','','Ridley\'s Flying Bulldogs from Hell',0),(33,'Side Hopper',3,'Metroid/SideHopper1.gif','Metroid/SideHopper1.gif','free','','Reptile of Kraid\'s Lair',0),(34,'Dessgeega',3,'Metroid/Dessgeega1.gif','Metroid/Dessgeega1.gif','free','','Scorpion of Ridley\'s Lair',0),(35,'Ripper',3,'Metroid/Ripper1.gif','Metroid/Ripper1.gif','free','','That one bug that looks like a croissant that you can freeze and use as a platform',0),(36,'Ripper II',3,'Metroid/RipperII1.gif','Metroid/RipperII1.gif','free','','Rocket Propelled Larva',0),(37,'Skree',3,'Metroid/Skree1.gif','Metroid/Skree1.gif','top','','They get their technique from old World War pilots from Japan.',0),(38,'Multiviola',3,'Metroid/Miltiviola1.gif|Metroid/Miltiviola2.gif','Metroid/Miltiviola3.gif|Metroid/Miltiviola4.gif','free','','Official dodgeball of Ridley\'s Lair (Standard Size)',0),(39,'Waver',3,'Metroid/Waver1.gif','Metroid/Waver1.gif','free','','These things would have been killer in a Gradius game.',0),(40,'Squeept',3,'Metroid/Squeept1.gif','Metroid/Squeept1.gif','free','','Hyper active lava dweller',0),(41,'Polyp',3,'Metroid/Polyp.gif','Metroid/Polyp.gif','bottom','','Mother Brain\'s personal colon polyps have it out for Samus!',0),(42,'Lava-Dragon',3,'Metroid/LavaDragon1.gif','Metroid/LavaDragon1.gif','free','','I\'ve always viewed seahorses with suspicion...',0),(43,'Rinka',3,'Metroid/Rinka1.gif','Metroid/Rinka1.gif','free','','Mother Brain\'s mysterious smoke rings. That\'s some powerful weed.',0),(44,'Metroid',3,'Metroid/Metroid1.gif|Metroid/Metroid2.gif','Metroid/Metroid1.gif','free','','Kirby\'s Nightmare Fuel',10),(45,'Fake Kraid',3,'Metroid/FakeKraid.gif','Metroid/FakeKraid.gif','bottom','','One missile...',0),(46,'Kraid',3,'Metroid/Kraid.gif','Metroid/Kraid.gif','bottom','','Remember kids, eating disorders are bad for you!',60),(47,'Ridley',3,'Metroid/Ridley.gif','Metroid/Ridley.gif','bottom','','We have the technology... We can rebuild him.',60),(48,'Mother Brain',3,'Metroid/MotherBrain.gif','Metroid/MotherBrain.gif','bottom','','Periods of her magnitude destroy planets!',75),(49,'Goomba',1,'SMB1/goomba.png','SMB1/goomba.png','free','','I started the \"bad mushroom\" craze!',0),(50,'Green Koopa',1,'SMB1/koopatroopa.png','SMB1/koopatroopa.png','free','','Overgrown terrestrial turtles?',0),(51,'Green Paratroopa',1,'SMB1/paratroopa.png','SMB1/paratroopa.png','free','','FLYING overgrown terrestrial turtles!?',0),(52,'Red Koopa',1,'SMB1/koopatroopa2.png','SMB1/koopatroopa2.png','free','','These ones are a bit smarter!',0),(53,'Red Paratroopa',1,'SMB1/paratroopa2.png','SMB1/paratroopa2.png','free','','Mario\'s personal stepstool.',0),(54,'Blooper',1,'SMB1/blooper.png','SMB1/blooper.png','free','','Not necessarily limited to water!',0),(55,'Spiny',1,'SMB1/spiny.png','SMB1/spiny.png','free','','Don\'t do it. It\'s worse than stepping on a porcupine.',0),(56,'Spiny Egg',1,'SMB1/spinyegg.png','SMB1/spinyegg.png','free','','Have you ever had a cactus thrown at you?',0),(57,'Lakitu',1,'SMB1/lakitu.png','SMB1/lakitu.png','free','','These guys rain down hate and evil.',0),(58,'Bullet Bill',1,'SMB1/bulletbill.png','SMB1/bulletbill.png','free','','High-caliber evil!',0),(59,'Piranha Plant',1,'SMB1/piranhaplant.png','SMB1/piranhaplant.png','free','','Science project gone wrong.',0),(60,'Hammer Brother',1,'SMB1/hammerbro.png','SMB1/hammerbro.png','free','','Have you ever thrown a hammer before? Serious business.',0),(61,'Cheep-Cheep',1,'SMB1/cheepcheep.png','SMB1/cheepcheep.png','free','','I prefer my fish in a frying pan. Flying\'s not my taste.',0),(62,'Buzzy Beetle',1,'SMB1/buzzybeetle.png','SMB1/buzzybeetle.png','free','','Immune to fire!',0),(63,'Mushroom',1,'SMB1/mushroom.gif','SMB2J/Magic%20Mushroom.png','free','','I thought WEED was the gateway drug?',0),(64,'Flower',1,'SMB1/fireflower.gif','SMB1/fireflower.gif','free','','Flowers that give you power to shoot fire... Sounds like secret government work.',0),(65,'Coin',1,'SMB1/coin.gif','SMB1/coin.gif','free','','Collect money for longevity!',0),(66,'Question Block',1,'SMB1/questionblock.gif','SMB1/questionblock.gif','free','','I wonder what\'s inside?',0),(67,'Starman',1,'SMB1/starman.gif','SMB1/starman.gif','free','','Grab it! Grab it!',0),(68,'Bowser',1,'SMB1/bowser.png','SMB1/bowser.png','free','','This is the REAL Bowser, not just a Goomba in a Bowser costume.',0),(69,'Kimono Peach',1,'SMB1/allnightnippon_peach.png','SMB1/allnightnippon_peach.png','free','','GTFO my game, Leisure Suit Larry.',0),(70,'Toad',1,'SMB1/toad.png','SMB1/toad.png','free','','The Jar-Jar Binks of Nintendo.',0),(71,'Peach',1,'SMB1/peach.png','SMB1/peach.png','free','','She sure looked a lot different back then, eh? Plastic surgery has come a long way!',0),(72,'TARDIS',0,'tardis.png','','free','Woooosh! Woooosh! Wooosh!','Funny little blue box! (It\'s bigger on the inside)',0),(73,'Weeping Angel',0,'weeping_angel.png','','free','Don\'t Blink!','What ever you do, DON\'T BLINK!!',0),(74,'Blue Virus',0,'drmario/bluvir.gif','','free','At least it\'s not an STI.','At least it\'s not an STI.',0),(75,'Red Virus',0,'drmario/redvir.gif','','free','Better dead than red!','Scarlet Fever!',0),(76,'Yellow Virus',0,'drmario/yelvir.gif','','free','Hmm.. the doctor wants to check your liver!','To cure: Lots and lots of yellow pills.',0),(77,'Clefable',4,'PKMNRB/clefable.png','','free','*garbled PCM noise*','Treasure of Mt. Moon!',25),(78,'Raichu',4,'PKMNRB/Raichu.png','','free','*more PCM noise..* (This may get old)','At least someone wasn\'t a fan of the anime.',0),(79,'Kirby',5,'Kirby/kirbs.gif','','free','In Soviet Russia, Pink Power Puff eats you!','What? That pink marshmallow?',0),(80,'King Dedede',5,'Kirby/dedede.gif','','free','','You mean he\'s not a penguin',0),(81,'Waddle Dee',5,'Kirby/waddledee.gif','','free','... .. ...','Totally harmless, and you still clicked him. Meanie.',0),(82,'Bobo',1,'SML3/bobo.gif','','free','','Most overgrown parrot ever.',25),(83,'Wario',1,'SML3/safariwario.gif','','free','','Good news! This guy\'s leading your safari.',0),(84,'Zenisukii',1,'SML3/Zenisukii.gif','','free','','Drop dead gorgeous on camera.',0),(85,'DD Duck',1,'SML3/DD.gif','','free','','Watch for the Boomerang!',0),(86,'Ino',0,'Ino.gif','','free','eh?','Bet you\'re thinking.. Who?',50),(87,'Gobb',0,'gobb.gif','','free','Minion of Evil','Zombies of Arcadia!',0),(91,'Kracko',5,'Kirby/kracko.gif','','free','Bzzzztttt','This thundercloud doesn\'t like you.',35),(90,'Stopper',0,'stopper.gif','','free','Your move, creep.','Stone towers that just love to get in your way.',0),(92,'Cappy',5,'Kirby/cappy.gif','','free','Mushroom?','Shy people look like mushrooms.',0),(93,'Gordo',5,'Kirby/gordo.gif','','free','Ineffective.','Generic spike-based enemy.',0),(94,'Scarfy',5,'Kirby/scarfy.gif','','free','Hold your breath!','Alergic to inhaling.',0),(95,'Missingno.',4,'PKMNRB/missingno.png','','free','%^PkToxic&*5464/][>','$%gdg65-sd$ÃƒÂ©PkMn-gdWATER GUN.',15),(96,'Bulbasaur',4,'PKMNRB/Bulbasaur.png','','free','Bulbabulba','Your rival just picked Charmander.',0),(97,'Charmander',4,'PKMNRB/Charmander.png','','free','Charchar','Your first couple of gym sessions are going to be a killer.',0),(98,'Squirtle',4,'PKMNRB/Squirtle.png','','free','SquirtSquirt','Cannons must come later.',0),(99,'Dragonair',4,'PKMNRB/Dragonair.png','','free','Use Surf!','Halfway there!',50),(100,'Eevee',4,'PKMNRB/Eevee.png','','free','Evostone!','The theory of evolution in a.. cat?',0),(101,'Dugtrio',4,'PKMNRB/Dugtrio.png','','bottom','Use Dig!','The boss of holes.',0),(102,'Gastly',4,'PKMNRB/Gastly.png','','free','Who let rip?','Who let this one out?',0),(103,'Geodude',4,'PKMNRB/Geodude.png','','free','Solid stuff.','Kafuka\'s got talent judge. NEXT!',0),(104,'Jigglypuff',4,'PKMNRB/Jigglypuff.png','','free','Use Sing!','Sing murders in the anime.. Misses in the game.',0),(105,'Magikarp',4,'PKMNRB/Magikarp.png','','free','500 PokÃ©Dollars.','The ultimate fish and chips.',0),(106,'Spearow',4,'PKMNRB/Spearow.png','','free','Use Fly!','So ugly, the anime picked Pidgey.',0),(107,'Lass',4,'PKMNRB/Lass.png','','free','Lass wants to battle!','Also known as \"Miniskirt\", but only in Japan.',35),(108,'Hiker',4,'PKMNRB/Hiker.png','','free','Tubby wants to battle','Takes more than a hike to lose that weight.',35),(109,'Dropper',1,'SML3/dropper.gif','','top','Petooooooo!','The falling spike that moves.',0),(110,'Pouncer',1,'SML3/pouncer.gif','','free','Thwomp?','Thwomp\'s badass cousin.',15),(111,'Pirate Goom',1,'SML3/sprgoom.gif','','free','Piracy at its peak.','Spears make the.. goom?',0),(112,'Wanderin\' Goom',1,'SML3/wndrgoom.gif','','free','Completely Harmless.','Goombas for Wario.',0),(113,'Bucket Head',1,'SML3/buckhead.gif','','free','Awesome bucket.','Bucket + Large Head = Ice Powers!',0),(114,'Captain Syrup',1,'SML3/syrup.gif','','free','Pirate Queen','The ultimate conwoman!',50),(115,'Genie',1,'SML3/genie.gif','','free','Make a wish.','The bigger the genie, the bigger the wish!',60),(116,'Princess Peach',1,'SMB2J/Princess%20Peach.png','','bottom','','The princess has been found in yet another castle?',40),(117,'1Up Mushroom',1,'SMB2J/1-Up%20Mushroom.png','SMB2J/1-Up%20Mushroom%20Dark.png','free','Extra Life!','We could all do with another life!',0),(120,'1Up Block',1,'SMB2/1Up.png','','free','Lift for life','Dreamin\' of a new life',0),(118,'Poison Mushroom',1,'SMB2J/Poison%20Mushroom.png','SMB2J/Posion%20Mushroom%20Gray.png','free','Gack!','Pure evil and developer spite rolled into a single package.',0),(121,'Coin',1,'SMB2/Coin.png','','free','Kaching','Subcon currency isn\'t valid for real life.',0),(122,'Crystal',1,'SMB2/Crystal.gif','','free','','Birdo laid an egg?',0),(123,'Key',1,'SMB2/Key.png','','free','','Protected by scary masked ghosts.',0),(124,'Luigi',1,'SMB2/LuigiL.png','','free','','Mario\'s brother.',35),(125,'Luigi',1,'SMB2/LuigiLRun.gif','','free','','The mean green ghost bustin\' coward.',0),(126,'Small Luigi',1,'SMB2/LuigiSRun.gif','','free','','Mammaa miiaaaaaa',0),(127,'Mario',1,'SMB2/MarioL.png','','free','','It\'sa me, Mario!',35),(128,'Mario',1,'SMB2/MarioLRun.gif','','free','','Was always faster than Luigi.',0),(129,'Small Mario',1,'SMB2/MarioSRun.gif','','free','','Head bigger than your body? Might need Dr. Mario for that.',0),(130,'Mushroom',1,'SMB2/Mushroom.png','','free','Shrooooom!','Even subcon has mushrooms.',0),(131,'Mushroom Block',1,'SMB2/MushroomBlock1.png','SMB2/MushroomBlock2.png','free','Shroom Blockz','Packs a punch',0),(132,'Mushroom Block',1,'SMB2/MushroomBlock3.png|SMB2/MushroomBlock5.png','SMB2/MushroomBlock4.png','free','Shroom Blocks','Various solid mushrooms, good for head bashin\'',0),(133,'POW Block',1,'SMB2/POWBlock.gif','','free','BWAMM!','Earthquake in a box!',0),(134,'Peach',1,'SMB2/PeachL.png','','free','','Princess in her adventure debut.',35),(135,'Peach',1,'SMB2/PeachLRun.gif','','free','','Pink dresses allow you to float in the sky for a short bit.',0),(136,'Small Peach',1,'SMB2/PeachSRun.gif','','free','','Ladies are immune to the big-head disease of Subcon.',0),(137,'Toad',1,'SMB2/ToadL.png','','free','','Among the tallest of the Toad Tribe',35),(138,'Small Toad',1,'SMB2/ToadSRun1.png','','free','','Small but mighty.',0),(139,'Bullet Bill',1,'SMB1A/smb1as_bulletbill.png','','free','Speeding Doom','Now with more shine.',0),(140,'Buzzy Beatle',1,'SMB1A/smb1as_buzzybeetle.png','','free','Fire doesn\'t work!','Shinier and still a fireproof pest.',0),(141,'Cheep Cheep',1,'SMB1A/smb1as_cheepcheep.png','','free','Chips, anyone?','Flying fish are a pain to catch.',0),(142,'Goomba',1,'SMB1A/smb1as_goomba.png','','free','','Still as squishy as always',0),(143,'Hammer Bros.',1,'SMB1A/smb1as_hammerbro.png','','free','','Stop! Hammertime!',0),(144,'Koopa Trooper',1,'SMB1A/smb1as_koopatroopa.png','','free','','Loyal servant of Bowser',0),(145,'Lakitu',1,'SMB1A/smb1as_lakitu.png','','free','','Takes forever to fish you out the lake in a race.',0),(146,'Paratroopa',1,'SMB1A/smb1as_paratroopa.png','','free','','Red with wings makes for a great stepping stone.',0),(147,'Piranha Plant',1,'SMB1A/smb1as_piranha.png','','bottom','','Venus fly trap on steroids.',0),(148,'Spiny',1,'SMB1A/smb1as_spiny.png','','bottom','','I hate these things.',0),(149,'Spiny Egg',1,'SMB1A/smb1as_spinyegg.png','','free','','Pain in the neck and butt since 1985.',0),(150,'Toad',1,'SMB1A/smb1as_thetoadyone.png','','free','','Seven castles hold this guy, one the princess. Guess which you got.',0),(151,'Fire Flower',1,'SMW/FireFlower.gif','','free','^_^','Such a happy flower.',0),(152,'Flying Question Mark Box',1,'SMW/FlyingBox.gif','','free','Grab it!','Because stationary ones were too easy.',40),(153,'1UP',1,'SMW/1up.gif','','sidepic','','Getting a life is a good thing.',0),(154,'Coin',1,'SMW/Coin.gif','','free','','Mario\'s personal life savings.',0),(155,'Green Shell',1,'SMW/GreenShell.gif','','free','','Nothing special here.',0),(156,'Mario',1,'SMW/MarioLRun.gif','SMW/MarioSRun.gif','free','','Saving the ladies since 1981.',35),(157,'Mushroom',1,'SMW/Mushroom.gif','','free','','Mushrooms help you grow tall!',0),(158,'P Switch',1,'SMW/PSwitch.gif','','bottom','Press it!','Turns coins into blocks and vice versa.',0),(159,'Apple',1,'SMW/PinkApple.gif','SMW/RedApple.gif','free','','Yoshi\'s favourite food!',0),(160,'Red Shell',1,'SMW/RedShell.gif','','free','','Spicy aftertaste.',0),(161,'Yoshi Coin',1,'SMW/YoshiCoin.gif','','free','','Five to a level, one to your collection.',50),(88,'Mew',4,'PKMNRB/Mew.png','','free','Congratulations!','Holy luck! You got Mew!',99),(89,'Robocop',0,'robocop.gif','','sidepic','Your move, creep.','I never understood the Robocop fanfare here, but it sure was nifty.',95),(162,'Hitler',0,'hitler.png','mechahitler.png','bottom','Auf wiedersehen.','Hey, whatcha know. Real people appear in games!',95),(163,'Derpy',0,'derpy.png','','free','','...Are you okay, Rainbow Dash~?',35),(164,'Applejack',0,'applejack.png','','free','','F\'git you, I kin eat all o\' dese apples!',35),(165,'Fluttershy',0,'fluttershy.png','','free','Please don\'t click me...','Fluttershy: \"Please don\'t click me... I mean, if that\'s alright with you...*meep*\" or just \"*meep*\"',35),(166,'Pinkie Pie',0,'pinkiepie.png','','free','','You know what this calls for? A POSTING SPREE!',35),(167,'Rainbow Dash',0,'rainbowdash.png','','free','','What\'d I tell ya? I wouldn\'t leave Kafuka hangin\'.',45),(168,'Rarity',0,'rarity.png','','free','','Oh, do be careful where you point that thing, darling.',85),(169,'Black Belt',0,'FF1/BB.gif','FF1/BB-Injured.gif|FF1/BB-Dead.gif','free','','Falcon Punch! Oh wait, wrong game.',15),(170,'Black Mage',0,'FF1/BM.gif','FF1/BM-Injured.gif|FF1/BM-Dead.gif','free','','Always a classic in 8bit Theatre. ',15),(171,'Black Wizard',0,'FF1/BW.gif','FF1/BW-Injured.gif|FF1/BW-Dead.gif','free','','Changing class involves growing a face.',50),(172,'Fighter',0,'FF1/F.gif','FF1/F-Injured.gif|FF1/F-Dead.gif','free','Sword-chucks at the ready!','I like swords.',15),(173,'Knight',0,'FF1/K.gif','FF1/K-Injured.gif|FF1/K-Dead.gif','free','','Saving the world since 1987!',50),(174,'Monk',0,'FF1/M-injured.gif','FF1/M-Dead.gif','free','','Resisting all Monkey jokes.',50),(175,'Ninja',0,'FF1/N.gif','FF1/N-Injured.gif|FF1/N-Dead.gif','free','','Red is the new stealthy colour, did you know?',50),(176,'Red Mage',0,'FF1/RM.gif','FF1/RM-Injured.gif|FF1/RM-Dead.gif','free','','Because logic states White + Black = Red.',15),(177,'Red Wizard',0,'FF1/RW.gif','FF1/RW-Injured.gif|FF1/RW-Dead.gif','free','','To this day, I still have no idea why they chose red.',50),(178,'Thief',0,'FF1/T.gif','FF1/T-Injured.gif|FF1/T-Dead.gif','free','','Got shiny? It\'s now his shiny.',15),(179,'White Mage',0,'FF1/WM.gif','FF1/WM-Injured.gif|FF1/WM-Dead.gif','free','','We all love a healer.',35),(180,'White Wizard',0,'FF1/WW.gif','FF1/WW-Injured.gif|FF1/WW-Dead.gif','free','','Can you heal my funny bones?',75),(181,'Twilight Sparkle',0,'twilight.png','','free','','Dear Princess Celestia. All the users on this board are CUH-RAZY!',35),(182,'Chikorita',4,'PKMNGS/Chikorita.png','','free','','I has leaf on my head!',0),(183,'Cyndaquil',4,'PKMNGS/Cyndaquil.png','','free','','Because echidna-based creatures burn.',0),(184,'Totodile',4,'PKMNGS/Totodile.png','','free','','Rockin\' Crock.',0),(185,'Red Gyarados',4,'PKMNGS/Gyarados.png','','free','Sparkle sparkly!','Gamefreak just loves to give us shiny PokÃ©mon.',50),(186,'Hoothoot',4,'PKMNGS/Hoothoot.png','','free','','The best excuse kids can use to not go to bed.',0),(187,'Porygon2',4,'PKMNGS/Porygon2.png','','free','','I\'m sure it\'s just an inflatable duck.',0),(188,'Sudowoodo',4,'PKMNGS/Sudowoodo.png','','free','','Snorlax\'s Johto buddy!',0),(189,'Wobbuffet',4,'PKMNGS/Wobbuffet.png','','free','','Ultimate punching bag!',0),(190,'Xatu',4,'PKMNGS/Xatu.png','','free','','A totem pole combined with a bird?',30),(191,'Poke Maniac',4,'PKMNGS/PokeManiac.png','','free','','Failed clones of the PokÃ©mon Professors.',35),(192,'Kimono Girl',4,'PKMNGS/KimonoGirl.png','','free','','Beat them up for HM03 to SURF!',35),(193,'Red Coin',1,'SMB1/redcoin.gif','','free','','Collect all five for a high score!',0),(194,'Yoshi',1,'SMB1/yoshi.gif','','free','','Yoshi in SMB1? Am I seeing things?',0),(195,'Dunk',1,'WL2/dunk.gif','','free','','Because basketball means jumping on your head.',20),(196,'Giant Spearman',1,'WL2/giantspearman.gif','','free','','Beat the Giant Spearman!',0),(197,'Wario',1,'WL2/wario.gif','','free','','Owning a giant castle helps you stay slender.',40),(198,'Hen',1,'WL2/hen.gif','','bottom','','I want a chicken that lays eggs larger than itself.',0),(199,'Zombie Wario',1,'WL2/zombwar.gif','','bottom','','Dead gorgeous.',25),(119,'Dhaos',0,'Dhaos.gif','','free','Dhaos Blast!','An angelic being bent on destruction to save his homeland? How noble.',70),(200,'Treecko',4,'PKMNRSFRLG/Treecko.png','','free','','A grass lizard',0),(201,'Torchic',4,'PKMNRSFRLG/Torchic.png','','free','','One hot chick.',0),(202,'Mudkip',4,'PKMNRSFRLG/Mudkip.png','','free','','So I herd u leik mudkipz?',0),(203,'Kirlia',4,'PKMNRSFRLG/Kirlia.png','','free','','I bet you wish you could evolve her?',15),(204,'Ludicolo',4,'PKMNRSFRLG/Ludicolo.png','','free','','It\'s a rain dancing pineapple!',0),(205,'Feebas',4,'PKMNRSFRLG/Feebas.png','','free','','You picked the right fishing spot today!',70),(206,'Wailord',4,'PKMNRSFRLG/Wailord.png','','free','','All we need now is a Skitty.',20),(207,'Jirachi',4,'PKMNRSFRLG/Jirachi.png','','free','','Wishing you a Doom Desire',40),(208,'Aroma Lady',4,'PKMNRSFRLG/AromaLady.png','','free','','Because battling is pleasant too.',0),(209,'Super Nerd',4,'PKMNRSFRLG/SuperNerd.png','','free','','Hey, Nerds can fight back too!',0),(210,'Venusaur',4,'PKMNRSFRLG/Venusaur.png','','free','','In full bloom.',80),(211,'Charizard',4,'PKMNRSFRLG/Charizard.png','','free','','Favourite of most little kids since the Anime episode..',80),(212,'Blastoise',4,'PKMNRSFRLG/Blastoise.png','','free','','Evolution results in two badass cannons.',80),(213,'Meganium',4,'PKMNRSFRLG/Meganium.png','','free','','It\'s a big flower dinosaur?',80),(214,'Typhlosion',4,'PKMNRSFRLG/Typhlosion.png','','free','','From a cute spikey creature to a badass creature.',80),(215,'Feraligatr',4,'PKMNRSFRLG/Feraligatr.png','','free','','Badass Alligator.',80),(216,'Sceptile',4,'PKMNRSFRLG/Sceptile.png','','free','','Congratulations, your Grovyle evolved into Sceptile!',85),(217,'Blaziken',4,'PKMNRSFRLG/Blaziken.png','','free','','Congratulations, your Combusken evolved into Blaziken!',85),(218,'Swampert',4,'PKMNRSFRLG/Swampert.png','','free','','Congratulations, your Marshtomp evolved into Swampert!',85),(219,'Fusion Suit Samus',3,'METROIDF/Samus.gif','','free','','That DNA transplant was a brilliant idea!',40),(220,'Arachnus',3,'METROIDF/Arachnus.gif','','free','Rauuurrgh!','Evil rolling ball of pain.',40),(221,'Nightmare',3,'METROIDF/Nightmare.gif','','free','Happy dreams.','Sleep tight, don\'t let this guy get you though.',40),(222,'Omega Metroid',3,'METROIDF/Omega.gif','','free','','Because if a timer wasn\'t bad enough.',70),(223,'SA-X',3,'METROIDF/Sa-X.gif','','bottom','','Evil clone of Samus #2.',85),(224,'Catbat',1,'WL4/Catbat.gif','','free','','A cat and a bat? Who came up with that one?',45),(225,'Princess Shokora',1,'WL4/Shokora.gif','','bottom','','Congratulations, you got all the treasures.',85),(226,'Spoiled Rotten',1,'WL4/Spoiled.gif','','bottom','','You should see it when it\'s angry..',40),(227,'Wario',1,'WL4/Wario.gif','','free','','Now in glorious 16-bit colour!',40),(228,'Wario Car',1,'WL4/WarioCar.gif','','bottom','','Got me some slick wheels!',70),(229,'Rabite',0,'SD3/Rabbite.gif','','free','Squeek!','Totally harmless.',0),(230,'Gorva',0,'SD3/Gorva.gif','','top','','I hate this guy..',40),(231,'Full Metal Hagger',0,'SD3/Hagger.gif','','free','','Holy crap! It\'s a metal crab!',40),(232,'Dolan',0,'SD3/Dolan.gif','','bottom','','One big ass werewolf.',65),(233,'Flammie',0,'SD3/Flammie.gif','','free','','Because a cute dragon makes epic transportation!',60),(234,'Lavos Spawn',0,'CT/LavosSpawn.gif','','free','','Doom in a larval stage.',80),(235,'Crono',0,'CT/Crono.gif','','free','','Yet another silent protagonist.',35),(236,'Robo',0,'CT/Robo.gif','','free','','Robots make the best companions.',35),(237,'Magus',0,'CT/Magus.gif','','free','','Dark magic, and a plot to destroy doom solo?',45),(238,'Schala',0,'CT/Schala.gif','','free','','Welcome to the Kingdom of Zeal.',35),(239,'Gaspar',0,'CT/Gaspar.gif','','free','','The loneliest man at the end of time.',35),(240,'Dalton',0,'CT/Dalton.gif','','free','','Give me back my time machine!',65),(241,'Lloyd Irving',0,'Lloyd.gif','','free','','I\'ll show you! Divine Justice!',80),(242,'Yuri Lowell',0,'Yuri.gif','','free','','I am <i>so</i> gonna kick your ass!',80),(243,'Natalia Luzu Kimlasca-Lanvaldear',0,'Natalia.gif','','free','','Pour forth, O starlight!',85);
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
-- Table structure for table `tokenrights`
--

DROP TABLE IF EXISTS `tokenrights`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tokenrights` (
  `t` int(11) NOT NULL,
  `r` varchar(255) NOT NULL,
  KEY `t` (`t`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tokenrights`
--

LOCK TABLES `tokenrights` WRITE;
/*!40000 ALTER TABLE `tokenrights` DISABLE KEYS */;
INSERT INTO `tokenrights` VALUES (3,'see-history'),(3,'edit-user'),(4,'edit-tokens'),(2,'see-history'),(200,'block-layouts'),(201,'disable-sprites'),(5,'edit-tokens'),(4,'edit-sprites'),(3,'show-ips'),(100,'show-ips u4'),(4,'not show-ips u4'),(1,'list c2'),(3,'list'),(2,'list c1'),(1,'list c3'),(1,'list c5'),(1,'list c6'),(1,'list c8'),(1,'list c20'),(1,'list c4');
/*!40000 ALTER TABLE `tokenrights` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tokens`
--

DROP TABLE IF EXISTS `tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tokens` (
  `id` int(11) NOT NULL,
  `img` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `nc0` varchar(6) NOT NULL,
  `nc1` varchar(6) NOT NULL,
  `nc2` varchar(6) NOT NULL,
  `nc_prio` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tokens`
--

LOCK TABLES `tokens` WRITE;
/*!40000 ALTER TABLE `tokens` DISABLE KEYS */;
INSERT INTO `tokens` VALUES (-500,'','Christmas Time Normal User','6AC061','FB6060','D09878',1),(-51,'','Pending User','c7d0f0','f2b6dc','9384b0',0),(-1,'','Banned','888888','888888','888888',50),(0,'','Guest','FF0000','FF0000','FF0000',-5),(1,'','Normal User','97ACEF','F185C9','7C60B0',0),(2,'img/tokens/silverkey.png','Global Moderator','AFFABE','C762F2','47B53C',20),(3,'img/tokens/goldkey.png','Administrator','FFEA95','C53A9E','F0C413',30),(4,'img/tokens/cogwheel.png','System Administrator','FFEA95','C53A9E','F0C413',30),(5,'img/tokens/root.png','Root','EE4444','E63282','AA3C3C',40),(100,'img/tokens/dodongobadge.png','Dodongo Badge','FF0000','FF0000','FF0000',-10),(101,'img/tokens/P_FLCL.png','P! Badge','FF0000','FF0000','FF0000',-10),(102,'img/tokens/aborder.gif','Veteran Acmlm\'s Board Member','FF0000','FF0000','FF0000',-10),(103,'img/tokens/yoshi.gif','Yoshi Badge','FF0000','FF0000','FF0000',-10),(104,'img/badges/glasses.png','X-Ray Glasses','','','',-10),(200,'','Block layout','FF0000','FF0000','FF0000',-10),(201,'','Disable Layout','FF0000','FF0000','FF0000',-10);
/*!40000 ALTER TABLE `tokens` ENABLE KEYS */;
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
INSERT INTO `user_group` VALUES (0,1,0),(-1,15,0);
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
  `birth` int(11) NOT NULL DEFAULT '-1',
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
  PRIMARY KEY (`id`)
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
-- Table structure for table `usertokens`
--

DROP TABLE IF EXISTS `usertokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usertokens` (
  `u` int(11) NOT NULL,
  `t` int(11) NOT NULL,
  KEY `u` (`u`),
  KEY `t` (`t`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usertokens`
--

LOCK TABLES `usertokens` WRITE;
/*!40000 ALTER TABLE `usertokens` DISABLE KEYS */;
INSERT INTO `usertokens` VALUES (1,1),(1,5);
/*!40000 ALTER TABLE `usertokens` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=222 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `x_perm`
--

LOCK TABLES `x_perm` WRITE;
/*!40000 ALTER TABLE `x_perm` DISABLE KEYS */;
INSERT INTO `x_perm` VALUES (1,2,'group','capture-sprites','',0,0),(2,2,'group','login','',0,0),(3,2,'group','update-own-profile','',0,0),(4,1,'group','view-profile-page','',0,0),(5,1,'group','view-public-categories','',0,0),(6,1,'group','view-public-forums','',0,0),(7,1,'group','view-public-posts','',0,0),(8,1,'group','view-public-threads','',0,0),(9,2,'group','create-public-post','',0,0),(10,2,'group','create-public-thread','',0,0),(11,2,'group','update-own-post','',0,0),(12,2,'group','use-post-layout','',0,0),(23,8,'group','consecutive-posts','',0,0),(24,3,'group','delete-post','',0,0),(25,3,'group','delete-thread','',0,0),(26,3,'group','update-post','',0,0),(27,3,'group','update-thread','',0,0),(28,3,'group','view-post-history','',0,0),(30,4,'group','edit-attentions-box','',0,0),(31,4,'group','edit-categories','',0,0),(32,4,'group','edit-forums','',0,0),(33,4,'group','edit-moods','',0,0),(34,4,'group','edit-permissions','',0,0),(36,4,'group','view-all-private-categories','',0,0),(37,4,'group','view-all-private-forums','',0,0),(38,4,'group','view-all-private-posts','',0,0),(39,4,'group','view-all-private-threads','',0,0),(40,4,'group','view-all-sprites','',0,0),(41,4,'group','view-permissions','',0,0),(45,6,'group','no-restrictions','',0,0),(46,7,'group','view-private-forum','forum',11,0),(47,7,'group','edit-forum-thread','forum',11,0),(48,7,'group','delete-forum-thread','forum',11,0),(49,7,'group','edit-forum-post','forum',11,0),(50,7,'group','delete-forum-post','forum',11,0),(54,7,'group','view-private-forum','forum',12,0),(55,7,'group','edit-forum-thread','forum',12,0),(56,7,'group','delete-forum-thread','forum',12,0),(57,7,'group','edit-forum-post','forum',12,0),(58,7,'group','delete-forum-post','forum',12,0),(59,7,'group','edit-forum-thread','forum',13,0),(60,7,'group','delete-forum-thread','forum',13,0),(61,7,'group','edit-forum-post','forum',13,0),(62,7,'group','delete-forum-post','forum',13,0),(63,7,'group','view-private-forum','forum',13,0),(64,4,'group','create-all-private-forum-threads','',0,0),(65,4,'group','create-all-private-forum-posts','',0,0),(66,10,'group','view-private-category','categories',2,0),(67,10,'group','view-private-forum','forum',2,0),(68,10,'group','view-private-forum','forum',3,0),(69,10,'group','create-private-forum-thread','forum',2,0),(70,10,'group','create-private-forum-post','forum',2,0),(71,10,'group','create-private-forum-thread','forum',3,0),(72,10,'group','create-private-forum-post','forum',3,0),(73,9,'group','create-public-thread','',0,1),(74,9,'group','create-public-post','',0,1),(75,9,'group','update-own-post','',0,1),(76,9,'group','update-own-profile','',0,1),(77,4,'group','update-profiles','',0,0),(78,9,'group','rate-thread','',0,1),(79,2,'group','rate-thread','',0,0),(80,1,'group','register','',0,0),(81,2,'group','register','',0,1),(82,2,'group','logout','',0,0),(83,1,'group','view-login','',0,0),(84,2,'group','view-login','',0,1),(85,2,'group','mark-read','',0,0),(86,8,'group','staff','',0,0),(87,9,'group','banned','',0,0),(88,8,'group','ignore-thread-time-limit','',0,0),(89,2,'group','rename-own-thread','',0,0),(90,7,'group','view-forum-post-history','forum',11,0),(91,7,'group','view-forum-post-history','forum',12,0),(92,7,'group','view-forum-post-history','forum',13,0),(93,7,'group','create-private-forum-thread','forum',11,0),(94,7,'group','create-private-forum-thread','forum',12,0),(95,7,'group','create-private-forum-thread','forum',13,0),(96,7,'group','create-private-forum-post','forum',11,0),(97,7,'group','create-private-forum-post','forum',12,0),(98,7,'group','create-private-forum-post','forum',13,0),(99,4,'group','view-post-ips','',0,0),(100,4,'group','edit-sprites','',0,0),(101,2,'group','update-own-moods','',0,0),(102,2,'group','view-user-urls','',0,0),(103,4,'group','view-hidden-users','',0,0),(104,4,'group','edit-users','',0,0),(126,2,'group','create-pms','',0,0),(127,2,'group','delete-own-pms','',0,0),(128,2,'group','view-own-pms','',0,0),(129,8,'group','edit-title','',0,0),(130,11,'group','create-pms','',0,1),(131,11,'group','delete-own-pms','',0,1),(132,11,'group','view-own-pms','',0,1),(133,2,'group','view-own-sprites','',0,0),(134,2,'group','create-public-post','forum',16,1),(135,2,'group','create-public-thread','forum',16,1),(136,12,'group','view-private-forum','forum',15,0),(137,12,'group','create-private-forum-post','forum',15,0),(138,12,'group','create-private-forum-thread','forum',15,0),(139,4,'group','override-readonly-forums','',0,0),(160,13,'group','edit-forum-thread','forum',1,0),(161,13,'group','delete-forum-thread','',1,0),(162,13,'group','edit-forum-post','',1,0),(163,13,'group','delete-forum-post','',1,0),(164,13,'group','view-forum-post-history','',1,0),(170,10,'group','edit-forum-thread','',2,0),(171,10,'group','delete-forum-thread','',2,0),(172,10,'group','edit-forum-post','',2,0),(173,10,'group','delete-forum-post','',2,0),(174,10,'group','view-forum-post-history','',2,0),(175,10,'group','edit-forum-thread','',3,0),(176,10,'group','delete-forum-thread','',3,0),(177,10,'group','edit-forum-post','',3,0),(178,10,'group','delete-forum-post','',3,0),(179,10,'group','view-forum-post-history','',3,0),(180,14,'group','edit-forum-thread','',2,0),(181,14,'group','delete-forum-thread','',2,0),(182,14,'group','edit-forum-post','',2,0),(183,14,'group','delete-forum-post','',2,0),(184,14,'group','view-forum-post-history','',2,0),(185,9,'group','rename-own-thread','',0,1),(186,4,'group','view-errors','',0,0),(187,4,'group','edit-ip-bans','',0,0),(188,1,'group','view-calendar','',0,0),(189,15,'group','view-calendar','',0,1),(190,10,'group','view-private-forum','',17,0),(191,10,'group','create-private-forum-post','',17,0),(192,10,'group','create-private-forum-thread','',17,0),(193,10,'group','edit-forum-thread','',17,0),(194,10,'group','delete-forum-thread','',17,0),(195,10,'group','edit-forum-post','',17,0),(196,10,'group','delete-forum-post','',17,0),(197,10,'group','view-forum-post-history','',17,0),(198,3,'group','create-all-forums-announcement','',0,0),(199,10,'group','create-forum-announcement','',2,0),(200,10,'group','create-forum-announcement','',3,0),(201,16,'group','view-private-forum','',21,0),(202,16,'group','create-private-forum-post','',21,0),(203,16,'group','create-private-forum-thread','',21,0),(204,16,'group','view-private-category','',9,0),(205,5,'group','view-private-forum','',21,0),(206,5,'group','create-private-forum-post','',21,0),(207,5,'group','create-private-forum-thread','',21,0),(208,16,'group','edit-forum-thread','',21,0),(209,16,'group','delete-forum-thread','',21,0),(210,16,'group','edit-forum-post','',21,0),(211,16,'group','delete-forum-post','',21,0),(212,16,'group','view-forum-post-history','',21,0),(214,10,'group','has-displayname','',0,1),(215,10,'group','view-acs-calendar','',0,0),(217,2,'group','post-radar','',0,0),(218,3,'group','show-as-staff','',0,0),(219,4,'group','show-as-staff','',0,0),(220,8,'group','show-as-staff','',0,0),(221,6,'group','show-as-staff','',0,0);
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

-- Dump completed on 2012-03-10 23:37:16
