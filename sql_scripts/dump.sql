-- MySQL dump 10.13  Distrib 5.5.35, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: church_manager
-- ------------------------------------------------------
-- Server version	5.5.35-0ubuntu0.13.10.2

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
-- Current Database: `church_manager`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `church_manager` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `church_manager`;

--
-- Table structure for table `association`
--

DROP TABLE IF EXISTS `association`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `association` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` longtext NOT NULL,
  `date_added` varchar(20) NOT NULL,
  `added_by` int(100) NOT NULL,
  `removed` int(2) NOT NULL DEFAULT '0',
  `removed_by` int(2) NOT NULL,
  `reason_removed` longtext,
  `date_removed` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `association`
--

LOCK TABLES `association` WRITE;
/*!40000 ALTER TABLE `association` DISABLE KEYS */;
/*!40000 ALTER TABLE `association` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `association_due`
--

DROP TABLE IF EXISTS `association_due`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `association_due` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `dues` double NOT NULL DEFAULT '0',
  `association_id` int(100) NOT NULL,
  `date_added` varchar(20) NOT NULL,
  `added_by` int(100) NOT NULL,
  `removed` int(2) NOT NULL DEFAULT '0',
  `removed_by` int(100) NOT NULL DEFAULT '0',
  `date_removed` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `association_association_due_fk` (`association_id`),
  CONSTRAINT `association_association_due_fk` FOREIGN KEY (`association_id`) REFERENCES `association` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `association_due`
--

LOCK TABLES `association_due` WRITE;
/*!40000 ALTER TABLE `association_due` DISABLE KEYS */;
/*!40000 ALTER TABLE `association_due` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `member`
--

DROP TABLE IF EXISTS `member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `member` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(200) NOT NULL,
  `othernames` longtext NOT NULL,
  `gender` varchar(10) NOT NULL,
  `registration_date` varchar(20) NOT NULL,
  `added_by` int(100) NOT NULL,
  `picture_url` longtext,
  `removed` int(2) NOT NULL DEFAULT '0',
  `phonenumber` varchar(50) NOT NULL,
  `reason_removed` longtext,
  `date_removed` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `member`
--

LOCK TABLES `member` WRITE;
/*!40000 ALTER TABLE `member` DISABLE KEYS */;
/*!40000 ALTER TABLE `member` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `member_association`
--

DROP TABLE IF EXISTS `member_association`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `member_association` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `member_id` int(100) NOT NULL,
  `association_id` int(100) NOT NULL,
  `added_by` int(100) NOT NULL,
  `date_added` varchar(20) NOT NULL,
  `removed` int(2) NOT NULL DEFAULT '0',
  `reason_removed` longtext,
  `date_removed` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `member_association_member_fk` (`member_id`),
  KEY `member_association_association_fk` (`association_id`),
  CONSTRAINT `member_association_member_fk` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`),
  CONSTRAINT `member_association_association_fk` FOREIGN KEY (`association_id`) REFERENCES `association` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `member_association`
--

LOCK TABLES `member_association` WRITE;
/*!40000 ALTER TABLE `member_association` DISABLE KEYS */;
/*!40000 ALTER TABLE `member_association` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `member_association_due`
--

DROP TABLE IF EXISTS `member_association_due`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `member_association_due` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `member_id` int(100) NOT NULL,
  `association_id` int(100) NOT NULL,
  `dues` double NOT NULL,
  `mouth` varchar(20) NOT NULL,
  `date_added` varchar(20) NOT NULL,
  `added_by` int(100) NOT NULL,
  `removed` int(2) NOT NULL DEFAULT '0',
  `removed_by` int(100) DEFAULT NULL,
  `date_removed` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `member_association_due_member_fk` (`member_id`),
  KEY `member_association_due_association_fk` (`association_id`),
  CONSTRAINT `member_association_due_member_fk` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`),
  CONSTRAINT `member_association_due_association_fk` FOREIGN KEY (`association_id`) REFERENCES `association` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `member_association_due`
--

LOCK TABLES `member_association_due` WRITE;
/*!40000 ALTER TABLE `member_association_due` DISABLE KEYS */;
/*!40000 ALTER TABLE `member_association_due` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `usertype` varchar(50) NOT NULL,
  `removed` int(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-04-02 22:34:42
