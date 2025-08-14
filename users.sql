-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 04, 2025 at 05:14 PM
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
-- Database: `hasmin_users`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_number` varchar(20) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_admin` tinyint(1) DEFAULT 0,
  `status` enum('pending','approved') DEFAULT 'pending',
  `COR` varchar(255) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `student_number`, `username`, `email`, `password`, `created_at`, `is_admin`, `status`, `COR`) VALUES
(22, 'admin', 'admin', 'admin@gmail.com', '$2y$10$USBc9Q6u0kiOR38A89yplONwdCrSQj2mUb58P201GbSTjZWPAQ/Ju', '2025-02-03 17:33:11', 1, '', ''),
(24, '2022-08680-MN-0', 'raebv', 'inocentesraebv@gmail.com', '$2y$10$VwAh.04kLlRQ5whpjq/Ay.dC6GFf1ePE1qxlyLbI39FK4Wx4Ol4LO', '2025-02-03 19:10:41', 0, 'approved', ''),
(25, 'a', 'a', 'a@gmail.com', '$2y$10$lKfJfjs7wnLq08DwFgkxZOBl2KD453NpdpKeBJjR3GgLknIWlExVa', '2025-02-03 19:13:09', 0, 'approved', '');

--
-- Fetch the number of active users
SELECT COUNT(*) as active_users_count FROM users WHERE status = 'approved';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
