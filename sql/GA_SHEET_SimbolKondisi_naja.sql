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

-- Dumping structure for table ga-sheet.simbol_kondisi
CREATE TABLE IF NOT EXISTS `simbol_kondisi` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `user_update_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dumping data for table ga-sheet.simbol_kondisi: ~13 rows (approximately)
INSERT INTO `simbol_kondisi` (`id`, `code`, `name`, `is_active`, `user_id`, `user_update_id`, `created_at`, `updated_at`) VALUES
	(1, '01', 'Bocor', 1, 1, NULL, '2024-07-01 06:46:20', '2024-07-01 06:46:22'),
	(2, '02', 'Hancur', 0, 2, NULL, '2024-07-03 04:11:33', '2024-07-03 04:11:33'),
	(3, '02', 'ilang', 1, 2, NULL, '2024-07-03 18:36:13', '2024-07-03 18:36:13'),
	(4, '02', 'putus', 0, 2, NULL, '2024-07-03 21:19:25', '2024-07-03 21:19:25'),
	(5, '02', 'dsa', 0, 2, NULL, '2024-07-04 01:03:27', '2024-07-04 01:03:27'),
	(6, '02', 'jdsda', 0, 2, NULL, '2024-07-04 01:04:24', '2024-07-04 01:04:24'),
	(7, '02', 's', 0, 2, NULL, '2024-07-04 01:52:57', '2024-07-04 01:52:57'),
	(8, '03', 'aaaa', 0, 2, NULL, '2024-07-04 06:32:28', '2024-07-04 06:32:28'),
	(9, '05', 'TEST', 1, 2, NULL, '2024-07-04 18:38:20', '2024-07-04 18:38:20'),
	(10, '001', 'dfasdfdsa', 0, 2, NULL, '2024-07-04 23:31:56', '2024-07-04 23:31:56'),
	(11, '002', 'dfasfdsa', 0, 2, NULL, '2024-07-04 23:34:43', '2024-07-04 23:34:43'),
	(12, '0043', 'nda', 0, 2, NULL, '2024-07-07 03:09:40', '2024-07-07 03:09:40'),
	(13, '021', 'Kotor', 0, 2, NULL, '2024-07-07 04:53:13', '2024-07-07 04:53:13');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
