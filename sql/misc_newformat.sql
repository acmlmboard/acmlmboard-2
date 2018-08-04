/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `misc` (
  `views` int(11) NOT NULL DEFAULT '0',
  `botviews` int(11) NOT NULL DEFAULT '0',
  `lockdown` int(11) NOT NULL DEFAULT '0',
  `lockdowntext` text COLLATE utf8_unicode_ci NOT NULL,
  `regdisable` int(11) NOT NULL DEFAULT '0',
  `regdisabletext` text COLLATE utf8_unicode_ci NOT NULL,
  `attentiontitle` text COLLATE utf8_unicode_ci NOT NULL,
  `attention` text COLLATE utf8_unicode_ci NOT NULL,
  `boardemail` text COLLATE utf8_unicode_ci NOT NULL,
  `maxpostsday` int(11) NOT NULL DEFAULT '0',
  `maxpostsdaydate` int(11) NOT NULL DEFAULT '0',
  `maxpostshour` int(11) NOT NULL DEFAULT '0',
  `maxpostshourdate` int(11) NOT NULL DEFAULT '0',
  `maxusers` int(11) NOT NULL DEFAULT '0',
  `maxusersdate` int(11) NOT NULL DEFAULT '0',
  `maxuserstext` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `misc`
--

LOCK TABLES `misc` WRITE;
/*!40000 ALTER TABLE `misc` DISABLE KEYS */;
INSERT INTO `misc` VALUES (5,0,0,'The board is currently unavailable. We apologize for any inconvenience.',0,'Registration is currently offline. We apologize for any inconvenience.','News','','emuz@address.com',0,0,0,0,0,0,'');
/*!40000 ALTER TABLE `misc` ENABLE KEYS */;
UNLOCK TABLES;