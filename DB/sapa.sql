-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 19, 2026 at 05:33 PM
-- Server version: 9.1.0
-- PHP Version: 8.1.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sapa`
--

-- --------------------------------------------------------

--
-- Table structure for table `akses`
--

DROP TABLE IF EXISTS `akses`;
CREATE TABLE IF NOT EXISTS `akses` (
  `id_akses` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_akses` varchar(255) NOT NULL,
  `kontak_akses` varchar(255) NOT NULL,
  `akses` enum('Admin','Manajer Mutu','Direktur') NOT NULL,
  `image_akses` varchar(255) DEFAULT NULL,
  `datetime_update` datetime NOT NULL,
  PRIMARY KEY (`id_akses`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `akses_login`
--

DROP TABLE IF EXISTS `akses_login`;
CREATE TABLE IF NOT EXISTS `akses_login` (
  `id_akses_login` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_akses` int UNSIGNED NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `date_creat` datetime NOT NULL,
  `date_expired` datetime NOT NULL,
  PRIMARY KEY (`id_akses_login`),
  KEY `id_akses` (`id_akses`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `respondent`
--

DROP TABLE IF EXISTS `respondent`;
CREATE TABLE IF NOT EXISTS `respondent` (
  `id_respondent` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `respondent_name` varchar(255) NOT NULL COMMENT 'Nama lengkap responden',
  `respondent_id_type` varchar(255) NOT NULL COMMENT 'No RM, NIK, BPJS DLL',
  `respondent_id_value` varchar(255) NOT NULL COMMENT 'Nilai dari nomor identitas yang digunakan',
  `respondent_sex` enum('Male','Female') NOT NULL,
  `respondent_brithdate` date DEFAULT NULL,
  PRIMARY KEY (`id_respondent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_email_gateway`
--

DROP TABLE IF EXISTS `setting_email_gateway`;
CREATE TABLE IF NOT EXISTS `setting_email_gateway` (
  `id_setting_email_gateway` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `email_gateway` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `password_gateway` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `url_provider` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `port_gateway` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `nama_pengirim` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `url_service` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`id_setting_email_gateway`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `setting_general`
--

DROP TABLE IF EXISTS `setting_general`;
CREATE TABLE IF NOT EXISTS `setting_general` (
  `id_setting_general` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `title_page` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `kata_kunci` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `alamat_bisnis` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email_bisnis` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `telepon_bisnis` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `favicon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `base_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`id_setting_general`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `setting_simrs`
--

DROP TABLE IF EXISTS `setting_simrs`;
CREATE TABLE IF NOT EXISTS `setting_simrs` (
  `id_setting_simrs` int NOT NULL AUTO_INCREMENT,
  `url_simrs` varchar(255) NOT NULL,
  `client_id` char(36) NOT NULL,
  `client_key` char(36) NOT NULL,
  `status` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id_setting_simrs`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_wa`
--

DROP TABLE IF EXISTS `setting_wa`;
CREATE TABLE IF NOT EXISTS `setting_wa` (
  `id_setting_wa` int NOT NULL AUTO_INCREMENT,
  `url_service` varchar(255) NOT NULL,
  `api_key` varchar(255) NOT NULL,
  `max_account` int NOT NULL,
  `max_connection` int NOT NULL COMMENT 'Dalam satuan detik',
  PRIMARY KEY (`id_setting_wa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `survey_answer`
--

DROP TABLE IF EXISTS `survey_answer`;
CREATE TABLE IF NOT EXISTS `survey_answer` (
  `id_survey_answer` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_survey_session` int UNSIGNED NOT NULL,
  `id_survey_question` int UNSIGNED NOT NULL,
  `answer_text` text NOT NULL,
  PRIMARY KEY (`id_survey_answer`),
  KEY `answer_to_session` (`id_survey_session`),
  KEY `answer_to_question` (`id_survey_question`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `survey_question`
--

DROP TABLE IF EXISTS `survey_question`;
CREATE TABLE IF NOT EXISTS `survey_question` (
  `id_survey_question` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_survey_session` int UNSIGNED NOT NULL,
  `question_type` enum('number','decimal','text','coded','boolean') NOT NULL,
  `question_text` text NOT NULL COMMENT 'Text pertanyaan',
  `alternative_answers` json DEFAULT NULL COMMENT 'Alternatif jawaban',
  PRIMARY KEY (`id_survey_question`),
  KEY `question_session` (`id_survey_session`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `survey_session`
--

DROP TABLE IF EXISTS `survey_session`;
CREATE TABLE IF NOT EXISTS `survey_session` (
  `id_survey_session` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `survey_name` varchar(255) NOT NULL,
  `datetime_start` datetime NOT NULL,
  `datetime_end` datetime NOT NULL,
  PRIMARY KEY (`id_survey_session`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `akses_login`
--
ALTER TABLE `akses_login`
  ADD CONSTRAINT `akses_login_to_akses` FOREIGN KEY (`id_akses`) REFERENCES `akses` (`id_akses`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `survey_answer`
--
ALTER TABLE `survey_answer`
  ADD CONSTRAINT `answer_to_question` FOREIGN KEY (`id_survey_question`) REFERENCES `survey_question` (`id_survey_question`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `answer_to_session` FOREIGN KEY (`id_survey_session`) REFERENCES `survey_session` (`id_survey_session`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `survey_question`
--
ALTER TABLE `survey_question`
  ADD CONSTRAINT `question_session` FOREIGN KEY (`id_survey_session`) REFERENCES `survey_session` (`id_survey_session`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
