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

-- Dumping structure for table ga-sheet.alat
CREATE TABLE IF NOT EXISTS `alat` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `user_update_id` bigint(20) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `location_id` bigint(20) DEFAULT NULL,
  `gedung_id` bigint(20) DEFAULT NULL,
  `ruangan_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dumping data for table ga-sheet.alat: ~5 rows (approximately)
INSERT INTO `alat` (`id`, `name`, `is_active`, `user_id`, `user_update_id`, `type`, `location_id`, `gedung_id`, `ruangan_id`, `created_at`, `updated_at`) VALUES
	(1, 'LAPTOP', 1, 2, NULL, 2, 6, 4, 4, '2024-07-04 07:39:17', '2024-07-04 18:25:43'),
	(2, 'monitor', 1, 2, NULL, 2, 6, 4, 4, '2024-07-04 08:12:01', '2024-07-04 18:24:58'),
	(3, 'APAR', 1, 2, NULL, 2, 6, 4, 4, '2024-07-04 18:25:16', '2024-07-04 18:25:16'),
	(4, 'Pel', 1, 2, NULL, 1, 6, 4, 4, '2024-07-12 07:28:10', '2024-07-12 07:28:11'),
	(5, 'Sapu', 1, 2, NULL, 1, 6, 4, 4, '2024-07-12 07:28:27', '2024-07-12 07:28:28');

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
  `is_checked` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dumping data for table ga-sheet.maintenance_tx: ~1 rows (approximately)
INSERT INTO `maintenance_tx` (`id`, `date`, `description`, `alat_id`, `alat_name`, `simbol_kondisi_id`, `simbol_kondisi_name`, `status`, `reason_reject`, `reason_cancel`, `user_id`, `user_nip`, `user_phone`, `user_name`, `company_id`, `company_name`, `department_id`, `department_name`, `user_head_id`, `user_head_nip`, `user_head_name`, `user_head_email`, `user_head_phone`, `user_update_id`, `start_date`, `finish_date`, `pic_id`, `pic_nip`, `pic_name`, `pic_email`, `pic_phone`, `category_id`, `category_name`, `number_of_work_id`, `number_of_work_name`, `is_checked`, `created_at`, `updated_at`) VALUES
	(1, '2024-07-08 18:36:37', 'Rusak Berat', 1, 'Proyektor', 1, 'Hancur', 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Naja', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- Dumping structure for table ga-sheet.master_gedung
CREATE TABLE IF NOT EXISTS `master_gedung` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `id_lokasi` bigint(20) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_update_id` bigint(20) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dumping data for table ga-sheet.master_gedung: ~9 rows (approximately)
INSERT INTO `master_gedung` (`id`, `user_id`, `id_lokasi`, `name`, `created_at`, `updated_at`, `user_update_id`, `is_active`) VALUES
	(4, 2, 6, 'HO', '2024-07-12 09:33:27', '2024-07-12 09:33:43', NULL, 1),
	(5, 2, 7, 'TULT', '2024-07-15 02:15:13', '2024-07-15 02:15:14', NULL, 1),
	(6, 2, 7, 'GEDUNG A', '2024-07-15 06:10:12', '2024-07-15 06:10:13', NULL, 1),
	(7, 2, 6, 'Lorem Ipsum', '2024-07-15 06:10:36', '2024-07-15 01:52:01', 2, 1),
	(8, 2, NULL, 'GEDUNG C', '2024-07-15 01:52:55', '2024-07-15 01:52:55', NULL, 1),
	(9, 2, 7, 'D', '2024-07-15 02:29:05', '2024-07-15 02:29:14', 2, 0),
	(10, 2, 6, 'PIK', '2024-07-15 02:43:56', '2024-07-15 02:43:56', NULL, 1),
	(11, 2, 7, 'A', '2024-07-15 08:28:54', '2024-07-15 08:28:54', NULL, 1),
	(12, 2, 7, 'TEST Bandung', '2024-07-15 19:01:54', '2024-07-15 19:02:12', 2, 1);

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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dumping data for table ga-sheet.master_lokasi: ~3 rows (approximately)
INSERT INTO `master_lokasi` (`id`, `user_id`, `name`, `created_at`, `updated_at`, `user_update_id`, `is_active`) VALUES
	(6, 2, 'Jakarta', '2024-07-15 02:16:37', '2024-07-15 02:16:38', NULL, 1),
	(7, 2, 'Bandung', '2024-07-15 02:16:48', '2024-07-15 02:16:49', NULL, 1),
	(8, 2, 'Ciracas', '2024-07-15 02:19:15', '2024-07-15 02:19:25', 2, 0);

-- Dumping structure for table ga-sheet.master_ruangan
CREATE TABLE IF NOT EXISTS `master_ruangan` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `id_gedung` bigint(20) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_update_id` bigint(20) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dumping data for table ga-sheet.master_ruangan: ~8 rows (approximately)
INSERT INTO `master_ruangan` (`id`, `user_id`, `id_gedung`, `name`, `created_at`, `updated_at`, `user_update_id`, `is_active`) VALUES
	(3, 2, 5, '12.11', '2024-07-15 02:17:02', '2024-07-15 02:17:02', NULL, 1),
	(4, 2, 4, 'Cendana', '2024-07-15 02:17:15', '2024-07-15 02:17:16', NULL, 1),
	(5, 2, 4, 'IT', '2024-07-15 06:11:34', '2024-07-15 06:11:35', NULL, 1),
	(6, 2, 5, '14.15', '2024-07-15 06:11:47', '2024-07-15 06:11:48', NULL, 1),
	(7, 2, 5, 'LAA', '2024-07-15 06:12:00', '2024-07-15 02:19:44', 2, 0),
	(8, 2, NULL, '11.14', '2024-07-15 02:19:57', '2024-07-15 02:19:57', NULL, 1),
	(9, 2, 5, '11.15', '2024-07-15 08:49:23', '2024-07-15 08:49:23', NULL, 1),
	(10, 2, 5, '11.11', '2024-07-15 08:49:44', '2024-07-15 08:49:44', NULL, 1);

-- Dumping structure for table ga-sheet.simbol_kondisi
CREATE TABLE IF NOT EXISTS `simbol_kondisi` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `user_update_id` bigint(20) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `location_id` bigint(20) DEFAULT NULL,
  `gedung_id` bigint(20) DEFAULT NULL,
  `ruangan_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dumping data for table ga-sheet.simbol_kondisi: ~5 rows (approximately)
INSERT INTO `simbol_kondisi` (`id`, `code`, `name`, `is_active`, `user_id`, `user_update_id`, `type`, `location_id`, `gedung_id`, `ruangan_id`, `created_at`, `updated_at`) VALUES
	(1, '01', 'Bocor', 1, 2, NULL, 1, 6, 4, 4, '2024-07-01 06:46:20', '2024-07-01 06:46:22'),
	(2, '02', 'Perbaikan', 1, 2, NULL, 2, 6, 4, 4, '2024-07-02 00:06:41', '2024-07-12 00:04:48'),
	(3, '03', 'Pembersihan', 1, 2, NULL, 1, 6, 4, 4, '2024-07-02 00:17:25', '2024-07-12 00:23:13'),
	(5, '05', 'Putus', 1, 2, NULL, 2, 6, 4, 4, '2024-07-11 19:36:57', '2024-07-12 02:53:58'),
	(7, '08', 'Pecah', 1, 2, NULL, 1, 6, 4, 4, '2024-07-14 18:35:50', '2024-07-14 18:35:50');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
