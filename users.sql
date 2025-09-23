-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 22, 2025 at 09:27 AM
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
-- Table structure for table `student_profiles`
--

CREATE TABLE `student_profiles` (
  `profile_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_initial` varchar(5) NOT NULL,
  `suffix` varchar(5) DEFAULT NULL,
  `home_address` varchar(255) NOT NULL,
  `mobile_number` varchar(15) NOT NULL,
  `campus` varchar(50) NOT NULL,
  `college` varchar(50) NOT NULL,
  `department` varchar(50) NOT NULL,
  `program` varchar(50) NOT NULL,
  `last_updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_profiles`
--

INSERT INTO `student_profiles` (`profile_id`, `user_id`, `last_name`, `first_name`, `middle_initial`, `suffix`, `home_address`, `mobile_number`, `campus`, `college`, `department`, `program`, `last_updated_at`) VALUES
(2, 9, 'Hella', 'HIHI', 'E', '', 'Sampaguita St., Manila City', '09171234567', 'PUP Main', 'College of Engineering', 'Computer Engineering', 'BS Computer Engineering', '2025-09-22 15:16:43');

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
(9, '2023-12345-MN-0', 'hellohihihi1234567890@gmail.com', '$2y$10$WJtEcWuf1yW41Nq.B25mhuJN/3kWhoGIeMF6wckIlrfTOLcK./oBC', 'student', 'active', NULL, NULL, '2025-09-22 15:15:44', '2025-09-22 15:15:30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `student_profiles`
--
ALTER TABLE `student_profiles`
  ADD PRIMARY KEY (`profile_id`),
  ADD KEY `user_id` (`user_id`);

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
-- AUTO_INCREMENT for table `student_profiles`
--
ALTER TABLE `student_profiles`
  MODIFY `profile_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `student_profiles`
--
ALTER TABLE `student_profiles`
  ADD CONSTRAINT `student_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

CREATE TABLE submissions (
  submission_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL, -- FK to users.user_id

  -- Student Snapshot Info
  first_name VARCHAR(50) NOT NULL,
  middle_name VARCHAR(50),
  last_name VARCHAR(50) NOT NULL,
  student_number VARCHAR(20) NOT NULL,
  home_address VARCHAR(255) NOT NULL,
  mobile_number VARCHAR(20) NOT NULL,
  webmail VARCHAR(100) NOT NULL,
  campus VARCHAR(100) NOT NULL,
  academic_level VARCHAR(50) NOT NULL,
  college VARCHAR(100) NOT NULL,
  program VARCHAR(100) NOT NULL,
  work_classification VARCHAR(255) NOT NULL,

  -- Work Metadata
  title VARCHAR(255) NOT NULL,
  adviser VARCHAR(100),
  adviser_coauthor TINYINT(1) DEFAULT 0,
  date_accomplished DATE NOT NULL,

  -- Terms
  accepted_terms TINYINT(1) DEFAULT 0,

  -- Status Tracking
  status VARCHAR(50) NOT NULL DEFAULT 'pending', -- pending, under_review, approved, rejected
  status_updated_at DATETIME,
  remarks TEXT,

  -- Version Control
  version INT DEFAULT 1,
  is_latest TINYINT(1) DEFAULT 1,

  -- Submission Type
  submission_type VARCHAR(50) NOT NULL DEFAULT 'copyright',

  -- Audit
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Helpful Indexes
CREATE INDEX idx_user_status ON submissions(user_id, status);
CREATE INDEX idx_student_number ON submissions(student_number);
CREATE INDEX idx_submission_date ON submissions(created_at);



CREATE TABLE submission_authors (
  author_id INT AUTO_INCREMENT PRIMARY KEY,
  submission_id INT NOT NULL,
  first_name VARCHAR(50) NOT NULL,
  middle_name VARCHAR(50),
  last_name VARCHAR(50) NOT NULL,
  student_id VARCHAR(20),
  mobile VARCHAR(20),
  home_address VARCHAR(255),
  webmail VARCHAR(100),
  role VARCHAR(50) DEFAULT 'Author', -- e.g. Author, Adviser
  is_adviser TINYINT(1) DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

  FOREIGN KEY (submission_id) REFERENCES submissions(submission_id) ON DELETE CASCADE
);

CREATE INDEX idx_submission_author ON submission_authors(submission_id);


CREATE TABLE submission_documents (
  document_id INT AUTO_INCREMENT PRIMARY KEY,
  submission_id INT NOT NULL,
  doc_type VARCHAR(100) NOT NULL,   -- e.g. 'journal_publication_format', 'approval_sheet'
  file_path VARCHAR(255) NOT NULL, -- path/filename in /uploads
  uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,

  FOREIGN KEY (submission_id) REFERENCES submissions(submission_id) ON DELETE CASCADE
);

ALTER TABLE submission_documents
ADD COLUMN file_size BIGINT NOT NULL,
ADD COLUMN mime_type VARCHAR(100) NOT NULL,
ADD COLUMN verified TINYINT(1) DEFAULT 0,
ADD COLUMN verified_by INT,
ADD COLUMN verified_at DATETIME DEFAULT NULL,
ADD FOREIGN KEY (verified_by) REFERENCES users(user_id);

CREATE INDEX idx_submission_doc ON submission_documents(submission_id, doc_type);

ALTER TABLE submissions 
MODIFY COLUMN status ENUM(
    'draft',
    'pending_review',
    'under_review', 
    'revision_needed',
    'approved',
    'rejected'
) DEFAULT 'draft',
ADD COLUMN reviewer_id INT,
ADD COLUMN reviewed_at DATETIME DEFAULT NULL,
ADD FOREIGN KEY (reviewer_id) REFERENCES users(user_id);

ALTER TABLE submissions
MODIFY COLUMN work_classification ENUM(
    '(a) Books, Pamphlets, articles and other writings',
    '(b) Periodicals and newspaper',
    '(c) Lectures, sermons, addresses, dissertations for oral delivery',
    '(d) Letters',
    '(e) Dramatic or dramatic-musical compositions; choreographic works',
    '(f) Musical compositions with or without words',
    '(g) Works of drawing, painting, architecture, sculpture, engraving, lithography',
    '(h) Original ornamental designs or models for articles of manufacture',
    '(i) Illustrations maps, plans, sketches, charts and three-dimensional works',
    '(j) Drawings or plastic works of a scientific or technical character',
    '(k) Photographic works including works produced by a process analogous to photography',
    '(l) Audiovisual works and cinematographic works',
    '(m) Pictorial illustrations and advertisements',
    '(n) Computer Programs',
    '(o) Other literary, scholarly, scientific and artistic works',
    '(p) Sound recordings',
    '(q) Broadcast recordings'
) NOT NULL;

-- Update student_profiles table
ALTER TABLE student_profiles
MODIFY COLUMN campus VARCHAR(255) NOT NULL,
MODIFY COLUMN college VARCHAR(255) NOT NULL,
MODIFY COLUMN department VARCHAR(255) NOT NULL,
MODIFY COLUMN program VARCHAR(255) NOT NULL;

-- Update submissions table
ALTER TABLE submissions
MODIFY COLUMN campus VARCHAR(255) NOT NULL,
MODIFY COLUMN academic_level ENUM(
    'Undergraduate',
    'Masters',
    'Doctorate',
    'Open University'
) NOT NULL,
MODIFY COLUMN college VARCHAR(255) NOT NULL,
MODIFY COLUMN program VARCHAR(255) NOT NULL;


