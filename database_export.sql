-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: nepal_ride_hub
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `purpose` enum('travel','function','pick_and_drop') DEFAULT 'travel',
  `pickup_location` varchar(255) DEFAULT NULL,
  `dropoff_location` varchar(255) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `with_driver` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `vehicle_id` (`vehicle_id`),
  CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookings`
--

LOCK TABLES `bookings` WRITE;
/*!40000 ALTER TABLE `bookings` DISABLE KEYS */;
INSERT INTO `bookings` VALUES (1,2,2,'2026-03-26','2026-03-28','travel','','',3000.00,'completed','2026-03-26 16:16:30',0),(4,2,5,'2026-04-10','2026-04-12','function','','',5000.00,'completed','2026-04-09 10:47:05',1);
/*!40000 ALTER TABLE `bookings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `emergency_contacts`
--

DROP TABLE IF EXISTS `emergency_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emergency_contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_name` varchar(100) NOT NULL,
  `phone_number` varchar(30) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT 'fa-phone',
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `emergency_contacts`
--

LOCK TABLES `emergency_contacts` WRITE;
/*!40000 ALTER TABLE `emergency_contacts` DISABLE KEYS */;
INSERT INTO `emergency_contacts` VALUES (1,'Nepal Police','100','National police emergency line available 24/7 across Nepal.','fa-shield-halved',1,1),(2,'Nepal Ambulance','102','Metropolitan Ambulance Service for immediate medical emergencies.','fa-truck-medical',2,1),(3,'Fire Brigade','101','Fire and rescue services for emergency fire situations.','fa-fire-extinguisher',3,1),(4,'Tourist Police','1144','Dedicated helpline for tourists facing issues during their Nepal visit.','fa-star-of-life',4,1),(5,'Nepal Ride Hub 24/7 Support','+977-01-4000000','Our round-the-clock customer support for all rental-related emergencies.','fa-headset',5,1),(6,'Roadside Assistance','+977-9800000001','Vehicle breakdown and roadside assistance for all active bookings.','fa-car-burst',6,1);
/*!40000 ALTER TABLE `emergency_contacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `emergency_incidents`
--

DROP TABLE IF EXISTS `emergency_incidents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emergency_incidents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `incident_type` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `location_text` varchar(255) DEFAULT NULL,
  `gps_lat` decimal(10,8) DEFAULT NULL,
  `gps_lng` decimal(11,8) DEFAULT NULL,
  `status` enum('open','in_progress','resolved') DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `booking_id` (`booking_id`),
  CONSTRAINT `emergency_incidents_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `emergency_incidents_ibfk_2` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `emergency_incidents`
--

LOCK TABLES `emergency_incidents` WRITE;
/*!40000 ALTER TABLE `emergency_incidents` DISABLE KEYS */;
INSERT INTO `emergency_incidents` VALUES (1,2,NULL,'Accident / Collision','accident','kathmandu',27.66193762,85.39342884,'resolved','2026-04-09 10:25:54'),(2,2,NULL,'Accident / Collision','accident','kathmandu',NULL,NULL,'in_progress','2026-04-29 07:54:29');
/*!40000 ALTER TABLE `emergency_incidents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `feedback`
--

DROP TABLE IF EXISTS `feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comments` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `vehicle_id` (`vehicle_id`),
  CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `feedback`
--

LOCK TABLES `feedback` WRITE;
/*!40000 ALTER TABLE `feedback` DISABLE KEYS */;
/*!40000 ALTER TABLE `feedback` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `maintenance`
--

DROP TABLE IF EXISTS `maintenance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `maintenance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vehicle_id` int(11) NOT NULL,
  `service_date` date NOT NULL,
  `description` text NOT NULL,
  `cost` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `vehicle_id` (`vehicle_id`),
  CONSTRAINT `maintenance_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `maintenance`
--

LOCK TABLES `maintenance` WRITE;
/*!40000 ALTER TABLE `maintenance` DISABLE KEYS */;
/*!40000 ALTER TABLE `maintenance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `site_reviews`
--

DROP TABLE IF EXISTS `site_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `site_reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` text NOT NULL,
  `service_type` varchar(100) DEFAULT 'general',
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `service_id` int(11) DEFAULT NULL,
  `admin_reply` text DEFAULT NULL,
  `replied_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `site_reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_reviews`
--

LOCK TABLES `site_reviews` WRITE;
/*!40000 ALTER TABLE `site_reviews` DISABLE KEYS */;
INSERT INTO `site_reviews` VALUES (2,2,NULL,5,'good service well behaved','general','approved','2026-04-09 12:00:42',NULL,'thank u','2026-04-09 12:02:12');
/*!40000 ALTER TABLE `site_reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sos_incidents`
--

DROP TABLE IF EXISTS `sos_incidents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sos_incidents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `gps_lat` decimal(10,8) DEFAULT NULL,
  `gps_lng` decimal(11,8) DEFAULT NULL,
  `status` enum('new','responded','resolved') DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `sos_incidents_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sos_incidents`
--

LOCK TABLES `sos_incidents` WRITE;
/*!40000 ALTER TABLE `sos_incidents` DISABLE KEYS */;
/*!40000 ALTER TABLE `sos_incidents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_documents`
--

DROP TABLE IF EXISTS `user_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `document_type` enum('citizenship','license','passport') NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `status` enum('pending','verified','rejected') DEFAULT 'pending',
  `expiry_date` date DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_documents_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_documents`
--

LOCK TABLES `user_documents` WRITE;
/*!40000 ALTER TABLE `user_documents` DISABLE KEYS */;
INSERT INTO `user_documents` VALUES (1,2,'citizenship','uploads/documents/2_citizenship_1774541408.jpg','verified','2083-02-22','2026-03-26 16:10:08'),(2,2,'license','uploads/documents/2_license_1774541592.jpg','verified','2026-03-27','2026-03-26 16:13:12'),(4,3,'citizenship','uploads/documents/3_citizenship_1778818264.png','verified','2026-05-15','2026-05-15 04:11:04');
/*!40000 ALTER TABLE `user_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `role` enum('customer','admin') DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `two_factor_secret` varchar(255) DEFAULT NULL,
  `two_factor_enabled` tinyint(1) DEFAULT 0,
  `two_factor_type` enum('none','totp','email') DEFAULT 'none',
  `backup_codes` text DEFAULT NULL,
  `facebook_id` varchar(100) DEFAULT NULL,
  `auth_provider` enum('standard','facebook') DEFAULT 'standard',
  `profile_photo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin','admin@nepalridehub.com','$2y$10$d6NGC8ddbpCoKtRguTMLSeXtplx5MJ0NWKlmxKS2NP2KlWY02tQoy','9769777947','kathmandu','Nepal','admin','2026-03-25 12:41:12',NULL,0,'none',NULL,NULL,'standard',NULL),(2,'rehan habib','samshedkhan741@gmail.com','$2y$10$x0qWeO3N/A94fzcZKLm3ne0aX6GTUg9NtccGvQyvlTiOVW9jL9fo2','9807735375','bagbazar','Nepal','customer','2026-03-25 12:44:42',NULL,0,'none',NULL,NULL,'standard',NULL),(3,'suraj','google_user@example.com','$2y$10$rZE5LDECGx/cxTJZ4mkDfupMy2fPdSydojAjU5HLxniuo0afeBWSe','980223465',NULL,NULL,'customer','2026-03-31 06:50:30',NULL,0,'none',NULL,NULL,'standard',NULL),(6,'TestUser','test@example.com','$2y$10$vubAdbR54QT1SsIfP5.bpu4iQ8LyPCMDgQc.IVA.mSKkINrTy9Dju','0000000000',NULL,NULL,'customer','2026-04-02 02:35:42',NULL,0,'none',NULL,NULL,'standard',NULL),(7,'Sahid Khan','sahidkhan777777778@gmail.com','$2y$10$PpyR7rVbXI80EEWErXYtJe7EHIQZosJxZa/0u1FhvkWQOfaG1rdle','981546727','bagbazar','Nepal','customer','2026-04-09 12:40:35',NULL,0,'none',NULL,NULL,'',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehicles`
--

DROP TABLE IF EXISTS `vehicles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vehicles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `type` enum('car','bike','bus','taxi','jeep','van') DEFAULT NULL,
  `condition_type` enum('city','offroad','highway','all-terrain') DEFAULT 'city',
  `brand` varchar(50) NOT NULL,
  `model_year` int(11) NOT NULL,
  `price_per_day` decimal(10,2) NOT NULL,
  `status` enum('available','maintenance','booked') DEFAULT 'available',
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `gps_lat` decimal(10,8) DEFAULT NULL,
  `gps_lng` decimal(11,8) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicles`
--

LOCK TABLES `vehicles` WRITE;
/*!40000 ALTER TABLE `vehicles` DISABLE KEYS */;
INSERT INTO `vehicles` VALUES (2,'Bajaj Pulsar NS200','bike','highway','Bajaj',2023,1200.00,'available','The ultimate naked sports bike with a perimeter frame and liquid-cooled engine, offering high performance and great fuel efficiency.','uploads/vehicles/bajaj_pulsar_ns200.png',28.20950000,83.95890000,'2026-04-10 15:49:13'),(3,'BMW G 310 R','bike','city','BMW',2024,4500.00,'available','Premium compact roadster from BMW Motorrad. Agile, easy to handle, and optimized for pure riding pleasure.','uploads/vehicles/bmw_g310r.png',27.57500000,84.48900000,'2026-04-10 15:49:13'),(4,'Royal Enfield Classic 350','bike','highway','Royal Enfield',2023,2500.00,'available','The timeless classic. Heavy-duty construction with a smooth J-series engine, ideal for long-distance cruising across Nepal.','uploads/vehicles/classic_350.png',27.48300000,83.27500000,'2026-04-10 15:49:13'),(5,'Toyota Corolla','car','highway','Toyota',2022,6000.00,'available','The world’s best-selling car. Unmatched reliability, comfort, and safety features for your family trips.','uploads/vehicles/toyota_corolla.png',27.71700000,85.52100000,'2026-04-10 15:49:13'),(6,'BMW 3 Series','car','city','BMW',2023,18000.00,'available','A luxury sedan that defines sporty elegance. Experience ultimate comfort and cutting-edge technology.','uploads/vehicles/bmw_3series.png',27.71200000,85.31300000,'2026-04-10 15:49:13'),(7,'Mahindra XUV700','car','all-terrain','Mahindra',2024,8500.00,'available','A feature-packed SUV with 5-star safety rating and ADAS technology. Spacious and powerful for all terrains.','uploads/vehicles/mahindra_xuv700.png',28.20950000,83.95890000,'2026-04-10 15:49:13'),(8,'Mahindra Thar 4x4','jeep','offroad','Mahindra',2024,10000.00,'available','The ultimate off-roader. Rugged, capable, and iconic. Perfect for exploring the high Himalayas of Nepal.','uploads/vehicles/mahindra_thar.png',27.57500000,84.48900000,'2026-04-10 15:49:13'),(9,'Toyota Hiace','van','highway','Toyota',2022,12000.00,'available','A reliable 14-seater van for group tours and large families. Spacious, comfortable, and efficient.','uploads/vehicles/toyata.png',27.48300000,83.27500000,'2026-04-10 15:49:13'),(10,'Ashok Leyland Tourist Bus','bus','highway','Ashok Leyland',2023,25000.00,'available','35-seater luxury AC bus for large group travel, school trips, and corporate events across Nepal.','uploads/vehicles/Tour.png',27.71700000,85.52100000,'2026-04-10 15:49:13'),(11,'KTM RC','bike','highway','KTM',2023,3500.00,'available','Light, powerful and packed with state-of-the-art technology, it guarantees a thrilling ride, whether you’re in the urban jungle or a forest of bends.','uploads/vehicles/yamahaR15.png',27.71200000,85.31300000,'2026-04-10 15:49:13'),(12,'Hyundai i20','car','city','Hyundai',2024,5000.00,'available','A premium hatchback with a sophisticated look and feel. Perfect for city driving with its smooth handling and modern features.','uploads/vehicles/newcar.png',28.20950000,83.95890000,'2026-04-10 15:49:13'),(13,'Suzuki Swift','car','city','Suzuki',2024,4500.00,'available','Fun to drive, easy to park, and extremely fuel-efficient. The Swift is the perfect urban companion for your daily needs.','uploads/vehicles/newcar.png',27.57500000,84.48900000,'2026-04-10 15:49:13'),(14,'Force Traveller','van','highway','Force',2023,15000.00,'available','A versatile mini-bus with comfortable seating for up to 17 passengers. Ideal for long trips and group travel.','uploads/vehicles/force_traveller.png',27.48300000,83.27500000,'2026-04-10 15:49:13'),(15,'Isuzu D-Max','jeep','all-terrain','Isuzu',2023,12000.00,'available','A powerful pickup truck that can handle anything you throw at it. Rugged, reliable, and built to last.','uploads/vehicles/mahindra_xuv700.png',27.71700000,85.52100000,'2026-04-10 15:49:13');
/*!40000 ALTER TABLE `vehicles` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-15 11:25:56
