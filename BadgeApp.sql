/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `BadgeDB` /*!40100 DEFAULT CHARACTER SET utf8 */;

use BadgeDB;

--
-- Temporary view structure for view `Cart_Summary`
--

DROP TABLE IF EXISTS `Cart_Summary`;
/*!50001 DROP VIEW IF EXISTS `Cart_Summary`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `Cart_Summary` AS SELECT 
 1 AS `tx_date`,
 1 AS `cat`,
 1 AS `tx_type`,
 1 AS `csku`,
 1 AS `citem`,
 1 AS `ea`,
 1 AS `qty`,
 1 AS `cprice`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `badge_certification`
--

DROP TABLE IF EXISTS `badge_certification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `badge_certification` (
  `id` int NOT NULL AUTO_INCREMENT,
  `badge_number` int NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `sticker` varchar(255) NOT NULL,
  `certification_type` int NOT NULL,
  `status` enum('0','1','2') NOT NULL,
  `fee` float(8,2) NOT NULL,
  `discount` float(8,2) NOT NULL,
  `amount_due` float(8,2) NOT NULL,
  `is_migrated` enum('0','1') NOT NULL DEFAULT '0',
  `cc_x_id` varchar(48) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `badge_certification`
--

LOCK TABLES `badge_certification` WRITE;
/*!40000 ALTER TABLE `badge_certification` DISABLE KEYS */;
/*!40000 ALTER TABLE `badge_certification` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `badge_subscriptions`
--

DROP TABLE IF EXISTS `badge_subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `badge_subscriptions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `badge_number` int NOT NULL,
  `club_id` int DEFAULT NULL,
  `badge_year` int DEFAULT NULL,
  `payment_type` enum('cash','check','credit','online','other') NOT NULL,
  `status` varchar(12) NOT NULL,
  `sticker` varchar(10) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `badge_fee` float(8,2) DEFAULT NULL,
  `paid_amount` float(8,2) NOT NULL,
  `discount` float(8,2) NOT NULL,
  `transaction_type` enum('RENEW','NEW','CERT') DEFAULT NULL,
  `is_migrated` enum('0','1') DEFAULT '0',
  `cc_x_id` varchar(48) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `badge_to_year` (`badge_number`,`badge_year`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `badge_subscriptions`
--

LOCK TABLES `badge_subscriptions` WRITE;
/*!40000 ALTER TABLE `badge_subscriptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `badge_subscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `badge_subscriptions_date`
--

DROP TABLE IF EXISTS `badge_subscriptions_date`;
/*!50001 DROP VIEW IF EXISTS `badge_subscriptions_date`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `badge_subscriptions_date` AS SELECT 
 1 AS `id`,
 1 AS `badge_number`,
 1 AS `club_id`,
 1 AS `badge_year`,
 1 AS `payment_type`,
 1 AS `status`,
 1 AS `sticker`,
 1 AS `created_at`,
 1 AS `badge_fee`,
 1 AS `paid_amount`,
 1 AS `discount`,
 1 AS `transaction_type`,
 1 AS `is_migrated`,
 1 AS `cc_x_id`,
 1 AS `bs_c_date`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `badge_to_club`
--

DROP TABLE IF EXISTS `badge_to_club`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `badge_to_club` (
  `badge_number` int NOT NULL,
  `club_id` int NOT NULL,
  PRIMARY KEY (`badge_number`,`club_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `badge_to_club`
--

LOCK TABLES `badge_to_club` WRITE;
/*!40000 ALTER TABLE `badge_to_club` DISABLE KEYS */;
/*!40000 ALTER TABLE `badge_to_club` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `badge_to_role`
--

DROP TABLE IF EXISTS `badge_to_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `badge_to_role` (
  `badge_number` int NOT NULL,
  `role` int NOT NULL,
  `club` int NOT NULL,
  PRIMARY KEY (`badge_number`,`role`,`club`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `badge_to_role`
--

LOCK TABLES `badge_to_role` WRITE;
/*!40000 ALTER TABLE `badge_to_role` DISABLE KEYS */;
/*!40000 ALTER TABLE `badge_to_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `badges`
--

DROP TABLE IF EXISTS `badges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `badges` (
  `id` int NOT NULL AUTO_INCREMENT,
  `badge_number` int NOT NULL,
  `prefix` varchar(15) NOT NULL,
  `first_name` varchar(35) NOT NULL,
  `last_name` varchar(35) NOT NULL,
  `suffix` varchar(15) DEFAULT NULL,
  `address` text NOT NULL,
  `city` varchar(25) NOT NULL,
  `state` varchar(10) NOT NULL,
  `zip` varchar(10) NOT NULL,
  `gender` varchar(2) DEFAULT NULL,
  `yob` int DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `email_vrfy` tinyint(1) DEFAULT '0',
  `phone` varchar(25) NOT NULL,
  `phone_op` varchar(25) DEFAULT NULL,
  `ice_contact` varchar(40) DEFAULT NULL,
  `ice_phone` varchar(25) DEFAULT NULL,
  `mem_type` int NOT NULL,
  `primary` varchar(11) DEFAULT NULL,
  `incep` datetime NOT NULL,
  `qrcode` text,
  `wt_date` date NOT NULL,
  `wt_instru` varchar(255) DEFAULT NULL,
  `remarks` text NOT NULL,
  `status` varchar(10) DEFAULT NULL,
  `soft_delete` enum('0') DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `expires` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `badge_number` (`badge_number`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `badges`
--

LOCK TABLES `badges` WRITE;
/*!40000 ALTER TABLE `badges` DISABLE KEYS */;
/*!40000 ALTER TABLE `badges` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `bn_to_by`
--

DROP TABLE IF EXISTS `bn_to_by`;
/*!50001 DROP VIEW IF EXISTS `bn_to_by`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `bn_to_by` AS SELECT 
 1 AS `badge_number`,
 1 AS `badge_year`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `bn_to_cl`
--

DROP TABLE IF EXISTS `bn_to_cl`;
/*!50001 DROP VIEW IF EXISTS `bn_to_cl`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `bn_to_cl` AS SELECT 
 1 AS `badge_number`,
 1 AS `club_id`,
 1 AS `short_name`,
 1 AS `club_name`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `cc_receipts`
--

DROP TABLE IF EXISTS `cc_receipts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cc_receipts` (
  `id` varchar(48) NOT NULL DEFAULT '',
  `badge_number` int NOT NULL,
  `tx_date` datetime NOT NULL,
  `tx_type` varchar(10) NOT NULL,
  `status` varchar(15) DEFAULT NULL,
  `amount` decimal(8,2) NOT NULL,
  `tax` decimal(5,2) NOT NULL DEFAULT '0.00',
  `authCode` varchar(6) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `cardNum` varchar(22) DEFAULT NULL,
  `cardType` varchar(20) DEFAULT NULL,
  `expYear` int DEFAULT NULL,
  `expMonth` int DEFAULT NULL,
  `cashier` varchar(50) NOT NULL,
  `cashier_badge` int DEFAULT NULL,
  `on_qb` int NOT NULL DEFAULT '0',
  `cart` text NOT NULL,
  `guest_cred` int DEFAULT '0',
  PRIMARY KEY (`id`,`badge_number`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cc_receipts`
--

LOCK TABLES `cc_receipts` WRITE;
/*!40000 ALTER TABLE `cc_receipts` DISABLE KEYS */;
/*!40000 ALTER TABLE `cc_receipts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `cc_receipts_date`
--

DROP TABLE IF EXISTS `cc_receipts_date`;
/*!50001 DROP VIEW IF EXISTS `cc_receipts_date`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `cc_receipts_date` AS SELECT 
 1 AS `id`,
 1 AS `badge_number`,
 1 AS `tx_date`,
 1 AS `tx_type`,
 1 AS `status`,
 1 AS `amount`,
 1 AS `tax`,
 1 AS `authCode`,
 1 AS `name`,
 1 AS `cardNum`,
 1 AS `cardType`,
 1 AS `expYear`,
 1 AS `expMonth`,
 1 AS `cashier`,
 1 AS `cashier_badge`,
 1 AS `on_qb`,
 1 AS `cart`,
 1 AS `guest_cred`,
 1 AS `cc_c_date`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `clubs`
--

DROP TABLE IF EXISTS `clubs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clubs` (
  `club_id` int NOT NULL AUTO_INCREMENT,
  `club_name` varchar(255) NOT NULL,
  `short_name` varchar(20) NOT NULL,
  `poc_email` varchar(255) DEFAULT NULL,
  `status` int NOT NULL,
  `is_club` int NOT NULL DEFAULT '0',
  `allow_members` int NOT NULL DEFAULT '1',
  `avoid` varchar(100) DEFAULT '',
  PRIMARY KEY (`club_id`),
  UNIQUE KEY `club_id` (`club_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clubs`
--

LOCK TABLES `clubs` WRITE;
/*!40000 ALTER TABLE `clubs` DISABLE KEYS */;
/*!40000 ALTER TABLE `clubs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_attendee`
--

DROP TABLE IF EXISTS `event_attendee`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_attendee` (
  `ea_id` int NOT NULL AUTO_INCREMENT,
  `ea_event_id` int NOT NULL,
  `ea_badge` int DEFAULT NULL,
  `ea_f_name` varchar(45) DEFAULT NULL,
  `ea_l_name` varchar(45) DEFAULT NULL,
  `ea_wb_serial` varchar(10) DEFAULT NULL,
  `ea_wc_logged` int DEFAULT NULL,
  `ea_wb_out` int DEFAULT '1',
  PRIMARY KEY (`ea_id`),
  UNIQUE KEY `id_ev_at_UNIQUE` (`ea_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_attendee`
--

LOCK TABLES `event_attendee` WRITE;
/*!40000 ALTER TABLE `event_attendee` DISABLE KEYS */;
/*!40000 ALTER TABLE `event_attendee` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `events` (
  `e_id` int NOT NULL AUTO_INCREMENT,
  `e_name` varchar(60) NOT NULL,
  `e_date` date NOT NULL,
  `e_poc` int NOT NULL,
  `sponsor` int DEFAULT NULL,
  `e_status` varchar(45) NOT NULL,
  `e_type` varchar(45) NOT NULL,
  `e_hours` int DEFAULT NULL,
  `e_inst` varchar(255) DEFAULT NULL,
  `e_rso` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`e_id`),
  UNIQUE KEY `e_id_UNIQUE` (`e_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `guest`
--

DROP TABLE IF EXISTS `guest`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `guest` (
  `id` int NOT NULL AUTO_INCREMENT,
  `badge_number` int NOT NULL,
  `g_first_name` varchar(35) NOT NULL,
  `g_last_name` varchar(35) NOT NULL,
  `g_city` varchar(255) DEFAULT NULL,
  `g_state` varchar(2) DEFAULT NULL,
  `g_yob` int DEFAULT NULL,
  `g_paid` varchar(1) DEFAULT '0',
  `tmp_badge` int DEFAULT NULL,
  `time_in` datetime NOT NULL,
  `time_out` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `guest`
--

LOCK TABLES `guest` WRITE;
/*!40000 ALTER TABLE `guest` DISABLE KEYS */;
/*!40000 ALTER TABLE `guest` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login_access_badge`
--

DROP TABLE IF EXISTS `login_access_badge`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `login_access_badge` (
  `l_id` int NOT NULL AUTO_INCREMENT,
  `l_date` datetime DEFAULT NULL,
  `module` varchar(45) DEFAULT NULL,
  `l_name` varchar(80) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `l_status` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`l_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_access_badge`
--

LOCK TABLES `login_access_badge` WRITE;
/*!40000 ALTER TABLE `login_access_badge` DISABLE KEYS */;
/*!40000 ALTER TABLE `login_access_badge` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login_access_badgeapp`
--

DROP TABLE IF EXISTS `login_access_badgeapp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `login_access_badgeapp` (
  `l_id` int NOT NULL AUTO_INCREMENT,
  `l_date` datetime DEFAULT NULL,
  `module` varchar(45) DEFAULT NULL,
  `l_name` varchar(80) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `l_status` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`l_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_access_badgeapp`
--

LOCK TABLES `login_access_badgeapp` WRITE;
/*!40000 ALTER TABLE `login_access_badgeapp` DISABLE KEYS */;
/*!40000 ALTER TABLE `login_access_badgeapp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login_access_tmp`
--

DROP TABLE IF EXISTS `login_access_tmp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `login_access_tmp` (
  `l_id` int NOT NULL AUTO_INCREMENT,
  `l_date` datetime DEFAULT NULL,
  `module` varchar(45) DEFAULT NULL,
  `l_name` varchar(80) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `l_status` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`l_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_access_tmp`
--

LOCK TABLES `login_access_tmp` WRITE;
/*!40000 ALTER TABLE `login_access_tmp` DISABLE KEYS */;
/*!40000 ALTER TABLE `login_access_tmp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mass_email`
--

DROP TABLE IF EXISTS `mass_email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mass_email` (
  `id` int NOT NULL AUTO_INCREMENT,
  `mass_to` varchar(255) NOT NULL,
  `mass_to_users` varchar(100) DEFAULT '',
  `mass_reply_to` varchar(255) DEFAULT NULL,
  `mass_reply_name` varchar(100) DEFAULT NULL,
  `mass_subject` varchar(255) NOT NULL,
  `mass_body` blob NOT NULL,
  `mass_created` datetime DEFAULT NULL,
  `mass_created_by` int DEFAULT NULL,
  `mass_updated` datetime DEFAULT NULL,
  `mass_updated_by` int DEFAULT NULL,
  `mass_running` int DEFAULT '0',
  `mass_start` datetime DEFAULT NULL,
  `mass_runtime` datetime DEFAULT NULL,
  `mass_lastbadge` int DEFAULT NULL,
  `mass_finished` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mass_email`
--

LOCK TABLES `mass_email` WRITE;
/*!40000 ALTER TABLE `mass_email` DISABLE KEYS */;
/*!40000 ALTER TABLE `mass_email` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `membership_type`
--

DROP TABLE IF EXISTS `membership_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `membership_type` (
  `id` int NOT NULL AUTO_INCREMENT,
  `type` varchar(25) NOT NULL,
  `self_service` int NOT NULL DEFAULT '1',
  `status` int NOT NULL,
  `sku_half` varchar(15) DEFAULT NULL,
  `sku_full` varchar(15) DEFAULT NULL,
  `renew_yearly` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `membership_type`
--

LOCK TABLES `membership_type` WRITE;
/*!40000 ALTER TABLE `membership_type` DISABLE KEYS */;
INSERT INTO `membership_type` VALUES (50,'Primary',1,1,'450115','450100',1),(51,'Family',1,1,'450125','450110',1),(52,'Junior',1,1,'450120','450105',1),(70,'15yr',1,1,'450200','450200',1),(88,'N.C.M.',0,1,NULL,NULL,0),(99,'Life',0,1,NULL,NULL,1);
/*!40000 ALTER TABLE `membership_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `officers`
--

DROP TABLE IF EXISTS `officers`;
/*!50001 DROP VIEW IF EXISTS `officers`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `officers` AS SELECT 
 1 AS `badge_number`,
 1 AS `full_name`,
 1 AS `role`,
 1 AS `role_name`,
 1 AS `club`,
 1 AS `club_name`,
 1 AS `short_name`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `params`
--

DROP TABLE IF EXISTS `params`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `params` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sell_date` varchar(5) NOT NULL,
  `guest_sku` int NOT NULL DEFAULT '460130',
  `guest_total` int NOT NULL DEFAULT '50',
  `status` enum('active','disabled') NOT NULL,
  `rso_email` text,
  `pp_id` varchar(82) DEFAULT NULL,
  `pp_sec` varchar(82) DEFAULT NULL,
  `qb_env` varchar(4) DEFAULT 'dev',
  `conv_p_merc_id` varchar(7) DEFAULT NULL,
  `conv_p_user_id` varchar(45) DEFAULT NULL,
  `conv_p_pin` varchar(64) DEFAULT NULL,
  `conv_d_merc_id` varchar(7) DEFAULT NULL,
  `conv_d_user_id` varchar(45) DEFAULT NULL,
  `conv_d_pin` varchar(65) DEFAULT NULL,
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
  `log_rotate` int DEFAULT NULL,
  `whitelist` text,
  `remote_users` varchar(100) DEFAULT NULL,
  `usps_api` varchar(45) DEFAULT NULL,
  `check_ip` varchar(45) DEFAULT NULL,
  `check_ip_name` varchar(45) DEFAULT NULL,
  `sku_student` varchar(15) DEFAULT NULL,
  `sku_wc_discount` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `params`
--

LOCK TABLES `params` WRITE;
/*!40000 ALTER TABLE `params` DISABLE KEYS */;
INSERT INTO `params` VALUES (1,'10-29',440100,15,'active','[\"action@agcrange.org\",\"agc.mxlead@gmail.com\",\"agc.rso@gmail.com\",\"dbcramer@comcast.net\",\"executivevp@agcrange.org\",\"it.help@agcrange.org\",\"jim@landerkin.com\",\"officemgr@agcrange.org\",\"president@agcrange.org\",\"trap@agcrange.org\",\"vp@agcrange.org\"]','','','prod','2149962','BadgeApp','WJZM35JNI0SUCWM4QJNXDFA2ND3T55J9FD8CCK103JUGDHEKI85M65NKW3F8A3WX','0019085','webpage','2RIJNNYLO2C8W35IVOCTJIJI845MH9GS2S4SO09YPESF84SE7ATBFCZBWN55XEEQ','xyz','Q0uF4fg11yjyOATw3mRLQv3qGAsF2WF63OJVIbGsTWumo0LhbJ','jPND0qjRMcsynyuzwIt8OQxwXDF8LAx82CwrJklr','2999-01-01',NULL,'Q0cREcLl5eV2ugf11r6FrAmIaWCCCirCIILH9jOMD5ccUJbVLq','j8AENs241kjaDv8Xv9Mgxyp7o8AWNmFmpj4wrz9j','123146064068589',NULL,NULL,'AB11604428879zdrbnXkOD8bvhCKRn1KBl7Je8Hr1FrMa1NS7g','2020-08-17 11:22:48',9,'[\"&\",\"ACTION\",\"AGC\",\"AND\",\"ARCHERY\",\"CERTIFICATION\",\"CLUB\",\"GUN\",\"MD\",\"METALLIC\",\"PISTOL\",\"RIFLE\",\"SHOOTING\",\"SILHOUETTE\",\"STATE\"]','passwd','066ASSOC7994','192.168.42.12','Memorial Hall Dish','435130','465150');
/*!40000 ALTER TABLE `params` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `post_print_transactions`
--

DROP TABLE IF EXISTS `post_print_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `post_print_transactions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `badge_number` int NOT NULL,
  `transaction_type` varchar(6) NOT NULL,
  `created_at` datetime NOT NULL,
  `fee` float(8,2) NOT NULL,
  `discount` float(8,2) NOT NULL,
  `paid_amount` float(8,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post_print_transactions`
--

LOCK TABLES `post_print_transactions` WRITE;
/*!40000 ALTER TABLE `post_print_transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `post_print_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `role_id` int NOT NULL AUTO_INCREMENT,
  `role_name` varchar(45) NOT NULL,
  `disp_order` int DEFAULT NULL,
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `role_name_UNIQUE` (`role_name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'President',10),(2,'Vice President',20),(3,'Executive Officer',30),(4,'Treasurer',40),(5,'Secretary',50),(6,'Trustee',60),(7,'Alternate Trustee',70);
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rso_reports`
--

DROP TABLE IF EXISTS `rso_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rso_reports` (
  `id` int NOT NULL AUTO_INCREMENT,
  `rso` varchar(255) DEFAULT NULL,
  `shift` varchar(2) DEFAULT NULL,
  `date_open` datetime DEFAULT NULL,
  `date_close` datetime DEFAULT NULL,
  `shift_anom` text,
  `notes` text,
  `wb_color` varchar(3) DEFAULT NULL,
  `mics` varchar(3) DEFAULT NULL,
  `wb_trap_cases` int DEFAULT NULL,
  `par_50` int DEFAULT NULL,
  `par_100` int DEFAULT NULL,
  `par_200` int DEFAULT NULL,
  `par_steel` int DEFAULT NULL,
  `par_nm_hq` int DEFAULT NULL,
  `par_m_hq` int DEFAULT NULL,
  `par_trap` int DEFAULT NULL,
  `par_arch` int DEFAULT NULL,
  `par_pel` int DEFAULT NULL,
  `par_spr` int DEFAULT NULL,
  `par_cio_stu` int DEFAULT NULL,
  `par_act` int DEFAULT NULL,
  `cash_bos` decimal(7,2) DEFAULT NULL,
  `cash_drop` decimal(7,2) DEFAULT '0.00',
  `cash_eos` decimal(7,2) DEFAULT '0.00',
  `stickers` text,
  `closing` text,
  `cash` text,
  `checks` text,
  `violations` text,
  `closed` int DEFAULT '0',
  `remarks` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rso_reports`
--

LOCK TABLES `rso_reports` WRITE;
/*!40000 ALTER TABLE `rso_reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `rso_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rule_list`
--

DROP TABLE IF EXISTS `rule_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rule_list` (
  `id` int NOT NULL AUTO_INCREMENT,
  `rule_abrev` varchar(6) NOT NULL,
  `vi_type` int NOT NULL,
  `rule_name` varchar(255) NOT NULL,
  `is_active` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=141 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rule_list`
--

LOCK TABLES `rule_list` WRITE;
/*!40000 ALTER TABLE `rule_list` DISABLE KEYS */;
INSERT INTO `rule_list` VALUES (1,'IA02',3,'Never allow the gun to point at anything you do not intend to shoot.',1),(2,'IA02',4,'Never allow LOADED gun to point at anything you do not intend to shoot.',1),(3,'IB01',3,'Arguing with RSO, AGC Officer, or Match Director',1),(4,'IB01',4,'Refusing to follow the directions of RSO, AGC Officer, or Match Director',1),(5,'IB02',1,'Range badges shall be in the possession of the named badge holder and readily visible at all times while on AGC property.',1),(6,'IB02',4,'Range badges may not be loaned or transferred to, or in the possession of, any other person.',1),(7,'IB03',4,'Persons prohibited by any Federal, State or local law from owning or possessing firearms are specifically prohibited from entering upon AGC property and are subject to arrest for trespassing.',1),(8,'IB04',1,'Guest wrist bands shall be in the possession of the person it was issued to and readily visible at all times while on AGC property.',1),(9,'IB04',2,'Adult badge holders sign in their guests on the AGC App or log provided and be issued a wrist band.',1),(10,'IB04',4,'Guest badges may not be loaned or transferred to, or in the possession of, any other person.',0),(11,'IB06',1,'A badge holder with shooting guests may only occupy one firing point and ONLY ONE PERSON in your party may fire at a time.',1),(12,'IB08',1,'Guests shall park in the outer parking lot when using the 50 or 100-yard ranges.',1),(13,'IB10',4,'Consumption of alcohol is permitted in the Barnes Range House and Memorial Hall ONLY; no alcoholic beverages are permitted on or near any AGC range firing area.',1),(14,'IB11',2,'Pets shall be accompanied by their owner, leashed and under control at all times.',1),(15,'IB12',4,'AGC reserves the right to remove and permanently ban any member, non-member, guest or student without refund for violent, inappropriate, rude, disorderly, threatening, unsportsmanlike or intoxicated behavior.',1),(16,'IB13',2,'Parking is permitted in designated areas as posted.',1),(17,'IB14',2,'Driving onto or parking on any of the ranges is prohibited unless prior permission is granted by the RSO or Executive VP.',1),(18,'IB15',3,'Instruction or demonstration involving drawing from holster, or aiming, or aiming and dry firing is prohibited in all buildings.',1),(19,'IC03a',4,'Do not touch firearms during a Cease Fire! (firearm left loaded)',1),(20,'IC03b',3,'A Cease Fire is in effect from when it is called at the end of the day until the range is called HOT the following morning.',1),(21,'IC03d',2,'During a Cease Fire, all uncased firearms shall remain pointed downrange or racked in an upright position with actions open, magazines removed and Empty Chamber Indicator (ECI) in place.',1),(22,'IC03d',4,'During a Cease Fire, all uncased firearms shall not contain live rounds in the chamber/cylinder/fixed magazine or an inserted removable magazine.',1),(23,'IC03e',1,'During a Cease Fire, you shall remain behind the White Stripe when not pulling or posting targets.',1),(24,'IC04',2,'Badge-Holders shall, if they leave the firing line for any reason, safe their firearms per I.C.3.d., and instruct their guest, if any, to remain behind the White Stripe if not accompanying badge-holder down range..',1),(25,'IC05',4,'Firearms containing ammunition in any manner shall NOT be brought onto AGC property.',1),(26,'IC07',3,'All uncased firearms shall be carried muzzle up while being carried from place to place.',1),(27,'IC10a',2,'LOADED if an Empty Chamber Indicator (ECI) is not in place.',1),(28,'IC10b',2,'LOADED if actions, cylinders or loading gates are closed.',1),(29,'IC10c',2,'LOADED if empty cases are in the chamber/cylinder/fixed magazine, or if a removable empty magazine is inserted.',1),(30,'IC10c',4,'LOADED if cartridges are in the chamber/cylinder/fixed magazine, or if a removable magazine with cartridges is inserted.',1),(31,'IC10d',3,'LOADED if Black Powder Firearms containing: propellant, projectile or cap; powder in the pan of a flintlock.',1),(32,'IC11',2,'Uncased firearms shall NOT be brought onto or taken from the Concrete Pad when a Cease Fire is in effect.',1),(33,'IC12',3,'Cased firearms may be brought onto the Concrete Pad and placed on the ground or shooting bench at any time.  You shall NOT open the case or otherwise handle the firearm until the line is called HOT.',1),(34,'IC13',3,'When on the Concrete Pad, firearms shall remain pointed downrange while being cased or uncased.',1),(35,'IC14',2,'Containers of propellant shall be kept closed when not being used.',1),(36,'IC15',2,'Cleaning of firearms on the Concrete Pad is permitted with muzzles pointed downrange or upright.',1),(37,'IC16',2,'Cleaning of firearms off the Concrete Pad is permitted only if the firearm action is clearly disabled; firearm disassembled, bolt removed, etc.',1),(38,'IC16',4,'Cleaning of firearms with ammunition present.',1),(39,'IC17',1,'No one shall fire at any target not in their lane.',1),(40,'IC18',4,'No one shall fire at any wildlife.',1),(41,'IC19',4,'No one shall fire at any permanent structure or fixture or engage in willful destruction of property.',1),(42,'IC20',2,'Semi-automatic strings may be fired on any range at a rate that allows the aiming and control of each shot.  All shots fired must strike within the designated Impact Area for the shooter’s position.',1),(43,'IC21',2,'The Firing Line on the 50, 100 (lanes 1-90) and 200-yard ranges is the forward edge of the Concrete Pad. In the bench rest area (lanes 91-100) the firing line is the red zone.',1),(44,'IC22',2,'Shooters shall position themselves so the muzzle of their firearm is at or beyond the forward edge of the Concrete Pad.',1),(45,'IC22',3,'Under NO CIRCUMSTANCES will a firearm be discharged if the muzzle is behind any person.',1),(46,'IC23',3,'No one shall go forward of the Firing Line (See I.C.21) while the line is hot.',1),(47,'IC24',2,'If a firearm fails to fire, the muzzle shall remain pointed at the Impact Area for a minimum of 30 seconds before remedial action is taken.',1),(48,'IC25',3,'Firearms, ammunition and ammunition components shall not be stored on AGC property.',1),(49,'IC26',3,'Tracer, incendiary and explosive ammunition is prohibited.',1),(50,'IC27',2,'Targets and target frames must not be capable of deflecting a projectile in an unsafe direction.',1),(51,'IC28',3,'Fully automatic fire is only permitted as detailed in Chapter XII of the Policy & Procedures manual.',1),(52,'IC29',3,'Holstered firearms may be worn only under applicable Maryland law, within the constraints and conditions of your carry permit.',1),(53,'IC30',3,'Drawing from holsters is only permitted as detailed in Chapter  XXI of the Policy & Procedures Manual.',1),(54,'IC31',1,'Shooters shall clean up their area and police their brass and shotshell hulls when finished shooting and firearms are not being handled.',1),(55,'IIA01',2,'Rounds must hit impact berm',1),(56,'IIA02',1,'On the 50, 100 and 200-yard ranges, other than paper targets may be used provided that all fired rounds easily pass through them and strike the Impact Area. ',1),(57,'IIA03',1,'Pictures, caricatures or illustrations depicting real people are prohibited.',1),(58,'IIA04',3,'Exploding targets are prohibited.',1),(59,'IIA05',2,'Glass targets or those containing glass are prohibited.',1),(60,'IIA06',1,'Targets shall NOT be placed on the Impact Areas.',1),(61,'IIA07',3,'Targets shall NOT be placed on the Protective Berms.',1),(62,'IIA08',1,'Targets shall be placed in the location that matches the shooter’s lane number.',1),(63,'IIB02',2,'You must display your named yellow badge with certification sticker in addition to your range badge when shooting at steel targets.',1),(64,'IIB03',2,'Steel targets and their mounts shall be submitted for inspection and approval by the Executive VP or his/her designee before initial use and are subject to inspection at any time.',1),(65,'IIB3b',2,'Pitted, cratered, holed, bent, warped or otherwise damaged targets are prohibited.',1),(66,'IIB4a',2,'Prohibited ammunition: Rifle rounds exceeding 3150 fps muzzle velocity.',1),(67,'IIB4b',2,'Prohibited ammunition: Pistol rounds exceeding 1500 fps muzzle velocity.',1),(68,'IIB04c',2,'Prohibited ammunition: Any round with a muzzle velocity less than 750 fps.',1),(69,'IIB04d',2,'Prohibited ammunition: Any round labeled “Magnum”.',1),(70,'IIB04e',2,'Prohibited ammunition: Armor piercing, steel core or ‘penetrator’.',1),(71,'IIB04f',2,'Prohibited ammunition: 50 BMG and all long-range tactical rounds.',1),(72,'IIB04g',2,'Prohibited ammunition: Shotgun slugs.',1),(73,'IIB04h',2,'Prohibited ammunition: 5.7 X 28 ammunition',1),(74,'III01',2,'Smoking is prohibited within 15 feet of black powder or black powder substitutes.',1),(75,'III02',1,'Prior to loading, shooters using muzzle loading rifles or pistols shall fire caps on all nipples of percussion firearms, or a pan full of powder in a flintlock, while pointing the firearm downrange.',1),(76,'III03',3,'Muzzle loading firearms using granulated propellant shall have the propellant poured into the muzzle from a powder measure.',1),(77,'III04',2,'Containers of propellant shall be kept closed when not being used.',1),(78,'III05',2,'Shooters using muzzle loading rifles shall place their rifle muzzle up in a v-notch in the loading bench or some other device during a Cease Fire or during loading.',1),(79,'III06',2,'Percussion and flintlock firearms shall be positioned with the muzzle forward of the Firing Line and pointed downrange when a percussion cap is affixed or when the pan is charged.',1),(80,'III07',2,'Muzzle loading handguns shall be placed muzzle up in a loading stand or similar device during a Cease Fire.',1),(81,'IVA01',2,'This range is designated for the shooting of pistol-caliber handguns with barrels 10” or less in length.',1),(82,'IVA01a',2,'Handgun cartridges with ballistics between .22 rimfire and .500 S&W are permitted.',1),(83,'IVA02',2,'Rifle-caliber handguns are prohibited.',1),(84,'IVA03',2,'Shot shells shall NOT be fired on this range.',1),(85,'IVA04',1,'Firing from a position other than standing, or sitting on a stool, is prohibited.',1),(86,'IVA05',2,'When AGC-owned frames are used, only one target with a single, centered aiming point is permitted.',1),(87,'IVB02',1,'Positions to the left of the orange roof support pole (at lane 57) are normally closed to use. ',1),(88,'IVB03',1,'The 10 fixed benches on the far right of the Barnes range are for Benchrest Position shooting only, rifle and rifled shotgun shooting only.',1),(89,'IVB04',1,'In the Benchrest area, everyone must be behind the red zone while the line is hot and muzzles must meet or extend into the red zone which is considered the firing line.',1),(90,'IVB05',2,'Portable shooting benches shall be positioned so that the front legs are at the forward edge of the Concrete Pad.',1),(91,'IVC02',2,'Portable shooting benches shall be positioned so that the front legs are at the forward edge of the Concrete Pad.',1),(92,'IVC03',3,'An orange flag shall be displayed forward of the firing line when anyone is downrange.',1),(93,'IVC04',3,'The target carriages shall ONLY be used for firing properly sighted-in rifles at paper targets.',1),(94,'IVC05a',2,'A conventional bullseye target shall be centered in the target frame.',1),(95,'IVC05b',2,'Multiple aiming point targets, or any target other than a conventional bullseye target, shall be mounted with the aiming point no closer than 12” from the frame side members and all your shots must strike on the target paper.',1),(96,'IVC06',2,'Silhouettes, gongs, and spinners may be used for silhouette or hunting HANDGUN practice ONLY and shall be positioned directly in front of the 50, 100 or 150-meter berms or 200-meter Impact Area.',1),(97,'IVC07',2,'Firing a rifle at any target placed anywhere closer than 200 yards is prohibited.',1),(98,'IVC08',2,'Portable target frames may be placed behind the 200-yard pits immediately in front of the impact area.',1),(99,'IVC09',2,'Portable target frames with PAPER TARGETS may be placed atop the protective berm immediately forward of the pits.',1),(100,'IVC10',2,'An AGC-style portable wooden frame with PAPER TARGETS may be placed in the receptacles on the back side of the protective berm bulkhead above the pit roof.',1),(101,'IVC11',3,'Firing at objects placed on the protective berms is prohibited.',1),(102,'IVC12',3,'People may remain in the pits between ceasefires only during organized shoots/practices under the control of a designated Match Director.',1),(103,'IVC13',3,'No personnel are permitted in the 200-yard target pits when shooting steel targets.',1),(104,'IVC14',2,'Firearms shall NOT be left unattended.',1),(105,'IVC15',2,'Initial sighting in of firearms/scopes/sights is prohibited.',1),(106,'IVD02',2,'Shooting forward of the 16-yard line is prohibited.',1),(107,'IVD03',3,'Only shotguns firing a maximum powder load of 3 drams equivalent, shot size 7 1/2, 8 or 9, and a maximum muzzle velocity of 1200 fps are permitted.',1),(108,'IVD04',4,'Firing slugs is prohibited.',1),(109,'IVD05',2,'Shotguns shall remain unloaded with actions open at all times until on station and ready to shoot.',1),(110,'IVD05',4,'Shotgun actions shall remain open at all times until on station and ready to shoot.',1),(111,'IVD06',2,'When shooting handicaps, shooters may shoot from a staggered position not to exceed 2 yards.',1),(112,'IVD07',2,'Portable traps and other throwing devices may be used when positioned on or behind the 16-yard line.',1),(113,'IVD08',2,'No one shall proceed beyond a trap house when any other fields are in use.',1),(114,'IVD09',2,'Spent and/or unspent shot shells shall not be picked up until shooters have unloaded and racked their shotguns.',1),(115,'IVD10',2,'It is permitted to walk to the trap house if the field is ‘clear’.  Shooters shall unload and rack their shotguns prior to anyone going to the trap house.',1),(116,'IVD11',3,'When a person is in a trap house, an orange safety cone shall be placed on top of the trap house.',1),(117,'IVD12',2,'All firearms used on the Trap Range shall be fired from the shoulder.',1),(118,'IVD13',2,'Folding stocks shall be in the extended position.',1),(119,'IVE01',3,'This facility is intended for PATTERNING of shotguns ONLY.  ',1),(120,'IVE02',4,'Patterning targets shall have a single aiming point centered on the patterning board.',1),(121,'IVE03',4,'SLUGS are prohibited,',1),(122,'IVE04',4,'LEAD shot sizes larger than #2 are prohibited,',1),(123,'IVE05',4,'STEEL shot sizes larger than BBB are prohibited,',1),(124,'IVE06',4,'Placing of, or shooting at, objects on top of patterning frame is prohibited,',1),(125,'IVF01',2,'Sky drawing is prohibited.',1),(126,'IVF02',2,'Only field point or target arrows may be shot at the AGC targets.',1),(127,'IVF03',2,'Broad head arrows shall NOT be shot at AGC targets.',1),(128,'IVF05',2,'Archers shall designate a common Firing Line.',1),(129,'IVG03',2,'Only compressed air, carbon dioxide, and spring-powered guns firing .177 or .22 caliber blunt-nosed lead pellets weighing less than 25 grains may be fired on this range.',1),(130,'IVG04',2,'The maximum allowable velocity is 1000 fps for .177 pellets and 800 fps for .22 pellets.',1),(131,'IVG05',2,'Only paper targets or AGC-approved metal or metal-clad targets may be used.',1),(132,'IVG06',1,'Shooters shall be aligned properly with their pellet traps.',1),(133,'IB04',2,'Failure to return Guest Badge upon leaving the range.',0),(134,'IC03a',2,'Do not touch unloaded firearms during a ceasefire.',1),(135,'IB02',1,'Non-shooting club members shall wear their range badge or current AGC club membership card, readily visible, while on AGC Property.',1),(136,'IB11',2,'You are responsible for collecting and disposing of your pet’s waste.',1),(137,'IB17',4,'Items that have been designated as illegal by federal, state and/or local jurisdictions are prohibited on AGC property.',1),(138,'IC02a',1,'Eye and ear protection are required on or near active (hot) firing lines. EXCEPTIONS: Pellet Range, Archery Range.',1),(139,'IB05',1,'Badge-holders are responsible for and shall supervise their guest(s) at ALL TIMES.',1),(140,'IC32',4,'Any live-fire incident which leads to the injury of oneself or others may, at the discretion of the On Duty RSO, result in the impoundment of the offenders range badge.',1);
/*!40000 ALTER TABLE `rule_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sticker`
--

DROP TABLE IF EXISTS `sticker`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sticker` (
  `s_id` int NOT NULL AUTO_INCREMENT,
  `sticker` varchar(10) DEFAULT NULL,
  `status` varchar(4) DEFAULT NULL,
  `holder` int DEFAULT NULL,
  `updated` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`s_id`),
  UNIQUE KEY `s_id_UNIQUE` (`s_id`),
  UNIQUE KEY `sticker_UNIQUE` (`sticker`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sticker`
--

LOCK TABLES `sticker` WRITE;
/*!40000 ALTER TABLE `sticker` DISABLE KEYS */;
/*!40000 ALTER TABLE `sticker` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `store_items`
--

DROP TABLE IF EXISTS `store_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_items` (
  `item_id` int NOT NULL AUTO_INCREMENT,
  `item` varchar(100) NOT NULL,
  `sku` varchar(15) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `type` varchar(45) NOT NULL,
  `paren` int DEFAULT NULL,
  `img` varchar(255) DEFAULT NULL,
  `stock` int DEFAULT '0',
  `kit_items` text,
  `active` int NOT NULL DEFAULT '0',
  `new_badge` int NOT NULL DEFAULT '0',
  `tax_rate` decimal(5,3) DEFAULT '0.000',
  PRIMARY KEY (`item_id`)
) ENGINE=MyISAM AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `store_items`
--

LOCK TABLES `store_items` WRITE;
/*!40000 ALTER TABLE `store_items` DISABLE KEYS */;
INSERT INTO `store_items` VALUES (6,'Action Shooting',NULL,NULL,'Category',NULL,NULL,0,NULL,1,0,0.000),(25,'Holster Shooting - Certification Fee','410100',30.00,'Service',6,NULL,0,NULL,1,0,0.000),(7,'Steel 12\" Gong Kit','410200',127.36,'Kits',6,NULL,0,'[410202,410204,410214,410216]',1,0,0.060),(12,'Steel 12\" Gong Only','410202',80.19,'Inventory',6,NULL,8,'null',1,0,0.060),(13,'Steel Angle Mount','410204',47.17,'Inventory',6,NULL,10,'null',1,0,0.060),(20,'Steel 8\" Gong Kit','410206',94.34,'Kits',6,NULL,0,'[410204,410208,410214,410216]',1,0,0.060),(21,'Steel 8\" Gong Only','410208',47.17,'Inventory',6,NULL,4,'null',1,0,0.060),(8,'Steel IPSC Kit','410210',141.51,'Kits',6,NULL,0,'[410204,410212,410214,410216]',1,0,0.060),(19,'Steel IPSC Silhouette Plate','410212',99.06,'Inventory',6,NULL,3,'null',1,0,0.060),(24,'Steel Shooting - Certification Fee','410105',20.00,'Service',6,NULL,0,'null',1,0,0.000),(22,'Steel Shooting Supplies - 2 X 4','410214',9.43,'Inventory',6,NULL,13,'null',1,0,0.060),(23,'Steel Shooting Supplies Nut & Bolt','410216',3.77,'Inventory',6,NULL,12,'null',1,0,0.060),(56,'AGC Club Dues',NULL,NULL,'Category',NULL,NULL,0,NULL,1,0,0.000),(58,'AGC - Associate Club Dues, large ','445105',300.00,'Service',56,NULL,0,'null',1,0,0.000),(57,'AGC - Charter Club Dues','445100',300.00,'Service',56,NULL,0,NULL,1,0,0.000),(59,'AGC - Club Initiation Fees','445110',200.00,'Service',56,NULL,0,NULL,1,0,0.000),(45,'AGC Member Classroom Rentals',NULL,NULL,'Category',NULL,NULL,0,NULL,1,0,0.000),(65,'Barnes Lower Classroom Rent  - Weekends','460118',40.00,'Service',45,NULL,0,NULL,1,0,0.000),(64,'Barnes Lower Classroom Rent - Weekdays','460116',20.00,'Service',45,NULL,0,NULL,1,0,0.000),(46,'Guest Bracelet Fee','440100',15.00,'Service',45,NULL,0,NULL,1,0,0.000),(61,'Memorial Hall Rent  - Weekends','460110',40.00,'Service',45,NULL,0,NULL,1,0,0.000),(60,'Memorial Hall Rent - Weekdays','460108',20.00,'Service',45,NULL,0,NULL,1,0,0.000),(62,'Memorial Hall Trap Room Rent - Weekdays','460112',20.00,'Service',45,NULL,0,NULL,1,0,0.000),(63,'Memorial Hall Trap Room Rent - Weekends','460114',40.00,'Service',45,NULL,0,NULL,1,0,0.000),(43,'CIO Organizations',NULL,NULL,'Category',NULL,NULL,0,NULL,1,0,0.000),(50,'CIO Annual Assessment Fee','435105',500.00,'Service',43,NULL,0,NULL,1,0,0.000),(55,'CIO Barnes Lower Classroom Rent -  Weekdays','435116',20.00,'Service',43,NULL,0,NULL,1,0,0.000),(66,'CIO Barnes Lower Classroom Rent - Weekends','435118',40.00,'Service',43,NULL,0,NULL,1,0,0.000),(44,'CIO Student Bracelet Fee','435130',25.00,'Service',43,NULL,0,'null',1,0,0.000),(49,'CIO Initiation Fee','435100',500.00,'Service',43,NULL,0,NULL,1,0,0.000),(51,'CIO Memorial Hall Rent - Weekdays','435108',20.00,'Service',43,NULL,0,NULL,1,0,0.000),(52,'CIO Memorial Hall Rent - Weekends','435110',40.00,'Service',43,NULL,0,NULL,1,0,0.000),(53,'CIO Memorial Hall Trap Room Rent - Weekdays','435112',20.00,'Service',43,NULL,0,NULL,1,0,0.000),(54,'CIO Memorial Hall Trap Room Rent - Weekends','435114',40.00,'Service',43,NULL,0,NULL,1,0,0.000),(40,'DNR Qualification Fees',NULL,NULL,'Category',NULL,NULL,0,NULL,1,0,0.000),(41,'Member DNR Hunter Qualification','420300',7.00,'Service',40,NULL,0,'null',1,0,0.000),(42,'Non-Member DNR Hunter Qualification','420305',10.00,'Service',40,NULL,0,'null',1,0,0.000),(37,'AGC Services',NULL,NULL,'Category',NULL,NULL,0,NULL,1,0,0.000),(38,'Member Sight-In','420200',5.00,'Service',37,NULL,0,'null',1,0,0.000),(39,'Non-Member Sight-In','420205',7.00,'Service',37,NULL,0,'null',1,0,0.000),(47,'Member Dues',NULL,NULL,'Category',NULL,NULL,0,NULL,1,0,0.000),(5,'Full Year Family Dues','450110',75.00,'Service',47,NULL,0,'null',1,0,0.000),(3,'Full Year Individual Dues','450100',225.00,'Service',47,NULL,0,'null',1,0,0.000),(4,'Full Year Junior Dues','450105',125.00,'Service',47,NULL,0,NULL,1,0,0.000),(11,'Half Year Family Dues','450125',42.00,'Service',47,NULL,0,'null',1,0,0.000),(9,'Half Year Individual Dues','450115',125.00,'Service',47,NULL,0,'null',1,0,0.000),(10,'Half Year Junior Dues','450120',75.00,'Service',47,NULL,0,'null',1,0,0.000),(27,'Paper Target','430400',0.94,'Inventory',15,NULL,-1306,'null',1,0,0.060),(1,'Sales',NULL,0.00,'Service',NULL,NULL,0,NULL,1,0,0.000),(15,'Shooting Supplies',NULL,NULL,'Category',NULL,NULL,0,NULL,1,0,0.000),(17,'Ear Plugs','430305',0.94,'Inventory',15,NULL,1523,NULL,1,0,0.060),(18,'ECI/OAI\'s','430300',0.94,'Inventory',15,NULL,847,'null',1,0,0.060),(16,'Target Frames','430200',30.19,'Inventory',15,NULL,48,'null',1,0,0.060),(31,'Trap Activities',NULL,NULL,'Category',NULL,NULL,0,NULL,1,0,0.000),(36,'Clay Birds - Case','405310',15.09,'Inventory',31,NULL,5,'null',1,0,0.060),(35,'Member Lincoln Trap Rental','405115',6.00,'Service',31,NULL,0,NULL,1,0,0.000),(32,'Member Trap Books','405100',25.00,'Service',31,NULL,0,NULL,1,0,0.000),(34,'Member Youth Trap Books','405110',15.00,'Service',31,NULL,0,NULL,1,0,0.000),(33,'Non-Member Trap Books','405105',35.00,'Service',31,NULL,0,NULL,1,0,0.000),(67,'Steel Rental ','410201',10.00,'NonInventory',6,NULL,0,NULL,1,0,0.000),(69,'Shot Shells','405210',14.15,'Inventory',15,NULL,0,NULL,1,0,0.060),(70,'Safety Glasses','430310',2.83,'Inventory',15,NULL,27,NULL,1,0,0.060),(72,'Range Orientation Fee','530418',10.00,'Service',37,NULL,0,NULL,1,0,0.000),(73,'Coffee','430120 ',0.94,'NonInventory',15,NULL,NULL,NULL,1,1,0.060),(74,'Discount - Work Credits','465150',1.00,'NonInventory',47,NULL,NULL,NULL,0,0,0.000),(75,'Free Badge','Free',0.00,'NonInventory',47,NULL,NULL,NULL,0,0,0.000),(76,'AGC - Associate Club Dues, small (< 15 BH)','445115',150.00,'Service',56,NULL,NULL,'null',1,0,0.000);
/*!40000 ALTER TABLE `store_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `full_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `company` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `privilege` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `status` smallint NOT NULL DEFAULT '10',
  `badge_number` int DEFAULT NULL,
  `auth_key` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL,
  `clubs` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `r_user` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `password_reset_token` (`password_reset_token`)
) ENGINE=InnoDB AUTO_INCREMENT=174 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (0,'member','noreply@thanks.com','',NULL,'[5]',10,0,'x','x',NULL,0,0,NULL,NULL),(1, 'root', 'no@thanks.com', 'Root Account', NULL, '[1]', '10', '0', 'Gma5L6jxaFXfvbVXqU8U-krH3cKJzv3x', '$2y$13$YWIY0EyZIzsamuccG2gphuuJxzhyUz1VHB5fvf6MbtMt5fVY3YRjO', NULL, '1651418046', '1651418046', '\"\"', '');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_privileges`
--

DROP TABLE IF EXISTS `user_privileges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_privileges` (
  `id` int NOT NULL AUTO_INCREMENT,
  `privilege` varchar(99) NOT NULL,
  `priv_sort` int NOT NULL,
  `timeout` int NOT NULL,
  `restricted` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_privileges`
--

LOCK TABLES `user_privileges` WRITE;
/*!40000 ALTER TABLE `user_privileges` DISABLE KEYS */;
INSERT INTO `user_privileges` VALUES (1,'Root',1,60,1),(2,'Admin',20,30,1),(3,'RSO',40,20,0),(4,'View',65,15,0),(5,'Member',80,6,0),(6,'RSO Lead',30,20,0),(7,'Work Credits',60,15,0),(8,'CIO',64,5,0),(9,'Calendar Coordinator',50,10,0),(10,'Cashier',45,15,0),(11,'Chairmen',81,10,0),(12,'RSO Action',41,5,0),(13,'Admin View Only',22,30,1),(14,'Remote Access',99,5,1),(15,'Shooting Bay',82,10,0);
/*!40000 ALTER TABLE `user_privileges` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `violations`
--

DROP TABLE IF EXISTS `violations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `violations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `badge_reporter` int NOT NULL,
  `vi_type` int NOT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `violations`
--

LOCK TABLES `violations` WRITE;
/*!40000 ALTER TABLE `violations` DISABLE KEYS */;
/*!40000 ALTER TABLE `violations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `work_credits`
--

DROP TABLE IF EXISTS `work_credits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `work_credits` (
  `id` int NOT NULL AUTO_INCREMENT,
  `badge_number` int NOT NULL,
  `work_date` date DEFAULT NULL,
  `work_hours` float(8,2) NOT NULL,
  `project_name` varchar(255) NOT NULL,
  `remarks` text NOT NULL,
  `authorized_by` varchar(255) DEFAULT NULL,
  `supervisor` varchar(255) NOT NULL,
  `status` int NOT NULL,
  `updated_at` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  `created_by` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `work_credits`
--

LOCK TABLES `work_credits` WRITE;
/*!40000 ALTER TABLE `work_credits` DISABLE KEYS */;
/*!40000 ALTER TABLE `work_credits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Final view structure for view `Cart_Summary`
--

/*!50001 DROP VIEW IF EXISTS `Cart_Summary`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `Cart_Summary` AS select `t1`.`tx_date` AS `tx_date`,`s3`.`item` AS `cat`,`t1`.`tx_type` AS `tx_type`,`refunds`.`csku` AS `csku`,`refunds`.`citem` AS `citem`,`refunds`.`ea` AS `ea`,`refunds`.`qty` AS `qty`,`refunds`.`cprice` AS `cprice` from (`cc_receipts` `t1` join ((json_table(`t1`.`cart`, '$[*]' columns (`ea` decimal(7,2) path '$.ea', `qty` int path '$.qty', `csku` text character set utf8mb4 path '$.sku', `citem` text character set utf8mb4 path '$.item', `cprice` decimal(7,2) path '$.price')) `refunds` left join `store_items` `s2` on((`refunds`.`csku` = `s2`.`sku`))) left join `store_items` `s3` on((`s2`.`paren` = `s3`.`item_id`)))) order by `t1`.`tx_date` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `badge_subscriptions_date`
--

/*!50001 DROP VIEW IF EXISTS `badge_subscriptions_date`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `badge_subscriptions_date` AS select `badge_subscriptions`.`id` AS `id`,`badge_subscriptions`.`badge_number` AS `badge_number`,`badge_subscriptions`.`club_id` AS `club_id`,`badge_subscriptions`.`badge_year` AS `badge_year`,`badge_subscriptions`.`payment_type` AS `payment_type`,`badge_subscriptions`.`status` AS `status`,`badge_subscriptions`.`sticker` AS `sticker`,`badge_subscriptions`.`created_at` AS `created_at`,`badge_subscriptions`.`badge_fee` AS `badge_fee`,`badge_subscriptions`.`paid_amount` AS `paid_amount`,`badge_subscriptions`.`discount` AS `discount`,`badge_subscriptions`.`transaction_type` AS `transaction_type`,`badge_subscriptions`.`is_migrated` AS `is_migrated`,`badge_subscriptions`.`cc_x_id` AS `cc_x_id`,cast(`badge_subscriptions`.`created_at` as date) AS `bs_c_date` from `badge_subscriptions` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `bn_to_by`
--

/*!50001 DROP VIEW IF EXISTS `bn_to_by`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `bn_to_by` AS select `badge_subscriptions`.`badge_number` AS `badge_number`,max(`badge_subscriptions`.`badge_year`) AS `badge_year` from `badge_subscriptions` group by `badge_subscriptions`.`badge_number` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `bn_to_cl`
--

/*!50001 DROP VIEW IF EXISTS `bn_to_cl`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `bn_to_cl` AS select `badge_to_club`.`badge_number` AS `badge_number`,`badge_to_club`.`club_id` AS `club_id`,`clubs`.`short_name` AS `short_name`,`clubs`.`club_name` AS `club_name` from (`badge_to_club` left join `clubs` on((`clubs`.`club_id` = `badge_to_club`.`club_id`))) order by `badge_to_club`.`badge_number` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `cc_receipts_date`
--

/*!50001 DROP VIEW IF EXISTS `cc_receipts_date`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`marc`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `cc_receipts_date` AS select `cc_receipts`.`id` AS `id`,`cc_receipts`.`badge_number` AS `badge_number`,`cc_receipts`.`tx_date` AS `tx_date`,`cc_receipts`.`tx_type` AS `tx_type`,`cc_receipts`.`status` AS `status`,`cc_receipts`.`amount` AS `amount`,`cc_receipts`.`tax` AS `tax`,`cc_receipts`.`authCode` AS `authCode`,`cc_receipts`.`name` AS `name`,`cc_receipts`.`cardNum` AS `cardNum`,`cc_receipts`.`cardType` AS `cardType`,`cc_receipts`.`expYear` AS `expYear`,`cc_receipts`.`expMonth` AS `expMonth`,`cc_receipts`.`cashier` AS `cashier`,`cc_receipts`.`cashier_badge` AS `cashier_badge`,`cc_receipts`.`on_qb` AS `on_qb`,`cc_receipts`.`cart` AS `cart`,`cc_receipts`.`guest_cred` AS `guest_cred`,cast(`cc_receipts`.`tx_date` as date) AS `cc_c_date` from `cc_receipts` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `officers`
--

/*!50001 DROP VIEW IF EXISTS `officers`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `officers` AS select `badge_to_role`.`badge_number` AS `badge_number`,concat(`badges`.`first_name`,' ',`badges`.`last_name`) AS `full_name`,`badge_to_role`.`role` AS `role`,`roles`.`role_name` AS `role_name`,`badge_to_role`.`club` AS `club`,`clubs`.`club_name` AS `club_name`,`clubs`.`short_name` AS `short_name` from (((`badge_to_role` join `badges` on((`badges`.`badge_number` = `badge_to_role`.`badge_number`))) join `roles` on((`roles`.`role_id` = `badge_to_role`.`role`))) join `clubs` on((`clubs`.`club_id` = `badge_to_role`.`club`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed 



-- MySQL dump

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `associat_agcnew` /*!40100 DEFAULT CHARACTER SET utf8 */;

use `associat_agcnew`;
--
-- Table structure for table `agc_calendar`
--

DROP TABLE IF EXISTS `agc_calendar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `agc_calendar` (
  `calendar_id` int NOT NULL AUTO_INCREMENT,
  `recurrent_calendar_id` int NOT NULL DEFAULT '0',
  `event_date` date NOT NULL,
  `club_id` int NOT NULL DEFAULT '0',
  `facility_id` varchar(100) NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `keywords` text NOT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `date_requested` datetime DEFAULT NULL,
  `lanes_requested` int NOT NULL DEFAULT '0',
  `recur_every` int DEFAULT '0',
  `recur_week_days` varchar(255) DEFAULT NULL,
  `recurrent_start_date` datetime DEFAULT NULL,
  `recurrent_end_date` datetime DEFAULT NULL,
  `event_status_id` int NOT NULL DEFAULT '0',
  `range_status_id` int NOT NULL DEFAULT '0',
  `conflict` tinyint DEFAULT '0',
  `deleted` tinyint NOT NULL DEFAULT '0',
  `approved` tinyint NOT NULL DEFAULT '0',
  `active` tinyint NOT NULL DEFAULT '0',
  `showed_up` int DEFAULT '0',
  `rollover` tinyint NOT NULL DEFAULT '0',
  `time_format` tinyint NOT NULL DEFAULT '1',
  `poc_badge` int NOT NULL DEFAULT '0',
  `remarks` text,
  PRIMARY KEY (`calendar_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `agc_calendar`
--

LOCK TABLES `agc_calendar` WRITE;
/*!40000 ALTER TABLE `agc_calendar` DISABLE KEYS */;
/*!40000 ALTER TABLE `agc_calendar` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Table structure for table `blacklisted_emails`
--

DROP TABLE IF EXISTS `blacklisted_emails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blacklisted_emails` (
  `blacklisted_email_id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `first_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `last_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `display_order` int NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`blacklisted_email_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `contact_groups`
--

DROP TABLE IF EXISTS `contact_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact_groups` (
  `group_id` int NOT NULL DEFAULT '0',
  `contact_id` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`group_id`,`contact_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `contacts`
--

DROP TABLE IF EXISTS `contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contacts` (
  `contact_id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `office` varchar(255) NOT NULL,
  `district` varchar(255) NOT NULL,
  `committee` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `display_order` int NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`contact_id`)
) ENGINE=MyISAM AUTO_INCREMENT=228 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `event_status`
--

DROP TABLE IF EXISTS `event_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_status` (
  `event_status_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `active` tinyint NOT NULL DEFAULT '0',
  `display_order` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`event_status_id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_status`
--

LOCK TABLES `event_status` WRITE;
/*!40000 ALTER TABLE `event_status` DISABLE KEYS */;
INSERT INTO `event_status` VALUES (1,'Members',1,1),(2,'Public',1,2),(3,'Law Enforcement',1,3),(4,'CIO Course',1,4),(6,'Members and Guest',1,6),(8,'MITAGS',0,8),(9,'Open To General Public ',0,9),(10,'Range Maintenance ',1,10),(11,'Executive Committee Invitation ',1,11),(12,'Board of Trustees Invitation ',1,12),(13,'Youth',1,13),(14,'Women',1,14),(15,'All Clubs Open House',1,15),(16,'Private Event',1,16),(17,'AGC Sanctioned Activity',1,17),(18,'Holiday',1,18),(19,'Canceled ',1,19),(20,'CIO Refresher Course - Invitation Only ',0,21),(21,'Rescheduled',1,20);
/*!40000 ALTER TABLE `event_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `facilities`
--

DROP TABLE IF EXISTS `facilities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `facilities` (
  `facility_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `available_lanes` int NOT NULL DEFAULT '0',
  `active` tinyint NOT NULL DEFAULT '0',
  `display_order` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`facility_id`)
) ENGINE=MyISAM AUTO_INCREMENT=41 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `facilities`
--

LOCK TABLES `facilities` WRITE;
/*!40000 ALTER TABLE `facilities` DISABLE KEYS */;
INSERT INTO `facilities` VALUES (2,'Pistol Range',45,1,9),(3,'Barnes Multi-Purpose Range',90,1,3),(4,'Barnes Range House Upper & Lower',0,0,0),(5,'Barnes Lower Class Room',0,1,2),(6,'Barnes Upper Meeting Room',0,1,5),(7,'High Power 200 Yard Range',10,0,0),(10,'Silhouette 200 Yard Range',10,0,0),(11,'Memorial Hall & Trap Room',0,0,0),(12,'Memorial Hall',0,0,0),(13,'Trap Room',0,1,24),(16,'Memorial Hall & Indoor Pellet Range',0,1,8),(17,'Trap 1',0,1,19),(19,'Trap 2',0,1,20),(20,'Trap 3',0,1,22),(21,'Trap 2 & 3',0,0,21),(23,'Trap 4 - Wobble Trap',0,1,23),(24,'Shotgun Patterning Range',0,1,11),(25,'Archery Range',0,1,1),(26,'Closed',0,0,0),(27,'All Facilities & Ranges',0,0,0),(28,'Bench Rest',10,1,6),(29,'Canceled ',0,0,0),(30,'Pistol Range (Points 46-58)',12,1,10),(31,'Barnes Office',0,1,4),(32,'200-yd Range',10,1,1),(33,'Campground',0,1,7),(34,'Shooting Bay 7',0,1,12),(35,'Shooting Bay 6',0,1,13),(36,'Shooting Bay 5',0,1,14),(37,'Shooting Bay 4',0,1,15),(38,'Shooting Bay 3',0,1,16),(39,'Shooting Bay 2',0,1,17),(40,'Shooting Bay 1',0,1,18);
/*!40000 ALTER TABLE `facilities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `groups` (
  `group_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `display_order` int NOT NULL DEFAULT '0',
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
  `les_statistics_id` int NOT NULL AUTO_INCREMENT,
  `year` int NOT NULL DEFAULT '0',
  `month` int NOT NULL DEFAULT '0',
  `total_visits` int NOT NULL DEFAULT '0',
  `total_unique_visits` int NOT NULL DEFAULT '0',
  `total_sent_emails` int NOT NULL DEFAULT '0',
  `emails_to_sender` int NOT NULL DEFAULT '0',
  `emails_to_legislators` int NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`les_statistics_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `les_statistics`
--

LOCK TABLES `les_statistics` WRITE;
/*!40000 ALTER TABLE `les_statistics` DISABLE KEYS */;
/*!40000 ALTER TABLE `les_statistics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `les_statistics_per_group`
--

DROP TABLE IF EXISTS `les_statistics_per_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `les_statistics_per_group` (
  `les_statistics_per_group_id` int NOT NULL AUTO_INCREMENT,
  `les_statistics_id` int NOT NULL DEFAULT '0',
  `email_receivers_group` int NOT NULL DEFAULT '0',
  `visits` int NOT NULL DEFAULT '0',
  `unique_visits` int NOT NULL DEFAULT '0',
  `sent_emails` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`les_statistics_per_group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `les_statistics_per_group`
--

LOCK TABLES `les_statistics_per_group` WRITE;
/*!40000 ALTER TABLE `les_statistics_per_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `les_statistics_per_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `range_status`
--

DROP TABLE IF EXISTS `range_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `range_status` (
  `range_status_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `active` tinyint NOT NULL DEFAULT '0',
  `restricted` tinyint DEFAULT '0',
  `display_order` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`range_status_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `range_status`
--

LOCK TABLES `range_status` WRITE;
/*!40000 ALTER TABLE `range_status` DISABLE KEYS */;
INSERT INTO `range_status` VALUES (1,'Open',1,0,3),(2,'Closed',1,0,1),(4,'Canceled',0,0,4),(5,'Range Open-Club Regulated',1,0,5),(6,'Caliber Restriction',1,1,6),(7,'Event Full',0,0,7),(8,'Waiting List Reservations Only',0,0,8);
/*!40000 ALTER TABLE `range_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sent_emails`
--

DROP TABLE IF EXISTS `sent_emails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sent_emails` (
  `sent_email_id` int NOT NULL AUTO_INCREMENT,
  `part_of_sent_email_id` int NOT NULL DEFAULT '0',
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address1` varchar(255) NOT NULL,
  `address2` varchar(255) NOT NULL,
  `phone` varchar(25) NOT NULL,
  `fax` varchar(25) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state_id` int DEFAULT '0',
  `zip` varchar(10) DEFAULT NULL,
  `add_me_to_tbmpac_mailing_list` tinyint(1) NOT NULL DEFAULT '0',
  `add_me_to_agc_mailing_list` tinyint(1) NOT NULL DEFAULT '0',
  `email_recievers` text NOT NULL,
  `email_recievers_group` int NOT NULL DEFAULT '0',
  `subject` text NOT NULL,
  `message` text NOT NULL,
  `sender_ip` varchar(20) NOT NULL DEFAULT '',
  `is_sent` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `display_order` int NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_locked` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`sent_email_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sent_emails`
--

LOCK TABLES `sent_emails` WRITE;
/*!40000 ALTER TABLE `sent_emails` DISABLE KEYS */;
/*!40000 ALTER TABLE `sent_emails` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed
