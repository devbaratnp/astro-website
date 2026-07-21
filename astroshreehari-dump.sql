-- MySQL dump 10.13  Distrib 8.4.6, for Win64 (x86_64)
--
-- Host: localhost    Database: astroshreehari
-- ------------------------------------------------------
-- Server version	12.0.2-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admin_users`
--

DROP TABLE IF EXISTS `admin_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `display_name` varchar(100) DEFAULT NULL,
  `role` enum('admin','editor') DEFAULT 'editor',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_users`
--

LOCK TABLES `admin_users` WRITE;
/*!40000 ALTER TABLE `admin_users` DISABLE KEYS */;
INSERT INTO `admin_users` VALUES (1,'admin','$2y$10$6M4WAJWkVEFJqsmMHZWkp.OQNA5RuzIGF.SZVmrK7yMpODUcckCGK','Administrator','admin','2026-07-11 10:06:27');
/*!40000 ALTER TABLE `admin_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appointments`
--

DROP TABLE IF EXISTS `appointments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `appointments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `service_type` enum('kundali','marriage','grahadasha','vastu','pooja','general') NOT NULL,
  `preferred_date` date DEFAULT NULL,
  `preferred_time` time DEFAULT NULL,
  `consultation_mode` enum('phone','whatsapp','video','inperson') DEFAULT 'whatsapp',
  `product_id` int(10) unsigned DEFAULT NULL,
  `meeting_url` varchar(500) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `birth_time` time DEFAULT NULL,
  `birth_place` varchar(200) DEFAULT NULL,
  `message` mediumtext NOT NULL,
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  `admin_notes` mediumtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `nwaran_name` varchar(100) DEFAULT '',
  `father_name` varchar(100) DEFAULT '',
  `mother_name` varchar(100) DEFAULT '',
  `birth_order` varchar(20) DEFAULT '',
  `birth_gender` varchar(10) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_date` (`preferred_date`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appointments`
--

LOCK TABLES `appointments` WRITE;
/*!40000 ALTER TABLE `appointments` DISABLE KEYS */;
INSERT INTO `appointments` VALUES (1,NULL,'Devbarat Prasad Patel','9811144402','pdewbrath@gmail.com','marriage','2026-07-15','17:00:00','video',NULL,'https://meet.jit.si/AstroShreeHari-a6068af2b260fc4a',NULL,NULL,'','test','pending',NULL,'2026-07-11 12:06:13','2026-07-11 12:06:13','','','','',''),(2,NULL,'Devbarat Prasad Patel','9811144402','mind59024@gmail.com','marriage','2026-07-16','10:00:00','whatsapp',NULL,NULL,NULL,NULL,'','gccyyu','pending',NULL,'2026-07-16 12:47:37','2026-07-16 12:47:37','','','','',''),(3,NULL,'Samyog','9824028374','sitaramtimsina@gmail.com','general','2026-07-17','16:00:00','whatsapp',NULL,NULL,NULL,NULL,'','Bratabandha','pending',NULL,'2026-07-17 08:08:52','2026-07-17 08:08:52','','','','',''),(4,NULL,'Mechelle Landry','+1 (437) 281-1256','luqur@mailinator.com','pooja','2026-07-17','09:00:00','whatsapp',NULL,NULL,'1935-04-16','09:44:00','Debitis lorem nisi s','Magnam aperiam quam','pending',NULL,'2026-07-17 14:53:26','2026-07-17 14:53:26','Akeem Gaines','Kato Roberts','Yeo Banks','????????????','??????????'),(5,NULL,'Samyog timsina','9844639228','sitaramtimsina3m@gmail.com','kundali','2026-07-17','09:00:00','whatsapp',NULL,NULL,'2018-12-02','16:40:00','Jhapa','???????????? ??????????????? ?????????????????????????????? ???????????? ???????????????????????????????????????','pending',NULL,'2026-07-17 21:35:03','2026-07-17 21:35:03','Rameshwar timsina','Taraprasad timsina','Ganga timsina','???????????????','??????????'),(6,NULL,'Jolie Stevenson','+1 (983) 344-6893','bugagiw@mailinator.com','general','2026-07-18','11:00:00','whatsapp',NULL,NULL,'1951-11-20','14:59:00','Sit consequatur ut','Perferendis eligendi','pending',NULL,'2026-07-18 07:54:59','2026-07-18 07:54:59','Tanisha Madden','Latifah Duncan','Evan Hewitt','??????????????????','??????????'),(7,NULL,'Jolie Stevenson','+1 (983) 344-6893','bugagiw@mailinator.com','general','2026-07-18','11:00:00','whatsapp',NULL,NULL,'1951-11-20','14:59:00','Sit consequatur ut','Perferendis eligendi','pending',NULL,'2026-07-18 07:56:14','2026-07-18 07:56:14','Tanisha Madden','Latifah Duncan','Evan Hewitt','??????????????????','??????????'),(8,NULL,'Devbarat','9811144402`','pdewbrath@gmail.com','marriage','2026-07-19','10:30:00','video',NULL,'https://meet.jit.si/AstroShreeHari-87f0980cea22b221','2006-12-19','23:16:00','Birgunj','gwsgwg','pending',NULL,'2026-07-19 22:25:13','2026-07-19 22:25:13','rrhr','rererh','sanju','???????????????','??????????'),(9,NULL,'Devbarat Prasad Patel','9811144402','mind59024@gmail.com','vastu','2026-07-20','10:30:00','video',NULL,'https://meet.jit.si/AstroShreeHari-d789efa540c8f198','2006-12-19','22:18:00','Phulkaul','wbgiwubg','pending',NULL,'2026-07-19 22:50:06','2026-07-19 22:50:06','Pata','Nagendar Prasad Kurmi','Manju Devi','???????????????','??????????'),(10,NULL,'Devbarat Prasad Patel','9811144402','mind59024@gmail.com','kundali','2026-07-22','16:30:00','whatsapp',NULL,NULL,'1943-02-08','00:30:00','Eiusmod vero in quas','Dolore culpa laborum','pending',NULL,'2026-07-19 23:11:56','2026-07-19 23:11:56','Zena Kim','Abel Robles','Yoko Shelton','??????????????????','??????????'),(11,NULL,'Devbarat Prasad Patel','9811144402','pdewbrath@gmail.com','vastu','2026-07-23','10:30:00','whatsapp',NULL,NULL,'1932-09-06','05:44:00','Reprehenderit id om','Dignissimos quo dict','pending',NULL,'2026-07-20 09:06:26','2026-07-20 09:06:26','Walker Mercer','Lester Nicholson','Oprah Greer','??????????????????','??????????');
/*!40000 ALTER TABLE `appointments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `articles`
--

DROP TABLE IF EXISTS `articles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `articles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title_ne` varchar(200) NOT NULL,
  `title_en` varchar(200) DEFAULT NULL,
  `slug` varchar(200) NOT NULL,
  `content_ne` mediumtext NOT NULL,
  `content_en` mediumtext DEFAULT NULL,
  `excerpt_ne` varchar(300) DEFAULT NULL,
  `excerpt_en` varchar(300) DEFAULT NULL,
  `cover_image` varchar(300) DEFAULT NULL,
  `tags` longtext DEFAULT NULL CHECK (json_valid(`tags`)),
  `is_published` tinyint(1) DEFAULT 0,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_slug` (`slug`),
  KEY `idx_published` (`is_published`,`published_at`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `articles`
--

LOCK TABLES `articles` WRITE;
/*!40000 ALTER TABLE `articles` DISABLE KEYS */;
INSERT INTO `articles` VALUES (2,'jgieurgb',NULL,'uwbiubwu','rvubiwgub',NULL,'uwiurb',NULL,NULL,NULL,1,'2026-07-21 09:35:30','2026-07-20 08:59:06','2026-07-21 09:35:30');
/*!40000 ALTER TABLE `articles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_messages`
--

DROP TABLE IF EXISTS `contact_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact_messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `subject` varchar(200) NOT NULL,
  `message` mediumtext NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_read` (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_messages`
--

LOCK TABLES `contact_messages` WRITE;
/*!40000 ALTER TABLE `contact_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `events` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('event','tour') NOT NULL DEFAULT 'event',
  `title_ne` varchar(200) NOT NULL,
  `title_en` varchar(200) DEFAULT '',
  `description_ne` text DEFAULT NULL,
  `description_en` text DEFAULT NULL,
  `date_from` date NOT NULL,
  `date_to` date DEFAULT NULL,
  `time_from` time DEFAULT NULL,
  `location` varchar(300) DEFAULT '',
  `cover_image` varchar(300) DEFAULT '',
  `registration_url` varchar(500) DEFAULT '',
  `contact_person` varchar(100) DEFAULT '',
  `contact_phone` varchar(20) DEFAULT '',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_type_date` (`type`,`is_active`,`date_from`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gallery_items`
--

DROP TABLE IF EXISTS `gallery_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gallery_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('image','video','audio') NOT NULL DEFAULT 'image',
  `title_ne` varchar(200) NOT NULL,
  `title_en` varchar(200) DEFAULT '',
  `url` varchar(500) DEFAULT '',
  `thumbnail` varchar(300) DEFAULT '',
  `embed_url` varchar(500) DEFAULT '',
  `source` varchar(100) DEFAULT '',
  `sort_order` int(10) unsigned DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_type_active` (`type`,`is_active`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gallery_items`
--

LOCK TABLES `gallery_items` WRITE;
/*!40000 ALTER TABLE `gallery_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `gallery_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `panchang`
--

DROP TABLE IF EXISTS `panchang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `panchang` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `bs_date` varchar(20) DEFAULT NULL,
  `tithi` varchar(100) DEFAULT NULL,
  `nakshatra` varchar(100) DEFAULT NULL,
  `moon_rashi` varchar(50) DEFAULT NULL,
  `yoga` varchar(100) DEFAULT NULL,
  `karana` varchar(100) DEFAULT NULL,
  `sunrise` time DEFAULT NULL,
  `sunset` time DEFAULT NULL,
  `rahu_kaal` time DEFAULT NULL,
  `auspicious_times` longtext DEFAULT NULL CHECK (json_valid(`auspicious_times`)),
  `special_events_ne` mediumtext DEFAULT NULL,
  `special_events_en` mediumtext DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `date` (`date`),
  KEY `idx_date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `panchang`
--

LOCK TABLES `panchang` WRITE;
/*!40000 ALTER TABLE `panchang` DISABLE KEYS */;
INSERT INTO `panchang` VALUES (2,'2026-07-12',NULL,'????????????','??????????????????',NULL,NULL,NULL,'05:16:00','19:02:00',NULL,NULL,'[]',NULL,'2026-07-12 07:08:26'),(3,'2026-07-13',NULL,'?????????????????????','??????????????????',NULL,NULL,NULL,'05:17:00','19:01:00',NULL,NULL,'[]',NULL,'2026-07-14 03:40:33'),(4,'2026-07-17',NULL,'??????????????????','??????????????????',NULL,NULL,NULL,'05:19:00','19:00:00',NULL,NULL,'[]',NULL,'2026-07-17 13:43:44'),(5,'2026-07-19',NULL,'????????????','??????????????????',NULL,NULL,NULL,'05:20:00','18:59:00',NULL,NULL,'[]',NULL,'2026-07-17 14:56:49'),(6,'2026-07-18',NULL,'??????????????????','??????????????????','????????????',NULL,NULL,'05:19:00','19:00:00',NULL,NULL,'[]',NULL,'2026-07-18 07:24:59'),(7,'2026-07-20',NULL,'??????????????????','??????????????????','????????????',NULL,NULL,'05:20:00','18:59:00',NULL,NULL,'[]',NULL,'2026-07-20 06:01:28'),(8,'2026-07-21',NULL,'त्रयोदशी','स्वाती','तुला',NULL,NULL,'05:21:00','18:59:00',NULL,NULL,'[]','[]','2026-07-21 11:28:01');
/*!40000 ALTER TABLE `panchang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `booking_type` enum('appointment','pooja','reward') NOT NULL,
  `booking_id` int(10) unsigned NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `user_phone` varchar(20) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` enum('esewa','khalti','imepay','bank') NOT NULL,
  `transaction_ref` varchar(100) NOT NULL,
  `screenshot_path` varchar(300) DEFAULT NULL,
  `admin_notes` mediumtext DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_payments_method_reference` (`method`,`transaction_ref`),
  KEY `idx_status` (`status`),
  KEY `idx_booking` (`booking_type`,`booking_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pooja_bookings`
--

DROP TABLE IF EXISTS `pooja_bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pooja_bookings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `service_id` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `preferred_date` date NOT NULL,
  `preferred_time` time DEFAULT NULL,
  `address` mediumtext DEFAULT NULL,
  `special_instructions` mediumtext DEFAULT NULL,
  `needs_materials` tinyint(1) DEFAULT 0,
  `is_live_stream` tinyint(1) DEFAULT 0,
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_date` (`preferred_date`),
  KEY `fk_pooja_bookings_service` (`service_id`),
  CONSTRAINT `fk_pooja_bookings_service` FOREIGN KEY (`service_id`) REFERENCES `pooja_services` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pooja_bookings`
--

LOCK TABLES `pooja_bookings` WRITE;
/*!40000 ALTER TABLE `pooja_bookings` DISABLE KEYS */;
/*!40000 ALTER TABLE `pooja_bookings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pooja_services`
--

DROP TABLE IF EXISTS `pooja_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pooja_services` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title_ne` varchar(200) NOT NULL,
  `title_en` varchar(200) NOT NULL,
  `description_ne` mediumtext DEFAULT NULL,
  `description_en` mediumtext DEFAULT NULL,
  `category` enum('shanti','graha','sanskar','festival','other') NOT NULL,
  `base_price` decimal(10,2) DEFAULT NULL,
  `duration_minutes` int(11) DEFAULT NULL,
  `materials_available` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pooja_services`
--

LOCK TABLES `pooja_services` WRITE;
/*!40000 ALTER TABLE `pooja_services` DISABLE KEYS */;
/*!40000 ALTER TABLE `pooja_services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title_ne` varchar(200) NOT NULL,
  `title_en` varchar(200) DEFAULT NULL,
  `description_ne` text DEFAULT NULL,
  `description_en` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `compare_price` decimal(10,2) DEFAULT NULL,
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`images`)),
  `category` varchar(100) DEFAULT NULL,
  `stock_status` enum('in_stock','out_of_stock','pre_order') DEFAULT 'in_stock',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_active` (`is_active`),
  KEY `idx_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `push_subscriptions`
--

DROP TABLE IF EXISTS `push_subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `push_subscriptions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `endpoint` varchar(700) NOT NULL,
  `p256dh` varchar(255) NOT NULL,
  `auth` varchar(255) NOT NULL,
  `language` enum('ne','en') DEFAULT 'ne',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_push_endpoint` (`endpoint`(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `push_subscriptions`
--

LOCK TABLES `push_subscriptions` WRITE;
/*!40000 ALTER TABLE `push_subscriptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `push_subscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rewards`
--

DROP TABLE IF EXISTS `rewards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rewards` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(100) NOT NULL,
  `user_phone` varchar(20) NOT NULL,
  `reward_type` enum('feature','discount','badge','service','other') NOT NULL,
  `title_ne` varchar(200) NOT NULL,
  `title_en` varchar(200) DEFAULT NULL,
  `description_ne` mediumtext DEFAULT NULL,
  `description_en` mediumtext DEFAULT NULL,
  `is_redeemed` tinyint(1) DEFAULT 0,
  `expires_at` date DEFAULT NULL,
  `awarded_by` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_phone` (`user_phone`),
  KEY `idx_redeemed` (`is_redeemed`),
  KEY `fk_rewards_admin` (`awarded_by`),
  CONSTRAINT `fk_rewards_admin` FOREIGN KEY (`awarded_by`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rewards`
--

LOCK TABLES `rewards` WRITE;
/*!40000 ALTER TABLE `rewards` DISABLE KEYS */;
/*!40000 ALTER TABLE `rewards` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `testimonials`
--

DROP TABLE IF EXISTS `testimonials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `testimonials` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `title` varchar(200) DEFAULT '',
  `content` text NOT NULL,
  `rating` tinyint(3) unsigned DEFAULT 5,
  `photo` varchar(300) DEFAULT '',
  `location` varchar(200) DEFAULT '',
  `sort_order` int(10) unsigned DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_active` (`is_active`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `testimonials`
--

LOCK TABLES `testimonials` WRITE;
/*!40000 ALTER TABLE `testimonials` DISABLE KEYS */;
/*!40000 ALTER TABLE `testimonials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `language` enum('ne','en') DEFAULT 'ne',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_phone` (`phone`),
  KEY `idx_phone` (`phone`),
  KEY `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'astroshreehari'
--

--
-- Dumping routines for database 'astroshreehari'
--

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-21 12:32:40
