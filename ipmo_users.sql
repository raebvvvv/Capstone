-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 06, 2025 at 08:48 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ipmo_users`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_notifications`
--

CREATE TABLE `admin_notifications` (
  `id` int(11) NOT NULL,
  `submission_id` int(11) NOT NULL,
  `submission_code` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL,
  `doc_type` varchar(100) NOT NULL,
  `message` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_notifications`
--

INSERT INTO `admin_notifications` (`id`, `submission_id`, `submission_code`, `user_id`, `doc_type`, `message`, `created_at`, `is_read`) VALUES
(64, 53, 'SRID-2025-20251006-1', 1, 'approval_sheet', 'User #1 re-uploaded approval sheet (SRID-2025-20251006-1)', '2025-10-06 12:25:42', 1),
(65, 53, 'SRID-2025-20251006-1', 1, 'approval_sheet', 'User #1 re-uploaded approval sheet (SRID-2025-20251006-1)', '2025-10-06 12:26:53', 1),
(66, 53, 'SRID-2025-20251006-1', 1, 'full_manuscript', 'User #1 re-uploaded full manuscript (SRID-2025-20251006-1)', '2025-10-06 12:27:59', 1),
(67, 61, 'SRID-2025-20251007-1', 1, 'approval_sheet', 'User #1 re-uploaded approval sheet (SRID-2025-20251007-1)', '2025-10-06 16:33:03', 1),
(68, 61, 'SRID-2025-20251007-1', 1, 'full_manuscript', 'User #1 re-uploaded full manuscript (SRID-2025-20251007-1)', '2025-10-06 16:49:53', 1),
(69, 63, 'SRID-2025-20251007-3', 1, 'approval_sheet', 'User #1 re-uploaded approval sheet (SRID-2025-20251007-3)', '2025-10-06 16:51:45', 1),
(70, 63, 'SRID-2025-20251007-3', 1, 'approval_sheet', 'User #1 re-uploaded approval sheet (SRID-2025-20251007-3)', '2025-10-06 16:56:00', 1),
(71, 63, 'SRID-2025-20251007-3', 1, 'approval_sheet', 'User #1 re-uploaded approval sheet (SRID-2025-20251007-3)', '2025-10-06 16:56:17', 1),
(72, 63, 'SRID-2025-20251007-3', 1, 'approval_sheet', 'User #1 re-uploaded approval sheet (SRID-2025-20251007-3)', '2025-10-06 17:02:58', 1);

-- --------------------------------------------------------

--
-- Table structure for table `admin_profiles`
--

CREATE TABLE `admin_profiles` (
  `profile_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `admin_number` varchar(10) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) NOT NULL,
  `suffix` varchar(5) DEFAULT NULL,
  `home_address` varchar(255) NOT NULL,
  `mobile_number` varchar(15) NOT NULL,
  `department` varchar(255) NOT NULL,
  `last_updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_profiles`
--

INSERT INTO `admin_profiles` (`profile_id`, `user_id`, `admin_number`, `last_name`, `first_name`, `middle_name`, `suffix`, `home_address`, `mobile_number`, `department`, `last_updated_at`) VALUES
(2, 4, 'ADM-001', 'Administrator', 'System', 'IPMO', NULL, 'IPMO Office, Main Campus', '09171234567', 'Intellectual Property Management Office', '2025-10-06 03:55:43');

-- --------------------------------------------------------

--
-- Table structure for table `advisers`
--

CREATE TABLE `advisers` (
  `adviser_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `advisers`
--

INSERT INTO `advisers` (`adviser_id`, `first_name`, `middle_name`, `last_name`, `email`, `department`, `contact_number`, `created_at`) VALUES
(23, 'Test', '', '', NULL, NULL, NULL, '2025-10-06 20:22:31'),
(24, 'Ca', '', '', NULL, NULL, NULL, '2025-10-06 20:54:41'),
(25, 'A', '', '', NULL, NULL, NULL, '2025-10-06 21:02:50'),
(26, 'C', '', '', NULL, NULL, NULL, '2025-10-06 23:16:53'),
(27, 'Ra', '', '', NULL, NULL, NULL, '2025-10-07 00:35:24'),
(28, 'John', '', '', NULL, NULL, NULL, '2025-10-07 01:51:55');

-- --------------------------------------------------------

--
-- Table structure for table `dashboard_summary`
--

CREATE TABLE `dashboard_summary` (
  `id` tinyint(4) NOT NULL,
  `last_updated` datetime NOT NULL,
  `total_users` int(11) NOT NULL DEFAULT 0,
  `total_apps` int(11) NOT NULL DEFAULT 0,
  `pending_apps` int(11) NOT NULL DEFAULT 0,
  `approved_apps` int(11) NOT NULL DEFAULT 0,
  `completed_apps` int(11) NOT NULL DEFAULT 0,
  `overview_undergrad` int(11) NOT NULL DEFAULT 0,
  `overview_grad` int(11) NOT NULL DEFAULT 0,
  `overview_open` int(11) NOT NULL DEFAULT 0,
  `by_college_json` longtext DEFAULT NULL,
  `by_campus_json` longtext DEFAULT NULL,
  `work_class_json` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dashboard_summary`
--

INSERT INTO `dashboard_summary` (`id`, `last_updated`, `total_users`, `total_apps`, `pending_apps`, `approved_apps`, `completed_apps`, `overview_undergrad`, `overview_grad`, `overview_open`, `by_college_json`, `by_campus_json`, `work_class_json`) VALUES
(1, '2025-10-07 02:23:44', 2, 16, 0, 0, 16, 3, 12, 1, '{\"labels\":[\"College of Human Kinetics (CHK)\",\"College of Accountancy and Finance (CAF)\",\"College of Science (CS)\"],\"values\":[9,1,1]}', '{\"labels\":[\"PUP Main (Sta. Mesa, Manila)\"],\"values\":[16]}', '{\"labels\":[\"(a) Books, Pamphlets, articles and other writings\",\"(m) Pictorial illustrations and advertisements\",\"(b) Periodicals and newspaper\",\"(q) Broadcast recordings\",\"(p) Sound recordings\",\"(n) Computer Programs\",\"(o) Other literary, scholarly, scientific and artistic works\"],\"values\":[4,3,3,2,2,1,1]}');

-- --------------------------------------------------------

--
-- Table structure for table `employee_profiles`
--

CREATE TABLE `employee_profiles` (
  `profile_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `employee_number` varchar(10) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) NOT NULL,
  `suffix` varchar(5) DEFAULT NULL,
  `home_address` varchar(255) NOT NULL,
  `mobile_number` varchar(15) NOT NULL,
  `campus` varchar(255) DEFAULT NULL,
  `college` varchar(255) DEFAULT NULL,
  `department` varchar(255) NOT NULL,
  `last_updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_profiles`
--

INSERT INTO `employee_profiles` (`profile_id`, `user_id`, `employee_number`, `last_name`, `first_name`, `middle_name`, `suffix`, `home_address`, `mobile_number`, `campus`, `college`, `department`, `last_updated_at`) VALUES
(1, 2, '12345', 'Lino', 'Mata', 'Bale', '', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234565', 'PUP Main (Sta. Mesa, Manila)', 'College of Social Sciences and Development (CSSD)', 'College Of Science', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student_profiles`
--

CREATE TABLE `student_profiles` (
  `profile_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `student_number` varchar(20) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) NOT NULL,
  `suffix` varchar(5) DEFAULT NULL,
  `home_address` varchar(255) NOT NULL,
  `mobile_number` varchar(15) NOT NULL,
  `campus` varchar(255) NOT NULL,
  `academic_level` enum('Undergraduate','Masters','Doctorate','Open University') NOT NULL DEFAULT 'Undergraduate',
  `college` varchar(255) NOT NULL,
  `department` varchar(255) NOT NULL,
  `program` varchar(255) NOT NULL,
  `last_updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_profiles`
--

INSERT INTO `student_profiles` (`profile_id`, `user_id`, `student_number`, `last_name`, `first_name`, `middle_name`, `suffix`, `home_address`, `mobile_number`, `campus`, `academic_level`, `college`, `department`, `program`, `last_updated_at`) VALUES
(16, 1, '2025-12346-MN-0', 'Minamo', 'Marisa', 'Mliinaw', '', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Human Kinetics (CHK)', '', 'Bachelor Of Physical Education (bpe)', NULL),
(17, 5, '2022-00880-MN-0', 'Inocentes', 'Raebv Lielmo', 'A', '', '4334A V. Francisco St. Sta. Mesa, Manila', '09156574831', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Architecture, Design and the Built Environment (CADBE)', '', 'Bachelor Of Science In Architecture (bs-arch)', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `submissions`
--

CREATE TABLE `submissions` (
  `submission_id` int(11) NOT NULL,
  `submission_code` varchar(30) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `student_number` varchar(20) NOT NULL,
  `home_address` varchar(255) NOT NULL,
  `mobile_number` varchar(20) NOT NULL,
  `webmail` varchar(100) NOT NULL,
  `campus` varchar(255) NOT NULL,
  `academic_level` enum('Undergraduate','Masters','Doctorate','Open University') NOT NULL,
  `college` varchar(255) NOT NULL,
  `program` varchar(255) NOT NULL,
  `work_classification` enum('(a) Books, Pamphlets, articles and other writings','(b) Periodicals and newspaper','(c) Lectures, sermons, addresses, dissertations for oral delivery','(d) Letters','(e) Dramatic or dramatic-musical compositions; choreographic works','(f) Musical compositions with or without words','(g) Works of drawing, painting, architecture, sculpture, engraving, lithography','(h) Original ornamental designs or models for articles of manufacture','(i) Illustrations maps, plans, sketches, charts and three-dimensional works','(j) Drawings or plastic works of a scientific or technical character','(k) Photographic works including works produced by a process analogous to photography','(l) Audiovisual works and cinematographic works','(m) Pictorial illustrations and advertisements','(n) Computer Programs','(o) Other literary, scholarly, scientific and artistic works','(p) Sound recordings','(q) Broadcast recordings') NOT NULL,
  `title` varchar(255) NOT NULL,
  `date_accomplished` date NOT NULL,
  `accepted_terms` tinyint(1) DEFAULT 0,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `status_updated_at` datetime DEFAULT NULL,
  `remarks` text DEFAULT 'for evaluation',
  `version` int(11) DEFAULT 1,
  `is_latest` tinyint(1) DEFAULT 1,
  `submission_type` varchar(50) NOT NULL DEFAULT 'copyright',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `reviewer_id` int(11) DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `adviser_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `submissions`
--

INSERT INTO `submissions` (`submission_id`, `submission_code`, `user_id`, `first_name`, `middle_name`, `last_name`, `student_number`, `home_address`, `mobile_number`, `webmail`, `campus`, `academic_level`, `college`, `program`, `work_classification`, `title`, `date_accomplished`, `accepted_terms`, `status`, `status_updated_at`, `remarks`, `version`, `is_latest`, `submission_type`, `created_at`, `updated_at`, `reviewer_id`, `reviewed_at`, `adviser_id`) VALUES
(53, 'SRID-2025-20251006-1', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Accountancy and Finance (CAF)', 'Bachelor of Science in Accountancy (BSA)', '(a) Books, Pamphlets, articles and other writings', 'Mindfulness on the Night Shift: A Longitudinal Study on the Impacts of Meditation on Nurse Productivity and Well-being', '2025-10-06', 1, 'completed', '2025-10-06 20:32:47', 'test', 1, 1, 'copyright', '2025-10-06 20:22:31', '2025-10-06 20:32:47', 4, '2025-10-06 20:28:12', 23),
(54, 'SRID-2025-20251006-2', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Doctorate', 'N/A', 'Doctor of Philosophy in Communication (PhD Com)', '(m) Pictorial illustrations and advertisements', 'BLABLA', '2025-10-01', 1, 'completed', '2025-10-06 20:59:42', 'test3', 1, 1, 'copyright', '2025-10-06 20:54:41', '2025-10-06 20:59:42', 4, '2025-10-06 20:59:10', 24),
(55, 'SRID-2025-20251006-3', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Masters', 'N/A', 'Master of Science in Construction Management (MSCM)', '(q) Broadcast recordings', 'a', '2025-10-06', 1, 'completed', '2025-10-06 21:03:28', 'testttt', 1, 1, 'copyright', '2025-10-06 21:02:50', '2025-10-06 21:03:28', 4, '2025-10-06 21:02:56', 25),
(56, 'SRID-2025-20251006-4', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Doctorate', 'N/A', 'Doctor of Philosophy in Economics (PhD Econ)', '(q) Broadcast recordings', 'a', '2025-09-19', 1, 'completed', '2025-10-07 00:23:45', 'oi', 1, 1, 'copyright', '2025-10-06 21:04:52', '2025-10-07 00:23:45', 4, '2025-10-07 00:08:52', 25),
(57, 'SRID-2025-20251006-5', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Science (CS)', 'Bachelor of Science Food Technology (BSFT)', '(m) Pictorial illustrations and advertisements', 'a', '2025-10-02', 1, 'completed', '2025-10-07 00:23:14', 'test', 1, 1, 'copyright', '2025-10-06 21:07:11', '2025-10-07 00:23:14', 4, '2025-10-07 00:02:19', 25),
(58, 'SRID-2025-20251006-6', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Open University', 'N/A', 'Bachelor of Public Administration (BPA)', '(p) Sound recordings', 'test', '2025-10-04', 1, 'completed', '2025-10-06 22:04:44', 'test', 1, 1, 'copyright', '2025-10-06 21:08:04', '2025-10-06 22:04:44', 4, '2025-10-06 22:01:07', 25),
(59, 'SRID-2025-20251006-7', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Human Kinetics (CHK)', 'Bachelor Of Physical Education (bpe)', '(n) Computer Programs', 'test3', '2025-10-06', 1, 'completed', '2025-10-07 00:02:55', 'mer', 1, 1, 'copyright', '2025-10-06 23:16:53', '2025-10-07 00:02:55', 4, '2025-10-06 23:47:08', 26),
(60, 'ERID-2025-20251006-8', 2, 'Mata', 'Bale', 'Lino', '12345', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234565', 'errorloading19990@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Doctorate', 'N/A', 'Doctor of Philosophy in Communication (PhD Com)', '(o) Other literary, scholarly, scientific and artistic works', 'a', '2025-10-06', 1, 'completed', '2025-10-07 00:02:41', 'test', 1, 1, 'copyright', '2025-10-06 23:25:53', '2025-10-07 00:02:41', 4, '2025-10-06 23:44:34', 25),
(61, 'SRID-2025-20251007-1', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Human Kinetics (CHK)', 'Bachelor Of Physical Education (bpe)', '(b) Periodicals and newspaper', 'a', '2025-10-07', 1, 'completed', '2025-10-07 01:24:40', 'mARKG', 1, 1, 'copyright', '2025-10-07 00:32:01', '2025-10-07 01:24:40', 4, '2025-10-07 01:11:45', 25),
(62, 'SRID-2025-20251007-2', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Human Kinetics (CHK)', 'Bachelor Of Physical Education (bpe)', '(a) Books, Pamphlets, articles and other writings', 'a', '2025-10-07', 1, 'completed', '2025-10-07 00:57:53', 'matoy', 1, 1, 'copyright', '2025-10-07 00:35:24', '2025-10-07 00:57:53', 4, '2025-10-07 00:35:43', 27),
(63, 'SRID-2025-20251007-3', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Human Kinetics (CHK)', 'Bachelor Of Physical Education (bpe)', '(a) Books, Pamphlets, articles and other writings', 'test', '2025-10-07', 1, 'completed', '2025-10-07 01:03:53', 'tset', 1, 1, 'copyright', '2025-10-07 00:51:16', '2025-10-07 01:03:53', 4, '2025-10-07 01:03:16', 25),
(64, 'SRID-2025-20251007-4', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Human Kinetics (CHK)', 'Bachelor Of Physical Education (bpe)', '(b) Periodicals and newspaper', 'a', '2025-10-07', 1, 'completed', '2025-10-07 01:33:38', 'MARKGO', 1, 1, 'copyright', '2025-10-07 01:18:46', '2025-10-07 01:33:38', 4, '2025-10-07 01:32:49', 25),
(65, 'SRID-2025-20251007-5', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Human Kinetics (CHK)', 'Bachelor Of Physical Education (bpe)', '(p) Sound recordings', 'tes1', '2025-10-07', 1, 'completed', '2025-10-07 01:50:51', 'test11', 1, 1, 'copyright', '2025-10-07 01:35:45', '2025-10-07 01:50:51', 4, '2025-10-07 01:35:56', 25),
(66, 'SRID-2025-20251007-6', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Human Kinetics (CHK)', 'Bachelor Of Physical Education (bpe)', '(b) Periodicals and newspaper', 'test', '2025-10-07', 1, 'completed', '2025-10-07 01:50:04', 'make', 1, 1, 'copyright', '2025-10-07 01:36:22', '2025-10-07 01:50:04', 4, '2025-10-07 01:36:46', 25),
(67, 'SRID-2025-20251007-7', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Human Kinetics (CHK)', 'Bachelor Of Physical Education (bpe)', '(m) Pictorial illustrations and advertisements', 'a', '2025-10-07', 1, 'completed', '2025-10-07 01:42:07', 'hey3', 1, 1, 'copyright', '2025-10-07 01:39:36', '2025-10-07 01:42:07', 4, '2025-10-07 01:41:55', 25),
(68, 'SRID-2025-20251007-8', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Human Kinetics (CHK)', 'Bachelor Of Physical Education (bpe)', '(a) Books, Pamphlets, articles and other writings', 'BOOM', '2025-10-07', 1, 'completed', '2025-10-07 01:53:24', 'testttt', 1, 1, 'copyright', '2025-10-07 01:51:55', '2025-10-07 01:53:24', 4, '2025-10-07 01:52:00', 28);

-- --------------------------------------------------------

--
-- Table structure for table `submission_authors`
--

CREATE TABLE `submission_authors` (
  `author_id` int(11) NOT NULL,
  `submission_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `student_id` varchar(20) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `home_address` varchar(255) DEFAULT NULL,
  `webmail` varchar(100) DEFAULT NULL,
  `role` varchar(50) DEFAULT 'Author',
  `is_adviser` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `adviser_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `submission_authors`
--

INSERT INTO `submission_authors` (`author_id`, `submission_id`, `first_name`, `middle_name`, `last_name`, `student_id`, `mobile`, `home_address`, `webmail`, `role`, `is_adviser`, `created_at`, `adviser_id`) VALUES
(1, 53, 'Test', '', '', NULL, NULL, NULL, NULL, 'Adviser', 1, '2025-10-06 20:22:31', 23),
(2, 53, 'Raebv Lielmo', '', 'Inocentes', '2022-08900-MN-0', '09156574831', '4334A V. Francisco St. Sta. Mesa, Manila', 'inocentesraebv@iskolarngbayan.pup.edu.ph', 'Author', 0, '2025-10-06 20:22:31', NULL),
(3, 55, 'A', '', '', NULL, NULL, NULL, NULL, 'Adviser', 1, '2025-10-06 21:02:50', 25),
(4, 56, 'A', '', '', NULL, NULL, NULL, NULL, 'Adviser', 1, '2025-10-06 21:04:52', 25),
(5, 57, 'A', '', '', NULL, NULL, NULL, NULL, 'Adviser', 1, '2025-10-06 21:07:11', 25),
(6, 58, 'A', '', '', NULL, NULL, NULL, NULL, 'Adviser', 1, '2025-10-06 21:08:04', 25),
(7, 59, 'C', '', '', NULL, NULL, NULL, NULL, 'Adviser', 1, '2025-10-06 23:16:53', 26),
(8, 60, 'A', '', '', NULL, NULL, NULL, NULL, 'Adviser', 1, '2025-10-06 23:25:53', 25),
(9, 60, '', '', '', NULL, NULL, NULL, NULL, 'Author', 0, '2025-10-06 23:25:53', NULL),
(10, 61, 'A', '', '', NULL, NULL, NULL, NULL, 'Adviser', 1, '2025-10-07 00:32:01', 25),
(11, 62, 'Ra', '', '', NULL, NULL, NULL, NULL, 'Adviser', 1, '2025-10-07 00:35:24', 27),
(12, 63, 'A', '', '', NULL, NULL, NULL, NULL, 'Adviser', 1, '2025-10-07 00:51:16', 25),
(13, 64, 'A', '', '', NULL, NULL, NULL, NULL, 'Adviser', 1, '2025-10-07 01:18:46', 25),
(14, 65, 'A', '', '', NULL, NULL, NULL, NULL, 'Adviser', 1, '2025-10-07 01:35:45', 25),
(15, 66, 'A', '', '', NULL, NULL, NULL, NULL, 'Adviser', 1, '2025-10-07 01:36:22', 25),
(16, 67, 'A', '', '', NULL, NULL, NULL, NULL, 'Adviser', 1, '2025-10-07 01:39:36', 25),
(17, 68, 'John', '', '', NULL, NULL, NULL, NULL, 'Adviser', 1, '2025-10-07 01:51:55', 28),
(18, 68, 'Raebv Lielmo', '', 'Inocentes', '2020-09121-MN-0', '09156574831', '4334A V. Francisco St. Sta. Mesa, Manila', 'inocentesraebv@iskolarngbayan.pup.edu.ph', 'Author', 0, '2025-10-07 01:51:55', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `submission_documents`
--

CREATE TABLE `submission_documents` (
  `document_id` int(11) NOT NULL,
  `submission_id` int(11) NOT NULL,
  `doc_type` varchar(100) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp(),
  `file_size` bigint(20) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `verified` tinyint(1) DEFAULT 0,
  `verified_by` int(11) DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `submission_documents`
--

INSERT INTO `submission_documents` (`document_id`, `submission_id`, `doc_type`, `file_path`, `uploaded_at`, `file_size`, `mime_type`, `verified`, `verified_by`, `verified_at`) VALUES
(1, 53, 'journal_publication_format', 'journal_publication_format_1759753351_8e717d2d.pdf', '2025-10-06 20:22:31', 66396, 'application/pdf', 0, NULL, NULL),
(2, 53, 'notarized_copyright', 'notarized_copyright_1759753351_2cd26530.pdf', '2025-10-06 20:22:31', 66396, 'application/pdf', 0, NULL, NULL),
(3, 53, 'receipt_payment', 'receipt_payment_1759753351_854daf31.pdf', '2025-10-06 20:22:31', 66396, 'application/pdf', 0, NULL, NULL),
(4, 53, 'full_manuscript', 'full_manuscript_1759753679_bffa7776.pdf', '2025-10-06 20:27:59', 66396, 'application/pdf', 0, NULL, NULL),
(5, 53, 'notarized_coauthorship', 'notarized_coauthorship_1759753351_dc11510f.pdf', '2025-10-06 20:22:31', 66396, 'application/pdf', 0, NULL, NULL),
(6, 53, 'approval_sheet', 'approval_sheet_1759753613_42478adc.pdf', '2025-10-06 20:26:53', 64202, 'application/pdf', 0, NULL, NULL),
(7, 53, 'record_copyright', 'record_copyright_1759753351_d0469cde.pdf', '2025-10-06 20:22:31', 66396, 'application/pdf', 0, NULL, NULL),
(8, 54, 'journal_publication_format', 'journal_publication_format_1759755281_3902ae19.pdf', '2025-10-06 20:54:41', 66396, 'application/pdf', 0, NULL, NULL),
(9, 54, 'notarized_copyright', 'notarized_copyright_1759755281_f1a04c46.pdf', '2025-10-06 20:54:41', 66396, 'application/pdf', 0, NULL, NULL),
(10, 54, 'receipt_payment', 'receipt_payment_1759755281_606559de.pdf', '2025-10-06 20:54:41', 66396, 'application/pdf', 0, NULL, NULL),
(11, 54, 'full_manuscript', 'full_manuscript_1759755281_d7600601.pdf', '2025-10-06 20:54:41', 66396, 'application/pdf', 0, NULL, NULL),
(12, 54, 'notarized_coauthorship', 'notarized_coauthorship_1759755281_639d67bc.pdf', '2025-10-06 20:54:41', 66396, 'application/pdf', 0, NULL, NULL),
(13, 54, 'approval_sheet', 'approval_sheet_1759755281_2e17940c.pdf', '2025-10-06 20:54:41', 66396, 'application/pdf', 0, NULL, NULL),
(14, 54, 'record_copyright', 'record_copyright_1759755281_317ed32f.pdf', '2025-10-06 20:54:41', 66396, 'application/pdf', 0, NULL, NULL),
(15, 55, 'journal_publication_format', 'journal_publication_format_1759755770_91df7fbc.pdf', '2025-10-06 21:02:50', 66396, 'application/pdf', 0, NULL, NULL),
(16, 55, 'notarized_copyright', 'notarized_copyright_1759755770_de119920.pdf', '2025-10-06 21:02:50', 66396, 'application/pdf', 0, NULL, NULL),
(17, 55, 'receipt_payment', 'receipt_payment_1759755770_3045b940.pdf', '2025-10-06 21:02:50', 66396, 'application/pdf', 0, NULL, NULL),
(18, 55, 'full_manuscript', 'full_manuscript_1759755770_b19f829a.pdf', '2025-10-06 21:02:50', 66396, 'application/pdf', 0, NULL, NULL),
(19, 55, 'notarized_coauthorship', 'notarized_coauthorship_1759755770_1e3f8591.pdf', '2025-10-06 21:02:50', 66396, 'application/pdf', 0, NULL, NULL),
(20, 55, 'approval_sheet', 'approval_sheet_1759755770_87095279.pdf', '2025-10-06 21:02:50', 66396, 'application/pdf', 0, NULL, NULL),
(21, 55, 'record_copyright', 'record_copyright_1759755770_1bc41f45.pdf', '2025-10-06 21:02:50', 66396, 'application/pdf', 0, NULL, NULL),
(22, 56, 'journal_publication_format', 'journal_publication_format_1759755892_52775818.pdf', '2025-10-06 21:04:52', 66396, 'application/pdf', 0, NULL, NULL),
(23, 56, 'notarized_copyright', 'notarized_copyright_1759755892_5b233953.pdf', '2025-10-06 21:04:52', 66396, 'application/pdf', 0, NULL, NULL),
(24, 56, 'receipt_payment', 'receipt_payment_1759755892_e4642dbe.pdf', '2025-10-06 21:04:52', 66396, 'application/pdf', 0, NULL, NULL),
(25, 56, 'full_manuscript', 'full_manuscript_1759755892_3c94d796.pdf', '2025-10-06 21:04:52', 66396, 'application/pdf', 0, NULL, NULL),
(26, 56, 'notarized_coauthorship', 'notarized_coauthorship_1759755892_ef3b8b91.pdf', '2025-10-06 21:04:52', 66396, 'application/pdf', 0, NULL, NULL),
(27, 56, 'approval_sheet', 'approval_sheet_1759755892_336d2638.pdf', '2025-10-06 21:04:52', 66396, 'application/pdf', 0, NULL, NULL),
(28, 56, 'record_copyright', 'record_copyright_1759755892_5de802b8.pdf', '2025-10-06 21:04:52', 66396, 'application/pdf', 0, NULL, NULL),
(29, 57, 'journal_publication_format', 'journal_publication_format_1759756031_e2a113c1.pdf', '2025-10-06 21:07:11', 66396, 'application/pdf', 0, NULL, NULL),
(30, 57, 'notarized_copyright', 'notarized_copyright_1759756031_85f8dc7c.pdf', '2025-10-06 21:07:11', 66396, 'application/pdf', 0, NULL, NULL),
(31, 57, 'receipt_payment', 'receipt_payment_1759756031_f67d0146.pdf', '2025-10-06 21:07:11', 66396, 'application/pdf', 0, NULL, NULL),
(32, 57, 'full_manuscript', 'full_manuscript_1759756031_d9d914dd.pdf', '2025-10-06 21:07:11', 66396, 'application/pdf', 0, NULL, NULL),
(33, 57, 'notarized_coauthorship', 'notarized_coauthorship_1759756031_c63cd0fe.pdf', '2025-10-06 21:07:11', 66396, 'application/pdf', 0, NULL, NULL),
(34, 57, 'approval_sheet', 'approval_sheet_1759756031_4e528340.pdf', '2025-10-06 21:07:11', 66396, 'application/pdf', 0, NULL, NULL),
(35, 57, 'record_copyright', 'record_copyright_1759756031_24d50425.pdf', '2025-10-06 21:07:11', 66396, 'application/pdf', 0, NULL, NULL),
(36, 58, 'journal_publication_format', 'journal_publication_format_1759756084_5923e489.pdf', '2025-10-06 21:08:04', 66396, 'application/pdf', 0, NULL, NULL),
(37, 58, 'notarized_copyright', 'notarized_copyright_1759756084_2ee93948.pdf', '2025-10-06 21:08:04', 66396, 'application/pdf', 0, NULL, NULL),
(38, 58, 'receipt_payment', 'receipt_payment_1759756084_290711c3.pdf', '2025-10-06 21:08:04', 66396, 'application/pdf', 0, NULL, NULL),
(39, 58, 'full_manuscript', 'full_manuscript_1759756084_29c35b7d.pdf', '2025-10-06 21:08:04', 66396, 'application/pdf', 0, NULL, NULL),
(40, 58, 'notarized_coauthorship', 'notarized_coauthorship_1759756084_f1889434.pdf', '2025-10-06 21:08:04', 66396, 'application/pdf', 0, NULL, NULL),
(41, 58, 'approval_sheet', 'approval_sheet_1759756084_ef5acb7b.pdf', '2025-10-06 21:08:04', 66396, 'application/pdf', 0, NULL, NULL),
(42, 58, 'record_copyright', 'record_copyright_1759756084_2d1c79c7.pdf', '2025-10-06 21:08:04', 66396, 'application/pdf', 0, NULL, NULL),
(43, 59, 'journal_publication_format', 'journal_publication_format_1759763813_6e420cfe.pdf', '2025-10-06 23:16:53', 64202, 'application/pdf', 0, NULL, NULL),
(44, 59, 'notarized_copyright', 'notarized_copyright_1759763813_105cf52b.pdf', '2025-10-06 23:16:53', 64202, 'application/pdf', 0, NULL, NULL),
(45, 59, 'receipt_payment', 'receipt_payment_1759763813_e82f6821.pdf', '2025-10-06 23:16:53', 64202, 'application/pdf', 0, NULL, NULL),
(46, 59, 'full_manuscript', 'full_manuscript_1759763813_76f12acc.pdf', '2025-10-06 23:16:53', 64202, 'application/pdf', 0, NULL, NULL),
(47, 59, 'notarized_coauthorship', 'notarized_coauthorship_1759763813_ad531ae6.pdf', '2025-10-06 23:16:53', 64202, 'application/pdf', 0, NULL, NULL),
(48, 59, 'approval_sheet', 'approval_sheet_1759763813_53d90005.pdf', '2025-10-06 23:16:53', 64202, 'application/pdf', 0, NULL, NULL),
(49, 59, 'record_copyright', 'record_copyright_1759763813_650b99aa.pdf', '2025-10-06 23:16:53', 64202, 'application/pdf', 0, NULL, NULL),
(50, 60, 'journal_publication_format', 'journal_publication_format_1759764353_e6ac2b17.pdf', '2025-10-06 23:25:53', 64202, 'application/pdf', 0, NULL, NULL),
(51, 60, 'notarized_copyright', 'notarized_copyright_1759764353_a6a17b62.pdf', '2025-10-06 23:25:53', 64202, 'application/pdf', 0, NULL, NULL),
(52, 60, 'receipt_payment', 'receipt_payment_1759764353_71a76b7a.pdf', '2025-10-06 23:25:53', 64202, 'application/pdf', 0, NULL, NULL),
(53, 60, 'presentation', 'presentation_1759764353_1bf284ba.pdf', '2025-10-06 23:25:53', 64202, 'application/pdf', 0, NULL, NULL),
(54, 60, 'notarized_coauthorship', 'notarized_coauthorship_1759764353_b05f0482.pdf', '2025-10-06 23:25:53', 64202, 'application/pdf', 0, NULL, NULL),
(55, 60, 'record_copyright', 'record_copyright_1759764353_4b119079.pdf', '2025-10-06 23:25:53', 64202, 'application/pdf', 0, NULL, NULL),
(56, 61, 'journal_publication_format', 'journal_publication_format_1759768321_14b00c87.pdf', '2025-10-07 00:32:01', 64202, 'application/pdf', 0, NULL, NULL),
(57, 61, 'notarized_copyright', 'notarized_copyright_1759768321_46314cab.pdf', '2025-10-07 00:32:01', 64202, 'application/pdf', 0, NULL, NULL),
(58, 61, 'receipt_payment', 'receipt_payment_1759768321_34b95150.pdf', '2025-10-07 00:32:01', 66396, 'application/pdf', 0, NULL, NULL),
(59, 61, 'full_manuscript', 'full_manuscript_1759769393_670c80e2.pdf', '2025-10-07 00:49:53', 66396, 'application/pdf', 0, NULL, NULL),
(60, 61, 'notarized_coauthorship', 'notarized_coauthorship_1759768321_8183e20b.pdf', '2025-10-07 00:32:01', 66396, 'application/pdf', 0, NULL, NULL),
(61, 61, 'approval_sheet', 'approval_sheet_1759768383_dee08dab.pdf', '2025-10-07 00:33:03', 66396, 'application/pdf', 0, NULL, NULL),
(62, 61, 'record_copyright', 'record_copyright_1759768321_090d5801.pdf', '2025-10-07 00:32:01', 66396, 'application/pdf', 0, NULL, NULL),
(63, 62, 'journal_publication_format', 'journal_publication_format_1759768524_803be513.pdf', '2025-10-07 00:35:24', 66396, 'application/pdf', 0, NULL, NULL),
(64, 62, 'notarized_copyright', 'notarized_copyright_1759768524_221d6542.pdf', '2025-10-07 00:35:24', 66396, 'application/pdf', 0, NULL, NULL),
(65, 62, 'receipt_payment', 'receipt_payment_1759768524_072e3cfd.pdf', '2025-10-07 00:35:24', 66396, 'application/pdf', 0, NULL, NULL),
(66, 62, 'full_manuscript', 'full_manuscript_1759768524_471a972d.pdf', '2025-10-07 00:35:24', 66396, 'application/pdf', 0, NULL, NULL),
(67, 62, 'notarized_coauthorship', 'notarized_coauthorship_1759768524_3121ce1d.pdf', '2025-10-07 00:35:24', 66396, 'application/pdf', 0, NULL, NULL),
(68, 62, 'approval_sheet', 'approval_sheet_1759768524_7268e6a4.pdf', '2025-10-07 00:35:24', 66396, 'application/pdf', 0, NULL, NULL),
(69, 62, 'record_copyright', 'record_copyright_1759768524_f72f04bf.pdf', '2025-10-07 00:35:24', 66396, 'application/pdf', 0, NULL, NULL),
(70, 63, 'journal_publication_format', 'journal_publication_format_1759769476_5648b831.pdf', '2025-10-07 00:51:16', 66396, 'application/pdf', 0, NULL, NULL),
(71, 63, 'notarized_copyright', 'notarized_copyright_1759769476_c5f93de6.pdf', '2025-10-07 00:51:16', 66396, 'application/pdf', 0, NULL, NULL),
(72, 63, 'receipt_payment', 'receipt_payment_1759769476_d86d5e01.pdf', '2025-10-07 00:51:16', 66396, 'application/pdf', 0, NULL, NULL),
(73, 63, 'full_manuscript', 'full_manuscript_1759769476_cb13d910.pdf', '2025-10-07 00:51:16', 66396, 'application/pdf', 0, NULL, NULL),
(74, 63, 'notarized_coauthorship', 'notarized_coauthorship_1759769476_d4919aee.pdf', '2025-10-07 00:51:16', 66396, 'application/pdf', 0, NULL, NULL),
(75, 63, 'approval_sheet', 'approval_sheet_1759770178_70764a82.pdf', '2025-10-07 01:02:58', 66396, 'application/pdf', 0, NULL, NULL),
(76, 63, 'record_copyright', 'record_copyright_1759769476_caa79027.pdf', '2025-10-07 00:51:16', 66396, 'application/pdf', 0, NULL, NULL),
(77, 64, 'journal_publication_format', 'journal_publication_format_1759771125_3c8ec8d7.pdf', '2025-10-07 01:18:46', 66396, 'application/pdf', 0, NULL, NULL),
(78, 64, 'notarized_copyright', 'notarized_copyright_1759771125_fb78a375.pdf', '2025-10-07 01:18:46', 66396, 'application/pdf', 0, NULL, NULL),
(79, 64, 'receipt_payment', 'receipt_payment_1759771125_3d8020ad.pdf', '2025-10-07 01:18:46', 66396, 'application/pdf', 0, NULL, NULL),
(80, 64, 'full_manuscript', 'full_manuscript_1759771125_4c6fb525.pdf', '2025-10-07 01:18:46', 66396, 'application/pdf', 0, NULL, NULL),
(81, 64, 'notarized_coauthorship', 'notarized_coauthorship_1759771125_ca26bbe3.pdf', '2025-10-07 01:18:46', 66396, 'application/pdf', 0, NULL, NULL),
(82, 64, 'approval_sheet', 'approval_sheet_1759771125_4fce49fe.pdf', '2025-10-07 01:18:46', 66396, 'application/pdf', 0, NULL, NULL),
(83, 64, 'record_copyright', 'record_copyright_1759771125_9abdcd77.pdf', '2025-10-07 01:18:46', 66396, 'application/pdf', 0, NULL, NULL),
(84, 65, 'journal_publication_format', 'journal_publication_format_1759772145_c3e1af2f.pdf', '2025-10-07 01:35:45', 64202, 'application/pdf', 0, NULL, NULL),
(85, 65, 'notarized_copyright', 'notarized_copyright_1759772145_d7a87a87.pdf', '2025-10-07 01:35:45', 64202, 'application/pdf', 0, NULL, NULL),
(86, 65, 'receipt_payment', 'receipt_payment_1759772145_653dc2b6.pdf', '2025-10-07 01:35:45', 64202, 'application/pdf', 0, NULL, NULL),
(87, 65, 'full_manuscript', 'full_manuscript_1759772145_29d1fbb0.pdf', '2025-10-07 01:35:45', 64202, 'application/pdf', 0, NULL, NULL),
(88, 65, 'notarized_coauthorship', 'notarized_coauthorship_1759772145_ccb8b65a.pdf', '2025-10-07 01:35:45', 64202, 'application/pdf', 0, NULL, NULL),
(89, 65, 'approval_sheet', 'approval_sheet_1759772145_2abab9ed.pdf', '2025-10-07 01:35:45', 64202, 'application/pdf', 0, NULL, NULL),
(90, 65, 'record_copyright', 'record_copyright_1759772145_6489ee8c.pdf', '2025-10-07 01:35:45', 64202, 'application/pdf', 0, NULL, NULL),
(91, 66, 'journal_publication_format', 'journal_publication_format_1759772182_f9db85f2.pdf', '2025-10-07 01:36:22', 64202, 'application/pdf', 0, NULL, NULL),
(92, 66, 'notarized_copyright', 'notarized_copyright_1759772182_7c7715e6.pdf', '2025-10-07 01:36:22', 64202, 'application/pdf', 0, NULL, NULL),
(93, 66, 'receipt_payment', 'receipt_payment_1759772182_c79dfda1.pdf', '2025-10-07 01:36:22', 64202, 'application/pdf', 0, NULL, NULL),
(94, 66, 'full_manuscript', 'full_manuscript_1759772182_c6b8af3e.pdf', '2025-10-07 01:36:22', 64202, 'application/pdf', 0, NULL, NULL),
(95, 66, 'notarized_coauthorship', 'notarized_coauthorship_1759772182_c4964232.pdf', '2025-10-07 01:36:22', 64202, 'application/pdf', 0, NULL, NULL),
(96, 66, 'approval_sheet', 'approval_sheet_1759772182_6384dd2d.pdf', '2025-10-07 01:36:22', 64202, 'application/pdf', 0, NULL, NULL),
(97, 66, 'record_copyright', 'record_copyright_1759772182_0b0a6403.pdf', '2025-10-07 01:36:22', 64202, 'application/pdf', 0, NULL, NULL),
(98, 67, 'journal_publication_format', 'journal_publication_format_1759772376_cafa3290.pdf', '2025-10-07 01:39:36', 64202, 'application/pdf', 0, NULL, NULL),
(99, 67, 'notarized_copyright', 'notarized_copyright_1759772376_bd1df5e9.pdf', '2025-10-07 01:39:36', 64202, 'application/pdf', 0, NULL, NULL),
(100, 67, 'receipt_payment', 'receipt_payment_1759772376_47f6f663.pdf', '2025-10-07 01:39:36', 64202, 'application/pdf', 0, NULL, NULL),
(101, 67, 'full_manuscript', 'full_manuscript_1759772376_6ede8b13.pdf', '2025-10-07 01:39:36', 64202, 'application/pdf', 0, NULL, NULL),
(102, 67, 'notarized_coauthorship', 'notarized_coauthorship_1759772376_4c619a54.pdf', '2025-10-07 01:39:36', 64202, 'application/pdf', 0, NULL, NULL),
(103, 67, 'approval_sheet', 'approval_sheet_1759772376_64ecad57.pdf', '2025-10-07 01:39:36', 64202, 'application/pdf', 0, NULL, NULL),
(104, 67, 'record_copyright', 'record_copyright_1759772376_9b6091d1.pdf', '2025-10-07 01:39:36', 64202, 'application/pdf', 0, NULL, NULL),
(105, 68, 'journal_publication_format', 'journal_publication_format_1759773115_d68ebb65.pdf', '2025-10-07 01:51:55', 64202, 'application/pdf', 0, NULL, NULL),
(106, 68, 'notarized_copyright', 'notarized_copyright_1759773115_0326a134.pdf', '2025-10-07 01:51:55', 64202, 'application/pdf', 0, NULL, NULL),
(107, 68, 'receipt_payment', 'receipt_payment_1759773115_d8134fd0.pdf', '2025-10-07 01:51:55', 64202, 'application/pdf', 0, NULL, NULL),
(108, 68, 'full_manuscript', 'full_manuscript_1759773115_55a102db.pdf', '2025-10-07 01:51:55', 64202, 'application/pdf', 0, NULL, NULL),
(109, 68, 'notarized_coauthorship', 'notarized_coauthorship_1759773115_594c73ce.pdf', '2025-10-07 01:51:55', 64202, 'application/pdf', 0, NULL, NULL),
(110, 68, 'approval_sheet', 'approval_sheet_1759773115_d4b868ca.pdf', '2025-10-07 01:51:55', 64202, 'application/pdf', 0, NULL, NULL),
(111, 68, 'record_copyright', 'record_copyright_1759773115_33629635.pdf', '2025-10-07 01:51:55', 64202, 'application/pdf', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `submission_incomplete_meta`
--

CREATE TABLE `submission_incomplete_meta` (
  `submission_id` int(11) NOT NULL,
  `scope` enum('pending','approved') NOT NULL,
  `issue_label` varchar(150) DEFAULT NULL,
  `admin_comment` text DEFAULT NULL,
  `affected_doc_types` text DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` datetime DEFAULT current_timestamp(),
  `reuploaded_doc_types` text DEFAULT NULL,
  `reupload_locked` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `submission_incomplete_meta`
--

INSERT INTO `submission_incomplete_meta` (`submission_id`, `scope`, `issue_label`, `admin_comment`, `affected_doc_types`, `updated_at`, `created_at`, `reuploaded_doc_types`, `reupload_locked`) VALUES
(53, 'pending', 'Error in Document/Upload', 'test', '', '2025-10-06 20:27:59', '2025-10-06 20:25:11', NULL, 0),
(53, 'approved', 'Documents don’t match', 'tset', 'approval_sheet|full_manuscript|journal_publication_format|notarized_coauthorship|notarized_copyright|receipt_payment|record_copyright', '2025-10-06 20:32:09', '2025-10-06 20:30:21', NULL, 0),
(58, 'pending', 'Error in Document/Upload', 'tse', 'approval_sheet', '2025-10-06 21:09:02', '2025-10-06 21:09:02', NULL, 0),
(61, 'pending', 'Error in Document/Upload', 'test', '', '2025-10-07 00:49:53', '2025-10-07 00:32:53', NULL, 0),
(61, 'approved', 'Missing Document', 'test', 'approval_sheet|full_manuscript|journal_publication_format|notarized_coauthorship|notarized_copyright|receipt_payment|record_copyright', '2025-10-07 01:12:53', '2025-10-07 01:12:01', NULL, 0),
(62, 'approved', 'Missing Document', 'test', 'approval_sheet', '2025-10-07 00:49:36', '2025-10-07 00:36:05', NULL, 0),
(63, 'pending', 'Error in Document/Upload', 'test', '', '2025-10-07 01:02:58', '2025-10-07 00:51:27', NULL, 0),
(64, 'pending', 'Incorrect Document/Upload', 'test', 'approval_sheet|full_manuscript', '2025-10-07 01:23:50', '2025-10-07 01:18:56', NULL, 0),
(66, 'pending', 'Incorrect Document/Upload', 'test', 'approval_sheet|full_manuscript', '2025-10-07 01:36:29', '2025-10-07 01:36:29', NULL, 0),
(67, 'pending', 'Incorrect Document/Upload', 'hey1', 'approval_sheet|full_manuscript', '2025-10-07 01:41:40', '2025-10-07 01:41:40', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `submission_notes`
--

CREATE TABLE `submission_notes` (
  `id` int(11) NOT NULL,
  `submission_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `note` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `submission_notes`
--

INSERT INTO `submission_notes` (`id`, `submission_id`, `user_id`, `note`, `created_at`) VALUES
(1, 58, 1, 'test', '2025-10-06 21:09:12');

-- --------------------------------------------------------

--
-- Table structure for table `submission_notes_admin_views`
--

CREATE TABLE `submission_notes_admin_views` (
  `id` int(11) NOT NULL,
  `submission_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `last_viewed_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `submission_notes_admin_views`
--

INSERT INTO `submission_notes_admin_views` (`id`, `submission_id`, `admin_id`, `last_viewed_at`) VALUES
(1, 58, 4, '2025-10-07 01:06:44');

-- --------------------------------------------------------

--
-- Table structure for table `ticket_tokens`
--

CREATE TABLE `ticket_tokens` (
  `id` int(11) NOT NULL,
  `submission_id` int(11) NOT NULL,
  `request_id` varchar(64) NOT NULL,
  `validation_token` varchar(64) NOT NULL,
  `token_expires` datetime NOT NULL,
  `status` enum('active','used','expired') NOT NULL DEFAULT 'active',
  `used_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_validation_attempts`
--

CREATE TABLE `ticket_validation_attempts` (
  `id` int(11) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `request_id` varchar(64) DEFAULT NULL,
  `token` varchar(64) DEFAULT NULL,
  `success` tinyint(1) NOT NULL DEFAULT 0,
  `attempted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','employee','admin') NOT NULL,
  `status` enum('active','inactive','pending') DEFAULT 'pending',
  `verification_code` varchar(16) DEFAULT NULL,
  `code_expires_at` datetime DEFAULT NULL,
  `email_verified_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password`, `role`, `status`, `verification_code`, `code_expires_at`, `email_verified_at`, `created_at`) VALUES
(1, 'aceplanetary0@gmail.com', '$2y$10$hhH/xfeUKvwPOTBNhk3IaO5Uk3vlOrAui2Hdq05.qcYI/1U.TANwi', 'student', 'active', NULL, NULL, '2025-10-06 03:36:13', '2025-10-06 03:35:45'),
(2, 'errorloading19990@gmail.com', '$2y$10$.N/AIAFQjEvxRcOgrd9nVOpMGt3tODrdZoPpsnJAYGHM.k/zsVbtK', 'employee', 'active', NULL, NULL, '2025-10-06 03:39:03', '2025-10-06 03:38:36'),
(4, 'admin@ipmo.local', '$2y$10$FcSO4or9z9oUzIEIJW/87uNeKHnf9wrdWOdN2w6A/N6E.jHnK2owy', 'admin', 'active', NULL, NULL, '2025-10-06 03:55:43', '2025-10-06 03:55:43'),
(5, 'inocentesraebv@gmail.com', '$2y$12$MvO.41oeGZKRmqJc6lM8Ueppkcugyq4/0lvZKR5m3ETVlNRPlvx2a', 'student', 'pending', '9481d21b6c60ec81', '2025-10-07 22:30:53', NULL, '2025-10-06 22:30:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `is_read` (`is_read`),
  ADD KEY `submission_id` (`submission_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `admin_profiles`
--
ALTER TABLE `admin_profiles`
  ADD PRIMARY KEY (`profile_id`),
  ADD KEY `fk_admin_user` (`user_id`);

--
-- Indexes for table `advisers`
--
ALTER TABLE `advisers`
  ADD PRIMARY KEY (`adviser_id`);

--
-- Indexes for table `dashboard_summary`
--
ALTER TABLE `dashboard_summary`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_profiles`
--
ALTER TABLE `employee_profiles`
  ADD PRIMARY KEY (`profile_id`),
  ADD KEY `fk_employee_user` (`user_id`);

--
-- Indexes for table `student_profiles`
--
ALTER TABLE `student_profiles`
  ADD PRIMARY KEY (`profile_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`submission_id`),
  ADD UNIQUE KEY `submission_code` (`submission_code`),
  ADD KEY `idx_user_status` (`user_id`,`status`),
  ADD KEY `idx_student_number` (`student_number`),
  ADD KEY `idx_submission_date` (`created_at`),
  ADD KEY `reviewer_id` (`reviewer_id`),
  ADD KEY `adviser_id` (`adviser_id`);

--
-- Indexes for table `submission_authors`
--
ALTER TABLE `submission_authors`
  ADD PRIMARY KEY (`author_id`),
  ADD KEY `idx_submission_author` (`submission_id`),
  ADD KEY `adviser_id` (`adviser_id`);

--
-- Indexes for table `submission_documents`
--
ALTER TABLE `submission_documents`
  ADD PRIMARY KEY (`document_id`),
  ADD KEY `verified_by` (`verified_by`),
  ADD KEY `idx_submission_doc` (`submission_id`,`doc_type`);

--
-- Indexes for table `submission_incomplete_meta`
--
ALTER TABLE `submission_incomplete_meta`
  ADD PRIMARY KEY (`submission_id`,`scope`);

--
-- Indexes for table `submission_notes`
--
ALTER TABLE `submission_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_sn_submission` (`submission_id`);

--
-- Indexes for table `submission_notes_admin_views`
--
ALTER TABLE `submission_notes_admin_views`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_view` (`submission_id`,`admin_id`),
  ADD KEY `idx_submission_admin` (`submission_id`,`admin_id`),
  ADD KEY `fk_snav_admin` (`admin_id`);

--
-- Indexes for table `ticket_tokens`
--
ALTER TABLE `ticket_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_request_id` (`request_id`),
  ADD KEY `idx_token` (`validation_token`),
  ADD KEY `fk_tt_submission` (`submission_id`);

--
-- Indexes for table `ticket_validation_attempts`
--
ALTER TABLE `ticket_validation_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ip_time` (`ip`,`attempted_at`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `admin_profiles`
--
ALTER TABLE `admin_profiles`
  MODIFY `profile_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `advisers`
--
ALTER TABLE `advisers`
  MODIFY `adviser_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `employee_profiles`
--
ALTER TABLE `employee_profiles`
  MODIFY `profile_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `student_profiles`
--
ALTER TABLE `student_profiles`
  MODIFY `profile_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `submission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `submission_authors`
--
ALTER TABLE `submission_authors`
  MODIFY `author_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `submission_documents`
--
ALTER TABLE `submission_documents`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT for table `submission_notes`
--
ALTER TABLE `submission_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `submission_notes_admin_views`
--
ALTER TABLE `submission_notes_admin_views`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ticket_tokens`
--
ALTER TABLE `ticket_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ticket_validation_attempts`
--
ALTER TABLE `ticket_validation_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_profiles`
--
ALTER TABLE `admin_profiles`
  ADD CONSTRAINT `fk_admin_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_profiles`
--
ALTER TABLE `employee_profiles`
  ADD CONSTRAINT `fk_employee_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_profiles`
--
ALTER TABLE `student_profiles`
  ADD CONSTRAINT `fk_student_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
