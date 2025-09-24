-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 24, 2025 at 10:31 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

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
(11, 'Hella', '', 'Deola', NULL, NULL, NULL, '2025-09-24 15:47:37');

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
(8, 19, 'Dela Cruz', 'Juan', 'Malinaw', '', '123 Sampaguita St., Manila City', '09171234567', 'PUP Main', 'College of Engineering', 'Computer Engineering', 'BS Computer Engineering', NULL);

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
(24, 'SRID-2025-20250924-1', 19, 'Juan', 'Malinaw', 'Dela Cruz', '2023-12345-MN-0', '123 Sampaguita St., Manila City', '09171234567', 'hellohihihi1234567890@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Masters', 'N/A', 'Master of Science in Computer Engineering (MSCpE)', '(k) Photographic works including works produced by a process analogous to photography', 'A Mathematical Model for Predicting the Diffusion of  Information in Social Networks', '2025-09-12', 1, 'pending_review', NULL, 'for evaluation', 1, 1, 'copyright', '2025-09-24 15:57:34', '2025-09-24 16:00:27', NULL, NULL, 11);

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
(31, 24, 'Janella', 'Malinaw', 'Salvador', '2023-12345-MN-0', '09171234567', '123 Sampaguita St., Manila City', 'juandelacruz@iskolarngbayan.pup.edu.ph', 'Author', 0, '2025-09-24 15:57:34', NULL);

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
(162, 24, 'journal_publication_format', 'journal_publication_format_1758700654_b4e33b42.pdf', '2025-09-24 15:57:34', 1748, 'application/pdf', 0, NULL, NULL),
(163, 24, 'notarized_copyright', 'notarized_copyright_1758700654_18d48f6d.pdf', '2025-09-24 15:57:34', 1748, 'application/pdf', 0, NULL, NULL),
(164, 24, 'receipt_payment', 'receipt_payment_1758700654_87b0d0c4.pdf', '2025-09-24 15:57:34', 1747, 'application/pdf', 0, NULL, NULL),
(165, 24, 'full_manuscript', 'full_manuscript_1758700654_bb820f0c.pdf', '2025-09-24 15:57:34', 1747, 'application/pdf', 0, NULL, NULL),
(166, 24, 'notarized_coauthorship', 'notarized_coauthorship_1758700654_b824f5f6.pdf', '2025-09-24 15:57:34', 1747, 'application/pdf', 0, NULL, NULL),
(167, 24, 'approval_sheet', 'approval_sheet_1758700654_9f363355.pdf', '2025-09-24 15:57:34', 1757, 'application/pdf', 0, NULL, NULL),
(168, 24, 'record_copyright', 'record_copyright_1758700654_c390d101.pdf', '2025-09-24 15:57:34', 1761, 'application/pdf', 0, NULL, NULL);

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
(19, '2023-12335-MN-0', 'hellohihihi1234567890@gmail.com', '$2y$10$.O0EiMRNxmuVXLUaleq6zeFYyTziPjpyO3Ixf4IWcY24moWKwJdsS', 'student', 'active', NULL, NULL, '2025-09-24 15:35:59', '2025-09-24 15:35:48');

--
-- Indexes for dumped tables
--

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
-- AUTO_INCREMENT for table `advisers`
--
ALTER TABLE `advisers`
  MODIFY `adviser_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `student_profiles`
--
ALTER TABLE `student_profiles`
  MODIFY `profile_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `submission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `submission_authors`
--
ALTER TABLE `submission_authors`
  MODIFY `author_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `submission_documents`
--
ALTER TABLE `submission_documents`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=169;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
