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

-- Dumping structure for table ga-sheet.cleaning_header
CREATE TABLE IF NOT EXISTS `cleaning_header` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `date` timestamp NULL DEFAULT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dumping data for table ga-sheet.cleaning_header: ~0 rows (approximately)

-- Dumping structure for table ga-sheet.cleaning_tx
CREATE TABLE IF NOT EXISTS `cleaning_tx` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `cleaning_header_id` bigint(20) DEFAULT 0,
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
  `lokasi_id` bigint(20) DEFAULT NULL,
  `gedung_id` bigint(20) DEFAULT NULL,
  `ruangan_id` bigint(20) DEFAULT NULL,
  `lokasi_name` varchar(255) DEFAULT NULL,
  `gedung_name` varchar(255) DEFAULT NULL,
  `ruangan_name` varchar(255) DEFAULT NULL,
  `number_of_work_name` varchar(255) DEFAULT NULL,
  `is_checked` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=COMPACT;

-- Dumping data for table ga-sheet.cleaning_tx: ~3 rows (approximately)
INSERT INTO `cleaning_tx` (`id`, `cleaning_header_id`, `date`, `description`, `alat_id`, `alat_name`, `simbol_kondisi_id`, `simbol_kondisi_name`, `status`, `reason_reject`, `reason_cancel`, `user_id`, `user_nip`, `user_phone`, `user_name`, `company_id`, `company_name`, `department_id`, `department_name`, `user_head_id`, `user_head_nip`, `user_head_name`, `user_head_email`, `user_head_phone`, `user_update_id`, `start_date`, `finish_date`, `pic_id`, `pic_nip`, `pic_name`, `pic_email`, `pic_phone`, `category_id`, `category_name`, `number_of_work_id`, `lokasi_id`, `gedung_id`, `ruangan_id`, `lokasi_name`, `gedung_name`, `ruangan_name`, `number_of_work_name`, `is_checked`, `created_at`, `updated_at`) VALUES
	(1, 0, '2024-07-24 20:03:57', 'fdsfdsfds', 4, 'Pel', 3, 'Pembersihan', 1, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2024-07-24 20:03:57', '2024-07-24 20:22:57'),
	(2, 0, '2024-07-24 20:05:07', 'fsdfdsfsd', 4, 'Pel', 3, 'Pembersihan', 1, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5, '11032156564', 'Sulton', 'sul@testr.com', '08546542321', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2024-07-24 20:05:07', '2024-07-24 20:05:07'),
	(3, 0, '2024-07-24 20:06:29', 'dfsfdsfds', 4, 'Pel', 3, 'Pembersihan', 1, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5, '11032156564', 'Sulton', 'sul@testr.com', '08546542321', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2024-07-24 20:06:29', '2024-07-24 20:06:29');

-- Dumping structure for table ga-sheet.cleaning_tx_history
CREATE TABLE IF NOT EXISTS `cleaning_tx_history` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `cleaning_header_id` bigint(20) DEFAULT 0,
  `cleaning_tx_id` bigint(20) DEFAULT 0,
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
  `is_checked` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=COMPACT;

-- Dumping data for table ga-sheet.cleaning_tx_history: ~1 rows (approximately)
INSERT INTO `cleaning_tx_history` (`id`, `cleaning_header_id`, `cleaning_tx_id`, `date`, `description`, `alat_id`, `alat_name`, `simbol_kondisi_id`, `simbol_kondisi_name`, `status`, `reason_reject`, `reason_cancel`, `user_id`, `user_nip`, `user_phone`, `user_name`, `company_id`, `company_name`, `department_id`, `department_name`, `user_head_id`, `user_head_nip`, `user_head_name`, `user_head_email`, `user_head_phone`, `user_update_id`, `start_date`, `finish_date`, `pic_id`, `pic_nip`, `pic_name`, `pic_email`, `pic_phone`, `category_id`, `category_name`, `number_of_work_id`, `number_of_work_name`, `is_checked`, `created_at`, `updated_at`) VALUES
	(1, 0, 0, '2024-07-24 20:06:29', 'dfsfdsfds', 4, 'Pel', 3, 'Pembersihan', 1, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5, '11032156564', 'Sulton', 'sul@testr.com', '08546542321', NULL, NULL, NULL, NULL, 1, '2024-07-24 20:06:29', '2024-07-24 20:06:29');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
