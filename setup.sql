-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 12, 2026 at 06:05 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `assessment_db`
--
CREATE DATABASE IF NOT EXISTS `assessment_db`;
USE `assessment_db`;

-- --------------------------------------------------------

--
-- Table structure for table `assessments`
--

CREATE TABLE IF NOT EXISTS `assessments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `assessment_type` varchar(100) DEFAULT NULL,
  `assigned_to` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `code` varchar(50) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `batch_year` varchar(20) DEFAULT NULL,
  `status` enum('Draft','Active') DEFAULT 'Draft',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `instructions` text DEFAULT NULL,
  `shuffle_questions` tinyint(1) DEFAULT 0,
  `shuffle_options` tinyint(1) DEFAULT 0,
  `proctored_exam` tinyint(1) DEFAULT 0,
  `browser_lockdown` tinyint(1) DEFAULT 0,
  `show_results` tinyint(1) DEFAULT 0,
  `allow_backtracking` tinyint(1) DEFAULT 0,
  `pass_mark` int(11) DEFAULT 50,
  `attempts` int(11) DEFAULT 1,
  `add_video` tinyint(1) NOT NULL DEFAULT 0,
  `intro_videos` text DEFAULT NULL,
  `pedagogy` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `employees`
--

CREATE TABLE IF NOT EXISTS `employees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` varchar(100) DEFAULT NULL,
  `college` varchar(255) DEFAULT NULL,
  `type` enum('internal','recruitment') DEFAULT 'internal',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `templates`
--

CREATE TABLE IF NOT EXISTS `templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `template_sections`
--

CREATE TABLE IF NOT EXISTS `template_sections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(11) NOT NULL,
  `marks_type` varchar(50) NOT NULL,
  `num_questions` int(11) NOT NULL,
  `knowledge_type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `template_id` (`template_id`),
  CONSTRAINT `template_sections_ibfk_1` FOREIGN KEY (`template_id`) REFERENCES `templates` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `test_packs`
--

CREATE TABLE IF NOT EXISTS `test_packs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `assessment_id` int(11) NOT NULL,
  `pack_name` varchar(255) NOT NULL,
  `user_role` varchar(100) NOT NULL,
  `template_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `assessment_id` (`assessment_id`),
  KEY `template_id` (`template_id`),
  CONSTRAINT `test_packs_ibfk_1` FOREIGN KEY (`assessment_id`) REFERENCES `assessments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `test_packs_ibfk_2` FOREIGN KEY (`template_id`) REFERENCES `templates` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `questions`
--

CREATE TABLE IF NOT EXISTS `questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `test_pack_id` int(11) NOT NULL,
  `template_id` int(11) DEFAULT NULL,
  `section_idx` int(11) DEFAULT 0,
  `type` varchar(50) NOT NULL,
  `question` text DEFAULT NULL,
  `option_a` text DEFAULT NULL,
  `option_b` text DEFAULT NULL,
  `option_c` text DEFAULT NULL,
  `option_d` text DEFAULT NULL,
  `correct_answer` varchar(10) DEFAULT NULL,
  `marks` int(11) NOT NULL,
  `knowledge_type` varchar(255) DEFAULT NULL,
  `pedagogy` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `question_bank_repositories`
--

CREATE TABLE IF NOT EXISTS `question_bank_repositories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `question_bank`
--

CREATE TABLE IF NOT EXISTS `question_bank` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `repository_id` int(11) UNSIGNED DEFAULT NULL,
  `question` text NOT NULL,
  `type` varchar(50) NOT NULL,
  `option_a` text DEFAULT NULL,
  `option_b` text DEFAULT NULL,
  `option_c` text DEFAULT NULL,
  `option_d` text DEFAULT NULL,
  `correct_answer` text DEFAULT NULL,
  `marks` int(11) NOT NULL DEFAULT 1,
  `category` varchar(255) DEFAULT NULL,
  `difficulty` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `pedagogy` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping sample data
--

INSERT INTO `employees` (`name`, `email`, `type`) VALUES 
('John Doe', 'john.doe@company.com', 'internal'),
('Jane Smith', 'jane.smith@company.com', 'internal'),
('Aditya Kumar', 'aditya.k@gmail.com', 'recruitment');

INSERT INTO `question_bank_repositories` (`name`) VALUES ('General');

COMMIT;
