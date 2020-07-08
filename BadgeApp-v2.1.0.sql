-- MySQL dump 10.13  Distrib 8.0.17, for Win64 (x86_64)
--
-- Host: 192.168.5.12    Database: BadgeDB
-- ------------------------------------------------------
-- Server version	5.7.30-0ubuntu0.18.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `BadgeDB`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `BadgeDB` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `BadgeDB`;

--
-- Table structure for table `badge_certification`
--

DROP TABLE IF EXISTS `badge_certification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `badge_certification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `badge_number` int(6) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `sticker` varchar(255) NOT NULL,
  `certification_type` int(11) NOT NULL,
  `status` enum('0','1','2') NOT NULL,
  `fee` float(8,2) NOT NULL,
  `discount` float(8,2) NOT NULL,
  `amount_due` float(8,2) NOT NULL,
  `is_migrated` enum('0','1') NOT NULL DEFAULT '0',
  `cc_x_id` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=853 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `badge_subscriptions`
--

DROP TABLE IF EXISTS `badge_subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `badge_subscriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `badge_number` int(6) NOT NULL,
  `club_id` int(11) DEFAULT NULL,
  `valid_from` date NOT NULL,
  `valid_true` date NOT NULL,
  `payment_type` enum('cash','check','credit','online','other') NOT NULL,
  `status` varchar(12) NOT NULL,
  `sticker` varchar(100) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `badge_fee` float(8,2) DEFAULT NULL,
  `paid_amount` float(8,2) NOT NULL,
  `discount` float(8,2) NOT NULL,
  `transaction_type` enum('RENEW','NEW','CERT') DEFAULT NULL,
  `is_migrated` enum('0','1') DEFAULT '0',
  `cc_x_id` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20817 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `badge_to_club`
--

DROP TABLE IF EXISTS `badge_to_club`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `badge_to_club` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `badge_number` int(5) NOT NULL,
  `club_id` int(5) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19275 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `badges`
--

DROP TABLE IF EXISTS `badges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `badges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `badge_number` int(6) NOT NULL,
  `prefix` varchar(15) NOT NULL,
  `first_name` varchar(35) NOT NULL,
  `last_name` varchar(35) NOT NULL,
  `suffix` varchar(15) DEFAULT NULL,
  `address` text NOT NULL,
  `city` varchar(25) NOT NULL,
  `state` varchar(10) NOT NULL,
  `zip` varchar(10) NOT NULL,
  `gender` enum('0','1') DEFAULT NULL,
  `yob` int(4) DEFAULT NULL,
  `email` varchar(61) NOT NULL,
  `email_vrfy` tinyint(1) DEFAULT '0',
  `phone` varchar(25) NOT NULL,
  `phone_op` varchar(25) DEFAULT NULL,
  `ice_contact` varchar(40) DEFAULT NULL,
  `ice_phone` varchar(25) DEFAULT NULL,
  `club_id` int(4) NOT NULL,
  `mem_type` int(11) NOT NULL,
  `primary` varchar(11) DEFAULT NULL,
  `incep` datetime NOT NULL,
  `expires` date NOT NULL,
  `qrcode` text,
  `wt_date` date NOT NULL,
  `wt_instru` varchar(255) DEFAULT NULL,
  `remarks` text NOT NULL,
  `status` varchar(10) DEFAULT NULL,
  `soft_delete` enum('0') DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `badge_number` (`badge_number`)
) ENGINE=InnoDB AUTO_INCREMENT=9648 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cc_receipts`
--

DROP TABLE IF EXISTS `cc_receipts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cc_receipts` (
  `id` varchar(15) NOT NULL DEFAULT '',
  `badge_number` int(6) NOT NULL,
  `tx_date` datetime NOT NULL,
  `tx_type` varchar(10) NOT NULL,
  `status` varchar(15) DEFAULT NULL,
  `amount` decimal(8,2) NOT NULL,
  `authCode` varchar(6) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `cardNum` varchar(22) DEFAULT NULL,
  `cardType` varchar(20) DEFAULT NULL,
  `expYear` int(4) DEFAULT NULL,
  `expMonth` int(2) DEFAULT NULL,
  `cashier` varchar(50) NOT NULL,
  `on_qb` int(1) NOT NULL DEFAULT '0',
  `cart` text NOT NULL,
  `guest_cred` int(3) DEFAULT '0',
  PRIMARY KEY (`id`,`badge_number`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `clubs`
--

DROP TABLE IF EXISTS `clubs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clubs` (
  `club_id` int(11) NOT NULL AUTO_INCREMENT,
  `club_name` varchar(255) NOT NULL,
  `short_name` varchar(20) NOT NULL,
  `poc_email` varchar(255) DEFAULT NULL,
  `status` int(1) NOT NULL,
  `is_club` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`club_id`),
  UNIQUE KEY `club_id` (`club_id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clubs`
--

LOCK TABLES `clubs` WRITE;
/*!40000 ALTER TABLE `clubs` DISABLE KEYS */;
INSERT INTO `clubs` VALUES (1,'Applied Physics Laboratory Gun Club','APL','Doug.Grantham@jhuapl.edu',0,1),(2,'Arlington Rifle and Pistol Club','Arlington','robcow1@gmail.com',0,1),(3,'Baltimore County Game & Fish','BCG&F','',1,1),(4,'Baltimore Rifle Club','Balto Rifle','paul_crawford_atty@yahoo.com',0,1),(5,'Berwyn Rod and Gun Club','Berwyn','membership@berwyn.org',0,1),(6,'Catonsville Scouters Rifle and Pistol Club','Catonsville','CSRPC51@aol.com',0,1),(7,'Chesapeake Rifle and Pistol Club','Chesapeake','WAJE0526@aol.com',0,1),(8,'Garrison Rifle and Revolver Club','Garrison','vicepresident@garrisonrr.org',0,1),(9,'Glenmore Rifle and Pistol Club','Glenmore','moodystp@hotmail.com',0,1),(10,'Greenbelt Gun Club','Greenbelt','secretary@greenbeltgunclub.com',0,1),(11,'Homewood Rifle and Pistol Club','Homewood','secretary@homewoodrpc.org',0,1),(12,'Howard County Rifle and Pistol Club','Howard Co','VP-Membership@howardcountyriflepistol.org',0,1),(13,'Marriottsville Metallic Silhouette Shooters','MMSS','Skipwine@yahoo.com',0,1),(14,'Marriottsville Muzzle Loaders','MML','kayak3x@verizon.net',0,1),(15,'Maryland Rifle Club','Md Rifle','president@mdrifleclub.org',0,1),(16,'Maryland Tenth Cavalry Gun Club','10th Cav','trooperkh1@gmail.com',0,1),(17,'Maryland Thompson Collectors Association','MDTCA','phiteshe@vt.edu',0,1),(18,'Meade Rifle and Pistol Club','Meade R&P','siegc@verizon.net',0,1),(19,'Meade Rod and Gun Club','Meade R&G','president@MeadeRodandGun.org',0,1),(20,'Monumental Rifle and Pistol Club','Monumental','secretary@monumental.org',0,1),(21,'Mount Washington Rod and Gun Club','Mt Wash','secretary@mtwashingtonrg.org',0,1),(22,'NASA/Goddard Sportsman\'s Club','Goddard','AGCRep@GoddardGunClub.Org',0,1),(23,'Old Post Rifle and Pistol Club','Old Post','stvnander397@gmail.com',0,1),(24,'Pioneer Rod and Gun Glub','Pioneer','',0,1),(25,'Stemmers Run Rifle and Pistol Club','Stemmers','s_fickus@hotmail.com',0,1),(26,'Stoney Creek Fishing and Hunting Club','Stoney Crk','jorgemccauley@verizon.net',0,1),(27,'Tidewater Muzzle Loaders','TidewaterML','kelley805@aol.com',0,1),(28,'Twelfth Precinct Pistol and Archery Club','12th Pct','buc708@aol.com',0,1),(29,'Northrop Grumman Westinghouse Shooting Sports Club','Westinghouse','stephen.gottesman1@verizon.net',0,1),(30,'Anne Aundel County County Gun Club','AA County','parker.george95@yahoo.com',0,1),(31,'Maryland State Rifle and Pistol Association','MSRPA','secretary@msrpa.org',0,1),(32,'Givati Rifle and Pistol Club','Givati','Info@GivatiRPC.org',0,1),(33,'Associated Gun Clubs Staff','AGC Staff','',0,1),(34,'Gadsden Pew Club','Gadsden Pew Club','secretary@gadsdenpewclub.com',0,1),(35,'Z Old Data','ZOD','president@associatedgunclubs.org',0,1),(36,'Maryland Shall Issue','MSI','mpennak@marylandshallissue.org',0,1),(37,'IOTA Firearms and Security Training','IOTA','',0,0),(38,'MD Gun Training Center','MDGTC','',0,0),(39,'Baltimore Firearms Training','Baltimore Firearms','',0,0),(40,'AGC-RSSOP','RSSOP','',0,0),(41,'AGC High Power','High Power','',0,0),(42,'AGC Action Shooting','ACTION','',0,0),(43,'AGC Walk','Walk-about','',0,0),(44,'Parks Firearms','Parks','',0,0),(45,'B&S Personal Safety And Firearm Education','B&S','mcbruzdzinski@bspsafe.com',0,0),(46,'Protective Concepts','Protective','Protectiveconceptsllc_MD@yahoo.com',0,0),(47,'Antebellum Tutelage','Antebellum','antebellumt@icloud.com',0,0),(48,'Summit Training Group','Summit','dennisboyle9@gmail.com',0,0),(49,'NoVA-MD Self Defense','NoVA','jim@nova-mdselfdefense.com',0,0),(50,'JW Firearms Training & Certification','JW','johnnysfirearms@gmail.com',0,0),(51,'blasterKRAFT Firearms Training','blasterKRAFT','chris@blasterKRAFT.com',0,0),(52,'Defender One Security','Defender','Jared@defenderonesecurity.com',0,0);
/*!40000 ALTER TABLE `clubs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_attendee`
--

DROP TABLE IF EXISTS `event_attendee`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_attendee` (
  `ea_id` int(11) NOT NULL AUTO_INCREMENT,
  `ea_event_id` int(11) NOT NULL,
  `ea_badge` int(5) DEFAULT NULL,
  `ea_f_name` varchar(45) DEFAULT NULL,
  `ea_l_name` varchar(45) DEFAULT NULL,
  `ea_wb_serial` varchar(10) DEFAULT NULL,
  `ea_wc_logged` int(2) DEFAULT NULL,
  `ea_wb_out` int(2) DEFAULT '1',
  PRIMARY KEY (`ea_id`),
  UNIQUE KEY `id_ev_at_UNIQUE` (`ea_id`)
) ENGINE=InnoDB AUTO_INCREMENT=979 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `events` (
  `e_id` int(11) NOT NULL AUTO_INCREMENT,
  `e_name` varchar(60) NOT NULL,
  `e_date` date NOT NULL,
  `e_poc` int(5) NOT NULL,
  `e_status` varchar(45) NOT NULL,
  `e_type` varchar(45) NOT NULL,
  `e_hours` int(5) DEFAULT NULL,
  `e_inst` varchar(255) DEFAULT NULL,
  `e_rso` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`e_id`),
  UNIQUE KEY `e_id_UNIQUE` (`e_id`)
) ENGINE=InnoDB AUTO_INCREMENT=247 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fees_structure`
--

DROP TABLE IF EXISTS `fees_structure`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fees_structure` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) DEFAULT NULL,
  `membership_id` int(11) DEFAULT NULL,
  `fee` float(10,2) NOT NULL,
  `status` enum('0','1') NOT NULL,
  `type` enum('badge_fee','certification') NOT NULL,
  `sku_full` varchar(15) DEFAULT NULL,
  `sku_half` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fees_structure`
--

LOCK TABLES `fees_structure` WRITE;
/*!40000 ALTER TABLE `fees_structure` DISABLE KEYS */;
INSERT INTO `fees_structure` VALUES (1,'PRIMARY',50,200.00,'0','badge_fee','450100','450115'),(2,'FAMILY',51,67.00,'0','badge_fee','450110','450125'),(3,'Junior',52,100.00,'0','badge_fee','450105','450120'),(4,'LIFE',99,0.00,'0','badge_fee',NULL,NULL),(5,'Steel',NULL,10.00,'0','certification','410105',NULL),(6,'Holster',NULL,20.00,'0','certification','410100',NULL),(7,'Action',NULL,10.00,'0','certification',NULL,NULL),(8,'15yr Badge',70,2500.00,'1','badge_fee','450200','450200'),(9,'Staff',88,0.00,'0','badge_fee',NULL,NULL);
/*!40000 ALTER TABLE `fees_structure` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `guest`
--

DROP TABLE IF EXISTS `guest`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `guest` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `badge_number` int(6) NOT NULL,
  `g_first_name` varchar(35) NOT NULL,
  `g_last_name` varchar(35) NOT NULL,
  `g_city` varchar(255) DEFAULT NULL,
  `g_state` varchar(2) DEFAULT NULL,
  `g_yob` int(4) DEFAULT NULL,
  `g_paid` varchar(1) DEFAULT '0',
  `tmp_badge` int(6) DEFAULT NULL,
  `time_in` datetime NOT NULL,
  `time_out` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8486 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mass_email`
--

DROP TABLE IF EXISTS `mass_email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mass_email` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mass_to` varchar(255) NOT NULL,
  `mass_reply_to` varchar(255) DEFAULT NULL,
  `mass_subject` varchar(255) NOT NULL,
  `mass_body` blob NOT NULL,
  `mass_created` datetime DEFAULT NULL,
  `mass_created_by` int(11) DEFAULT NULL,
  `mass_updated` datetime DEFAULT NULL,
  `mass_updated_by` int(11) DEFAULT NULL,
  `mass_running` int(11) DEFAULT '0',
  `mass_start` datetime DEFAULT NULL,
  `mass_runtime` datetime DEFAULT NULL,
  `mass_lastbadge` int(11) DEFAULT NULL,
  `mass_finished` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `membership_type`
--

DROP TABLE IF EXISTS `membership_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `membership_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(25) NOT NULL,
  `status` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `membership_type`
--

LOCK TABLES `membership_type` WRITE;
/*!40000 ALTER TABLE `membership_type` DISABLE KEYS */;
INSERT INTO `membership_type` VALUES (50,'Primary','0'),(51,'Family','0'),(52,'Junior','0'),(70,'15yr','0'),(88,'Staff','0'),(99,'Life','0');
/*!40000 ALTER TABLE `membership_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `params`
--

DROP TABLE IF EXISTS `params`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `params` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sell_date` varchar(5) NOT NULL,
  `guest_sku` int(6) NOT NULL DEFAULT '0',
  `guest_total` int(3) NOT NULL DEFAULT '50',
  `status` enum('active','disabled') NOT NULL,
  `pp_id` varchar(82) DEFAULT NULL,
  `pp_sec` varchar(82) DEFAULT NULL,
  `qb_env` varchar(4) DEFAULT 'dev',
  `qb_realmId` varchar(20) DEFAULT NULL,
  `qb_oauth_cust_key` varchar(50) DEFAULT NULL,
  `qb_oauth_cust_sec` varchar(40) DEFAULT NULL,
  `qb_token_date` date DEFAULT NULL,
  `qb_token` varchar(255) DEFAULT NULL,
  `qb_oa2_id` varchar(50) DEFAULT NULL,
  `qb_oa2_sec` varchar(40) DEFAULT NULL,
  `qb_oa2_realmId` varchar(18) DEFAULT NULL,
  `qb_oa2_access_token` text,
  `qb_oa2_access_date` datetime DEFAULT NULL,
  `qb_oa2_refresh_token` varchar(50) DEFAULT NULL,
  `qb_oa2_refresh_date` datetime DEFAULT NULL,
  `log_rotate` int(3) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `params`
--

LOCK TABLES `params` WRITE;
/*!40000 ALTER TABLE `params` DISABLE KEYS */;
INSERT INTO `params` VALUES (1,'10-20',460130,50,'active','','','dev',NULL,'','',NULL,NULL,'','','',NULL,NULL,'','2020-08-25 14:28:50',6);
/*!40000 ALTER TABLE `params` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `post_print_transactions`
--

DROP TABLE IF EXISTS `post_print_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `post_print_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `badge_number` int(6) NOT NULL,
  `transaction_type` varchar(6) NOT NULL,
  `club_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `fee` float(8,2) NOT NULL,
  `discount` float(8,2) NOT NULL,
  `paid_amount` float(8,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16310 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rule_list`
--

DROP TABLE IF EXISTS `rule_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rule_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rule_abrev` varchar(6) NOT NULL,
  `vi_type` int(2) NOT NULL,
  `rule_name` varchar(255) NOT NULL,
  `is_active` int(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=140 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rule_list`
--

LOCK TABLES `rule_list` WRITE;
/*!40000 ALTER TABLE `rule_list` DISABLE KEYS */;
INSERT INTO `rule_list` VALUES (1,'IA02',3,'Never allow the gun to point at anything you do not intend to shoot.',1),(2,'IA02',4,'Never allow LOADED gun to point at anything you do not intend to shoot.',1),(3,'IB01',3,'Arguing with RSO, AGC Officer, or Match Director',1),(4,'IB01',4,'Refusing to follow the directions of RSO, AGC Officer, or Match Director',1),(5,'IB02',1,'Range badges shall be in the possession of the named badge holder and readily visible at all times while on AGC property.',1),(6,'IB02',4,'Range badges may not be loaned or transferred to, or in the possession of, any other person.',1),(7,'IB03',4,'Persons prohibited by any Federal, State or local law from owning or possessing firearms are specifically prohibited from entering upon AGC property and are subject to arrest for trespassing.',1),(8,'IB04',1,'Guest wrist bands shall be in the possession of the person it was issued to and readily visible at all times while on AGC property.',1),(9,'IB04',2,'Adult badge holders sign in their guests on the AGC App or log provided and be issued a wrist band.',1),(10,'IB04',4,'Guest badges may not be loaned or transferred to, or in the possession of, any other person.',0),(11,'IB06',1,'A badge holder with shooting guests may only occupy one firing point and ONLY ONE PERSON in your party may fire at a time.',1),(12,'IB08',1,'Guests shall park in the outer parking lot when using the 50 or 100-yard ranges.',1),(13,'IB10',4,'Consumption of alcohol is permitted in the Barnes Range House and Memorial Hall ONLY; no alcoholic beverages are permitted on or near any AGC range firing area.',1),(14,'IB11',2,'Pets shall be accompanied by their owner, leashed and under control at all times.',1),(15,'IB12',4,'AGC reserves the right to remove and permanently ban any member, non-member, guest or student without refund for violent, inappropriate, rude, disorderly, threatening, unsportsmanlike or intoxicated behavior.',1),(16,'IB13',2,'Parking is permitted in designated areas as posted.',1),(17,'IB14',2,'Driving onto or parking on any of the ranges is prohibited unless prior permission is granted by the RSO or Executive VP.',1),(18,'IB15',3,'Instruction or demonstration involving drawing from holster, or aiming, or aiming and dry firing is prohibited in all buildings.',1),(19,'IC03a',4,'Do not touch firearms during a Cease Fire! (firearm left loaded)',1),(20,'IC03b',3,'A Cease Fire is in effect from when it is called at the end of the day until the range is called HOT the following morning.',1),(21,'IC03d',2,'During a Cease Fire, all uncased firearms shall remain pointed downrange or racked in an upright position with actions open, magazines removed and Empty Chamber Indicator (ECI) in place.',1),(22,'IC03d',4,'During a Cease Fire, all uncased firearms shall not contain live rounds in the chamber/cylinder/fixed magazine or an inserted removable magazine.',1),(23,'IC03e',1,'During a Cease Fire, you shall remain behind the White Stripe when not pulling or posting targets.',1),(24,'IC04',2,'Badge-Holders shall, if they leave the firing line for any reason, safe their firearms per I.C.3.d., and instruct their guest, if any, to remain behind the White Stripe if not accompanying badge-holder down range..',1),(25,'IC05',4,'Firearms containing ammunition in any manner shall NOT be brought onto AGC property.',1),(26,'IC07',3,'All uncased firearms shall be carried muzzle up while being carried from place to place.',1),(27,'IC10a',2,'LOADED if an Empty Chamber Indicator (ECI) is not in place.',1),(28,'IC10b',2,'LOADED if actions, cylinders or loading gates are closed.',1),(29,'IC10c',2,'LOADED if empty cases are in the chamber/cylinder/fixed magazine, or if a removable empty magazine is inserted.',1),(30,'IC10c',4,'LOADED if cartridges are in the chamber/cylinder/fixed magazine, or if a removable magazine with cartridges is inserted.',1),(31,'IC10d',3,'LOADED if Black Powder Firearms containing: propellant, projectile or cap; powder in the pan of a flintlock.',1),(32,'IC11',2,'Uncased firearms shall NOT be brought onto or taken from the Concrete Pad when a Cease Fire is in effect.',1),(33,'IC12',3,'Cased firearms may be brought onto the Concrete Pad and placed on the ground or shooting bench at any time.  You shall NOT open the case or otherwise handle the firearm until the line is called HOT.',1),(34,'IC13',3,'Firearms shall be cased or uncased on the shooting bench/table and remain pointed downrange at all times while on the firing line.',1),(35,'IC14',2,'Containers of propellant shall be kept closed when not being used.',1),(36,'IC15',2,'Cleaning of firearms on the Concrete Pad is permitted with muzzles pointed downrange or upright.',1),(37,'IC16',2,'Cleaning of firearms off the Concrete Pad is permitted only if the firearm action is clearly disabled; firearm disassembled, bolt removed, etc.',1),(38,'IC16',4,'Cleaning of firearms with ammunition present.',1),(39,'IC17',1,'No one shall fire at any target not in their lane.',1),(40,'IC18',4,'No one shall fire at any wildlife.',1),(41,'IC19',4,'No one shall fire at any permanent structure or fixture or engage in willful destruction of property.',1),(42,'IC20',2,'Semi-automatic strings may be fired on any range at a rate that allows the aiming and control of each shot.  All shots fired must strike within the designated Impact Area for the shooter’s position.',1),(43,'IC21',2,'The Firing Line on the 50, 100 (lanes 1-90) and 200-yard ranges is the forward edge of the Concrete Pad. In the bench rest area (lanes 91-100) the firing line is the red zone.',1),(44,'IC22',2,'Shooters shall position themselves so the muzzle of their firearm is at or beyond the forward edge of the Concrete Pad.',1),(45,'IC22',3,'Under NO CIRCUMSTANCES will a firearm be discharged if the muzzle is behind any person.',1),(46,'IC23',3,'No one shall go forward of the Firing Line (See I.C.21) while the line is hot.',1),(47,'IC24',2,'If a firearm fails to fire, the muzzle shall remain pointed at the Impact Area for a minimum of 30 seconds before remedial action is taken.',1),(48,'IC25',3,'Firearms, ammunition and ammunition components shall not be stored on AGC property.',1),(49,'IC26',3,'Tracer, incendiary and explosive ammunition is prohibited.',1),(50,'IC27',2,'Targets and target frames must not be capable of deflecting a projectile in an unsafe direction.',1),(51,'IC28',3,'Fully automatic fire is only permitted as detailed in Chapter XII of the Policy & Procedures manual.',1),(52,'IC29',3,'Holstered firearms may be worn only under applicable Maryland law, within the constraints and conditions of your carry permit.',1),(53,'IC30',3,'Drawing from holsters is only permitted as detailed in Chapter  XXI of the Policy & Procedures Manual.',1),(54,'IC31',1,'Shooters shall clean up their area and police their brass and shotshell hulls when finished shooting and firearms are not being handled.',1),(55,'IIA01',2,'Rounds must hit impact berm',1),(56,'IIA02',1,'On the 50, 100 and 200-yard ranges, other than paper targets may be used provided that all fired rounds easily pass through them and strike the Impact Area. ',1),(57,'IIA03',1,'Pictures, caricatures or illustrations depicting real people are prohibited.',1),(58,'IIA04',3,'Exploding targets are prohibited.',1),(59,'IIA05',2,'Glass targets or those containing glass are prohibited.',1),(60,'IIA06',1,'Targets shall NOT be placed on the Impact Areas.',1),(61,'IIA07',3,'Targets shall NOT be placed on the Protective Berms.',1),(62,'IIA08',1,'Targets shall be placed in the location that matches the shooter’s lane number.',1),(63,'IIB02',2,'You must display your named yellow badge with certification sticker in addition to your range badge when shooting at steel targets.',1),(64,'IIB03',2,'Steel targets and their mounts shall be submitted for inspection and approval by the Executive VP or his/her designee before initial use and are subject to inspection at any time.',1),(65,'IIB3b',2,'Pitted, cratered, holed, bent, warped or otherwise damaged targets are prohibited.',1),(66,'IIB4a',2,'Prohibited ammunition: Rifle rounds exceeding 3150 fps muzzle velocity.',1),(67,'IIB4b',2,'Prohibited ammunition: Pistol rounds exceeding 1500 fps muzzle velocity.',1),(68,'IIB04c',2,'Prohibited ammunition: Any round with a muzzle velocity less than 750 fps.',1),(69,'IIB04d',2,'Prohibited ammunition: Any round labeled “Magnum”.',1),(70,'IIB04e',2,'Prohibited ammunition: Armor piercing, steel core or ‘penetrator’.',1),(71,'IIB04f',2,'Prohibited ammunition: 50 BMG and all long-range tactical rounds.',1),(72,'IIB04g',2,'Prohibited ammunition: Shotgun slugs.',1),(73,'IIB04h',2,'Prohibited ammunition: 5.7 X 28 ammunition',1),(74,'III01',2,'Smoking is prohibited within 15 feet of black powder or black powder substitutes.',1),(75,'III02',1,'Prior to loading, shooters using muzzle loading rifles or pistols shall fire caps on all nipples of percussion firearms, or a pan full of powder in a flintlock, while pointing the firearm downrange.',1),(76,'III03',3,'Muzzle loading firearms using granulated propellant shall have the propellant poured into the muzzle from a powder measure.',1),(77,'III04',2,'Containers of propellant shall be kept closed when not being used.',1),(78,'III05',2,'Shooters using muzzle loading rifles shall place their rifle muzzle up in a v-notch in the loading bench or some other device during a Cease Fire or during loading.',1),(79,'III06',2,'Percussion and flintlock firearms shall be positioned with the muzzle forward of the Firing Line and pointed downrange when a percussion cap is affixed or when the pan is charged.',1),(80,'III07',2,'Muzzle loading handguns shall be placed muzzle up in a loading stand or similar device during a Cease Fire.',1),(81,'IVA01',2,'This range is designated for the shooting of pistol-caliber handguns with barrels 10” or less in length.',1),(82,'IVA01a',2,'Handgun cartridges with ballistics between .22 rimfire and .500 S&W are permitted.',1),(83,'IVA02',2,'Rifle-caliber handguns are prohibited.',1),(84,'IVA03',2,'Shot shells shall NOT be fired on this range.',1),(85,'IVA04',1,'Firing from a position other than standing, or sitting on a stool, is prohibited.',1),(86,'IVA05',2,'When AGC-owned frames are used, only one target with a single, centered aiming point is permitted.',1),(87,'IVB02',1,'Positions to the left of the orange roof support pole (at lane 57) are normally closed to use. ',1),(88,'IVB03',1,'The 10 fixed benches on the far right of the Barnes range are for Benchrest Position shooting only, rifle and rifled shotgun shooting only.',1),(89,'IVB04',1,'In the Benchrest area, everyone must be behind the red zone while the line is hot and muzzles must meet or extend into the red zone which is considered the firing line.',1),(90,'IVB05',2,'Portable shooting benches shall be positioned so that the front legs are at the forward edge of the Concrete Pad.',1),(91,'IVC02',2,'Portable shooting benches shall be positioned so that the front legs are at the forward edge of the Concrete Pad.',1),(92,'IVC03',3,'An orange flag shall be displayed forward of the firing line when anyone is downrange.',1),(93,'IVC04',3,'The target carriages shall ONLY be used for firing properly sighted-in rifles at paper targets.',1),(94,'IVC05a',2,'A conventional bullseye target shall be centered in the target frame.',1),(95,'IVC05b',2,'Multiple aiming point targets, or any target other than a conventional bullseye target, shall be mounted with the aiming point no closer than 12” from the frame side members and all your shots must strike on the target paper.',1),(96,'IVC06',2,'Silhouettes, gongs, and spinners may be used for silhouette or hunting HANDGUN practice ONLY and shall be positioned directly in front of the 50, 100 or 150-meter berms or 200-meter Impact Area.',1),(97,'IVC07',2,'Firing a rifle at any target placed anywhere closer than 200 yards is prohibited.',1),(98,'IVC08',2,'Portable target frames may be placed behind the 200-yard pits immediately in front of the impact area.',1),(99,'IVC09',2,'Portable target frames with PAPER TARGETS may be placed atop the protective berm immediately forward of the pits.',1),(100,'IVC10',2,'An AGC-style portable wooden frame with PAPER TARGETS may be placed in the receptacles on the back side of the protective berm bulkhead above the pit roof.',1),(101,'IVC11',3,'Firing at objects placed on the protective berms is prohibited.',1),(102,'IVC12',3,'People may remain in the pits between ceasefires only during organized shoots/practices under the control of a designated Match Director.',1),(103,'IVC13',3,'No personnel are permitted in the 200-yard target pits when shooting steel targets.',1),(104,'IVC14',2,'Firearms shall NOT be left unattended.',1),(105,'IVC15',2,'Initial sighting in of firearms/scopes/sights is prohibited.',1),(106,'IVD02',2,'Shooting forward of the 16-yard line is prohibited.',1),(107,'IVD03',3,'Only shotguns firing a maximum powder load of 3 drams equivalent, shot size 7 1/2, 8 or 9, and a maximum muzzle velocity of 1200 fps are permitted.',1),(108,'IVD04',4,'Firing slugs is prohibited.',1),(109,'IVD05',2,'Shotguns shall remain unloaded with actions open at all times until on station and ready to shoot.',1),(110,'IVD05',4,'Shotgun actions shall remain open at all times until on station and ready to shoot.',1),(111,'IVD06',2,'When shooting handicaps, shooters may shoot from a staggered position not to exceed 2 yards.',1),(112,'IVD07',2,'Portable traps and other throwing devices may be used when positioned on or behind the 16-yard line.',1),(113,'IVD08',2,'No one shall proceed beyond a trap house when any other fields are in use.',1),(114,'IVD09',2,'Spent and/or unspent shot shells shall not be picked up until shooters have unloaded and racked their shotguns.',1),(115,'IVD10',2,'It is permitted to walk to the trap house if the field is ‘clear’.  Shooters shall unload and rack their shotguns prior to anyone going to the trap house.',1),(116,'IVD11',3,'When a person is in a trap house, an orange safety cone shall be placed on top of the trap house.',1),(117,'IVD12',2,'All firearms used on the Trap Range shall be fired from the shoulder.',1),(118,'IVD13',2,'Folding stocks shall be in the extended position.',1),(119,'IVE01',3,'This facility is intended for PATTERNING of shotguns ONLY.  ',1),(120,'IVE02',4,'Patterning targets shall have a single aiming point centered on the patterning board.',1),(121,'IVE03',4,'SLUGS are prohibited,',1),(122,'IVE04',4,'LEAD shot sizes larger than #2 are prohibited,',1),(123,'IVE05',4,'STEEL shot sizes larger than BBB are prohibited,',1),(124,'IVE06',4,'Placing of, or shooting at, objects on top of patterning frame is prohibited,',1),(125,'IVF01',2,'Sky drawing is prohibited.',1),(126,'IVF02',2,'Only field point or target arrows may be shot at the AGC targets.',1),(127,'IVF03',2,'Broad head arrows shall NOT be shot at AGC targets.',1),(128,'IVF05',2,'Archers shall designate a common Firing Line.',1),(129,'IVG03',2,'Only compressed air, carbon dioxide, and spring-powered guns firing .177 or .22 caliber blunt-nosed lead pellets weighing less than 25 grains may be fired on this range.',1),(130,'IVG04',2,'The maximum allowable velocity is 1000 fps for .177 pellets and 800 fps for .22 pellets.',1),(131,'IVG05',2,'Only paper targets or AGC-approved metal or metal-clad targets may be used.',1),(132,'IVG06',1,'Shooters shall be aligned properly with their pellet traps.',1),(133,'IB04',2,'Failure to return Guest Badge upon leaving the range.',0),(134,'IC03a',2,'Do not touch unloaded firearms during a ceasefire.',1),(135,'IB02',1,'Non-shooting club members shall wear their range badge or current AGC club membership card, readily visible, while on AGC Property.',1),(136,'IB11',2,'You are responsible for collecting and disposing of your pet’s waste.',1),(137,'IB17',4,'Items that have been designated as illegal by federal, state and/or local jurisdictions are prohibited on AGC property.',1),(138,'IC02a',1,'Eye and ear protection are required on or near active (hot) firing lines. EXCEPTIONS: Pellet Range, Archery Range.',1),(139,'IB05',1,'Badge-holders are responsible for and shall supervise their guest(s) at ALL TIMES.',1);
/*!40000 ALTER TABLE `rule_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `store_items`
--

DROP TABLE IF EXISTS `store_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `item` varchar(100) NOT NULL,
  `sku` varchar(15) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `type` varchar(45) NOT NULL,
  `paren` int(11) DEFAULT NULL,
  `img` varchar(255) DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  `active` int(1) NOT NULL DEFAULT '0',
  `new_badge` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`item_id`)
) ENGINE=MyISAM AUTO_INCREMENT=67 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `store_items`
--

LOCK TABLES `store_items` WRITE;
/*!40000 ALTER TABLE `store_items` DISABLE KEYS */;
INSERT INTO `store_items` VALUES (6,'Action Shooting',NULL,NULL,'Category',NULL,NULL,NULL,0,0),(25,'Holster Shooting - Certification Fee','410100',20.00,'Service',6,NULL,NULL,0,0),(7,'Steel 12\" Gong Kit','410200',100.00,'NonInventory',6,NULL,NULL,0,0),(12,'Steel 12\" Gong Only','410202',70.00,'NonInventory',6,NULL,NULL,0,0),(13,'Steel 2 X 4 Angle Mount','410204',40.00,'NonInventory',6,NULL,NULL,0,0),(20,'Steel 8\" Gong Kit','410206',80.00,'NonInventory',6,NULL,NULL,0,0),(21,'Steel 8\" Gong Only','410208',45.00,'NonInventory',6,NULL,NULL,0,0),(8,'Steel IPSC Kit','410210',120.00,'NonInventory',6,NULL,NULL,0,0),(19,'Steel IPSC Silhouette Plate','410212',90.00,'NonInventory',6,NULL,NULL,0,0),(24,'Steel Shooting - Certification Fee','410105',10.00,'Service',6,NULL,NULL,0,0),(22,'Steel Shooting Supplies - 2 X 4','410214',5.00,'NonInventory',6,NULL,NULL,0,0),(23,'Steel Shooting Supplies Nut & Bolt','410216',3.00,'NonInventory',6,NULL,NULL,0,0),(56,'AGC Club Dues',NULL,NULL,'Category',NULL,NULL,NULL,0,0),(58,'AGC - Affiliate Club Dues','445105',50.00,'Service',56,NULL,NULL,0,0),(57,'AGC - Charter Club Dues','445100',50.00,'Service',56,NULL,NULL,0,0),(59,'AGC - Club Initiation Fees','445110',200.00,'Service',56,NULL,NULL,0,0),(45,'AGC Member Classroom Rentals',NULL,NULL,'Category',NULL,NULL,NULL,0,0),(65,'Barnes Lower Classroom Rent  - Weekends','460118',40.00,'Service',45,NULL,NULL,0,0),(64,'Barnes Lower Classroom Rent - Weekdays','460116',20.00,'Service',45,NULL,NULL,0,0),(46,'Guest Bracelet Fee','460130',10.00,'Service',45,NULL,NULL,0,0),(61,'Memorial Hall Rent  - Weekends','460110',40.00,'Service',45,NULL,NULL,0,0),(60,'Memorial Hall Rent - Weekdays','460108',20.00,'Service',45,NULL,NULL,0,0),(62,'Memorial Hall Trap Room Rent - Weekdays','460112',20.00,'Service',45,NULL,NULL,0,0),(63,'Memorial Hall Trap Room Rent - Weekends','460114',40.00,'Service',45,NULL,NULL,0,0),(43,'CIO Organizations',NULL,NULL,'Category',NULL,NULL,NULL,0,0),(50,'CIO Annual Assessment Fee','435105',50.00,'Service',43,NULL,NULL,0,0),(55,'CIO Barnes Lower Classroom Rent -  Weekdays','435116',20.00,'Service',43,NULL,NULL,0,0),(66,'CIO Barnes Lower Classroom Rent - Weekends','435118',40.00,'Service',43,NULL,NULL,0,0),(44,'CIO Guest Bracelet Fee','435130',10.00,'Service',43,NULL,NULL,0,0),(49,'CIO Initiation Fee','435100',200.00,'Service',43,NULL,NULL,0,0),(51,'CIO Memorial Hall Rent - Weekdays','435108',20.00,'Service',43,NULL,NULL,0,0),(52,'CIO Memorial Hall Rent - Weekends','435110',40.00,'Service',43,NULL,NULL,0,0),(53,'CIO Memorial Hall Trap Room Rent - Weekdays','435112',20.00,'Service',43,NULL,NULL,0,0),(54,'CIO Memorial Hall Trap Room Rent - Weekends','435114',40.00,'Service',43,NULL,NULL,0,0),(40,'DNR Qualification Fees',NULL,NULL,'Category',NULL,NULL,NULL,0,0),(41,'Member DNR Qualification','420300',7.00,'Service',40,NULL,NULL,0,0),(42,'Non-Member DNR Qualification','420305',10.00,'Service',40,NULL,NULL,0,0),(37,'Hunter Sight-In',NULL,NULL,'Category',NULL,NULL,NULL,0,0),(38,'Member Hunter Sight-In','420200',5.00,'Service',37,NULL,NULL,0,0),(39,'Non-Member Hunter Sight-In','420205',7.00,'Service',37,NULL,NULL,0,0),(47,'Member Dues',NULL,NULL,'Category',NULL,NULL,NULL,0,0),(5,'Full Year Family Dues','450110',67.00,'Service',47,NULL,NULL,0,0),(3,'Full Year Individual Dues','450100',200.00,'Service',47,NULL,NULL,0,0),(4,'Full Year Junior Dues','450105',100.00,'Service',47,NULL,NULL,0,0),(11,'Half Year Family Dues','450125',32.00,'Service',47,NULL,NULL,0,0),(9,'Half Year Individual Dues','450115',95.00,'Service',47,NULL,NULL,0,0),(10,'Half Year Junior Dues','450120',48.00,'Service',47,NULL,NULL,0,0),(48,'NRA Paper Targets',NULL,NULL,'Category',NULL,NULL,NULL,0,0),(27,'NRA Paper Target - A-2315','430400',1.00,'NonInventory',48,NULL,NULL,0,0),(28,'NRA Paper Target - B-16','430402',1.00,'NonInventory',48,NULL,NULL,0,0),(29,'NRA Paper Target - ST-4','430404',1.00,'NonInventory',48,NULL,NULL,0,0),(30,'NRA Paper Target - TQ-4D','430406',1.00,'NonInventory',48,NULL,NULL,0,0),(1,'Sales',NULL,0.00,'Service',NULL,NULL,NULL,0,0),(15,'Shooting Supplies',NULL,NULL,'Category',NULL,NULL,NULL,0,0),(17,'Ear Plugs','430305',1.00,'NonInventory',15,NULL,NULL,0,0),(18,'ECI/OAI\'s','430300',1.00,'NonInventory',15,NULL,NULL,0,0),(16,'Target Frames','430200',25.00,'NonInventory',15,NULL,NULL,0,0),(31,'Trap Activities',NULL,NULL,'Category',NULL,NULL,NULL,0,0),(36,'Clay Birds - Case','405310',15.00,'NonInventory',31,NULL,NULL,0,0),(35,'Member Lincoln Trap Books','405115',5.00,'Service',31,NULL,NULL,0,0),(32,'Member Trap Books','405100',20.00,'Service',31,NULL,NULL,0,0),(34,'Member Youth Trap Books','405110',15.00,'Service',31,NULL,NULL,0,0),(33,'Non-Member Trap Books','405105',30.00,'Service',31,NULL,NULL,0,0);
/*!40000 ALTER TABLE `store_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `full_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `company` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `privilege` varchar(45) NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '10',
  `badge_number` int(5) DEFAULT NULL,
  `auth_key` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `clubs` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `password_reset_token` (`password_reset_token`)
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (0,'member','member@agc.com','',NULL,5,10,0,'x','x',NULL,0,0,NULL),(11,'marc','sharkbit@hotmail.com','Marc Riley',NULL,1,10,NULL,'n6v55GSvazLiu2o6O2z1lzNS5rXXwwnr','$2y$13$JBMfUH3mdSAAOAptbbDS3ebCXbiC4hW5b9/uLcAPHXdHHrZJC/xUm',NULL,1517415913,1518133273,NULL);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_privileges`
--

DROP TABLE IF EXISTS `user_privileges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_privileges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `privilege` varchar(99) NOT NULL,
  `priv_sort` int(3) NOT NULL,
  `timeout` int(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_privileges`
--

LOCK TABLES `user_privileges` WRITE;
/*!40000 ALTER TABLE `user_privileges` DISABLE KEYS */;
INSERT INTO `user_privileges` VALUES (1,'Root',1,60),(2,'Admin',20,30),(3,'RSO',40,20),(4,'View',65,15),(5,'Member',80,2),(6,'RSO Lead',30,20),(7,'Work Credits',60,15),(8,'CIO',64,5),(9,'Calendar_Coordinator',50,10),(10,'Cashier',41,15),(11, 'Charemen', 81, 10);
/*!40000 ALTER TABLE `user_privileges` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `violations`
--

DROP TABLE IF EXISTS `violations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `violations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `badge_reporter` int(5) NOT NULL,
  `vi_type` int(2) NOT NULL,
  `vi_override` tinyint(1) NOT NULL DEFAULT '0',
  `badge_involved` varchar(255) NOT NULL,
  `badge_witness` varchar(255) NOT NULL,
  `vi_date` datetime NOT NULL,
  `vi_loc` varchar(10) NOT NULL DEFAULT 'o',
  `vi_sum` varchar(255) NOT NULL,
  `vi_rules` varchar(255) NOT NULL,
  `vi_report` text NOT NULL,
  `vi_action` text NOT NULL,
  `was_guest` tinyint(1) NOT NULL DEFAULT '0',
  `hear_date` datetime DEFAULT NULL,
  `hear_sum` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=707 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `work_credits`
--

DROP TABLE IF EXISTS `work_credits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `work_credits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `badge_number` int(6) NOT NULL,
  `work_date` date DEFAULT NULL,
  `work_hours` float(8,2) NOT NULL,
  `project_name` varchar(255) NOT NULL,
  `remarks` text NOT NULL,
  `authorized_by` varchar(255) DEFAULT NULL,
  `supervisor` varchar(255) NOT NULL,
  `status` int(1) NOT NULL,
  `updated_at` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  `created_by` int(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=898 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;


-- #########################################################################################################
-- ### Current Database: `associat_agcnew` #################################################################
-- #########################################################################################################

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `associat_agcnew` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `associat_agcnew`;

--
-- Table structure for table `agc_calendar`
--

DROP TABLE IF EXISTS `agc_calendar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `agc_calendar` (
  `calendar_id` int(11) NOT NULL AUTO_INCREMENT,
  `recurrent_calendar_id` int(11) NOT NULL DEFAULT '0',
  `event_date` date NOT NULL,
  `club_id` int(11) NOT NULL DEFAULT '0',
  `facility_id` int(11) NOT NULL DEFAULT '0',
  `event_name` varchar(255) NOT NULL,
  `keywords` text NOT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `date_requested` datetime DEFAULT NULL,
  `lanes_requested` int(11) NOT NULL DEFAULT '0',
  `recur_every` int(11) DEFAULT '0',
  `pattern_type` int(11) NOT NULL DEFAULT '0',
  `recur_week_days` varchar(255) DEFAULT NULL,
  `recurrent_start_date` datetime DEFAULT NULL,
  `recurrent_end_date` datetime DEFAULT NULL,
  `event_status_id` int(11) NOT NULL DEFAULT '0',
  `range_status_id` int(11) NOT NULL DEFAULT '0',
  `conflict` tinyint(11) DEFAULT '0',
  `deleted` tinyint(11) NOT NULL DEFAULT '0',
  `approved` tinyint(11) NOT NULL DEFAULT '0',
  `active` tinyint(11) NOT NULL DEFAULT '0',
  `rollover` tinyint(11) NOT NULL DEFAULT '0',
  `time_format` tinyint(11) NOT NULL DEFAULT '1',
  `poc_badge` int(5) NOT NULL DEFAULT '0',
  `poc_name` varchar(255) DEFAULT NULL,
  `poc_phone` varchar(255) DEFAULT NULL,
  `poc_email` varchar(255) DEFAULT NULL,
  `display_order` int(11) NOT NULL DEFAULT '0',
  `remarks` text,
  PRIMARY KEY (`calendar_id`)
) ENGINE=MyISAM AUTO_INCREMENT=43947 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `agc_titles`
--

DROP TABLE IF EXISTS `agc_titles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `agc_titles` (
  `title_id` int(11) NOT NULL AUTO_INCREMENT,
  `agc` varchar(255) NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '0',
  `display_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`title_id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `agc_titles`
--

LOCK TABLES `agc_titles` WRITE;
/*!40000 ALTER TABLE `agc_titles` DISABLE KEYS */;
INSERT INTO `agc_titles` VALUES (1,'President',1,1),(2,'Vice President',1,2),(3,'Executive Vice President',1,3),(4,'Legislative Vice President',1,4),(5,'Treasurer',1,5),(6,'Secretary',1,6),(7,'RSSOP Chairman',1,7),(8,'RSO',1,8),(9,'High Power Chairman',1,9),(10,'Smallbore Chairman',1,10),(11,'Pistol Chairman',1,11),(12,'Trap Chairman',1,12),(13,'Renter',1,13),(15,'Staff RSO',1,15),(16,'Trap Official',1,16),(18,'Archery Chairman',1,18),(19,'Class III Chairman',1,19),(22,'Finance Committee',1,22),(27,'Range Badge Chairman',1,27),(29,'None',1,0);
/*!40000 ALTER TABLE `agc_titles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assets`
--

DROP TABLE IF EXISTS `assets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `assets` (
  `asset_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `active` tinyint(11) NOT NULL DEFAULT '0',
  `display_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`asset_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `badge_types`
--

DROP TABLE IF EXISTS `badge_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `badge_types` (
  `type_id` int(11) NOT NULL AUTO_INCREMENT,
  `option` varchar(255) NOT NULL,
  `price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `active` tinyint(4) NOT NULL DEFAULT '0',
  `display_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`type_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `badge_types`
--

LOCK TABLES `badge_types` WRITE;
/*!40000 ALTER TABLE `badge_types` DISABLE KEYS */;
INSERT INTO `badge_types` VALUES (1,'Full',150.00,1,1),(2,'Half',75.00,1,2),(3,'Junior',75.00,1,3),(4,'Half Junior',38.00,1,4),(5,'Family',50.00,1,5),(6,'Half Family',25.00,1,6),(7,'Junior Family',50.00,1,7),(8,'Half Junior Family',25.00,1,8),(9,'Life',9000.00,1,9);
/*!40000 ALTER TABLE `badge_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `badge_years`
--

DROP TABLE IF EXISTS `badge_years`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `badge_years` (
  `badge_year_id` int(11) NOT NULL AUTO_INCREMENT,
  `badge_year` int(11) NOT NULL DEFAULT '0',
  `color` varchar(25) NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '0',
  `display_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`badge_year_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `badge_years`
--

LOCK TABLES `badge_years` WRITE;
/*!40000 ALTER TABLE `badge_years` DISABLE KEYS */;
INSERT INTO `badge_years` VALUES (1,2010,'',1,1),(2,2011,'',1,2);
/*!40000 ALTER TABLE `badge_years` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blacklisted_emails`
--

DROP TABLE IF EXISTS `blacklisted_emails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blacklisted_emails` (
  `blacklisted_email_id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `first_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `display_order` int(11) NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`blacklisted_email_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `club_titles`
--

DROP TABLE IF EXISTS `club_titles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `club_titles` (
  `title_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '0',
  `display_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`title_id`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `club_titles`
--

LOCK TABLES `club_titles` WRITE;
/*!40000 ALTER TABLE `club_titles` DISABLE KEYS */;
INSERT INTO `club_titles` VALUES (1,'President',1,1),(2,'Vice President',1,2),(3,'Executive Officer',1,3),(4,'Treasurer',1,4),(5,'Secretary',1,5),(6,'Trustee',1,6),(16,'None',1,0),(23,'Alternate Trustee',1,22);
/*!40000 ALTER TABLE `club_titles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clubs`
--

DROP TABLE IF EXISTS `clubs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clubs` (
  `club_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `nick_name` varchar(255) NOT NULL,
  `ca` varchar(1) NOT NULL,
  `contact_first_name` varchar(255) NOT NULL,
  `contact_last_name` varchar(255) NOT NULL,
  `contact_phone` varchar(255) NOT NULL,
  `display_in_administration` tinyint(4) NOT NULL DEFAULT '0',
  `display_in_badges_administration` tinyint(4) NOT NULL DEFAULT '0',
  `is_cio` tinyint(4) NOT NULL DEFAULT '0',
  `active` tinyint(11) NOT NULL DEFAULT '0',
  `display_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`club_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1109 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clubs`
--

LOCK TABLES `clubs` WRITE;
/*!40000 ALTER TABLE `clubs` DISABLE KEYS */;
INSERT INTO `clubs` VALUES (2,'Arlington Rifle & Pistol Club','Arlington','C','C','','(410) 123-4567',1,0,0,1,1),(4,'Baltimore Rifle & Pistol Club','Baltimore','C','C','','(410) 123-4567',1,0,0,1,2),(7,'Chesapeake Rifle & Pistol Club','Chesapeake','C','C','','',1,1,0,1,3),(8,'Garrison Rifle & Revolver Club','Garrison','C','C','','(410) 437-4864',1,0,0,1,4),(9,'Glenmore Rifle & Pistol Club','Glenmore','C','C','','202-285-5914',1,1,0,1,5),(10,'Greenbelt Gun Club, Inc.','Greenbelt','C','C','','(410) 123-4567',1,0,0,1,6),(14,'Marriottsville Muzzle Loaders','MML','C','C','','(410) 123-4567',1,0,0,1,7),(11,'Homewood Rifle & Pistol Club','Homewood','C','C','','(410) 123-4567',1,0,0,1,8),(15,'Maryland Rifle Club','MD Rifle Club','C','C','','410-123-4567',1,0,0,1,9),(12,'Howard County Rifle & Pistol Club','Howard Co.','C','C','','(410) 123-4567',1,0,0,1,10),(20,'Monumental Rifle & Pistol Club','Monumental','C','C','','(410) 123-4567',1,0,0,1,11),(21,'Mount Washington Rod & Gun Club','Mt. Washington','C','C','','',1,0,0,1,12),(23,'Old Post Rifle and Pistol Club','Old Post','C','C','','(410) 123-4567',1,0,0,1,13),(25,'Stemmers Run Rifle & Pistol Club','Stemmers Run','C','C','','',1,1,0,1,14),(29,'Northrop Grumman Westinghouse Shooting Sports Club','NGWSSC','C','C','','(410) 123-4567',1,0,0,1,15),(1,'Applied Physics Laboratory Gun Club','APL','A','C','','(410) 123-4567',1,0,0,1,16),(6,'Catonsville Scouters Rifle & Pistol Club','Catonsville','A','C','','(410) 123-4567',1,0,0,1,17),(13,'Marriottsville Metallic Silhouette Shooters','MMSS','A','C','','(410) 123-4567',1,0,0,1,19),(18,'Meade Rifle & Pistol Club','Meade Rifle','A','C','','(410) 123-4567',1,0,0,1,20),(22,'NASA/Goddard Sportsmans Club','Goddard','A','C','','(410) 123-4567',1,0,0,1,21),(19,'Meade Rod & Gun Club','Meade R&G','A','C','','',1,0,0,1,22),(27,'Tidewater Muzzle Loaders','Tidewater','A','C','','(410) 123-4567',1,0,0,0,23),(16,'Maryland Tenth Cavalry Gun Club','MD Tenth Cavalry ','A','C','','202-253-0624',1,0,0,1,24),(5,'Berwyn Rod & Gun Club Inc.','Berwyn','A','C','','(410) 123-4567',1,0,0,1,25),(24,'Pioneer Rod & Gun Club Inc.','Pioneeer','A','C','','301-123-4567',1,0,0,1,26),(26,'Stoney Creek Hunting & Fishing Club','Stoney Creek','A','C','','(410) 123-4567',1,0,0,1,27),(33,'AGC','AGC','','O','','(410) 123-4567',1,0,0,1,28),(17,'Maryland Thompson Collectors Association','MDTCA','A','C','','',1,0,0,1,36),(1062,'B&S Personal Safety and Firearm Education','B&S','','O','','443-629-0311 ',1,1,1,1,38),(38,'MD Gun Training Center','MDGTC','','O','','410-499-3266',1,0,1,1,39),(1106,'blasterKraft Firearms Training','blasterKraft','','O','','410-921-0227',1,0,0,1,83),(37,'IOTA Firearms and Security Training','IOTA','','O','','410-750-3278',1,0,1,1,41),(28,'12th Precinct Pistol & Archery Club','12th Precinct','A','C','','',1,0,0,1,42),(1067,'Trident Security Training','Trident','','O','','443-983-8942',1,0,1,0,43),(1085,'AGC CIO Training','AGCCIO','','I','','301-996-6783',0,0,0,1,64),(1076,'NoVA-MD Self Defense, LLC','NoVA','','O','','443-904-1455',1,0,1,1,55),(1079,'AGC Training','Training','','I','','301-996-6783',0,0,0,1,58),(1077,'Monumental Rifle & Pistol','MRPC2','','O','','',0,1,0,1,56),(44,'Parks Firearms','Parks','','O','','4106450443',1,0,0,1,84),(40,'AGC-RSSOP','RSSOP','','I','','',1,0,0,1,79),(1098,'AGC EVP','AGC','','I','','302-857-9544',0,1,0,1,77),(31,'Maryland State Rifle & Pistol Association','MSRPA','A','C','','410-123-4567',1,1,0,1,62),(1084,'Protective Concepts','Protective','','O','','443-574-4116',1,0,1,1,63),(30,'Anne Arundel County Gun Club','AACGC','A','C','','443-517-7438',1,0,0,1,65),(1105,'AGC VP','AGC VP','','I','','4106983775',0,0,0,1,82),(1088,'Summit Training Group','Summit','','O','','410-598-2473',1,0,1,1,67),(32,'Givati Rifle and Pistol Club','Givati','A','C','','410-123-4567',1,0,0,1,68),(1092,'AGC','AGC','','I','','123-4567',0,0,0,1,71),(1093,'Super Admin','AGC','','I','','',0,0,0,1,72),(1108,'Defender one security','JKrieger','','','','4438470650',1,0,1,1,85),(39,'Baltimore Firearms Training','Baltimore Firearms','','O','','410-555-5555',1,1,1,1,74),(41,'AGC High Power','High Power','','I','','443-300-6360',1,0,0,1,81),(1096,'Antebellum Tutelage','Antebellum','','O','','410-971-2735',1,0,1,1,75),(1097,'JW Firearms Training and Certification','JW Firearms Training','','O','','443-864-0183',1,0,1,1,76),(34,'Gadsden Pew Club','Gadsden','A','C','','410-919-3460',1,0,0,1,78),(42,'AGC Action Shooting','ACTION','','I','','4105551212',1,0,0,1,80),(43,'AGC Walk','Walk-about','','I','','410-461-8532',1,0,0,1,1);
/*!40000 ALTER TABLE `clubs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_groups`
--

DROP TABLE IF EXISTS `contact_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact_groups` (
  `group_id` int(11) NOT NULL DEFAULT '0',
  `contact_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`group_id`,`contact_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_groups`
--

LOCK TABLES `contact_groups` WRITE;
/*!40000 ALTER TABLE `contact_groups` DISABLE KEYS */;
INSERT INTO `contact_groups` VALUES (1,115),(1,141),(1,142),(1,143),(1,144),(1,145),(1,146),(1,147),(1,148),(1,149),(1,150),(1,151),(1,152),(1,153),(1,154),(1,155),(1,156),(1,157),(1,158),(1,159),(1,160),(1,161),(1,162),(1,163),(1,165),(1,166),(1,167),(1,168),(1,169),(1,170),(1,171),(1,172),(1,173),(1,174),(1,175),(1,176),(1,177),(1,178),(1,179),(1,180),(1,181),(1,182),(1,183),(1,184),(1,185),(1,186),(1,187),(2,1),(2,2),(2,3),(2,4),(2,5),(2,6),(2,7),(2,8),(2,9),(2,10),(2,11),(2,12),(2,13),(2,14),(2,15),(2,16),(2,17),(2,18),(2,19),(2,20),(2,21),(2,22),(2,23),(2,24),(2,25),(2,26),(2,27),(2,28),(2,29),(2,30),(2,31),(2,32),(2,33),(2,34),(2,35),(2,36),(2,37),(2,38),(2,39),(2,40),(2,41),(2,42),(2,43),(2,44),(2,46),(2,47),(2,48),(2,49),(2,50),(2,51),(2,52),(2,53),(2,54),(2,55),(2,56),(2,57),(2,58),(2,59),(2,60),(2,61),(2,63),(2,64),(2,65),(2,66),(2,67),(2,68),(2,69),(2,70),(2,71),(2,72),(2,73),(2,74),(2,75),(2,76),(2,77),(2,78),(2,79),(2,80),(2,81),(2,82),(2,83),(2,84),(2,85),(2,86),(2,87),(2,88),(2,89),(2,90),(2,91),(2,92),(2,93),(2,94),(2,95),(2,96),(2,97),(2,98),(2,99),(2,100),(2,101),(2,102),(2,103),(2,104),(2,105),(2,106),(2,107),(2,108),(2,109),(2,110),(2,111),(2,112),(2,113),(2,114),(2,116),(2,117),(2,118),(2,119),(2,120),(2,121),(2,122),(2,123),(2,124),(2,125),(2,126),(2,127),(2,128),(2,129),(2,130),(2,131),(2,132),(2,133),(2,134),(2,135),(2,136),(2,137),(2,138),(2,139),(2,140),(2,211),(2,212),(3,154),(3,155),(3,156),(3,157),(3,158),(3,159),(3,160),(3,161),(3,162),(3,163),(3,178),(3,186),(4,3),(4,12),(4,24),(4,25),(4,26),(4,27),(4,28),(4,29),(4,30),(4,32),(4,33),(4,34),(4,35),(4,36),(4,38),(4,40),(4,41),(4,42),(4,43),(4,44),(4,46),(4,47),(4,98),(5,115),(5,176),(5,177),(5,179),(5,180),(5,181),(5,182),(5,183),(5,184),(5,185),(6,10),(6,15),(6,94),(6,95),(6,96),(6,97),(6,99),(6,100),(6,101),(6,102),(6,103),(6,104),(6,105),(6,106),(6,107),(6,108),(6,109),(6,110),(6,111),(6,113),(6,116),(6,211),(7,1),(7,2),(7,3),(7,4),(7,5),(7,6),(7,7),(7,8),(7,9),(7,10),(7,11),(7,12),(7,13),(7,14),(7,15),(7,16),(7,17),(7,18),(7,19),(7,20),(7,21),(7,22),(7,23),(7,24),(7,25),(7,26),(7,27),(7,28),(7,29),(7,30),(7,31),(7,32),(7,33),(7,34),(7,35),(7,36),(7,37),(7,38),(7,39),(7,40),(7,41),(7,42),(7,43),(7,44),(7,46),(7,47),(7,48),(7,49),(7,50),(7,51),(7,52),(7,53),(7,54),(7,55),(7,56),(7,57),(7,58),(7,59),(7,60),(7,61),(7,63),(7,64),(7,65),(7,66),(7,67),(7,68),(7,69),(7,70),(7,71),(7,72),(7,73),(7,74),(7,75),(7,76),(7,77),(7,78),(7,79),(7,80),(7,81),(7,82),(7,83),(7,84),(7,85),(7,86),(7,87),(7,88),(7,89),(7,90),(7,91),(7,92),(7,93),(7,94),(7,95),(7,96),(7,97),(7,98),(7,99),(7,100),(7,101),(7,102),(7,103),(7,104),(7,105),(7,106),(7,107),(7,108),(7,109),(7,110),(7,111),(7,112),(7,113),(7,114),(7,115),(7,116),(7,117),(7,118),(7,119),(7,120),(7,121),(7,122),(7,123),(7,124),(7,125),(7,126),(7,127),(7,128),(7,129),(7,130),(7,131),(7,132),(7,133),(7,134),(7,135),(7,136),(7,137),(7,138),(7,139),(7,140),(7,141),(7,142),(7,143),(7,144),(7,145),(7,146),(7,147),(7,148),(7,149),(7,150),(7,151),(7,152),(7,153),(7,154),(7,155),(7,156),(7,157),(7,158),(7,159),(7,160),(7,161),(7,162),(7,163),(7,165),(7,166),(7,167),(7,168),(7,169),(7,170),(7,171),(7,172),(7,173),(7,174),(7,175),(7,176),(7,177),(7,178),(7,179),(7,180),(7,181),(7,182),(7,183),(7,184),(7,185),(7,186),(7,187),(7,211),(7,212),(8,1),(8,67),(8,114),(8,118),(8,119),(8,120),(8,121),(8,122),(8,123),(8,124),(8,125),(8,126),(8,127),(8,128),(8,129),(8,132),(8,133),(8,134),(8,135),(8,136),(8,137),(8,139),(8,140),(9,31),(9,39),(9,73),(9,75),(9,76),(9,77),(9,78),(9,79),(9,80),(9,81),(9,82),(9,83),(9,84),(9,85),(9,86),(9,87),(9,88),(9,89),(9,90),(9,91),(9,92),(9,93),(9,138),(12,165),(12,166),(12,167),(12,168),(12,169),(12,170),(12,171),(12,172),(12,173),(12,174),(12,175),(13,49),(13,50),(13,51),(13,52),(13,53),(13,54),(13,55),(13,56),(13,57),(13,58),(13,59),(13,60),(13,61),(13,63),(13,64),(13,65),(13,66),(13,68),(13,69),(13,70),(13,71),(13,130),(13,131),(16,196),(16,197),(16,198),(16,199),(16,200),(16,201),(16,202),(16,203),(16,204),(16,205),(16,206),(16,207),(16,208),(16,209),(16,210),(17,188),(17,189),(17,190),(17,191),(17,192),(17,193),(17,194),(17,195),(18,2),(18,4),(18,5),(18,6),(18,7),(18,8),(18,9),(18,11),(18,13),(18,14),(18,16),(18,17),(18,18),(18,19),(18,20),(18,21),(18,22),(18,23),(18,37),(18,48),(18,74),(18,112),(18,117),(18,212),(19,141),(19,142),(19,143),(19,144),(19,145),(19,146),(19,147),(19,148),(19,149),(19,150),(19,151),(19,152),(19,187);
/*!40000 ALTER TABLE `contact_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contacts`
--

DROP TABLE IF EXISTS `contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contacts` (
  `contact_id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `office` varchar(255) NOT NULL,
  `district` varchar(255) NOT NULL,
  `committee` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `display_order` int(11) NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`contact_id`)
) ENGINE=MyISAM AUTO_INCREMENT=213 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contacts`
--

LOCK TABLES `contacts` WRITE;
/*!40000 ALTER TABLE `contacts` DISABLE KEYS */;
INSERT INTO `contacts` VALUES (8,'Gabriel','','Acevero','gabriel.acevero@house.state.md.us','Delegate','','39','APP',1,8,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(5,'Benjamin S.','','Barnes','ben.barnes@house.state.md.us','Delegate','','21','APP',1,5,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(13,'Wendell R.','','Beitzel','wendell.beitzel@house.state.md.us','Delegate','','1A','APP',1,13,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(37,'Tony','','Bridges','tony.bridges@house.state.md.us','Delegate','','41','APP',1,37,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(6,'Mark S.','','Chang','mark.chang@house.state.md.us','Delegate','','32','APP',1,6,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(112,'Paul D.','','Corderman','paul.corderman@house.state.md.us','Delegate','','2B','APP',1,112,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(7,'Jefferson L.','','Ghrist','jeff.ghrist@house.state.md.us','Delegate','','36','APP',1,7,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(21,'Keith E.','','Haynes','keith.haynes@house.state.md.us','Delegate','','44A','APP',1,21,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(117,'Shaneka','','Henson','shaneka.henson@house.state.md.us','Delegate','','30A','APP',1,117,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(2,'Shelly L.','','Hettleman','shelly.hettleman@house.state.md.us','Delegate','','11','APP',1,2,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(18,'Michael A.','','Jackson','michael.jackson@house.state.md.us','Delegate','','27B','APP',1,18,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(23,'Trent M.','','Kittleman','trent.kittleman@house.state.md.us','Delegate','','9A','APP',1,23,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(4,'Marc A.','','Korman','marc.korman@house.state.md.us','Delegate','','16','APP',1,4,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(19,'Carol L.','','Krimm','carol.krimm@house.state.md.us','Delegate','','3A','APP',1,19,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(20,'Nino','','Mangione','nino.mangione@house.state.md.us','Delegate','','42B','APP',1,20,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(11,'Maggie','','McIntosh','maggie.mcintosh@house.state.md.us','Delegate','','43','APP',1,11,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(14,'Michael W.','','McKay','mike.mckay@house.state.md.us','Delegate','','1C','APP',1,14,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(74,'Richard W.','','Metzgar','ric.metzgar@house.state.md.us','Delegate','','6','APP',1,74,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(17,'Elizabeth G. (Susie)','','Proctor','susie.proctor@house.state.md.us','Delegate','','27A','APP',1,17,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(9,'Kirill','','Reznik','kirill.reznik@house.state.md.us','Delegate','','39','APP',1,9,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(16,'Geraldine','','Valentino-Smith','geraldine.valentino@house.state.md.us','Delegate','','23A','APP',1,16,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(22,'Patrick G.','','Young Jr.','pat.young@house.state.md.us','Delegate','','44B','APP',1,22,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(47,'Carl L.','','Anderton Jr.','carl.anderton@house.state.md.us','Delegate','','38B','E&T',1,47,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(36,'Dalya','','Attar','dalya.attar@house.state.md.us','Delegate','','41','E&T',1,36,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(29,'Kumar P.','','Barve','kumar.barve@house.state.md.us','Delegate','','17','E&T',1,29,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(38,'Regina T.','','Boyce','regina.boyce@house.state.md.us','Delegate','','43','E&T',1,38,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(24,'Barrie S.','','Ciliberti','barrie.ciliberti@house.state.md.us','Delegate','','4','E&T',1,24,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(42,'Gerald W. (Jerry)','','Clark','jerry.clark@house.state.md.us','Delegate','','29C','E&T',1,42,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(27,'David','','Fraser-Hidalgo','david.fraser.hidalgo@house.state.md.us','Delegate','','15','E&T',1,27,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(30,'James W.','','Gilchrist','jim.gilchrist@house.state.md.us','Delegate','','17','E&T',1,30,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(34,'Andrea Fletcher','','Harrison','andrea.harrison@house.state.md.us','Delegate','','24','E&T',1,34,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(40,'Anne','','Healey','anne.healey@house.state.md.us','Delegate','','22','E&T',1,40,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(41,'Marvin E.','','Holmes Jr.','marvin.holmes@house.state.md.us','Delegate','','23B','E&T',1,41,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(35,'Jay A.','','Jacobs','jay.jacobs@house.state.md.us','Delegate','','36','E&T',1,35,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(25,'Jay','','Jalisi','jay.jalisi@house.state.md.us','Delegate','','10','E&T',1,25,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(33,'Mary A.','','Lehman','mary.lehman@house.state.md.us','Delegate','','21','E&T',1,33,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(12,'Brooke E.','','Lierman','brooke.lierman@house.state.md.us','Delegate','','46','E&T',1,12,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(28,'Sara N.','','Love','sara.love@house.state.md.us','Delegate','','16','E&T',1,28,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(46,'Charles J.','','Otto','charles.otto@house.state.md.us','Delegate','','38A','E&T',1,46,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(43,'Neil C.','','Parrott','neil.parrott@house.state.md.us','Delegate','','2A','E&T',1,43,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(26,'Dana M.','','Stein','dana.stein@house.state.md.us','Delegate','','11','E&T',1,26,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(32,'Vaughn M.','','Stewart III','vaughn.stewart@house.state.md.us','Delegate','','19','E&T',1,32,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(3,'Jennifer R.','','Terrasa','jennifer.terrasa@house.state.md.us','Delegate','','13','E&T',1,3,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(98,'Melissa','','Wells','melissa.wells@house.state.md.us','Delegate','','40','E&T',1,98,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(44,'William J.','','Wivell','william.wivell@house.state.md.us','Delegate','','2A','E&T',1,44,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(68,'Christopher T.','','Adams','christopher.adams@house.state.md.us','Delegate','','37B','ECM',1,68,'2020-01-09 22:32:05','2020-01-11 11:09:33'),(60,'Steven J.','','Arentz','steven.arentz@house.state.md.us','Delegate','','36','ECM',1,60,'2020-01-09 22:32:05','2020-01-11 11:16:23'),(61,'Talmadge','','Branch','talmadge.branch@house.state.md.us','Delegate','','45','ECM',1,61,'2020-01-09 22:32:05','2020-01-11 11:16:33'),(51,'Benjamin T.','','Brooks Sr.','benjamin.brooks@house.state.md.us','Delegate','','10','ECM',1,51,'2020-01-09 22:32:05','2020-01-11 11:14:13'),(66,'Edward P. (Ned)','','Carey','ned.carey@house.state.md.us','Delegate','','31A','ECM',1,66,'2020-01-09 22:32:05','2020-01-11 11:17:36'),(55,'Lorig','','Charkoudian','lorig.charkoudian@house.state.md.us','Delegate','','20','ECM',1,55,'2020-01-09 22:32:05','2020-01-11 11:15:10'),(64,'Brian M.','','Crosby','brian.crosby@house.state.md.us','Delegate','','29B','ECM',1,64,'2020-01-09 22:32:05','2020-01-11 11:17:06'),(56,'Dereck E.','','Davis','dereck.davis@house.state.md.us','Delegate','','25','ECM',1,56,'2020-01-09 22:32:05','2020-01-11 11:15:22'),(53,'Kathleen M.','','Dumais','kathleen.dumais@house.state.md.us','Delegate','','15','ECM',1,53,'2020-01-09 22:32:05','2020-01-11 11:14:47'),(70,'Diana M.','','Fennell','diana.fennell@house.state.md.us','Delegate','','47A','ECM',1,70,'2020-01-09 22:32:05','2020-01-11 11:18:15'),(63,'Mark N.','','Fisher','mark.fisher@house.state.md.us','Delegate','','27C','ECM',1,63,'2020-01-09 22:32:05','2020-01-11 11:16:53'),(65,'Seth A.','','Howard','seth.howard@house.state.md.us','Delegate','','30B','ECM',1,65,'2020-01-09 22:32:05','2020-01-11 11:17:21'),(49,'Richard K.','','Impallaria','rick.impallaria@house.state.md.us','Delegate','','7','ECM',1,49,'2020-01-09 22:32:05','2020-01-11 11:13:51'),(69,'John F.','','Mautz IV','johnny.mautz@house.state.md.us','Delegate','','37B','ECM',1,69,'2020-01-09 22:32:05','2020-01-11 11:18:03'),(71,'Warren E.','','Miller','warren.miller@house.state.md.us','Delegate','','9A','ECM',1,71,'2020-01-09 22:32:05','2020-01-11 11:18:28'),(54,'Lily','','Qi','lily.qi@house.state.md.us','Delegate','','15','ECM',1,54,'2020-01-09 22:32:05','2020-01-11 11:15:00'),(52,'Pamela E.','','Queen','pam.queen@house.state.md.us','Delegate','','14','ECM',1,52,'2020-01-09 22:32:05','2020-01-11 11:14:35'),(59,'Michael J.','','Rogers','michael.rogers@house.state.md.us','Delegate','','32','ECM',1,59,'2020-01-09 22:32:05','2020-01-11 11:16:12'),(130,'Veronica L.','','Turner','veronica.turner@house.state.md.us','Delegate','','26','ECM',1,130,'2020-01-09 22:32:05','2020-01-11 11:18:51'),(57,'Kriselda','','Valderrama','kris.valderrama@house.state.md.us','Delegate','','26','ECM',1,57,'2020-01-09 22:32:05','2020-01-11 11:15:32'),(72,'M. Courtney','','Watson','courtney.watson@house.state.md.us','Delegate','','9B','ECM',1,72,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(58,'C. T.','','Wilson','ct.wilson@house.state.md.us','Delegate','','28','ECM',1,58,'2020-01-09 22:32:05','2020-01-11 11:15:54'),(84,'Heather','','Bagnall','heather.bagnall@house.state.md.us','Delegate','','33','HGO',1,84,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(82,'Erek L.','','Barron','erek.barron@house.state.md.us','Delegate','','24','HGO',1,82,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(76,'Harry (H. B.)','','Bhandari','harry.bhandari@house.state.md.us','Delegate','','8','HGO',1,76,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(31,'Alfred C.','','Carr Jr.','alfred.carr@house.state.md.us','Delegate','','18','HGO',1,31,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(83,'Nick','','Charles','nick.charles@house.state.md.us','Delegate','','25','HGO',1,83,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(88,'Brian A.','','Chisholm','brian.chisholm@house.state.md.us','Delegate','','31B','HGO',1,88,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(80,'Bonnie L.','','Cullison','bonnie.cullison@house.state.md.us','Delegate','','19','HGO',1,80,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(77,'Terri L.','','Hill','terri.hill@house.state.md.us','Delegate','','12','HGO',1,77,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(90,'Steve','','Johnson','steve.johnson@house.state.md.us','Delegate','','34A','HGO',1,90,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(79,'Ariana B.','','Kelly','ariana.kelly@house.state.md.us','Delegate','','16','HGO',1,79,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(93,'Kenneth P.','','Kerr','ken.kerr@house.state.md.us','Delegate','','3B','HGO',1,93,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(89,'Nicholaus R.','','Kipke','nicholaus.kipke@house.state.md.us','Delegate','','31B','HGO',1,89,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(73,'Susan W.','','Krebs','susan.krebs@house.state.md.us','Delegate','','5','HGO',1,73,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(39,'Robbyn T.','','Lewis','robbyn.lewis@house.state.md.us','Delegate','','46','HGO',1,39,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(87,'Matt','','Morgan','matthew.morgan@house.state.md.us','Delegate','','29A','HGO',1,87,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(81,'Joseline A.','','Pena-Melnyk','joseline.pena.melnyk@house.state.md.us','Delegate','','21','HGO',1,81,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(78,'Shane E.','','Pendergrass','shane.pendergrass@house.state.md.us','Delegate','','13','HGO',1,78,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(138,'Teresa E.','','Reilly','teresa.reilly@house.state.md.us','Delegate','','35B','HGO',1,138,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(86,'Samuel I.','','Rosenberg','samuel.rosenberg@house.state.md.us','Delegate','','41','HGO',1,86,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(85,'Sid A.','','Saab','sid.saab@house.state.md.us','Delegate','','33','HGO',1,85,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(91,'Sheree','','Sample-Hughes','sheree.sample.hughes@house.state.md.us','Delegate','','37A','HGO',1,91,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(75,'Kathy','','Szeliga','kathy.szeliga@house.state.md.us','Delegate','','7','HGO',1,75,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(92,'Karen Lewis','','Young','karen.young@house.state.md.us','Delegate','','3A','HGO',1,92,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(10,'Curtis S. (Curt)','','Anderson','curt.anderson@house.state.md.us','Delegate','','43','JUD',1,10,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(97,'Lauren R.','','Arikan','lauren.arikan@house.state.md.us','Delegate','','7','JUD',1,97,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(100,'Vanessa E.','','Atterbeary','vanessa.atterbeary@house.state.md.us','Delegate','','13','JUD',1,100,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(106,'J. Sandy','','Bartlett','sandy.bartlett@house.state.md.us','Delegate','','32','JUD',1,106,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(99,'Jon S.','','Cardin','jon.cardin@house.state.md.us','Delegate','','11','JUD',1,99,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(110,'Luke H.','','Clippinger','luke.clippinger@house.state.md.us','Delegate','','46','JUD',1,110,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(109,'Frank M.','','Conaway Jr.','frank.conaway@house.state.md.us','Delegate','','40','JUD',1,109,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(94,'Daniel L.','','Cox','dan.cox@house.state.md.us','Delegate','','4','JUD',1,94,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(102,'Charlotte','','Crutchfield','charlotte.crutchfield@house.state.md.us','Delegate','','19','JUD',1,102,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(105,'Debra M.','','Davis','debra.davis@house.state.md.us','Delegate','','28','JUD',1,105,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(116,'Wanika B.','','Fisher','wanika.fisher@house.state.md.us','Delegate','','47B','JUD',1,116,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(96,'Robin L.','','Grammer Jr.','robin.grammer@house.state.md.us','Delegate','','6','JUD',1,96,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(114,'Wayne A.','','Hartman','wayne.hartman@house.state.md.us','Delegate','','38C','W&M',1,114,'2020-01-09 22:32:05','2020-01-16 23:53:15'),(104,'Jazz M.','','Lewis','jazz.lewis@house.state.md.us','Delegate','','24','JUD',1,104,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(108,'Lesley J.','','Lopez','lesley.lopez@house.state.md.us','Delegate','','39','JUD',1,108,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(107,'Michael E.','','Malone','michael.malone@house.state.md.us','Delegate','','33','JUD',1,107,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(113,'Susan K.','','McComas','susan.mccomas@house.state.md.us','Delegate','','34B','JUD',1,113,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(103,'David','','Moon','david.moon@house.state.md.us','Delegate','','20','JUD',1,103,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(95,'Jesse T.','','Pippy','jesse.pippy@house.state.md.us','Delegate','','4','JUD',1,95,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(101,'Emily K.','','Shetty','emily.shetty@house.state.md.us','Delegate','','18','JUD',1,101,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(115,'Charles E.','','Sydnor III','charles.sydnor@senate.state.md.us','Senator','','44','JPR',1,115,'2020-01-09 22:32:05','2020-01-16 23:21:08'),(111,'Ronald L.','','Watson','ron.watson@house.state.md.us','Delegate','','23B','JUD',1,111,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(129,'Darryl','','Barnes','darryl.barnes@house.state.md.us','Delegate','','25','W&M',1,129,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(121,'Joseph C.','','Boteler III','joseph.boteler@house.state.md.us','Delegate','','8','W&M',1,121,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(135,'Jason C.','','Buckel','jason.buckel@house.state.md.us','Delegate','','1B','W&M',1,135,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(136,'Alice J.','','Cain','alice.cain@house.state.md.us','Delegate','','30A','W&M',1,136,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(126,'Julie Palakovich','','Carr','julie.palakovichcarr@house.state.md.us','Delegate','','17','W&M',1,126,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(122,'Eric D.','','Ebersole','eric.ebersole@house.state.md.us','Delegate','','12','W&M',1,122,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(123,'Jessica M.','','Feldmark','jessica.feldmark@house.state.md.us','Delegate','','12','W&M',1,123,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(139,'Michele J.','','Guyton','michele.guyton@house.state.md.us','Delegate','','42B','W&M',1,139,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(137,'Kevin B.','','Hornberger','kevin.hornberger@house.state.md.us','Delegate','','35A','W&M',1,137,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(140,'Julian','','Ivey','julian.ivey@house.state.md.us','Delegate','','47A','W&M',1,140,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(1,'Adrienne A.','','Jones','adrienne.jones@house.state.md.us','Delegate','','10','W&M',1,1,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(124,'Anne R.','','Kaiser','anne.kaiser@house.state.md.us','Delegate','','14','W&M',1,124,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(67,'Mary Ann','','Lisanti','maryann.lisanti@house.state.md.us','Delegate','','34A','W&M',1,67,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(120,'Robert B.','','Long','bob.long@house.state.md.us','Delegate','','6','W&M',1,120,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(125,'Eric G.','','Luedtke','eric.luedtke@house.state.md.us','Delegate','','14','W&M',1,125,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(133,'Nick J.','','Mosby','nick.mosby@house.state.md.us','Delegate','','40','W&M',1,133,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(132,'Edith J.','','Patterson','edith.patterson@house.state.md.us','Delegate','','28','W&M',1,132,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(118,'April R.','','Rose','april.rose@house.state.md.us','Delegate','','5','W&M',1,118,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(119,'Haven N.','','Shoemaker Jr.','haven.shoemaker@house.state.md.us','Delegate','','5','W&M',1,119,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(134,'Stephanie M.','','Smith','stephanie.smith@house.state.md.us','Delegate','','45','W&M',1,134,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(131,'Jay','','Walker','jay.walker@house.state.md.us','Delegate','','26','ECM',1,131,'2020-01-09 22:32:05','2020-01-16 23:42:20'),(128,'Alonzo T.','','Washington','alonzo.washington@house.state.md.us','Delegate','','22','W&M',1,128,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(127,'Jheanelle K.','','Wilkins','jheanelle.wilkins@house.state.md.us','Delegate','','20','W&M',1,127,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(48,'Catherine','','Forbes','catherine.forbes@house.state.md.us','Delegate','','42A','APP',1,48,'2020-01-09 22:32:05','2020-01-11 09:32:59'),(50,'Carl','','Jackson','carl.jackson@house.state.md.us','Delegate','','8','ECM',1,50,'2020-01-09 22:32:05','2020-01-11 11:14:04'),(15,'Nicole','A','Williams','nicole.williams@house.state.md.us','Delegate','','22','JUD',1,15,'2020-01-09 22:32:05','2020-01-09 22:57:07'),(141,'George C.','','Edwards','george.edwards@senate.state.md.us','Senator','','1','B&T',1,141,'2020-01-09 22:32:05','2020-01-11 11:04:59'),(142,'Andrew A.','','Serafini','andrew.serafini@senate.state.md.us','Senator','','2','B&T',1,142,'2020-01-09 22:32:05','2020-01-11 11:07:57'),(143,'Johnny Ray','','Salling','johnnyray.salling@senate.state.md.us','Senator','','6','B&T',1,143,'2020-01-09 22:32:05','2020-01-11 11:07:44'),(144,'Guy J.','','Guzzone','guy.guzzone@senate.state.md.us','Senator','','13','B&T',1,144,'2020-01-09 22:32:05','2020-01-11 11:06:32'),(145,'Craig J.','','Zucker','craig.zucker@senate.state.md.us','Senator','','14','B&T',1,145,'2020-01-09 22:32:05','2020-01-11 11:08:09'),(146,'James C.','','Rosapepe','jim.rosapepe@senate.state.md.us','Senator','','21','B&T',1,146,'2020-01-09 22:32:05','2020-01-11 11:07:31'),(147,'Douglas J. J.','','Peters','douglas.peters@senate.state.md.us','Senator','','23','B&T',1,147,'2020-01-09 22:32:05','2020-01-11 11:07:14'),(148,'Melony G.','','Griffith','melony.griffith@senate.state.md.us','Senator','','25','B&T',1,148,'2020-01-09 22:32:05','2020-01-11 11:06:14'),(149,'Sarah K.','','Elfreth','sarah.elfreth@senate.state.md.us','Senator','','30','B&T',1,149,'2020-01-09 22:32:05','2020-01-11 11:05:42'),(150,'Adelaide C.','','Eckardt','adelaide.eckardt@senate.state.md.us','Senator','','37','B&T',1,150,'2020-01-09 22:32:05','2020-01-11 10:59:14'),(151,'Nancy J.','','King','nancy.king@senate.state.md.us','Senator','','39','B&T',1,151,'2020-01-09 22:32:05','2020-01-11 11:06:46'),(152,'Cory V.','','McCray','cory.mccray@senate.state.md.us','Senator','','45','B&T',1,152,'2020-01-09 22:32:05','2020-01-11 11:06:59'),(153,'William C.','','Ferguson IV','bill.ferguson@senate.state.md.us','Senator','','46','',1,153,'2020-01-09 22:32:05','2020-01-16 23:06:42'),(154,'Ronald N.','','Young','ronald.young@senate.state.md.us','Senator','','3','EHEA',1,154,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(155,'Clarence K.','','Lam','clarence.lam@senate.state.md.us','Senator','','12','EHEA',1,155,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(156,'Cheryl C.','','Kagan','cheryl.kagan@senate.state.md.us','Senator','','17','EHEA',1,156,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(157,'Paul G.','','Pinsky','paul.pinsky@senate.state.md.us','Senator','','22','EHEA',1,157,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(158,'Obie','','Patterson','obie.patterson@senate.state.md.us','Senator','','26','EHEA',1,158,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(159,'Arthur','','Ellis','arthur.ellis@senate.state.md.us','Senator','','28','EHEA',1,159,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(160,'John D. (Jack)','','Bailey','jack.bailey@senate.state.md.us','Senator','','29','EHEA',1,160,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(161,'Bryan W.','','Simonaire','bryan.simonaire@senate.state.md.us','Senator','','31','EHEA',1,161,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(162,'Jason C.','','Gallion','jason.gallion@senate.state.md.us','Senator','','35','EHEA',1,162,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(163,'Mary Beth','','Carozza','marybeth.carozza@senate.state.md.us','Senator','','38','EHEA',1,163,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(165,'J. B.','','Jennings','jb.jennings@senate.state.md.us','Senator','','7','FIN',1,165,'2020-01-09 22:32:05','2020-01-11 10:56:25'),(166,'Katherine A.','','Klausmeier','katherine.klausmeier@senate.state.md.us','Senator','','8','FIN',1,166,'2020-01-09 22:32:05','2020-01-11 10:57:03'),(167,'Delores G.','','Kelley','delores.kelley@senate.state.md.us','Senator','','10','FIN',1,167,'2020-01-09 22:32:05','2020-01-11 10:56:42'),(168,'Brian J.','','Feldman','brian.feldman@senate.state.md.us','Senator','','15','FIN',1,168,'2020-01-09 22:32:05','2020-01-11 10:55:20'),(169,'Benjamin F.','','Kramer','benjamin.kramer@senate.state.md.us','Senator','','19','FIN',1,169,'2020-01-09 22:32:05','2020-01-11 10:57:15'),(170,'Joanne C.','','Benson','joanne.benson@senate.state.md.us','Senator','','24','FIN',1,170,'2020-01-09 22:32:05','2020-01-11 10:55:02'),(171,'Pamela G.','','Beidle','pamela.beidle@senate.state.md.us','Senator','','32','FIN',1,171,'2020-01-09 22:32:05','2020-01-11 10:54:32'),(172,'Edward R.','','Reilly','edward.reilly@senate.state.md.us','Senator','','33','FIN',1,172,'2020-01-09 22:32:05','2020-01-11 10:58:23'),(173,'Stephen S.','','Hershey Jr.','steve.hershey@senate.state.md.us','Senator','','36','FIN',1,173,'2020-01-09 22:32:05','2020-01-11 10:56:07'),(174,'Antonio L.','','Hayes','antonio.hayes@senate.state.md.us','Senator','','40','FIN',1,174,'2020-01-09 22:32:05','2020-01-11 10:55:51'),(175,'Malcolm L.','','Augustine','malcolm.augustine@senate.state.md.us','Senator','','47','FIN',1,175,'2020-01-09 22:32:05','2020-01-11 10:54:01'),(176,'Michael J.','','Hough','michael.hough@senate.state.md.us','Senator','','4','JPR',1,176,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(177,'Justin D.','','Ready','justin.ready@senate.state.md.us','Senator','','4','JPR',1,177,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(178,'Katie Fry','','Hester','katie.hester@senate.state.md.us','Senator','','9','EHEA',1,178,'2020-01-09 22:32:05','2020-01-16 23:19:52'),(179,'Robert A. (Bobby)','','Zirkin','bobby.zirkin@senate.state.md.us','Senator','','11','JPR',1,179,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(180,'Susan C.','','Lee','susan.lee@senate.state.md.us','Senator','','16','JPR',1,180,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(181,'Jeffrey D.','','Waldstreicher','jeffrey.waldstreicher@senate.state.md.us','Senator','','18','JPR',1,181,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(182,'William C.','','Smith Jr.','will.smith@senate.state.md.us','Senator','','20','JPR',1,182,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(183,'Robert G.','','Cassilly','bob.cassilly@senate.state.md.us','Senator','','34','JPR',1,183,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(184,'Jill P.','','Carter','jill.carter@senate.state.md.us','Senator','','41','JPR',1,184,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(185,'Christopher R.','','West','chris.west@senate.state.md.us','Senator','','42','JPR',1,185,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(186,'Mary L.','','Washington','mary.washington@senate.state.md.us','Senator','','43','EHEA',1,186,'2020-01-09 22:32:05','2020-01-16 23:26:13'),(187,'Thomas V. (Mike)','','Miller Jr.','thomas.v.mike.miller@senate.state.md.us','Senator','','27','B&T',1,187,'2020-01-09 22:32:05','2020-01-16 23:07:03'),(188,'John','A.','Olszewski Jr.','johnnyo@baltimorecountymd.gov','County Executive','Baltimore County Executive','Baltimore County','',1,188,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(189,'Tom','','Quirk','council1@baltimorecountymd.gov','Councilman','Baltimore County Council','BCo Council District 1','',1,189,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(190,'Izzy','','Patoka','council2@baltimorecountymd.gov','Councilwoman','Baltimore County Council','BCo Council District 2','',1,190,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(191,'Wade','','Kach','council3@baltimorecountymd.gov','Councilman','Baltimore County Council','BCo Council District 3','',1,191,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(192,'Julian','E.','Jones Jr.','council4@baltimorecountymd.gov','Councilman','Baltimore County Council','BCo Council District 4','',1,192,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(193,'David','','Marks','council5@baltimorecountymd.gov','Councilman','Baltimore County Council','BCo Council District 5','',1,193,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(194,'Cathy','','Bevins','council6@baltimorecountymd.gov','Councilwoman','Baltimore County Council','BCo Council District 6','',1,194,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(195,'Todd','K.','Crandell','council7@baltimorecountymd.gov','Councilman','Baltimore County Council','BCo Council District 7','',1,195,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(196,'Zeke','','Cohen','zeke.cohen@baltimorecity.gov','Councilman','Baltimore City Council','City Council District 1','',1,196,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(197,'Edward','','Reisinger','edward.reisinger@baltimorecity.gov','Council Vice-President','Baltimore City Council','City Council District 10','',1,197,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(198,'Eric','T.','Costello','eric.costello@baltimorecity.gov','Councilman','Baltimore City Council','City Council District 11','',1,198,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(199,'Carl','','Stokes','carl.stokes@baltimorecity.gov','Councilman','Baltimore City Council','City Council District 12','',1,199,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(200,'Shannon','','Sneed','shannon.sneed@baltimorecity.gov','Councilman','Baltimore City Council','City Council District 13','',1,200,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(201,'Mary Pat','','Clarke','marypat.clarke@baltimorecity.gov','Councilwoman','Baltimore City Council','City Council District 14','',1,201,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(202,'Danielle','','McCray','danielle.mccray@baltimorecity.gov','Councilman','Baltimore City Council','City Council District 2','',1,202,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(203,'Ryan','','Dorsey','ryan.dorsey@baltimorecity.gov','Councilman','Baltimore City Council','City Council District 3','',1,203,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(204,'Bill','','Henry','bill.henry@baltimorecity.gov','Councilman','Baltimore City Council','City Council District 4','',1,204,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(205,'Isaac','Yitzy','Schleifer','isaac.schleifer@baltimorecity.gov','Councilwoman','Baltimore City Council','City Council District 5','',1,205,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(206,'Sharon','Green','Middleton','sharon.middleton@baltimorecity.gov','Councilwoman','Baltimore City Council','City Council District 6','',1,206,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(207,'Leon','F.','Pinkett III','leon.pinkett@baltimorecity.gov','Councilman','Baltimore City Council','City Council District 7','',1,207,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(208,'Kristerfer','','Burnett','kristerfer.burnett@baltimorecity.gov','Councilwoman','Baltimore City Council','City Council District 8','',1,208,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(209,'John','T.','Bullock','John.bullock@baltimorecity.gov','Councilman','Baltimore City Council','City Council District 9','',1,209,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(210,'Brandon','M.','Scott','CouncilPresident@baltimorecity.gov','Council President','Baltimore City Council','','',1,210,'2020-01-09 22:32:05','0000-00-00 00:00:00'),(211,'Michael','J','Griffith','michael.griffith@house.state.md.us','Delegate','','35B','JUD',1,999,'2020-01-09 22:29:10','2020-01-11 09:11:52'),(212,'Jared','','Solomon','jared.solomon@house.state.md.us','Delegate','','18','APP',1,999,'2020-01-17 00:04:13','2020-01-17 05:04:13');
/*!40000 ALTER TABLE `contacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `countries`
--

DROP TABLE IF EXISTS `countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `countries` (
  `country_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `isocode2` varchar(2) NOT NULL,
  `isocode3` varchar(3) NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '0',
  `display_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`country_id`)
) ENGINE=MyISAM AUTO_INCREMENT=226 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `countries`
--

LOCK TABLES `countries` WRITE;
/*!40000 ALTER TABLE `countries` DISABLE KEYS */;
INSERT INTO `countries` VALUES (225,'United States','US','USA',1,0);
/*!40000 ALTER TABLE `countries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `emails_template`
--

DROP TABLE IF EXISTS `emails_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `emails_template` (
  `email_template_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL DEFAULT '0',
  `email_template` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `display_order` int(11) NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`email_template_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `emails_template`
--

LOCK TABLES `emails_template` WRITE;
/*!40000 ALTER TABLE `emails_template` DISABLE KEYS */;
/*!40000 ALTER TABLE `emails_template` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_status`
--

DROP TABLE IF EXISTS `event_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_status` (
  `event_status_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `active` tinyint(11) NOT NULL DEFAULT '0',
  `display_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`event_status_id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_status`
--

LOCK TABLES `event_status` WRITE;
/*!40000 ALTER TABLE `event_status` DISABLE KEYS */;
INSERT INTO `event_status` VALUES (1,'Members',1,1),(2,'Public',1,2),(3,'Law Enforcement',1,3),(4,'CIO Course',1,4),(6,'Members and Guest',1,6),(8,'MITAGS',0,8),(9,'Open To General Public ',0,9),(10,'Range Maintenance ',1,10),(11,'Executive Committee Invitation ',1,11),(12,'Board of Trustees Invitation ',1,12),(13,'Youth',1,13),(14,'Women',1,14),(15,'All Clubs Open House',1,15),(16,'Private Event',1,16),(17,'AGC Sanctioned Activity',1,17),(18,'Holiday',1,18),(19,'Canceled ',1,19),(20,'CIO Refresher Course - Invitation Only ',0,20);
/*!40000 ALTER TABLE `event_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `facilities`
--

DROP TABLE IF EXISTS `facilities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `facilities` (
  `facility_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `available_lanes` int(11) NOT NULL DEFAULT '0',
  `active` tinyint(11) NOT NULL DEFAULT '0',
  `display_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`facility_id`)
) ENGINE=MyISAM AUTO_INCREMENT=40 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `facilities`
--

LOCK TABLES `facilities` WRITE;
/*!40000 ALTER TABLE `facilities` DISABLE KEYS */;
INSERT INTO `facilities` VALUES (2,'Pistol Range',58,1,9),(3,'Barnes Multi-Purpose Range',90,1,3),(4,'Barnes Range House Upper & Lower',0,0,0),(5,'Barnes Lower Class Room',0,1,2),(6,'Barnes Upper Meeting Room',0,1,5),(7,'High Power 200 Yard Range',10,0,0),(10,'Silhouette 200 Yard Range',10,0,0),(11,'Memorial Hall & Trap Room',0,0,0),(12,'Memorial Hall',0,0,0),(13,'Trap Room',0,1,24),(16,'Memorial Hall & Indoor Pellet Range',0,1,8),(17,'Trap 1',0,1,19),(19,'Trap 2',0,1,20),(20,'Trap 3',0,1,22),(21,'Trap 2 & 3',0,0,21),(23,'Trap 4 - Wobble Trap',0,1,23),(24,'Shotgun Patterning Range',0,1,11),(25,'Shooting Bay 7 & Archery Range',0,1,18),(26,'Closed',0,0,0),(27,'All Facilities & Ranges',0,0,0),(28,'Bench Rest',10,1,6),(29,'Canceled ',0,0,0),(30,'Pistol Range, Points 46-58',12,1,10),(31,'Barnes Office',0,1,4),(32,'200-yd Range',10,1,1),(33,'Campground',0,1,7),(34,'Shooting Bay 1',0,1,12),(35,'Shooting Bay 2',0,1,13),(36,'Shooting Bay 3',0,1,14),(37,'Shooting Bay 4',0,1,15),(38,'Shooting Bay 5',0,1,16),(39,'Shooting Bay 6',0,1,17);
/*!40000 ALTER TABLE `facilities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `groups` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `display_order` int(11) NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`group_id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groups`
--

LOCK TABLES `groups` WRITE;
/*!40000 ALTER TABLE `groups` DISABLE KEYS */;
INSERT INTO `groups` VALUES (1,'Senators',1,12,'2012-01-23 00:00:00','0000-00-00 00:00:00'),(2,'Delegates',1,7,'2012-01-23 00:00:00','0000-00-00 00:00:00'),(3,'Senate Educ Health & Env Affairs Comm',1,9,'2012-01-23 00:00:00','0000-00-00 00:00:00'),(4,'House Env & Transport Affairs Comm',1,3,'2012-01-23 00:00:00','0000-00-00 00:00:00'),(5,'Senate Judicial Proceedings Comm',1,11,'2012-01-23 00:00:00','0000-00-00 00:00:00'),(6,'House Judiciary Comm',1,5,'2012-01-23 00:00:00','0000-00-00 00:00:00'),(7,'Maryland General Assembly',1,13,'2012-01-27 00:00:00','0000-00-00 00:00:00'),(8,'House Ways & Means Comm',1,6,'2013-02-11 00:00:00','0000-00-00 00:00:00'),(10,'Pro-2nd Amendment Legislators',0,16,'2013-04-03 11:49:00','0000-00-00 00:00:00'),(9,'House Health & Gov Ops Comm',1,4,'2013-02-11 00:00:00','0000-00-00 00:00:00'),(11,'ANTI - 2nd Amendment Legislators',0,18,'2013-04-11 15:10:00','0000-00-00 00:00:00'),(12,'Senate Finance Comm',1,10,'2013-08-20 00:00:00','0000-00-00 00:00:00'),(13,'House Economic Matters Comm  ',1,2,'2013-08-21 00:00:00','0000-00-00 00:00:00'),(14,'Newspaper Editors',0,17,'2013-09-10 00:00:00','0000-00-00 00:00:00'),(15,'Television News Directors',0,19,'2013-09-10 00:00:00','0000-00-00 00:00:00'),(16,'Baltimore City Council',1,14,'2015-11-20 00:00:00','0000-00-00 00:00:00'),(17,'Baltimore County Council',1,15,'2015-11-20 00:00:00','0000-00-00 00:00:00'),(18,'House Appropriations Committee',1,1,'2016-02-11 00:00:00','0000-00-00 00:00:00'),(19,'Senate Budget and Taxation Committee',1,8,'2016-02-15 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `les_statistics`
--

DROP TABLE IF EXISTS `les_statistics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `les_statistics` (
  `les_statistics_id` int(11) NOT NULL AUTO_INCREMENT,
  `year` int(11) NOT NULL DEFAULT '0',
  `month` int(11) NOT NULL DEFAULT '0',
  `total_visits` int(11) NOT NULL DEFAULT '0',
  `total_unique_visits` int(11) NOT NULL DEFAULT '0',
  `total_sent_emails` int(11) NOT NULL DEFAULT '0',
  `emails_to_sender` int(11) NOT NULL DEFAULT '0',
  `emails_to_legislators` int(11) NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`les_statistics_id`)
) ENGINE=MyISAM AUTO_INCREMENT=70 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `les_statistics`
--

LOCK TABLES `les_statistics` WRITE;
/*!40000 ALTER TABLE `les_statistics` DISABLE KEYS */;
INSERT INTO `les_statistics` VALUES (1,2013,1,8499,3762,552465,3636,548829,'2013-02-21 00:00:00'),(2,2012,12,148,52,1663,14,1649,'2013-02-21 00:00:00'),(3,2012,11,101,47,0,0,0,'2013-02-21 00:00:00'),(4,2012,10,142,83,230,2,228,'2013-02-21 00:00:00'),(5,2012,9,116,62,0,0,0,'2013-02-21 00:00:00'),(7,2013,2,5724,2655,440243,3327,436916,'2013-03-01 07:28:57'),(8,2013,3,5669,2542,475997,3547,472450,'2013-04-01 03:00:03'),(9,2013,4,1817,1033,114601,968,113633,'2013-05-08 03:24:28'),(10,2013,5,206,85,1157,12,1145,'2013-06-01 03:00:03'),(11,2013,6,228,75,612,5,607,'2013-07-01 03:00:02'),(12,2013,7,258,57,944,6,938,'2013-08-01 03:00:02'),(13,2013,8,231,86,1053,11,1042,'2013-09-01 03:00:02'),(14,2013,9,848,484,40107,254,39853,'2013-10-01 03:00:02'),(15,2013,10,131,72,374,2,372,'2013-11-01 03:00:02'),(16,2013,11,99,56,507,6,501,'2013-12-01 03:00:03'),(17,2013,12,140,69,1202,10,1192,'2014-01-01 03:00:02'),(18,2014,1,194,99,4363,50,4313,'2014-02-01 03:00:02'),(19,2014,2,298,157,21969,174,21795,'2014-03-01 03:00:02'),(20,2014,3,340,145,9577,113,9464,'2014-04-01 03:00:03'),(21,2014,4,158,71,1142,11,1131,'2014-05-01 03:00:03'),(22,2014,5,126,70,0,0,0,'2014-06-01 03:00:02'),(23,2014,6,160,94,195,3,192,'2014-07-01 03:00:02'),(24,2014,7,196,96,1334,12,1322,'2014-08-01 03:00:03'),(25,2014,8,182,74,1316,7,1309,'2014-09-01 03:00:03'),(26,2014,9,152,86,376,2,374,'2014-10-01 03:00:03'),(27,2014,10,222,90,0,0,0,'2014-11-01 03:00:02'),(28,2014,11,173,67,376,2,374,'2014-12-01 03:00:02'),(29,2014,12,120,62,190,2,188,'2015-01-01 03:00:02'),(30,2015,1,211,77,784,6,778,'2015-02-01 03:00:02'),(31,2015,2,226,90,2732,40,2692,'2015-03-01 03:00:02'),(32,2015,3,309,157,4091,43,4048,'2015-04-01 03:00:02'),(33,2015,4,203,101,843,9,834,'2015-05-01 03:00:03'),(34,2015,5,193,90,189,1,188,'2015-06-01 03:00:12'),(35,2015,6,149,70,189,1,188,'2015-07-01 03:00:07'),(36,2015,7,153,76,0,0,0,'2015-08-01 03:00:03'),(37,2015,8,219,89,378,2,376,'2015-09-01 03:00:03'),(38,2015,9,157,56,0,0,0,'2015-10-01 03:00:03'),(39,2015,10,115,66,189,1,188,'2015-11-01 03:00:02'),(40,2015,11,162,57,567,3,564,'2015-12-01 03:00:03'),(41,2015,12,262,81,1480,11,1469,'2016-01-01 03:00:04'),(42,2016,1,277,122,1578,25,1553,'2016-02-01 03:00:03'),(43,2016,2,406,182,4961,100,4861,'2016-03-01 03:00:03'),(44,2016,3,1479,847,25223,433,24790,'2016-04-01 03:00:03'),(45,2016,4,285,163,1786,31,1755,'2016-05-01 03:00:03'),(46,2016,5,204,100,0,0,0,'2016-06-01 03:00:05'),(47,2016,6,263,130,281,31,250,'2016-07-01 03:00:03'),(48,2017,3,758,40,0,0,0,'2017-04-05 18:07:21'),(49,2017,4,1160,75,68,34,34,'2017-05-01 02:00:02'),(50,2017,5,323,87,0,0,0,'2017-06-01 02:00:02'),(51,2017,6,440,95,0,0,0,'2017-07-01 02:00:02'),(52,2017,7,308,80,12,6,6,'2017-08-01 02:00:03'),(53,2017,8,227,85,0,0,0,'2017-09-01 02:00:04'),(54,2017,9,240,118,0,0,0,'2017-10-01 02:00:02'),(55,2017,10,235,117,0,0,0,'2017-11-01 02:00:04'),(56,2017,11,226,98,0,0,0,'2017-12-01 02:00:05'),(57,2017,12,305,118,0,0,0,'2018-01-01 02:00:04'),(58,2018,1,258,117,0,0,0,'2018-02-01 02:00:04'),(59,2018,2,597,237,3284,93,3191,'2018-03-01 02:00:05'),(60,2018,3,752,261,7466,92,7374,'2018-04-01 02:00:04'),(61,2018,4,396,133,1229,7,1222,'2018-05-01 02:00:02'),(62,2018,5,95,46,0,0,0,'2018-06-01 02:00:04'),(63,2018,6,79,45,0,0,0,'2018-07-01 02:00:03'),(64,2018,7,38,22,0,0,0,'2018-08-01 02:00:03'),(65,2018,8,160,52,0,0,0,'2018-09-01 02:00:04'),(66,2018,9,74,36,0,0,0,'2018-10-01 02:00:04'),(67,2018,10,66,42,0,0,0,'2018-11-01 02:00:05'),(68,2018,11,123,34,0,0,0,'2018-12-03 10:22:47'),(69,2018,12,377,60,1,1,0,'2019-01-31 21:00:01');
/*!40000 ALTER TABLE `les_statistics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `les_statistics_per_group`
--

DROP TABLE IF EXISTS `les_statistics_per_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `les_statistics_per_group` (
  `les_statistics_per_group_id` int(11) NOT NULL AUTO_INCREMENT,
  `les_statistics_id` int(11) NOT NULL DEFAULT '0',
  `email_receivers_group` int(11) NOT NULL DEFAULT '0',
  `visits` int(11) NOT NULL DEFAULT '0',
  `unique_visits` int(11) NOT NULL DEFAULT '0',
  `sent_emails` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`les_statistics_per_group_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1084 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `les_statistics_per_group`
--

LOCK TABLES `les_statistics_per_group` WRITE;
/*!40000 ALTER TABLE `les_statistics_per_group` DISABLE KEYS */;
INSERT INTO `les_statistics_per_group` VALUES (1,1,7,6041,3326,508421),(2,1,1,933,568,12704),(3,1,2,548,337,26816),(4,1,3,60,31,120),(5,1,4,48,23,152),(6,1,5,417,254,1866),(7,1,6,452,311,2386),(8,1,8,0,0,0),(9,1,9,0,0,0),(10,2,1,46,30,143),(11,2,2,25,18,567),(12,2,3,9,7,0),(13,2,4,10,8,0),(14,2,5,14,12,11),(15,2,6,10,7,0),(16,2,7,34,22,924),(17,2,8,0,0,0),(18,2,9,0,0,0),(19,3,1,22,12,0),(20,3,2,17,12,0),(21,3,3,17,9,0),(22,3,4,8,7,0),(23,3,5,6,5,0),(24,3,6,10,9,0),(25,3,7,21,11,0),(26,3,8,0,0,0),(27,3,9,0,0,0),(28,4,1,49,34,42),(29,4,2,18,14,0),(30,4,3,10,9,0),(31,4,4,31,26,0),(32,4,5,9,8,0),(33,4,6,8,7,0),(34,4,7,17,10,188),(35,4,8,0,0,0),(36,4,9,0,0,0),(37,5,1,22,11,0),(38,5,2,19,19,0),(39,5,3,18,17,0),(40,5,4,26,24,0),(41,5,5,9,8,0),(42,5,6,8,6,0),(43,5,7,14,11,0),(44,5,8,0,0,0),(45,5,9,0,0,0),(55,7,1,751,447,15179),(56,7,2,306,204,18854),(57,7,3,57,36,72),(58,7,4,57,34,150),(59,7,5,665,403,3784),(60,7,6,342,212,3389),(61,7,7,3409,2110,397991),(62,7,8,58,37,184),(63,7,9,79,53,640),(64,8,1,309,173,5337),(65,8,2,661,408,48538),(66,8,3,61,39,147),(67,8,4,63,40,575),(68,8,5,143,89,566),(69,8,6,682,385,9594),(70,8,7,3307,1982,404913),(71,8,8,178,139,2410),(72,8,9,265,175,3917),(73,9,1,188,119,3180),(74,9,2,745,532,11821),(75,9,3,20,13,36),(76,9,4,15,14,25),(77,9,5,51,37,158),(78,9,6,38,25,132),(79,9,7,610,433,74090),(80,9,8,12,11,24),(81,9,10,116,91,25083),(82,9,9,22,20,52),(83,9,11,0,0,0),(84,10,1,42,21,48),(85,10,2,16,12,2),(86,10,3,6,5,0),(87,10,4,5,4,0),(88,10,5,11,10,0),(89,10,6,17,12,0),(90,10,7,36,27,569),(91,10,8,9,7,0),(92,10,10,31,22,320),(93,10,9,10,8,0),(94,10,11,23,11,218),(95,11,1,47,25,96),(96,11,2,20,17,140),(97,11,3,14,11,0),(98,11,4,15,11,0),(99,11,5,15,13,0),(100,11,6,14,12,0),(101,11,7,30,23,376),(102,11,8,12,9,0),(103,11,10,27,16,0),(104,11,9,16,10,0),(105,11,11,18,15,0),(106,12,1,0,0,0),(107,12,2,0,0,4),(108,12,3,0,0,0),(109,12,4,0,0,0),(110,12,5,0,0,0),(111,12,6,0,0,0),(112,12,7,0,0,940),(113,12,8,0,0,0),(114,12,10,0,0,0),(115,12,9,0,0,0),(116,12,11,0,0,0),(117,13,1,33,26,0),(118,13,2,20,17,6),(119,13,3,9,9,0),(120,13,4,11,9,0),(121,13,5,22,14,0),(122,13,6,13,11,24),(123,13,7,42,26,752),(124,13,8,9,7,0),(125,13,10,20,16,160),(126,13,9,13,10,0),(127,13,11,14,13,109),(128,13,12,15,5,2),(129,13,13,10,3,0),(130,14,1,55,41,160),(131,14,2,35,26,167),(132,14,3,10,8,0),(133,14,4,9,7,0),(134,14,5,19,14,0),(135,14,6,16,11,0),(136,14,7,515,377,39051),(137,14,8,10,7,0),(138,14,10,44,35,400),(139,14,9,14,12,0),(140,14,11,20,14,109),(141,14,12,33,15,110),(142,14,13,57,18,110),(143,14,14,6,3,0),(144,14,15,5,3,0),(145,15,1,29,23,0),(146,15,2,7,6,0),(147,15,3,7,6,0),(148,15,4,6,5,0),(149,15,5,4,3,0),(150,15,6,8,7,0),(151,15,7,31,21,374),(152,15,8,8,6,0),(153,15,10,11,9,0),(154,15,9,8,6,0),(155,15,11,11,9,0),(156,15,12,0,0,0),(157,15,13,1,1,0),(158,15,14,0,0,0),(159,15,15,0,0,0),(160,16,1,16,13,3),(161,16,2,9,7,21),(162,16,3,4,3,0),(163,16,4,7,6,0),(164,16,5,9,8,0),(165,16,6,6,5,0),(166,16,7,17,13,374),(167,16,8,7,6,0),(168,16,10,10,8,0),(169,16,9,8,7,0),(170,16,11,6,5,109),(171,16,12,0,0,0),(172,16,13,0,0,0),(173,16,14,0,0,0),(174,16,15,0,0,0),(175,17,1,13,11,0),(176,17,2,15,13,4),(177,17,3,7,6,0),(178,17,4,6,5,0),(179,17,5,16,13,0),(180,17,6,13,12,72),(181,17,7,23,14,1126),(182,17,8,11,11,0),(183,17,10,18,16,0),(184,17,9,11,10,0),(185,17,11,7,6,0),(186,17,12,0,0,0),(187,17,13,0,0,0),(188,17,14,0,0,0),(189,17,15,0,0,0),(190,18,1,25,18,54),(191,18,2,26,18,452),(192,18,3,5,5,12),(193,18,4,4,3,0),(194,18,5,12,10,12),(195,18,6,22,18,231),(196,18,7,42,31,3200),(197,18,8,9,8,0),(198,18,10,26,18,160),(199,18,9,9,7,24),(200,18,11,14,11,218),(201,18,12,0,0,0),(202,18,13,0,0,0),(203,18,14,0,0,0),(204,18,15,0,0,0),(205,19,1,24,22,578),(206,19,2,18,16,566),(207,19,3,5,5,0),(208,19,4,10,10,25),(209,19,5,23,17,240),(210,19,6,18,8,253),(211,19,7,92,59,19552),(212,19,8,41,31,240),(213,19,10,41,25,244),(214,19,9,9,8,24),(215,19,11,17,15,247),(216,19,12,0,0,0),(217,19,13,0,0,0),(218,19,14,0,0,0),(219,19,15,0,0,0),(220,20,1,38,25,576),(221,20,2,35,22,2112),(222,20,3,11,10,12),(223,20,4,13,9,0),(224,20,5,20,13,24),(225,20,6,49,22,811),(226,20,7,106,80,5685),(227,20,8,21,15,96),(228,20,10,22,19,80),(229,20,9,13,11,72),(230,20,11,12,9,109),(231,20,12,0,0,0),(232,20,13,0,0,0),(233,20,14,0,0,0),(234,20,15,0,0,0),(235,21,1,23,21,4),(236,21,2,12,11,10),(237,21,3,10,9,0),(238,21,4,9,8,0),(239,21,5,7,6,0),(240,21,6,13,13,0),(241,21,7,45,30,1128),(242,21,8,11,10,0),(243,21,10,9,9,0),(244,21,9,9,8,0),(245,21,11,10,9,0),(246,21,12,0,0,0),(247,21,13,0,0,0),(248,21,14,0,0,0),(249,21,15,0,0,0),(250,22,1,19,16,0),(251,22,2,6,5,0),(252,22,3,6,5,0),(253,22,4,13,11,0),(254,22,5,9,8,0),(255,22,6,7,6,0),(256,22,7,29,28,0),(257,22,8,8,7,0),(258,22,10,9,8,0),(259,22,9,10,9,0),(260,22,11,10,8,0),(261,22,12,0,0,0),(262,22,13,0,0,0),(263,22,14,0,0,0),(264,22,15,0,0,0),(265,23,1,22,19,0),(266,23,2,12,10,7),(267,23,3,13,11,0),(268,23,4,12,12,0),(269,23,5,12,9,0),(270,23,6,18,15,0),(271,23,7,33,28,188),(272,23,8,8,7,0),(273,23,10,9,7,0),(274,23,9,12,11,0),(275,23,11,9,8,0),(276,23,12,0,0,0),(277,23,13,0,0,0),(278,23,14,0,0,0),(279,23,15,0,0,0),(280,24,1,20,17,2),(281,24,2,22,17,16),(282,24,3,8,7,0),(283,24,4,11,8,0),(284,24,5,11,9,0),(285,24,6,11,9,0),(286,24,7,43,28,1316),(287,24,8,17,16,0),(288,24,10,19,18,0),(289,24,9,17,15,0),(290,24,11,17,16,0),(291,24,12,0,0,0),(292,24,13,0,0,0),(293,24,14,0,0,0),(294,24,15,0,0,0),(295,25,1,18,16,0),(296,25,2,18,12,0),(297,25,3,11,10,0),(298,25,4,12,10,0),(299,25,5,15,10,0),(300,25,6,9,7,0),(301,25,7,39,28,1316),(302,25,8,15,11,0),(303,25,10,16,13,0),(304,25,9,16,12,0),(305,25,11,13,10,0),(306,25,12,0,0,0),(307,25,13,0,0,0),(308,25,14,0,0,0),(309,25,15,0,0,0),(310,26,1,22,20,0),(311,26,2,16,12,0),(312,26,3,8,7,0),(313,26,4,11,9,0),(314,26,5,8,7,0),(315,26,6,9,8,0),(316,26,7,35,28,376),(317,26,8,9,7,0),(318,26,10,9,8,0),(319,26,9,11,10,0),(320,26,11,14,10,0),(321,26,12,0,0,0),(322,26,13,0,0,0),(323,26,14,0,0,0),(324,26,15,0,0,0),(325,27,1,20,18,0),(326,27,2,21,17,0),(327,27,3,15,12,0),(328,27,4,19,15,0),(329,27,5,16,14,0),(330,27,6,19,17,0),(331,27,7,41,34,0),(332,27,8,21,19,0),(333,27,10,12,11,0),(334,27,9,17,15,0),(335,27,11,21,14,0),(336,27,12,0,0,0),(337,27,13,0,0,0),(338,27,14,0,0,0),(339,27,15,0,0,0),(340,28,1,16,14,0),(341,28,2,18,15,0),(342,28,3,11,9,0),(343,28,4,10,9,0),(344,28,5,15,13,0),(345,28,6,9,8,0),(346,28,7,47,24,376),(347,28,8,12,11,0),(348,28,10,14,10,0),(349,28,9,12,11,0),(350,28,11,9,8,0),(351,28,12,0,0,0),(352,28,13,0,0,0),(353,28,14,0,0,0),(354,28,15,0,0,0),(355,29,1,15,14,0),(356,29,2,18,14,0),(357,29,3,7,7,0),(358,29,4,5,5,0),(359,29,5,9,9,0),(360,29,6,9,8,0),(361,29,7,29,19,190),(362,29,8,7,7,0),(363,29,10,8,6,0),(364,29,9,5,5,0),(365,29,11,8,6,0),(366,29,12,0,0,0),(367,29,13,0,0,0),(368,29,14,0,0,0),(369,29,15,0,0,0),(370,30,1,23,21,9),(371,30,2,20,14,23),(372,30,3,12,9,0),(373,30,4,29,11,0),(374,30,5,22,15,0),(375,30,6,15,14,0),(376,30,7,37,27,752),(377,30,8,17,11,0),(378,30,10,11,9,0),(379,30,9,13,11,0),(380,30,11,12,8,0),(381,30,12,0,0,0),(382,30,13,0,0,0),(383,30,14,0,0,0),(384,30,15,0,0,0),(385,31,1,27,17,51),(386,31,2,22,13,700),(387,31,3,10,7,0),(388,31,4,9,7,23),(389,31,5,21,14,108),(390,31,6,30,19,299),(391,31,7,59,38,1504),(392,31,8,15,10,23),(393,31,10,7,6,0),(394,31,9,16,9,24),(395,31,11,10,7,0),(396,31,12,0,0,0),(397,31,13,0,0,0),(398,31,14,0,0,0),(399,31,15,0,0,0),(400,32,1,37,24,49),(401,32,2,33,25,142),(402,32,3,9,8,0),(403,32,4,10,10,0),(404,32,5,39,22,120),(405,32,6,29,24,208),(406,32,7,108,70,3572),(407,32,8,13,12,0),(408,32,10,11,9,0),(409,32,9,14,9,0),(410,32,11,6,5,0),(411,32,12,0,0,0),(412,32,13,0,0,0),(413,32,14,0,0,0),(414,32,15,0,0,0),(415,33,1,36,25,97),(416,33,2,17,13,173),(417,33,3,10,6,0),(418,33,4,12,9,0),(419,33,5,11,9,0),(420,33,6,14,10,0),(421,33,7,69,49,573),(422,33,8,9,6,0),(423,33,10,8,6,0),(424,33,9,13,10,0),(425,33,11,4,4,0),(426,33,12,0,0,0),(427,33,13,0,0,0),(428,33,14,0,0,0),(429,33,15,0,0,0),(430,34,1,35,28,0),(431,34,2,17,14,0),(432,34,3,11,9,0),(433,34,4,13,10,0),(434,34,5,15,13,0),(435,34,6,14,13,0),(436,34,7,47,35,189),(437,34,8,12,11,0),(438,34,10,11,10,0),(439,34,9,12,11,0),(440,34,11,6,6,0),(441,34,12,0,0,0),(442,34,13,0,0,0),(443,34,14,0,0,0),(444,34,15,0,0,0),(445,35,1,48,25,0),(446,35,2,11,8,0),(447,35,3,6,6,0),(448,35,4,6,5,0),(449,35,5,8,5,0),(450,35,6,12,8,0),(451,35,7,33,29,189),(452,35,8,9,7,0),(453,35,10,9,5,0),(454,35,9,6,6,0),(455,35,11,1,1,0),(456,35,12,0,0,0),(457,35,13,0,0,0),(458,35,14,0,0,0),(459,35,15,0,0,0),(460,36,1,32,21,0),(461,36,2,8,8,0),(462,36,3,9,9,0),(463,36,4,11,11,0),(464,36,5,13,11,0),(465,36,6,19,17,0),(466,36,7,30,27,0),(467,36,8,8,8,0),(468,36,10,13,12,0),(469,36,9,8,8,0),(470,36,11,2,2,0),(471,36,12,0,0,0),(472,36,13,0,0,0),(473,36,14,0,0,0),(474,36,15,0,0,0),(475,37,1,36,23,0),(476,37,2,19,16,0),(477,37,3,16,14,0),(478,37,4,15,13,0),(479,37,5,18,16,0),(480,37,6,24,19,0),(481,37,7,42,34,378),(482,37,8,22,19,0),(483,37,10,7,7,0),(484,37,9,16,13,0),(485,37,11,4,4,0),(486,37,12,0,0,0),(487,37,13,0,0,0),(488,37,14,0,0,0),(489,37,15,0,0,0),(490,38,1,33,22,0),(491,38,2,11,10,0),(492,38,3,17,13,0),(493,38,4,12,11,0),(494,38,5,9,9,0),(495,38,6,13,11,0),(496,38,7,26,22,0),(497,38,8,14,12,0),(498,38,10,4,4,0),(499,38,9,13,12,0),(500,38,11,5,3,0),(501,38,12,0,0,0),(502,38,13,0,0,0),(503,38,14,0,0,0),(504,38,15,0,0,0),(505,39,1,31,20,0),(506,39,2,8,8,0),(507,39,3,7,7,0),(508,39,4,4,4,0),(509,39,5,6,6,0),(510,39,6,14,12,0),(511,39,7,25,23,189),(512,39,8,4,4,0),(513,39,10,6,6,0),(514,39,9,7,7,0),(515,39,11,3,3,0),(516,39,12,0,0,0),(517,39,13,0,0,0),(518,39,14,0,0,0),(519,39,15,0,0,0),(520,40,1,34,22,0),(521,40,2,9,8,0),(522,40,3,9,9,0),(523,40,4,10,10,0),(524,40,5,12,10,0),(525,40,6,13,11,0),(526,40,7,38,27,567),(527,40,8,12,11,0),(528,40,10,6,5,0),(529,40,9,7,6,0),(530,40,11,4,4,0),(531,40,12,0,0,0),(532,40,13,0,0,0),(533,40,14,0,0,0),(534,40,15,0,0,0),(535,40,16,5,3,0),(536,40,17,3,3,0),(537,41,1,33,15,0),(538,41,2,19,16,140),(539,41,3,13,10,0),(540,41,4,16,13,0),(541,41,5,19,10,17),(542,41,6,20,16,0),(543,41,7,83,38,1323),(544,41,8,16,12,0),(545,41,10,5,4,0),(546,41,9,13,10,0),(547,41,11,4,3,0),(548,41,12,0,0,0),(549,41,13,0,0,0),(550,41,14,0,0,0),(551,41,15,0,0,0),(552,41,16,12,4,0),(553,41,17,9,3,0),(554,42,1,32,22,51),(555,42,2,18,17,148),(556,42,3,11,9,0),(557,42,4,16,14,0),(558,42,5,23,16,84),(559,42,6,33,22,161),(560,42,7,57,39,1134),(561,42,8,12,11,0),(562,42,10,6,6,0),(563,42,9,16,15,0),(564,42,11,9,8,0),(565,42,12,0,0,0),(566,42,13,0,0,0),(567,42,14,0,0,0),(568,42,15,0,0,0),(569,42,16,17,15,0),(570,42,17,27,16,0),(571,43,1,37,26,309),(572,43,2,26,23,569),(573,43,3,20,14,12),(574,43,4,16,14,50),(575,43,5,39,27,290),(576,43,6,74,50,784),(577,43,7,88,64,2822),(578,43,8,20,12,100),(579,43,10,6,6,0),(580,43,9,13,10,25),(581,43,11,5,5,0),(582,43,12,0,0,0),(583,43,13,0,0,0),(584,43,14,0,0,0),(585,43,15,0,0,0),(586,43,16,7,6,0),(587,43,17,9,9,0),(588,43,18,16,9,0),(589,43,19,30,8,0),(590,44,1,60,41,494),(591,44,2,556,377,17745),(592,44,3,6,6,0),(593,44,4,11,11,0),(594,44,5,441,315,1899),(595,44,6,196,148,1305),(596,44,7,157,103,3780),(597,44,8,8,8,0),(598,44,10,6,6,0),(599,44,9,6,6,0),(600,44,11,2,2,0),(601,44,12,0,0,0),(602,44,13,0,0,0),(603,44,14,0,0,0),(604,44,15,0,0,0),(605,44,16,9,9,0),(606,44,17,4,4,0),(607,44,18,8,8,0),(608,44,19,9,9,0),(609,45,1,42,23,537),(610,45,2,32,26,612),(611,45,3,8,8,0),(612,45,4,9,9,0),(613,45,5,17,17,47),(614,45,6,35,26,23),(615,45,7,53,48,567),(616,45,8,10,10,0),(617,45,10,11,11,0),(618,45,9,9,9,0),(619,45,11,1,1,0),(620,45,12,0,0,0),(621,45,13,0,0,0),(622,45,14,0,0,0),(623,45,15,0,0,0),(624,45,16,10,10,0),(625,45,17,17,14,0),(626,45,18,18,18,0),(627,45,19,13,12,0),(628,46,1,20,11,0),(629,46,2,10,9,0),(630,46,3,11,9,0),(631,46,4,9,8,0),(632,46,5,10,9,0),(633,46,6,32,25,0),(634,46,7,41,34,0),(635,46,8,6,5,0),(636,46,10,6,5,0),(637,46,9,9,8,0),(638,46,11,5,4,0),(639,46,12,0,0,0),(640,46,13,0,0,0),(641,46,14,0,0,0),(642,46,15,0,0,0),(643,46,16,9,9,0),(644,46,17,24,10,0),(645,46,18,5,5,0),(646,46,19,7,7,0),(647,47,1,12,9,0),(648,47,2,9,7,0),(649,47,3,7,4,0),(650,47,4,3,3,0),(651,47,5,14,10,12),(652,47,6,21,13,0),(653,47,7,30,25,0),(654,47,8,8,6,0),(655,47,10,6,3,0),(656,47,9,3,3,0),(657,47,11,4,1,0),(658,47,12,0,0,0),(659,47,13,0,0,0),(660,47,14,0,0,0),(661,47,15,0,0,0),(662,47,16,13,9,0),(663,47,17,118,68,269),(664,47,18,9,6,0),(665,47,19,6,4,0),(666,48,1,2,2,0),(667,48,2,5,5,0),(668,48,3,1,1,0),(669,48,4,2,2,0),(670,48,5,3,3,0),(671,48,6,4,4,0),(672,48,7,719,17,0),(673,48,8,5,5,0),(674,48,10,0,0,0),(675,48,9,4,4,0),(676,48,11,0,0,0),(677,48,12,0,0,0),(678,48,13,0,0,0),(679,48,14,0,0,0),(680,48,15,0,0,0),(681,48,16,1,1,0),(682,48,17,3,3,0),(683,48,18,5,5,0),(684,48,19,4,3,0),(685,49,1,188,16,8),(686,49,2,63,18,6),(687,49,3,34,10,4),(688,49,4,36,13,6),(689,49,5,46,13,4),(690,49,6,47,13,6),(691,49,7,462,20,14),(692,49,8,42,14,10),(693,49,10,10,3,0),(694,49,9,32,12,4),(695,49,11,12,3,0),(696,49,12,6,2,0),(697,49,13,12,3,0),(698,49,14,10,2,0),(699,49,15,11,3,0),(700,49,16,32,10,2),(701,49,17,41,14,4),(702,49,18,42,14,0),(703,49,19,34,15,0),(704,50,1,21,15,0),(705,50,2,19,15,0),(706,50,3,18,16,0),(707,50,4,20,19,0),(708,50,5,18,13,0),(709,50,6,24,19,0),(710,50,7,76,24,0),(711,50,8,18,15,0),(712,50,10,0,0,0),(713,50,9,24,17,0),(714,50,11,0,0,0),(715,50,12,0,0,0),(716,50,13,0,0,0),(717,50,14,0,0,0),(718,50,15,0,0,0),(719,50,16,20,16,0),(720,50,17,26,17,0),(721,50,18,23,17,0),(722,50,19,16,15,0),(723,51,1,32,15,0),(724,51,2,36,16,0),(725,51,3,24,13,0),(726,51,4,29,18,0),(727,51,5,38,18,0),(728,51,6,38,23,0),(729,51,7,45,26,0),(730,51,8,35,18,0),(731,51,10,0,0,0),(732,51,9,32,18,0),(733,51,11,0,0,0),(734,51,12,0,0,0),(735,51,13,0,0,0),(736,51,14,0,0,0),(737,51,15,0,0,0),(738,51,16,29,15,0),(739,51,17,34,19,0),(740,51,18,34,20,0),(741,51,19,34,19,0),(742,52,1,19,13,0),(743,52,2,28,15,12),(744,52,3,17,11,0),(745,52,4,26,16,0),(746,52,5,22,14,0),(747,52,6,22,13,0),(748,52,7,36,24,0),(749,52,8,24,18,0),(750,52,10,0,0,0),(751,52,9,19,13,0),(752,52,11,0,0,0),(753,52,12,0,0,0),(754,52,13,0,0,0),(755,52,14,0,0,0),(756,52,15,0,0,0),(757,52,16,21,13,0),(758,52,17,24,15,0),(759,52,18,29,18,0),(760,52,19,21,15,0),(761,53,1,15,15,0),(762,53,2,19,14,0),(763,53,3,9,9,0),(764,53,4,26,20,0),(765,53,5,14,12,0),(766,53,6,18,11,0),(767,53,7,25,14,0),(768,53,8,13,13,0),(769,53,10,0,0,0),(770,53,9,14,14,0),(771,53,11,0,0,0),(772,53,12,0,0,0),(773,53,13,0,0,0),(774,53,14,0,0,0),(775,53,15,0,0,0),(776,53,16,18,12,0),(777,53,17,24,15,0),(778,53,18,21,14,0),(779,53,19,11,11,0),(780,54,1,20,18,0),(781,54,2,23,21,0),(782,54,3,11,10,0),(783,54,4,18,15,0),(784,54,5,18,15,0),(785,54,6,13,12,0),(786,54,7,24,20,0),(787,54,8,18,14,0),(788,54,10,0,0,0),(789,54,9,22,16,0),(790,54,11,0,0,0),(791,54,12,0,0,0),(792,54,13,0,0,0),(793,54,14,0,0,0),(794,54,15,0,0,0),(795,54,16,20,15,0),(796,54,17,18,17,0),(797,54,18,19,17,0),(798,54,19,16,15,0),(799,55,1,19,17,0),(800,55,2,23,21,0),(801,55,3,12,11,0),(802,55,4,28,14,0),(803,55,5,17,15,0),(804,55,6,18,15,0),(805,55,7,23,19,0),(806,55,8,16,13,0),(807,55,10,0,0,0),(808,55,9,17,16,0),(809,55,11,0,0,0),(810,55,12,0,0,0),(811,55,13,0,0,0),(812,55,14,0,0,0),(813,55,15,0,0,0),(814,55,16,14,13,0),(815,55,17,17,16,0),(816,55,18,19,15,0),(817,55,19,12,11,0),(818,56,1,15,14,0),(819,56,2,17,16,0),(820,56,3,15,12,0),(821,56,4,26,12,0),(822,56,5,16,13,0),(823,56,6,14,13,0),(824,56,7,17,15,0),(825,56,8,15,12,0),(826,56,10,0,0,0),(827,56,9,14,11,0),(828,56,11,0,0,0),(829,56,12,0,0,0),(830,56,13,0,0,0),(831,56,14,0,0,0),(832,56,15,0,0,0),(833,56,16,11,10,0),(834,56,17,31,24,0),(835,56,18,21,16,0),(836,56,19,14,10,0),(837,57,1,20,16,0),(838,57,2,28,19,0),(839,57,3,18,13,0),(840,57,4,33,18,0),(841,57,5,19,14,0),(842,57,6,21,16,0),(843,57,7,24,16,0),(844,57,8,21,16,0),(845,57,10,0,0,0),(846,57,9,21,17,0),(847,57,11,0,0,0),(848,57,12,0,0,0),(849,57,13,0,0,0),(850,57,14,0,0,0),(851,57,15,0,0,0),(852,57,16,29,18,0),(853,57,17,31,22,0),(854,57,18,24,18,0),(855,57,19,16,13,0),(856,58,1,21,18,0),(857,58,2,25,19,0),(858,58,3,19,15,0),(859,58,4,17,14,0),(860,58,5,23,19,0),(861,58,6,23,17,0),(862,58,7,18,14,0),(863,58,8,19,14,0),(864,58,10,0,0,0),(865,58,9,15,13,0),(866,58,11,0,0,0),(867,58,12,0,0,0),(868,58,13,0,0,0),(869,58,14,0,0,0),(870,58,15,0,0,0),(871,58,16,17,12,0),(872,58,17,24,19,0),(873,58,18,18,13,0),(874,58,19,19,14,0),(875,59,1,28,24,192),(876,59,2,39,32,426),(877,59,3,21,16,0),(878,59,4,22,16,0),(879,59,5,132,86,487),(880,59,6,158,108,661),(881,59,7,57,41,1516),(882,59,8,25,19,0),(883,59,10,0,0,0),(884,59,9,24,21,0),(885,59,11,0,0,0),(886,59,12,0,0,0),(887,59,13,0,0,0),(888,59,14,0,0,0),(889,59,15,0,0,0),(890,59,16,21,14,0),(891,59,17,29,24,2),(892,59,18,20,16,0),(893,59,19,21,18,0),(894,60,1,62,53,192),(895,60,2,104,81,2293),(896,60,3,38,34,0),(897,60,4,46,40,0),(898,60,5,66,55,84),(899,60,6,80,66,706),(900,60,7,124,86,4164),(901,60,8,37,32,27),(902,60,10,0,0,0),(903,60,9,40,38,0),(904,60,11,0,0,0),(905,60,12,0,0,0),(906,60,13,0,0,0),(907,60,14,0,0,0),(908,60,15,0,0,0),(909,60,16,34,29,0),(910,60,17,46,42,0),(911,60,18,40,36,0),(912,60,19,35,32,0),(913,61,1,30,29,0),(914,61,2,33,30,284),(915,61,3,26,21,0),(916,61,4,28,28,0),(917,61,5,29,29,0),(918,61,6,23,21,0),(919,61,7,51,34,945),(920,61,8,22,22,0),(921,61,10,0,0,0),(922,61,9,34,31,0),(923,61,11,0,0,0),(924,61,12,0,0,0),(925,61,13,0,0,0),(926,61,14,0,0,0),(927,61,15,0,0,0),(928,61,16,23,19,0),(929,61,17,41,34,0),(930,61,18,32,28,0),(931,61,19,24,24,0),(932,62,1,8,7,0),(933,62,2,8,6,0),(934,62,3,3,3,0),(935,62,4,9,8,0),(936,62,5,9,8,0),(937,62,6,4,4,0),(938,62,7,5,5,0),(939,62,8,6,4,0),(940,62,10,0,0,0),(941,62,9,11,10,0),(942,62,11,0,0,0),(943,62,12,0,0,0),(944,62,13,0,0,0),(945,62,14,0,0,0),(946,62,15,0,0,0),(947,62,16,5,5,0),(948,62,17,14,10,0),(949,62,18,10,9,0),(950,62,19,3,2,0),(951,63,1,8,8,0),(952,63,2,10,10,0),(953,63,3,0,0,0),(954,63,4,7,6,0),(955,63,5,7,7,0),(956,63,6,3,3,0),(957,63,7,9,4,0),(958,63,8,4,4,0),(959,63,10,0,0,0),(960,63,9,6,6,0),(961,63,11,0,0,0),(962,63,12,0,0,0),(963,63,13,0,0,0),(964,63,14,0,0,0),(965,63,15,0,0,0),(966,63,16,4,3,0),(967,63,17,12,11,0),(968,63,18,7,7,0),(969,63,19,2,2,0),(970,64,1,3,2,0),(971,64,2,3,3,0),(972,64,3,0,0,0),(973,64,4,1,1,0),(974,64,5,3,3,0),(975,64,6,2,2,0),(976,64,7,5,5,0),(977,64,8,5,5,0),(978,64,10,0,0,0),(979,64,9,8,8,0),(980,64,11,0,0,0),(981,64,12,0,0,0),(982,64,13,0,0,0),(983,64,14,0,0,0),(984,64,15,0,0,0),(985,64,16,1,1,0),(986,64,17,6,4,0),(987,64,18,1,1,0),(988,64,19,0,0,0),(989,65,1,14,10,0),(990,65,2,13,8,0),(991,65,3,2,2,0),(992,65,4,9,6,0),(993,65,5,7,7,0),(994,65,6,10,9,0),(995,65,7,34,20,0),(996,65,8,12,8,0),(997,65,10,0,0,0),(998,65,9,16,11,0),(999,65,11,0,0,0),(1000,65,12,0,0,0),(1001,65,13,0,0,0),(1002,65,14,0,0,0),(1003,65,15,0,0,0),(1004,65,16,8,6,0),(1005,65,17,16,10,0),(1006,65,18,12,7,0),(1007,65,19,7,5,0),(1008,66,1,6,5,0),(1009,66,2,7,6,0),(1010,66,3,2,2,0),(1011,66,4,2,2,0),(1012,66,5,6,5,0),(1013,66,6,6,5,0),(1014,66,7,13,4,0),(1015,66,8,6,6,0),(1016,66,10,0,0,0),(1017,66,9,2,2,0),(1018,66,11,0,0,0),(1019,66,12,0,0,0),(1020,66,13,0,0,0),(1021,66,14,0,0,0),(1022,66,15,0,0,0),(1023,66,16,4,3,0),(1024,66,17,12,11,0),(1025,66,18,6,5,0),(1026,66,19,2,2,0),(1027,67,1,9,9,0),(1028,67,2,5,4,0),(1029,67,3,3,3,0),(1030,67,4,1,1,0),(1031,67,5,6,5,0),(1032,67,6,4,4,0),(1033,67,7,10,10,0),(1034,67,8,5,5,0),(1035,67,10,0,0,0),(1036,67,9,3,3,0),(1037,67,11,0,0,0),(1038,67,12,0,0,0),(1039,67,13,0,0,0),(1040,67,14,0,0,0),(1041,67,15,0,0,0),(1042,67,16,2,2,0),(1043,67,17,13,10,0),(1044,67,18,2,2,0),(1045,67,19,3,3,0),(1046,0,1,0,0,0),(1047,0,2,0,0,0),(1048,0,3,0,0,0),(1049,0,4,0,0,0),(1050,0,5,0,0,0),(1051,0,6,0,0,0),(1052,0,7,0,0,0),(1053,0,8,0,0,0),(1054,0,10,0,0,0),(1055,0,9,0,0,0),(1056,0,11,0,0,0),(1057,0,12,0,0,0),(1058,0,13,0,0,0),(1059,0,14,0,0,0),(1060,0,15,0,0,0),(1061,0,16,0,0,0),(1062,0,17,0,0,0),(1063,0,18,0,0,0),(1064,0,19,0,0,0),(1065,69,1,46,17,0),(1066,69,2,35,14,1),(1067,69,3,23,6,0),(1068,69,4,22,7,0),(1069,69,5,27,9,0),(1070,69,6,27,11,0),(1071,69,7,29,13,0),(1072,69,8,29,10,0),(1073,69,10,0,0,0),(1074,69,9,27,11,0),(1075,69,11,0,0,0),(1076,69,12,0,0,0),(1077,69,13,0,0,0),(1078,69,14,0,0,0),(1079,69,15,0,0,0),(1080,69,16,24,8,0),(1081,69,17,41,14,0),(1082,69,18,27,7,0),(1083,69,19,20,5,0);
/*!40000 ALTER TABLE `les_statistics_per_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `range_status`
--

DROP TABLE IF EXISTS `range_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `range_status` (
  `range_status_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `active` tinyint(11) NOT NULL DEFAULT '0',
  `display_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`range_status_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `range_status`
--

LOCK TABLES `range_status` WRITE;
/*!40000 ALTER TABLE `range_status` DISABLE KEYS */;
INSERT INTO `range_status` VALUES (1,'Open',1,3),(2,'Closed',1,1),(4,'Canceled',0,4),(5,'Range Open-Club Regulated',1,5),(6,'Caliber Restriction',1,6),(7,'Event Full',0,7),(8,'Waiting List Reservations Only',0,8);
/*!40000 ALTER TABLE `range_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `description` text,
  `active_in_admin` int(11) NOT NULL DEFAULT '0',
  `active_in_frontend` int(11) NOT NULL DEFAULT '0',
  `need_update` int(11) NOT NULL DEFAULT '0',
  `display_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`role_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Super Administrator','super_administrator','',1,0,1,1),(2,'Administrator','administrator','',1,0,1,2),(3,'Contact','contact','',1,0,1,3),(4,'Badge Super Administrator','badge_super_administrator','',1,0,1,4),(5,'Badge Administrator','badge_administrator','',1,0,1,5),(6,'Legislative Email System Administrator','legislative_email_system_administrator','',1,0,1,6),(7,'Super Administrator (can not delete events)','super_administrator_no_delete','',1,0,1,7),(8,'Range Officer','range_officer','',1,0,1,8);
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles_modules_actions`
--

DROP TABLE IF EXISTS `roles_modules_actions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles_modules_actions` (
  `role_id` int(11) NOT NULL DEFAULT '0',
  `module_id` int(11) NOT NULL DEFAULT '0',
  `add` tinyint(4) NOT NULL DEFAULT '0',
  `edit` tinyint(4) NOT NULL DEFAULT '0',
  `delete` tinyint(4) NOT NULL DEFAULT '0',
  `view` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`role_id`,`module_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles_modules_actions`
--

LOCK TABLES `roles_modules_actions` WRITE;
/*!40000 ALTER TABLE `roles_modules_actions` DISABLE KEYS */;
INSERT INTO `roles_modules_actions` VALUES (1,1,1,1,1,1),(1,2,1,1,1,1),(1,3,1,1,1,1),(1,4,1,1,1,1),(1,5,1,1,1,1),(1,6,1,1,1,1),(1,7,1,1,1,1),(1,8,1,1,1,1),(1,9,1,1,1,1),(1,10,0,0,0,0),(1,11,0,0,0,0),(2,1,0,0,0,0),(2,2,1,1,1,1),(2,3,0,0,0,0),(2,4,0,0,0,0),(2,5,0,0,0,0),(2,6,0,0,0,0),(2,7,0,0,0,0),(2,8,0,0,0,0),(2,9,0,0,0,0),(4,1,1,1,1,1),(4,2,1,1,1,1),(4,3,1,1,1,1),(4,4,1,1,1,1),(4,5,1,1,1,1),(4,6,1,1,1,1),(4,7,1,1,1,1),(4,8,1,1,1,1),(4,9,1,1,1,1),(4,10,1,1,1,1),(4,11,1,1,1,1),(4,12,1,1,1,1),(4,15,1,1,1,1),(4,16,1,1,1,1),(5,1,1,0,0,1),(5,3,0,0,0,1),(5,10,0,0,0,1),(5,11,0,0,0,1),(5,12,0,0,0,1),(5,15,1,0,0,1),(5,16,1,0,0,1),(6,17,1,1,1,1),(6,18,1,1,1,1),(6,19,1,1,1,1),(6,20,1,1,1,1),(6,21,1,1,1,1),(6,22,1,1,1,1),(6,23,1,1,1,1),(7,1,1,1,0,1),(7,2,1,1,0,1),(7,3,1,1,0,1),(7,4,1,1,0,1),(7,5,1,1,0,1),(7,6,1,1,0,1),(7,7,1,1,0,1),(7,8,1,1,0,1),(7,9,1,1,0,1),(7,10,0,0,0,0),(7,11,0,0,0,0),(8,1,0,0,0,0),(8,2,0,0,0,1),(8,3,0,0,0,0),(8,4,0,0,0,0),(8,5,0,0,0,0),(8,6,0,0,0,0),(8,7,0,0,0,0),(8,8,0,0,0,0),(8,9,0,0,0,0),(8,10,0,0,0,0),(8,11,0,0,0,0),(4,17,1,1,1,1),(4,18,1,1,1,1),(4,19,1,1,1,1),(4,20,1,1,1,1),(4,21,1,1,1,1),(4,22,1,1,1,1),(4,23,1,1,1,1);
/*!40000 ALTER TABLE `roles_modules_actions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `safety_orientation_course`
--

DROP TABLE IF EXISTS `safety_orientation_course`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `safety_orientation_course` (
  `orientation_course_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `club_instructor_id` int(11) NOT NULL DEFAULT '0',
  `explanation_organization` text NOT NULL,
  `brief_history_agc` text NOT NULL,
  `characteristics_firearms` text NOT NULL,
  `safe_handling_procedures_different_types` text NOT NULL,
  `firearms_made_safe_cease_fire` text NOT NULL,
  `when_cease_fire_called` text NOT NULL,
  `hours_operation_reasons_strictly_enforced` text NOT NULL,
  `physical_walk_through_each_range` text NOT NULL,
  `yard_pistol_range_50` text NOT NULL,
  `yard_range_100` text NOT NULL,
  `yard_range_200` text NOT NULL,
  `trap_range` text NOT NULL,
  `archery_range` text NOT NULL,
  `patterning_range` text NOT NULL,
  `indoor_pellet_range` text NOT NULL,
  `restrooms_telephones_etc` text NOT NULL,
  `parking_procedures` text NOT NULL,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `submited_date_time` varchar(255) NOT NULL,
  `ip_address` varchar(255) NOT NULL,
  `active` int(11) NOT NULL DEFAULT '0',
  `display_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`orientation_course_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `safety_orientation_course`
--

LOCK TABLES `safety_orientation_course` WRITE;
/*!40000 ALTER TABLE `safety_orientation_course` DISABLE KEYS */;
/*!40000 ALTER TABLE `safety_orientation_course` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sent_emails`
--

DROP TABLE IF EXISTS `sent_emails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sent_emails` (
  `sent_email_id` int(11) NOT NULL AUTO_INCREMENT,
  `part_of_sent_email_id` int(11) NOT NULL DEFAULT '0',
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address1` varchar(255) NOT NULL,
  `address2` varchar(255) NOT NULL,
  `phone` varchar(25) NOT NULL,
  `fax` varchar(25) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state_id` int(11) DEFAULT '0',
  `zip` varchar(10) DEFAULT NULL,
  `add_me_to_tbmpac_mailing_list` tinyint(1) NOT NULL DEFAULT '0',
  `add_me_to_agc_mailing_list` tinyint(1) NOT NULL DEFAULT '0',
  `email_recievers` text NOT NULL,
  `email_recievers_group` int(11) NOT NULL DEFAULT '0',
  `subject` text NOT NULL,
  `message` text NOT NULL,
  `sender_ip` varchar(20) NOT NULL DEFAULT '',
  `is_sent` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `display_order` int(11) NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_locked` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`sent_email_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1763819 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sent_emails_history`
--

DROP TABLE IF EXISTS `sent_emails_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sent_emails_history` (
  `sent_email_id` int(11) NOT NULL AUTO_INCREMENT,
  `part_of_sent_email_id` int(11) NOT NULL DEFAULT '0',
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address1` varchar(255) NOT NULL,
  `address2` varchar(255) NOT NULL,
  `phone` varchar(25) NOT NULL,
  `fax` varchar(25) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state_id` int(11) DEFAULT '0',
  `zip` varchar(10) DEFAULT NULL,
  `add_me_to_tbmpac_mailing_list` tinyint(1) NOT NULL DEFAULT '0',
  `add_me_to_agc_mailing_list` tinyint(1) NOT NULL DEFAULT '0',
  `email_recievers` text NOT NULL,
  `email_recievers_group` int(11) NOT NULL DEFAULT '0',
  `subject` text NOT NULL,
  `message` text NOT NULL,
  `sender_ip` varchar(20) NOT NULL DEFAULT '',
  `is_sent` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `display_order` int(11) NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_locked` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`sent_email_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1741830 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `site_modules`
--

DROP TABLE IF EXISTS `site_modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_modules` (
  `module_id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `in_administration` tinyint(11) NOT NULL DEFAULT '0',
  `in_frontend` tinyint(11) NOT NULL DEFAULT '0',
  `active` tinyint(4) NOT NULL DEFAULT '0',
  `display_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`module_id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_modules`
--

LOCK TABLES `site_modules` WRITE;
/*!40000 ALTER TABLE `site_modules` DISABLE KEYS */;
INSERT INTO `site_modules` VALUES (1,'users','Users','',1,0,1,1),(2,'agc_calendar','Agc Calendar','',1,0,1,2),(3,'clubs','Clubs','',1,0,1,3),(4,'event_status','Event Status','',1,0,1,4),(5,'facilities','Facilities','',1,0,1,5),(6,'range_status','Range Status','',1,0,1,6),(7,'trap_operation_dates','Trap Operation Dates','',1,0,1,7),(8,'roles','Roles','',1,0,1,8),(9,'site_modules','Site Modules','',1,0,1,9),(10,'agc_titles','Agc Titles','',1,0,1,10),(11,'badge_types','Badge Types','',1,0,1,11),(12,'club_titles','Club Titles','',1,0,1,12),(15,'users_details','Users Details','',1,0,1,13),(16,'reports','Reports','',1,0,1,14),(17,'contacts','Contacts','',1,0,1,15),(18,'groups','Groups','',1,0,1,16),(19,'sent_emails','Sent Emails','<p>\n	Recieved and sent emails queue.</p>\n',1,0,1,17),(20,'words_filter','Words Filter',' ',1,0,1,18),(21,'visits','Visits',' ',1,0,1,19),(22,'blacklisted_emails','Blacklisted Emails','<p>\r\n	Maintain unwanted email addresses and avoid sending messages from these users.</p>\r\n',1,0,1,20),(23,'sent_emails_history','Sent Emails History','<p>\r\n   Sent emails.</p>',1,0,1,21);
/*!40000 ALTER TABLE `site_modules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `states`
--

DROP TABLE IF EXISTS `states`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `states` (
  `state_id` int(11) NOT NULL AUTO_INCREMENT,
  `country_id` int(11) NOT NULL DEFAULT '0',
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`state_id`)
) ENGINE=MyISAM AUTO_INCREMENT=79 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `states`
--

LOCK TABLES `states` WRITE;
/*!40000 ALTER TABLE `states` DISABLE KEYS */;
INSERT INTO `states` VALUES (3,225,'AS','American Samoa'),(4,225,'AZ','Arizona'),(5,225,'AR','Arkansas'),(6,225,'AF','Armed Forces Africa'),(7,225,'AA','Armed Forces Americas'),(8,225,'AC','Armed Forces Canada'),(9,225,'AE','Armed Forces Europe'),(10,225,'AM','Armed Forces Middle East'),(11,225,'AP','Armed Forces Pacific'),(12,225,'CA','California'),(13,225,'CO','Colorado'),(14,225,'CT','Connecticut'),(15,225,'DE','Delaware'),(16,225,'DC','District of Columbia'),(17,225,'FM','Federated States Of Micronesia'),(18,225,'FL','Florida'),(19,225,'GA','Georgia'),(20,225,'GU','Guam'),(21,225,'HI','Hawaii'),(22,225,'ID','Idaho'),(23,225,'IL','Illinois'),(24,225,'IN','Indiana'),(25,225,'IA','Iowa'),(26,225,'KS','Kansas'),(27,225,'KY','Kentucky'),(28,225,'LA','Louisiana'),(29,225,'ME','Maine'),(30,225,'MH','Marshall Islands'),(31,225,'MD','Maryland'),(32,225,'MA','Massachusetts'),(33,225,'MI','Michigan'),(34,225,'MN','Minnesota'),(35,225,'MS','Mississippi'),(36,225,'MO','Missouri'),(37,225,'MT','Montana'),(38,225,'NE','Nebraska'),(39,225,'NV','Nevada'),(40,225,'NH','New Hampshire'),(41,225,'NJ','New Jersey'),(42,225,'NM','New Mexico'),(43,225,'NY','New York'),(44,225,'NC','North Carolina'),(45,225,'ND','North Dakota'),(46,225,'MP','Northern Mariana Islands'),(47,225,'OH','Ohio'),(48,225,'OK','Oklahoma'),(49,225,'OR','Oregon'),(50,225,'PW','Palau'),(51,225,'PA','Pennsylvania'),(52,225,'PR','Puerto Rico'),(53,225,'RI','Rhode Island'),(54,225,'SC','South Carolina'),(55,225,'SD','South Dakota'),(56,225,'TN','Tennessee'),(57,225,'TX','Texas'),(58,225,'UT','Utah'),(59,225,'VT','Vermont'),(60,225,'VI','Virgin Islands'),(61,225,'VA','Virginia'),(62,225,'WA','Washington'),(63,225,'WV','West Virginia'),(64,225,'WI','Wisconsin'),(65,225,'WY','Wyoming'),(66,38,'AB','Alberta'),(67,38,'BC','British Columbia'),(68,38,'MB','Manitoba'),(69,38,'NF','Newfoundland'),(70,38,'NB','New Brunswick'),(71,38,'NS','Nova Scotia'),(72,38,'NT','Northwest Territories'),(73,38,'NU','Nunavut'),(74,38,'ON','Ontario'),(75,38,'PE','Prince Edward Island'),(76,38,'QC','Quebec'),(77,38,'SK','Saskatchewan'),(78,38,'YT','Yukon Territory'),(1,225,'AL','Alabama'),(2,225,'AK','Alaska');
/*!40000 ALTER TABLE `states` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trap_operation_dates`
--

DROP TABLE IF EXISTS `trap_operation_dates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trap_operation_dates` (
  `trap_operation_date_id` int(11) NOT NULL AUTO_INCREMENT,
  `OPNS_date` date NOT NULL DEFAULT '0000-00-00',
  `MTO_name_1` varchar(255) NOT NULL,
  `MTO_name_2` varchar(255) NOT NULL,
  `MTO_name_3` varchar(255) NOT NULL,
  `active` tinyint(11) NOT NULL DEFAULT '0',
  `display_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`trap_operation_date_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trap_operation_dates`
--

LOCK TABLES `trap_operation_dates` WRITE;
/*!40000 ALTER TABLE `trap_operation_dates` DISABLE KEYS */;
INSERT INTO `trap_operation_dates` VALUES (1,'2009-12-03','aa','sss','ddd',1,1);
/*!40000 ALTER TABLE `trap_operation_dates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL DEFAULT '0',
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `gender` enum('Male','Female') NOT NULL DEFAULT 'Male',
  `email` varchar(255) NOT NULL,
  `image` varchar(200) NOT NULL,
  `registration_date` date NOT NULL DEFAULT '0000-00-00',
  `activation_code` varchar(150) NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '0',
  `display_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10671 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,1,'admin','SystemAdmin','admin','admin','Male','no@associatedgunclubs.org','admin','0000-00-00','00',0,0);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_badges_years`
--

DROP TABLE IF EXISTS `users_badges_years`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users_badges_years` (
  `badge_year_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `badge_type_id` int(11) NOT NULL DEFAULT '0',
  `badge_number` varchar(25) NOT NULL,
  `color` varchar(25) NOT NULL,
  PRIMARY KEY (`badge_year_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_badges_years`
--

LOCK TABLES `users_badges_years` WRITE;
/*!40000 ALTER TABLE `users_badges_years` DISABLE KEYS */;
INSERT INTO `users_badges_years` VALUES (1,8359,1,'932','FF0000'),(1,8366,1,'480','FF0000'),(1,8372,1,'548','FF0000'),(1,8373,1,'253','FF0000'),(1,8377,1,'559','FF0000'),(1,8378,1,'880','FF0000'),(1,8379,1,'572','FF0000'),(1,8380,0,'0','FF0000'),(1,8381,1,'890','FF0000'),(1,8387,1,'1036','FF0000'),(1,8389,1,'210','FF0000'),(1,8391,0,'1203','FF0000'),(1,8392,1,'254','FF0000'),(1,8393,1,'1940','FF0000'),(1,8396,1,'227','FF0000'),(1,8397,1,'1739','FF0000'),(1,8402,1,'1003','FF0000'),(1,8403,1,'300','FF0000'),(1,8404,1,'581','FF0000'),(1,8407,1,'258','FF0000'),(1,8409,1,'692','FF0000'),(1,8411,9,'140','FF0000'),(1,8412,1,'1708','FF0000'),(1,8413,1,'3','FF0000'),(1,8414,1,'883','FF0000'),(1,8415,1,'205','FF0000'),(1,8417,1,'957','FF0000'),(1,8418,1,'2','FF0000'),(1,8419,1,'83','FF0000'),(1,8420,1,'18','FF0000'),(1,8421,1,'1398','FF0000'),(1,8422,1,'1034','FF0000'),(1,8423,1,'822','FF0000'),(1,8424,1,'711','FF0000'),(1,8431,1,'0','FF0000'),(1,8434,1,'219','FF0000'),(1,8436,1,'459','FF0000'),(1,8437,1,'622','FF0000'),(1,8440,1,'226','FF0000'),(1,8441,1,'1771','FF0000'),(1,8442,0,'0','FF0000'),(1,8446,1,'582','FF0000'),(1,8447,1,'1494','FF0000'),(1,8449,1,'201','FF0000'),(1,8454,0,'0','FF0000'),(1,8456,1,'232','FF0000'),(1,8463,1,'1089','FF0000'),(1,8464,1,'1078','FF0000'),(1,8468,1,'114','FF0000'),(1,8469,0,'0','FF0000'),(1,8470,1,'899','FF0000'),(1,8471,1,'977','FF0000'),(1,8473,1,'217','FF0000'),(1,8474,1,'1045','FF0000'),(1,8476,1,'1006','FF0000'),(1,8486,1,'1722','FF0000'),(1,8487,1,'1713','FF0000'),(1,8489,1,'1192','FF0000'),(1,8490,1,'1281','FF0000'),(1,8495,9,'148','FF0000'),(1,8496,1,'1052','FF0000'),(1,8497,1,'293','FF0000'),(1,8498,9,'109','FF0000'),(1,8499,9,'8','FF0000'),(1,8501,1,'345','FF0000'),(1,8502,1,'368','FF0000'),(1,8505,1,'57','FF0000'),(1,8508,1,'1527','FF0000'),(1,8509,9,'147','FF0000'),(1,8511,1,'949','FF0000'),(1,8512,9,'58','FF0000'),(1,8513,1,'1014','FF0000'),(1,8514,1,'1061','FF0000'),(1,8518,1,'231','FF0000'),(1,8524,1,'230','FF0000'),(1,8525,1,'245','FF0000'),(1,8526,1,'60','FF0000'),(1,8530,1,'1241','FF0000'),(1,8531,1,'1939','FF0000'),(1,8532,1,'1000','FF0000'),(1,8533,1,'407','FF0000'),(1,8535,1,'999','FF0000'),(1,8536,1,'420','FF0000'),(1,8538,1,'813','FF0000'),(1,8542,1,'256','FF0000'),(1,8543,1,'798','FF0000'),(1,8544,1,'1821','FF0000'),(1,8547,1,'1862','FF0000'),(1,8550,1,'1774','FF0000'),(1,8551,1,'1776','FF0000'),(1,8552,1,'690','FF0000'),(1,8553,1,'1259','FF0000'),(1,8554,1,'640','FF0000'),(1,8555,1,'1751','FF0000'),(1,8560,0,'1207','FF0000'),(1,8561,0,'637','FF0000'),(1,8562,0,'1251','FF0000'),(1,8563,0,'154','FF0000'),(1,8568,1,'279','FF0000'),(1,8571,1,'460','FF0000'),(1,8572,1,'580','FF0000'),(1,8573,1,'471','FF0000'),(1,8574,1,'558','FF0000'),(1,8577,1,'645','FF0000'),(1,8582,1,'446','FF0000'),(1,8583,1,'235','FF0000'),(1,8585,1,'552','FF0000'),(1,8587,1,'1','FF0000'),(1,8589,9,'3','FF0000'),(1,8592,1,'4','FF0000'),(1,8593,1,'5','FF0000'),(1,8594,1,'6','FF0000'),(1,8595,1,'7','FF0000'),(1,8599,1,'8','FF0000'),(1,8600,1,'10','FF0000'),(1,8601,9,'11','FF0000'),(1,8602,1,'12','FF0000'),(1,8603,1,'15','FF0000'),(1,8604,1,'17','FF0000'),(1,8607,1,'19','FF0000'),(1,8608,1,'20','FF0000'),(1,8609,1,'22','FF0000'),(1,8610,9,'23','FF0000'),(1,8613,1,'24','FF0000'),(1,8614,1,'25','FF0000'),(1,8615,1,'26','FF0000'),(1,8616,1,'27','FF0000'),(1,8617,9,'28','FF0000'),(1,8618,1,'28','FF0000'),(1,8619,1,'29','FF0000'),(1,8620,9,'30','FF0000'),(1,8621,1,'31','FF0000'),(1,8622,1,'32','FF0000'),(1,8623,9,'33','FF0000'),(1,8624,1,'33','FF0000'),(1,8625,1,'34','FF0000'),(1,8626,9,'35','FF0000'),(1,8627,1,'36','FF0000'),(1,8628,9,'37','FF0000'),(1,8629,1,'37','FF0000'),(1,8630,1,'38','FF0000'),(1,8631,1,'40','FF0000'),(1,8632,9,'41','FF0000'),(1,8633,1,'41','FF0000'),(1,8634,9,'42','FF0000'),(1,8635,1,'42','FF0000'),(1,8636,1,'43','FF0000'),(1,8637,1,'44','FF0000'),(1,8638,9,'45','FF0000'),(1,8639,1,'45','FF0000'),(1,8640,1,'46','FF0000'),(1,8641,9,'48','FF0000'),(1,8643,1,'49','FF0000'),(1,8644,1,'50','FF0000'),(1,8645,1,'51','FF0000'),(1,8646,9,'52','FF0000'),(1,8647,1,'52','FF0000'),(1,8648,9,'53','FF0000'),(1,8649,1,'53','FF0000'),(1,8650,9,'55','FF0000'),(1,8651,1,'55','FF0000'),(1,8652,1,'56','FF0000'),(1,8655,9,'57','FF0000'),(1,8658,1,'59','FF0000'),(1,8660,9,'61','FF0000'),(1,8661,1,'61','FF0000'),(1,8662,1,'62','FF0000'),(1,8663,1,'63','FF0000'),(1,8664,1,'65','FF0000'),(1,8665,9,'66','FF0000'),(1,8666,1,'66','FF0000'),(1,8667,1,'67','FF0000'),(1,8668,9,'67','FF0000'),(1,8669,1,'68','FF0000'),(1,8670,1,'70','FF0000'),(1,8671,9,'70','FF0000'),(1,8672,1,'72','FF0000'),(1,8673,9,'73','FF0000'),(1,8674,9,'74','FF0000'),(1,8675,1,'74','FF0000'),(1,8676,1,'75','FF0000'),(1,8677,1,'76','FF0000'),(1,8678,1,'77','FF0000'),(1,8679,1,'79','FF0000'),(1,8680,1,'80','FF0000'),(1,8681,1,'82','FF0000'),(1,8682,9,'83','FF0000'),(1,8685,9,'84','FF0000'),(1,8686,1,'84','FF0000'),(1,8687,5,'87','FF0000'),(1,8688,9,'87','FF0000'),(1,8689,1,'88','FF0000'),(1,8690,9,'89','FF0000'),(1,8691,9,'90','FF0000'),(1,8692,1,'90','FF0000'),(1,8693,9,'91','FF0000'),(1,8694,1,'91','FF0000'),(1,8695,1,'93','FF0000'),(1,8696,1,'94','FF0000'),(1,8697,1,'96','FF0000'),(1,8698,9,'97','FF0000'),(1,8699,1,'97','FF0000'),(1,8700,9,'98','FF0000'),(1,8701,1,'98','FF0000'),(1,8702,9,'99','FF0000'),(1,8703,1,'99','FF0000'),(1,8704,1,'100','FF0000'),(1,8705,1,'102','FF0000'),(1,8706,1,'103','FF0000'),(1,8707,1,'105','FF0000'),(1,8708,9,'106','FF0000'),(1,8709,1,'106','FF0000'),(1,8710,1,'107','FF0000'),(1,8711,1,'108','FF0000'),(1,8713,1,'109','FF0000'),(1,8714,1,'110','FF0000'),(1,8715,9,'112','FF0000'),(1,8716,1,'112','FF0000'),(1,8717,1,'113','FF0000'),(1,8719,1,'114','FF0000'),(1,8720,5,'116','FF0000'),(1,8721,1,'117','FF0000'),(1,8722,1,'118','FF0000'),(1,8723,1,'119','FF0000'),(1,8724,1,'120','FF0000'),(1,8725,1,'121','FF0000'),(1,8726,9,'121','FF0000'),(1,8727,5,'122','FF0000'),(1,8728,9,'122','FF0000'),(1,8729,9,'123','FF0000'),(1,8730,1,'123','FF0000'),(1,8731,9,'124','FF0000'),(1,8732,1,'124','FF0000'),(1,8733,9,'125','FF0000'),(1,8734,1,'125','FF0000'),(1,8735,9,'127','FF0000'),(1,8736,1,'127','FF0000'),(1,8737,1,'128','FF0000'),(1,8738,9,'129','FF0000'),(1,8739,1,'129','FF0000'),(1,8740,9,'130','FF0000'),(1,8741,9,'131','FF0000'),(1,8742,1,'131','FF0000'),(1,8743,9,'132','FF0000'),(1,8744,1,'132','FF0000'),(1,8745,1,'133','FF0000'),(1,8746,1,'134','FF0000'),(1,8747,9,'135','FF0000'),(1,8748,1,'135','FF0000'),(1,8750,1,'136','FF0000'),(1,8751,9,'137','FF0000'),(1,8752,1,'138','FF0000'),(1,8753,1,'139','FF0000'),(1,8755,1,'140','FF0000'),(1,8756,1,'141','FF0000'),(1,8757,9,'141','FF0000'),(1,8758,9,'143','FF0000'),(1,8759,1,'144','FF0000'),(1,8760,9,'144','FF0000'),(1,8761,1,'145','FF0000'),(1,8762,1,'146','FF0000'),(1,8765,1,'147','FF0000'),(1,8767,1,'148','FF0000'),(1,8768,9,'149','FF0000'),(1,8769,1,'150','FF0000'),(1,8770,9,'150','FF0000'),(1,8771,1,'151','FF0000'),(1,8772,1,'153','FF0000'),(1,8773,9,'153','FF0000'),(1,8774,1,'154','FF0000'),(1,8776,9,'155','FF0000'),(1,8777,1,'155','FF0000'),(1,8778,1,'156','FF0000'),(1,8779,1,'157','FF0000'),(1,8780,9,'158','FF0000'),(1,8781,1,'158','FF0000'),(1,8782,1,'160','FF0000'),(1,8783,9,'161','FF0000'),(1,8784,1,'161','FF0000'),(1,8785,1,'162','FF0000'),(1,8786,9,'162','FF0000'),(1,8787,9,'163','FF0000'),(1,8788,9,'164','FF0000'),(1,8789,1,'164','FF0000'),(1,8790,1,'165','FF0000'),(1,8791,9,'166','FF0000'),(1,8792,1,'166','FF0000'),(1,8793,1,'167','FF0000'),(1,8794,9,'168','FF0000'),(1,8795,1,'168','FF0000'),(1,8796,9,'169','FF0000'),(1,8797,9,'170','FF0000'),(1,8798,1,'170','FF0000'),(1,8799,1,'171','FF0000'),(1,8800,9,'172','FF0000'),(1,8801,9,'173','FF0000'),(1,8802,1,'173','FF0000'),(1,8803,9,'175','FF0000'),(1,8804,1,'175','FF0000'),(1,8805,9,'176','FF0000'),(1,8806,1,'176','FF0000'),(1,8807,9,'177','FF0000'),(1,8808,1,'177','FF0000'),(1,8809,1,'178','FF0000'),(1,8810,1,'180','FF0000'),(1,8811,1,'184','FF0000'),(1,8813,1,'187','FF0000'),(1,8814,9,'187','FF0000'),(1,8815,9,'190','FF0000'),(1,8816,1,'191','FF0000'),(1,8817,1,'192','FF0000'),(1,8818,1,'193','FF0000'),(1,8819,1,'195','FF0000'),(1,8820,1,'197','FF0000'),(1,8821,9,'198','FF0000'),(1,8822,1,'198','FF0000'),(1,8823,1,'199','FF0000'),(1,8824,1,'200','FF0000'),(1,8827,1,'202','FF0000'),(1,8828,9,'202','FF0000'),(1,8829,1,'203','FF0000'),(1,8830,1,'204','FF0000'),(1,8833,1,'206','FF0000'),(1,8834,1,'207','FF0000'),(1,8835,1,'208','FF0000'),(1,8836,9,'208','FF0000'),(1,8837,1,'209','FF0000'),(1,8838,9,'209','FF0000'),(1,8841,1,'211','FF0000'),(1,8842,9,'211','FF0000'),(1,8843,1,'212','FF0000'),(1,8844,1,'213','FF0000'),(1,8845,1,'214','FF0000'),(1,8846,1,'215','FF0000'),(1,8847,1,'216','FF0000'),(1,8849,1,'218','FF0000'),(1,8852,9,'219','FF0000'),(1,8853,1,'220','FF0000'),(1,8854,1,'221','FF0000'),(1,8856,9,'222','FF0000'),(1,8857,1,'223','FF0000'),(1,8858,9,'223','FF0000'),(1,8862,5,'225','FF0000'),(1,8867,5,'228','FF0000'),(1,8868,1,'229','FF0000'),(1,8871,9,'230','FF0000'),(1,8874,9,'231','FF0000'),(1,8877,1,'233','FF0000'),(1,8878,5,'234','FF0000'),(1,8879,9,'234','FF0000'),(1,8881,1,'236','FF0000'),(1,8882,5,'237','FF0000'),(1,8883,9,'237','FF0000'),(1,8884,9,'238','FF0000'),(1,8885,1,'238','FF0000'),(1,8886,9,'239','FF0000'),(1,8887,1,'239','FF0000'),(1,8888,9,'240','FF0000'),(1,8889,1,'240','FF0000'),(1,8890,1,'241','FF0000'),(1,8891,1,'242','FF0000'),(1,8892,1,'243','FF0000'),(1,8893,1,'244','FF0000'),(1,8895,1,'246','FF0000'),(1,8896,1,'247','FF0000'),(1,8897,1,'248','FF0000'),(1,8898,1,'249','FF0000'),(1,8899,1,'250','FF0000'),(1,8900,1,'251','FF0000'),(1,8906,1,'255','FF0000'),(1,8910,1,'257','FF0000'),(1,8914,1,'259','FF0000'),(1,8915,1,'260','FF0000'),(1,8916,1,'261','FF0000'),(1,8917,1,'262','FF0000'),(1,8918,1,'263','FF0000'),(1,8919,1,'264','FF0000'),(1,8920,1,'265','FF0000'),(1,8921,1,'266','FF0000'),(1,8922,1,'267','FF0000'),(1,8923,1,'268','FF0000'),(1,8924,1,'269','FF0000'),(1,8925,1,'270','FF0000'),(1,8926,1,'271','FF0000'),(1,8927,1,'272','FF0000'),(1,8928,1,'273','FF0000'),(1,8929,1,'274','FF0000'),(1,8930,1,'275','FF0000'),(1,8931,1,'276','FF0000'),(1,8932,1,'277','FF0000'),(1,8933,1,'278','FF0000'),(1,8936,1,'280','FF0000'),(1,8937,1,'282','FF0000'),(1,8938,1,'283','FF0000'),(1,8939,1,'284','FF0000'),(1,8940,1,'285','FF0000'),(1,8941,1,'286','FF0000'),(1,8942,1,'287','FF0000'),(1,8943,1,'288','FF0000'),(1,8944,1,'289','FF0000'),(1,8945,1,'290','FF0000'),(1,8946,1,'291','FF0000'),(1,8949,1,'295','FF0000'),(1,8950,1,'296','FF0000'),(1,8951,1,'297','FF0000'),(1,8952,1,'298','FF0000'),(1,8953,1,'299','FF0000'),(1,8955,5,'301','FF0000'),(1,8956,1,'302','FF0000'),(1,8958,1,'303','FF0000'),(1,8959,1,'304','FF0000'),(1,8960,1,'305','FF0000'),(1,8961,1,'306','FF0000'),(1,8962,1,'307','FF0000'),(1,8963,1,'308','FF0000'),(1,8964,1,'309','FF0000'),(1,8965,1,'310','FF0000'),(1,8966,1,'311','FF0000'),(1,8967,1,'312','FF0000'),(1,8968,1,'313','FF0000'),(1,8969,1,'314','FF0000'),(1,8970,1,'315','FF0000'),(1,8971,1,'316','FF0000'),(1,8972,1,'317','FF0000'),(1,8973,1,'318','FF0000'),(1,8974,1,'319','FF0000'),(1,8975,1,'320','FF0000'),(1,8976,1,'321','FF0000'),(1,8977,1,'322','FF0000'),(1,8978,1,'323','FF0000'),(1,8979,1,'324','FF0000'),(1,8980,1,'325','FF0000'),(1,8981,1,'326','FF0000'),(1,8982,1,'327','FF0000'),(1,8983,1,'328','FF0000'),(1,8984,1,'329','FF0000'),(1,8985,1,'330','FF0000'),(1,8986,1,'331','FF0000'),(1,8987,1,'332','FF0000'),(1,8988,1,'333','FF0000'),(1,8989,1,'334','FF0000'),(1,8990,1,'335','FF0000'),(1,8991,1,'336','FF0000'),(1,8992,1,'337','FF0000'),(1,8993,1,'338','FF0000'),(1,8994,1,'339','FF0000'),(1,8995,1,'340','FF0000'),(1,8996,1,'341','FF0000'),(1,8997,1,'342','FF0000'),(1,8998,1,'343','FF0000'),(1,8999,1,'344','FF0000'),(1,9002,1,'346','FF0000'),(1,9003,1,'347','FF0000'),(1,9004,1,'348','FF0000'),(1,9005,1,'349','FF0000'),(1,9006,1,'350','FF0000'),(1,9007,1,'351','FF0000'),(1,9008,1,'352','FF0000'),(1,9009,1,'353','FF0000'),(1,9010,1,'354','FF0000'),(1,9011,1,'355','FF0000'),(1,9012,1,'356','FF0000'),(1,9013,1,'357','FF0000'),(1,9014,1,'358','FF0000'),(1,9015,1,'359','FF0000'),(1,9016,1,'360','FF0000'),(1,9017,1,'361','FF0000'),(1,9018,1,'362','FF0000'),(1,9019,1,'363','FF0000'),(1,9020,1,'364','FF0000'),(1,9021,1,'365','FF0000'),(1,9022,1,'366','FF0000'),(1,9023,1,'367','FF0000'),(1,9025,1,'369','FF0000'),(1,9026,1,'370','FF0000'),(1,9027,1,'371','FF0000'),(1,9029,1,'372','FF0000'),(1,9030,1,'373','FF0000'),(1,9031,1,'374','FF0000'),(1,9032,1,'375','FF0000'),(1,9033,1,'376','FF0000'),(1,9034,1,'377','FF0000'),(1,9035,1,'378','FF0000'),(1,9036,1,'379','FF0000'),(1,9037,1,'380','FF0000'),(1,9038,1,'381','FF0000'),(1,9039,1,'382','FF0000'),(1,9040,1,'383','FF0000'),(1,9041,1,'384','FF0000'),(1,9042,1,'385','FF0000'),(1,9043,1,'386','FF0000'),(1,9044,1,'387','FF0000'),(1,9045,1,'388','FF0000'),(1,9046,1,'389','FF0000'),(1,9047,1,'390','FF0000'),(1,9048,1,'391','FF0000'),(1,9049,1,'392','FF0000'),(1,9050,1,'393','FF0000'),(1,9051,1,'394','FF0000'),(1,9052,1,'395','FF0000'),(1,9053,1,'396','FF0000'),(1,9054,1,'397','FF0000'),(1,9055,1,'398','FF0000'),(1,9056,1,'399','FF0000'),(1,9057,1,'400','FF0000'),(1,9058,1,'401','FF0000'),(1,9059,1,'402','FF0000'),(1,9060,1,'403','FF0000'),(1,9061,1,'404','FF0000'),(1,9062,1,'405','FF0000'),(1,9063,1,'406','FF0000'),(1,9065,1,'408','FF0000'),(1,9066,1,'409','FF0000'),(1,9067,1,'410','FF0000'),(1,9068,1,'411','FF0000'),(1,9069,1,'412','FF0000'),(1,9070,1,'413','FF0000'),(1,9071,1,'414','FF0000'),(1,9072,1,'415','FF0000'),(1,9073,1,'416','FF0000'),(1,9074,1,'417','FF0000'),(1,9075,1,'418','FF0000'),(1,9076,1,'419','FF0000'),(1,9078,1,'421','FF0000'),(1,9079,1,'422','FF0000'),(1,9080,1,'423','FF0000'),(1,9081,1,'424','FF0000'),(1,9082,1,'425','FF0000'),(1,9083,1,'426','FF0000'),(1,9084,1,'427','FF0000'),(1,9085,1,'428','FF0000'),(1,9086,1,'429','FF0000'),(1,9087,1,'430','FF0000'),(1,9088,1,'431','FF0000'),(1,9089,1,'432','FF0000'),(1,9090,1,'433','FF0000'),(1,9091,1,'434','FF0000'),(1,9092,1,'435','FF0000'),(1,9093,1,'436','FF0000'),(1,9094,1,'437','FF0000'),(1,9095,1,'438','FF0000'),(1,9096,1,'439','FF0000'),(1,9097,1,'440','FF0000'),(1,9098,1,'441','FF0000'),(1,9099,1,'442','FF0000'),(1,9100,1,'443','FF0000'),(1,9101,1,'444','FF0000'),(1,9102,1,'445','FF0000'),(1,9103,1,'446','FF0000'),(1,9104,1,'447','FF0000'),(1,9105,1,'448','FF0000'),(1,9107,1,'449','FF0000'),(1,9108,1,'450','FF0000'),(1,9109,1,'451','FF0000'),(1,9110,1,'452','FF0000'),(1,9111,1,'453','FF0000'),(1,9112,1,'454','FF0000'),(1,9113,1,'455','FF0000'),(1,9114,1,'456','FF0000'),(1,9115,1,'457','FF0000'),(1,9116,1,'458','FF0000'),(1,9120,1,'461','FF0000'),(1,9121,1,'1601','FF0000'),(1,9122,1,'463','FF0000'),(1,9123,1,'464','FF0000'),(1,9124,1,'465','FF0000'),(1,9125,1,'466','FF0000'),(1,9126,1,'467','FF0000'),(1,9127,1,'468','FF0000'),(1,9128,1,'469','FF0000'),(1,9129,1,'470','FF0000'),(1,9132,1,'472','FF0000'),(1,9133,1,'473','FF0000'),(1,9134,1,'474','FF0000'),(1,9135,1,'475','FF0000'),(1,9136,1,'476','FF0000'),(1,9137,1,'477','FF0000'),(1,9138,1,'478','FF0000'),(1,9139,1,'479','FF0000'),(1,9142,1,'481','FF0000'),(1,9143,1,'482','FF0000'),(1,9144,1,'483','FF0000'),(1,9145,1,'484','FF0000'),(1,9146,1,'485','FF0000'),(1,9147,1,'486','FF0000'),(1,9148,1,'487','FF0000'),(1,9149,1,'488','FF0000'),(1,9150,1,'489','FF0000'),(1,9151,1,'490','FF0000'),(1,9152,1,'491','FF0000'),(1,9153,1,'492','FF0000'),(1,9154,1,'493','FF0000'),(1,9155,1,'494','FF0000'),(1,9156,1,'495','FF0000'),(1,9157,1,'496','FF0000'),(1,9158,1,'497','FF0000'),(1,9159,1,'498','FF0000'),(1,9160,1,'499','FF0000'),(1,9161,1,'500','FF0000'),(1,9162,1,'501','FF0000'),(1,9163,1,'502','FF0000'),(1,9164,1,'503','FF0000'),(1,9165,1,'504','FF0000'),(1,9166,1,'505','FF0000'),(1,9167,5,'506','FF0000'),(1,9168,1,'507','FF0000'),(1,9169,1,'508','FF0000'),(1,9170,1,'509','FF0000'),(1,9171,1,'510','FF0000'),(1,9172,1,'511','FF0000'),(1,9173,1,'512','FF0000'),(1,9174,1,'513','FF0000'),(1,9175,1,'514','FF0000'),(1,9176,1,'515','FF0000'),(1,9177,1,'516','FF0000'),(1,9178,1,'517','FF0000'),(1,9179,1,'517','FF0000'),(1,9180,1,'518','FF0000'),(1,9181,1,'519','FF0000'),(1,9182,1,'520','FF0000'),(1,9183,1,'521','FF0000'),(1,9184,1,'522','FF0000'),(1,9185,1,'523','FF0000'),(1,9186,1,'524','FF0000'),(1,9187,1,'525','FF0000'),(1,9188,1,'526','FF0000'),(1,9189,1,'527','FF0000'),(1,9190,1,'528','FF0000'),(1,9191,1,'529','FF0000'),(1,9192,1,'530','FF0000'),(1,9193,1,'531','FF0000'),(1,9194,1,'532','FF0000'),(1,9195,1,'533','FF0000'),(1,9196,1,'534','FF0000'),(1,9197,1,'535','FF0000'),(1,9198,1,'536','FF0000'),(1,9199,1,'537','FF0000'),(1,9200,1,'538','FF0000'),(1,9201,1,'539','FF0000'),(1,9202,1,'540','FF0000'),(1,9203,1,'541','FF0000'),(1,9204,1,'542','FF0000'),(1,9205,1,'543','FF0000'),(1,9206,1,'544','FF0000'),(1,9207,1,'545','FF0000'),(1,9208,1,'546','FF0000'),(1,9209,1,'547','FF0000'),(1,9212,1,'549','FF0000'),(1,9213,1,'550','FF0000'),(1,9214,1,'551','FF0000'),(1,9216,1,'553','FF0000'),(1,9217,1,'554','FF0000'),(1,9218,1,'555','FF0000'),(1,9219,1,'556','FF0000'),(1,9220,1,'557','FF0000'),(1,9226,1,'560','FF0000'),(1,9227,1,'561','FF0000'),(1,9228,1,'562','FF0000'),(1,9229,1,'563','FF0000'),(1,9230,1,'564','FF0000'),(1,9231,1,'565','FF0000'),(1,9232,1,'566','FF0000'),(1,9233,1,'567','FF0000'),(1,9234,1,'568','FF0000'),(1,9235,1,'569','FF0000'),(1,9236,1,'570','FF0000'),(1,9237,5,'571','FF0000'),(1,9240,1,'573','FF0000'),(1,9241,1,'574','FF0000'),(1,9242,1,'575','FF0000'),(1,9243,1,'576','FF0000'),(1,9244,1,'577','FF0000'),(1,9245,1,'578','FF0000'),(1,9246,1,'579','FF0000'),(1,9251,1,'583','FF0000'),(1,9252,1,'584','FF0000'),(1,9253,1,'585','FF0000'),(1,9254,1,'586','FF0000'),(1,9255,1,'587','FF0000'),(1,9256,1,'588','FF0000'),(1,9257,1,'589','FF0000'),(1,9258,1,'590','FF0000'),(1,9259,1,'591','FF0000'),(1,9260,1,'593','FF0000'),(1,9261,1,'594','FF0000'),(1,9262,1,'595','FF0000'),(1,9263,1,'596','FF0000'),(1,9264,1,'597','FF0000'),(1,9265,1,'598','FF0000'),(1,9266,1,'599','FF0000'),(1,9267,1,'600','FF0000'),(1,9268,1,'601','FF0000'),(1,9269,1,'602','FF0000'),(1,9270,1,'603','FF0000'),(1,9271,1,'604','FF0000'),(1,9272,1,'605','FF0000'),(1,9273,1,'606','FF0000'),(1,9274,1,'607','FF0000'),(1,9275,1,'608','FF0000'),(1,9276,1,'609','FF0000'),(1,9278,1,'610','FF0000'),(1,9279,1,'611','FF0000'),(1,9280,1,'612','FF0000'),(1,9281,1,'613','FF0000'),(1,9282,1,'614','FF0000'),(1,9283,1,'615','FF0000'),(1,9284,1,'616','FF0000'),(1,9285,1,'617','FF0000'),(1,9286,1,'618','FF0000'),(1,9287,1,'619','FF0000'),(1,9288,1,'620','FF0000'),(1,9289,1,'621','FF0000'),(1,9292,1,'623','FF0000'),(1,9293,1,'624','FF0000'),(1,9294,1,'625','FF0000'),(1,9295,1,'626','FF0000'),(1,9296,1,'627','FF0000'),(1,9298,1,'629','FF0000'),(1,9300,1,'630','FF0000'),(1,9301,1,'631','FF0000'),(1,9302,1,'632','FF0000'),(1,9303,1,'633','FF0000'),(1,9304,1,'634','FF0000'),(1,9305,1,'635','FF0000'),(1,9306,1,'636','FF0000'),(1,9308,1,'638','FF0000'),(1,9309,1,'639','FF0000'),(1,9312,1,'641','FF0000'),(1,9313,1,'642','FF0000'),(1,9314,1,'643','FF0000'),(1,9315,1,'644','FF0000'),(1,9317,1,'647','FF0000'),(1,9318,1,'648','FF0000'),(1,9319,1,'649','FF0000'),(1,9321,1,'650','FF0000'),(1,9322,1,'651','FF0000'),(1,9323,1,'652','FF0000'),(1,9324,1,'653','FF0000'),(1,9325,1,'654','FF0000'),(1,9326,1,'655','FF0000'),(1,9327,1,'656','FF0000'),(1,9328,1,'657','FF0000'),(1,9329,1,'658','FF0000'),(1,9330,1,'659','FF0000'),(1,9332,1,'660','FF0000'),(1,9333,1,'661','FF0000'),(1,9334,1,'662','FF0000'),(1,9335,1,'663','FF0000'),(1,9336,1,'664','FF0000'),(1,9337,1,'665','FF0000'),(1,9338,1,'666','FF0000'),(1,9339,1,'667','FF0000'),(1,9340,1,'668','FF0000'),(1,9341,1,'669','FF0000'),(1,9342,1,'670','FF0000'),(1,9343,1,'671','FF0000'),(1,9344,1,'672','FF0000'),(1,9345,1,'673','FF0000'),(1,9346,1,'674','FF0000'),(1,9347,1,'675','FF0000'),(1,9348,1,'676','FF0000'),(1,9349,1,'677','FF0000'),(1,9350,5,'678','FF0000'),(1,9351,1,'679','FF0000'),(1,9352,1,'680','FF0000'),(1,9353,1,'681','FF0000'),(1,9354,1,'682','FF0000'),(1,9355,1,'683','FF0000'),(1,9356,1,'684','FF0000'),(1,9357,1,'685','FF0000'),(1,9358,5,'686','FF0000'),(1,9359,1,'687','FF0000'),(1,9360,1,'688','FF0000'),(1,9361,1,'689','FF0000'),(1,9364,1,'691','FF0000'),(1,9367,1,'693','FF0000'),(1,9368,1,'694','FF0000'),(1,9369,1,'695','FF0000'),(1,9370,1,'696','FF0000'),(1,9371,1,'697','FF0000'),(1,9372,1,'698','FF0000'),(1,9374,1,'699','FF0000'),(1,9375,1,'700','FF0000'),(1,9376,1,'701','FF0000'),(1,9377,1,'702','FF0000'),(1,9378,5,'704','FF0000'),(1,9379,1,'705','FF0000'),(1,9380,1,'706','FF0000'),(1,9381,1,'708','FF0000'),(1,9382,1,'709','FF0000'),(1,9383,5,'710','FF0000'),(1,9385,1,'712','FF0000'),(1,9386,1,'713','FF0000'),(1,9387,1,'715','FF0000'),(1,9388,1,'716','FF0000'),(1,9389,5,'717','FF0000'),(1,9390,1,'718','FF0000'),(1,9391,1,'720','FF0000'),(1,9392,1,'721','FF0000'),(1,9393,1,'722','FF0000'),(1,9394,1,'723','FF0000'),(1,9395,1,'724','FF0000'),(1,9396,1,'725','FF0000'),(1,9397,1,'726','FF0000'),(1,9398,1,'727','FF0000'),(1,9399,1,'729','FF0000'),(1,9400,1,'730','FF0000'),(1,9401,1,'731','FF0000'),(1,9402,5,'732','FF0000'),(1,9403,1,'733','FF0000'),(1,9404,5,'734','FF0000'),(1,9406,1,'735','FF0000'),(1,9407,1,'736','FF0000'),(1,9408,1,'737','FF0000'),(1,9409,1,'738','FF0000'),(1,9410,1,'739','FF0000'),(1,9411,1,'740','FF0000'),(1,9412,1,'741','FF0000'),(1,9413,1,'742','FF0000'),(1,9414,1,'743','FF0000'),(1,9415,1,'744','FF0000'),(1,9416,1,'746','FF0000'),(1,9417,1,'747','FF0000'),(1,9418,1,'748','FF0000'),(1,9419,1,'750','FF0000'),(1,9420,1,'751','FF0000'),(1,9421,1,'752','FF0000'),(1,9422,1,'753','FF0000'),(1,9423,1,'754','FF0000'),(1,9424,1,'756','FF0000'),(1,9425,1,'758','FF0000'),(1,9426,1,'760','FF0000'),(1,9427,1,'761','FF0000'),(1,9428,1,'762','FF0000'),(1,9429,1,'763','FF0000'),(1,9430,1,'765','FF0000'),(1,9431,1,'766','FF0000'),(1,9432,5,'767','FF0000'),(1,9433,1,'769','FF0000'),(1,9434,1,'770','FF0000'),(1,9435,1,'771','FF0000'),(1,9436,5,'773','FF0000'),(1,9437,1,'774','FF0000'),(1,9438,1,'775','FF0000'),(1,9439,1,'776','FF0000'),(1,9440,1,'777','FF0000'),(1,9441,1,'778','FF0000'),(1,9442,1,'779','FF0000'),(1,9443,1,'780','FF0000'),(1,9444,1,'781','FF0000'),(1,9445,1,'782','FF0000'),(1,9446,1,'783','FF0000'),(1,9447,1,'784','FF0000'),(1,9448,1,'785','FF0000'),(1,9449,1,'786','FF0000'),(1,9450,1,'787','FF0000'),(1,9451,1,'788','FF0000'),(1,9452,5,'790','FF0000'),(1,9453,1,'791','FF0000'),(1,9454,1,'792','FF0000'),(1,9455,1,'793','FF0000'),(1,9456,1,'794','FF0000'),(1,9457,1,'795','FF0000'),(1,9458,1,'796','FF0000'),(1,9459,1,'797','FF0000'),(1,9461,1,'800','FF0000'),(1,9462,1,'801','FF0000'),(1,9463,1,'803','FF0000'),(1,9464,1,'804','FF0000'),(1,9465,1,'805','FF0000'),(1,9466,1,'806','FF0000'),(1,9467,1,'807','FF0000'),(1,9468,1,'808','FF0000'),(1,9469,1,'809','FF0000'),(1,9470,1,'810','FF0000'),(1,9471,1,'811','FF0000'),(1,9472,5,'812','FF0000'),(1,9475,1,'814','FF0000'),(1,9476,1,'815','FF0000'),(1,9477,1,'816','FF0000'),(1,9479,1,'817','FF0000'),(1,9480,1,'818','FF0000'),(1,9481,1,'819','FF0000'),(1,9482,1,'820','FF0000'),(1,9483,1,'821','FF0000'),(1,9486,1,'823','FF0000'),(1,9487,1,'824','FF0000'),(1,9488,1,'825','FF0000'),(1,9489,1,'826','FF0000'),(1,9490,1,'827','FF0000'),(1,9491,1,'828','FF0000'),(1,9492,1,'829','FF0000'),(1,9493,1,'830','FF0000'),(1,9494,1,'831','FF0000'),(1,9495,1,'832','FF0000'),(1,9496,1,'833','FF0000'),(1,9497,1,'834','FF0000'),(1,9498,1,'835','FF0000'),(1,9499,1,'836','FF0000'),(1,9500,1,'838','FF0000'),(1,9501,1,'839','FF0000'),(1,9502,1,'840','FF0000'),(1,9503,1,'841','FF0000'),(1,9504,1,'842','FF0000'),(1,9505,1,'843','FF0000'),(1,9506,1,'845','FF0000'),(1,9507,1,'846','FF0000'),(1,9508,1,'848','FF0000'),(1,9509,1,'849','FF0000'),(1,9510,1,'850','FF0000'),(1,9511,1,'851','FF0000'),(1,9512,1,'853','FF0000'),(1,9513,5,'854','FF0000'),(1,9514,1,'855','FF0000'),(1,9515,1,'856','FF0000'),(1,9516,1,'857','FF0000'),(1,9517,1,'858','FF0000'),(1,9518,1,'859','FF0000'),(1,9519,1,'860','FF0000'),(1,9520,1,'861','FF0000'),(1,9521,1,'862','FF0000'),(1,9522,1,'863','FF0000'),(1,9523,1,'864','FF0000'),(1,9524,1,'865','FF0000'),(1,9525,1,'866','FF0000'),(1,9526,1,'867','FF0000'),(1,9527,1,'868','FF0000'),(1,9528,5,'869','FF0000'),(1,9529,1,'870','FF0000'),(1,9530,1,'871','FF0000'),(1,9531,1,'872','FF0000'),(1,9532,1,'873','FF0000'),(1,9533,1,'874','FF0000'),(1,9534,1,'875','FF0000'),(1,9535,1,'876','FF0000'),(1,9536,1,'877','FF0000'),(1,9537,1,'878','FF0000'),(1,9538,1,'879','FF0000'),(1,9541,1,'881','FF0000'),(1,9542,1,'882','FF0000'),(1,9544,1,'884','FF0000'),(1,9545,1,'885','FF0000'),(1,9546,1,'886','FF0000'),(1,9547,1,'888','FF0000'),(1,9548,1,'889','FF0000'),(1,9551,1,'891','FF0000'),(1,9552,1,'892','FF0000'),(1,9553,1,'893','FF0000'),(1,9554,1,'894','FF0000'),(1,9555,1,'895','FF0000'),(1,9556,1,'896','FF0000'),(1,9557,1,'897','FF0000'),(1,9560,1,'900','FF0000'),(1,9561,1,'901','FF0000'),(1,9562,1,'902','FF0000'),(1,9563,1,'903','FF0000'),(1,9564,1,'904','FF0000'),(1,9565,1,'905','FF0000'),(1,9566,1,'906','FF0000'),(1,9567,1,'907','FF0000'),(1,9568,1,'908','FF0000'),(1,9569,1,'909','FF0000'),(1,9570,1,'910','FF0000'),(1,9571,1,'911','FF0000'),(1,9572,1,'912','FF0000'),(1,9573,5,'913','FF0000'),(1,9574,1,'914','FF0000'),(1,9575,1,'915','FF0000'),(1,9576,1,'916','FF0000'),(1,9577,1,'917','FF0000'),(1,9578,5,'918','FF0000'),(1,9579,5,'919','FF0000'),(1,9580,1,'920','FF0000'),(1,9581,5,'921','FF0000'),(1,9582,5,'922','FF0000'),(1,9583,1,'923','FF0000'),(1,9584,1,'924','FF0000'),(1,9585,1,'925','FF0000'),(1,9586,1,'926','FF0000'),(1,9587,1,'927','FF0000'),(1,9588,1,'928','FF0000'),(1,9589,1,'929','FF0000'),(1,9590,1,'930','FF0000'),(1,9591,1,'931','FF0000'),(1,9594,1,'933','FF0000'),(1,9595,1,'934','FF0000'),(1,9596,1,'935','FF0000'),(1,9597,1,'936','FF0000'),(1,9598,1,'937','FF0000'),(1,9599,1,'938','FF0000'),(1,9600,1,'939','FF0000'),(1,9601,1,'940','FF0000'),(1,9602,1,'941','FF0000'),(1,9603,1,'942','FF0000'),(1,9604,1,'943','FF0000'),(1,9605,5,'944','FF0000'),(1,9606,1,'945','FF0000'),(1,9607,5,'946','FF0000'),(1,9608,1,'947','FF0000'),(1,9609,1,'948','FF0000'),(1,9612,1,'950','FF0000'),(1,9613,1,'951','FF0000'),(1,9614,1,'952','FF0000'),(1,9615,1,'953','FF0000'),(1,9616,1,'954','FF0000'),(1,9617,1,'955','FF0000'),(1,9618,1,'956','FF0000'),(1,9621,1,'958','FF0000'),(1,9622,1,'959','FF0000'),(1,9623,1,'960','FF0000'),(1,9624,1,'961','FF0000'),(1,9625,1,'962','FF0000'),(1,9626,1,'963','FF0000'),(1,9627,1,'964','FF0000'),(1,9628,1,'965','FF0000'),(1,9629,1,'966','FF0000'),(1,9630,1,'967','FF0000'),(1,9631,1,'968','FF0000'),(1,9632,1,'969','FF0000'),(1,9633,1,'970','FF0000'),(1,9634,1,'971','FF0000'),(1,9635,1,'972','FF0000'),(1,9636,1,'973','FF0000'),(1,9637,1,'974','FF0000'),(1,9638,1,'975','FF0000'),(1,9639,1,'976','FF0000'),(1,9642,1,'978','FF0000'),(1,9643,1,'979','FF0000'),(1,9644,1,'980','FF0000'),(1,9645,1,'981','FF0000'),(1,9646,1,'982','FF0000'),(1,9647,1,'983','FF0000'),(1,9648,1,'984','FF0000'),(1,9649,1,'985','FF0000'),(1,9650,1,'986','FF0000'),(1,9651,1,'987','FF0000'),(1,9652,1,'988','FF0000'),(1,9653,1,'989','FF0000'),(1,9654,5,'990','FF0000'),(1,9655,1,'991','FF0000'),(1,9656,1,'992','FF0000'),(1,9657,1,'993','FF0000'),(1,9658,1,'994','FF0000'),(1,9659,1,'995','FF0000'),(1,9660,1,'996','FF0000'),(1,9661,1,'997','FF0000'),(1,9662,1,'998','FF0000'),(1,9666,1,'1001','FF0000'),(1,9667,1,'1002','FF0000'),(1,9670,1,'1004','FF0000'),(1,9671,1,'1005','FF0000'),(1,9674,1,'1007','FF0000'),(1,9675,1,'1008','FF0000'),(1,9676,1,'1009','FF0000'),(1,9677,1,'1010','FF0000'),(1,9678,1,'1011','FF0000'),(1,9679,1,'1013','FF0000'),(1,9681,1,'1015','FF0000'),(1,9682,1,'1016','FF0000'),(1,9683,1,'1017','FF0000'),(1,9684,1,'1018','FF0000'),(1,9685,1,'1019','FF0000'),(1,9686,1,'1020','FF0000'),(1,9687,1,'1021','FF0000'),(1,9688,1,'1022','FF0000'),(1,9689,1,'1023','FF0000'),(1,9690,1,'1024','FF0000'),(1,9691,1,'1025','FF0000'),(1,9693,1,'1026','FF0000'),(1,9694,1,'1027','FF0000'),(1,9695,1,'1028','FF0000'),(1,9696,1,'1029','FF0000'),(1,9697,1,'1030','FF0000'),(1,9698,1,'1031','FF0000'),(1,9700,1,'1032','FF0000'),(1,9701,1,'1033','FF0000'),(1,9704,1,'1035','FF0000'),(1,9706,1,'1037','FF0000'),(1,9707,1,'1038','FF0000'),(1,9708,1,'1039','FF0000'),(1,9709,1,'1040','FF0000'),(1,9710,1,'1041','FF0000'),(1,9711,1,'1042','FF0000'),(1,9712,1,'1043','FF0000'),(1,9713,1,'1044','FF0000'),(1,9716,1,'1046','FF0000'),(1,9717,1,'1047','FF0000'),(1,9718,1,'1048','FF0000'),(1,9719,1,'1049','FF0000'),(1,9720,1,'1050','FF0000'),(1,9722,1,'1053','FF0000'),(1,9723,1,'1054','FF0000'),(1,9724,1,'1055','FF0000'),(1,9725,1,'1056','FF0000'),(1,9727,1,'1057','FF0000'),(1,9728,1,'1058','FF0000'),(1,9729,1,'1059','FF0000'),(1,9730,1,'1060','FF0000'),(1,9733,1,'1062','FF0000'),(1,9734,1,'1063','FF0000'),(1,9735,1,'1064','FF0000'),(1,9736,5,'1065','FF0000'),(1,9738,1,'1067','FF0000'),(1,9739,1,'1068','FF0000'),(1,9740,1,'1069','FF0000'),(1,9741,1,'1070','FF0000'),(1,9742,1,'1071','FF0000'),(1,9743,1,'1072','FF0000'),(1,9744,1,'1073','FF0000'),(1,9745,1,'1074','FF0000'),(1,9746,1,'1075','FF0000'),(1,9747,1,'1076','FF0000'),(1,9748,1,'1077','FF0000'),(1,9752,1,'1080','FF0000'),(1,9753,1,'1081','FF0000'),(1,9754,1,'1082','FF0000'),(1,9755,1,'1084','FF0000'),(1,9756,1,'1085','FF0000'),(1,9757,1,'1086','FF0000'),(1,9758,1,'1087','FF0000'),(1,9759,1,'1088','FF0000'),(1,9762,1,'1090','FF0000'),(1,9763,1,'1091','FF0000'),(1,9764,1,'1092','FF0000'),(1,9765,1,'1093','FF0000'),(1,9766,1,'1094','FF0000'),(1,9767,1,'1095','FF0000'),(1,9769,1,'1096','FF0000'),(1,9770,1,'1097','FF0000'),(1,9771,1,'1098','FF0000'),(1,9772,1,'1099','FF0000'),(1,9773,1,'1100','FF0000'),(1,9774,1,'1101','FF0000'),(1,9775,1,'1101','FF0000'),(1,9776,1,'1102','FF0000'),(1,9777,1,'1103','FF0000'),(1,9778,1,'1104','FF0000'),(1,9779,1,'1105','FF0000'),(1,9780,1,'1106','FF0000'),(1,9781,1,'1107','FF0000'),(1,9782,1,'1108','FF0000'),(1,9783,1,'1109','FF0000'),(1,9784,1,'1110','FF0000'),(1,9785,5,'1111','FF0000'),(1,9786,1,'1112','FF0000'),(1,9787,1,'1113','FF0000'),(1,9788,1,'1114','FF0000'),(1,9789,1,'1115','FF0000'),(1,9790,1,'1116','FF0000'),(1,9791,1,'1117','FF0000'),(1,9792,1,'1118','FF0000'),(1,9793,1,'1119','FF0000'),(1,9794,1,'1120','FF0000'),(1,9795,1,'1121','FF0000'),(1,9796,5,'1122','FF0000'),(1,9797,1,'1123','FF0000'),(1,9798,1,'1124','FF0000'),(1,9799,1,'1125','FF0000'),(1,9801,1,'1126','FF0000'),(1,9802,1,'1127','FF0000'),(1,9803,1,'1128','FF0000'),(1,9804,1,'1129','FF0000'),(1,9805,1,'1130','FF0000'),(1,9806,1,'1131','FF0000'),(1,9807,1,'1132','FF0000'),(1,9808,1,'1133','FF0000'),(1,9809,1,'1134','FF0000'),(1,9810,1,'1134','FF0000'),(1,9811,1,'1136','FF0000'),(1,9812,1,'1137','FF0000'),(1,9813,1,'1138','FF0000'),(1,9814,1,'1139','FF0000'),(1,9815,1,'1140','FF0000'),(1,9816,1,'1141','FF0000'),(1,9817,1,'1142','FF0000'),(1,9818,1,'1143','FF0000'),(1,9819,1,'1144','FF0000'),(1,9820,1,'1145','FF0000'),(1,9821,1,'1146','FF0000'),(1,9822,1,'1147','FF0000'),(1,9823,1,'1148','FF0000'),(1,9824,1,'1149','FF0000'),(1,9825,1,'1150','FF0000'),(1,9826,1,'1151','FF0000'),(1,9827,1,'1152','FF0000'),(1,9828,1,'1153','FF0000'),(1,9829,1,'1154','FF0000'),(1,9831,1,'1155','FF0000'),(1,9832,1,'1156','FF0000'),(1,9833,1,'1157','FF0000'),(1,9834,1,'1158','FF0000'),(1,9835,1,'1159','FF0000'),(1,9836,1,'1160','FF0000'),(1,9837,1,'1161','FF0000'),(1,9838,1,'1162','FF0000'),(1,9839,1,'1163','FF0000'),(1,9840,1,'1164','FF0000'),(1,9841,1,'1165','FF0000'),(1,9842,1,'1166','FF0000'),(1,9843,1,'1167','FF0000'),(1,9844,1,'1168','FF0000'),(1,9845,1,'1169','FF0000'),(1,9846,1,'1170','FF0000'),(1,9847,1,'1171','FF0000'),(1,9848,1,'1172','FF0000'),(1,9849,1,'1174','FF0000'),(1,9850,1,'1175','FF0000'),(1,9851,1,'1176','FF0000'),(1,9852,1,'1177','FF0000'),(1,9853,1,'1178','FF0000'),(1,9854,1,'1179','FF0000'),(1,9855,1,'1180','FF0000'),(1,9856,1,'1181','FF0000'),(1,9857,1,'1182','FF0000'),(1,9858,1,'1183','FF0000'),(1,9859,1,'1184','FF0000'),(1,9860,1,'1185','FF0000'),(1,9861,1,'1186','FF0000'),(1,9862,1,'1187','FF0000'),(1,9863,1,'1189','FF0000'),(1,9864,1,'1190','FF0000'),(1,9865,1,'1191','FF0000'),(1,9868,1,'1192','FF0000'),(1,9869,1,'1193','FF0000'),(1,9870,1,'1194','FF0000'),(1,9871,5,'1195','FF0000'),(1,9872,5,'1197','FF0000'),(1,9873,1,'1198','FF0000'),(1,9874,1,'1199','FF0000'),(1,9875,1,'1200','FF0000'),(1,9876,1,'1201','FF0000'),(1,9877,1,'1202','FF0000'),(1,9880,1,'1204','FF0000'),(1,9881,1,'1205','FF0000'),(1,9882,1,'1206','FF0000'),(1,9885,1,'1208','FF0000'),(1,9886,1,'1209','FF0000'),(1,9887,1,'1210','FF0000'),(1,9888,1,'1211','FF0000'),(1,9889,1,'1212','FF0000'),(1,9890,1,'1213','FF0000'),(1,9891,1,'1214','FF0000'),(1,9892,1,'1215','FF0000'),(1,9893,1,'1216','FF0000'),(1,9894,1,'1217','FF0000'),(1,9896,1,'1219','FF0000'),(1,9897,1,'1220','FF0000'),(1,9898,1,'1221','FF0000'),(1,9899,1,'1222','FF0000'),(1,9900,1,'1224','FF0000'),(1,9901,1,'1225','FF0000'),(1,9902,1,'1226','FF0000'),(1,9903,1,'1227','FF0000'),(1,9904,1,'1228','FF0000'),(1,9905,1,'1229','FF0000'),(1,9906,1,'1230','FF0000'),(1,9907,5,'1231','FF0000'),(1,9908,1,'1232','FF0000'),(1,9909,1,'1233','FF0000'),(1,9910,1,'1234','FF0000'),(1,9911,5,'1235','FF0000'),(1,9912,1,'1236','FF0000'),(1,9913,1,'1237','FF0000'),(1,9914,1,'1238','FF0000'),(1,9915,1,'1239','FF0000'),(1,9916,1,'1240','FF0000'),(1,9918,1,'1242','FF0000'),(1,9919,1,'1243','FF0000'),(1,9920,1,'1244','FF0000'),(1,9921,1,'1245','FF0000'),(1,9922,5,'1246','FF0000'),(1,9923,1,'1247','FF0000'),(1,9924,1,'1248','FF0000'),(1,9925,1,'1250','FF0000'),(1,9928,1,'1252','FF0000'),(1,9929,1,'1253','FF0000'),(1,9930,1,'1254','FF0000'),(1,9931,1,'1255','FF0000'),(1,9932,1,'1256','FF0000'),(1,9933,1,'1257','FF0000'),(1,9934,1,'1258','FF0000'),(1,9937,1,'1260','FF0000'),(1,9938,1,'1262','FF0000'),(1,9939,5,'1263','FF0000'),(1,9940,1,'1264','FF0000'),(1,9941,1,'1265','FF0000'),(1,9942,1,'1266','FF0000'),(1,9943,1,'1267','FF0000'),(1,9944,1,'1268','FF0000'),(1,9945,1,'1269','FF0000'),(1,9946,1,'1270','FF0000'),(1,9947,1,'1271','FF0000'),(1,9948,1,'1272','FF0000'),(1,9949,1,'1273','FF0000'),(1,9950,1,'1274','FF0000'),(1,9951,1,'1275','FF0000'),(1,9952,1,'1276','FF0000'),(1,9953,1,'1277','FF0000'),(1,9954,1,'1278','FF0000'),(1,9955,1,'1279','FF0000'),(1,9956,1,'1280','FF0000'),(1,9959,1,'1282','FF0000'),(1,9960,1,'1283','FF0000'),(1,9961,1,'1284','FF0000'),(1,9962,1,'1285','FF0000'),(1,9963,1,'1286','FF0000'),(1,9964,1,'1287','FF0000'),(1,9965,1,'1288','FF0000'),(1,9966,1,'1290','FF0000'),(1,9967,1,'1291','FF0000'),(1,9968,1,'1292','FF0000'),(1,9969,1,'1293','FF0000'),(1,9970,5,'1294','FF0000'),(1,9971,1,'1295','FF0000'),(1,9972,1,'1297','FF0000'),(1,9973,1,'1298','FF0000'),(1,9974,1,'1299','FF0000'),(1,9975,1,'1300','FF0000'),(1,9976,1,'1301','FF0000'),(1,9977,1,'1302','FF0000'),(1,9978,1,'1303','FF0000'),(1,9979,1,'1304','FF0000'),(1,9980,1,'1305','FF0000'),(1,9981,1,'1306','FF0000'),(1,9982,1,'1307','FF0000'),(1,9983,1,'1308','FF0000'),(1,9984,1,'1309','FF0000'),(1,9985,1,'1310','FF0000'),(1,9986,1,'1311','FF0000'),(1,9987,1,'1312','FF0000'),(1,9988,1,'1314','FF0000'),(1,9989,1,'1315','FF0000'),(1,9990,1,'1316','FF0000'),(1,9991,1,'1317','FF0000'),(1,9992,1,'1318','FF0000'),(1,9993,1,'1319','FF0000'),(1,9994,1,'1320','FF0000'),(1,9995,1,'1321','FF0000'),(1,9996,1,'1322','FF0000'),(1,9997,1,'1323','FF0000'),(1,9998,1,'1324','FF0000'),(1,9999,1,'1325','FF0000'),(1,10000,1,'1326','FF0000'),(1,10001,1,'1327','FF0000'),(1,10002,1,'1328','FF0000'),(1,10003,1,'1329','FF0000'),(1,10004,1,'1330','FF0000'),(1,10005,1,'1331','FF0000'),(1,10006,1,'1332','FF0000'),(1,10007,1,'1333','FF0000'),(1,10008,1,'1334','FF0000'),(1,10009,1,'1335','FF0000'),(1,10010,1,'1336','FF0000'),(1,10011,1,'1337','FF0000'),(1,10012,1,'1338','FF0000'),(1,10013,1,'1339','FF0000'),(1,10014,1,'1340','FF0000'),(1,10015,1,'1341','FF0000'),(1,10016,1,'1342','FF0000'),(1,10017,1,'1343','FF0000'),(1,10018,1,'1344','FF0000'),(1,10019,1,'1345','FF0000'),(1,10020,1,'1346','FF0000'),(1,10021,1,'1347','FF0000'),(1,10022,1,'1348','FF0000'),(1,10023,1,'1349','FF0000'),(1,10024,1,'1350','FF0000'),(1,10025,1,'1351','FF0000'),(1,10026,1,'1352','FF0000'),(1,10027,1,'1354','FF0000'),(1,10028,1,'1355','FF0000'),(1,10029,1,'1356','FF0000'),(1,10030,1,'1357','FF0000'),(1,10031,1,'1358','FF0000'),(1,10032,1,'1359','FF0000'),(1,10033,1,'1360','FF0000'),(1,10034,1,'1361','FF0000'),(1,10035,5,'1362','FF0000'),(1,10036,1,'1363','FF0000'),(1,10037,1,'1364','FF0000'),(1,10038,1,'1365','FF0000'),(1,10039,1,'1366','FF0000'),(1,10040,1,'1367','FF0000'),(1,10041,1,'1368','FF0000'),(1,10042,1,'1369','FF0000'),(1,10043,1,'1370','FF0000'),(1,10044,1,'1371','FF0000'),(1,10045,1,'1372','FF0000'),(1,10046,1,'1373','FF0000'),(1,10047,1,'1374','FF0000'),(1,10048,1,'1375','FF0000'),(1,10049,1,'1376','FF0000'),(1,10050,1,'1377','FF0000'),(1,10051,1,'1378','FF0000'),(1,10052,1,'1379','FF0000'),(1,10053,1,'1380','FF0000'),(1,10054,1,'1381','FF0000'),(1,10055,1,'1382','FF0000'),(1,10056,1,'1384','FF0000'),(1,10057,1,'1385','FF0000'),(1,10058,1,'1386','FF0000'),(1,10059,1,'1387','FF0000'),(1,10060,1,'1388','FF0000'),(1,10061,1,'1389','FF0000'),(1,10062,1,'1390','FF0000'),(1,10063,1,'1391','FF0000'),(1,10064,1,'1393','FF0000'),(1,10065,1,'1394','FF0000'),(1,10066,1,'1396','FF0000'),(1,10067,1,'1397','FF0000'),(1,10069,1,'1399','FF0000'),(1,10070,5,'1400','FF0000'),(1,10071,1,'1401','FF0000'),(1,10072,1,'1402','FF0000'),(1,10073,1,'1403','FF0000'),(1,10074,1,'1404','FF0000'),(1,10075,5,'1405','FF0000'),(1,10076,1,'1406','FF0000'),(1,10077,1,'1407','FF0000'),(1,10078,1,'1408','FF0000'),(1,10079,1,'1409','FF0000'),(1,10080,1,'1410','FF0000'),(1,10081,1,'1411','FF0000'),(1,10082,1,'1412','FF0000'),(1,10083,1,'1413','FF0000'),(1,10084,1,'1414','FF0000'),(1,10085,1,'1415','FF0000'),(1,10086,1,'1416','FF0000'),(1,10087,1,'1417','FF0000'),(1,10088,1,'1418','FF0000'),(1,10089,1,'1419','FF0000'),(1,10090,1,'1420','FF0000'),(1,10091,1,'1421','FF0000'),(1,10092,1,'1422','FF0000'),(1,10093,1,'1423','FF0000'),(1,10094,1,'1424','FF0000'),(1,10095,1,'1425','FF0000'),(1,10096,1,'1426','FF0000'),(1,10097,1,'1427','FF0000'),(1,10098,1,'1428','FF0000'),(1,10099,1,'1429','FF0000'),(1,10100,5,'1430','FF0000'),(1,10101,1,'1431','FF0000'),(1,10102,3,'1432','FF0000'),(1,10103,1,'1433','FF0000'),(1,10104,1,'1434','FF0000'),(1,10105,1,'1435','FF0000'),(1,10106,1,'1436','FF0000'),(1,10107,1,'1437','FF0000'),(1,10108,1,'1438','FF0000'),(1,10109,1,'1439','FF0000'),(1,10110,1,'1441','FF0000'),(1,10111,1,'1442','FF0000'),(1,10112,1,'1443','FF0000'),(1,10113,1,'1444','FF0000'),(1,10114,1,'1445','FF0000'),(1,10115,1,'1446','FF0000'),(1,10116,1,'1447','FF0000'),(1,10117,1,'1448','FF0000'),(1,10118,1,'1449','FF0000'),(1,10119,1,'1449','FF0000'),(1,10120,1,'1450','FF0000'),(1,10121,1,'1451','FF0000'),(1,10122,1,'1452','FF0000'),(1,10123,1,'1453','FF0000'),(1,10124,5,'1454','FF0000'),(1,10125,1,'1455','FF0000'),(1,10126,1,'1456','FF0000'),(1,10127,1,'1458','FF0000'),(1,10128,1,'1460','FF0000'),(1,10129,1,'1461','FF0000'),(1,10130,1,'1462','FF0000'),(1,10131,1,'1463','FF0000'),(1,10132,1,'1464','FF0000'),(1,10133,1,'1465','FF0000'),(1,10134,1,'1466','FF0000'),(1,10135,1,'1467','FF0000'),(1,10136,1,'1468','FF0000'),(1,10137,1,'1469','FF0000'),(1,10138,1,'1470','FF0000'),(1,10139,5,'1472','FF0000'),(1,10140,1,'1473','FF0000'),(1,10141,1,'1474','FF0000'),(1,10142,1,'1475','FF0000'),(1,10143,1,'1476','FF0000'),(1,10144,1,'1477','FF0000'),(1,10145,1,'1478','FF0000'),(1,10146,1,'1479','FF0000'),(1,10147,1,'1480','FF0000'),(1,10148,1,'1481','FF0000'),(1,10149,1,'1482','FF0000'),(1,10150,1,'1483','FF0000'),(1,10151,1,'1484','FF0000'),(1,10152,1,'1485','FF0000'),(1,10153,5,'1486','FF0000'),(1,10154,1,'1487','FF0000'),(1,10155,1,'1488','FF0000'),(1,10156,1,'1489','FF0000'),(1,10157,1,'1490','FF0000'),(1,10158,1,'1491','FF0000'),(1,10159,1,'1492','FF0000'),(1,10160,1,'1493','FF0000'),(1,10162,1,'1494','FF0000'),(1,10163,1,'1495','FF0000'),(1,10164,1,'1496','FF0000'),(1,10165,1,'1497','FF0000'),(1,10166,1,'1498','FF0000'),(1,10167,5,'1499','FF0000'),(1,10168,1,'1500','FF0000'),(1,10169,1,'1502','FF0000'),(1,10170,1,'1503','FF0000'),(1,10171,1,'1504','FF0000'),(1,10172,1,'1505','FF0000'),(1,10173,1,'1506','FF0000'),(1,10174,1,'1507','FF0000'),(1,10175,1,'1508','FF0000'),(1,10176,1,'1509','FF0000'),(1,10177,1,'1510','FF0000'),(1,10178,1,'1511','FF0000'),(1,10179,1,'1512','FF0000'),(1,10180,1,'1513','FF0000'),(1,10181,1,'1514','FF0000'),(1,10182,1,'1515','FF0000'),(1,10183,1,'1516','FF0000'),(1,10184,1,'1517','FF0000'),(1,10185,1,'1518','FF0000'),(1,10186,1,'1519','FF0000'),(1,10187,1,'1521','FF0000'),(1,10188,1,'1523','FF0000'),(1,10189,1,'1524','FF0000'),(1,10190,1,'1525','FF0000'),(1,10191,1,'1526','FF0000'),(1,10193,1,'1528','FF0000'),(1,10194,1,'1529','FF0000'),(1,10195,1,'1530','FF0000'),(1,10196,1,'1531','FF0000'),(1,10197,1,'1532','FF0000'),(1,10198,1,'1533','FF0000'),(1,10199,1,'1534','FF0000'),(1,10200,1,'1535','FF0000'),(1,10201,1,'1536','FF0000'),(1,10202,1,'1537','FF0000'),(1,10203,1,'1538','FF0000'),(1,10204,1,'1539','FF0000'),(1,10205,1,'1540','FF0000'),(1,10206,5,'1541','FF0000'),(1,10207,1,'1542','FF0000'),(1,10208,1,'1543','FF0000'),(1,10209,1,'1544','FF0000'),(1,10210,1,'1545','FF0000'),(1,10211,1,'1546','FF0000'),(1,10212,1,'1547','FF0000'),(1,10213,5,'1549','FF0000'),(1,10214,1,'1550','FF0000'),(1,10215,1,'1551','FF0000'),(1,10216,1,'1552','FF0000'),(1,10217,1,'1553','FF0000'),(1,10218,1,'1554','FF0000'),(1,10219,1,'1555','FF0000'),(1,10220,1,'1556','FF0000'),(1,10221,1,'1557','FF0000'),(1,10222,1,'1558','FF0000'),(1,10223,1,'1559','FF0000'),(1,10224,1,'1560','FF0000'),(1,10225,1,'1561','FF0000'),(1,10226,1,'1562','FF0000'),(1,10227,1,'1563','FF0000'),(1,10228,1,'1564','FF0000'),(1,10229,1,'1565','FF0000'),(1,10232,1,'1566','FF0000'),(1,10233,1,'1567','FF0000'),(1,10235,1,'1568','FF0000'),(1,10236,5,'1569','FF0000'),(1,10237,1,'1570','FF0000'),(1,10238,1,'1571','FF0000'),(1,10239,1,'1572','FF0000'),(1,10240,1,'1573','FF0000'),(1,10241,1,'1574','FF0000'),(1,10242,1,'1575','FF0000'),(1,10243,1,'1576','FF0000'),(1,10244,1,'1577','FF0000'),(1,10245,1,'1578','FF0000'),(1,10246,1,'1579','FF0000'),(1,10247,1,'1580','FF0000'),(1,10248,1,'1581','FF0000'),(1,10249,1,'1582','FF0000'),(1,10250,1,'1583','FF0000'),(1,10251,1,'1584','FF0000'),(1,10252,1,'1584','FF0000'),(1,10253,1,'1585','FF0000'),(1,10254,1,'1586','FF0000'),(1,10255,1,'1587','FF0000'),(1,10256,1,'1588','FF0000'),(1,10257,1,'1589','FF0000'),(1,10258,1,'1590','FF0000'),(1,10259,1,'1591','FF0000'),(1,10260,1,'1592','FF0000'),(1,10261,1,'1593','FF0000'),(1,10262,1,'1594','FF0000'),(1,10263,1,'1595','FF0000'),(1,10265,1,'1596','FF0000'),(1,10266,1,'1597','FF0000'),(1,10267,1,'1598','FF0000'),(1,10268,1,'1599','FF0000'),(1,10269,1,'1600','FF0000'),(1,10270,1,'1702','FF0000'),(1,10271,1,'1703','FF0000'),(1,10272,1,'1704','FF0000'),(1,10273,1,'1705','FF0000'),(1,10274,1,'1706','FF0000'),(1,10275,1,'1707','FF0000'),(1,10278,1,'1709','FF0000'),(1,10279,1,'1710','FF0000'),(1,10280,1,'1711','FF0000'),(1,10281,1,'1712','FF0000'),(1,10284,1,'1714','FF0000'),(1,10285,1,'1716','FF0000'),(1,10286,1,'1717','FF0000'),(1,10287,1,'1718','FF0000'),(1,10288,1,'1719','FF0000'),(1,10289,1,'1720','FF0000'),(1,10290,1,'1721','FF0000'),(1,10293,1,'1723','FF0000'),(1,10294,1,'1724','FF0000'),(1,10295,1,'1725','FF0000'),(1,10296,1,'1726','FF0000'),(1,10297,1,'1728','FF0000'),(1,10298,1,'1729','FF0000'),(1,10299,1,'1730','FF0000'),(1,10300,1,'1731','FF0000'),(1,10301,1,'1732','FF0000'),(1,10302,1,'1733','FF0000'),(1,10303,1,'1734','FF0000'),(1,10304,1,'1735','FF0000'),(1,10305,1,'1736','FF0000'),(1,10306,1,'1737','FF0000'),(1,10307,1,'1738','FF0000'),(1,10309,1,'1740','FF0000'),(1,10310,1,'1741','FF0000'),(1,10311,1,'1742','FF0000'),(1,10313,1,'1743','FF0000'),(1,10314,1,'1744','FF0000'),(1,10315,1,'1745','FF0000'),(1,10316,1,'1746','FF0000'),(1,10317,1,'1747','FF0000'),(1,10318,1,'1748','FF0000'),(1,10319,1,'1749','FF0000'),(1,10320,1,'1750','FF0000'),(1,10322,1,'1752','FF0000'),(1,10323,1,'1753','FF0000'),(1,10324,1,'1754','FF0000'),(1,10325,1,'1755','FF0000'),(1,10326,1,'1756','FF0000'),(1,10327,1,'1757','FF0000'),(1,10328,1,'1759','FF0000'),(1,10331,1,'1760','FF0000'),(1,10332,1,'1762','FF0000'),(1,10333,1,'1763','FF0000'),(1,10334,1,'1764','FF0000'),(1,10338,1,'1766','FF0000'),(1,10339,1,'1767','FF0000'),(1,10344,1,'1768','FF0000'),(1,10345,1,'1769','FF0000'),(1,10346,1,'1770','FF0000'),(1,10348,1,'1772','FF0000'),(1,10349,1,'1773','FF0000'),(1,10356,1,'1777','FF0000'),(1,10357,1,'1778','FF0000'),(1,10358,1,'1779','FF0000'),(1,10359,1,'1780','FF0000'),(1,10360,1,'1781','FF0000'),(1,10361,1,'1782','FF0000'),(1,10362,1,'1783','FF0000'),(1,10363,1,'1784','FF0000'),(1,10365,1,'1786','FF0000'),(1,10366,1,'1787','FF0000'),(1,10367,5,'1788','FF0000'),(1,10368,5,'1789','FF0000'),(1,10369,1,'1790','FF0000'),(1,10370,1,'1791','FF0000'),(1,10371,1,'1792','FF0000'),(1,10372,1,'1793','FF0000'),(1,10373,1,'1795','FF0000'),(1,10374,1,'1796','FF0000'),(1,10375,1,'1797','FF0000'),(1,10376,1,'1798','FF0000'),(1,10377,1,'1799','FF0000'),(1,10378,1,'1800','FF0000'),(1,10379,1,'1900','FF0000'),(1,10380,1,'1901','FF0000'),(1,10381,1,'1902','FF0000'),(1,10382,1,'1903','FF0000'),(1,10383,1,'1904','FF0000'),(1,10384,1,'1906','FF0000'),(1,10385,1,'1907','FF0000'),(1,10386,1,'1908','FF0000'),(1,10387,1,'1910','FF0000'),(1,10388,1,'1911','FF0000'),(1,10389,1,'1912','FF0000'),(1,10390,1,'1913','FF0000'),(1,10391,1,'1914','FF0000'),(1,10392,1,'1915','FF0000'),(1,10393,1,'1916','FF0000'),(1,10394,1,'1917','FF0000'),(1,10395,5,'1918','FF0000'),(1,10396,1,'1919','FF0000'),(1,10397,1,'1920','FF0000'),(1,10398,1,'1921','FF0000'),(1,10399,1,'1922','FF0000'),(1,10400,5,'1923','FF0000'),(1,10401,1,'1924','FF0000'),(1,10402,1,'1925','FF0000'),(1,10403,1,'1926','FF0000'),(1,10404,1,'1926','FF0000'),(1,10405,1,'1927','FF0000'),(1,10407,1,'1928','FF0000'),(1,10408,1,'1929','FF0000'),(1,10409,1,'1930','FF0000'),(1,10410,1,'1931','FF0000'),(1,10411,1,'1932','FF0000'),(1,10412,1,'1933','FF0000'),(1,10413,1,'1934','FF0000'),(1,10414,1,'1935','FF0000'),(1,10415,1,'1936','FF0000'),(1,10416,1,'1937','FF0000'),(1,10417,1,'1938','FF0000'),(1,10422,1,'1942','FF0000'),(1,10423,5,'1943','FF0000'),(1,10424,1,'1943','FF0000'),(1,10426,1,'1944','FF0000'),(1,10427,1,'1946','FF0000'),(1,10428,1,'1947','FF0000'),(1,10429,1,'1948','FF0000'),(1,10430,1,'1949','FF0000'),(1,10431,1,'1950','FF0000'),(1,10432,1,'1952','FF0000'),(1,10433,1,'1953','FF0000'),(1,10434,5,'1954','FF0000'),(1,10435,1,'1955','FF0000'),(1,10436,1,'1956','FF0000'),(1,10437,1,'1957','FF0000'),(1,10438,1,'1958','FF0000'),(1,10439,1,'1959','FF0000'),(1,10440,1,'1960','FF0000'),(1,10441,1,'1961','FF0000'),(1,10442,1,'1962','FF0000'),(1,10443,1,'1963','FF0000'),(1,10444,1,'1964','FF0000'),(1,10445,1,'1965','FF0000'),(1,10446,1,'1966','FF0000'),(1,10447,1,'1967','FF0000'),(1,10448,1,'1968','FF0000'),(1,10449,1,'1969','FF0000'),(1,10450,1,'1970','FF0000'),(1,10451,1,'1972','FF0000'),(1,10452,1,'1973','FF0000'),(1,10453,5,'1974','FF0000'),(1,10454,5,'1975','FF0000'),(1,10455,1,'1976','FF0000'),(1,10456,1,'1977','FF0000'),(1,10457,5,'1978','FF0000'),(1,10458,1,'1979','FF0000'),(1,10459,1,'1981','FF0000'),(1,10460,1,'1982','FF0000'),(1,10461,1,'1983','FF0000'),(1,10462,1,'1984','FF0000'),(1,10463,1,'1985','FF0000'),(1,10464,1,'1986','FF0000'),(1,10465,1,'1987','FF0000'),(1,10466,1,'1988','FF0000'),(1,10467,1,'1989','FF0000'),(1,10468,1,'1990','FF0000'),(1,10469,5,'1991','FF0000'),(1,10470,5,'1992','FF0000'),(1,10472,1,'1993','FF0000'),(1,10473,5,'1994','FF0000'),(1,10474,1,'1995','FF0000'),(1,10475,1,'1996','FF0000'),(1,10476,1,'1997','FF0000'),(1,10477,1,'1998','FF0000'),(1,10478,1,'1999','FF0000'),(1,10479,1,'152','FF0000'),(1,10480,2,'115','FF0000'),(1,10481,1,'1812','FF0000'),(1,10482,2,'143','FF0000'),(1,10483,2,'163','FF0000'),(1,10484,2,'189','FF0000'),(1,10485,1,'186','FF0000'),(1,10486,1,'130','FF0000'),(1,10487,1,'92','FF0000'),(1,10488,1,'30','FF0000'),(1,10489,1,'39','FF0000'),(1,10490,1,'48','FF0000'),(1,10491,1,'47','FF0000'),(1,10492,2,'182','FF0000'),(1,10493,2,'104','FF0000'),(1,10494,2,'188','FF0000'),(1,10495,2,'64','FF0000'),(1,10496,1,'126','FF0000'),(1,10497,2,'294','FF0000'),(1,10498,2,'142','FF0000'),(1,10499,2,'159','FF0000'),(1,10500,1,'196','FF0000'),(1,10501,2,'181','FF0000'),(1,10502,2,'169','FF0000'),(1,10503,1,'137','FF0000'),(1,10504,1,'174','FF0000'),(1,10505,2,'1809','FF0000'),(1,10506,2,'1856','FF0000'),(1,10507,6,'1898','FF0000'),(1,10508,2,'1881','FF0000'),(1,10509,1,'1841','FF0000'),(1,10510,2,'1847','FF0000'),(1,10511,2,'1877','FF0000'),(1,10512,2,'1828','FF0000'),(1,10513,2,'1879','FF0000'),(1,10514,1,'1837','FF0000'),(1,10515,2,'1888','FF0000'),(1,10516,2,'1870','FF0000'),(1,10517,2,'1868','FF0000'),(1,10518,1,'1815','FF0000'),(1,10519,5,'1834','FF0000'),(1,10520,5,'1895','FF0000'),(1,10521,1,'1899','FF0000'),(1,10522,2,'1864','FF0000'),(1,10523,1,'1835','FF0000'),(1,10524,1,'1874','FF0000'),(1,10525,2,'1833','FF0000'),(1,10526,2,'1859','FF0000'),(1,10527,2,'1854','FF0000'),(1,10528,6,'1873','FF0000'),(1,10529,2,'1830','FF0000'),(1,10530,1,'1861','FF0000'),(1,10531,2,'1876','FF0000'),(1,10532,2,'1860','FF0000'),(1,10533,6,'1850','FF0000'),(1,10534,2,'1890','FF0000'),(1,10535,2,'1813','FF0000'),(1,10536,2,'1819','FF0000'),(1,10537,2,'1869','FF0000'),(1,10538,2,'1832','FF0000'),(1,10539,2,'1872','FF0000'),(1,10540,2,'1829','FF0000'),(1,10541,1,'1811','FF0000'),(1,10542,1,'1897','FF0000'),(1,10543,1,'1803','FF0000'),(1,10544,5,'1804','FF0000'),(1,10545,5,'1805','FF0000'),(1,10546,2,'1820','FF0000'),(1,10547,2,'1816','FF0000'),(1,10548,2,'1808','FF0000'),(1,10549,1,'1844','FF0000'),(1,10550,1,'1855','FF0000'),(1,10551,1,'1818','FF0000'),(1,10552,2,'1852','FF0000'),(1,10555,2,'1843','FF0000'),(1,10556,2,'1893','FF0000'),(1,10557,2,'1857','FF0000'),(1,10558,1,'1817','FF0000'),(1,10559,5,'1822','FF0000'),(1,10560,1,'1840','FF0000'),(1,10561,2,'1883','FF0000'),(1,10562,1,'1875','FF0000'),(1,10563,2,'1889','FF0000'),(1,10564,2,'1849','FF0000'),(1,10565,1,'1807','FF0000'),(1,10566,1,'1845','FF0000'),(1,10567,1,'1858','FF0000'),(1,10568,2,'1851','FF0000'),(1,10569,2,'1814','FF0000'),(1,10570,2,'1886','FF0000'),(1,10571,1,'1882','FF0000'),(1,10572,2,'1880','FF0000'),(1,10573,2,'1810','FF0000'),(1,10574,2,'1863','FF0000'),(1,10575,6,'1853','FF0000'),(1,10576,1,'1896','FF0000'),(1,10577,1,'120','FF0000'),(1,10578,2,'1838','FF0000'),(1,10579,2,'1887','FF0000'),(1,10580,1,'1824','FF0000'),(1,10581,2,'1642','FF0000'),(1,10582,1,'1645','FF0000'),(1,10583,2,'1639','FF0000'),(1,10584,1,'1621','FF0000'),(1,10585,2,'1678','FF0000'),(1,10586,2,'1614','FF0000'),(1,10587,2,'1629','FF0000'),(1,10588,2,'1698','FF0000'),(1,10589,2,'1651','FF0000'),(1,10590,2,'1662','FF0000'),(1,10591,2,'1694','FF0000'),(1,10592,2,'1699','FF0000'),(1,10593,6,'1697','FF0000'),(1,10594,6,'1692','FF0000'),(1,10595,2,'1632','FF0000');
/*!40000 ALTER TABLE `users_badges_years` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_details`
--

DROP TABLE IF EXISTS `users_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users_details` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `club_id` int(11) NOT NULL DEFAULT '0',
  `agc_title_id` int(11) NOT NULL DEFAULT '0',
  `club_title_id` int(11) NOT NULL DEFAULT '0',
  `business_phone` varchar(255) NOT NULL,
  `home_phone` varchar(255) NOT NULL,
  `mobile_phone` varchar(255) NOT NULL,
  `fax_number` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state_id` int(11) NOT NULL DEFAULT '0',
  `country_id` varchar(255) NOT NULL,
  `zip` varchar(255) NOT NULL,
  `category` enum('Personal','Business','Family') DEFAULT NULL,
  `family_badge` int(11) NOT NULL DEFAULT '0',
  `current_badge_credits` int(11) NOT NULL DEFAULT '0',
  `last_years_badge_credits` int(11) NOT NULL DEFAULT '0',
  `payment` varchar(255) NOT NULL DEFAULT '0',
  `minutes` tinyint(4) NOT NULL DEFAULT '0',
  `legislative_alert` tinyint(4) NOT NULL DEFAULT '0',
  `notes` text NOT NULL,
  `code` int(11) NOT NULL DEFAULT '0',
  `trustee` tinyint(4) NOT NULL DEFAULT '0',
  `alternate` tinyint(4) NOT NULL DEFAULT '0',
  `attendee` tinyint(4) NOT NULL DEFAULT '0',
  `agc_office_id` int(11) NOT NULL DEFAULT '0',
  `agc_committee_id` int(11) NOT NULL DEFAULT '0',
  `affiliation` varchar(255) NOT NULL DEFAULT '0',
  `s_GUID` varchar(255) NOT NULL DEFAULT '0',
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `visits`
--

DROP TABLE IF EXISTS `visits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `visits` (
  `visit_id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `location` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `email_receivers_group` int(11) NOT NULL DEFAULT '0',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`visit_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18383 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `words_filter`
--

DROP TABLE IF EXISTS `words_filter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `words_filter` (
  `words_filter_id` int(11) NOT NULL AUTO_INCREMENT,
  `word` varchar(255) NOT NULL,
  `is_html_tag` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `display_order` int(11) NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`words_filter_id`)
) ENGINE=MyISAM AUTO_INCREMENT=50 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `words_filter`
--

LOCK TABLES `words_filter` WRITE;
/*!40000 ALTER TABLE `words_filter` DISABLE KEYS */;
INSERT INTO `words_filter` VALUES (1,'iframe',1,1,1,'2012-03-12 00:00:00','0000-00-00 00:00:00'),(2,'a',1,1,2,'2012-03-12 00:00:00','0000-00-00 00:00:00'),(3,'link',1,1,3,'2012-03-12 00:00:00','0000-00-00 00:00:00'),(4,'script',1,1,4,'2012-03-13 00:00:00','0000-00-00 00:00:00'),(5,'turd',0,1,5,'2013-01-08 00:00:00','0000-00-00 00:00:00'),(6,'shit',0,1,6,'2013-01-08 00:00:00','0000-00-00 00:00:00'),(7,'cunt',0,1,7,'2013-01-08 00:00:00','0000-00-00 00:00:00'),(8,'pussy',0,1,8,'2013-01-08 00:00:00','0000-00-00 00:00:00'),(9,'fuck',0,1,9,'2013-01-08 00:00:00','0000-00-00 00:00:00'),(10,'screw',0,1,10,'2013-01-08 00:00:00','0000-00-00 00:00:00'),(11,'kike',0,1,11,'2013-01-08 00:00:00','0000-00-00 00:00:00'),(12,'nigger',0,1,12,'2013-01-08 00:00:00','0000-00-00 00:00:00'),(13,'damn',0,1,13,'2013-01-08 00:00:00','0000-00-00 00:00:00'),(14,'motherfucker',0,1,14,'2013-01-08 00:00:00','0000-00-00 00:00:00'),(15,'bastard',0,1,15,'2013-01-08 00:00:00','0000-00-00 00:00:00'),(16,'Hitler',0,1,16,'2013-01-08 00:00:00','0000-00-00 00:00:00'),(17,'fag',0,1,17,'2013-01-08 00:00:00','0000-00-00 00:00:00'),(18,'friggin',0,1,18,'2013-01-08 00:00:00','0000-00-00 00:00:00'),(19,'frigging',0,1,19,'2013-01-08 00:00:00','0000-00-00 00:00:00'),(20,'ass',0,1,20,'2013-01-08 00:00:00','0000-00-00 00:00:00'),(21,'bullshit',0,1,21,'2013-01-08 00:00:00','0000-00-00 00:00:00'),(22,'bitch',0,1,22,'2013-01-09 00:00:00','0000-00-00 00:00:00'),(23,'slut',0,1,23,'2013-01-09 00:00:00','0000-00-00 00:00:00'),(24,'whore',0,1,24,'2013-01-09 00:00:00','0000-00-00 00:00:00'),(48,'shity',0,1,47,'2016-03-23 00:00:00','0000-00-00 00:00:00'),(46,'-1\' 1 [1@agc.agchq.net]',0,1,45,'2014-03-20 00:00:00','0000-00-00 00:00:00'),(47,'shitty',0,1,46,'2016-03-23 00:00:00','0000-00-00 00:00:00'),(36,'moron',0,1,35,'2013-01-16 00:00:00','0000-00-00 00:00:00'),(37,'morons',0,1,36,'2013-01-16 00:00:00','0000-00-00 00:00:00'),(38,'fucker',0,1,37,'2013-01-21 00:00:00','0000-00-00 00:00:00'),(39,'tit',0,1,38,'2013-01-21 00:00:00','0000-00-00 00:00:00'),(40,'tits',0,1,39,'2013-01-21 00:00:00','0000-00-00 00:00:00'),(41,'puppet',0,1,40,'2013-01-22 00:00:00','0000-00-00 00:00:00'),(42,'BS',0,1,41,'2013-08-04 00:00:00','0000-00-00 00:00:00'),(43,'bs',0,1,42,'2013-08-04 00:00:00','0000-00-00 00:00:00'),(44,'Hell',0,1,43,'2013-08-04 00:00:00','0000-00-00 00:00:00'),(45,'hell',0,1,44,'2013-08-04 00:00:00','0000-00-00 00:00:00'),(49,'mendenhall',0,1,48,'2016-07-13 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `words_filter` ENABLE KEYS */;

UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-06-19  8:50:27
