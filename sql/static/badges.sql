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
-- Table structure for table `user_badges`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE IF NOT EXISTS `user_badges` (
  `user_id` int(11) NOT NULL,
  `badge_id` int(11) NOT NULL,
  `badge_var` varchar(32) DEFAULT NULL,
  KEY `user_id` (`user_id`),
  KEY `badge_id` (`badge_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-05-25 20:33:38
