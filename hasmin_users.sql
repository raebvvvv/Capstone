-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 06, 2025 at 09:00 AM
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
-- Table structure for table `rental_requests`
--
CREATE TABLE `rental_requests` (
  `request_id` int(11) NOT NULL,
  `student_id` varchar(255) NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `course_section` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `professor` varchar(255) NOT NULL,
  `user_classification` varchar(255) NOT NULL,
  `borrowing_date` date NOT NULL,
  `borrowing_time` time NOT NULL,
  `returning_date` date NOT NULL,
  `tools_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`tools_data`)),
  `request_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Pending', 'Approved', 'Completed', 'Denied - Out of Stock') DEFAULT 'Pending',
  `approved_timestamp` timestamp NULL DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `completed_timestamp` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `rental_requests`
INSERT INTO `rental_requests` (`request_id`, `student_id`, `student_name`, `course_section`, `subject`, `professor`, `user_classification`, `borrowing_date`, `borrowing_time`, `returning_date`, `tools_data`, `request_timestamp`, `status`, `approved_timestamp`, `remark`, `completed_timestamp`) VALUES
(1, 'a', 'a', 'bist 3-2', 'a', 'dela rosa buhay', 'Non-HM Student', '2025-02-05', '18:51:00', '2025-02-14', '[{\"category\":\"Glassware\",\"name\":\"Beer Mug\",\"quantity\":1,\"img\":\"tools\\/beer-mug.png\"},{\"category\":\"Servingware\",\"name\":\"Bar Tray\",\"quantity\":1,\"img\":\"tools\\/bar-tray.png\"},{\"category\":\"Silverware\",\"name\":\"Bar Spoon\",\"quantity\":1,\"img\":\"tools\\/bar-spoon.png\"}]', '2025-02-05 02:51:46', 'Completed', '2025-02-05 11:41:07', NULL, '2025-02-06 06:56:32'),
(2, 'a', 'a', 'bist 3-2', 'Hello its me', 'di ko alam', 'HM Student', '2025-02-05', '19:41:00', '2025-02-07', '[{\"category\":\"Servingware\",\"name\":\"Bar Tray\",\"quantity\":1,\"img\":\"tools\\/bar-tray.png\"}]', '2025-02-05 03:42:00', 'Completed', '2025-02-05 11:51:26', NULL, '2025-02-06 06:56:32'),
(3, 'a', 'a', 'BSHM 1-1', 'pe', 'dela rosa buhay', 'HM Student', '2025-02-05', '06:00:00', '2025-02-06', '[{\"category\":\"Glassware\",\"name\":\"Beer Mug\",\"quantity\":1,\"img\":\"tools\\/beer-mug.png\"}]', '2025-02-05 05:57:47', 'Completed', '2025-02-05 14:00:49', 'Missing', '2025-02-06 06:56:32'),
(4, 'a', 'a', 'BSIT 3-2', 'P.E', 'canlas', 'Non-HM Student', '2025-02-07', '14:51:00', '2025-02-12', '[{\"category\":\"Baking Tools\",\"name\":\"Ceramic Ramekin\",\"quantity\":1,\"img\":\"tools\\/ceramic-ramekin.png\"},{\"category\":\"Glassware\",\"name\":\"Champagne\",\"quantity\":1,\"img\":\"tools\\/champagne.png\"},{\"category\":\"Silverware\",\"name\":\"Cake Slicer\",\"quantity\":1,\"img\":\"tools\\/cake-slicer.png\"},{\"category\":\"Glassware\",\"name\":\"Goblet\",\"quantity\":1,\"img\":\"tools\\/goblet.png\"}]', '2025-02-05 22:52:08', 'Completed', '2025-02-06 06:52:43', NULL, '2025-02-06 06:52:48'),
(5, 'a', 'a', 'HAHA - 123', 'haah cute', 'ikaw lang', 'PUP Employee', '2025-02-07', '14:58:00', '2025-02-21', '[{\"category\":\"Glassware\",\"name\":\"Beer Mug\",\"quantity\":1,\"img\":\"tools\\/beer-mug.png\"},{\"category\":\"Servingware\",\"name\":\"Bar Tray\",\"quantity\":1,\"img\":\"tools\\/bar-tray.png\"},{\"category\":\"Bar Tools\",\"name\":\"Ice Scoop\",\"quantity\":1,\"img\":\"tools\\/ice-scoop.png\"},{\"category\":\"Tableware\",\"name\":\"Gravy Boat and Saucer\",\"quantity\":1,\"img\":\"tools\\/gravy-boat-saucer.png\"}]', '2025-02-05 22:58:12', 'Completed', '2025-02-06 06:58:44', NULL, '2025-02-06 07:07:41'),
(6, 'a', 'a', 'HAHA - 123', 'haah cute', 'ikaw lang', 'HM Student', '2025-02-07', '15:22:00', '2025-02-15', '[{\"category\":\"Glassware\",\"name\":\"Beer Mug\",\"quantity\":3,\"img\":\"tools\\/beer-mug.png\"}]', '2025-02-05 23:22:42', 'Completed', '2025-02-06 07:24:56', 'Missing', '2025-02-06 07:31:11'),
(7, 'a', 'a', 'BSIT 3-2', 'haah cute', 'ikaw lang', 'HM Student', '2025-02-07', '15:29:00', '2025-02-14', '[{\"category\":\"Servingware\",\"name\":\"Bar Tray\",\"quantity\":1,\"img\":\"tools\\/bar-tray.png\"}]', '2025-02-05 23:29:07', 'Completed', '2025-02-06 07:29:47', 'Broken', '2025-02-06 07:31:23'),
(8, 'a', 'a', 'BSIT 3-2', 'haah cute', 'ikaw lang', 'HM Student', '2025-02-07', '15:46:00', '2025-02-08', '[{\"category\":\"Servingware\",\"name\":\"Bar Tray\",\"quantity\":1,\"img\":\"tools\\/bar-tray.png\"}]', '2025-02-05 23:46:26', 'Completed', '2025-02-06 07:50:06', 'Late', '2025-02-06 07:50:41');

-- --------------------------------------------------------

--
-- Table structure for table `tools`
--

CREATE TABLE `tools` (
  `tool_id` int(11) NOT NULL,
  `tool_name` varchar(255) NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `status` enum('available','out_of_stock') DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tools`
--

INSERT INTO `tools` (`tool_id`, `tool_name`, `category`, `stock_quantity`, `image_url`, `status`) VALUES
(1, 'Banana Split Plate', 'Glassware', 30, 'tools/banana-split-plate.png', 'available'),
(2, 'Bar Spoon', 'Silverware', 50, 'tools/bar-spoon.png', 'available'),
(3, 'Bar Tray', 'Servingware', 20, 'tools/bar-tray.png', 'available'),
(4, 'Beer Mug', 'Glassware', 20, 'tools/beer-mug.png', 'available'),
(5, 'Bread Knife', 'Silverware', 15, 'tools/bread-knife.png', 'available'),
(6, 'Butcher Knife', 'Silverware', 2, 'tools/butcher-knife.png', 'available'),
(7, 'Butter Knife', 'Silverware', 50, 'tools/butter-knife.png', 'available'),
(8, 'Cake Slicer', 'Silverware', 2, 'tools/cake-slicer.png', 'available'),
(9, 'Ceramic Ramekin', 'Baking Tools', 50, 'tools/ceramic-ramekin.png', 'available'),
(10, 'Champagne', 'Glassware', 2, 'tools/champagne.png', 'available'),
(11, 'Glass Pitcher', 'Glassware', 50, 'tools/glass-pitcher.png', 'available'),
(12, 'Goblet', 'Glassware', 50, 'tools/goblet.png', 'available'),
(13, 'Gravy Boat and Saucer', 'Tableware', 50, 'tools/gravy-boat-saucer.png', 'available'),
(14, 'Highball Glass', 'Glassware', 50, 'tools/high-ball.png', 'available'),
(15, 'Ice Scoop', 'Bar Tools', 50, 'tools/ice-scoop.png', 'available'),
(16, 'Jigger', 'Bar Tools', 50, 'tools/jigger.png', 'available'),
(17, 'Large Shaker', 'Bar Tools', 50, 'tools/large-shaker.png', 'available'),
(18, 'Lasagna Plate', 'Tableware', 50, 'tools/lasagna-plate.png', 'available'),
(19, 'Mami Bowl', 'Tableware', 50, 'tools/mami-bowl.png', 'available'),
(20, 'Measuring Spoons', 'Kitchenware', 50, 'tools/measuring-spoons.png', 'available'),
(21, 'Parfait Glass', 'Glassware', 50, 'tools/parfait.png', 'available'),
(22, 'Pina Colada Glass', 'Glassware', 50, 'tools/pina-colada.png', 'available'),
(23, 'Puto Molder', 'Baking Tools', 50, 'tools/puto-molder.png', 'available'),
(24, 'Red Wine Glass', 'Glassware', 50, 'tools/red-wine.png', 'available'),
(25, 'Rice Cooker', 'Kitchenware', 50, 'tools/rice-cooker.png', 'available'),
(26, 'Round Baking Pan', 'Baking Tools', 50, 'tools/round-baking-pans.png', 'available'),
(27, 'Salad Fork', 'Silverware', 50, 'tools/salad-fork.png', 'available'),
(28, 'Salt and Pepper Shaker', 'Tableware', 50, 'tools/salt-pepper-shaker.png', 'available'),
(29, 'Sauce Dish', 'Tableware', 50, 'tools/sauce-dish.png', 'available'),
(30, 'Sauce Pourer', 'Glassware', 50, 'tools/sauce-glass.png', 'available'),
(31, 'Saucer', 'Tableware', 50, 'tools/saucer.png', 'available'),
(32, 'Serving Fork', 'Silverware', 50, 'tools/serving-fork.png', 'available'),
(33, 'Serving Spoon', 'Silverware', 50, 'tools/serving-spoon.png', 'available'),
(34, 'Serving Tray', 'Servingware', 50, 'tools/serving-tray.png', 'available'),
(35, 'Silicon Spatula', 'Kitchenware', 50, 'tools/silicon-spatula.png', 'available'),
(36, 'Small Shaker', 'Bar Tools', 50, 'tools/small-shaker.png', 'available'),
(37, 'Soda Glass', 'Glassware', 50, 'tools/soda.png', 'available'),
(38, 'Soup Bowl', 'Tableware', 50, 'tools/soup-bowl.png', 'available'),
(39, 'Stainless Baking Pan', 'Baking Tools', 50, 'tools/stainless-baking-pan.png', 'available'),
(40, 'Stainless Steel Bowl', 'Baking Tools', 50, 'tools/stainless-bowl.png', 'available'),
(41, 'Stainless Steel Kettle', 'Kitchenware', 50, 'tools/stainless-kettle.png', 'available'),
(42, 'Stainless Steel Ladle', 'Kitchenware', 50, 'tools/stainless-ladle.png', 'available'),
(43, 'Stainless Steel Sianse', 'Kitchenware', 50, 'tools/stainless-sianse.png', 'available'),
(44, 'Stainless Steel Tong', 'Kitchenware', 50, 'tools/stainless-tong.png', 'available'),
(45, 'Strainer', 'Kitchenware', 50, 'tools/strainer.png', 'available'),
(46, 'Turn Table', 'Tableware', 50, 'tools/turn-table.png', 'available'),
(47, 'White Wine Glass', 'Glassware', 50, 'tools/white-wine.png', 'available'),
(48, 'Wooden Bowl', 'Tableware', 50, 'tools/wooden-bowl.png', 'available'),
(49, 'Wooden Plate', 'Tableware', 50, 'tools/wooden-plate.png', 'available');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `student_number` varchar(20) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_admin` tinyint(1) DEFAULT 0,
  `status` enum('pending','approved') DEFAULT 'pending',
  `COR` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `student_number`, `username`, `email`, `password`, `created_at`, `is_admin`, `status`, `COR`) VALUES
(22, 'admin', 'admin', 'admin@gmail.com', '$2y$10$USBc9Q6u0kiOR38A89yplONwdCrSQj2mUb58P201GbSTjZWPAQ/Ju', '2025-02-03 17:33:11', 1, '', ''),
(24, '2022-08680-MN-0', 'raebv', 'inocentesraebv@gmail.com', '$2y$10$VwAh.04kLlRQ5whpjq/Ay.dC6GFf1ePE1qxlyLbI39FK4Wx4Ol4LO', '2025-02-03 19:10:41', 0, 'approved', ''),
(25, 'a', 'a', 'a@gmail.com', '$2y$10$lKfJfjs7wnLq08DwFgkxZOBl2KD453NpdpKeBJjR3GgLknIWlExVa', '2025-02-03 19:13:09', 0, 'approved', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `rental_requests`
--
ALTER TABLE `rental_requests`
  ADD PRIMARY KEY (`request_id`);

--
-- Indexes for table `tools`
--
ALTER TABLE `tools`
  ADD PRIMARY KEY (`tool_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `rental_requests`
--
ALTER TABLE `rental_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tools`
--
ALTER TABLE `tools`
  MODIFY `tool_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
