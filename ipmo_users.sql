-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 29, 2025 at 02:49 PM
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
(1, 37, 'SRID-2025-20250927-4', 19, 'approval_sheet', 'User #19 re-uploaded approval sheet (SRID-2025-20250927-4)', '2025-09-28 14:15:40', 1),
(2, 37, 'SRID-2025-20250927-4', 19, 'approval_sheet', 'User #19 re-uploaded approval sheet (SRID-2025-20250927-4)', '2025-09-28 14:16:39', 1),
(3, 37, 'SRID-2025-20250927-4', 19, 'approval_sheet', 'User #19 re-uploaded approval sheet (SRID-2025-20250927-4)', '2025-09-28 14:52:29', 1),
(4, 37, 'SRID-2025-20250927-4', 19, 'approval_sheet', 'User #19 re-uploaded approval sheet (SRID-2025-20250927-4)', '2025-09-28 14:56:13', 1),
(5, 37, 'SRID-2025-20250927-4', 19, 'approval_sheet', 'User #19 re-uploaded approval sheet (SRID-2025-20250927-4)', '2025-09-28 14:56:14', 1),
(6, 37, 'SRID-2025-20250927-4', 19, 'approval_sheet', 'User #19 re-uploaded approval sheet (SRID-2025-20250927-4)', '2025-09-28 15:08:04', 1),
(7, 29, 'SRID-2025-20250924-2', 19, 'approval_sheet', 'User #19 re-uploaded approval sheet (SRID-2025-20250924-2)', '2025-09-28 15:09:19', 1),
(8, 37, 'SRID-2025-20250927-4', 19, 'approval_sheet', 'User #19 re-uploaded approval sheet (SRID-2025-20250927-4)', '2025-09-28 15:13:42', 1),
(9, 29, 'SRID-2025-20250924-2', 19, 'approval_sheet', 'User #19 re-uploaded approval sheet (SRID-2025-20250924-2)', '2025-09-28 15:14:08', 1),
(10, 37, 'SRID-2025-20250927-4', 19, 'approval_sheet', 'User #19 re-uploaded approval sheet (SRID-2025-20250927-4)', '2025-09-28 15:14:32', 1),
(11, 29, 'SRID-2025-20250924-2', 19, 'approval_sheet', 'User #19 re-uploaded approval sheet (SRID-2025-20250924-2)', '2025-09-28 15:14:49', 1),
(12, 37, 'SRID-2025-20250927-4', 19, 'approval_sheet', 'User #19 re-uploaded approval sheet (SRID-2025-20250927-4)', '2025-09-28 15:15:34', 1),
(13, 37, 'SRID-2025-20250927-4', 19, 'approval_sheet', 'User #19 re-uploaded approval sheet (SRID-2025-20250927-4)', '2025-09-28 15:16:50', 1),
(14, 37, 'SRID-2025-20250927-4', 19, 'approval_sheet', 'User #19 re-uploaded approval sheet (SRID-2025-20250927-4)', '2025-09-28 15:17:42', 1),
(15, 37, 'SRID-2025-20250927-4', 19, 'approval_sheet', 'User #19 re-uploaded approval sheet (SRID-2025-20250927-4)', '2025-09-28 15:18:47', 1),
(16, 37, 'SRID-2025-20250927-4', 19, 'approval_sheet', 'User #19 re-uploaded approval sheet (SRID-2025-20250927-4)', '2025-09-28 15:19:50', 1),
(17, 37, 'SRID-2025-20250927-4', 19, 'approval_sheet', 'User #19 re-uploaded approval sheet (SRID-2025-20250927-4)', '2025-09-28 15:23:18', 1),
(18, 29, 'SRID-2025-20250924-2', 19, 'approval_sheet', 'User #19 re-uploaded approval sheet (SRID-2025-20250924-2)', '2025-09-28 15:24:01', 1),
(19, 37, 'SRID-2025-20250927-4', 19, 'approval_sheet', 'User #19 re-uploaded approval sheet (SRID-2025-20250927-4)', '2025-09-28 15:27:58', 1),
(20, 37, 'SRID-2025-20250927-4', 19, 'full_manuscript', 'User #19 re-uploaded full manuscript (SRID-2025-20250927-4)', '2025-09-28 15:28:54', 1),
(21, 37, 'SRID-2025-20250927-4', 19, 'approval_sheet', 'User #19 re-uploaded approval sheet (SRID-2025-20250927-4)', '2025-09-28 15:32:51', 1),
(22, 37, 'SRID-2025-20250927-4', 19, 'approval_sheet', 'User #19 re-uploaded approval sheet (SRID-2025-20250927-4)', '2025-09-28 15:36:13', 1),
(23, 29, 'SRID-2025-20250924-2', 19, 'approval_sheet', 'User #19 re-uploaded approval sheet (SRID-2025-20250924-2)', '2025-09-28 15:39:48', 1),
(24, 37, 'SRID-2025-20250927-4', 19, 'approval_sheet', 'User #19 re-uploaded approval sheet (SRID-2025-20250927-4)', '2025-09-28 15:49:28', 1),
(25, 37, 'SRID-2025-20250927-4', 19, 'approval_sheet', 'User #19 re-uploaded approval sheet (SRID-2025-20250927-4)', '2025-09-28 15:51:47', 1),
(26, 37, 'SRID-2025-20250927-4', 19, 'approval_sheet', 'User #19 re-uploaded approval sheet (SRID-2025-20250927-4)', '2025-09-28 15:55:48', 1),
(27, 37, 'SRID-2025-20250927-4', 19, 'approval_sheet', 'User #19 re-uploaded approval sheet (SRID-2025-20250927-4)', '2025-09-28 15:56:54', 1),
(28, 37, 'SRID-2025-20250927-4', 19, 'approval_sheet', 'User #19 re-uploaded approval sheet (SRID-2025-20250927-4)', '2025-09-28 16:01:19', 1),
(29, 37, 'SRID-2025-20250927-4', 19, 'approval_sheet', 'User #19 re-uploaded approval sheet (SRID-2025-20250927-4)', '2025-09-28 16:01:52', 1),
(30, 37, 'SRID-2025-20250927-4', 19, 'approval_sheet', 'User #19 re-uploaded approval sheet (SRID-2025-20250927-4)', '2025-09-28 16:06:52', 1),
(31, 37, 'SRID-2025-20250927-4', 19, 'approval_sheet', 'User #19 re-uploaded approval sheet (SRID-2025-20250927-4)', '2025-09-28 16:18:28', 1),
(32, 37, 'SRID-2025-20250927-4', 19, 'approval_sheet', 'User #19 re-uploaded approval sheet (SRID-2025-20250927-4)', '2025-09-28 16:32:44', 1),
(33, 27, 'SRID-2025-20250924-3', 19, 'approval_sheet', 'User #19 re-uploaded approval sheet (SRID-2025-20250924-3)', '2025-09-28 16:39:16', 1),
(34, 27, 'SRID-2025-20250924-3', 19, 'approval_sheet', 'User #19 re-uploaded approval sheet (SRID-2025-20250924-3)', '2025-09-28 16:44:17', 1),
(35, 37, 'SRID-2025-20250927-4', 19, 'approval_sheet', 'User #19 re-uploaded approval sheet (SRID-2025-20250927-4)', '2025-09-29 11:19:42', 1),
(36, 37, 'SRID-2025-20250927-4', 19, 'approval_sheet', 'User #19 re-uploaded approval sheet (SRID-2025-20250927-4)', '2025-09-29 11:21:38', 1),
(37, 37, 'SRID-2025-20250927-4', 19, 'approval_sheet', 'User #19 re-uploaded approval sheet (SRID-2025-20250927-4)', '2025-09-29 11:25:12', 1),
(38, 37, 'SRID-2025-20250927-4', 19, 'full_manuscript', 'User #19 re-uploaded full manuscript (SRID-2025-20250927-4)', '2025-09-29 11:25:12', 1),
(39, 37, 'SRID-2025-20250927-4', 19, 'approval_sheet', 'User #19 re-uploaded approval sheet (SRID-2025-20250927-4)', '2025-09-29 11:33:44', 1);

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
(11, 'Hella', '', 'Deola', NULL, NULL, NULL, '2025-09-24 15:47:37'),
(12, 'Avada', '', 'kadavra', NULL, NULL, NULL, '2025-09-24 17:43:05'),
(13, 'test', '', '', NULL, NULL, NULL, '2025-09-24 18:57:04'),
(14, 'ad', '', '', NULL, NULL, NULL, '2025-09-24 21:32:32'),
(15, 'a', '', '', NULL, NULL, NULL, '2025-09-27 21:05:13');

-- --------------------------------------------------------

--
-- Table structure for table `student_profiles`
--

CREATE TABLE `student_profiles` (
  `profile_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) NOT NULL,
  `suffix` varchar(5) DEFAULT NULL,
  `home_address` varchar(255) NOT NULL,
  `mobile_number` varchar(15) NOT NULL,
  `campus` varchar(255) NOT NULL,
  `college` varchar(255) NOT NULL,
  `department` varchar(255) NOT NULL,
  `program` varchar(255) NOT NULL,
  `last_updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_profiles`
--

INSERT INTO `student_profiles` (`profile_id`, `user_id`, `last_name`, `first_name`, `middle_name`, `suffix`, `home_address`, `mobile_number`, `campus`, `college`, `department`, `program`, `last_updated_at`) VALUES
(8, 19, 'Dela Cruz', 'Juan', 'Malinaw', '', '123 Sampaguita St., Manila City', '09171234567', 'PUP Main', 'College of Engineering', 'Computer Engineering', 'BS Computer Engineering', NULL),
(9, 20, 'Sinyales', 'Sandra', 'Halima', '', '23 Sampaguita St., Manila City', '09171234569', 'PUP Main', 'College of Engineering', 'Computer Engineering', 'BS Computer Engineering', NULL),
(10, 21, 'Xander', 'Limo', 'Hixa', '', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234566', 'PUP Main', 'College of Engineering', 'Computer Engineering', 'BS Computer Engineering', NULL),
(11, 22, 'Inocentes', 'Raebv Lielmo', 'A', '', '4334A V. Francisco St. Sta. Mesa, Manila', '09156574831', 'a', 'a', 'a', 'a', NULL);

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
(27, 'SRID-2025-20250924-3', 19, 'Juan', 'Malinaw', 'Dela Cruz', '2023-12335-MN-0', '123 Sampaguita St., Manila City', '09171234567', 'hellohihihi1234567890@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Masters', 'N/A', 'Master of Science in Information Technology (MSIT)', '(m) Pictorial illustrations and advertisements', 'eqweq', '2025-09-24', 1, 'approved', '2025-09-29 00:45:15', 'test', 1, 1, 'copyright', '2025-09-24 17:33:20', '2025-09-29 00:45:15', 18, '2025-09-29 00:45:15', 11),
(29, 'SRID-2025-20250924-2', 19, 'Juan', 'Malinaw', 'Dela Cruz', '2023-12335-MN-0', '123 Sampaguita St., Manila City', '09171234567', 'hellohihihi1234567890@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Science (CS)', 'Bachelor of Science in Chemistry (BSCHEM)', '(n) Computer Programs', 'A Mathematical Model for Predicting the Diffusion of  Information in Social Networks', '2025-09-16', 1, 'approved', '2025-09-29 19:31:08', 'testing', 1, 1, 'copyright', '2025-09-24 17:45:57', '2025-09-29 19:31:08', 18, '2025-09-29 19:31:08', 12),
(31, 'SRID-2025-20250924-1', 19, 'Juan', 'Malinaw', 'Dela Cruz', '2023-12335-MN-0', '123 Sampaguita St., Manila City', '09171234567', 'hellohihihi1234567890@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Masters', 'N/A', 'Master of Science in Construction Management (MSCM)', '(e) Dramatic or dramatic-musical compositions; choreographic works', 'A Mathematical Model for Predicting the Diffusion of  Information in Social Networks', '2025-09-22', 1, 'approved', '2025-09-29 19:30:41', 'Error in Document', 1, 1, 'copyright', '2025-09-24 17:55:30', '2025-09-29 19:30:41', 18, '2025-09-27 22:34:43', 11),
(32, 'SRID-2025-20250924-4', 19, 'Juan', 'Malinaw', 'Dela Cruz', '2023-12335-MN-0', '123 Sampaguita St., Manila City', '09171234567', 'hellohihihi1234567890@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Open University', 'N/A', 'Doctor in Public Administration (DPA)', '(b) Periodicals and newspaper', 'testt', '2025-09-24', 1, 'approved', '2025-09-27 23:38:41', 'Missing Document', 1, 1, 'copyright', '2025-09-24 18:57:04', '2025-09-27 23:38:41', 18, '2025-09-27 22:30:01', 13),
(33, 'SRID-2025-20250924-5', 19, 'Juan', 'Malinaw', 'Dela Cruz', '2023-12335-MN-0', '123 Sampaguita St., Manila City', '09171234567', 'hellohihihi1234567890@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Masters', 'N/A', 'Master of Science in Construction Management (MSCM)', '(q) Broadcast recordings', 'a', '2025-09-24', 1, 'completed', '2025-09-28 01:49:09', 'LESTGO', 1, 1, 'copyright', '2025-09-24 21:32:32', '2025-09-28 01:49:09', 18, '2025-09-27 22:14:06', 14),
(34, 'SRID-2025-20250927-1', 19, 'Juan', 'Malinaw', 'Dela Cruz', '2023-12335-MN-0', '123 Sampaguita St., Manila City', '09171234567', 'hellohihihi1234567890@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Masters', 'N/A', 'Master in Business Administration (MBA)', '(b) Periodicals and newspaper', 'a', '2025-09-27', 1, 'completed', '2025-09-28 01:42:01', 'test', 1, 1, 'copyright', '2025-09-27 21:05:13', '2025-09-28 01:42:01', 22, '2025-09-27 22:01:13', 15),
(35, 'SRID-2025-20250927-2', 19, 'Juan', 'Malinaw', 'Dela Cruz', '2023-12335-MN-0', '123 Sampaguita St., Manila City', '09171234567', 'hellohihihi1234567890@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Masters', 'N/A', 'Master of Science in Mathematics (MSM)', '(p) Sound recordings', 'TEST', '2025-09-27', 1, 'completed', '2025-09-28 00:37:01', 'Error in Document', 1, 1, 'copyright', '2025-09-27 21:17:27', '2025-09-28 00:37:01', 22, '2025-09-27 22:00:19', 15),
(36, 'SRID-2025-20250927-3', 19, 'Juan', 'Malinaw', 'Dela Cruz', '2023-12335-MN-0', '123 Sampaguita St., Manila City', '09171234567', 'hellohihihi1234567890@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Masters', 'N/A', 'Master in Business Administration (MBA)', '(c) Lectures, sermons, addresses, dissertations for oral delivery', 'HEHE', '2025-09-27', 1, 'completed', '2025-09-28 00:44:48', 'for evaluation', 1, 1, 'copyright', '2025-09-27 23:48:30', '2025-09-28 00:44:48', 18, '2025-09-28 00:35:43', 15),
(37, 'SRID-2025-20250927-4', 19, 'Juan', 'Malinaw', 'Dela Cruz', '2023-12335-MN-0', '123 Sampaguita St., Manila City', '09171234567', 'hellohihihi1234567890@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Masters', 'N/A', 'Master in Business Administration (MBA)', '(a) Books, Pamphlets, articles and other writings', 'TESTING', '2025-09-27', 1, 'pending_review', '2025-09-29 19:33:36', 'For Evaluation', 1, 1, 'copyright', '2025-09-27 23:54:02', '2025-09-29 19:33:44', NULL, NULL, 15),
(38, 'SRID-2025-20250927-5', 19, 'Juan', 'Malinaw', 'Dela Cruz', '2023-12335-MN-0', '123 Sampaguita St., Manila City', '09171234567', 'hellohihihi1234567890@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Masters', 'N/A', 'Master of Science in Mathematics (MSM)', '(o) Other literary, scholarly, scientific and artistic works', 'a', '2025-09-26', 1, 'completed', '2025-09-28 02:27:57', 'test', 1, 1, 'copyright', '2025-09-28 02:11:50', '2025-09-28 02:27:57', 18, '2025-09-28 02:17:29', 15);

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
(35, 27, 'Hella', '', 'Deola', NULL, NULL, NULL, NULL, 'Adviser', 1, '2025-09-24 17:33:20', 11),
(36, 27, 'eqwe', 'eqweq', 'Garcia', '2023-12335-MN-0', '09171234567', '123 Sampaguita St., Manila City', 'juandelacruz@iskolarngbayan.pup.edu.ph', 'Author', 0, '2025-09-24 17:33:20', NULL),
(39, 29, 'Avada', '', 'kadavra', NULL, NULL, NULL, NULL, 'Adviser', 1, '2025-09-24 17:45:57', 12),
(40, 29, 'Juan', 'Malinaw', 'Dela', '2023-12335-MN-0', '09171234567', '123 Sampaguita St., Manila City', 'juandelacruz@iskolarngbayan.pup.edu.ph', 'Author', 0, '2025-09-24 17:45:57', NULL),
(43, 31, 'Hella', '', 'Deola', NULL, NULL, NULL, NULL, 'Adviser', 1, '2025-09-24 17:55:30', 11),
(44, 31, 'Juan', 'Malinaw', 'Dela Cruz', '2023-12335-MN-0', '09171234567', '123 Sampaguita St., Manila City', 'juandelacruz@iskolarngbayan.pup.edu.ph', 'Author', 0, '2025-09-24 17:55:30', NULL),
(45, 33, 'ad', '', '', NULL, NULL, NULL, NULL, 'Adviser', 1, '2025-09-24 21:32:32', 14),
(46, 33, 'a', 'a', 'a', '2020-09121-MN-0', '09156574831', 'a', 'firstnamelastname@iskolarngbayan.pup.edu.ph', 'Author', 0, '2025-09-24 21:32:32', NULL),
(47, 34, 'Raebv Lielmo', '', 'Inocentes', '2020-09131-MN-0', '09156574831', '4334A V. Francisco St. Sta. Mesa, Manila', 'inocentesraebv@iskolarngbayan.pup.edu.ph', 'Author', 0, '2025-09-27 21:05:13', NULL),
(48, 35, 'a', '', '', NULL, NULL, NULL, NULL, 'Adviser', 1, '2025-09-27 21:17:27', 15),
(49, 36, 'a', '', '', NULL, NULL, NULL, NULL, 'Adviser', 1, '2025-09-27 23:48:30', 15),
(50, 37, 'a', '', '', NULL, NULL, NULL, NULL, 'Adviser', 1, '2025-09-27 23:54:02', 15),
(51, 38, 'a', '', '', NULL, NULL, NULL, NULL, 'Adviser', 1, '2025-09-28 02:11:50', 15);

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
(183, 27, 'journal_publication_format', 'journal_publication_format_1758706400_c1409241.pdf', '2025-09-24 17:33:20', 1757, 'application/pdf', 0, NULL, NULL),
(184, 27, 'notarized_copyright', 'notarized_copyright_1758706400_ab8726a7.pdf', '2025-09-24 17:33:20', 1757, 'application/pdf', 0, NULL, NULL),
(185, 27, 'receipt_payment', 'receipt_payment_1758706400_2397ed93.pdf', '2025-09-24 17:33:20', 1747, 'application/pdf', 0, NULL, NULL),
(186, 27, 'full_manuscript', 'full_manuscript_1758706400_923aa16d.pdf', '2025-09-24 17:33:20', 1761, 'application/pdf', 0, NULL, NULL),
(187, 27, 'notarized_coauthorship', 'notarized_coauthorship_1758706400_bbb442ce.pdf', '2025-09-24 17:33:20', 1747, 'application/pdf', 0, NULL, NULL),
(188, 27, 'approval_sheet', 'approval_sheet_1759077857_44410eff.pdf', '2025-09-29 00:44:17', 171741, 'application/pdf', 0, NULL, NULL),
(189, 27, 'record_copyright', 'record_copyright_1758706400_d0f68b4c.pdf', '2025-09-24 17:33:20', 1767, 'application/pdf', 0, NULL, NULL),
(197, 29, 'journal_publication_format', 'journal_publication_format_1758707157_27772190.pdf', '2025-09-24 17:45:57', 1757, 'application/pdf', 0, NULL, NULL),
(198, 29, 'notarized_copyright', 'notarized_copyright_1758707157_ee33b3ab.pdf', '2025-09-24 17:45:57', 1767, 'application/pdf', 0, NULL, NULL),
(199, 29, 'receipt_payment', 'receipt_payment_1758707157_80d62120.pdf', '2025-09-24 17:45:57', 1747, 'application/pdf', 0, NULL, NULL),
(200, 29, 'full_manuscript', 'full_manuscript_1758707157_e53f2d10.pdf', '2025-09-24 17:45:57', 1748, 'application/pdf', 0, NULL, NULL),
(201, 29, 'notarized_coauthorship', 'notarized_coauthorship_1758707157_9ed516fb.pdf', '2025-09-24 17:45:57', 1757, 'application/pdf', 0, NULL, NULL),
(202, 29, 'approval_sheet', 'approval_sheet_1759073988_e16e04a4.pdf', '2025-09-28 23:39:48', 355072, 'application/pdf', 0, NULL, NULL),
(203, 29, 'record_copyright', 'record_copyright_1758707157_cade18a0.pdf', '2025-09-24 17:45:57', 1761, 'application/pdf', 0, NULL, NULL),
(211, 31, 'journal_publication_format', 'journal_publication_format_1758707730_cdfae69d.pdf', '2025-09-24 17:55:30', 1748, 'application/pdf', 0, NULL, NULL),
(212, 31, 'notarized_copyright', 'notarized_copyright_1758707730_bc2d2dd4.pdf', '2025-09-24 17:55:30', 1748, 'application/pdf', 0, NULL, NULL),
(213, 31, 'receipt_payment', 'receipt_payment_1758707730_369693c8.pdf', '2025-09-24 17:55:30', 1757, 'application/pdf', 0, NULL, NULL),
(214, 31, 'full_manuscript', 'full_manuscript_1758707730_6b4ecd09.pdf', '2025-09-24 17:55:30', 1757, 'application/pdf', 0, NULL, NULL),
(215, 31, 'notarized_coauthorship', 'notarized_coauthorship_1758707730_b9d498fe.pdf', '2025-09-24 17:55:30', 1761, 'application/pdf', 0, NULL, NULL),
(216, 31, 'approval_sheet', 'approval_sheet_1758707730_0c70b636.pdf', '2025-09-24 17:55:30', 1747, 'application/pdf', 0, NULL, NULL),
(217, 31, 'record_copyright', 'record_copyright_1758707730_c796a47d.pdf', '2025-09-24 17:55:30', 1747, 'application/pdf', 0, NULL, NULL),
(218, 32, 'journal_publication_format', 'journal_publication_format_1758711424_ab156a5c.pdf', '2025-09-24 18:57:04', 355072, 'application/pdf', 0, NULL, NULL),
(219, 32, 'notarized_copyright', 'notarized_copyright_1758711424_b1e5456f.pdf', '2025-09-24 18:57:04', 355072, 'application/pdf', 0, NULL, NULL),
(220, 32, 'receipt_payment', 'receipt_payment_1758711424_5dd8ea4f.pdf', '2025-09-24 18:57:04', 355072, 'application/pdf', 0, NULL, NULL),
(221, 32, 'full_manuscript', 'full_manuscript_1758711424_8d4ce34e.pdf', '2025-09-24 18:57:04', 355072, 'application/pdf', 0, NULL, NULL),
(222, 32, 'notarized_coauthorship', 'notarized_coauthorship_1758711424_8d0eb5dd.pdf', '2025-09-24 18:57:04', 355072, 'application/pdf', 0, NULL, NULL),
(223, 32, 'approval_sheet', 'approval_sheet_1758711424_4958e683.pdf', '2025-09-24 18:57:04', 355072, 'application/pdf', 0, NULL, NULL),
(224, 32, 'record_copyright', 'record_copyright_1758711424_55b289ec.pdf', '2025-09-24 18:57:04', 355072, 'application/pdf', 0, NULL, NULL),
(225, 33, 'journal_publication_format', 'journal_publication_format_1758720752_c909fb4f.pdf', '2025-09-24 21:32:32', 355072, 'application/pdf', 0, NULL, NULL),
(226, 33, 'notarized_copyright', 'notarized_copyright_1758720752_c473cb7d.pdf', '2025-09-24 21:32:32', 355072, 'application/pdf', 0, NULL, NULL),
(227, 33, 'receipt_payment', 'receipt_payment_1758720752_4fbd13dd.pdf', '2025-09-24 21:32:32', 355072, 'application/pdf', 0, NULL, NULL),
(228, 33, 'full_manuscript', 'full_manuscript_1758720752_6469d409.pdf', '2025-09-24 21:32:32', 355072, 'application/pdf', 0, NULL, NULL),
(229, 33, 'notarized_coauthorship', 'notarized_coauthorship_1758720752_610440e1.pdf', '2025-09-24 21:32:32', 355072, 'application/pdf', 0, NULL, NULL),
(230, 33, 'approval_sheet', 'approval_sheet_1758720752_f4a82676.pdf', '2025-09-24 21:32:32', 355072, 'application/pdf', 0, NULL, NULL),
(231, 33, 'record_copyright', 'record_copyright_1758720752_a679b1ae.pdf', '2025-09-24 21:32:32', 355072, 'application/pdf', 0, NULL, NULL),
(232, 34, 'journal_publication_format', 'journal_publication_format_1758978313_bc10661d.pdf', '2025-09-27 21:05:13', 171741, 'application/pdf', 0, NULL, NULL),
(233, 34, 'notarized_copyright', 'notarized_copyright_1758978313_d5267e62.pdf', '2025-09-27 21:05:13', 171741, 'application/pdf', 0, NULL, NULL),
(234, 34, 'receipt_payment', 'receipt_payment_1758978313_4c6f2b67.pdf', '2025-09-27 21:05:13', 171741, 'application/pdf', 0, NULL, NULL),
(235, 34, 'full_manuscript', 'full_manuscript_1758978313_b10ef739.pdf', '2025-09-27 21:05:13', 171741, 'application/pdf', 0, NULL, NULL),
(236, 34, 'notarized_coauthorship', 'notarized_coauthorship_1758978313_0f69d6b7.pdf', '2025-09-27 21:05:13', 171741, 'application/pdf', 0, NULL, NULL),
(237, 34, 'approval_sheet', 'approval_sheet_1758978313_780daf78.pdf', '2025-09-27 21:05:13', 171741, 'application/pdf', 0, NULL, NULL),
(238, 34, 'record_copyright', 'record_copyright_1758978313_de4324dc.pdf', '2025-09-27 21:05:13', 171741, 'application/pdf', 0, NULL, NULL),
(239, 35, 'journal_publication_format', 'journal_publication_format_1758979047_0ae4ce82.pdf', '2025-09-27 21:17:27', 171741, 'application/pdf', 0, NULL, NULL),
(240, 35, 'notarized_copyright', 'notarized_copyright_1758979047_cf419e22.pdf', '2025-09-27 21:17:27', 171741, 'application/pdf', 0, NULL, NULL),
(241, 35, 'receipt_payment', 'receipt_payment_1758979047_a27a1a46.pdf', '2025-09-27 21:17:27', 171741, 'application/pdf', 0, NULL, NULL),
(242, 35, 'full_manuscript', 'full_manuscript_1758979047_3729729e.pdf', '2025-09-27 21:17:27', 171741, 'application/pdf', 0, NULL, NULL),
(243, 35, 'notarized_coauthorship', 'notarized_coauthorship_1758979047_f936dd52.pdf', '2025-09-27 21:17:27', 171741, 'application/pdf', 0, NULL, NULL),
(244, 35, 'approval_sheet', 'approval_sheet_1758979047_439e85eb.pdf', '2025-09-27 21:17:27', 171741, 'application/pdf', 0, NULL, NULL),
(245, 35, 'record_copyright', 'record_copyright_1758979047_4198cb62.pdf', '2025-09-27 21:17:27', 171741, 'application/pdf', 0, NULL, NULL),
(246, 36, 'journal_publication_format', 'journal_publication_format_1758988110_1cf82596.pdf', '2025-09-27 23:48:30', 171741, 'application/pdf', 0, NULL, NULL),
(247, 36, 'notarized_copyright', 'notarized_copyright_1758988110_fe2cbbc7.pdf', '2025-09-27 23:48:30', 171741, 'application/pdf', 0, NULL, NULL),
(248, 36, 'receipt_payment', 'receipt_payment_1758988110_7d7391cf.pdf', '2025-09-27 23:48:30', 171741, 'application/pdf', 0, NULL, NULL),
(249, 36, 'full_manuscript', 'full_manuscript_1758988110_3c069b96.pdf', '2025-09-27 23:48:30', 171741, 'application/pdf', 0, NULL, NULL),
(250, 36, 'notarized_coauthorship', 'notarized_coauthorship_1758988110_670c9813.pdf', '2025-09-27 23:48:30', 171741, 'application/pdf', 0, NULL, NULL),
(251, 36, 'approval_sheet', 'approval_sheet_1758988110_86624ff1.pdf', '2025-09-27 23:48:30', 171741, 'application/pdf', 0, NULL, NULL),
(252, 36, 'record_copyright', 'record_copyright_1758988110_da3b5c1c.pdf', '2025-09-27 23:48:30', 171741, 'application/pdf', 0, NULL, NULL),
(253, 37, 'journal_publication_format', 'journal_publication_format_1758988442_2ea8d677.pdf', '2025-09-27 23:54:02', 171741, 'application/pdf', 0, NULL, NULL),
(254, 37, 'notarized_copyright', 'notarized_copyright_1758988442_102257d6.pdf', '2025-09-27 23:54:02', 171741, 'application/pdf', 0, NULL, NULL),
(255, 37, 'receipt_payment', 'receipt_payment_1758988442_3328ccda.pdf', '2025-09-27 23:54:02', 171741, 'application/pdf', 0, NULL, NULL),
(256, 37, 'full_manuscript', 'full_manuscript_1759145112_472bdd43.pdf', '2025-09-29 19:25:12', 171741, 'application/pdf', 0, NULL, NULL),
(257, 37, 'notarized_coauthorship', 'notarized_coauthorship_1758988442_f754df6e.pdf', '2025-09-27 23:54:02', 171741, 'application/pdf', 0, NULL, NULL),
(258, 37, 'approval_sheet', 'approval_sheet_1759145624_680fad91.pdf', '2025-09-29 19:33:44', 171741, 'application/pdf', 0, NULL, NULL),
(259, 37, 'record_copyright', 'record_copyright_1758988442_4120e2ee.pdf', '2025-09-27 23:54:02', 171741, 'application/pdf', 0, NULL, NULL),
(260, 38, 'journal_publication_format', 'journal_publication_format_1758996710_20522c43.pdf', '2025-09-28 02:11:50', 171741, 'application/pdf', 0, NULL, NULL),
(261, 38, 'notarized_copyright', 'notarized_copyright_1758996710_3461e90a.pdf', '2025-09-28 02:11:50', 171741, 'application/pdf', 0, NULL, NULL),
(262, 38, 'receipt_payment', 'receipt_payment_1758996710_f8359d1f.pdf', '2025-09-28 02:11:50', 171741, 'application/pdf', 0, NULL, NULL),
(263, 38, 'full_manuscript', 'full_manuscript_1758996710_90a425fe.pdf', '2025-09-28 02:11:50', 171741, 'application/pdf', 0, NULL, NULL),
(264, 38, 'notarized_coauthorship', 'notarized_coauthorship_1758996710_e69df97d.pdf', '2025-09-28 02:11:50', 171741, 'application/pdf', 0, NULL, NULL),
(265, 38, 'approval_sheet', 'approval_sheet_1758996710_8e8360e9.pdf', '2025-09-28 02:11:50', 171741, 'application/pdf', 0, NULL, NULL),
(266, 38, 'record_copyright', 'record_copyright_1758996710_04836bf3.pdf', '2025-09-28 02:11:50', 171741, 'application/pdf', 0, NULL, NULL);

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
(27, 'pending', 'Error in Document/Upload', 'test', '', '2025-09-29 00:44:17', '2025-09-29 00:39:07', NULL, 0),
(29, 'pending', 'Error in Document/Upload', 'test', '', '2025-09-28 23:39:48', '2025-09-27 23:31:41', NULL, 0),
(31, 'approved', 'Error in Document', 'test', 'approval_sheet', '2025-09-29 19:30:41', '2025-09-29 19:30:41', NULL, 0),
(32, 'approved', 'Missing Document', NULL, 'approval_sheet|full_manuscript|journal_publication_format|notarized_coauthorship|notarized_copyright|receipt_payment|record_copyright', '2025-09-27 23:38:41', '2025-09-27 23:38:41', NULL, 0),
(33, 'approved', 'Missing Document', 'test', 'approval_sheet', '2025-09-27 23:40:23', '2025-09-27 23:38:31', NULL, 0),
(34, 'approved', 'Missing Document', 'test', 'approval_sheet', '2025-09-28 00:11:49', '2025-09-27 23:38:15', NULL, 0),
(35, 'approved', 'Error in Document', 'test', 'approval_sheet', '2025-09-28 00:20:12', '2025-09-27 23:31:49', NULL, 0),
(37, 'pending', 'Incorrect Document/Upload', 'test', '', '2025-09-29 19:33:44', '2025-09-27 23:57:00', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `student_number` varchar(20) NOT NULL,
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

INSERT INTO `users` (`user_id`, `student_number`, `email`, `password`, `role`, `status`, `verification_code`, `code_expires_at`, `email_verified_at`, `created_at`) VALUES
(18, '2020-12345-MN-0', 'admin@ipmo.local', '$2y$10$KLqtU38o9d2UNOL5rrxU7.SnM6d3gL1PxxtqltKwbb6NnuJdBcR3W', 'admin', 'active', NULL, NULL, NULL, '2025-09-23 21:52:32'),
(19, '2023-12335-MN-0', 'hellohihihi1234567890@gmail.com', '$2y$10$.O0EiMRNxmuVXLUaleq6zeFYyTziPjpyO3Ixf4IWcY24moWKwJdsS', 'student', 'active', NULL, NULL, '2025-09-24 15:35:59', '2025-09-24 15:35:48'),
(20, '2023-12345-MN-0', 'aceplanetary0@gmail.com', '$2y$10$.dyFX21SHVFX9cVsiIJ18O3jEA9PtXmZpvtI65RxasxVFOnpnMT8q', 'student', 'active', NULL, NULL, '2025-09-24 18:22:51', '2025-09-24 18:22:40'),
(21, '2023-12355-MN-0', 'errorloading19990@gmail.com', '$2y$10$KeO9PfVxlab10fBUfbafZ.cFcwBhxr8xKoLNvBQuPl.rwrsrq4AHi', 'student', 'pending', '19a79347aa094355', '2025-09-25 12:26:02', NULL, '2025-09-24 18:26:02'),
(22, '2022-08680-MN-0', 'inocentesraebv@gmail.com', '$2y$12$s37ZbFYWYuPs.aK08SGzHOYkZQqCH/iQgg1tyahLuld8gT.kEFUIe', 'student', 'active', NULL, NULL, '2025-09-24 23:02:31', '2025-09-24 22:57:00');

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
-- Indexes for table `advisers`
--
ALTER TABLE `advisers`
  ADD PRIMARY KEY (`adviser_id`);

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
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `student_number` (`student_number`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `advisers`
--
ALTER TABLE `advisers`
  MODIFY `adviser_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `student_profiles`
--
ALTER TABLE `student_profiles`
  MODIFY `profile_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `submission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `submission_authors`
--
ALTER TABLE `submission_authors`
  MODIFY `author_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `submission_documents`
--
ALTER TABLE `submission_documents`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=267;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `student_profiles`
--
ALTER TABLE `student_profiles`
  ADD CONSTRAINT `student_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `submissions`
--
ALTER TABLE `submissions`
  ADD CONSTRAINT `submissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `submissions_ibfk_2` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `submissions_ibfk_3` FOREIGN KEY (`adviser_id`) REFERENCES `advisers` (`adviser_id`);

--
-- Constraints for table `submission_authors`
--
ALTER TABLE `submission_authors`
  ADD CONSTRAINT `submission_authors_ibfk_1` FOREIGN KEY (`submission_id`) REFERENCES `submissions` (`submission_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `submission_authors_ibfk_2` FOREIGN KEY (`adviser_id`) REFERENCES `advisers` (`adviser_id`);

--
-- Constraints for table `submission_documents`
--
ALTER TABLE `submission_documents`
  ADD CONSTRAINT `submission_documents_ibfk_1` FOREIGN KEY (`submission_id`) REFERENCES `submissions` (`submission_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `submission_documents_ibfk_2` FOREIGN KEY (`verified_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `submission_incomplete_meta`
--
ALTER TABLE `submission_incomplete_meta`
  ADD CONSTRAINT `fk_sim_submission` FOREIGN KEY (`submission_id`) REFERENCES `submissions` (`submission_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
