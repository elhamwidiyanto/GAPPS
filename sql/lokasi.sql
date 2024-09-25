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

-- Dumping structure for table ga-sheet.maintenance_tx
CREATE TABLE IF NOT EXISTS `maintenance_tx` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `date` timestamp NULL DEFAULT NULL,
  `description` text DEFAULT NULL,
  `alat_id` bigint(20) DEFAULT NULL,
  `alat_name` varchar(255) DEFAULT NULL,
  `simbol_kondisi_id` bigint(20) DEFAULT NULL,
  `simbol_kondisi_name` varchar(255) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `reason_reject` text DEFAULT NULL,
  `reason_cancel` text DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `user_nip` varchar(255) DEFAULT NULL,
  `user_phone` varchar(20) DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `company_id` bigint(20) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `department_id` bigint(20) DEFAULT NULL,
  `department_name` varchar(255) DEFAULT NULL,
  `user_head_id` bigint(20) DEFAULT NULL,
  `user_head_nip` varchar(255) DEFAULT NULL,
  `user_head_name` varchar(255) DEFAULT NULL,
  `user_head_email` varchar(255) DEFAULT NULL,
  `user_head_phone` varchar(20) DEFAULT NULL,
  `user_update_id` bigint(20) DEFAULT NULL,
  `start_date` timestamp NULL DEFAULT NULL,
  `finish_date` timestamp NULL DEFAULT NULL,
  `pic_id` bigint(20) DEFAULT NULL,
  `pic_nip` varchar(255) DEFAULT NULL,
  `pic_name` varchar(255) DEFAULT NULL,
  `pic_email` varchar(255) DEFAULT NULL,
  `pic_phone` varchar(20) DEFAULT NULL,
  `category_id` bigint(20) DEFAULT NULL,
  `category_name` varchar(255) DEFAULT NULL,
  `number_of_work_id` bigint(20) DEFAULT NULL,
  `number_of_work_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dumping data for table ga-sheet.maintenance_tx: ~0 rows (approximately)
INSERT INTO `maintenance_tx` (`id`, `date`, `description`, `alat_id`, `alat_name`, `simbol_kondisi_id`, `simbol_kondisi_name`, `status`, `reason_reject`, `reason_cancel`, `user_id`, `user_nip`, `user_phone`, `user_name`, `company_id`, `company_name`, `department_id`, `department_name`, `user_head_id`, `user_head_nip`, `user_head_name`, `user_head_email`, `user_head_phone`, `user_update_id`, `start_date`, `finish_date`, `pic_id`, `pic_nip`, `pic_name`, `pic_email`, `pic_phone`, `category_id`, `category_name`, `number_of_work_id`, `number_of_work_name`, `created_at`, `updated_at`) VALUES
	(1, '2024-07-08 18:36:37', 'Rusak Berat', 1, 'Proyektor', 1, 'Hancur', 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naja', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- Dumping structure for table ga-sheet.master_gedung
CREATE TABLE IF NOT EXISTS `master_gedung` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `id_lokasi` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_update_id` bigint(20) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dumping data for table ga-sheet.master_gedung: ~2 rows (approximately)
INSERT INTO `master_gedung` (`id`, `user_id`, `id_lokasi`, `name`, `created_at`, `updated_at`, `user_update_id`, `is_active`) VALUES
	(4, 2, 6, 'HO', '2024-07-12 09:33:27', '2024-07-12 09:33:43', NULL, 1),
	(5, 2, 7, 'TULT', '2024-07-15 02:15:13', '2024-07-15 02:15:14', NULL, 1);

-- Dumping structure for table ga-sheet.master_lokasi
CREATE TABLE IF NOT EXISTS `master_lokasi` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_update_id` bigint(20) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dumping data for table ga-sheet.master_lokasi: ~2 rows (approximately)
INSERT INTO `master_lokasi` (`id`, `user_id`, `name`, `created_at`, `updated_at`, `user_update_id`, `is_active`) VALUES
	(6, 2, 'Jakarta', '2024-07-15 02:16:37', '2024-07-15 02:16:38', NULL, 1),
	(7, 2, 'Bandung', '2024-07-15 02:16:48', '2024-07-15 02:16:49', NULL, 1);

-- Dumping structure for table ga-sheet.master_ruangan
CREATE TABLE IF NOT EXISTS `master_ruangan` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `id_gedung` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_update_id` bigint(20) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dumping data for table ga-sheet.master_ruangan: ~2 rows (approximately)
INSERT INTO `master_ruangan` (`id`, `user_id`, `id_gedung`, `name`, `created_at`, `updated_at`, `user_update_id`, `is_active`) VALUES
	(3, 2, 5, '12.11', '2024-07-15 02:17:02', '2024-07-15 02:17:02', NULL, 1),
	(4, 2, 4, 'Cendana', '2024-07-15 02:17:15', '2024-07-15 02:17:16', NULL, 1);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
