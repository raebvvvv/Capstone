-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 08, 2025 at 07:00 PM
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
(72, 63, 'SRID-2025-20251007-3', 1, 'approval_sheet', 'User #1 re-uploaded approval sheet (SRID-2025-20251007-3)', '2025-10-06 17:02:58', 1),
(73, 69, 'ERID-2025-20251008-1', 2, 'journal_publication_format', 'User #2 re-uploaded journal publication format (ERID-2025-20251008-1)', '2025-10-07 17:02:06', 1),
(74, 69, 'ERID-2025-20251008-1', 2, 'journal_publication_format', 'User #2 re-uploaded journal publication format (ERID-2025-20251008-1)', '2025-10-07 17:03:36', 1),
(75, 69, 'ERID-2025-20251008-1', 2, 'notarized_coauthorship', 'User #2 re-uploaded notarized coauthorship (ERID-2025-20251008-1)', '2025-10-07 17:03:36', 1),
(76, 69, 'ERID-2025-20251008-1', 2, 'notarized_copyright', 'User #2 re-uploaded notarized copyright (ERID-2025-20251008-1)', '2025-10-07 17:03:36', 1),
(77, 69, 'ERID-2025-20251008-1', 2, 'receipt_payment', 'User #2 re-uploaded receipt payment (ERID-2025-20251008-1)', '2025-10-07 17:03:37', 1),
(78, 69, 'ERID-2025-20251008-1', 2, 'record_copyright', 'User #2 re-uploaded record copyright (ERID-2025-20251008-1)', '2025-10-07 17:03:37', 1),
(79, 69, 'ERID-2025-20251008-1', 2, 'journal_publication_format', 'User #2 re-uploaded journal publication format (ERID-2025-20251008-1)', '2025-10-07 17:06:03', 1),
(80, 69, 'ERID-2025-20251008-1', 2, 'notarized_coauthorship', 'User #2 re-uploaded notarized coauthorship (ERID-2025-20251008-1)', '2025-10-07 17:06:03', 1),
(81, 69, 'ERID-2025-20251008-1', 2, 'notarized_copyright', 'User #2 re-uploaded notarized copyright (ERID-2025-20251008-1)', '2025-10-07 17:06:03', 1),
(82, 69, 'ERID-2025-20251008-1', 2, 'receipt_payment', 'User #2 re-uploaded receipt payment (ERID-2025-20251008-1)', '2025-10-07 17:06:03', 1),
(83, 69, 'ERID-2025-20251008-1', 2, 'record_copyright', 'User #2 re-uploaded record copyright (ERID-2025-20251008-1)', '2025-10-07 17:06:03', 1),
(84, 74, 'ERID-2025-20251008-6', 2, 'presentation', 'User #2 re-uploaded presentation (ERID-2025-20251008-6)', '2025-10-08 04:49:38', 1),
(85, 75, 'ERID-2025-20251008-7', 2, 'presentation', 'User #2 re-uploaded presentation (ERID-2025-20251008-7)', '2025-10-08 05:05:25', 1),
(86, 80, 'ERID-2025-20251008-12', 2, 'journal_publication_format', 'User #2 re-uploaded journal publication format (ERID-2025-20251008-12)', '2025-10-08 05:14:49', 1),
(87, 80, 'ERID-2025-20251008-12', 2, 'notarized_coauthorship', 'User #2 re-uploaded notarized coauthorship (ERID-2025-20251008-12)', '2025-10-08 05:14:49', 1),
(88, 80, 'ERID-2025-20251008-12', 2, 'notarized_copyright', 'User #2 re-uploaded notarized copyright (ERID-2025-20251008-12)', '2025-10-08 05:14:49', 1),
(89, 80, 'ERID-2025-20251008-12', 2, 'presentation', 'User #2 re-uploaded presentation (ERID-2025-20251008-12)', '2025-10-08 05:14:49', 1),
(90, 80, 'ERID-2025-20251008-12', 2, 'receipt_payment', 'User #2 re-uploaded receipt payment (ERID-2025-20251008-12)', '2025-10-08 05:14:49', 1),
(91, 80, 'ERID-2025-20251008-12', 2, 'record_copyright', 'User #2 re-uploaded record copyright (ERID-2025-20251008-12)', '2025-10-08 05:14:49', 1),
(92, 85, 'ERID-2025-20251008-17', 2, 'journal_publication_format', 'User #2 re-uploaded journal publication format (ERID-2025-20251008-17)', '2025-10-08 05:42:17', 1),
(93, 85, 'ERID-2025-20251008-17', 2, 'notarized_coauthorship', 'User #2 re-uploaded notarized coauthorship (ERID-2025-20251008-17)', '2025-10-08 05:42:17', 1),
(94, 85, 'ERID-2025-20251008-17', 2, 'notarized_copyright', 'User #2 re-uploaded notarized copyright (ERID-2025-20251008-17)', '2025-10-08 05:42:17', 1),
(95, 85, 'ERID-2025-20251008-17', 2, 'presentation', 'User #2 re-uploaded presentation (ERID-2025-20251008-17)', '2025-10-08 05:42:17', 1),
(96, 85, 'ERID-2025-20251008-17', 2, 'receipt_payment', 'User #2 re-uploaded receipt payment (ERID-2025-20251008-17)', '2025-10-08 05:42:17', 1),
(97, 85, 'ERID-2025-20251008-17', 2, 'record_copyright', 'User #2 re-uploaded record copyright (ERID-2025-20251008-17)', '2025-10-08 05:42:17', 1),
(98, 91, 'ERID-2025-20251008-23', 6, 'journal_publication_format', 'User #6 re-uploaded journal publication format (ERID-2025-20251008-23)', '2025-10-08 07:43:09', 1),
(99, 91, 'ERID-2025-20251008-23', 6, 'notarized_coauthorship', 'User #6 re-uploaded notarized coauthorship (ERID-2025-20251008-23)', '2025-10-08 07:43:09', 1),
(100, 91, 'ERID-2025-20251008-23', 6, 'notarized_copyright', 'User #6 re-uploaded notarized copyright (ERID-2025-20251008-23)', '2025-10-08 07:43:09', 1),
(101, 91, 'ERID-2025-20251008-23', 6, 'presentation', 'User #6 re-uploaded presentation (ERID-2025-20251008-23)', '2025-10-08 07:43:09', 1),
(102, 91, 'ERID-2025-20251008-23', 6, 'receipt_payment', 'User #6 re-uploaded receipt payment (ERID-2025-20251008-23)', '2025-10-08 07:43:09', 1),
(103, 91, 'ERID-2025-20251008-23', 6, 'record_copyright', 'User #6 re-uploaded record copyright (ERID-2025-20251008-23)', '2025-10-08 07:43:09', 1),
(104, 88, 'SRID-2025-20251008-20', 1, 'approval_sheet', 'User #1 re-uploaded approval sheet (SRID-2025-20251008-20)', '2025-10-08 07:44:26', 1),
(105, 90, 'SRID-2025-20251008-22', 1, 'approval_sheet', 'User #1 re-uploaded approval sheet (SRID-2025-20251008-22)', '2025-10-08 07:44:38', 1),
(106, 92, 'SRID-2025-20251008-24', 1, 'approval_sheet', 'User #1 re-uploaded approval sheet (SRID-2025-20251008-24)', '2025-10-08 07:51:00', 1),
(107, 94, 'SRID-2025-20251008-26', 1, 'approval_sheet', 'User #1 re-uploaded approval sheet (SRID-2025-20251008-26)', '2025-10-08 07:56:08', 1),
(108, 94, 'SRID-2025-20251008-26', 1, 'approval_sheet', 'User #1 re-uploaded approval sheet (SRID-2025-20251008-26)', '2025-10-08 08:00:56', 1);

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
(2, 4, 'ADM-001', 'Dela Cruz', 'Juan', 'IPMO', NULL, 'IPMO Office, Main Campus', '09171234567', 'Intellectual Property Management Office', '2025-10-06 03:55:43'),
(3, 4, 'ADM-002', 'Kanin', 'Manang', 'IPMO', NULL, 'IPMO Office, Main Campus', '09171234567', 'Intellectual Property Management Office', '2025-10-06 03:55:43'),
(4, 4, 'ADM-003', 'Sabaw', 'Manong', 'IPMO', NULL, 'IPMO Office, Main Campus', '09171234567', 'Intellectual Property Management Office', '2025-10-06 03:55:43');

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
(28, 'John', '', '', NULL, NULL, NULL, '2025-10-07 01:51:55'),
(29, 'Csd', '', '', NULL, NULL, NULL, '2025-10-08 13:14:26'),
(30, 'Cs', '', '', NULL, NULL, NULL, '2025-10-08 13:15:26'),
(31, 'S', '', '', NULL, NULL, NULL, '2025-10-08 13:18:59'),
(32, 'Ac', '', '', NULL, NULL, NULL, '2025-10-08 13:40:31'),
(33, 'D', '', '', NULL, NULL, NULL, '2025-10-08 13:41:56'),
(34, 'Sdf', '', '', NULL, NULL, NULL, '2025-10-08 13:43:05'),
(35, '', '', '', NULL, NULL, NULL, '2025-10-08 13:44:39');

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
(1, '2025-10-08 22:08:41', 3, 43, 5, 7, 31, 22, 20, 1, '{\"labels\":[\"College of Social Sciences and Development (CSSD)\",\"College of Human Kinetics (CHK)\",\"College of Education (COED)\",\"College of Computer and Information Sciences (CCIS)\",\"College of Accountancy and Finance (CAF)\",\"College of Science (CS)\"],\"values\":[19,9,6,2,1,1]}', '{\"labels\":[\"PUP Main (Sta. Mesa, Manila)\"],\"values\":[43]}', '{\"labels\":[\"(a) Books, Pamphlets, articles and other writings\",\"(b) Periodicals and newspaper\",\"(p) Sound recordings\",\"(m) Pictorial illustrations and advertisements\",\"(n) Computer Programs\",\"(q) Broadcast recordings\",\"(o) Other literary, scholarly, scientific and artistic works\",\"(k) Photographic works including works produced by a process analogous to photography\",\"(l) Audiovisual works and cinematographic works\",\"(c) Lectures, sermons, addresses, dissertations for oral delivery\"],\"values\":[10,8,7,4,4,3,2,2,2,1]}');

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
  `academic_level` varchar(50) DEFAULT NULL,
  `college` varchar(255) DEFAULT NULL,
  `department` varchar(255) NOT NULL,
  `program` varchar(255) DEFAULT NULL,
  `last_updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_profiles`
--

INSERT INTO `employee_profiles` (`profile_id`, `user_id`, `employee_number`, `last_name`, `first_name`, `middle_name`, `suffix`, `home_address`, `mobile_number`, `campus`, `academic_level`, `college`, `department`, `program`, `last_updated_at`) VALUES
(1, 2, '12345', 'Lino', 'Mata', 'Bale', '', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234565', 'PUP Main (Sta. Mesa, Manila)', 'Not Studying', 'College of Social Sciences and Development (CSSD)', 'College Of Science', 'N/A', NULL),
(2, 6, '54321', 'Inocentes', 'Raebv Lielmo', 'A', '', '4334A V. Francisco St. Sta. Mesa, Manila', '09156574831', 'PUP Main (Sta. Mesa, Manila)', 'Doctorate', 'College of Computer and Information Sciences (CCIS)', 'Department of Computer Science', 'Bachelor of Science in Computer Science (BSCS)', '2025-10-08 14:56:13');

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
(16, 1, '2025-12346-MN-0', 'Minamo', 'Marisa', 'Mliinaw', '', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Education (COED)', '', 'Bachelor of Secondary Education - English (BSEd)', '2025-10-08 15:03:38'),
(17, 5, '2022-00880-MN-0', 'Inocentes', 'Raebv Lielmo', 'A', '', '4334A V. Francisco St. Sta. Mesa, Manila', '09156574831', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Architecture, Design and the Built Environment (CADBE)', '', 'Bachelor Of Science In Architecture (bs-arch)', NULL),
(18, 7, '2022-08379-MN-0', 'Cruz', 'Juan', 'Dela', '', '4334A V. Francisco St.', '09156574831', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Accountancy and Finance (CAF)', '', 'Bachelor Of Science In Accountancy (bsa)', NULL),
(25, 14, '2022-99999-MN-0', 'Cruz', 'Bo', 'Dela', '', '4334A V. Francisco St.', '09156574831', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Architecture, Design and the Built Environment (CADBE)', '', 'Bachelor Of Science In Architecture (bs-arch)', NULL);

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
(68, 'SRID-2025-20251007-8', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Human Kinetics (CHK)', 'Bachelor Of Physical Education (bpe)', '(a) Books, Pamphlets, articles and other writings', 'BOOM', '2025-10-07', 1, 'completed', '2025-10-07 01:53:24', 'testttt', 1, 1, 'copyright', '2025-10-07 01:51:55', '2025-10-07 01:53:24', 4, '2025-10-07 01:52:00', 28),
(69, 'ERID-2025-20251008-1', 2, 'Mata', 'Bale', 'Lino', '12345', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234565', 'errorloading19990@gmail.com', 'PUP Main (Sta. Mesa, Manila)', '', 'College of Social Sciences and Development (CSSD)', 'N/A', '(p) Sound recordings', 'TESTTTING1', '2025-10-08', 1, 'completed', '2025-10-08 01:09:24', 'GOGOGO', 1, 1, 'copyright', '2025-10-08 00:57:49', '2025-10-08 01:14:35', 4, '2025-10-08 01:06:29', 25),
(70, 'ERID-2025-20251008-2', 2, 'Mata', 'Bale', 'Lino', '12345', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234565', 'errorloading19990@gmail.com', 'PUP Main (Sta. Mesa, Manila)', '', 'College of Social Sciences and Development (CSSD)', 'N/A', '(k) Photographic works including works produced by a process analogous to photography', '2TEST', '2025-10-08', 1, 'approved', '2025-10-08 13:04:02', 'for evaluation', 1, 1, 'copyright', '2025-10-08 01:16:30', '2025-10-08 13:04:02', 4, '2025-10-08 13:04:02', 25),
(71, 'ERID-2025-20251008-3', 2, 'Mata', 'Bale', 'Lino', '12345', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234565', 'errorloading19990@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Masters', 'College of Social Sciences and Development (CSSD)', 'N/A', '(a) Books, Pamphlets, articles and other writings', 'test3', '2025-10-08', 1, 'completed', '2025-10-08 01:41:35', 'for evaluation', 1, 1, 'copyright', '2025-10-08 01:40:51', '2025-10-08 01:41:35', 4, '2025-10-08 01:41:23', 25),
(72, 'ERID-2025-20251008-4', 2, 'Mata', 'Bale', 'Lino', '12345', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234565', 'errorloading19990@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Masters', 'College of Social Sciences and Development (CSSD)', 'N/A', '(l) Audiovisual works and cinematographic works', 'test4', '2025-10-08', 1, 'approved', '2025-10-08 12:59:16', 'testtt', 1, 1, 'copyright', '2025-10-08 01:42:59', '2025-10-08 12:59:16', 4, '2025-10-08 12:59:16', 25),
(73, 'ERID-2025-20251008-5', 2, 'Mata', 'Bale', 'Lino', '12345', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234565', 'errorloading19990@gmail.com', 'PUP Main (Sta. Mesa, Manila)', '', 'College of Social Sciences and Development (CSSD)', 'N/A', '(a) Books, Pamphlets, articles and other writings', 'test5', '2025-10-08', 1, 'approved', '2025-10-08 12:58:58', 'testtttiungg', 1, 1, 'copyright', '2025-10-08 01:52:43', '2025-10-08 12:58:58', 4, '2025-10-08 12:58:58', 25),
(74, 'ERID-2025-20251008-6', 2, 'Mata', 'Bale', 'Lino', '12345', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234565', 'errorloading19990@gmail.com', 'PUP Main (Sta. Mesa, Manila)', '', 'College of Social Sciences and Development (CSSD)', 'N/A', '(p) Sound recordings', 'test6', '2025-10-08', 1, 'approved', '2025-10-08 12:58:46', 'For Evaluation', 1, 1, 'copyright', '2025-10-08 02:07:39', '2025-10-08 12:58:46', 4, '2025-10-08 12:58:46', 25),
(75, 'ERID-2025-20251008-7', 2, 'Mata', 'Bale', 'Lino', '12345', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234565', 'errorloading19990@gmail.com', 'PUP Main (Sta. Mesa, Manila)', '', 'College of Social Sciences and Development (CSSD)', 'N/A', '(a) Books, Pamphlets, articles and other writings', 'testfor2', '2025-10-08', 1, 'approved', '2025-10-08 13:05:36', 'For Evaluation', 1, 1, 'copyright', '2025-10-08 13:05:01', '2025-10-08 13:05:36', 4, '2025-10-08 13:05:36', 25),
(76, 'ERID-2025-20251008-8', 2, 'Mata', 'Bale', 'Lino', '12345', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234565', 'errorloading19990@gmail.com', 'PUP Main (Sta. Mesa, Manila)', '', 'College of Social Sciences and Development (CSSD)', 'N/A', '(q) Broadcast recordings', 'test3', '2025-10-08', 1, 'approved', '2025-10-08 13:09:33', 'For Evaluation', 1, 1, 'copyright', '2025-10-08 13:07:58', '2025-10-08 13:09:33', 4, '2025-10-08 13:09:33', 25),
(77, 'ERID-2025-20251008-9', 2, 'Mata', 'Bale', 'Lino', '12345', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234565', 'errorloading19990@gmail.com', 'PUP Main (Sta. Mesa, Manila)', '', 'College of Social Sciences and Development (CSSD)', 'N/A', '(a) Books, Pamphlets, articles and other writings', 'pentest', '2025-10-08', 1, 'completed', '2025-10-08 13:39:36', 'testset', 1, 1, 'copyright', '2025-10-08 13:08:26', '2025-10-08 13:39:36', 4, '2025-10-08 13:09:12', 24),
(78, 'ERID-2025-20251008-10', 2, 'Mata', 'Bale', 'Lino', '12345', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234565', 'errorloading19990@gmail.com', 'PUP Main (Sta. Mesa, Manila)', '', 'College of Social Sciences and Development (CSSD)', 'N/A', '(a) Books, Pamphlets, articles and other writings', 'banh', '2025-10-08', 1, 'completed', '2025-10-08 13:17:18', 'test3', 1, 1, 'copyright', '2025-10-08 13:13:11', '2025-10-08 13:17:18', 4, '2025-10-08 13:13:55', 26),
(79, 'ERID-2025-20251008-11', 2, 'Mata', 'Bale', 'Lino', '12345', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234565', 'errorloading19990@gmail.com', 'PUP Main (Sta. Mesa, Manila)', '', 'College of Social Sciences and Development (CSSD)', 'N/A', '(b) Periodicals and newspaper', 'bang2', '2025-10-08', 1, 'completed', '2025-10-08 13:39:25', NULL, 1, 1, 'copyright', '2025-10-08 13:13:31', '2025-10-08 13:39:25', 4, '2025-10-08 13:14:03', 25),
(80, 'ERID-2025-20251008-12', 2, 'Mata', 'Bale', 'Lino', '12345', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234565', 'errorloading19990@gmail.com', 'PUP Main (Sta. Mesa, Manila)', '', 'College of Social Sciences and Development (CSSD)', 'N/A', '(o) Other literary, scholarly, scientific and artistic works', 'bang3', '2025-10-08', 1, 'completed', '2025-10-08 13:16:20', 'For Evaluation', 1, 1, 'copyright', '2025-10-08 13:14:26', '2025-10-08 13:16:20', 4, '2025-10-08 13:15:02', 29),
(81, 'ERID-2025-20251008-13', 2, 'Mata', 'Bale', 'Lino', '12345', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234565', 'errorloading19990@gmail.com', 'PUP Main (Sta. Mesa, Manila)', '', 'College of Social Sciences and Development (CSSD)', 'N/A', '(a) Books, Pamphlets, articles and other writings', 'bang5', '2025-10-08', 1, 'completed', '2025-10-08 13:16:59', 'test2', 1, 1, 'copyright', '2025-10-08 13:15:26', '2025-10-08 13:16:59', 4, '2025-10-08 13:15:55', 30),
(82, 'ERID-2025-20251008-14', 2, 'Mata', 'Bale', 'Lino', '12345', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234565', 'errorloading19990@gmail.com', 'PUP Main (Sta. Mesa, Manila)', '', 'College of Social Sciences and Development (CSSD)', 'N/A', '(p) Sound recordings', 'bang6t', '2025-10-08', 1, 'completed', '2025-10-08 13:19:30', 'for evaluation', 1, 1, 'copyright', '2025-10-08 13:18:59', '2025-10-08 13:19:30', 4, '2025-10-08 13:19:04', 31),
(83, 'ERID-2025-20251008-15', 2, 'Mata', 'Bale', 'Lino', '12345', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234565', 'errorloading19990@gmail.com', 'PUP Main (Sta. Mesa, Manila)', '', 'College of Social Sciences and Development (CSSD)', 'N/A', '(k) Photographic works including works produced by a process analogous to photography', 'BANG1', '2025-10-08', 1, 'completed', '2025-10-08 13:41:33', NULL, 1, 1, 'copyright', '2025-10-08 13:40:12', '2025-10-08 13:41:33', 4, '2025-10-08 13:41:06', 25),
(84, 'ERID-2025-20251008-16', 2, 'Mata', 'Bale', 'Lino', '12345', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234565', 'errorloading19990@gmail.com', 'PUP Main (Sta. Mesa, Manila)', '', 'College of Social Sciences and Development (CSSD)', 'N/A', '(b) Periodicals and newspaper', 'BANG2', '2025-10-08', 1, 'completed', '2025-10-08 13:41:16', NULL, 1, 1, 'copyright', '2025-10-08 13:40:31', '2025-10-08 13:41:16', 4, '2025-10-08 13:40:52', 32),
(85, 'ERID-2025-20251008-17', 2, 'Mata', 'Bale', 'Lino', '12345', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234565', 'errorloading19990@gmail.com', 'PUP Main (Sta. Mesa, Manila)', '', 'College of Social Sciences and Development (CSSD)', 'N/A', '(m) Pictorial illustrations and advertisements', 'bagn3', '2025-10-08', 1, 'completed', '2025-10-08 13:42:34', NULL, 1, 1, 'copyright', '2025-10-08 13:41:56', '2025-10-08 13:42:34', 4, '2025-10-08 13:42:28', 33),
(86, 'ERID-2025-20251008-18', 2, 'Mata', 'Bale', 'Lino', '12345', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234565', 'errorloading19990@gmail.com', 'PUP Main (Sta. Mesa, Manila)', '', 'College of Social Sciences and Development (CSSD)', 'N/A', '(c) Lectures, sermons, addresses, dissertations for oral delivery', 'bang5', '2025-10-08', 1, 'completed', '2025-10-08 13:44:12', NULL, 1, 1, 'copyright', '2025-10-08 13:43:05', '2025-10-08 13:44:12', 4, '2025-10-08 13:43:47', 34),
(87, 'ERID-2025-20251008-19', 2, 'Mata', 'Bale', 'Lino', '12345', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234565', 'errorloading19990@gmail.com', 'PUP Main (Sta. Mesa, Manila)', '', 'College of Social Sciences and Development (CSSD)', 'N/A', '(b) Periodicals and newspaper', 'bahn3', '2025-10-08', 1, 'completed', '2025-10-08 13:47:53', 'mark2', 1, 1, 'copyright', '2025-10-08 13:44:39', '2025-10-08 13:47:53', 4, '2025-10-08 13:44:58', 35),
(88, 'SRID-2025-20251008-20', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Education (COED)', 'Bachelor of Secondary Education - English (BSEd)', '(l) Audiovisual works and cinematographic works', 'test', '2025-10-08', 1, 'pending_review', '2025-10-08 15:26:46', 'For Evaluation', 1, 1, 'copyright', '2025-10-08 15:10:02', '2025-10-08 15:44:26', NULL, NULL, 35),
(89, 'SRID-2025-20251008-21', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Education (COED)', 'Bachelor of Secondary Education - English (BSEd)', '(b) Periodicals and newspaper', 'test', '2025-10-08', 1, 'approved', '2025-10-08 16:13:04', 'ttt', 1, 1, 'copyright', '2025-10-08 15:21:06', '2025-10-08 16:13:04', 4, '2025-10-08 15:21:24', 35),
(90, 'SRID-2025-20251008-22', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Education (COED)', 'Bachelor of Secondary Education - English (BSEd)', '(n) Computer Programs', 'test', '2025-10-08', 1, 'pending_review', '2025-10-08 15:27:23', 'For Evaluation', 1, 1, 'copyright', '2025-10-08 15:27:08', '2025-10-08 15:44:38', NULL, NULL, 35),
(91, 'ERID-2025-20251008-23', 6, 'Raebv Lielmo', 'A', 'Inocentes', '54321', '4334A V. Francisco St. Sta. Mesa, Manila', '09156574831', 'thinkingwan00@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Doctorate', 'College of Computer and Information Sciences (CCIS)', 'Bachelor of Science in Computer Science (BSCS)', '(b) Periodicals and newspaper', 'test', '2025-10-08', 1, 'pending_review', '2025-10-08 15:42:53', 'For Evaluation', 1, 1, 'copyright', '2025-10-08 15:36:40', '2025-10-08 15:43:09', NULL, NULL, 35),
(92, 'SRID-2025-20251008-24', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Education (COED)', 'Bachelor of Secondary Education - English (BSEd)', '(p) Sound recordings', 'test', '2025-10-08', 1, 'pending_review', '2025-10-08 15:50:52', 'For Evaluation', 1, 1, 'copyright', '2025-10-08 15:48:29', '2025-10-08 15:51:00', NULL, NULL, 35),
(93, 'SRID-2025-20251008-25', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Education (COED)', 'Bachelor of Secondary Education - English (BSEd)', '(n) Computer Programs', 'test', '2025-10-08', 1, 'pending_review', NULL, 'for evaluation', 1, 1, 'copyright', '2025-10-08 15:51:20', '2025-10-08 15:51:20', NULL, NULL, 35),
(94, 'SRID-2025-20251008-26', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Education (COED)', 'Bachelor of Secondary Education - English (BSEd)', '(p) Sound recordings', 't', '2025-10-08', 1, 'completed', '2025-10-08 18:02:27', NULL, 1, 1, 'copyright', '2025-10-08 15:55:46', '2025-10-08 18:02:27', 4, '2025-10-08 18:02:21', 35),
(95, 'ERID-2025-20251008-27', 6, 'Raebv Lielmo', 'A', 'Inocentes', '54321', '4334A V. Francisco St. Sta. Mesa, Manila', '09156574831', 'thinkingwan00@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Doctorate', 'College of Computer and Information Sciences (CCIS)', 'Bachelor of Science in Computer Science (BSCS)', '(n) Computer Programs', 'test', '2025-10-08', 1, 'completed', '2025-10-08 20:55:15', NULL, 1, 1, 'copyright', '2025-10-08 20:55:01', '2025-10-08 20:55:15', 4, '2025-10-08 20:55:09', 35),
(96, 'ERID-2025-20251008-28', 6, 'Raebv Lielmo', 'A', 'Inocentes', '54321', '4334A V. Francisco St. Sta. Mesa, Manila', '09156574831', 'thinkingwan00@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Doctorate', 'College of Computer and Information Sciences (CCIS)', 'Bachelor of Science in Computer Science (BSCS)', '(p) Sound recordings', 'test', '2025-10-08', 1, 'pending_review', NULL, 'for evaluation', 1, 1, 'copyright', '2025-10-08 22:36:17', '2025-10-08 22:36:17', NULL, NULL, 32);

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
(18, 68, 'Raebv Lielmo', '', 'Inocentes', '2020-09121-MN-0', '09156574831', '4334A V. Francisco St. Sta. Mesa, Manila', 'inocentesraebv@iskolarngbayan.pup.edu.ph', 'Author', 0, '2025-10-07 01:51:55', NULL),
(19, 69, 'Raebv Lielmo', '', 'Inocentes', '25222', '09156574831', '4334A V. Francisco St. Sta. Mesa, Manila', 'firstnamelastname@iskolarngbayan.pup.edu.ph', 'Author', 0, '2025-10-08 00:57:49', NULL);

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
(111, 68, 'record_copyright', 'record_copyright_1759773115_33629635.pdf', '2025-10-07 01:51:55', 64202, 'application/pdf', 0, NULL, NULL),
(112, 69, 'journal_publication_format', 'journal_publication_format_1759856763_ac858d59.pdf', '2025-10-08 01:06:03', 361007, 'application/pdf', 0, NULL, NULL),
(113, 69, 'notarized_copyright', 'notarized_copyright_1759856763_21698bb8.pdf', '2025-10-08 01:06:03', 361007, 'application/pdf', 0, NULL, NULL),
(114, 69, 'receipt_payment', 'receipt_payment_1759856763_4516908e.pdf', '2025-10-08 01:06:03', 361007, 'application/pdf', 0, NULL, NULL),
(115, 69, 'presentation', 'presentation_1759856269_47e4d18f.pdf', '2025-10-08 00:57:49', 66396, 'application/pdf', 0, NULL, NULL),
(116, 69, 'notarized_coauthorship', 'notarized_coauthorship_1759856763_0b38f0e3.pdf', '2025-10-08 01:06:03', 361007, 'application/pdf', 0, NULL, NULL),
(117, 69, 'record_copyright', 'record_copyright_1759856763_22634edd.pdf', '2025-10-08 01:06:03', 361007, 'application/pdf', 0, NULL, NULL),
(118, 70, 'journal_publication_format', 'journal_publication_format_1759857390_35ac5e94.pdf', '2025-10-08 01:16:30', 86468, 'application/pdf', 0, NULL, NULL),
(119, 70, 'notarized_copyright', 'notarized_copyright_1759857390_73c63e04.pdf', '2025-10-08 01:16:30', 86468, 'application/pdf', 0, NULL, NULL),
(120, 70, 'receipt_payment', 'receipt_payment_1759857390_05b65777.pdf', '2025-10-08 01:16:30', 86468, 'application/pdf', 0, NULL, NULL),
(121, 70, 'presentation', 'presentation_1759857390_58393924.pdf', '2025-10-08 01:16:30', 86468, 'application/pdf', 0, NULL, NULL),
(122, 70, 'notarized_coauthorship', 'notarized_coauthorship_1759857390_c67eae99.pdf', '2025-10-08 01:16:30', 86468, 'application/pdf', 0, NULL, NULL),
(123, 70, 'record_copyright', 'record_copyright_1759857390_a4a449e0.pdf', '2025-10-08 01:16:30', 86468, 'application/pdf', 0, NULL, NULL),
(124, 71, 'journal_publication_format', 'journal_publication_format_1759858851_04b93a87.pdf', '2025-10-08 01:40:51', 66396, 'application/pdf', 0, NULL, NULL),
(125, 71, 'notarized_copyright', 'notarized_copyright_1759858851_280822e3.pdf', '2025-10-08 01:40:51', 66396, 'application/pdf', 0, NULL, NULL),
(126, 71, 'receipt_payment', 'receipt_payment_1759858851_91c373e2.pdf', '2025-10-08 01:40:51', 66396, 'application/pdf', 0, NULL, NULL),
(127, 71, 'presentation', 'presentation_1759858851_577bc427.pdf', '2025-10-08 01:40:51', 86468, 'application/pdf', 0, NULL, NULL),
(128, 71, 'notarized_coauthorship', 'notarized_coauthorship_1759858851_7bd17e5d.pdf', '2025-10-08 01:40:51', 66396, 'application/pdf', 0, NULL, NULL),
(129, 71, 'record_copyright', 'record_copyright_1759858851_138ab5a0.pdf', '2025-10-08 01:40:51', 66396, 'application/pdf', 0, NULL, NULL),
(130, 72, 'journal_publication_format', 'journal_publication_format_1759858979_a05e9d1a.pdf', '2025-10-08 01:42:59', 86468, 'application/pdf', 0, NULL, NULL),
(131, 72, 'notarized_copyright', 'notarized_copyright_1759858979_a6103fe5.pdf', '2025-10-08 01:42:59', 86468, 'application/pdf', 0, NULL, NULL),
(132, 72, 'receipt_payment', 'receipt_payment_1759858979_5fc2b116.pdf', '2025-10-08 01:42:59', 86468, 'application/pdf', 0, NULL, NULL),
(133, 72, 'presentation', 'presentation_1759858979_0d5ffa7d.pdf', '2025-10-08 01:42:59', 86468, 'application/pdf', 0, NULL, NULL),
(134, 72, 'notarized_coauthorship', 'notarized_coauthorship_1759858979_ae63dc73.pdf', '2025-10-08 01:42:59', 86468, 'application/pdf', 0, NULL, NULL),
(135, 72, 'record_copyright', 'record_copyright_1759858979_7d32a588.pdf', '2025-10-08 01:42:59', 86468, 'application/pdf', 0, NULL, NULL),
(136, 73, 'journal_publication_format', 'journal_publication_format_1759859563_12f40023.pdf', '2025-10-08 01:52:43', 86468, 'application/pdf', 0, NULL, NULL),
(137, 73, 'notarized_copyright', 'notarized_copyright_1759859563_d5de69b1.pdf', '2025-10-08 01:52:43', 86468, 'application/pdf', 0, NULL, NULL),
(138, 73, 'receipt_payment', 'receipt_payment_1759859563_fe5ecc2c.pdf', '2025-10-08 01:52:43', 86468, 'application/pdf', 0, NULL, NULL),
(139, 73, 'presentation', 'presentation_1759859563_218744ab.pdf', '2025-10-08 01:52:43', 86468, 'application/pdf', 0, NULL, NULL),
(140, 73, 'notarized_coauthorship', 'notarized_coauthorship_1759859563_25bcf54e.pdf', '2025-10-08 01:52:43', 86468, 'application/pdf', 0, NULL, NULL),
(141, 73, 'record_copyright', 'record_copyright_1759859563_adfcd7ba.pdf', '2025-10-08 01:52:43', 86468, 'application/pdf', 0, NULL, NULL),
(142, 74, 'journal_publication_format', 'journal_publication_format_1759860459_8db5cec3.pdf', '2025-10-08 02:07:39', 86468, 'application/pdf', 0, NULL, NULL),
(143, 74, 'notarized_copyright', 'notarized_copyright_1759860459_6cca432d.pdf', '2025-10-08 02:07:39', 86468, 'application/pdf', 0, NULL, NULL),
(144, 74, 'receipt_payment', 'receipt_payment_1759860459_d9d7213c.pdf', '2025-10-08 02:07:39', 86468, 'application/pdf', 0, NULL, NULL),
(145, 74, 'presentation', 'presentation_1759898978_a2373a7d.pdf', '2025-10-08 12:49:38', 86468, 'application/pdf', 0, NULL, NULL),
(146, 74, 'notarized_coauthorship', 'notarized_coauthorship_1759860459_0e01e89e.pdf', '2025-10-08 02:07:39', 86468, 'application/pdf', 0, NULL, NULL),
(147, 74, 'record_copyright', 'record_copyright_1759860459_4de2aa04.pdf', '2025-10-08 02:07:39', 86468, 'application/pdf', 0, NULL, NULL),
(148, 75, 'journal_publication_format', 'journal_publication_format_1759899901_ffc7481a.pdf', '2025-10-08 13:05:01', 86468, 'application/pdf', 0, NULL, NULL),
(149, 75, 'notarized_copyright', 'notarized_copyright_1759899901_a7b85a66.pdf', '2025-10-08 13:05:01', 86468, 'application/pdf', 0, NULL, NULL),
(150, 75, 'receipt_payment', 'receipt_payment_1759899901_58fcb6ce.pdf', '2025-10-08 13:05:01', 86468, 'application/pdf', 0, NULL, NULL),
(151, 75, 'presentation', 'presentation_1759899925_76f25dec.pdf', '2025-10-08 13:05:25', 86468, 'application/pdf', 0, NULL, NULL),
(152, 75, 'notarized_coauthorship', 'notarized_coauthorship_1759899901_12dc6629.pdf', '2025-10-08 13:05:01', 86468, 'application/pdf', 0, NULL, NULL),
(153, 75, 'record_copyright', 'record_copyright_1759899901_d7033d4d.pdf', '2025-10-08 13:05:01', 86468, 'application/pdf', 0, NULL, NULL),
(154, 76, 'journal_publication_format', 'journal_publication_format_1759900078_874506bb.pdf', '2025-10-08 13:07:58', 66396, 'application/pdf', 0, NULL, NULL),
(155, 76, 'notarized_copyright', 'notarized_copyright_1759900078_184f33ba.pdf', '2025-10-08 13:07:58', 66396, 'application/pdf', 0, NULL, NULL),
(156, 76, 'receipt_payment', 'receipt_payment_1759900078_1c5f16cc.pdf', '2025-10-08 13:07:58', 66396, 'application/pdf', 0, NULL, NULL),
(157, 76, 'presentation', 'presentation_1759900078_49e5db3c.pdf', '2025-10-08 13:07:58', 66396, 'application/pdf', 0, NULL, NULL),
(158, 76, 'notarized_coauthorship', 'notarized_coauthorship_1759900078_7abe7fe5.pdf', '2025-10-08 13:07:58', 66396, 'application/pdf', 0, NULL, NULL),
(159, 76, 'record_copyright', 'record_copyright_1759900078_45f9c608.pdf', '2025-10-08 13:07:58', 66396, 'application/pdf', 0, NULL, NULL),
(160, 77, 'journal_publication_format', 'journal_publication_format_1759900106_0392599b.pdf', '2025-10-08 13:08:26', 86468, 'application/pdf', 0, NULL, NULL),
(161, 77, 'notarized_copyright', 'notarized_copyright_1759900106_c6672534.pdf', '2025-10-08 13:08:26', 86468, 'application/pdf', 0, NULL, NULL),
(162, 77, 'receipt_payment', 'receipt_payment_1759900106_0e826fd2.pdf', '2025-10-08 13:08:26', 66396, 'application/pdf', 0, NULL, NULL),
(163, 77, 'presentation', 'presentation_1759900106_22298404.pdf', '2025-10-08 13:08:26', 86468, 'application/pdf', 0, NULL, NULL),
(164, 77, 'notarized_coauthorship', 'notarized_coauthorship_1759900106_216faf62.pdf', '2025-10-08 13:08:26', 66396, 'application/pdf', 0, NULL, NULL),
(165, 77, 'record_copyright', 'record_copyright_1759900106_64e4ebbb.pdf', '2025-10-08 13:08:26', 66396, 'application/pdf', 0, NULL, NULL),
(166, 78, 'journal_publication_format', 'journal_publication_format_1759900391_a391efe0.pdf', '2025-10-08 13:13:11', 66396, 'application/pdf', 0, NULL, NULL),
(167, 78, 'notarized_copyright', 'notarized_copyright_1759900391_4fc36f47.pdf', '2025-10-08 13:13:11', 66396, 'application/pdf', 0, NULL, NULL),
(168, 78, 'receipt_payment', 'receipt_payment_1759900391_b5afb2d8.pdf', '2025-10-08 13:13:11', 66396, 'application/pdf', 0, NULL, NULL),
(169, 78, 'presentation', 'presentation_1759900391_48583f24.pdf', '2025-10-08 13:13:11', 66396, 'application/pdf', 0, NULL, NULL),
(170, 78, 'notarized_coauthorship', 'notarized_coauthorship_1759900391_f3e0e2e0.pdf', '2025-10-08 13:13:11', 66396, 'application/pdf', 0, NULL, NULL),
(171, 78, 'record_copyright', 'record_copyright_1759900391_5f9e7b07.pdf', '2025-10-08 13:13:11', 66396, 'application/pdf', 0, NULL, NULL),
(172, 79, 'journal_publication_format', 'journal_publication_format_1759900411_70428d58.pdf', '2025-10-08 13:13:31', 66396, 'application/pdf', 0, NULL, NULL),
(173, 79, 'notarized_copyright', 'notarized_copyright_1759900411_39185702.pdf', '2025-10-08 13:13:31', 66396, 'application/pdf', 0, NULL, NULL),
(174, 79, 'receipt_payment', 'receipt_payment_1759900411_0fc8922f.pdf', '2025-10-08 13:13:31', 66396, 'application/pdf', 0, NULL, NULL),
(175, 79, 'presentation', 'presentation_1759900411_4cc55b1a.pdf', '2025-10-08 13:13:31', 66396, 'application/pdf', 0, NULL, NULL),
(176, 79, 'notarized_coauthorship', 'notarized_coauthorship_1759900411_82d76df1.pdf', '2025-10-08 13:13:31', 66396, 'application/pdf', 0, NULL, NULL),
(177, 79, 'record_copyright', 'record_copyright_1759900411_6fbd898f.pdf', '2025-10-08 13:13:31', 66396, 'application/pdf', 0, NULL, NULL),
(178, 80, 'journal_publication_format', 'journal_publication_format_1759900489_8d5fd99c.pdf', '2025-10-08 13:14:49', 86468, 'application/pdf', 0, NULL, NULL),
(179, 80, 'notarized_copyright', 'notarized_copyright_1759900489_4a792d5e.pdf', '2025-10-08 13:14:49', 86468, 'application/pdf', 0, NULL, NULL),
(180, 80, 'receipt_payment', 'receipt_payment_1759900489_c021b64d.pdf', '2025-10-08 13:14:49', 86468, 'application/pdf', 0, NULL, NULL),
(181, 80, 'presentation', 'presentation_1759900489_ea9dfb23.pdf', '2025-10-08 13:14:49', 86468, 'application/pdf', 0, NULL, NULL),
(182, 80, 'notarized_coauthorship', 'notarized_coauthorship_1759900489_9e6366d3.pdf', '2025-10-08 13:14:49', 86468, 'application/pdf', 0, NULL, NULL),
(183, 80, 'record_copyright', 'record_copyright_1759900489_021c4b1f.pdf', '2025-10-08 13:14:49', 86468, 'application/pdf', 0, NULL, NULL),
(184, 81, 'journal_publication_format', 'journal_publication_format_1759900526_118b601b.pdf', '2025-10-08 13:15:26', 66396, 'application/pdf', 0, NULL, NULL),
(185, 81, 'notarized_copyright', 'notarized_copyright_1759900526_dd05cc99.pdf', '2025-10-08 13:15:26', 66396, 'application/pdf', 0, NULL, NULL),
(186, 81, 'receipt_payment', 'receipt_payment_1759900526_48db76f3.pdf', '2025-10-08 13:15:26', 66396, 'application/pdf', 0, NULL, NULL),
(187, 81, 'presentation', 'presentation_1759900526_3223a38c.pdf', '2025-10-08 13:15:26', 66396, 'application/pdf', 0, NULL, NULL),
(188, 81, 'notarized_coauthorship', 'notarized_coauthorship_1759900526_ae1d2d90.pdf', '2025-10-08 13:15:26', 86468, 'application/pdf', 0, NULL, NULL),
(189, 81, 'record_copyright', 'record_copyright_1759900526_ab3b6bf0.pdf', '2025-10-08 13:15:26', 66396, 'application/pdf', 0, NULL, NULL),
(190, 82, 'journal_publication_format', 'journal_publication_format_1759900739_598283a5.pdf', '2025-10-08 13:18:59', 66396, 'application/pdf', 0, NULL, NULL),
(191, 82, 'notarized_copyright', 'notarized_copyright_1759900739_019f8628.pdf', '2025-10-08 13:18:59', 86468, 'application/pdf', 0, NULL, NULL),
(192, 82, 'receipt_payment', 'receipt_payment_1759900739_76a4ec0a.pdf', '2025-10-08 13:18:59', 86468, 'application/pdf', 0, NULL, NULL),
(193, 82, 'presentation', 'presentation_1759900739_6f29eb05.pdf', '2025-10-08 13:18:59', 86468, 'application/pdf', 0, NULL, NULL),
(194, 82, 'notarized_coauthorship', 'notarized_coauthorship_1759900739_ddfe5f3a.pdf', '2025-10-08 13:18:59', 86468, 'application/pdf', 0, NULL, NULL),
(195, 82, 'record_copyright', 'record_copyright_1759900739_1abc4ecd.pdf', '2025-10-08 13:18:59', 86468, 'application/pdf', 0, NULL, NULL),
(196, 83, 'journal_publication_format', 'journal_publication_format_1759902012_85ddbdcc.pdf', '2025-10-08 13:40:12', 66396, 'application/pdf', 0, NULL, NULL),
(197, 83, 'notarized_copyright', 'notarized_copyright_1759902012_741c037d.pdf', '2025-10-08 13:40:12', 66396, 'application/pdf', 0, NULL, NULL),
(198, 83, 'receipt_payment', 'receipt_payment_1759902012_03068b7d.pdf', '2025-10-08 13:40:12', 66396, 'application/pdf', 0, NULL, NULL),
(199, 83, 'presentation', 'presentation_1759902012_f58c81ba.pdf', '2025-10-08 13:40:12', 66396, 'application/pdf', 0, NULL, NULL),
(200, 83, 'notarized_coauthorship', 'notarized_coauthorship_1759902012_1f873c42.pdf', '2025-10-08 13:40:12', 66396, 'application/pdf', 0, NULL, NULL),
(201, 83, 'record_copyright', 'record_copyright_1759902012_3102eabb.pdf', '2025-10-08 13:40:12', 66396, 'application/pdf', 0, NULL, NULL),
(202, 84, 'journal_publication_format', 'journal_publication_format_1759902031_3afc17c0.pdf', '2025-10-08 13:40:31', 86468, 'application/pdf', 0, NULL, NULL),
(203, 84, 'notarized_copyright', 'notarized_copyright_1759902031_422e765b.pdf', '2025-10-08 13:40:31', 66396, 'application/pdf', 0, NULL, NULL),
(204, 84, 'receipt_payment', 'receipt_payment_1759902031_8214d7d0.pdf', '2025-10-08 13:40:31', 66396, 'application/pdf', 0, NULL, NULL),
(205, 84, 'presentation', 'presentation_1759902031_0a5ee1eb.pdf', '2025-10-08 13:40:31', 66396, 'application/pdf', 0, NULL, NULL),
(206, 84, 'notarized_coauthorship', 'notarized_coauthorship_1759902031_9263017d.pdf', '2025-10-08 13:40:31', 66396, 'application/pdf', 0, NULL, NULL),
(207, 84, 'record_copyright', 'record_copyright_1759902031_086af607.pdf', '2025-10-08 13:40:31', 66396, 'application/pdf', 0, NULL, NULL),
(208, 85, 'journal_publication_format', 'journal_publication_format_1759902137_875588df.pdf', '2025-10-08 13:42:17', 86468, 'application/pdf', 0, NULL, NULL),
(209, 85, 'notarized_copyright', 'notarized_copyright_1759902137_f617ed49.pdf', '2025-10-08 13:42:17', 86468, 'application/pdf', 0, NULL, NULL),
(210, 85, 'receipt_payment', 'receipt_payment_1759902137_82554793.pdf', '2025-10-08 13:42:17', 86468, 'application/pdf', 0, NULL, NULL),
(211, 85, 'presentation', 'presentation_1759902137_53f9736e.pdf', '2025-10-08 13:42:17', 86468, 'application/pdf', 0, NULL, NULL),
(212, 85, 'notarized_coauthorship', 'notarized_coauthorship_1759902137_0778b8bb.pdf', '2025-10-08 13:42:17', 86468, 'application/pdf', 0, NULL, NULL),
(213, 85, 'record_copyright', 'record_copyright_1759902137_f5f80621.pdf', '2025-10-08 13:42:17', 86468, 'application/pdf', 0, NULL, NULL),
(214, 86, 'journal_publication_format', 'journal_publication_format_1759902185_56b499e1.pdf', '2025-10-08 13:43:05', 66396, 'application/pdf', 0, NULL, NULL),
(215, 86, 'notarized_copyright', 'notarized_copyright_1759902185_04b41aaf.pdf', '2025-10-08 13:43:05', 86468, 'application/pdf', 0, NULL, NULL),
(216, 86, 'receipt_payment', 'receipt_payment_1759902185_3cde2cbc.pdf', '2025-10-08 13:43:05', 86468, 'application/pdf', 0, NULL, NULL),
(217, 86, 'presentation', 'presentation_1759902185_95fab0f0.pdf', '2025-10-08 13:43:05', 86468, 'application/pdf', 0, NULL, NULL),
(218, 86, 'notarized_coauthorship', 'notarized_coauthorship_1759902185_3749f2ed.pdf', '2025-10-08 13:43:05', 86468, 'application/pdf', 0, NULL, NULL),
(219, 86, 'record_copyright', 'record_copyright_1759902185_9c896d23.pdf', '2025-10-08 13:43:05', 86468, 'application/pdf', 0, NULL, NULL),
(220, 87, 'journal_publication_format', 'journal_publication_format_1759902279_d11067c1.pdf', '2025-10-08 13:44:39', 86468, 'application/pdf', 0, NULL, NULL),
(221, 87, 'notarized_copyright', 'notarized_copyright_1759902279_b58591e1.pdf', '2025-10-08 13:44:39', 66396, 'application/pdf', 0, NULL, NULL),
(222, 87, 'receipt_payment', 'receipt_payment_1759902279_31a095c9.pdf', '2025-10-08 13:44:39', 86468, 'application/pdf', 0, NULL, NULL),
(223, 87, 'presentation', 'presentation_1759902279_7ea1311e.pdf', '2025-10-08 13:44:39', 66396, 'application/pdf', 0, NULL, NULL),
(224, 87, 'notarized_coauthorship', 'notarized_coauthorship_1759902279_dbd21055.pdf', '2025-10-08 13:44:39', 86468, 'application/pdf', 0, NULL, NULL),
(225, 87, 'record_copyright', 'record_copyright_1759902279_8532b5d9.pdf', '2025-10-08 13:44:39', 66396, 'application/pdf', 0, NULL, NULL),
(226, 88, 'journal_publication_format', 'journal_publication_format_1759907402_09b21907.pdf', '2025-10-08 15:10:02', 66396, 'application/pdf', 0, NULL, NULL),
(227, 88, 'notarized_copyright', 'notarized_copyright_1759907402_538b5126.pdf', '2025-10-08 15:10:02', 66396, 'application/pdf', 0, NULL, NULL),
(228, 88, 'receipt_payment', 'receipt_payment_1759907402_65de1c0d.pdf', '2025-10-08 15:10:02', 66396, 'application/pdf', 0, NULL, NULL),
(229, 88, 'full_manuscript', 'full_manuscript_1759907402_b39161dd.pdf', '2025-10-08 15:10:02', 86468, 'application/pdf', 0, NULL, NULL),
(230, 88, 'notarized_coauthorship', 'notarized_coauthorship_1759907402_9589839d.pdf', '2025-10-08 15:10:02', 66396, 'application/pdf', 0, NULL, NULL),
(231, 88, 'approval_sheet', 'approval_sheet_1759909466_43f170f1.pdf', '2025-10-08 15:44:26', 66396, 'application/pdf', 0, NULL, NULL),
(232, 88, 'record_copyright', 'record_copyright_1759907402_51c49a56.pdf', '2025-10-08 15:10:02', 66396, 'application/pdf', 0, NULL, NULL),
(233, 89, 'journal_publication_format', 'journal_publication_format_1759908066_f6fdad9f.pdf', '2025-10-08 15:21:06', 66396, 'application/pdf', 0, NULL, NULL),
(234, 89, 'notarized_copyright', 'notarized_copyright_1759908066_3fcac808.pdf', '2025-10-08 15:21:06', 66396, 'application/pdf', 0, NULL, NULL),
(235, 89, 'receipt_payment', 'receipt_payment_1759908066_38087db4.pdf', '2025-10-08 15:21:06', 66396, 'application/pdf', 0, NULL, NULL),
(236, 89, 'full_manuscript', 'full_manuscript_1759908066_eae4cd07.pdf', '2025-10-08 15:21:06', 66396, 'application/pdf', 0, NULL, NULL),
(237, 89, 'notarized_coauthorship', 'notarized_coauthorship_1759908066_5ebac6c2.pdf', '2025-10-08 15:21:07', 66396, 'application/pdf', 0, NULL, NULL),
(238, 89, 'approval_sheet', 'approval_sheet_1759908066_5039e4b7.pdf', '2025-10-08 15:21:07', 66396, 'application/pdf', 0, NULL, NULL),
(239, 89, 'record_copyright', 'record_copyright_1759908066_97504f20.pdf', '2025-10-08 15:21:07', 66396, 'application/pdf', 0, NULL, NULL),
(240, 90, 'journal_publication_format', 'journal_publication_format_1759908428_789d9f9f.pdf', '2025-10-08 15:27:08', 66396, 'application/pdf', 0, NULL, NULL),
(241, 90, 'notarized_copyright', 'notarized_copyright_1759908428_2cadc374.pdf', '2025-10-08 15:27:08', 66396, 'application/pdf', 0, NULL, NULL),
(242, 90, 'receipt_payment', 'receipt_payment_1759908428_75f88104.pdf', '2025-10-08 15:27:08', 66396, 'application/pdf', 0, NULL, NULL),
(243, 90, 'full_manuscript', 'full_manuscript_1759908428_5b165b40.pdf', '2025-10-08 15:27:08', 66396, 'application/pdf', 0, NULL, NULL),
(244, 90, 'notarized_coauthorship', 'notarized_coauthorship_1759908428_f1f4b2ee.pdf', '2025-10-08 15:27:08', 66396, 'application/pdf', 0, NULL, NULL),
(245, 90, 'approval_sheet', 'approval_sheet_1759909478_c6c62336.pdf', '2025-10-08 15:44:38', 66396, 'application/pdf', 0, NULL, NULL),
(246, 90, 'record_copyright', 'record_copyright_1759908428_e8898b1b.pdf', '2025-10-08 15:27:08', 66396, 'application/pdf', 0, NULL, NULL),
(247, 91, 'journal_publication_format', 'journal_publication_format_1759909389_ac798c3d.pdf', '2025-10-08 15:43:09', 66396, 'application/pdf', 0, NULL, NULL),
(248, 91, 'notarized_copyright', 'notarized_copyright_1759909389_14f2a86e.pdf', '2025-10-08 15:43:09', 66396, 'application/pdf', 0, NULL, NULL),
(249, 91, 'receipt_payment', 'receipt_payment_1759909389_3c883e8c.pdf', '2025-10-08 15:43:09', 66396, 'application/pdf', 0, NULL, NULL),
(250, 91, 'presentation', 'presentation_1759909389_15a198d4.pdf', '2025-10-08 15:43:09', 66396, 'application/pdf', 0, NULL, NULL),
(251, 91, 'notarized_coauthorship', 'notarized_coauthorship_1759909389_f515db14.pdf', '2025-10-08 15:43:09', 66396, 'application/pdf', 0, NULL, NULL),
(252, 91, 'record_copyright', 'record_copyright_1759909389_54295efb.pdf', '2025-10-08 15:43:09', 66396, 'application/pdf', 0, NULL, NULL),
(253, 92, 'journal_publication_format', 'journal_publication_format_1759909709_e2fbc272.pdf', '2025-10-08 15:48:29', 86468, 'application/pdf', 0, NULL, NULL),
(254, 92, 'notarized_copyright', 'notarized_copyright_1759909709_33559646.pdf', '2025-10-08 15:48:29', 86468, 'application/pdf', 0, NULL, NULL),
(255, 92, 'receipt_payment', 'receipt_payment_1759909709_c1c7ad36.pdf', '2025-10-08 15:48:29', 66396, 'application/pdf', 0, NULL, NULL),
(256, 92, 'full_manuscript', 'full_manuscript_1759909709_a486e49c.pdf', '2025-10-08 15:48:29', 66396, 'application/pdf', 0, NULL, NULL),
(257, 92, 'notarized_coauthorship', 'notarized_coauthorship_1759909709_45825bff.pdf', '2025-10-08 15:48:29', 66396, 'application/pdf', 0, NULL, NULL),
(258, 92, 'approval_sheet', 'approval_sheet_1759909860_efd084b2.pdf', '2025-10-08 15:51:00', 86468, 'application/pdf', 0, NULL, NULL),
(259, 92, 'record_copyright', 'record_copyright_1759909709_43cf692d.pdf', '2025-10-08 15:48:29', 86468, 'application/pdf', 0, NULL, NULL),
(260, 93, 'journal_publication_format', 'journal_publication_format_1759909880_73b53f3f.pdf', '2025-10-08 15:51:20', 66396, 'application/pdf', 0, NULL, NULL),
(261, 93, 'notarized_copyright', 'notarized_copyright_1759909880_91c1c536.pdf', '2025-10-08 15:51:20', 66396, 'application/pdf', 0, NULL, NULL),
(262, 93, 'receipt_payment', 'receipt_payment_1759909880_66e16c6a.pdf', '2025-10-08 15:51:20', 86468, 'application/pdf', 0, NULL, NULL),
(263, 93, 'full_manuscript', 'full_manuscript_1759909880_b3b6e6a7.pdf', '2025-10-08 15:51:20', 86468, 'application/pdf', 0, NULL, NULL),
(264, 93, 'notarized_coauthorship', 'notarized_coauthorship_1759909880_43e3411d.pdf', '2025-10-08 15:51:20', 86468, 'application/pdf', 0, NULL, NULL),
(265, 93, 'approval_sheet', 'approval_sheet_1759909880_83bb9162.pdf', '2025-10-08 15:51:20', 66396, 'application/pdf', 0, NULL, NULL),
(266, 93, 'record_copyright', 'record_copyright_1759909880_4d214546.pdf', '2025-10-08 15:51:20', 66396, 'application/pdf', 0, NULL, NULL),
(267, 94, 'journal_publication_format', 'journal_publication_format_1759910146_9ccd2df5.pdf', '2025-10-08 15:55:46', 66396, 'application/pdf', 0, NULL, NULL),
(268, 94, 'notarized_copyright', 'notarized_copyright_1759910146_629eccac.pdf', '2025-10-08 15:55:46', 66396, 'application/pdf', 0, NULL, NULL),
(269, 94, 'receipt_payment', 'receipt_payment_1759910146_51e52cbc.pdf', '2025-10-08 15:55:46', 66396, 'application/pdf', 0, NULL, NULL),
(270, 94, 'full_manuscript', 'full_manuscript_1759910146_850a31e2.pdf', '2025-10-08 15:55:46', 66396, 'application/pdf', 0, NULL, NULL),
(271, 94, 'notarized_coauthorship', 'notarized_coauthorship_1759910146_81ef4df4.pdf', '2025-10-08 15:55:46', 66396, 'application/pdf', 0, NULL, NULL),
(272, 94, 'approval_sheet', 'approval_sheet_1759910456_b1de4772.pdf', '2025-10-08 16:00:56', 66396, 'application/pdf', 0, NULL, NULL),
(273, 94, 'record_copyright', 'record_copyright_1759910146_d3a189e2.pdf', '2025-10-08 15:55:46', 66396, 'application/pdf', 0, NULL, NULL),
(274, 95, 'journal_publication_format', 'journal_publication_format_1759928101_c44d06dc.pdf', '2025-10-08 20:55:01', 1649, 'application/pdf', 0, NULL, NULL),
(275, 95, 'notarized_copyright', 'notarized_copyright_1759928101_22d9c125.pdf', '2025-10-08 20:55:01', 1649, 'application/pdf', 0, NULL, NULL),
(276, 95, 'receipt_payment', 'receipt_payment_1759928101_f1d2d1dd.pdf', '2025-10-08 20:55:01', 1649, 'application/pdf', 0, NULL, NULL),
(277, 95, 'presentation', 'presentation_1759928101_fc62447d.pdf', '2025-10-08 20:55:01', 1649, 'application/pdf', 0, NULL, NULL),
(278, 95, 'notarized_coauthorship', 'notarized_coauthorship_1759928101_d79bf687.pdf', '2025-10-08 20:55:01', 1649, 'application/pdf', 0, NULL, NULL),
(279, 95, 'record_copyright', 'record_copyright_1759928101_3a19d058.pdf', '2025-10-08 20:55:01', 1649, 'application/pdf', 0, NULL, NULL),
(280, 96, 'journal_publication_format', 'journal_publication_format_1759934177_ac647bbb.pdf', '2025-10-08 22:36:17', 1653, 'application/pdf', 0, NULL, NULL),
(281, 96, 'notarized_copyright', 'notarized_copyright_1759934177_95b1234c.pdf', '2025-10-08 22:36:17', 1653, 'application/pdf', 0, NULL, NULL),
(282, 96, 'receipt_payment', 'receipt_payment_1759934177_57066133.pdf', '2025-10-08 22:36:17', 1653, 'application/pdf', 0, NULL, NULL),
(283, 96, 'presentation', 'presentation_1759934177_0e2414c0.pdf', '2025-10-08 22:36:17', 1653, 'application/pdf', 0, NULL, NULL),
(284, 96, 'notarized_coauthorship', 'notarized_coauthorship_1759934177_8080ab7c.pdf', '2025-10-08 22:36:17', 1653, 'application/pdf', 0, NULL, NULL),
(285, 96, 'record_copyright', 'record_copyright_1759934177_3c481c68.pdf', '2025-10-08 22:36:17', 1653, 'application/pdf', 0, NULL, NULL);

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
(67, 'pending', 'Incorrect Document/Upload', 'hey1', 'approval_sheet|full_manuscript', '2025-10-07 01:41:40', '2025-10-07 01:41:40', NULL, 0),
(69, 'pending', 'Incorrect Document/Upload', 'testtt', '', '2025-10-08 01:06:03', '2025-10-08 01:01:25', NULL, 0),
(69, 'approved', 'Documents don’t match', 'test', 'journal_publication_format|notarized_coauthorship|notarized_copyright|presentation|receipt_payment|record_copyright', '2025-10-08 01:09:08', '2025-10-08 01:07:45', NULL, 0),
(74, 'pending', 'Incorrect Document/Upload', 'test', '', '2025-10-08 12:49:38', '2025-10-08 12:42:02', NULL, 0),
(75, 'pending', 'Incorrect Document/Upload', 'test', '', '2025-10-08 13:05:25', '2025-10-08 13:05:17', NULL, 0),
(76, 'pending', 'Incorrect Document/Upload', 'test', 'notarized_coauthorship', '2025-10-08 13:09:28', '2025-10-08 13:09:28', NULL, 0),
(78, 'approved', NULL, 'testttt', NULL, '2025-10-08 13:13:55', '2025-10-08 13:13:55', NULL, 0),
(80, 'pending', 'Error in Document/Upload', 'tes', '', '2025-10-08 13:14:49', '2025-10-08 13:14:36', NULL, 0),
(81, 'pending', 'Incorrect Document/Upload', 'test1', 'journal_publication_format|notarized_coauthorship|notarized_copyright|presentation|receipt_payment|record_copyright', '2025-10-08 13:15:35', '2025-10-08 13:15:35', NULL, 0),
(81, 'approved', NULL, 'test2', NULL, '2025-10-08 13:15:55', '2025-10-08 13:15:55', NULL, 0),
(83, 'approved', NULL, 'test', NULL, '2025-10-08 13:41:06', '2025-10-08 13:41:06', NULL, 0),
(85, 'pending', 'Error in Document/Upload', 'test', '', '2025-10-08 13:42:17', '2025-10-08 13:42:04', NULL, 0),
(86, 'pending', 'Incorrect Document/Upload', 'tes1', 'journal_publication_format', '2025-10-08 13:43:16', '2025-10-08 13:43:16', NULL, 0),
(86, 'approved', 'Missing Document', 'mark1', 'journal_publication_format', '2025-10-08 13:44:04', '2025-10-08 13:43:47', NULL, 0),
(87, 'pending', 'Error in Document/Upload', 'test1', 'journal_publication_format', '2025-10-08 13:44:49', '2025-10-08 13:44:49', NULL, 0),
(87, 'approved', 'Error in Document', 'mark1', 'journal_publication_format', '2025-10-08 13:47:34', '2025-10-08 13:44:58', NULL, 0),
(88, 'pending', 'Incorrect Document/Upload', 'test', '', '2025-10-08 15:44:26', '2025-10-08 15:13:26', NULL, 0),
(89, 'approved', NULL, 'ttt', 'full_manuscript', '2025-10-08 16:13:04', '2025-10-08 16:12:19', NULL, 0),
(90, 'pending', 'Error in Document/Upload', 'test', '', '2025-10-08 15:44:38', '2025-10-08 15:27:23', NULL, 0),
(91, 'pending', 'Error in Document/Upload', 'test', '', '2025-10-08 15:43:09', '2025-10-08 15:42:53', NULL, 0),
(92, 'pending', 'Incorrect Document/Upload', 'test', '', '2025-10-08 15:51:00', '2025-10-08 15:50:52', NULL, 0),
(94, 'pending', 'Incorrect Document/Upload', 'test', 'approval_sheet', '2025-10-08 16:08:44', '2025-10-08 15:55:58', NULL, 0);

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
(1, 58, 1, 'test', '2025-10-06 21:09:12'),
(2, 86, 2, 'test', '2025-10-08 13:43:34');

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
(1, 58, 4, '2025-10-07 01:06:44'),
(5, 86, 4, '2025-10-08 13:44:16');

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
(5, 'inocentesraebv@gmail.com', '$2y$12$MvO.41oeGZKRmqJc6lM8Ueppkcugyq4/0lvZKR5m3ETVlNRPlvx2a', 'student', 'pending', '9481d21b6c60ec81', '2025-10-07 22:30:53', NULL, '2025-10-06 22:30:53'),
(6, 'thinkingwan00@gmail.com', '$2y$12$0y66PWURrNDe6JcjfLEKq./G20X66tAdMqJP3lC6LriVPAPfN4eAS', 'employee', 'active', 'd8b22737708b65f0', '2025-10-08 22:19:24', NULL, '2025-10-07 22:19:24'),
(7, 'nadaone@gmail.com', '$2y$12$WPx18A4KGzbFRi4CtTJOMuuFtRE.ULuCNxYgklx7VA4S0TUBPL8hS', 'student', 'pending', '5517b8c4d76fa2bf', '2025-10-08 22:41:29', NULL, '2025-10-07 22:41:29'),
(14, 'hellohihihi1234567890@gmail.com', '$2y$12$6.K2IbpZUjnJzyI3wzVihOb7mDlXiXwZ6tMRECzaXVmdZH9UjL54i', 'student', 'pending', '1d501032ef650c70', '2025-10-08 23:58:05', NULL, '2025-10-07 23:58:05');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT for table `admin_profiles`
--
ALTER TABLE `admin_profiles`
  MODIFY `profile_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `advisers`
--
ALTER TABLE `advisers`
  MODIFY `adviser_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `employee_profiles`
--
ALTER TABLE `employee_profiles`
  MODIFY `profile_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `student_profiles`
--
ALTER TABLE `student_profiles`
  MODIFY `profile_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `submission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT for table `submission_authors`
--
ALTER TABLE `submission_authors`
  MODIFY `author_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `submission_documents`
--
ALTER TABLE `submission_documents`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=286;

--
-- AUTO_INCREMENT for table `submission_notes`
--
ALTER TABLE `submission_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `submission_notes_admin_views`
--
ALTER TABLE `submission_notes_admin_views`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

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
