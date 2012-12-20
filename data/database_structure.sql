-- MySQL dump 10.13  Distrib 5.5.28, for debian-linux-gnu (i686)
--
-- Host: localhost    Database: usilu_mobiledev
-- ------------------------------------------------------
-- Server version	5.5.28-0ubuntu0.12.10.2

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
-- Table structure for table `AcademicCalendar`
--

DROP TABLE IF EXISTS `AcademicCalendar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AcademicCalendar` (
  `year` varchar(50) DEFAULT NULL,
  `url` varchar(200) DEFAULT NULL,
  `filename` varchar(100) DEFAULT NULL,
  `mime` varchar(50) DEFAULT NULL,
  `timemodify` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ExaminationTimetables`
--

DROP TABLE IF EXISTS `ExaminationTimetables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ExaminationTimetables` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `level` enum('bachelor','master') DEFAULT NULL,
  `faculty` enum('com','eco','info','arch') DEFAULT NULL,
  `session` varchar(100) DEFAULT NULL,
  `registration_begin` date DEFAULT NULL,
  `registration_end` date DEFAULT NULL,
  `examination_begin` date DEFAULT NULL,
  `examination_end` date DEFAULT NULL,
  `filename` varchar(100) DEFAULT NULL,
  `mime` varchar(50) DEFAULT NULL,
  `url` varchar(200) DEFAULT NULL,
  `timemodify` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `MenuMensa`
--

DROP TABLE IF EXISTS `MenuMensa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `MenuMensa` (
  `url` varchar(500) DEFAULT NULL,
  `filename` varchar(100) DEFAULT NULL,
  `mime` varchar(50) DEFAULT NULL,
  `timemodify` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `TeachingTimetables`
--

DROP TABLE IF EXISTS `TeachingTimetables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TeachingTimetables` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `faculty` enum('com','eco','info','arch') DEFAULT NULL,
  `level` enum('bachelor','master','phd') DEFAULT NULL,
  `program` varchar(200) DEFAULT NULL,
  `semester` enum('fall','spring') DEFAULT NULL,
  `filename` varchar(100) DEFAULT NULL,
  `mime` varchar(50) DEFAULT NULL,
  `url` varchar(200) DEFAULT NULL,
  `timemodify` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-12-20 11:50:27
