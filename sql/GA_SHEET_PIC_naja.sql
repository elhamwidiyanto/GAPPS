-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.4.32-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             12.7.0.6850
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for ga-sheet
CREATE DATABASE IF NOT EXISTS `ga-sheet` /*!40100 DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci */;
USE `ga-sheet`;

-- Dumping structure for table ga-sheet.pic
CREATE TABLE IF NOT EXISTS `pic` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `pic_name` varchar(255) DEFAULT NULL,
  `pic_phone` varchar(255) DEFAULT NULL,
  `pic_email` varchar(255) DEFAULT NULL,
  `pic_nip` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `company_id` bigint(20) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `departement_id` bigint(20) DEFAULT NULL,
  `departement_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dumping data for table ga-sheet.pic: ~9 rows (approximately)
INSERT INTO `pic` (`id`, `user_id`, `pic_name`, `pic_phone`, `pic_email`, `pic_nip`, `updated_at`, `created_at`, `company_id`, `company_name`, `departement_id`, `departement_name`) VALUES
	(1, 1, 'Naja', '085156513756', 'naja@test.com', '1006545112', '2024-07-03 19:34:51', '2024-07-03 19:34:51', 1, 'SEA', 1, 'IT'),
	(5, 2, 'Sulton', '08546542321', 'sul@testr.com', '11032156564', '2024-07-05 01:45:50', '2024-07-03 21:18:28', NULL, 'TELU', NULL, 'PR'),
	(6, 2, 'Apep', '024568974564', 'apip@test.com', '1103213085', '2024-07-03 23:44:36', '2024-07-03 23:44:36', NULL, 'TELU', NULL, 'IT'),
	(7, 2, 'Dani', '+62 851578347', 'dani@test.com', '100054548', '2024-07-07 05:12:19', '2024-07-07 05:12:19', NULL, 'TELU', NULL, 'IT'),
	(8, 2, 'kjgj', '085156548987', 'lkhkj@kjk.com', '111032165', '2024-07-07 11:57:33', '2024-07-07 05:17:41', NULL, 'SMK Telkom Malang', NULL, 'LO'),
	(9, 2, 'Yuda', '+62 85654525984', 'yud@test.com', '1103236456', '2024-07-07 05:44:52', '2024-07-07 05:44:52', NULL, 'SMK Telkom Malang', NULL, 'TU'),
	(10, 2, 'trgdsfgd', '+62 85697067926', 'maulanaja4@gmail.com', '3213213432', '2024-07-07 06:12:23', '2024-07-07 06:12:23', NULL, 'SMK Telkom Malang', NULL, 'GG'),
	(11, 2, 'dfsfdafs', '+62 85697067926', 'maulanaja4@gmail.com', '121321321', '2024-07-07 11:44:19', '2024-07-07 11:44:19', NULL, 'SMK Telkom Malang', NULL, 'AAA'),
	(12, 2, 'sdadsadsa', '085697067926', 'maulanaja4@gmail.com', '12312321', '2024-07-07 11:50:24', '2024-07-07 11:46:51', NULL, 'SMK Telkom Malang', NULL, 'BBBB'),
	(13, 2, 'fafsada', '085697067926', 'maulanaja4@gmail.com', '212132131', '2024-07-07 11:48:17', '2024-07-07 11:48:17', NULL, 'SMK Telkom Malang', NULL, 'CCCCC');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
