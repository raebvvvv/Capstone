-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 11, 2025 at 10:47 PM
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
-- Table structure for table `academic_levels`
--

CREATE TABLE `academic_levels` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `academic_levels`
--

INSERT INTO `academic_levels` (`id`, `name`, `code`) VALUES
(1, 'Undergraduate', 'UG'),
(2, 'Masters', 'MS'),
(3, 'Doctorate', 'PhD'),
(4, 'Open University', 'OU'),
(5, 'Not Studying', 'NS');

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

-- --------------------------------------------------------

--
-- Table structure for table `campuses`
--

CREATE TABLE `campuses` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `campuses`
--

INSERT INTO `campuses` (`id`, `name`, `code`) VALUES
(1, 'PUP Main (Sta. Mesa, Manila)', 'MAIN'),
(7, 'test', 'test');

-- --------------------------------------------------------

--
-- Table structure for table `colleges`
--

CREATE TABLE `colleges` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `campus_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `colleges`
--

INSERT INTO `colleges` (`id`, `name`, `code`, `campus_id`) VALUES
(1, 'College of Accountancy and Finance (CAF)', 'CAF', 1),
(2, 'College of Architecture, Design and the Built Environment (CADBE)', 'CADBE', 1),
(3, 'College of Arts and Letters (CAL)', 'CAL', 1),
(4, 'College of Business Administration (CBA)', 'CBA', 1),
(5, 'College of Communication (COC)', 'COC', 1),
(6, 'College of Computer and Information Sciences (CCIS)', 'CCIS', 1),
(7, 'College of Education (COED)', 'COED', 1),
(8, 'College of Engineering (CE)', 'CE', 1),
(9, 'College of Human Kinetics (CHK)', 'CHK', 1),
(10, 'College of Law (CL)', 'CL', 1),
(11, 'College of Political Science and Public Administration (CPSPA)', 'CPSPA', 1),
(12, 'College of Social Sciences and Development (CSSD)', 'CSSD', 1),
(13, 'College of Science (CS)', 'CS', 1),
(14, 'College of Tourism, Hospitality and Transportation Management (CTHTM)', 'CTHTM', 1),
(15, 'Institute of Technology', 'ITech', 1),
(31, 'TEST', 'TEST', NULL);

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

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `college_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `code`, `college_id`) VALUES
(1, 'Department of Accountancy', NULL, 1),
(2, 'Department of Finance and Economics', NULL, 1),
(3, 'Department of Management Accounting', NULL, 1),
(4, 'Department of Computer Science', NULL, 6),
(5, 'Department of Information Technology', NULL, 6),
(6, 'Department of Civil Engineering', NULL, 8),
(7, 'Department of Computer Engineering', NULL, 8),
(8, 'Department of Electrical Engineering', NULL, 8),
(9, 'Department of Electronics Engineering', NULL, 8),
(10, 'Department of Industrial Engineering', NULL, 8),
(11, 'Department of Mechanical Engineering', NULL, 8),
(12, 'Department of Railway Engineering', NULL, 8),
(13, 'Department of Business Administration', NULL, 4),
(14, 'Department of Entrepreneurship', NULL, 4),
(15, 'Department of Office Administration', NULL, 4);

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'both'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `name`, `code`, `role`) VALUES
(1, 'Journal Publication Format', 'JPF', 'both'),
(2, 'Notarized Copyright Application Form', 'NCAF', 'both'),
(3, 'Receipt of Payment', 'RCPT', 'both'),
(4, 'Full Manuscript', 'FMSS', 'student'),
(5, 'Notarized Co-Authorship', 'NCAU', 'both'),
(6, 'Approval Sheet (Thesis)', 'APRV', 'student'),
(7, 'Record of Copyright Application', 'ROCA', 'both'),
(8, 'Presentation', 'PRSN', 'employee'),
(11, 'Heart', NULL, 'student');

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

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `college_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`id`, `name`, `code`, `college_id`) VALUES
(1, 'Bachelor of Science in Accountancy (BSA)', 'BSA', 1),
(2, 'Bachelor of Science in Business Administration Major in Financial Management (BSBAFM)', 'BSBAFM', 1),
(3, 'Bachelor of Science in Management Accounting (BSMA)', 'BSMA', 1),
(4, 'Bachelor of Science in Architecture (BS-ARCH)', 'BS-ARCH', 2),
(5, 'Bachelor of Science in Interior Design (BSID)', 'BSID', 2),
(6, 'Bachelor of Science in Environmental Planning (BSEP)', 'BSEP', 2),
(7, 'Bachelor of Arts in English Language Studies (ABELS)', 'ABELS', 3),
(8, 'Bachelor of Arts in Filipinology (ABF)', 'ABF', 3),
(9, 'Bachelor of Arts in Literary and Cultural Studies (ABLCS)', 'ABLCS', 3),
(10, 'Bachelor of Arts in Philosophy (AB-PHILO)', 'AB-PHILO', 3),
(11, 'Bachelor of Performing Arts major in Theater Arts (BPEA)', 'BPEA', 3),
(12, 'Doctor in Business Administration (DBA)', 'DBA', 4),
(13, 'Master in Business Administration (MBA)', 'MBA', 4),
(14, 'Bachelor of Science in Business Administration major in Human Resource Management (BSBAHRM)', 'BSBAHRM', 4),
(15, 'Bachelor of Science in Business Administration major in Marketing Management (BSBA-MM)', 'BSBA-MM', 4),
(16, 'Bachelor of Science in Entrepreneurship (BSENTREP)', 'BSENTREP', 4),
(17, 'Bachelor of Science in Office Administration (BSOA)', 'BSOA', 4),
(18, 'Bachelor in Advertising and Public Relations (BADPR)', 'BADPR', 5),
(19, 'Bachelor of Arts in Broadcasting (BA Broadcasting)', 'BA Broadcasting', 5),
(20, 'Bachelor of Arts in Communication Research (BACR)', 'BACR', 5),
(21, 'Bachelor of Arts in Journalism (BAJ)', 'BAJ', 5),
(22, 'Bachelor of Science in Computer Science (BSCS)', 'BSCS', 6),
(23, 'Bachelor of Science in Information Technology (BSIT)', 'BSIT', 6),
(24, 'Doctor of Philsophy in Education Management (PhDEM)', 'PhDEM', 7),
(25, 'Master of Arts in Education Management (MAEM)', 'MAEM', 7),
(26, 'Master in Business Education (MBE)', 'MBE', 7),
(27, 'Master in Library and Information Science (MLIS)', 'MLIS', 7),
(28, 'Master of Arts in English Language Teaching (MAELT)', 'MAELT', 7),
(29, 'Master of Arts in Education major in Mathematics Education (MAEd-ME)', 'MAEd-ME', 7),
(30, 'Master of Arts in Physical Education and Sports (MAPES)', 'MAPES', 7),
(31, 'Master of Arts in Education major in Teaching in the Challenged Areas (MAED-TCA)', 'MAED-TCA', 7),
(32, 'Post-Baccalaureate Diploma in Education (PBDE)', 'PBDE', 7),
(33, 'Bachelor of Technology and Livelihood Education - Home Economics (BTLEd)', 'BTLEd', 7),
(34, 'Bachelor of Technology and Livelihood Education - Industrial Arts (BTLEd)', 'BTLEd', 7),
(35, 'Bachelor of Technology and Livelihood Education - ICT (BTLEd)', 'BTLEd', 7),
(36, 'Bachelor of Library and Information Science (BLIS)', 'BLIS', 7),
(37, 'Bachelor of Secondary Education - English (BSEd)', 'BSEd', 7),
(38, 'Bachelor of Secondary Education - Mathematics (BSEd)', 'BSEd', 7),
(39, 'Bachelor of Secondary Education - Science (BSEd)', 'BSEd', 7),
(40, 'Bachelor of Secondary Education - Filipino (BSEd)', 'BSEd', 7),
(41, 'Bachelor of Secondary Education - Social Studies (BSEd)', 'BSEd', 7),
(42, 'Bachelor of Elementary Education (BEEd)', 'BEEd', 7),
(43, 'Bachelor of Early Childhood Education (BECEd)', 'BECEd', 7),
(44, 'Bachelor of Science in Civil Engineering (BSCE)', 'BSCE', 8),
(45, 'Bachelor of Science in Computer Engineering (BSCpE)', 'BSCpE', 8),
(46, 'Bachelor of Science in Electrical Engineering (BSEE)', 'BSEE', 8),
(47, 'Bachelor of Science in Electronics Engineering (BSECE)', 'BSECE', 8),
(48, 'Bachelor of Science in Industrial Engineering (BSIE)', 'BSIE', 8),
(49, 'Bachelor of Science in Mechanical Engineering (BSME)', 'BSME', 8),
(50, 'Bachelor of Science in Railway Engineering (BSRE)', 'BSRE', 8),
(51, 'Bachelor of Physical Education (BPE)', 'BPE', 9),
(52, 'Bachelor of Science in Exercises and Sports (BSESS)', 'BSESS', 9),
(53, 'Juris Doctor (JD)', 'JD', 10),
(54, 'Doctor in Public Administration (DPA)', 'DPA', 11),
(55, 'Master in Public Administration (MPA)', 'MPA', 11),
(56, 'Bachelor of Arts in Political Science (BAPS)', 'BAPS', 11),
(57, 'Bachelor of Arts in Political Economy (BAPE)', 'BAPE', 11),
(58, 'Bachelor of Arts in International Studies (BAIS)', 'BAIS', 11),
(59, 'Bachelor of Public Administration (BPA)', 'BPA', 11),
(60, 'Bachelor of Arts in History (BAH)', 'BAH', 12),
(61, 'Bachelor of Arts in Sociology (BAS)', 'BAS', 12),
(62, 'Bachelor of Science in Cooperatives (BSC)', 'BSC', 12),
(63, 'Bachelor of Science in Economics (BSE)', 'BSE', 12),
(64, 'Bachelor of Science in Psychology (BSPSY)', 'BSPSY', 12),
(65, 'Bachelor of Science Food Technology (BSFT)', 'BSFT', 13),
(66, 'Bachelor of Science in Applied Mathematics (BSAPMATH)', 'BSAPMATH', 13),
(67, 'Bachelor of Science in Biology (BSBIO)', 'BSBIO', 13),
(68, 'Bachelor of Science in Chemistry (BSCHEM)', 'BSCHEM', 13),
(69, 'Bachelor of Science in Mathematics (BSMATH)', 'BSMATH', 13),
(70, 'Bachelor of Science in Nutrition and Dietetics (BSND)', 'BSND', 13),
(71, 'Bachelor of Science in Physics (BSPHY)', 'BSPHY', 13),
(72, 'Bachelor of Science in Statistics (BSSTAT)', 'BSSTAT', 13),
(73, 'Bachelor of Science in Hospitality Management (BSHM)', 'BSHM', 14),
(74, 'Bachelor of Science in Tourism Management (BSTM)', 'BSTM', 14),
(75, 'Bachelor of Science in Transportation Management (BSTRM)', 'BSTRM', 14),
(76, 'Diploma in Computer Engineering Technology (DCET)', 'DCET', 15),
(77, 'Diploma in Electrical Engineering Technology (DEET)', 'DEET', 15),
(78, 'Diploma in Electronics Engineering Technology (DECET)', 'DECET', 15),
(79, 'Diploma in Information Communication Technology (DICT)', 'DICT', 15),
(80, 'Diploma in Mechanical Engineering Technology (DMET)', 'DMET', 15),
(81, 'Diploma in Office Management (DOMT)', 'DOMT', 15),
(82, 'Master in Applied Statistics (MAS)', 'MAS', NULL),
(83, 'Master in Business Administration (MBA)', 'MBA', NULL),
(84, 'Master in Construction Management (MCM)', 'MCM', NULL),
(85, 'Master in Educational Management (MEM)', 'MEM', NULL),
(86, 'Master in Public Administration (MPA)', 'MPA', NULL),
(87, 'Master of Arts in Communication (MAC)', 'MAC', NULL),
(88, 'Master of Arts in English Language Studies (MAELS)', 'MAELS', NULL),
(89, 'Master of Arts in History (MAH)', 'MAH', NULL),
(90, 'Master of Arts in Filipino (MAF)', 'MAF', NULL),
(91, 'Master of Arts in Psychology (MAP)', 'MAP', NULL),
(92, 'Master of Arts in Technology Management (MATM)', 'MATM', NULL),
(93, 'Master of Science in Biology (MSBio)', 'MSBio', NULL),
(94, 'Master of Science in Civil Engineering (MSCE)', 'MSCE', NULL),
(95, 'Master of Science in Computer Engineering (MSCpE)', 'MSCpE', NULL),
(96, 'Master of Science in Computer Science (MSCS)', 'MSCS', NULL),
(97, 'Master of Science in Construction Management (MSCM)', 'MSCM', NULL),
(98, 'Master of Science in Information Technology (MSIT)', 'MSIT', NULL),
(99, 'Master of Science in Mathematics (MSM)', 'MSM', NULL),
(100, 'Doctor of Philosophy in Communication (PhD Com)', 'PhD Com', NULL),
(101, 'Doctor of Philosophy in Economics (PhD Econ)', 'PhD Econ', NULL),
(102, 'Doctor of Philosophy in English Language Studies (PhD ELS)', 'PhD ELS', NULL),
(103, 'Doctor of Philosophy in Filipino (PhD Fil)', 'PhD Fil', NULL),
(104, 'Doctor of Philosophy in Psychology (PhD Psy)', 'PhD Psy', NULL),
(105, 'Doctor in Business Administration (DBA)', 'DBA', NULL),
(106, 'Doctor in Engineering Management (D.Eng)', 'D.Eng', NULL),
(107, 'Doctor of Philsophy in Education Management (PhDEM)', 'PhDEM', NULL),
(108, 'Doctor in Public Administration (DPA)', 'DPA', NULL),
(109, 'Master in Communication (MC)', 'MC', NULL),
(110, 'Master in Business Administration (MBA)', 'MBA', NULL),
(111, 'Master of Arts in Education Management (MAEM)', 'MAEM', NULL),
(112, 'Master in Information Technology (MIT)', 'MIT', NULL),
(113, 'Master in Public Administration (MPA)', 'MPA', NULL),
(114, 'Master of Science in Construction Management (MSCM)', 'MSCM', NULL),
(115, 'Post Baccalaureate Diploma in Information Technology (PBDIT)', 'PBDIT', NULL),
(116, 'Bachelor of Science in Entrepreneurship (BSENTREP)', 'BSENTREP', NULL),
(117, 'Bachelor of Arts in Broadcasting (BABR)', 'BABR', NULL),
(118, 'Bachelor of Science in Business Administration major in Human Resource Management (BSBAHRM)', 'BSBAHRM', NULL),
(119, 'Bachelor of Science in Business Administration major in Marketing Management (BSBAMM)', 'BSBAMM', NULL),
(120, 'Bachelor of Science in Office Administration (BSOA)', 'BSOA', NULL),
(121, 'Bachelor of Science in Tourism Management (BSTM)', 'BSTM', NULL),
(122, 'Bachelor of Public Administration (BPA)', 'BPA', NULL),
(123, 'Bachelor of Science in Business Administration (BSBA)', 'BSBA', NULL),
(124, 'Bachelor of Science in Information Technology (BSIT)', 'BSIT', NULL),
(206, 'Master in Applied Statistics (MAS)', 'MAS', NULL),
(207, 'Master in Business Administration (MBA)', 'MBA', NULL),
(208, 'Master in Construction Management (MCM)', 'MCM', NULL),
(209, 'Master in Educational Management (MEM)', 'MEM', NULL),
(210, 'Master in Public Administration (MPA)', 'MPA', NULL),
(211, 'Master of Arts in Communication (MAC)', 'MAC', NULL),
(212, 'Master of Arts in English Language Studies (MAELS)', 'MAELS', NULL),
(213, 'Master of Arts in History (MAH)', 'MAH', NULL),
(214, 'Master of Arts in Filipino (MAF)', 'MAF', NULL),
(215, 'Master of Arts in Psychology (MAP)', 'MAP', NULL),
(216, 'Master of Arts in Technology Management (MATM)', 'MATM', NULL),
(217, 'Master of Science in Biology (MSBio)', 'MSBio', NULL),
(218, 'Master of Science in Civil Engineering (MSCE)', 'MSCE', NULL),
(219, 'Master of Science in Computer Engineering (MSCpE)', 'MSCpE', NULL),
(220, 'Master of Science in Computer Science (MSCS)', 'MSCS', NULL),
(221, 'Master of Science in Construction Management (MSCM)', 'MSCM', NULL),
(222, 'Master of Science in Information Technology (MSIT)', 'MSIT', NULL),
(223, 'Master of Science in Mathematics (MSM)', 'MSM', NULL),
(224, 'Doctor of Philosophy in Communication (PhD Com)', 'PhD Com', NULL),
(225, 'Doctor of Philosophy in Economics (PhD Econ)', 'PhD Econ', NULL),
(226, 'Doctor of Philosophy in English Language Studies (PhD ELS)', 'PhD ELS', NULL),
(227, 'Doctor of Philosophy in Filipino (PhD Fil)', 'PhD Fil', NULL),
(228, 'Doctor of Philosophy in Psychology (PhD Psy)', 'PhD Psy', NULL),
(229, 'Doctor in Business Administration (DBA)', 'DBA', NULL),
(230, 'Doctor in Engineering Management (D.Eng)', 'D.Eng', NULL),
(231, 'Doctor of Philsophy in Education Management (PhDEM)', 'PhDEM', NULL),
(232, 'Doctor in Public Administration (DPA)', 'DPA', NULL),
(233, 'Master in Communication (MC)', 'MC', NULL),
(234, 'Master in Business Administration (MBA)', 'MBA', NULL),
(235, 'Master of Arts in Education Management (MAEM)', 'MAEM', NULL),
(236, 'Master in Information Technology (MIT)', 'MIT', NULL),
(237, 'Master in Public Administration (MPA)', 'MPA', NULL),
(238, 'Master of Science in Construction Management (MSCM)', 'MSCM', NULL),
(239, 'Post Baccalaureate Diploma in Information Technology (PBDIT)', 'PBDIT', NULL),
(240, 'Bachelor of Science in Entrepreneurship (BSENTREP)', 'BSENTREP', NULL),
(241, 'Bachelor of Arts in Broadcasting (BABR)', 'BABR', NULL),
(242, 'Bachelor of Science in Business Administration major in Human Resource Management (BSBAHRM)', 'BSBAHRM', NULL),
(243, 'Bachelor of Science in Business Administration major in Marketing Management (BSBAMM)', 'BSBAMM', NULL),
(244, 'Bachelor of Science in Office Administration (BSOA)', 'BSOA', NULL),
(245, 'Bachelor of Science in Tourism Management (BSTM)', 'BSTM', NULL),
(246, 'Bachelor of Public Administration (BPA)', 'BPA', NULL),
(247, 'Bachelor of Science in Business Administration (BSBA)', 'BSBA', NULL),
(248, 'Bachelor of Science in Information Technology (BSIT)', 'BSIT', NULL),
(249, 'TEST', 'TEST', 31),
(331, 'Master in Applied Statistics (MAS)', 'MAS', NULL),
(332, 'Master in Business Administration (MBA)', 'MBA', NULL),
(333, 'Master in Construction Management (MCM)', 'MCM', NULL),
(334, 'Master in Educational Management (MEM)', 'MEM', NULL),
(335, 'Master in Public Administration (MPA)', 'MPA', NULL),
(336, 'Master of Arts in Communication (MAC)', 'MAC', NULL),
(337, 'Master of Arts in English Language Studies (MAELS)', 'MAELS', NULL),
(338, 'Master of Arts in History (MAH)', 'MAH', NULL),
(339, 'Master of Arts in Filipino (MAF)', 'MAF', NULL),
(340, 'Master of Arts in Psychology (MAP)', 'MAP', NULL),
(341, 'Master of Arts in Technology Management (MATM)', 'MATM', NULL),
(342, 'Master of Science in Biology (MSBio)', 'MSBio', NULL),
(343, 'Master of Science in Civil Engineering (MSCE)', 'MSCE', NULL),
(344, 'Master of Science in Computer Engineering (MSCpE)', 'MSCpE', NULL),
(345, 'Master of Science in Computer Science (MSCS)', 'MSCS', NULL),
(346, 'Master of Science in Construction Management (MSCM)', 'MSCM', NULL),
(347, 'Master of Science in Information Technology (MSIT)', 'MSIT', NULL),
(348, 'Master of Science in Mathematics (MSM)', 'MSM', NULL),
(349, 'Doctor of Philosophy in Communication (PhD Com)', 'PhD Com', NULL),
(350, 'Doctor of Philosophy in Economics (PhD Econ)', 'PhD Econ', NULL),
(351, 'Doctor of Philosophy in English Language Studies (PhD ELS)', 'PhD ELS', NULL),
(352, 'Doctor of Philosophy in Filipino (PhD Fil)', 'PhD Fil', NULL),
(353, 'Doctor of Philosophy in Psychology (PhD Psy)', 'PhD Psy', NULL),
(354, 'Doctor in Business Administration (DBA)', 'DBA', NULL),
(355, 'Doctor in Engineering Management (D.Eng)', 'D.Eng', NULL),
(356, 'Doctor of Philsophy in Education Management (PhDEM)', 'PhDEM', NULL),
(357, 'Doctor in Public Administration (DPA)', 'DPA', NULL),
(358, 'Master in Communication (MC)', 'MC', NULL),
(359, 'Master in Business Administration (MBA)', 'MBA', NULL),
(360, 'Master of Arts in Education Management (MAEM)', 'MAEM', NULL),
(361, 'Master in Information Technology (MIT)', 'MIT', NULL),
(362, 'Master in Public Administration (MPA)', 'MPA', NULL),
(363, 'Master of Science in Construction Management (MSCM)', 'MSCM', NULL),
(364, 'Post Baccalaureate Diploma in Information Technology (PBDIT)', 'PBDIT', NULL),
(365, 'Bachelor of Science in Entrepreneurship (BSENTREP)', 'BSENTREP', NULL),
(366, 'Bachelor of Arts in Broadcasting (BABR)', 'BABR', NULL),
(367, 'Bachelor of Science in Business Administration major in Human Resource Management (BSBAHRM)', 'BSBAHRM', NULL),
(368, 'Bachelor of Science in Business Administration major in Marketing Management (BSBAMM)', 'BSBAMM', NULL),
(369, 'Bachelor of Science in Office Administration (BSOA)', 'BSOA', NULL),
(370, 'Bachelor of Science in Tourism Management (BSTM)', 'BSTM', NULL),
(371, 'Bachelor of Public Administration (BPA)', 'BPA', NULL),
(372, 'Bachelor of Science in Business Administration (BSBA)', 'BSBA', NULL),
(373, 'Bachelor of Science in Information Technology (BSIT)', 'BSIT', NULL);

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
  `created_at` datetime DEFAULT current_timestamp(),
  `password_reset_token` varchar(255) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password`, `role`, `status`, `verification_code`, `code_expires_at`, `email_verified_at`, `created_at`, `password_reset_token`, `token_expiry`) VALUES
(1, 'aceplanetary0@gmail.com', '$2y$10$hhH/xfeUKvwPOTBNhk3IaO5Uk3vlOrAui2Hdq05.qcYI/1U.TANwi', 'student', 'active', NULL, NULL, '2025-10-06 03:36:13', '2025-10-06 03:35:45', NULL, NULL),
(2, 'errorloading19990@gmail.com', '$2y$10$.N/AIAFQjEvxRcOgrd9nVOpMGt3tODrdZoPpsnJAYGHM.k/zsVbtK', 'employee', 'active', NULL, NULL, '2025-10-06 03:39:03', '2025-10-06 03:38:36', NULL, NULL),
(4, 'admin@ipmo.local', '$2y$10$FcSO4or9z9oUzIEIJW/87uNeKHnf9wrdWOdN2w6A/N6E.jHnK2owy', 'admin', 'active', NULL, NULL, '2025-10-06 03:55:43', '2025-10-06 03:55:43', NULL, NULL),
(5, 'inocentesraebv@gmail.com', '$2y$12$MvO.41oeGZKRmqJc6lM8Ueppkcugyq4/0lvZKR5m3ETVlNRPlvx2a', 'student', 'active', '9481d21b6c60ec81', '2025-10-07 22:30:53', NULL, '2025-10-06 22:30:53', NULL, NULL),
(6, 'thinkingwan00@gmail.com', '$2y$12$0y66PWURrNDe6JcjfLEKq./G20X66tAdMqJP3lC6LriVPAPfN4eAS', 'employee', 'active', 'd8b22737708b65f0', '2025-10-08 22:19:24', NULL, '2025-10-07 22:19:24', NULL, NULL),
(7, 'nadaone@gmail.com', '$2y$12$WPx18A4KGzbFRi4CtTJOMuuFtRE.ULuCNxYgklx7VA4S0TUBPL8hS', 'student', 'pending', '5517b8c4d76fa2bf', '2025-10-08 22:41:29', NULL, '2025-10-07 22:41:29', NULL, NULL),
(14, 'hellohihihi1234567890@gmail.com', '$2y$12$6.K2IbpZUjnJzyI3wzVihOb7mDlXiXwZ6tMRECzaXVmdZH9UjL54i', 'student', 'active', '1d501032ef650c70', '2025-10-08 23:58:05', NULL, '2025-10-07 23:58:05', NULL, NULL),
(17, 'markreinier.garcia@gmail.com', '$2y$10$NllV37z8YA35Qcpy3e/OQuTmjnoGF5W/cnzS8LzJbyT0keJ2cMs5q', 'employee', 'active', NULL, NULL, '2025-10-11 04:01:04', '2025-10-11 04:00:50', '8a8fd8f5333963235eb8efaf34ec862618e140c37bdfbeaa415374664a905d6f', '2025-10-10 23:50:21');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_levels`
--
ALTER TABLE `academic_levels`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_levels_name` (`name`);

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
-- Indexes for table `campuses`
--
ALTER TABLE `campuses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_campus_name` (`name`);

--
-- Indexes for table `colleges`
--
ALTER TABLE `colleges`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_college_name` (`name`),
  ADD KEY `idx_campus_id` (`campus_id`);

--
-- Indexes for table `dashboard_summary`
--
ALTER TABLE `dashboard_summary`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_department_name_college` (`name`,`college_id`),
  ADD KEY `idx_college_id` (`college_id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_document_name_role` (`name`,`role`);

--
-- Indexes for table `employee_profiles`
--
ALTER TABLE `employee_profiles`
  ADD PRIMARY KEY (`profile_id`),
  ADD KEY `fk_employee_user` (`user_id`);

--
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_program_name_college` (`name`,`college_id`),
  ADD KEY `idx_college_id` (`college_id`);

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
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_levels`
--
ALTER TABLE `academic_levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=119;

--
-- AUTO_INCREMENT for table `admin_profiles`
--
ALTER TABLE `admin_profiles`
  MODIFY `profile_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `advisers`
--
ALTER TABLE `advisers`
  MODIFY `adviser_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `campuses`
--
ALTER TABLE `campuses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `colleges`
--
ALTER TABLE `colleges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `employee_profiles`
--
ALTER TABLE `employee_profiles`
  MODIFY `profile_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=374;

--
-- AUTO_INCREMENT for table `student_profiles`
--
ALTER TABLE `student_profiles`
  MODIFY `profile_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `submission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=136;

--
-- AUTO_INCREMENT for table `submission_authors`
--
ALTER TABLE `submission_authors`
  MODIFY `author_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `submission_documents`
--
ALTER TABLE `submission_documents`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=542;

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
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_profiles`
--
ALTER TABLE `admin_profiles`
  ADD CONSTRAINT `fk_admin_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `colleges`
--
ALTER TABLE `colleges`
  ADD CONSTRAINT `fk_college_campus` FOREIGN KEY (`campus_id`) REFERENCES `campuses` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `fk_dept_college` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_profiles`
--
ALTER TABLE `employee_profiles`
  ADD CONSTRAINT `fk_employee_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `programs`
--
ALTER TABLE `programs`
  ADD CONSTRAINT `fk_prog_college` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `submissions`
--
ALTER TABLE `submissions`
  ADD CONSTRAINT `fk_submissions_reviewer_id` FOREIGN KEY (`reviewer_id`) REFERENCES `admin_profiles` (`profile_id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
