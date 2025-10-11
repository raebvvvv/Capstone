-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 11, 2025 at 04:27 PM
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
  `is_read` tinyint(1) DEFAULT 0,
  `notification_type` varchar(50) NOT NULL DEFAULT 'resubmission',
  `occurrence_count` int(11) NOT NULL DEFAULT 1,
  `meta` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_notifications`
--

INSERT INTO `admin_notifications` (`id`, `submission_id`, `submission_code`, `user_id`, `doc_type`, `message`, `created_at`, `is_read`, `notification_type`, `occurrence_count`, `meta`) VALUES
(64, 53, 'SRID-2025-20251006-1', 1, 'approval_sheet', 'User #1 re-uploaded approval sheet (SRID-2025-20251006-1)', '2025-10-06 12:25:42', 1, 'resubmission', 1, '[\"approval_sheet\"]'),
(65, 53, 'SRID-2025-20251006-1', 1, 'approval_sheet', 'User #1 re-uploaded approval sheet (SRID-2025-20251006-1)', '2025-10-06 12:26:53', 1, 'resubmission', 1, '[\"approval_sheet\"]'),
(66, 53, 'SRID-2025-20251006-1', 1, 'full_manuscript', 'User #1 re-uploaded full manuscript (SRID-2025-20251006-1)', '2025-10-06 12:27:59', 1, 'resubmission', 1, '[\"full_manuscript\"]'),
(67, 61, 'SRID-2025-20251007-1', 1, 'approval_sheet', 'User #1 re-uploaded approval sheet (SRID-2025-20251007-1)', '2025-10-06 16:33:03', 1, 'resubmission', 1, '[\"approval_sheet\"]'),
(68, 61, 'SRID-2025-20251007-1', 1, 'full_manuscript', 'User #1 re-uploaded full manuscript (SRID-2025-20251007-1)', '2025-10-06 16:49:53', 1, 'resubmission', 1, '[\"full_manuscript\"]'),
(69, 63, 'SRID-2025-20251007-3', 1, 'approval_sheet', 'User #1 re-uploaded approval sheet (SRID-2025-20251007-3)', '2025-10-06 16:51:45', 1, 'resubmission', 1, '[\"approval_sheet\"]'),
(70, 63, 'SRID-2025-20251007-3', 1, 'approval_sheet', 'User #1 re-uploaded approval sheet (SRID-2025-20251007-3)', '2025-10-06 16:56:00', 1, 'resubmission', 1, '[\"approval_sheet\"]'),
(71, 63, 'SRID-2025-20251007-3', 1, 'approval_sheet', 'User #1 re-uploaded approval sheet (SRID-2025-20251007-3)', '2025-10-06 16:56:17', 1, 'resubmission', 1, '[\"approval_sheet\"]'),
(72, 63, 'SRID-2025-20251007-3', 1, 'approval_sheet', 'User #1 re-uploaded approval sheet (SRID-2025-20251007-3)', '2025-10-06 17:02:58', 1, 'resubmission', 1, '[\"approval_sheet\"]'),
(73, 69, 'ERID-2025-20251008-1', 2, 'journal_publication_format', 'User #2 re-uploaded journal publication format (ERID-2025-20251008-1)', '2025-10-07 17:02:06', 1, 'resubmission', 1, '[\"journal_publication_format\"]'),
(74, 69, 'ERID-2025-20251008-1', 2, 'journal_publication_format', 'User #2 re-uploaded journal publication format (ERID-2025-20251008-1)', '2025-10-07 17:03:36', 1, 'resubmission', 1, '[\"journal_publication_format\"]'),
(75, 69, 'ERID-2025-20251008-1', 2, 'notarized_coauthorship', 'User #2 re-uploaded notarized coauthorship (ERID-2025-20251008-1)', '2025-10-07 17:03:36', 1, 'resubmission', 1, '[\"notarized_coauthorship\"]'),
(76, 69, 'ERID-2025-20251008-1', 2, 'notarized_copyright', 'User #2 re-uploaded notarized copyright (ERID-2025-20251008-1)', '2025-10-07 17:03:36', 1, 'resubmission', 1, '[\"notarized_copyright\"]'),
(77, 69, 'ERID-2025-20251008-1', 2, 'receipt_payment', 'User #2 re-uploaded receipt payment (ERID-2025-20251008-1)', '2025-10-07 17:03:37', 1, 'resubmission', 1, '[\"receipt_payment\"]'),
(78, 69, 'ERID-2025-20251008-1', 2, 'record_copyright', 'User #2 re-uploaded record copyright (ERID-2025-20251008-1)', '2025-10-07 17:03:37', 1, 'resubmission', 1, '[\"record_copyright\"]'),
(79, 69, 'ERID-2025-20251008-1', 2, 'journal_publication_format', 'User #2 re-uploaded journal publication format (ERID-2025-20251008-1)', '2025-10-07 17:06:03', 1, 'resubmission', 1, '[\"journal_publication_format\"]'),
(80, 69, 'ERID-2025-20251008-1', 2, 'notarized_coauthorship', 'User #2 re-uploaded notarized coauthorship (ERID-2025-20251008-1)', '2025-10-07 17:06:03', 1, 'resubmission', 1, '[\"notarized_coauthorship\"]'),
(81, 69, 'ERID-2025-20251008-1', 2, 'notarized_copyright', 'User #2 re-uploaded notarized copyright (ERID-2025-20251008-1)', '2025-10-07 17:06:03', 1, 'resubmission', 1, '[\"notarized_copyright\"]'),
(82, 69, 'ERID-2025-20251008-1', 2, 'receipt_payment', 'User #2 re-uploaded receipt payment (ERID-2025-20251008-1)', '2025-10-07 17:06:03', 1, 'resubmission', 1, '[\"receipt_payment\"]'),
(83, 69, 'ERID-2025-20251008-1', 2, 'record_copyright', 'User #2 re-uploaded record copyright (ERID-2025-20251008-1)', '2025-10-07 17:06:03', 1, 'resubmission', 1, '[\"record_copyright\"]'),
(84, 74, 'ERID-2025-20251008-6', 2, 'presentation', 'User #2 re-uploaded presentation (ERID-2025-20251008-6)', '2025-10-08 04:49:38', 1, 'resubmission', 1, '[\"presentation\"]'),
(85, 75, 'ERID-2025-20251008-7', 2, 'presentation', 'User #2 re-uploaded presentation (ERID-2025-20251008-7)', '2025-10-08 05:05:25', 1, 'resubmission', 1, '[\"presentation\"]'),
(86, 80, 'ERID-2025-20251008-12', 2, 'journal_publication_format', 'User #2 re-uploaded journal publication format (ERID-2025-20251008-12)', '2025-10-08 05:14:49', 1, 'resubmission', 1, '[\"journal_publication_format\"]'),
(87, 80, 'ERID-2025-20251008-12', 2, 'notarized_coauthorship', 'User #2 re-uploaded notarized coauthorship (ERID-2025-20251008-12)', '2025-10-08 05:14:49', 1, 'resubmission', 1, '[\"notarized_coauthorship\"]'),
(88, 80, 'ERID-2025-20251008-12', 2, 'notarized_copyright', 'User #2 re-uploaded notarized copyright (ERID-2025-20251008-12)', '2025-10-08 05:14:49', 1, 'resubmission', 1, '[\"notarized_copyright\"]'),
(89, 80, 'ERID-2025-20251008-12', 2, 'presentation', 'User #2 re-uploaded presentation (ERID-2025-20251008-12)', '2025-10-08 05:14:49', 1, 'resubmission', 1, '[\"presentation\"]'),
(90, 80, 'ERID-2025-20251008-12', 2, 'receipt_payment', 'User #2 re-uploaded receipt payment (ERID-2025-20251008-12)', '2025-10-08 05:14:49', 1, 'resubmission', 1, '[\"receipt_payment\"]'),
(91, 80, 'ERID-2025-20251008-12', 2, 'record_copyright', 'User #2 re-uploaded record copyright (ERID-2025-20251008-12)', '2025-10-08 05:14:49', 1, 'resubmission', 1, '[\"record_copyright\"]'),
(92, 85, 'ERID-2025-20251008-17', 2, 'journal_publication_format', 'User #2 re-uploaded journal publication format (ERID-2025-20251008-17)', '2025-10-08 05:42:17', 1, 'resubmission', 1, '[\"journal_publication_format\"]'),
(93, 85, 'ERID-2025-20251008-17', 2, 'notarized_coauthorship', 'User #2 re-uploaded notarized coauthorship (ERID-2025-20251008-17)', '2025-10-08 05:42:17', 1, 'resubmission', 1, '[\"notarized_coauthorship\"]'),
(94, 85, 'ERID-2025-20251008-17', 2, 'notarized_copyright', 'User #2 re-uploaded notarized copyright (ERID-2025-20251008-17)', '2025-10-08 05:42:17', 1, 'resubmission', 1, '[\"notarized_copyright\"]'),
(95, 85, 'ERID-2025-20251008-17', 2, 'presentation', 'User #2 re-uploaded presentation (ERID-2025-20251008-17)', '2025-10-08 05:42:17', 1, 'resubmission', 1, '[\"presentation\"]'),
(96, 85, 'ERID-2025-20251008-17', 2, 'receipt_payment', 'User #2 re-uploaded receipt payment (ERID-2025-20251008-17)', '2025-10-08 05:42:17', 1, 'resubmission', 1, '[\"receipt_payment\"]'),
(97, 85, 'ERID-2025-20251008-17', 2, 'record_copyright', 'User #2 re-uploaded record copyright (ERID-2025-20251008-17)', '2025-10-08 05:42:17', 1, 'resubmission', 1, '[\"record_copyright\"]'),
(98, 91, 'ERID-2025-20251008-23', 6, 'journal_publication_format', 'User #6 re-uploaded journal publication format (ERID-2025-20251008-23)', '2025-10-08 07:43:09', 1, 'resubmission', 1, '[\"journal_publication_format\"]'),
(99, 91, 'ERID-2025-20251008-23', 6, 'notarized_coauthorship', 'User #6 re-uploaded notarized coauthorship (ERID-2025-20251008-23)', '2025-10-08 07:43:09', 1, 'resubmission', 1, '[\"notarized_coauthorship\"]'),
(100, 91, 'ERID-2025-20251008-23', 6, 'notarized_copyright', 'User #6 re-uploaded notarized copyright (ERID-2025-20251008-23)', '2025-10-08 07:43:09', 1, 'resubmission', 1, '[\"notarized_copyright\"]'),
(101, 91, 'ERID-2025-20251008-23', 6, 'presentation', 'User #6 re-uploaded presentation (ERID-2025-20251008-23)', '2025-10-08 07:43:09', 1, 'resubmission', 1, '[\"presentation\"]'),
(102, 91, 'ERID-2025-20251008-23', 6, 'receipt_payment', 'User #6 re-uploaded receipt payment (ERID-2025-20251008-23)', '2025-10-08 07:43:09', 1, 'resubmission', 1, '[\"receipt_payment\"]'),
(103, 91, 'ERID-2025-20251008-23', 6, 'record_copyright', 'User #6 re-uploaded record copyright (ERID-2025-20251008-23)', '2025-10-08 07:43:09', 1, 'resubmission', 1, '[\"record_copyright\"]'),
(104, 88, 'SRID-2025-20251008-20', 1, 'approval_sheet', 'User #1 re-uploaded approval sheet (SRID-2025-20251008-20)', '2025-10-08 07:44:26', 1, 'resubmission', 1, '[\"approval_sheet\"]'),
(105, 90, 'SRID-2025-20251008-22', 1, 'approval_sheet', 'User #1 re-uploaded approval sheet (SRID-2025-20251008-22)', '2025-10-08 07:44:38', 1, 'resubmission', 1, '[\"approval_sheet\"]'),
(106, 92, 'SRID-2025-20251008-24', 1, 'approval_sheet', 'User #1 re-uploaded approval sheet (SRID-2025-20251008-24)', '2025-10-08 07:51:00', 1, 'resubmission', 1, '[\"approval_sheet\"]'),
(107, 94, 'SRID-2025-20251008-26', 1, 'approval_sheet', 'User #1 re-uploaded approval sheet (SRID-2025-20251008-26)', '2025-10-08 07:56:08', 1, 'resubmission', 1, '[\"approval_sheet\"]'),
(108, 94, 'SRID-2025-20251008-26', 1, 'approval_sheet', 'User #1 re-uploaded approval sheet (SRID-2025-20251008-26)', '2025-10-08 08:00:56', 1, 'resubmission', 1, '[\"approval_sheet\"]'),
(109, 102, 'ERID-2025-20251009-5', 6, 'journal_publication_format', 'User #6 re-uploaded journal publication format (ERID-2025-20251009-5)', '2025-10-09 12:46:23', 1, 'resubmission', 1, '[\"journal_publication_format\"]'),
(110, 102, 'ERID-2025-20251009-5', 6, 'notarized_coauthorship', 'User #6 re-uploaded notarized coauthorship (ERID-2025-20251009-5)', '2025-10-09 12:46:23', 1, 'resubmission', 1, '[\"notarized_coauthorship\"]'),
(111, 102, 'ERID-2025-20251009-5', 6, 'notarized_copyright', 'User #6 re-uploaded notarized copyright (ERID-2025-20251009-5)', '2025-10-09 12:46:23', 1, 'resubmission', 1, '[\"notarized_copyright\"]'),
(112, 102, 'ERID-2025-20251009-5', 6, 'presentation', 'User #6 re-uploaded presentation (ERID-2025-20251009-5)', '2025-10-09 12:46:23', 1, 'resubmission', 1, '[\"presentation\"]'),
(113, 102, 'ERID-2025-20251009-5', 6, 'receipt_payment', 'User #6 re-uploaded receipt payment (ERID-2025-20251009-5)', '2025-10-09 12:46:23', 1, 'resubmission', 1, '[\"receipt_payment\"]'),
(114, 102, 'ERID-2025-20251009-5', 6, 'record_copyright', 'User #6 re-uploaded record copyright (ERID-2025-20251009-5)', '2025-10-09 12:46:23', 1, 'resubmission', 1, '[\"record_copyright\"]'),
(115, 106, 'ERID-2025-20251009-9', 6, 'journal_publication_format', 'User #6 re-uploaded journal publication format (ERID-2025-20251009-9)', '2025-10-09 13:24:50', 1, 'resubmission', 1, '[\"journal_publication_format\"]'),
(116, 122, 'SRID-2025-20251010-3', 1, 'approval_sheet', 'User #1 re-uploaded approval sheet (SRID-2025-20251010-3)', '2025-10-10 07:15:22', 1, 'resubmission', 1, '[\"approval_sheet\"]'),
(117, 123, 'SRID-2025-20251010-4', 1, 'approval_sheet', 'User #1 re-uploaded approval sheet (SRID-2025-20251010-4)', '2025-10-10 09:47:50', 1, 'resubmission', 1, '[\"approval_sheet\"]'),
(118, 123, 'SRID-2025-20251010-4', 1, 'approval_sheet', 'User #1 re-uploaded approval sheet (SRID-2025-20251010-4)', '2025-10-10 09:55:07', 1, 'resubmission', 1, '[\"approval_sheet\"]'),
(119, 127, 'SRID-2025-20251010-8', 1, 'record_copyright', 'User #1 resubmitted 7 documents for SRID-2025-20251010-8', '2025-10-10 23:25:38', 1, 'resubmission', 7, '[\"approval_sheet\",\"full_manuscript\",\"journal_publication_format\",\"notarized_coauthorship\",\"notarized_copyright\",\"receipt_payment\",\"record_copyright\"]'),
(120, 120, 'SRID-2025-20251010-1', 1, 'notarized_coauthorship', 'Resubmission: 9 uploads for SRID-2025-20251010-1', '2025-10-10 23:49:41', 1, 'resubmission', 9, '[\"approval_sheet\",\"approval_sheet\",\"full_manuscript\",\"journal_publication_format\",\"notarized_coauthorship\",\"approval_sheet\",\"full_manuscript\",\"journal_publication_format\",\"notarized_coauthorship\"]'),
(121, 133, 'SRID-2025-20251011-4', 1, 'journal_publication_format', 'Resubmission: 3 uploads for SRID-2025-20251011-4', '2025-10-11 00:07:43', 1, 'resubmission', 3, '[\"approval_sheet\",\"full_manuscript\",\"journal_publication_format\"]'),
(122, 133, 'SRID-2025-20251011-4', 1, 'full_manuscript', 'Resubmission: 2 uploads for SRID-2025-20251011-4', '2025-10-11 01:07:55', 1, 'resubmission', 2, '[\"approval_sheet\",\"full_manuscript\"]'),
(123, 133, 'SRID-2025-20251011-4', 1, 'approval_sheet', 'User #1 resubmitted 3 documents for SRID-2025-20251011-4', '2025-10-11 01:51:13', 1, 'resubmission', 3, '[{\"doc_type\":\"approval_sheet\",\"file_name\":\"approval_sheet_1760147473_5d91836d.pdf\",\"file_size\":1831,\"created_at\":\"2025-10-11 09:51:13\",\"user_id\":1},{\"doc_type\":\"full_manuscript\",\"file_name\":\"full_manuscript_1760147473_35836073.pdf\",\"file_size\":1822,\"created_at\":\"2025-10-11 09:51:13\",\"user_id\":1}]'),
(124, 133, 'SRID-2025-20251011-4', 1, 'approval_sheet', 'User #1 resubmitted 2 documents for SRID-2025-20251011-4', '2025-10-11 09:06:13', 1, 'resubmission', 2, '[{\"doc_type\":\"approval_sheet\",\"file_name\":\"approval_sheet_1760173573_a1366a56.pdf\",\"file_size\":1831,\"created_at\":\"2025-10-11 17:06:13\",\"user_id\":1},{\"doc_type\":\"full_manuscript\",\"file_name\":\"full_manuscript_1760173573_30d338f5.pdf\",\"file_size\":1822,\"created_at\":\"2025-10-11 17:06:13\",\"user_id\":1}]'),
(125, 133, 'SRID-2025-20251011-4', 1, 'approval_sheet', 'User #1 resubmitted 4 documents for SRID-2025-20251011-4', '2025-10-11 09:27:42', 1, 'resubmission', 4, '[{\"doc_type\":\"approval_sheet\",\"file_name\":\"approval_sheet_1760174607_8411e8ad.pdf\",\"file_size\":1831,\"created_at\":\"2025-10-11 17:23:27\",\"user_id\":1},{\"doc_type\":\"full_manuscript\",\"file_name\":\"full_manuscript_1760174607_e96f2229.pdf\",\"file_size\":1822,\"created_at\":\"2025-10-11 17:23:27\",\"user_id\":1},{\"doc_type\":\"approval_sheet\",\"file_name\":\"approval_sheet_1760174862_182cb0a5.pdf\",\"file_size\":1831,\"created_at\":\"2025-10-11 17:27:42\",\"user_id\":1},{\"doc_type\":\"full_manuscript\",\"file_name\":\"full_manuscript_1760174862_dff25200.pdf\",\"file_size\":1822,\"created_at\":\"2025-10-11 17:27:42\",\"user_id\":1}]'),
(126, 133, 'SRID-2025-20251011-4', 1, 'approval_sheet', 'User #1 resubmitted 4 documents for SRID-2025-20251011-4', '2025-10-11 10:09:03', 1, 'resubmission', 4, '[{\"doc_type\":\"approval_sheet\",\"file_name\":\"approval_sheet_1760176824_73db9a44.pdf\",\"file_size\":1831,\"created_at\":\"2025-10-11 18:00:24\",\"user_id\":1},{\"doc_type\":\"approval_sheet\",\"file_name\":\"approval_sheet_1760176970_df8a1772.pdf\",\"file_size\":1831,\"created_at\":\"2025-10-11 18:02:50\",\"user_id\":1},{\"doc_type\":\"approval_sheet\",\"file_name\":\"approval_sheet_1760177343_9f446698.pdf\",\"file_size\":1831,\"created_at\":\"2025-10-11 18:09:03\",\"user_id\":1},{\"doc_type\":\"full_manuscript\",\"file_name\":\"full_manuscript_1760177343_c78e36e5.pdf\",\"file_size\":1822,\"created_at\":\"2025-10-11 18:09:03\",\"user_id\":1}]'),
(127, 129, 'SRID-2025-20251010-10', 1, 'approval_sheet', 'User #1 resubmitted 1 document for SRID-2025-20251010-10', '2025-10-11 10:08:44', 1, 'resubmission', 1, '[{\"doc_type\":\"approval_sheet\",\"file_name\":\"approval_sheet_1760177324_8c701cf6.pdf\",\"file_size\":1831,\"created_at\":\"2025-10-11 18:08:44\",\"user_id\":1}]'),
(128, 133, 'SRID-2025-20251011-4', 1, 'receipt_payment', 'User #1 resubmitted 2 documents for SRID-2025-20251011-4', '2025-10-11 10:23:24', 1, 'resubmission', 2, '[{\"doc_type\":\"receipt_payment\",\"file_name\":\"receipt_payment_1760178131_94d23a01.pdf\",\"file_size\":1821,\"created_at\":\"2025-10-11 18:22:11\",\"user_id\":1},{\"doc_type\":\"notarized_coauthorship\",\"file_name\":\"notarized_coauthorship_1760178204_d3fe3dab.pdf\",\"file_size\":1841,\"created_at\":\"2025-10-11 18:23:24\",\"user_id\":1}]'),
(129, 129, 'SRID-2025-20251010-10', 1, 'approval_sheet', 'User #1 resubmitted 1 document for SRID-2025-20251010-10', '2025-10-11 10:25:24', 1, 'resubmission', 1, '[{\"doc_type\":\"approval_sheet\",\"file_name\":\"approval_sheet_1760178324_2b7dba29.pdf\",\"file_size\":1831,\"created_at\":\"2025-10-11 18:25:24\",\"user_id\":1}]'),
(130, 133, 'SRID-2025-20251011-4', 1, 'notarized_copyright', 'User #1 resubmitted 1 document for SRID-2025-20251011-4', '2025-10-11 10:40:44', 1, 'resubmission', 1, '[{\"doc_type\":\"notarized_copyright\",\"file_name\":\"notarized_copyright_1760179244_c163b950.pdf\",\"file_size\":1841,\"created_at\":\"2025-10-11 18:40:44\",\"user_id\":1}]'),
(131, 133, 'SRID-2025-20251011-4', 1, 'approval_sheet', 'User #1 resubmitted 6 documents for SRID-2025-20251011-4', '2025-10-11 11:33:25', 1, 'resubmission', 6, '[{\"doc_type\":\"approval_sheet\",\"file_name\":\"approval_sheet_1760180819_3e10043b.pdf\",\"file_size\":1831,\"created_at\":\"2025-10-11 19:06:59\",\"user_id\":1},{\"doc_type\":\"notarized_coauthorship\",\"file_name\":\"notarized_coauthorship_1760181249_89489ba7.pdf\",\"file_size\":1831,\"created_at\":\"2025-10-11 19:14:09\",\"user_id\":1},{\"doc_type\":\"approval_sheet\",\"file_name\":\"approval_sheet_1760181609_673ae258.pdf\",\"file_size\":1831,\"created_at\":\"2025-10-11 19:20:09\",\"user_id\":1},{\"doc_type\":\"full_manuscript\",\"file_name\":\"full_manuscript_1760181609_feff289d.pdf\",\"file_size\":1822,\"created_at\":\"2025-10-11 19:20:09\",\"user_id\":1},{\"doc_type\":\"approval_sheet\",\"file_name\":\"approval_sheet_1760182041_f12c1cbd.pdf\",\"file_size\":1831,\"created_at\":\"2025-10-11 19:27:21\",\"user_id\":1},{\"doc_type\":\"journal_publication_format\",\"file_name\":\"journal_publication_format_1760182405_bd61025c.pdf\",\"file_size\":1831,\"created_at\":\"2025-10-11 19:33:25\",\"user_id\":1}]'),
(132, 133, 'SRID-2025-20251011-4', 1, 'receipt_payment', 'User #1 resubmitted 1 document for SRID-2025-20251011-4', '2025-10-11 12:20:02', 1, 'resubmission', 1, '[{\"doc_type\":\"receipt_payment\",\"file_name\":\"receipt_payment_1760185202_d35c9dd3.pdf\",\"file_size\":1821,\"created_at\":\"2025-10-11 20:20:02\",\"user_id\":1}]'),
(133, 133, 'SRID-2025-20251011-4', 1, 'full_manuscript', 'User #1 resubmitted 1 document for SRID-2025-20251011-4', '2025-10-11 12:33:12', 1, 'resubmission', 1, '[{\"doc_type\":\"full_manuscript\",\"file_name\":\"full_manuscript_1760185992_51b270fe.pdf\",\"file_size\":1822,\"created_at\":\"2025-10-11 20:33:12\",\"user_id\":1}]'),
(134, 133, 'SRID-2025-20251011-4', 1, 'journal_publication_format', 'User #1 resubmitted 1 document for SRID-2025-20251011-4', '2025-10-11 13:00:47', 1, 'resubmission', 1, '[{\"doc_type\":\"journal_publication_format\",\"file_name\":\"journal_publication_format_1760187647_b3fd7fae.pdf\",\"file_size\":1831,\"created_at\":\"2025-10-11 21:00:47\",\"user_id\":1}]'),
(135, 133, 'SRID-2025-20251011-4', 1, 'approval_sheet', 'User #1 resubmitted 4 documents for SRID-2025-20251011-4', '2025-10-11 13:21:59', 0, 'resubmission', 4, '[{\"doc_type\":\"approval_sheet\",\"file_name\":\"approval_sheet_1760188360_86570608.pdf\",\"file_size\":1831,\"created_at\":\"2025-10-11 21:12:40\",\"user_id\":1},{\"doc_type\":\"full_manuscript\",\"file_name\":\"full_manuscript_1760188360_8ad430d3.pdf\",\"file_size\":1822,\"created_at\":\"2025-10-11 21:12:40\",\"user_id\":1},{\"doc_type\":\"receipt_payment\",\"file_name\":\"receipt_payment_1760188649_0b358f6e.pdf\",\"file_size\":1821,\"created_at\":\"2025-10-11 21:17:29\",\"user_id\":1},{\"doc_type\":\"approval_sheet\",\"file_name\":\"approval_sheet_1760188919_61a1db00.pdf\",\"file_size\":1831,\"created_at\":\"2025-10-11 21:21:59\",\"user_id\":1}]'),
(136, 130, 'SRID-2025-20251011-1', 1, 'approval_sheet', 'User #1 resubmitted 1 document for SRID-2025-20251011-1', '2025-10-11 13:21:51', 0, 'resubmission', 1, '[{\"doc_type\":\"approval_sheet\",\"file_name\":\"approval_sheet_1760188911_fa1523a5.pdf\",\"file_size\":1831,\"created_at\":\"2025-10-11 21:21:51\",\"user_id\":1}]');

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
(35, '', '', '', NULL, NULL, NULL, '2025-10-08 13:44:39'),
(36, 'Janellee', '', 'Sagum', NULL, NULL, NULL, '2025-10-09 01:57:01'),
(37, 'Hatodg', '', '', NULL, NULL, NULL, '2025-10-09 19:33:30'),
(38, 'Dustin', '', '', NULL, NULL, NULL, '2025-10-09 20:44:47'),
(39, 'Aaaa', '', '', NULL, NULL, NULL, '2025-10-09 21:10:59'),
(40, 'Antonio', '', '', NULL, NULL, NULL, '2025-10-09 22:41:02'),
(41, 'Nida', '', '', NULL, NULL, NULL, '2025-10-09 23:23:01'),
(42, 'Adviser', '', '', NULL, NULL, NULL, '2025-10-09 23:32:11'),
(43, 'Why', '', '', NULL, NULL, NULL, '2025-10-09 23:33:17'),
(44, 'Te', '', '', NULL, NULL, NULL, '2025-10-09 23:34:05'),
(45, 'Bang', '', '', NULL, NULL, NULL, '2025-10-09 23:40:06'),
(46, 'Brave', '', '', NULL, NULL, NULL, '2025-10-09 23:45:56'),
(47, 'Janelleee', '', '', NULL, NULL, NULL, '2025-10-10 17:33:42'),
(48, 'Tse', '', '', NULL, NULL, NULL, '2025-10-10 17:39:44'),
(49, 'Ste', '', '', NULL, NULL, NULL, '2025-10-10 20:21:42'),
(50, 'Tes', '', '', NULL, NULL, NULL, '2025-10-10 21:02:49'),
(51, 'Bom', '', '', NULL, NULL, NULL, '2025-10-11 00:00:35'),
(55, 'Tseess', '', '', NULL, NULL, NULL, '2025-10-11 00:22:32'),
(56, 'Tesssdsd', '', '', NULL, NULL, NULL, '2025-10-11 00:27:03');

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
(1, '2025-10-11 18:13:27', 6, 80, 15, 15, 50, 41, 38, 1, '{\"labels\":[\"College of Education (COED)\",\"College of Computer and Information Sciences (CCIS)\",\"College of Social Sciences and Development (CSSD)\",\"College of Human Kinetics (CHK)\",\"College of Accountancy and Finance (CAF)\",\"College of Science (CS)\"],\"values\":[24,21,19,9,1,1]}', '{\"labels\":[\"PUP Main (Sta. Mesa, Manila)\"],\"values\":[80]}', '{\"labels\":[\"(b) Periodicals and newspaper\",\"(a) Books, Pamphlets, articles and other writings\",\"(n) Computer Programs\",\"(p) Sound recordings\",\"(m) Pictorial illustrations and advertisements\",\"(o) Other literary, scholarly, scientific and artistic works\",\"(q) Broadcast recordings\",\"(l) Audiovisual works and cinematographic works\",\"(k) Photographic works including works produced by a process analogous to photography\",\"(c) Lectures, sermons, addresses, dissertations for oral delivery\",\"Others\"],\"values\":[15,13,11,9,8,8,6,5,2,2,1]}');

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
(2, 6, '54321', 'Inocentes', 'Raebv Lielmo', 'A', '', '4334A V. Francisco St. Sta. Mesa, Manilaa', '09156574831', 'PUP Main (Sta. Mesa, Manila)', 'Doctorate', 'College of Computer and Information Sciences (CCIS)', 'Department of Information Technology', 'Bachelor of Science in Computer Science (BSCS)', '2025-10-11 02:17:57'),
(3, 17, '78901', 'Garcia', 'Mark', 'E.', '', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09054052007', 'PUP Main (Sta. Mesa, Manila)', 'Not Studying', 'College of Computer and Information Sciences (CCIS)', 'Department of Information Technology', 'N/A', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `resubmission_audit`
--

CREATE TABLE `resubmission_audit` (
  `id` int(11) NOT NULL,
  `submission_id` int(11) NOT NULL,
  `submission_code` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL,
  `doc_type` varchar(100) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `resubmission_audit`
--

INSERT INTO `resubmission_audit` (`id`, `submission_id`, `submission_code`, `user_id`, `doc_type`, `file_name`, `file_size`, `created_at`) VALUES
(1, 127, 'SRID-2025-20251010-8', 1, 'approval_sheet', 'approval_sheet_1760138738_b27ebd26.pdf', 1831, '2025-10-10 23:25:38'),
(2, 127, 'SRID-2025-20251010-8', 1, 'full_manuscript', 'full_manuscript_1760138738_73d76f1d.pdf', 1822, '2025-10-10 23:25:38'),
(3, 127, 'SRID-2025-20251010-8', 1, 'journal_publication_format', 'journal_publication_format_1760138738_8581021b.pdf', 1831, '2025-10-10 23:25:38'),
(4, 127, 'SRID-2025-20251010-8', 1, 'notarized_coauthorship', 'notarized_coauthorship_1760138738_428dd8ce.pdf', 1831, '2025-10-10 23:25:38'),
(5, 127, 'SRID-2025-20251010-8', 1, 'notarized_copyright', 'notarized_copyright_1760138738_13cc0575.pdf', 1841, '2025-10-10 23:25:38'),
(6, 127, 'SRID-2025-20251010-8', 1, 'receipt_payment', 'receipt_payment_1760138738_6e2fdf03.pdf', 1821, '2025-10-10 23:25:38'),
(7, 127, 'SRID-2025-20251010-8', 1, 'record_copyright', 'record_copyright_1760138738_dd57888e.pdf', 1835, '2025-10-10 23:25:38'),
(8, 120, 'SRID-2025-20251010-1', 1, 'approval_sheet', 'approval_sheet_1760139620_95f34b3b.pdf', 1831, '2025-10-10 23:40:20'),
(9, 120, 'SRID-2025-20251010-1', 1, 'approval_sheet', 'approval_sheet_1760139919_fd34323c.pdf', 1831, '2025-10-10 23:45:19'),
(10, 120, 'SRID-2025-20251010-1', 1, 'full_manuscript', 'full_manuscript_1760139919_dc7b90b4.pdf', 1822, '2025-10-10 23:45:19'),
(11, 120, 'SRID-2025-20251010-1', 1, 'journal_publication_format', 'journal_publication_format_1760139919_c4565410.pdf', 1831, '2025-10-10 23:45:19'),
(12, 120, 'SRID-2025-20251010-1', 1, 'notarized_coauthorship', 'notarized_coauthorship_1760139919_e873effe.pdf', 1831, '2025-10-10 23:45:19'),
(13, 120, 'SRID-2025-20251010-1', 1, 'approval_sheet', 'approval_sheet_1760140181_559b3586.pdf', 1831, '2025-10-10 23:49:41'),
(14, 120, 'SRID-2025-20251010-1', 1, 'full_manuscript', 'full_manuscript_1760140181_13883821.pdf', 1822, '2025-10-10 23:49:41'),
(15, 120, 'SRID-2025-20251010-1', 1, 'journal_publication_format', 'journal_publication_format_1760140181_2897acf1.pdf', 1831, '2025-10-10 23:49:41'),
(16, 120, 'SRID-2025-20251010-1', 1, 'notarized_coauthorship', 'notarized_coauthorship_1760140181_cf7aab20.pdf', 1831, '2025-10-10 23:49:41'),
(17, 133, 'SRID-2025-20251011-4', 1, 'approval_sheet', 'approval_sheet_1760141263_b893be9c.pdf', 1831, '2025-10-11 00:07:43'),
(18, 133, 'SRID-2025-20251011-4', 1, 'full_manuscript', 'full_manuscript_1760141263_23fc5d0f.pdf', 1822, '2025-10-11 00:07:43'),
(19, 133, 'SRID-2025-20251011-4', 1, 'journal_publication_format', 'journal_publication_format_1760141263_702187e9.pdf', 1831, '2025-10-11 00:07:43'),
(20, 133, 'SRID-2025-20251011-4', 1, 'approval_sheet', 'approval_sheet_1760144875_baebb91f.pdf', 1831, '2025-10-11 01:07:55'),
(21, 133, 'SRID-2025-20251011-4', 1, 'full_manuscript', 'full_manuscript_1760144875_cccbd8cd.pdf', 1822, '2025-10-11 01:07:55'),
(22, 133, 'SRID-2025-20251011-4', 1, 'approval_sheet', 'approval_sheet_1760147473_5d91836d.pdf', 1831, '2025-10-11 01:51:13'),
(23, 133, 'SRID-2025-20251011-4', 1, 'full_manuscript', 'full_manuscript_1760147473_35836073.pdf', 1822, '2025-10-11 01:51:13'),
(24, 133, 'SRID-2025-20251011-4', 1, 'approval_sheet', 'approval_sheet_1760173573_a1366a56.pdf', 1831, '2025-10-11 09:06:13'),
(25, 133, 'SRID-2025-20251011-4', 1, 'full_manuscript', 'full_manuscript_1760173573_30d338f5.pdf', 1822, '2025-10-11 09:06:13'),
(26, 133, 'SRID-2025-20251011-4', 1, 'approval_sheet', 'approval_sheet_1760174607_8411e8ad.pdf', 1831, '2025-10-11 09:23:27'),
(27, 133, 'SRID-2025-20251011-4', 1, 'full_manuscript', 'full_manuscript_1760174607_e96f2229.pdf', 1822, '2025-10-11 09:23:27'),
(28, 133, 'SRID-2025-20251011-4', 1, 'approval_sheet', 'approval_sheet_1760174862_182cb0a5.pdf', 1831, '2025-10-11 09:27:42'),
(29, 133, 'SRID-2025-20251011-4', 1, 'full_manuscript', 'full_manuscript_1760174862_dff25200.pdf', 1822, '2025-10-11 09:27:42'),
(30, 133, 'SRID-2025-20251011-4', 1, 'approval_sheet', 'approval_sheet_1760176824_73db9a44.pdf', 1831, '2025-10-11 10:00:24'),
(31, 133, 'SRID-2025-20251011-4', 1, 'approval_sheet', 'approval_sheet_1760176970_df8a1772.pdf', 1831, '2025-10-11 10:02:50'),
(32, 129, 'SRID-2025-20251010-10', 1, 'approval_sheet', 'approval_sheet_1760177324_8c701cf6.pdf', 1831, '2025-10-11 10:08:44'),
(33, 133, 'SRID-2025-20251011-4', 1, 'approval_sheet', 'approval_sheet_1760177343_9f446698.pdf', 1831, '2025-10-11 10:09:03'),
(34, 133, 'SRID-2025-20251011-4', 1, 'full_manuscript', 'full_manuscript_1760177343_c78e36e5.pdf', 1822, '2025-10-11 10:09:03'),
(35, 133, 'SRID-2025-20251011-4', 1, 'receipt_payment', 'receipt_payment_1760178131_94d23a01.pdf', 1821, '2025-10-11 10:22:11'),
(36, 133, 'SRID-2025-20251011-4', 1, 'notarized_coauthorship', 'notarized_coauthorship_1760178204_d3fe3dab.pdf', 1841, '2025-10-11 10:23:24'),
(37, 129, 'SRID-2025-20251010-10', 1, 'approval_sheet', 'approval_sheet_1760178324_2b7dba29.pdf', 1831, '2025-10-11 10:25:24'),
(38, 133, 'SRID-2025-20251011-4', 1, 'notarized_copyright', 'notarized_copyright_1760179244_c163b950.pdf', 1841, '2025-10-11 10:40:44'),
(39, 133, 'SRID-2025-20251011-4', 1, 'approval_sheet', 'approval_sheet_1760180819_3e10043b.pdf', 1831, '2025-10-11 11:06:59'),
(40, 133, 'SRID-2025-20251011-4', 1, 'notarized_coauthorship', 'notarized_coauthorship_1760181249_89489ba7.pdf', 1831, '2025-10-11 11:14:09'),
(41, 133, 'SRID-2025-20251011-4', 1, 'approval_sheet', 'approval_sheet_1760181609_673ae258.pdf', 1831, '2025-10-11 11:20:09'),
(42, 133, 'SRID-2025-20251011-4', 1, 'full_manuscript', 'full_manuscript_1760181609_feff289d.pdf', 1822, '2025-10-11 11:20:09'),
(43, 133, 'SRID-2025-20251011-4', 1, 'approval_sheet', 'approval_sheet_1760182041_f12c1cbd.pdf', 1831, '2025-10-11 11:27:21'),
(44, 133, 'SRID-2025-20251011-4', 1, 'journal_publication_format', 'journal_publication_format_1760182405_bd61025c.pdf', 1831, '2025-10-11 11:33:25'),
(45, 133, 'SRID-2025-20251011-4', 1, 'receipt_payment', 'receipt_payment_1760185202_d35c9dd3.pdf', 1821, '2025-10-11 12:20:02'),
(46, 133, 'SRID-2025-20251011-4', 1, 'full_manuscript', 'full_manuscript_1760185992_51b270fe.pdf', 1822, '2025-10-11 12:33:12'),
(47, 133, 'SRID-2025-20251011-4', 1, 'journal_publication_format', 'journal_publication_format_1760187647_b3fd7fae.pdf', 1831, '2025-10-11 13:00:47'),
(48, 133, 'SRID-2025-20251011-4', 1, 'approval_sheet', 'approval_sheet_1760188360_86570608.pdf', 1831, '2025-10-11 13:12:40'),
(49, 133, 'SRID-2025-20251011-4', 1, 'full_manuscript', 'full_manuscript_1760188360_8ad430d3.pdf', 1822, '2025-10-11 13:12:40'),
(50, 133, 'SRID-2025-20251011-4', 1, 'receipt_payment', 'receipt_payment_1760188649_0b358f6e.pdf', 1821, '2025-10-11 13:17:29'),
(51, 130, 'SRID-2025-20251011-1', 1, 'approval_sheet', 'approval_sheet_1760188911_fa1523a5.pdf', 1831, '2025-10-11 13:21:51'),
(52, 133, 'SRID-2025-20251011-4', 1, 'approval_sheet', 'approval_sheet_1760188919_61a1db00.pdf', 1831, '2025-10-11 13:21:59');

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
(17, 5, '2022-00880-MN-0', 'Inocentes', 'Raebv Lielmo', 'A', '', '4334A V. Francisco St. Sta. Mesa, Manila', '09156574831', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Architecture, Design and the Built Environment (CADBE)', '', 'Bachelor of Science in Interior Design (BSID)', '2025-10-11 00:49:12'),
(18, 7, '2022-08379-MN-0', 'Cruz', 'Juan', 'Dela', '', '4334A V. Francisco St.', '09156574831', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Accountancy and Finance (CAF)', '', 'Bachelor Of Science In Accountancy (bsa)', NULL),
(25, 14, '2022-99999-MN-0', 'Cruz', 'Boa', 'Dela', '', '4334A V. Francisco St.', '09156574831', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Tourism, Hospitality and Transportation Management (CTHTM)', '', 'Please select a college first', '2025-10-11 01:05:00');

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
(53, 'SRID-2025-20251006-1', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Accountancy and Finance (CAF)', 'Bachelor of Science in Accountancy (BSA)', '(a) Books, Pamphlets, articles and other writings', 'Mindfulness on the Night Shift: A Longitudinal Study on the Impacts of Meditation on Nurse Productivity and Well-being', '2025-10-06', 1, 'completed', '2025-10-06 20:32:47', 'test', 1, 1, 'copyright', '2025-10-06 20:22:31', '2025-10-09 01:22:58', 4, '2025-10-06 20:28:12', 23),
(54, 'SRID-2025-20251006-2', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Doctorate', 'N/A', 'Doctor of Philosophy in Communication (PhD Com)', '(m) Pictorial illustrations and advertisements', 'BLABLA', '2025-10-01', 1, 'completed', '2025-10-06 20:59:42', 'test3', 1, 1, 'copyright', '2025-10-06 20:54:41', '2025-10-06 20:59:42', 4, '2025-10-06 20:59:10', 24),
(55, 'SRID-2025-20251006-3', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Masters', 'N/A', 'Master of Science in Construction Management (MSCM)', '(q) Broadcast recordings', 'a', '2025-10-06', 1, 'completed', '2025-10-06 21:03:28', 'testttt', 1, 1, 'copyright', '2025-10-06 21:02:50', '2025-10-06 21:03:28', 4, '2025-10-06 21:02:56', 25),
(56, 'SRID-2025-20251006-4', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Doctorate', 'N/A', 'Doctor of Philosophy in Economics (PhD Econ)', '(q) Broadcast recordings', 'a', '2025-09-19', 1, 'completed', '2025-10-07 00:23:45', 'oi', 1, 1, 'copyright', '2025-10-06 21:04:52', '2025-10-07 00:23:45', 4, '2025-10-07 00:08:52', 25),
(57, 'SRID-2025-20251006-5', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Science (CS)', 'Bachelor of Science Food Technology (BSFT)', '(m) Pictorial illustrations and advertisements', 'a', '2025-10-02', 1, 'completed', '2025-10-07 00:23:14', 'test', 1, 1, 'copyright', '2025-10-06 21:07:11', '2025-10-07 00:23:14', 4, '2025-10-07 00:02:19', 25),
(58, 'SRID-2025-20251006-6', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Open University', 'N/A', 'Bachelor of Public Administration (BPA)', '(p) Sound recordings', 'test', '2025-10-04', 1, 'completed', '2025-10-06 22:04:44', 'test', 1, 1, 'copyright', '2025-10-06 21:08:04', '2025-10-06 22:04:44', 4, '2025-10-06 22:01:07', 25),
(59, 'SRID-2025-20251006-7', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Human Kinetics (CHK)', 'Bachelor Of Physical Education (bpe)', '(n) Computer Programs', 'test3', '2025-10-06', 1, 'completed', '2025-10-07 00:02:55', 'mer', 1, 1, 'copyright', '2025-10-06 23:16:53', '2025-10-07 00:02:55', 4, '2025-10-06 23:47:08', 26),
(60, 'ERID-2025-20251006-8', 2, 'Mata', 'Bale', 'Lino', '12345', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234565', 'errorloading19990@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Doctorate', 'N/A', 'Doctor of Philosophy in Communication (PhD Com)', '(o) Other literary, scholarly, scientific and artistic works', 'a', '2025-10-06', 1, 'completed', '2025-10-07 00:02:41', 'test', 1, 1, 'copyright', '2025-10-06 23:25:53', '2025-10-07 00:02:41', 4, '2025-10-06 23:44:34', 25),
(61, 'SRID-2025-20251007-1', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Human Kinetics (CHK)', 'Bachelor Of Physical Education (bpe)', '(b) Periodicals and newspaper', 'a', '2025-10-07', 1, 'completed', '2025-10-07 01:24:40', 'mARKG', 1, 1, 'copyright', '2025-10-07 00:32:01', '2025-10-07 01:24:40', 4, '2025-10-07 01:11:45', 25),
(62, 'SRID-2025-20251007-2', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Human Kinetics (CHK)', 'Bachelor Of Physical Education (bpe)', '(a) Books, Pamphlets, articles and other writings', 'a', '2025-10-07', 1, 'completed', '2025-10-07 00:57:53', 'matoy', 1, 1, 'copyright', '2025-10-07 00:35:24', '2025-10-07 00:57:53', 4, '2025-10-07 00:35:43', 27),
(63, 'SRID-2025-20251007-3', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Human Kinetics (CHK)', 'Bachelor Of Physical Education (bpe)', '(a) Books, Pamphlets, articles and other writings', 'test', '2025-10-07', 1, 'completed', '2025-10-07 01:03:53', 'tset', 1, 1, 'copyright', '2025-10-07 00:51:16', '2025-10-09 01:53:14', 2, '2025-10-07 01:03:16', 25),
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
(88, 'SRID-2025-20251008-20', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Education (COED)', 'Bachelor of Secondary Education - English (BSEd)', '(l) Audiovisual works and cinematographic works', 'test', '2025-10-08', 1, 'completed', '2025-10-09 20:24:02', NULL, 1, 1, 'copyright', '2025-10-08 15:10:02', '2025-10-09 20:24:02', 4, '2025-10-09 20:23:30', 35),
(89, 'SRID-2025-20251008-21', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Education (COED)', 'Bachelor of Secondary Education - English (BSEd)', '(b) Periodicals and newspaper', 'test', '2025-10-08', 1, 'completed', '2025-10-09 01:41:18', NULL, 1, 1, 'copyright', '2025-10-08 15:21:06', '2025-10-09 01:41:18', 4, '2025-10-08 15:21:24', 35),
(90, 'SRID-2025-20251008-22', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Education (COED)', 'Bachelor of Secondary Education - English (BSEd)', '(n) Computer Programs', 'test', '2025-10-08', 1, 'approved', '2025-10-09 20:09:05', 'For Evaluation', 1, 1, 'copyright', '2025-10-08 15:27:08', '2025-10-09 20:09:05', 2, '2025-10-09 20:09:05', 35),
(91, 'ERID-2025-20251008-23', 6, 'Raebv Lielmo', 'A', 'Inocentes', '54321', '4334A V. Francisco St. Sta. Mesa, Manila', '09156574831', 'thinkingwan00@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Doctorate', 'College of Computer and Information Sciences (CCIS)', 'Bachelor of Science in Computer Science (BSCS)', '(b) Periodicals and newspaper', 'test', '2025-10-08', 1, 'approved', '2025-10-09 19:54:30', 'For Evaluation', 1, 1, 'copyright', '2025-10-08 15:36:40', '2025-10-09 19:54:30', 2, '2025-10-09 19:54:30', 35),
(92, 'SRID-2025-20251008-24', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Education (COED)', 'Bachelor of Secondary Education - English (BSEd)', '(p) Sound recordings', 'test', '2025-10-08', 1, 'approved', '2025-10-09 19:53:36', 'For Evaluation', 1, 1, 'copyright', '2025-10-08 15:48:29', '2025-10-09 19:53:36', 2, '2025-10-09 19:53:36', 35),
(93, 'SRID-2025-20251008-25', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Education (COED)', 'Bachelor of Secondary Education - English (BSEd)', '(n) Computer Programs', 'test', '2025-10-08', 1, 'completed', '2025-10-09 02:16:06', NULL, 1, 1, 'copyright', '2025-10-08 15:51:20', '2025-10-09 02:16:06', 2, '2025-10-09 02:15:34', 35),
(94, 'SRID-2025-20251008-26', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Education (COED)', 'Bachelor of Secondary Education - English (BSEd)', '(p) Sound recordings', 't', '2025-10-08', 1, 'completed', '2025-10-08 18:02:27', NULL, 1, 1, 'copyright', '2025-10-08 15:55:46', '2025-10-08 18:02:27', 4, '2025-10-08 18:02:21', 35),
(95, 'ERID-2025-20251008-27', 6, 'Raebv Lielmo', 'A', 'Inocentes', '54321', '4334A V. Francisco St. Sta. Mesa, Manila', '09156574831', 'thinkingwan00@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Doctorate', 'College of Computer and Information Sciences (CCIS)', 'Bachelor of Science in Computer Science (BSCS)', '(n) Computer Programs', 'test', '2025-10-08', 1, 'completed', '2025-10-08 20:55:15', NULL, 1, 1, 'copyright', '2025-10-08 20:55:01', '2025-10-08 20:55:15', 4, '2025-10-08 20:55:09', 35),
(96, 'ERID-2025-20251008-28', 6, 'Raebv Lielmo', 'A', 'Inocentes', '54321', '4334A V. Francisco St. Sta. Mesa, Manila', '09156574831', 'thinkingwan00@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Doctorate', 'College of Computer and Information Sciences (CCIS)', 'Bachelor of Science in Computer Science (BSCS)', '(p) Sound recordings', 'test', '2025-10-08', 1, 'approved', '2025-10-09 02:10:52', 'for evaluation', 1, 1, 'copyright', '2025-10-08 22:36:17', '2025-10-09 02:10:52', 2, '2025-10-09 02:10:52', 32),
(98, 'ERID-2025-20251009-1', 6, 'Raebv Lielmo', 'A', 'Inocentes', '54321', '4334A V. Francisco St. Sta. Mesa, Manila', '09156574831', 'thinkingwan00@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Doctorate', 'College of Computer and Information Sciences (CCIS)', 'Bachelor of Science in Computer Science (BSCS)', '(n) Computer Programs', 'ENHANCING INTELLECTUAL PROPERTY APPLICATIONS: PUP’S E-IPMO SERVICES FOR ACADEMIC WORKS', '2025-10-09', 1, 'completed', '2025-10-09 02:51:55', NULL, 1, 1, 'copyright', '2025-10-09 02:51:07', '2025-10-09 02:51:55', 2, '2025-10-09 02:51:35', 36),
(99, 'SRID-2025-20251009-2', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Education (COED)', 'Bachelor of Secondary Education - English (BSEd)', '(q) Broadcast recordings', 'test', '2025-10-09', 1, 'completed', '2025-10-09 19:39:44', NULL, 1, 1, 'copyright', '2025-10-09 19:33:30', '2025-10-09 19:39:44', 2, '2025-10-09 19:37:55', 37),
(100, 'SRID-2025-20251009-3', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Education (COED)', 'Bachelor of Secondary Education - English (BSEd)', '(b) Periodicals and newspaper', 'test', '2025-10-09', 1, 'completed', '2025-10-09 20:16:38', NULL, 1, 1, 'copyright', '2025-10-09 20:15:21', '2025-10-09 20:16:38', 2, '2025-10-09 20:15:50', 24),
(101, 'SRID-2025-20251009-4', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Education (COED)', 'Bachelor of Secondary Education - English (BSEd)', '(o) Other literary, scholarly, scientific and artistic works', 'test2', '2025-10-09', 1, 'completed', '2025-10-09 20:28:32', NULL, 1, 1, 'copyright', '2025-10-09 20:27:15', '2025-10-09 20:28:32', 3, '2025-10-09 20:28:12', 25),
(102, 'ERID-2025-20251009-5', 6, 'Raebv Lielmo', 'A', 'Inocentes', '54321', '4334A V. Francisco St. Sta. Mesa, Manila', '09156574831', 'thinkingwan00@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Doctorate', 'College of Computer and Information Sciences (CCIS)', 'Bachelor of Science in Computer Science (BSCS)', '(b) Periodicals and newspaper', 'ROADCHECK: An interactive driving decision simulator for enhancing road safety of drivers and commuters', '2025-10-09', 1, 'completed', '2025-10-09 20:48:16', 'go2', 1, 1, 'copyright', '2025-10-09 20:44:47', '2025-10-09 20:48:16', 3, '2025-10-09 20:46:53', 38),
(103, 'SRID-2025-20251009-6', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Education (COED)', 'Bachelor of Secondary Education - English (BSEd)', '(p) Sound recordings', 'test', '2025-10-09', 1, 'approved', '2025-10-09 21:11:07', 'for evaluation', 1, 1, 'copyright', '2025-10-09 21:10:59', '2025-10-09 21:11:07', NULL, '2025-10-09 21:11:07', 39),
(104, 'SRID-2025-20251009-7', 1, 'Marisa1', 'Mliinaw1', 'Minamo1', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Education (COED)', 'Bachelor of Secondary Education - English (BSEd)', '(b) Periodicals and newspaper', 'test', '2025-10-09', 1, 'approved', '2025-10-09 21:22:20', 'For Evaluation', 1, 1, 'copyright', '2025-10-09 21:16:28', '2025-10-09 21:22:20', NULL, '2025-10-09 21:22:20', 25),
(105, 'SRID-2025-20251009-8', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Education (COED)', 'Bachelor of Secondary Education - English (BSEd)', '(n) Computer Programs', 's', '2025-10-09', 1, 'completed', '2025-10-09 22:37:25', 'tGOGO', 1, 1, 'copyright', '2025-10-09 21:23:08', '2025-10-09 22:37:25', 3, '2025-10-09 21:23:37', 23),
(106, 'ERID-2025-20251009-9', 6, 'Raebv Lielmo', 'A', 'Inocentes', '54321', '4334A V. Francisco St. Sta. Mesa, Manila', '09156574831', 'thinkingwan00@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Doctorate', 'College of Computer and Information Sciences (CCIS)', 'Bachelor of Science in Computer Science (BSCS)', '(n) Computer Programs', 'test', '2025-10-09', 1, 'approved', '2025-10-09 21:24:58', 'For Evaluation', 1, 1, 'copyright', '2025-10-09 21:24:26', '2025-10-09 21:24:58', NULL, '2025-10-09 21:24:58', 25),
(107, 'ERID-2025-20251009-10', 6, 'Raebv Lielmo', 'A', 'Inocentes', '54321', '4334A V. Francisco St. Sta. Mesa, Manila', '09156574831', 'thinkingwan00@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Doctorate', 'College of Computer and Information Sciences (CCIS)', 'Bachelor of Science in Computer Science (BSCS)', '(o) Other literary, scholarly, scientific and artistic works', 'test', '2025-10-09', 1, 'completed', '2025-10-09 22:37:44', NULL, 1, 1, 'copyright', '2025-10-09 21:25:36', '2025-10-09 22:37:44', 3, '2025-10-09 21:26:01', 25),
(108, 'SRID-2025-20251009-11', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Education (COED)', 'Bachelor of Secondary Education - English (BSEd)', '(m) Pictorial illustrations and advertisements', 'test', '2025-10-09', 1, 'completed', '2025-10-09 22:36:53', NULL, 1, 1, 'copyright', '2025-10-09 21:27:52', '2025-10-09 22:36:53', 3, '2025-10-09 21:28:03', 25),
(109, 'ERID-2025-20251009-12', 6, 'Raebv Lielmo', 'A', 'Inocentes', '54321', '4334A V. Francisco St. Sta. Mesa, Manila', '09156574831', 'thinkingwan00@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Doctorate', 'College of Computer and Information Sciences (CCIS)', 'Bachelor of Science in Computer Science (BSCS)', '(c) Lectures, sermons, addresses, dissertations for oral delivery', 'test', '2025-10-09', 1, 'completed', '2025-10-09 22:13:44', '098', 1, 1, 'copyright', '2025-10-09 21:30:17', '2025-10-09 22:13:44', 3, '2025-10-09 22:12:53', 25),
(110, 'SRID-2025-20251009-13', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Education (COED)', 'Bachelor of Secondary Education - English (BSEd)', '(n) Computer Programs', 'test', '2025-10-09', 1, 'completed', '2025-10-09 22:22:27', 'GOGOGO', 1, 1, 'copyright', '2025-10-09 21:30:39', '2025-10-09 22:22:27', 3, '2025-10-09 22:12:35', 25),
(111, 'ERID-2025-20251009-14', 6, 'Raebv Lielmo', 'A', 'Inocentes', '54321', '4334A V. Francisco St. Sta. Mesa, Manila', '09156574831', 'thinkingwan00@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Doctorate', 'College of Computer and Information Sciences (CCIS)', 'Bachelor of Science in Computer Science (BSCS)', '(b) Periodicals and newspaper', 'script', '2025-10-09', 1, 'pending_review', NULL, 'for evaluation', 1, 1, 'copyright', '2025-10-09 22:41:02', '2025-10-09 22:41:02', NULL, NULL, 40),
(112, 'ERID-2025-20251009-15', 6, 'Raebv Lielmo', 'A', 'Inocentes', '54321', '4334A V. Francisco St. Sta. Mesa, Manila', '09156574831', 'thinkingwan00@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Doctorate', 'College of Computer and Information Sciences (CCIS)', 'Bachelor of Science in Computer Science (BSCS)', '(a) Books, Pamphlets, articles and other writings', 'test', '2025-10-09', 1, 'pending_review', NULL, 'for evaluation', 1, 1, 'copyright', '2025-10-09 22:44:36', '2025-10-09 22:44:36', NULL, NULL, 23),
(113, 'ERID-2025-20251009-16', 6, 'Raebv Lielmo', 'A', 'Inocentes', '54321', '4334A V. Francisco St. Sta. Mesa, Manila', '09156574831', 'thinkingwan00@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Doctorate', 'College of Computer and Information Sciences (CCIS)', 'Bachelor of Science in Computer Science (BSCS)', '(l) Audiovisual works and cinematographic works', 'te', '2025-10-09', 1, 'completed', '2025-10-09 23:25:05', NULL, 1, 1, 'copyright', '2025-10-09 23:23:01', '2025-10-09 23:25:05', 3, '2025-10-09 23:24:55', 41),
(114, 'ERID-2025-20251009-17', 6, 'Raebv Lielmo', 'A', 'Inocentes', '54321', '4334A V. Francisco St. Sta. Mesa, Manila', '09156574831', 'thinkingwan00@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Doctorate', 'College of Computer and Information Sciences (CCIS)', 'Bachelor of Science in Computer Science (BSCS)', '(b) Periodicals and newspaper', ',you', '2025-10-09', 1, 'pending_review', NULL, 'for evaluation', 1, 1, 'copyright', '2025-10-09 23:32:11', '2025-10-09 23:32:11', NULL, NULL, 42),
(115, 'ERID-2025-20251009-18', 6, 'Raebv Lielmo', 'A', 'Inocentes', '54321', '4334A V. Francisco St. Sta. Mesa, Manila', '09156574831', 'thinkingwan00@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Doctorate', 'College of Computer and Information Sciences (CCIS)', 'Bachelor of Science in Computer Science (BSCS)', '(b) Periodicals and newspaper', 'asd', '2025-10-09', 1, 'completed', '2025-10-09 23:35:50', NULL, 1, 1, 'copyright', '2025-10-09 23:33:17', '2025-10-09 23:35:50', 3, '2025-10-09 23:35:47', 43),
(116, 'ERID-2025-20251009-19', 6, 'Raebv Lielmo', 'A', 'Inocentes', '54321', '4334A V. Francisco St. Sta. Mesa, Manila', '09156574831', 'thinkingwan00@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Doctorate', 'College of Computer and Information Sciences (CCIS)', 'Bachelor of Science in Computer Science (BSCS)', '(a) Books, Pamphlets, articles and other writings', 'tee', '2025-10-09', 1, 'pending_review', NULL, 'for evaluation', 1, 1, 'copyright', '2025-10-09 23:34:05', '2025-10-09 23:34:05', NULL, NULL, 44),
(117, 'ERID-2025-20251009-20', 6, 'Raebv Lielmo', 'A', 'Inocentes', '54321', '4334A V. Francisco St. Sta. Mesa, Manila', '09156574831', 'thinkingwan00@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Doctorate', 'College of Computer and Information Sciences (CCIS)', 'Bachelor of Science in Computer Science (BSCS)', '(n) Computer Programs', 'test', '2025-10-09', 1, 'pending_review', NULL, 'for evaluation', 1, 1, 'copyright', '2025-10-09 23:40:06', '2025-10-09 23:40:06', NULL, NULL, 45),
(118, 'ERID-2025-20251009-21', 6, 'Raebv Lielmo', 'A', 'Inocentes', '54321', '4334A V. Francisco St. Sta. Mesa, Manila', '09156574831', 'thinkingwan00@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Doctorate', 'College of Computer and Information Sciences (CCIS)', 'Bachelor of Science in Computer Science (BSCS)', '(l) Audiovisual works and cinematographic works', 'test2', '2025-10-09', 1, 'pending_review', NULL, 'for evaluation', 1, 1, 'copyright', '2025-10-09 23:45:56', '2025-10-09 23:45:56', NULL, NULL, 46),
(119, 'ERID-2025-20251009-22', 6, 'Raebv Lielmo', 'A', 'Inocentes', '54321', '4334A V. Francisco St. Sta. Mesa, Manila', '09156574831', 'thinkingwan00@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Doctorate', 'College of Computer and Information Sciences (CCIS)', 'Bachelor of Science in Computer Science (BSCS)', '(m) Pictorial illustrations and advertisements', 'testtttt', '2025-10-08', 1, 'completed', '2025-10-09 23:51:39', NULL, 1, 1, 'copyright', '2025-10-09 23:51:18', '2025-10-09 23:51:39', 3, '2025-10-09 23:51:35', 23),
(120, 'SRID-2025-20251010-1', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Education (COED)', 'Bachelor of Secondary Education - English (BSEd)', '(m) Pictorial illustrations and advertisements', 'a', '2025-10-10', 1, 'pending_review', '2025-10-11 07:49:22', 'For Evaluation', 1, 1, 'copyright', '2025-10-10 14:20:12', '2025-10-11 07:49:41', NULL, NULL, 25),
(121, 'SRID-2025-20251010-2', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Education (COED)', 'Bachelor of Secondary Education - English (BSEd)', '(n) Computer Programs', 'tes', '2025-10-10', 1, 'approved', '2025-10-10 17:16:07', 'for evaluation', 1, 1, 'copyright', '2025-10-10 14:27:33', '2025-10-10 17:16:07', NULL, '2025-10-10 17:16:07', 25),
(122, 'SRID-2025-20251010-3', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Education (COED)', 'Bachelor of Secondary Education - English (BSEd)', '(i) Illustrations maps, plans, sketches, charts and three-dimensional works', 'a', '2025-10-10', 1, 'completed', '2025-10-10 17:04:44', NULL, 1, 1, 'copyright', '2025-10-10 14:55:15', '2025-10-10 17:04:44', 3, '2025-10-10 17:04:40', 25),
(123, 'SRID-2025-20251010-4', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Education (COED)', 'Bachelor of Secondary Education - English (BSEd)', '(q) Broadcast recordings', 'Alice\'s Adventures in Wonderland and Through the Looking-Glass or The Book of Going Forth by Day', '2025-10-10', 1, 'completed', '2025-10-10 18:01:46', 'GOGOGo', 1, 1, 'copyright', '2025-10-10 17:33:42', '2025-10-10 18:01:46', 3, '2025-10-10 17:55:25', 47),
(124, 'SRID-2025-20251010-5', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Education (COED)', 'Bachelor of Secondary Education - English (BSEd)', '(l) Audiovisual works and cinematographic works', 'test', '2025-10-10', 1, 'completed', '2025-10-10 18:14:01', NULL, 1, 1, 'copyright', '2025-10-10 17:39:44', '2025-10-10 18:14:01', 2, '2025-10-10 18:13:57', 48),
(125, 'SRID-2025-20251010-6', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Education (COED)', 'Bachelor of Secondary Education - English (BSEd)', '(b) Periodicals and newspaper', 'tes', '2025-10-10', 1, 'approved', '2025-10-10 21:00:01', 'test', 1, 1, 'copyright', '2025-10-10 20:21:42', '2025-10-10 21:00:01', NULL, '2025-10-10 21:00:01', 49),
(126, 'ERID-2025-20251010-7', 6, 'Raebv Lielmo', 'A', 'Inocentes', '54321', '4334A V. Francisco St. Sta. Mesa, Manila', '09156574831', 'thinkingwan00@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Doctorate', 'College of Computer and Information Sciences (CCIS)', 'Bachelor of Science in Computer Science (BSCS)', '(n) Computer Programs', 'te', '2025-10-10', 1, 'pending', '2025-10-10 21:00:57', 'For Evaluation', 1, 1, 'copyright', '2025-10-10 20:34:52', '2025-10-10 21:00:57', NULL, NULL, 44),
(127, 'SRID-2025-20251010-8', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Education (COED)', 'Bachelor of Secondary Education - English (BSEd)', '(m) Pictorial illustrations and advertisements', 'te', '2025-10-10', 1, 'pending_review', '2025-10-10 21:03:05', 'For Evaluation', 1, 1, 'copyright', '2025-10-10 21:02:49', '2025-10-11 07:25:38', NULL, NULL, 50),
(128, 'ERID-2025-20251010-9', 6, 'Raebv Lielmo', 'A', 'Inocentes', '54321', '4334A V. Francisco St. Sta. Mesa, Manila', '09156574831', 'thinkingwan00@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Doctorate', 'College of Computer and Information Sciences (CCIS)', 'Bachelor of Science in Computer Science (BSCS)', '(q) Broadcast recordings', 't', '2025-10-10', 1, 'pending_review', NULL, 'for evaluation', 1, 1, 'copyright', '2025-10-10 21:04:10', '2025-10-10 21:04:10', NULL, NULL, 50),
(129, 'SRID-2025-20251010-10', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Education (COED)', 'Bachelor of Secondary Education - English (BSEd)', '(o) Other literary, scholarly, scientific and artistic works', 'test', '2025-10-10', 1, 'approved', '2025-10-11 19:19:16', 'Needs Attention', 1, 1, 'copyright', '2025-10-10 23:50:38', '2025-10-11 19:19:16', NULL, '2025-10-11 19:16:27', 23),
(130, 'SRID-2025-20251011-1', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Education (COED)', 'Bachelor of Secondary Education - English (BSEd)', '(a) Books, Pamphlets, articles and other writings', 'as', '2025-10-10', 1, 'pending_review', '2025-10-11 21:21:22', 'For Evaluation', 1, 1, 'copyright', '2025-10-11 00:00:35', '2025-10-11 21:21:51', NULL, NULL, 51),
(131, 'ERID-2025-20251011-2', 6, 'Raebv Lielmo', 'A', 'Inocentes', '54321', '4334A V. Francisco St. Sta. Mesa, Manila', '09156574831', 'thinkingwan00@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Doctorate', 'College of Computer and Information Sciences (CCIS)', 'Bachelor of Science in Computer Science (BSCS)', '(o) Other literary, scholarly, scientific and artistic works', ',you', '2025-10-11', 1, 'pending_review', NULL, 'for evaluation', 1, 1, 'copyright', '2025-10-11 00:02:07', '2025-10-11 00:02:07', NULL, NULL, 45),
(132, 'ERID-2025-20251011-3', 6, 'Raebv Lielmo', 'A', 'Inocentes', '54321', '4334A V. Francisco St. Sta. Mesa, Manila', '09156574831', 'thinkingwan00@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Doctorate', 'College of Computer and Information Sciences (CCIS)', 'Bachelor of Science in Computer Science (BSCS)', '(o) Other literary, scholarly, scientific and artistic works', 'asdasd', '2025-10-11', 1, 'pending_review', NULL, 'for evaluation', 1, 1, 'copyright', '2025-10-11 00:22:32', '2025-10-11 00:22:32', NULL, NULL, 55),
(133, 'SRID-2025-20251011-4', 1, 'Marisa', 'Mliinaw', 'Minamo', '2025-12346-MN-0', '4746 Peralta St. V. Mapa Sta. Mesa Manila', '09171234567', 'aceplanetary0@gmail.com', 'PUP Main (Sta. Mesa, Manila)', 'Undergraduate', 'College of Education (COED)', 'Bachelor of Secondary Education - English (BSEd)', '(o) Other literary, scholarly, scientific and artistic works', 'testtttt', '2025-10-11', 1, 'pending', '2025-10-11 21:22:09', 'For Evaluation', 1, 1, 'copyright', '2025-10-11 00:27:03', '2025-10-11 21:22:09', NULL, NULL, 56);

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
(19, 69, 'Raebv Lielmo', '', 'Inocentes', '25222', '09156574831', '4334A V. Francisco St. Sta. Mesa, Manila', 'firstnamelastname@iskolarngbayan.pup.edu.ph', 'Author', 0, '2025-10-08 00:57:49', NULL),
(20, 97, '', '', '', '', '', '', '', 'Author', 0, '2025-10-09 01:57:02', NULL),
(21, 97, 'Mark', '', 'Garcia', '12986', '09156574831', '4334A V. Francisco St. Sta. Mesa, Manila', 'inocentesraebv@iskolarngbayan.pup.edu.ph', 'Author', 0, '2025-10-09 01:57:02', NULL),
(22, 97, 'Criselle', '', 'Trinidad', '02141', '09156574831', '4334A V. Francisco St. Sta. Mesa, Manila', 'inocentesraebv@iskolarngbayan.pup.edu.ph', 'Author', 0, '2025-10-09 01:57:02', NULL),
(23, 98, 'Paula', '', 'Cama', '12536', '09156574831', '4334A V. Francisco St. Sta. Mesa, Manila', 'inocentesraebv@iskolarngbayan.pup.edu.ph', 'Author', 0, '2025-10-09 02:51:07', NULL),
(24, 98, 'Criselle', '', 'Trinidad', '02141', '09156574831', '4334A V. Francisco St. Sta. Mesa, Manila', 'inocentesraebv@iskolarngbayan.pup.edu.ph', 'Author', 0, '2025-10-09 02:51:07', NULL),
(25, 98, 'Mark', '', 'Garcia', '25257', '09156574831', '4334A V. Francisco St. Sta. Mesa, Manila', 'inocentesraebv@iskolarngbayan.pup.edu.ph', 'Author', 0, '2025-10-09 02:51:07', NULL),
(26, 100, 'Ca', '', '', NULL, NULL, NULL, NULL, 'Adviser', 1, '2025-10-09 20:15:21', 24),
(27, 100, 'Test', '', 'Inocentes', '2022-08090-MN-0', '09156574831', '4334A V. Francisco St.', 'a@iskolarngbayan.pup.edu.ph', 'Author', 0, '2025-10-09 20:15:21', NULL),
(28, 101, 'A', '', '', NULL, NULL, NULL, NULL, 'Adviser', 1, '2025-10-09 20:27:15', 25),
(29, 102, 'Ethan', '', 'Cruz', '24563', '09156574831', '4334A V. Francisco St. Sta. Mesa, Manila', 'inocentesraebv@iskolarngbayan.pup.edu.ph', 'Author', 0, '2025-10-09 20:44:47', NULL),
(30, 102, 'Jan', '', 'Rouello', '24553', '09156574831', '4334A V. Francisco St. Sta. Mesa, Manila', 'inocentesraebv@iskolarngbayan.pup.edu.ph', 'Author', 0, '2025-10-09 20:44:47', NULL),
(31, 102, 'Arsi', '', 'Okol', '25888', '09156574831', '4334A V. Francisco St. Sta. Mesa, Manila', 'arsi@iskolarngbayan.pup.edu.ph', 'Author', 0, '2025-10-09 20:44:47', NULL),
(32, 102, 'Josh', '', 'Tubola', '98353', '09156574831', '4334A V. Francisco St. Sta. Mesa, Manila', 'inocentesraebv@iskolarngbayan.pup.edu.ph', 'Author', 0, '2025-10-09 20:44:47', NULL),
(33, 103, 'Aaaa', '', '', NULL, NULL, NULL, NULL, 'Adviser', 1, '2025-10-09 21:10:59', 39),
(34, 104, 'A', '', '', NULL, NULL, NULL, NULL, 'Adviser', 1, '2025-10-09 21:16:28', 25),
(35, 105, 'Test', '', '', NULL, NULL, NULL, NULL, 'Adviser', 1, '2025-10-09 21:23:08', 23),
(36, 108, 'A', '', '', NULL, NULL, NULL, NULL, 'Adviser', 1, '2025-10-09 21:27:52', 25),
(37, 110, 'A', '', '', NULL, NULL, NULL, NULL, 'Adviser', 1, '2025-10-09 21:30:39', 25),
(38, 111, 'Nada', '', 'One', '92922', '09828820242', '', 'at@iskolarngbayan.pup.edu.ph', 'Author', 0, '2025-10-09 22:41:02', NULL),
(39, 113, 'Test', '', '', '', '', '', '', 'Author', 0, '2025-10-09 23:23:01', NULL),
(40, 113, 'Raebv', '', 'Reee', '24242', '09878928888', '', 'firstna@iskolarngbayan.pup.edu.ph', 'Author', 0, '2025-10-09 23:23:01', NULL),
(41, 114, 'Hjun', '', '', '24552', '09898889999', '', 'firsmelastname@iskolarngbayan.pup.edu.ph', 'Author', 1, '2025-10-09 23:32:11', NULL),
(42, 115, 'Asdas', '', '', '25522', '09892929900', '', 'firstastname@iskolarngbayan.pup.edu.ph', 'Author', 1, '2025-10-09 23:33:17', NULL),
(43, 117, 'Bang', '', '', '', '', '', '', 'Author', 1, '2025-10-09 23:40:06', NULL),
(44, 117, 'Bujang', '', '', '25252', '09989232222', '', 'me@iskolarngbayan.pup.edu.ph', 'Author', 1, '2025-10-09 23:40:06', NULL),
(45, 118, 'Brave', '', '', '', '', '', '', 'Author', 0, '2025-10-09 23:45:56', NULL),
(46, 118, 'Test', '', '', '24224', '09890982822', '', 'firstne@iskolarngbayan.pup.edu.ph', 'Author', 0, '2025-10-09 23:45:56', NULL),
(47, 119, 'Test', '', '', '', '', '', '', 'Author', 1, '2025-10-09 23:51:18', NULL),
(48, 119, 'Set', '', '', '24222', '09892829999', '', 'firstnaame@iskolarngbayan.pup.edu.ph', 'Author', 0, '2025-10-09 23:51:18', NULL),
(49, 120, 'A', '', '', '', '', '', '', 'Author', 0, '2025-10-10 14:20:12', NULL),
(50, 121, 'A', '', '', '', '', '', '', 'Author', 0, '2025-10-10 14:27:33', NULL),
(51, 122, 'A', '', '', '', '', '', '', 'Author', 0, '2025-10-10 14:55:15', NULL),
(52, 123, 'Janelleee', '', '', '', '', '', '', 'Author', 0, '2025-10-10 17:33:42', NULL),
(53, 123, 'Heart', '', 'Trinidad', '2022-08490-MN-0', '09282882923', '', 'heart@iskolarngbayan.pup.edu.ph', 'Author', 0, '2025-10-10 17:33:42', NULL),
(54, 123, 'Mark', '', 'Garcia', '2922-99890-MN-0', '09827878848', '', 'e@iskolarngbayan.pup.edu.ph', 'Author', 0, '2025-10-10 17:33:42', NULL),
(55, 123, 'Paula', '', 'Cama', '2092-09829-MN-0', '09282989999', '', 'as@iskolarngbayan.pup.edu.ph', 'Author', 0, '2025-10-10 17:33:42', NULL),
(56, 124, 'Tse', '', '', '', '', '', '', 'Author', 0, '2025-10-10 17:39:44', NULL),
(57, 125, 'Ste', '', '', '', '', '', '', 'Author', 0, '2025-10-10 20:21:42', NULL),
(58, 126, 'Te', '', '', '', '', '', '', 'Author', 1, '2025-10-10 20:34:53', NULL),
(59, 127, 'Tes', '', '', '', '', '', '', 'Author', 0, '2025-10-10 21:02:49', NULL),
(60, 128, 'Tes', '', '', '', '', '', '', 'Author', 1, '2025-10-10 21:04:10', NULL),
(61, 129, 'Test', '', '', '', '', '', '', 'Author', 0, '2025-10-10 23:50:38', NULL),
(62, 129, 'Test', '', '', '2022-09880-MN-0', '09890989999', '', 'ame@iskolarngbayan.pup.edu.ph', 'Author', 0, '2025-10-10 23:50:38', NULL),
(63, 130, 'Bom', '', '', '', '', '', '', 'Author', 0, '2025-10-11 00:00:35', NULL),
(64, 130, 'Raebv', '', '', '2022-08920-MN-0', '09892029992', '', 'as@iskolarngbayan.pup.edu.ph', 'Author', 0, '2025-10-11 00:00:35', NULL),
(65, 131, 'Bang', '', '', '', '', '', '', 'Author', 1, '2025-10-11 00:02:07', NULL),
(66, 131, 'Ree', '', 'D', '42142', '09281989999', '', 'asme@iskolarngbayan.pup.edu.ph', 'Author', 0, '2025-10-11 00:02:07', NULL),
(67, 132, 'Tseess', '', '', '', '', '', '', 'Author', 1, '2025-10-11 00:22:32', NULL),
(68, 132, 'Steset', '', '', '12312', '09892029499', '', 'asdas@iskolarngbayan.pup.edu.ph', 'Author', 0, '2025-10-11 00:22:32', NULL),
(69, 133, 'Tesssdsd', '', '', '', '', '', '', 'Author', 1, '2025-10-11 00:27:03', NULL),
(70, 133, 'Sdfsdfsd', '', '', '2022-08290-MN-0', '09892029999', '', 'fasdaselas@iskolarngbayan.pup.edu.ph', 'Author', 0, '2025-10-11 00:27:03', NULL);

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
(285, 96, 'record_copyright', 'record_copyright_1759934177_3c481c68.pdf', '2025-10-08 22:36:17', 1653, 'application/pdf', 0, NULL, NULL),
(286, 97, 'journal_publication_format', 'journal_publication_format_1759946221_1e1df058.pdf', '2025-10-09 01:57:01', 1653, 'application/pdf', 0, NULL, NULL),
(287, 97, 'notarized_copyright', 'notarized_copyright_1759946221_5ccc02a7.pdf', '2025-10-09 01:57:01', 1653, 'application/pdf', 0, NULL, NULL),
(288, 97, 'receipt_payment', 'receipt_payment_1759946221_4ce636fb.pdf', '2025-10-09 01:57:01', 1653, 'application/pdf', 0, NULL, NULL),
(289, 97, 'presentation', 'presentation_1759946221_76ded77d.pdf', '2025-10-09 01:57:01', 1653, 'application/pdf', 0, NULL, NULL),
(290, 97, 'notarized_coauthorship', 'notarized_coauthorship_1759946221_385dddd9.pdf', '2025-10-09 01:57:02', 1653, 'application/pdf', 0, NULL, NULL),
(291, 97, 'record_copyright', 'record_copyright_1759946221_1a18d8c2.pdf', '2025-10-09 01:57:02', 1653, 'application/pdf', 0, NULL, NULL),
(292, 98, 'journal_publication_format', 'journal_publication_format_1759949467_c928befe.pdf', '2025-10-09 02:51:07', 1653, 'application/pdf', 0, NULL, NULL),
(293, 98, 'notarized_copyright', 'notarized_copyright_1759949467_d9d42c11.pdf', '2025-10-09 02:51:07', 1653, 'application/pdf', 0, NULL, NULL),
(294, 98, 'receipt_payment', 'receipt_payment_1759949467_fdfed018.pdf', '2025-10-09 02:51:07', 1653, 'application/pdf', 0, NULL, NULL),
(295, 98, 'presentation', 'presentation_1759949467_7ff5439d.pdf', '2025-10-09 02:51:07', 1653, 'application/pdf', 0, NULL, NULL),
(296, 98, 'notarized_coauthorship', 'notarized_coauthorship_1759949467_7907adbc.pdf', '2025-10-09 02:51:07', 1653, 'application/pdf', 0, NULL, NULL),
(297, 98, 'record_copyright', 'record_copyright_1759949467_151976dd.pdf', '2025-10-09 02:51:07', 1653, 'application/pdf', 0, NULL, NULL),
(298, 99, 'journal_publication_format', 'journal_publication_format_1760009610_c3c5d470.pdf', '2025-10-09 19:33:30', 177389, 'application/pdf', 0, NULL, NULL),
(299, 99, 'notarized_copyright', 'notarized_copyright_1760009610_e32f6783.pdf', '2025-10-09 19:33:30', 177349, 'application/pdf', 0, NULL, NULL),
(300, 99, 'receipt_payment', 'receipt_payment_1760009610_bc78ffe0.pdf', '2025-10-09 19:33:30', 385456, 'application/pdf', 0, NULL, NULL),
(301, 99, 'full_manuscript', 'full_manuscript_1760009610_927e33ca.pdf', '2025-10-09 19:33:30', 177349, 'application/pdf', 0, NULL, NULL),
(302, 99, 'notarized_coauthorship', 'notarized_coauthorship_1760009610_ecfd5eb5.pdf', '2025-10-09 19:33:30', 380568, 'application/pdf', 0, NULL, NULL),
(303, 99, 'approval_sheet', 'approval_sheet_1760009610_ec13773f.pdf', '2025-10-09 19:33:30', 385456, 'application/pdf', 0, NULL, NULL),
(304, 99, 'record_copyright', 'record_copyright_1760009610_bccde336.pdf', '2025-10-09 19:33:30', 177349, 'application/pdf', 0, NULL, NULL),
(305, 100, 'journal_publication_format', 'journal_publication_format_1760012121_28d18a28.pdf', '2025-10-09 20:15:21', 1831, 'application/pdf', 0, NULL, NULL),
(306, 100, 'notarized_copyright', 'notarized_copyright_1760012121_cff50979.pdf', '2025-10-09 20:15:21', 1831, 'application/pdf', 0, NULL, NULL),
(307, 100, 'receipt_payment', 'receipt_payment_1760012121_dac54848.pdf', '2025-10-09 20:15:21', 1835, 'application/pdf', 0, NULL, NULL),
(308, 100, 'full_manuscript', 'full_manuscript_1760012121_95451a2e.pdf', '2025-10-09 20:15:21', 1821, 'application/pdf', 0, NULL, NULL),
(309, 100, 'notarized_coauthorship', 'notarized_coauthorship_1760012121_598ef89a.pdf', '2025-10-09 20:15:21', 1841, 'application/pdf', 0, NULL, NULL),
(310, 100, 'approval_sheet', 'approval_sheet_1760012121_ada5698a.pdf', '2025-10-09 20:15:21', 1835, 'application/pdf', 0, NULL, NULL),
(311, 100, 'record_copyright', 'record_copyright_1760012121_6707e691.pdf', '2025-10-09 20:15:21', 1831, 'application/pdf', 0, NULL, NULL),
(312, 101, 'journal_publication_format', 'journal_publication_format_1760012835_d6226d4f.pdf', '2025-10-09 20:27:15', 1835, 'application/pdf', 0, NULL, NULL),
(313, 101, 'notarized_copyright', 'notarized_copyright_1760012835_b704d4d6.pdf', '2025-10-09 20:27:15', 1821, 'application/pdf', 0, NULL, NULL),
(314, 101, 'receipt_payment', 'receipt_payment_1760012835_77d15dec.pdf', '2025-10-09 20:27:15', 1835, 'application/pdf', 0, NULL, NULL),
(315, 101, 'full_manuscript', 'full_manuscript_1760012835_c4872c53.pdf', '2025-10-09 20:27:15', 1835, 'application/pdf', 0, NULL, NULL),
(316, 101, 'notarized_coauthorship', 'notarized_coauthorship_1760012835_b42fbaf5.pdf', '2025-10-09 20:27:15', 1835, 'application/pdf', 0, NULL, NULL),
(317, 101, 'approval_sheet', 'approval_sheet_1760012835_b099f846.pdf', '2025-10-09 20:27:15', 1835, 'application/pdf', 0, NULL, NULL),
(318, 101, 'record_copyright', 'record_copyright_1760012835_42d4668f.pdf', '2025-10-09 20:27:15', 1835, 'application/pdf', 0, NULL, NULL),
(319, 102, 'journal_publication_format', 'journal_publication_format_1760013983_ee58734c.pdf', '2025-10-09 20:46:23', 1649, 'application/pdf', 0, NULL, NULL),
(320, 102, 'notarized_copyright', 'notarized_copyright_1760013983_f3cfa83c.pdf', '2025-10-09 20:46:23', 1649, 'application/pdf', 0, NULL, NULL),
(321, 102, 'receipt_payment', 'receipt_payment_1760013983_70b82948.pdf', '2025-10-09 20:46:23', 1649, 'application/pdf', 0, NULL, NULL),
(322, 102, 'presentation', 'presentation_1760013983_6c852d7e.pdf', '2025-10-09 20:46:23', 1649, 'application/pdf', 0, NULL, NULL),
(323, 102, 'notarized_coauthorship', 'notarized_coauthorship_1760013983_955311de.pdf', '2025-10-09 20:46:23', 1649, 'application/pdf', 0, NULL, NULL),
(324, 102, 'record_copyright', 'record_copyright_1760013983_d7dd0351.pdf', '2025-10-09 20:46:23', 1649, 'application/pdf', 0, NULL, NULL),
(325, 103, 'journal_publication_format', 'journal_publication_format_1760015459_afbe12f1.pdf', '2025-10-09 21:10:59', 1653, 'application/pdf', 0, NULL, NULL),
(326, 103, 'notarized_copyright', 'notarized_copyright_1760015459_d50f830c.pdf', '2025-10-09 21:10:59', 1653, 'application/pdf', 0, NULL, NULL),
(327, 103, 'receipt_payment', 'receipt_payment_1760015459_84a51c8d.pdf', '2025-10-09 21:10:59', 1653, 'application/pdf', 0, NULL, NULL),
(328, 103, 'full_manuscript', 'full_manuscript_1760015459_4cf3fdb1.pdf', '2025-10-09 21:10:59', 1653, 'application/pdf', 0, NULL, NULL),
(329, 103, 'notarized_coauthorship', 'notarized_coauthorship_1760015459_9284aa16.pdf', '2025-10-09 21:10:59', 1653, 'application/pdf', 0, NULL, NULL),
(330, 103, 'approval_sheet', 'approval_sheet_1760015459_3ecd4a96.pdf', '2025-10-09 21:10:59', 1653, 'application/pdf', 0, NULL, NULL),
(331, 103, 'record_copyright', 'record_copyright_1760015459_1d45ca20.pdf', '2025-10-09 21:10:59', 1653, 'application/pdf', 0, NULL, NULL),
(332, 104, 'journal_publication_format', 'journal_publication_format_1760015788_00436983.pdf', '2025-10-09 21:16:28', 1831, 'application/pdf', 0, NULL, NULL),
(333, 104, 'notarized_copyright', 'notarized_copyright_1760015788_8426b8c1.pdf', '2025-10-09 21:16:28', 1841, 'application/pdf', 0, NULL, NULL),
(334, 104, 'receipt_payment', 'receipt_payment_1760015788_1c15b828.pdf', '2025-10-09 21:16:28', 1821, 'application/pdf', 0, NULL, NULL),
(335, 104, 'full_manuscript', 'full_manuscript_1760015788_57ad3c84.pdf', '2025-10-09 21:16:28', 1831, 'application/pdf', 0, NULL, NULL),
(336, 104, 'notarized_coauthorship', 'notarized_coauthorship_1760015788_ee17e3fb.pdf', '2025-10-09 21:16:28', 1841, 'application/pdf', 0, NULL, NULL),
(337, 104, 'approval_sheet', 'approval_sheet_1760015788_c44a33c2.pdf', '2025-10-09 21:16:28', 1831, 'application/pdf', 0, NULL, NULL),
(338, 104, 'record_copyright', 'record_copyright_1760015788_c04b70ac.pdf', '2025-10-09 21:16:28', 1821, 'application/pdf', 0, NULL, NULL),
(339, 105, 'journal_publication_format', 'journal_publication_format_1760016188_5359c6d3.pdf', '2025-10-09 21:23:08', 1835, 'application/pdf', 0, NULL, NULL),
(340, 105, 'notarized_copyright', 'notarized_copyright_1760016188_85cc875e.pdf', '2025-10-09 21:23:08', 1821, 'application/pdf', 0, NULL, NULL),
(341, 105, 'receipt_payment', 'receipt_payment_1760016188_a8f43fbe.pdf', '2025-10-09 21:23:08', 1831, 'application/pdf', 0, NULL, NULL),
(342, 105, 'full_manuscript', 'full_manuscript_1760016188_6dcba003.pdf', '2025-10-09 21:23:08', 1835, 'application/pdf', 0, NULL, NULL),
(343, 105, 'notarized_coauthorship', 'notarized_coauthorship_1760016188_9960ae94.pdf', '2025-10-09 21:23:08', 1821, 'application/pdf', 0, NULL, NULL),
(344, 105, 'approval_sheet', 'approval_sheet_1760016188_d5a01284.pdf', '2025-10-09 21:23:08', 1821, 'application/pdf', 0, NULL, NULL),
(345, 105, 'record_copyright', 'record_copyright_1760016188_6a26c634.pdf', '2025-10-09 21:23:08', 1821, 'application/pdf', 0, NULL, NULL),
(346, 106, 'journal_publication_format', 'journal_publication_format_1760016290_78013682.pdf', '2025-10-09 21:24:50', 1821, 'application/pdf', 0, NULL, NULL),
(347, 106, 'notarized_copyright', 'notarized_copyright_1760016266_74466382.pdf', '2025-10-09 21:24:26', 1841, 'application/pdf', 0, NULL, NULL),
(348, 106, 'receipt_payment', 'receipt_payment_1760016266_8630b687.pdf', '2025-10-09 21:24:26', 1822, 'application/pdf', 0, NULL, NULL),
(349, 106, 'presentation', 'presentation_1760016266_ff49f8fc.pdf', '2025-10-09 21:24:26', 1821, 'application/pdf', 0, NULL, NULL),
(350, 106, 'notarized_coauthorship', 'notarized_coauthorship_1760016266_640e15d4.pdf', '2025-10-09 21:24:26', 1831, 'application/pdf', 0, NULL, NULL),
(351, 106, 'record_copyright', 'record_copyright_1760016266_ac126ae2.pdf', '2025-10-09 21:24:26', 1831, 'application/pdf', 0, NULL, NULL),
(352, 107, 'journal_publication_format', 'journal_publication_format_1760016336_b3b6eea5.pdf', '2025-10-09 21:25:36', 1841, 'application/pdf', 0, NULL, NULL);
INSERT INTO `submission_documents` (`document_id`, `submission_id`, `doc_type`, `file_path`, `uploaded_at`, `file_size`, `mime_type`, `verified`, `verified_by`, `verified_at`) VALUES
(353, 107, 'notarized_copyright', 'notarized_copyright_1760016336_252ebff2.pdf', '2025-10-09 21:25:36', 1821, 'application/pdf', 0, NULL, NULL),
(354, 107, 'receipt_payment', 'receipt_payment_1760016336_61a860dc.pdf', '2025-10-09 21:25:36', 1821, 'application/pdf', 0, NULL, NULL),
(355, 107, 'presentation', 'presentation_1760016336_1dc3ce32.pdf', '2025-10-09 21:25:36', 1835, 'application/pdf', 0, NULL, NULL),
(356, 107, 'notarized_coauthorship', 'notarized_coauthorship_1760016336_6ac88bea.pdf', '2025-10-09 21:25:36', 1822, 'application/pdf', 0, NULL, NULL),
(357, 107, 'record_copyright', 'record_copyright_1760016336_3602265a.pdf', '2025-10-09 21:25:36', 1835, 'application/pdf', 0, NULL, NULL),
(358, 108, 'journal_publication_format', 'journal_publication_format_1760016472_f27e8280.pdf', '2025-10-09 21:27:52', 1841, 'application/pdf', 0, NULL, NULL),
(359, 108, 'notarized_copyright', 'notarized_copyright_1760016472_dec53b2e.pdf', '2025-10-09 21:27:52', 1831, 'application/pdf', 0, NULL, NULL),
(360, 108, 'receipt_payment', 'receipt_payment_1760016472_da4af469.pdf', '2025-10-09 21:27:52', 1821, 'application/pdf', 0, NULL, NULL),
(361, 108, 'full_manuscript', 'full_manuscript_1760016472_1c9fc3d9.pdf', '2025-10-09 21:27:53', 1821, 'application/pdf', 0, NULL, NULL),
(362, 108, 'notarized_coauthorship', 'notarized_coauthorship_1760016472_47848cdf.pdf', '2025-10-09 21:27:53', 1831, 'application/pdf', 0, NULL, NULL),
(363, 108, 'approval_sheet', 'approval_sheet_1760016472_3ee48e68.pdf', '2025-10-09 21:27:53', 1841, 'application/pdf', 0, NULL, NULL),
(364, 108, 'record_copyright', 'record_copyright_1760016472_dd698f0d.pdf', '2025-10-09 21:27:53', 1831, 'application/pdf', 0, NULL, NULL),
(365, 109, 'journal_publication_format', 'journal_publication_format_1760016617_6e58fb20.pdf', '2025-10-09 21:30:17', 1835, 'application/pdf', 0, NULL, NULL),
(366, 109, 'notarized_copyright', 'notarized_copyright_1760016617_2334fecb.pdf', '2025-10-09 21:30:17', 1841, 'application/pdf', 0, NULL, NULL),
(367, 109, 'receipt_payment', 'receipt_payment_1760016617_0c242d11.pdf', '2025-10-09 21:30:17', 1821, 'application/pdf', 0, NULL, NULL),
(368, 109, 'presentation', 'presentation_1760016617_0ee13861.pdf', '2025-10-09 21:30:17', 1821, 'application/pdf', 0, NULL, NULL),
(369, 109, 'notarized_coauthorship', 'notarized_coauthorship_1760016617_4c6981b3.pdf', '2025-10-09 21:30:17', 1831, 'application/pdf', 0, NULL, NULL),
(370, 109, 'record_copyright', 'record_copyright_1760016617_82b0c069.pdf', '2025-10-09 21:30:17', 1821, 'application/pdf', 0, NULL, NULL),
(371, 110, 'journal_publication_format', 'journal_publication_format_1760016639_01d388cc.pdf', '2025-10-09 21:30:39', 1841, 'application/pdf', 0, NULL, NULL),
(372, 110, 'notarized_copyright', 'notarized_copyright_1760016639_eb42d5bf.pdf', '2025-10-09 21:30:39', 1821, 'application/pdf', 0, NULL, NULL),
(373, 110, 'receipt_payment', 'receipt_payment_1760016639_20a1372f.pdf', '2025-10-09 21:30:39', 1831, 'application/pdf', 0, NULL, NULL),
(374, 110, 'full_manuscript', 'full_manuscript_1760016639_915b8862.pdf', '2025-10-09 21:30:39', 1841, 'application/pdf', 0, NULL, NULL),
(375, 110, 'notarized_coauthorship', 'notarized_coauthorship_1760016639_51c93307.pdf', '2025-10-09 21:30:39', 1831, 'application/pdf', 0, NULL, NULL),
(376, 110, 'approval_sheet', 'approval_sheet_1760016639_583e4a03.pdf', '2025-10-09 21:30:39', 1831, 'application/pdf', 0, NULL, NULL),
(377, 110, 'record_copyright', 'record_copyright_1760016639_ba61d53b.pdf', '2025-10-09 21:30:39', 1821, 'application/pdf', 0, NULL, NULL),
(378, 111, 'journal_publication_format', 'journal_publication_format_1760020862_8cc7fc59.pdf', '2025-10-09 22:41:02', 1841, 'application/pdf', 0, NULL, NULL),
(379, 111, 'notarized_copyright', 'notarized_copyright_1760020862_021aee9f.pdf', '2025-10-09 22:41:02', 1831, 'application/pdf', 0, NULL, NULL),
(380, 111, 'receipt_payment', 'receipt_payment_1760020862_9b9f2429.pdf', '2025-10-09 22:41:02', 1831, 'application/pdf', 0, NULL, NULL),
(381, 111, 'presentation', 'presentation_1760020862_f677a426.pdf', '2025-10-09 22:41:02', 1831, 'application/pdf', 0, NULL, NULL),
(382, 111, 'notarized_coauthorship', 'notarized_coauthorship_1760020862_5eb4b946.pdf', '2025-10-09 22:41:02', 1831, 'application/pdf', 0, NULL, NULL),
(383, 111, 'record_copyright', 'record_copyright_1760020862_aaed88a4.pdf', '2025-10-09 22:41:02', 1821, 'application/pdf', 0, NULL, NULL),
(384, 112, 'journal_publication_format', 'journal_publication_format_1760021076_6949b6ed.pdf', '2025-10-09 22:44:36', 1831, 'application/pdf', 0, NULL, NULL),
(385, 112, 'notarized_copyright', 'notarized_copyright_1760021076_d5c63122.pdf', '2025-10-09 22:44:36', 1821, 'application/pdf', 0, NULL, NULL),
(386, 112, 'receipt_payment', 'receipt_payment_1760021076_696e7e2a.pdf', '2025-10-09 22:44:36', 1831, 'application/pdf', 0, NULL, NULL),
(387, 112, 'presentation', 'presentation_1760021076_3af4aca7.pdf', '2025-10-09 22:44:36', 1841, 'application/pdf', 0, NULL, NULL),
(388, 112, 'notarized_coauthorship', 'notarized_coauthorship_1760021076_2ab24ba8.pdf', '2025-10-09 22:44:36', 1821, 'application/pdf', 0, NULL, NULL),
(389, 112, 'record_copyright', 'record_copyright_1760021076_9b0fae6e.pdf', '2025-10-09 22:44:36', 1831, 'application/pdf', 0, NULL, NULL),
(390, 113, 'journal_publication_format', 'journal_publication_format_1760023381_c1357477.pdf', '2025-10-09 23:23:01', 1841, 'application/pdf', 0, NULL, NULL),
(391, 113, 'notarized_copyright', 'notarized_copyright_1760023381_ddcf08be.pdf', '2025-10-09 23:23:01', 1821, 'application/pdf', 0, NULL, NULL),
(392, 113, 'receipt_payment', 'receipt_payment_1760023381_e0337ed8.pdf', '2025-10-09 23:23:01', 1831, 'application/pdf', 0, NULL, NULL),
(393, 113, 'presentation', 'presentation_1760023381_844d4b57.pdf', '2025-10-09 23:23:01', 1841, 'application/pdf', 0, NULL, NULL),
(394, 113, 'notarized_coauthorship', 'notarized_coauthorship_1760023381_41c71342.pdf', '2025-10-09 23:23:01', 1831, 'application/pdf', 0, NULL, NULL),
(395, 113, 'record_copyright', 'record_copyright_1760023381_fcc80206.pdf', '2025-10-09 23:23:01', 1822, 'application/pdf', 0, NULL, NULL),
(396, 114, 'journal_publication_format', 'journal_publication_format_1760023931_ab65ebbb.pdf', '2025-10-09 23:32:11', 1841, 'application/pdf', 0, NULL, NULL),
(397, 114, 'notarized_copyright', 'notarized_copyright_1760023931_2ae74005.pdf', '2025-10-09 23:32:11', 1835, 'application/pdf', 0, NULL, NULL),
(398, 114, 'receipt_payment', 'receipt_payment_1760023931_1d6f3888.pdf', '2025-10-09 23:32:11', 1831, 'application/pdf', 0, NULL, NULL),
(399, 114, 'presentation', 'presentation_1760023931_3133a382.pdf', '2025-10-09 23:32:11', 1831, 'application/pdf', 0, NULL, NULL),
(400, 114, 'notarized_coauthorship', 'notarized_coauthorship_1760023931_5c2a67aa.pdf', '2025-10-09 23:32:11', 1841, 'application/pdf', 0, NULL, NULL),
(401, 114, 'record_copyright', 'record_copyright_1760023931_9ea8c6ea.pdf', '2025-10-09 23:32:11', 1822, 'application/pdf', 0, NULL, NULL),
(402, 115, 'journal_publication_format', 'journal_publication_format_1760023997_aeb58959.pdf', '2025-10-09 23:33:17', 1822, 'application/pdf', 0, NULL, NULL),
(403, 115, 'notarized_copyright', 'notarized_copyright_1760023997_dc62a3c6.pdf', '2025-10-09 23:33:17', 1821, 'application/pdf', 0, NULL, NULL),
(404, 115, 'receipt_payment', 'receipt_payment_1760023997_7f6689a5.pdf', '2025-10-09 23:33:17', 1831, 'application/pdf', 0, NULL, NULL),
(405, 115, 'presentation', 'presentation_1760023997_25bc1127.pdf', '2025-10-09 23:33:17', 1831, 'application/pdf', 0, NULL, NULL),
(406, 115, 'notarized_coauthorship', 'notarized_coauthorship_1760023997_dd915431.pdf', '2025-10-09 23:33:17', 1831, 'application/pdf', 0, NULL, NULL),
(407, 115, 'record_copyright', 'record_copyright_1760023997_4c9006ec.pdf', '2025-10-09 23:33:17', 1821, 'application/pdf', 0, NULL, NULL),
(408, 116, 'journal_publication_format', 'journal_publication_format_1760024045_baa3fb16.pdf', '2025-10-09 23:34:05', 1821, 'application/pdf', 0, NULL, NULL),
(409, 116, 'notarized_copyright', 'notarized_copyright_1760024045_01b6aa89.pdf', '2025-10-09 23:34:05', 1831, 'application/pdf', 0, NULL, NULL),
(410, 116, 'receipt_payment', 'receipt_payment_1760024045_5f074f0d.pdf', '2025-10-09 23:34:05', 1831, 'application/pdf', 0, NULL, NULL),
(411, 116, 'presentation', 'presentation_1760024045_f5681226.pdf', '2025-10-09 23:34:05', 1831, 'application/pdf', 0, NULL, NULL),
(412, 116, 'notarized_coauthorship', 'notarized_coauthorship_1760024045_34d99ca8.pdf', '2025-10-09 23:34:05', 1831, 'application/pdf', 0, NULL, NULL),
(413, 116, 'record_copyright', 'record_copyright_1760024045_5ddb6f8e.pdf', '2025-10-09 23:34:05', 1821, 'application/pdf', 0, NULL, NULL),
(414, 117, 'journal_publication_format', 'journal_publication_format_1760024406_468a7fee.pdf', '2025-10-09 23:40:06', 1841, 'application/pdf', 0, NULL, NULL),
(415, 117, 'notarized_copyright', 'notarized_copyright_1760024406_b5a21daa.pdf', '2025-10-09 23:40:06', 1841, 'application/pdf', 0, NULL, NULL),
(416, 117, 'receipt_payment', 'receipt_payment_1760024406_f8ddd450.pdf', '2025-10-09 23:40:06', 1831, 'application/pdf', 0, NULL, NULL),
(417, 117, 'presentation', 'presentation_1760024406_6f6e6137.pdf', '2025-10-09 23:40:06', 1831, 'application/pdf', 0, NULL, NULL),
(418, 117, 'notarized_coauthorship', 'notarized_coauthorship_1760024406_6cc46929.pdf', '2025-10-09 23:40:06', 1821, 'application/pdf', 0, NULL, NULL),
(419, 117, 'record_copyright', 'record_copyright_1760024406_4a704365.pdf', '2025-10-09 23:40:06', 1835, 'application/pdf', 0, NULL, NULL),
(420, 118, 'journal_publication_format', 'journal_publication_format_1760024756_07011317.pdf', '2025-10-09 23:45:56', 1831, 'application/pdf', 0, NULL, NULL),
(421, 118, 'notarized_copyright', 'notarized_copyright_1760024756_f4cbd624.pdf', '2025-10-09 23:45:56', 1822, 'application/pdf', 0, NULL, NULL),
(422, 118, 'receipt_payment', 'receipt_payment_1760024756_d500a88d.pdf', '2025-10-09 23:45:56', 1831, 'application/pdf', 0, NULL, NULL),
(423, 118, 'presentation', 'presentation_1760024756_64681e3b.pdf', '2025-10-09 23:45:56', 1821, 'application/pdf', 0, NULL, NULL),
(424, 118, 'notarized_coauthorship', 'notarized_coauthorship_1760024756_ac962a61.pdf', '2025-10-09 23:45:56', 1841, 'application/pdf', 0, NULL, NULL),
(425, 118, 'record_copyright', 'record_copyright_1760024756_ecb5ba84.pdf', '2025-10-09 23:45:56', 1831, 'application/pdf', 0, NULL, NULL),
(426, 119, 'journal_publication_format', 'journal_publication_format_1760025078_8b254bdd.pdf', '2025-10-09 23:51:18', 1835, 'application/pdf', 0, NULL, NULL),
(427, 119, 'notarized_copyright', 'notarized_copyright_1760025078_a0eebaaf.pdf', '2025-10-09 23:51:18', 1821, 'application/pdf', 0, NULL, NULL),
(428, 119, 'receipt_payment', 'receipt_payment_1760025078_a880ac23.pdf', '2025-10-09 23:51:18', 1831, 'application/pdf', 0, NULL, NULL),
(429, 119, 'presentation', 'presentation_1760025078_ccc80550.pdf', '2025-10-09 23:51:18', 1831, 'application/pdf', 0, NULL, NULL),
(430, 119, 'notarized_coauthorship', 'notarized_coauthorship_1760025078_604252da.pdf', '2025-10-09 23:51:18', 1831, 'application/pdf', 0, NULL, NULL),
(431, 119, 'record_copyright', 'record_copyright_1760025078_b741ed45.pdf', '2025-10-09 23:51:18', 1831, 'application/pdf', 0, NULL, NULL),
(432, 120, 'journal_publication_format', 'journal_publication_format_1760140181_2897acf1.pdf', '2025-10-11 07:49:41', 1831, 'application/pdf', 0, NULL, NULL),
(433, 120, 'notarized_copyright', 'notarized_copyright_1760077212_6548b3bb.pdf', '2025-10-10 14:20:12', 1821, 'application/pdf', 0, NULL, NULL),
(434, 120, 'receipt_payment', 'receipt_payment_1760077212_c58b90c7.pdf', '2025-10-10 14:20:12', 1822, 'application/pdf', 0, NULL, NULL),
(435, 120, 'full_manuscript', 'full_manuscript_1760140181_13883821.pdf', '2025-10-11 07:49:41', 1822, 'application/pdf', 0, NULL, NULL),
(436, 120, 'notarized_coauthorship', 'notarized_coauthorship_1760140181_cf7aab20.pdf', '2025-10-11 07:49:41', 1831, 'application/pdf', 0, NULL, NULL),
(437, 120, 'approval_sheet', 'approval_sheet_1760140181_559b3586.pdf', '2025-10-11 07:49:41', 1831, 'application/pdf', 0, NULL, NULL),
(438, 120, 'record_copyright', 'record_copyright_1760077212_33d6076d.pdf', '2025-10-10 14:20:12', 1835, 'application/pdf', 0, NULL, NULL),
(439, 121, 'journal_publication_format', 'journal_publication_format_1760077653_ed96d1e9.pdf', '2025-10-10 14:27:33', 1821, 'application/pdf', 0, NULL, NULL),
(440, 121, 'notarized_copyright', 'notarized_copyright_1760077653_8a5f9e91.pdf', '2025-10-10 14:27:33', 1831, 'application/pdf', 0, NULL, NULL),
(441, 121, 'receipt_payment', 'receipt_payment_1760077653_b3b0eccd.pdf', '2025-10-10 14:27:33', 1835, 'application/pdf', 0, NULL, NULL),
(442, 121, 'full_manuscript', 'full_manuscript_1760077653_86db9d74.pdf', '2025-10-10 14:27:33', 1821, 'application/pdf', 0, NULL, NULL),
(443, 121, 'notarized_coauthorship', 'notarized_coauthorship_1760077653_7b6b8f97.pdf', '2025-10-10 14:27:33', 1831, 'application/pdf', 0, NULL, NULL),
(444, 121, 'approval_sheet', 'approval_sheet_1760077653_892cb911.pdf', '2025-10-10 14:27:33', 1831, 'application/pdf', 0, NULL, NULL),
(445, 121, 'record_copyright', 'record_copyright_1760077653_96687461.pdf', '2025-10-10 14:27:33', 1822, 'application/pdf', 0, NULL, NULL),
(446, 122, 'journal_publication_format', 'journal_publication_format_1760079315_d2c7e7fe.pdf', '2025-10-10 14:55:15', 1831, 'application/pdf', 0, NULL, NULL),
(447, 122, 'notarized_copyright', 'notarized_copyright_1760079315_c8719f79.pdf', '2025-10-10 14:55:15', 1831, 'application/pdf', 0, NULL, NULL),
(448, 122, 'receipt_payment', 'receipt_payment_1760079315_54b174e2.pdf', '2025-10-10 14:55:15', 1835, 'application/pdf', 0, NULL, NULL),
(449, 122, 'full_manuscript', 'full_manuscript_1760079315_49ccc317.pdf', '2025-10-10 14:55:15', 1831, 'application/pdf', 0, NULL, NULL),
(450, 122, 'notarized_coauthorship', 'notarized_coauthorship_1760079315_2face208.pdf', '2025-10-10 14:55:15', 1831, 'application/pdf', 0, NULL, NULL),
(451, 122, 'approval_sheet', 'approval_sheet_1760080522_afeec81e.pdf', '2025-10-10 15:15:22', 1831, 'application/pdf', 0, NULL, NULL),
(452, 122, 'record_copyright', 'record_copyright_1760079315_bc17cb84.pdf', '2025-10-10 14:55:15', 1841, 'application/pdf', 0, NULL, NULL),
(453, 123, 'journal_publication_format', 'journal_publication_format_1760088822_af1f4979.pdf', '2025-10-10 17:33:42', 1821, 'application/pdf', 0, NULL, NULL),
(454, 123, 'notarized_copyright', 'notarized_copyright_1760088822_8cda44a2.pdf', '2025-10-10 17:33:42', 1831, 'application/pdf', 0, NULL, NULL),
(455, 123, 'receipt_payment', 'receipt_payment_1760088822_56c5e3a7.pdf', '2025-10-10 17:33:42', 1822, 'application/pdf', 0, NULL, NULL),
(456, 123, 'full_manuscript', 'full_manuscript_1760088822_b4f15f76.pdf', '2025-10-10 17:33:42', 1822, 'application/pdf', 0, NULL, NULL),
(457, 123, 'notarized_coauthorship', 'notarized_coauthorship_1760088822_1b6f2522.pdf', '2025-10-10 17:33:42', 1821, 'application/pdf', 0, NULL, NULL),
(458, 123, 'approval_sheet', 'approval_sheet_1760090107_b1badd59.pdf', '2025-10-10 17:55:07', 1831, 'application/pdf', 0, NULL, NULL),
(459, 123, 'record_copyright', 'record_copyright_1760088822_35a946ce.pdf', '2025-10-10 17:33:42', 1835, 'application/pdf', 0, NULL, NULL),
(460, 124, 'journal_publication_format', 'journal_publication_format_1760089183_44a25933.pdf', '2025-10-10 17:39:44', 312670, 'application/pdf', 0, NULL, NULL),
(461, 124, 'notarized_copyright', 'notarized_copyright_1760089183_20cf89b1.pdf', '2025-10-10 17:39:44', 312670, 'application/pdf', 0, NULL, NULL),
(462, 124, 'receipt_payment', 'receipt_payment_1760089183_0dccbfaa.pdf', '2025-10-10 17:39:44', 312670, 'application/pdf', 0, NULL, NULL),
(463, 124, 'full_manuscript', 'full_manuscript_1760089183_1da247a2.pdf', '2025-10-10 17:39:44', 312670, 'application/pdf', 0, NULL, NULL),
(464, 124, 'notarized_coauthorship', 'notarized_coauthorship_1760089183_02278d0c.pdf', '2025-10-10 17:39:44', 312670, 'application/pdf', 0, NULL, NULL),
(465, 124, 'approval_sheet', 'approval_sheet_1760089183_bfc5fefd.pdf', '2025-10-10 17:39:44', 312670, 'application/pdf', 0, NULL, NULL),
(466, 124, 'record_copyright', 'record_copyright_1760089183_8d52caf0.pdf', '2025-10-10 17:39:44', 312670, 'application/pdf', 0, NULL, NULL),
(467, 125, 'journal_publication_format', 'journal_publication_format_20251010_202141_005a1302.pdf', '2025-10-10 20:21:42', 41341591, 'application/pdf', 0, NULL, NULL),
(468, 125, 'notarized_copyright', 'notarized_copyright_20251010_202141_c1222a0d.pdf', '2025-10-10 20:21:42', 41341591, 'application/pdf', 0, NULL, NULL),
(469, 125, 'receipt_payment', 'receipt_payment_20251010_202141_3209bcf2.pdf', '2025-10-10 20:21:42', 12567220, 'application/pdf', 0, NULL, NULL),
(470, 125, 'full_manuscript', 'full_manuscript_20251010_202142_169d03db.pdf', '2025-10-10 20:21:42', 41341591, 'application/pdf', 0, NULL, NULL),
(471, 125, 'notarized_coauthorship', 'notarized_coauthorship_20251010_202142_8e079db7.pdf', '2025-10-10 20:21:42', 41341591, 'application/pdf', 0, NULL, NULL),
(472, 125, 'approval_sheet', 'approval_sheet_20251010_202142_292eb0c6.pdf', '2025-10-10 20:21:42', 12567220, 'application/pdf', 0, NULL, NULL),
(473, 125, 'record_copyright', 'record_copyright_20251010_202142_aba533e5.pdf', '2025-10-10 20:21:42', 41341591, 'application/pdf', 0, NULL, NULL),
(474, 126, 'journal_publication_format', 'journal_publication_format_20251010_203451_97308124.pdf', '2025-10-10 20:34:52', 41341591, 'application/pdf', 0, NULL, NULL),
(475, 126, 'notarized_copyright', 'notarized_copyright_20251010_203452_a1aac8b3.pdf', '2025-10-10 20:34:52', 41341591, 'application/pdf', 0, NULL, NULL),
(476, 126, 'receipt_payment', 'receipt_payment_20251010_203452_e9c05440.pdf', '2025-10-10 20:34:53', 41341591, 'application/pdf', 0, NULL, NULL),
(477, 126, 'presentation', 'presentation_20251010_203452_6941726b.pdf', '2025-10-10 20:34:53', 41341591, 'application/pdf', 0, NULL, NULL),
(478, 126, 'record_copyright', 'record_copyright_20251010_203452_9c7b790b.pdf', '2025-10-10 20:34:53', 41341591, 'application/pdf', 0, NULL, NULL),
(479, 126, 'notarized_coauthorship', 'notarized_coauthorship_20251010_203452_515a6b57.pdf', '2025-10-10 20:34:53', 41341591, 'application/pdf', 0, NULL, NULL),
(480, 127, 'journal_publication_format', 'journal_publication_format_1760138738_8581021b.pdf', '2025-10-11 07:25:38', 1831, 'application/pdf', 0, NULL, NULL),
(481, 127, 'notarized_copyright', 'notarized_copyright_1760138738_13cc0575.pdf', '2025-10-11 07:25:38', 1841, 'application/pdf', 0, NULL, NULL),
(482, 127, 'receipt_payment', 'receipt_payment_1760138738_6e2fdf03.pdf', '2025-10-11 07:25:38', 1821, 'application/pdf', 0, NULL, NULL),
(483, 127, 'full_manuscript', 'full_manuscript_1760138738_73d76f1d.pdf', '2025-10-11 07:25:38', 1822, 'application/pdf', 0, NULL, NULL),
(484, 127, 'notarized_coauthorship', 'notarized_coauthorship_1760138738_428dd8ce.pdf', '2025-10-11 07:25:38', 1831, 'application/pdf', 0, NULL, NULL),
(485, 127, 'approval_sheet', 'approval_sheet_1760138738_b27ebd26.pdf', '2025-10-11 07:25:38', 1831, 'application/pdf', 0, NULL, NULL),
(486, 127, 'record_copyright', 'record_copyright_1760138738_dd57888e.pdf', '2025-10-11 07:25:38', 1835, 'application/pdf', 0, NULL, NULL),
(487, 128, 'journal_publication_format', 'journal_publication_format_20251010_210409_84f50583.pdf', '2025-10-10 21:04:10', 12567220, 'application/pdf', 0, NULL, NULL),
(488, 128, 'notarized_copyright', 'notarized_copyright_20251010_210409_f5be56a0.pdf', '2025-10-10 21:04:10', 12567220, 'application/pdf', 0, NULL, NULL),
(489, 128, 'receipt_payment', 'receipt_payment_20251010_210409_6a3ade20.pdf', '2025-10-10 21:04:10', 12567220, 'application/pdf', 0, NULL, NULL),
(490, 128, 'presentation', 'presentation_20251010_210409_721ccc4c.pdf', '2025-10-10 21:04:10', 12567220, 'application/pdf', 0, NULL, NULL),
(491, 128, 'record_copyright', 'record_copyright_20251010_210409_5fb700f5.pdf', '2025-10-10 21:04:10', 12567220, 'application/pdf', 0, NULL, NULL),
(492, 128, 'notarized_coauthorship', 'notarized_coauthorship_20251010_210410_0c91416e.pdf', '2025-10-10 21:04:10', 12567220, 'application/pdf', 0, NULL, NULL),
(493, 129, 'journal_publication_format', 'journal_publication_format_20251010_235037_a6a954a3.pdf', '2025-10-10 23:50:38', 41341591, 'application/pdf', 0, NULL, NULL),
(494, 129, 'notarized_copyright', 'notarized_copyright_20251010_235037_121d76aa.pdf', '2025-10-10 23:50:38', 41341591, 'application/pdf', 0, NULL, NULL),
(495, 129, 'receipt_payment', 'receipt_payment_20251010_235037_ece3aa03.pdf', '2025-10-10 23:50:38', 41341591, 'application/pdf', 0, NULL, NULL),
(496, 129, 'full_manuscript', 'full_manuscript_20251010_235037_25bcb7fd.pdf', '2025-10-10 23:50:38', 41341591, 'application/pdf', 0, NULL, NULL),
(497, 129, 'notarized_coauthorship', 'notarized_coauthorship_20251010_235037_e9699d6e.pdf', '2025-10-10 23:50:38', 41341591, 'application/pdf', 0, NULL, NULL),
(498, 129, 'approval_sheet', 'approval_sheet_1760178324_2b7dba29.pdf', '2025-10-11 18:25:24', 1831, 'application/pdf', 0, NULL, NULL),
(499, 129, 'record_copyright', 'record_copyright_20251010_235038_e91589d3.pdf', '2025-10-10 23:50:38', 41341591, 'application/pdf', 0, NULL, NULL),
(500, 130, 'journal_publication_format', 'journal_publication_format_20251011_000034_7bbaba72.pdf', '2025-10-11 00:00:35', 12567220, 'application/pdf', 0, NULL, NULL),
(501, 130, 'notarized_copyright', 'notarized_copyright_20251011_000035_f3c98af7.pdf', '2025-10-11 00:00:35', 41341591, 'application/pdf', 0, NULL, NULL),
(502, 130, 'receipt_payment', 'receipt_payment_20251011_000035_06cddc3c.pdf', '2025-10-11 00:00:35', 12567220, 'application/pdf', 0, NULL, NULL),
(503, 130, 'full_manuscript', 'full_manuscript_20251011_000035_59903ddf.pdf', '2025-10-11 00:00:35', 12567220, 'application/pdf', 0, NULL, NULL),
(504, 130, 'notarized_coauthorship', 'notarized_coauthorship_20251011_000035_7a112cd4.pdf', '2025-10-11 00:00:35', 12567220, 'application/pdf', 0, NULL, NULL),
(505, 130, 'approval_sheet', 'approval_sheet_1760188911_fa1523a5.pdf', '2025-10-11 21:21:51', 1831, 'application/pdf', 0, NULL, NULL),
(506, 130, 'record_copyright', 'record_copyright_20251011_000035_49e815db.pdf', '2025-10-11 00:00:35', 12567220, 'application/pdf', 0, NULL, NULL),
(507, 131, 'journal_publication_format', 'journal_publication_format_20251011_000206_1c42b40c.pdf', '2025-10-11 00:02:07', 12567220, 'application/pdf', 0, NULL, NULL),
(508, 131, 'notarized_copyright', 'notarized_copyright_20251011_000206_9db3d9c7.pdf', '2025-10-11 00:02:07', 12567220, 'application/pdf', 0, NULL, NULL),
(509, 131, 'receipt_payment', 'receipt_payment_20251011_000206_eff17075.pdf', '2025-10-11 00:02:07', 12567220, 'application/pdf', 0, NULL, NULL),
(510, 131, 'presentation', 'presentation_20251011_000207_6d9c3af0.pdf', '2025-10-11 00:02:07', 12567220, 'application/pdf', 0, NULL, NULL),
(511, 131, 'record_copyright', 'record_copyright_20251011_000207_0e5dc4d6.pdf', '2025-10-11 00:02:07', 12567220, 'application/pdf', 0, NULL, NULL),
(512, 131, 'notarized_coauthorship', 'notarized_coauthorship_20251011_000207_2fa27e11.pdf', '2025-10-11 00:02:07', 12567220, 'application/pdf', 0, NULL, NULL),
(513, 132, 'journal_publication_format', 'journal_publication_format_20251011_002231_13eaf551.pdf', '2025-10-11 00:22:32', 12567220, 'application/pdf', 0, NULL, NULL),
(514, 132, 'notarized_copyright', 'notarized_copyright_20251011_002231_74619fd3.pdf', '2025-10-11 00:22:32', 12567220, 'application/pdf', 0, NULL, NULL),
(515, 132, 'receipt_payment', 'receipt_payment_20251011_002231_ba4e77d2.pdf', '2025-10-11 00:22:32', 12567220, 'application/pdf', 0, NULL, NULL),
(516, 132, 'presentation', 'presentation_20251011_002231_feed1982.pdf', '2025-10-11 00:22:32', 12567220, 'application/pdf', 0, NULL, NULL),
(517, 132, 'record_copyright', 'record_copyright_20251011_002232_356d93c8.pdf', '2025-10-11 00:22:32', 12567220, 'application/pdf', 0, NULL, NULL),
(518, 132, 'notarized_coauthorship', 'notarized_coauthorship_20251011_002232_2287fa42.pdf', '2025-10-11 00:22:32', 12567220, 'application/pdf', 0, NULL, NULL),
(519, 133, 'journal_publication_format', 'journal_publication_format_1760187647_b3fd7fae.pdf', '2025-10-11 21:00:47', 1831, 'application/pdf', 0, NULL, NULL),
(520, 133, 'notarized_copyright', 'notarized_copyright_1760179244_c163b950.pdf', '2025-10-11 18:40:44', 1841, 'application/pdf', 0, NULL, NULL),
(521, 133, 'receipt_payment', 'receipt_payment_1760188649_0b358f6e.pdf', '2025-10-11 21:17:29', 1821, 'application/pdf', 0, NULL, NULL),
(522, 133, 'full_manuscript', 'full_manuscript_1760188360_8ad430d3.pdf', '2025-10-11 21:12:40', 1822, 'application/pdf', 0, NULL, NULL),
(523, 133, 'notarized_coauthorship', 'notarized_coauthorship_1760181249_89489ba7.pdf', '2025-10-11 19:14:09', 1831, 'application/pdf', 0, NULL, NULL),
(524, 133, 'approval_sheet', 'approval_sheet_1760188919_61a1db00.pdf', '2025-10-11 21:21:59', 1831, 'application/pdf', 0, NULL, NULL),
(525, 133, 'record_copyright', 'record_copyright_20251011_002703_7db42b87.pdf', '2025-10-11 00:27:03', 12567220, 'application/pdf', 0, NULL, NULL);

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
(94, 'pending', 'Incorrect Document/Upload', 'test', 'approval_sheet', '2025-10-08 16:08:44', '2025-10-08 15:55:58', NULL, 0),
(102, 'pending', 'Incorrect Document/Upload', 'test', '', '2025-10-09 20:46:23', '2025-10-09 20:45:55', NULL, 0),
(102, 'approved', 'Error in Document', 'test', 'journal_publication_format|notarized_coauthorship|notarized_copyright|presentation|receipt_payment|record_copyright', '2025-10-09 20:47:55', '2025-10-09 20:46:53', NULL, 0),
(104, 'pending', 'Error in Document/Upload', 'test', 'approval_sheet|full_manuscript|journal_publication_format|notarized_coauthorship|notarized_copyright|receipt_payment|record_copyright', '2025-10-09 21:21:49', '2025-10-09 21:21:49', NULL, 0),
(105, 'pending', NULL, 'test', 'approval_sheet', '2025-10-09 21:23:19', '2025-10-09 21:23:19', NULL, 0),
(106, 'pending', NULL, 'test', '', '2025-10-09 21:24:50', '2025-10-09 21:24:36', NULL, 0),
(107, 'pending', NULL, 'test', 'notarized_coauthorship', '2025-10-09 21:25:42', '2025-10-09 21:25:42', NULL, 0),
(107, 'approved', NULL, 'test', NULL, '2025-10-09 21:26:01', '2025-10-09 21:26:01', NULL, 0),
(108, 'approved', NULL, 'test', NULL, '2025-10-09 21:28:03', '2025-10-09 21:28:03', NULL, 0),
(109, 'approved', NULL, 'gio', NULL, '2025-10-09 22:12:53', '2025-10-09 22:12:53', NULL, 0),
(110, 'pending', NULL, 'test', 'approval_sheet', '2025-10-09 22:12:16', '2025-10-09 22:12:16', NULL, 0),
(110, 'approved', NULL, 'test', NULL, '2025-10-09 22:12:35', '2025-10-09 22:12:35', NULL, 0),
(120, 'pending', 'Error in Document/Upload', NULL, '', '2025-10-11 07:49:41', '2025-10-10 21:01:32', NULL, 0),
(122, 'pending', 'Incorrect Document/Upload', 'test', '', '2025-10-10 15:15:22', '2025-10-10 15:14:47', NULL, 0),
(123, 'pending', NULL, 'test3', '', '2025-10-10 17:55:07', '2025-10-10 17:46:12', NULL, 0),
(123, 'approved', NULL, 'test', 'approval_sheet|full_manuscript|journal_publication_format|notarized_coauthorship|notarized_copyright|receipt_payment|record_copyright', '2025-10-10 18:01:15', '2025-10-10 17:55:25', NULL, 0),
(125, 'approved', NULL, 'test', NULL, '2025-10-10 21:00:01', '2025-10-10 21:00:01', NULL, 0),
(126, 'pending', NULL, 'test', 'journal_publication_format', '2025-10-10 21:00:57', '2025-10-10 21:00:57', NULL, 0),
(127, 'pending', 'Error in Document/Upload', 'test', '', '2025-10-11 07:25:38', '2025-10-10 21:03:05', NULL, 0),
(129, 'pending', 'Error in Document/Upload', 'kulang', '', '2025-10-11 18:25:24', '2025-10-11 09:37:05', NULL, 0),
(129, 'approved', NULL, NULL, 'approval_sheet', '2025-10-11 19:19:16', '2025-10-11 19:19:16', NULL, 0),
(130, 'pending', NULL, NULL, '', '2025-10-11 21:21:51', '2025-10-11 21:21:22', NULL, 0),
(133, 'pending', NULL, NULL, 'journal_publication_format', '2025-10-11 21:22:09', '2025-10-11 08:07:12', NULL, 0);

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
(1, 58, 4, '2025-10-11 17:19:44'),
(5, 86, 4, '2025-10-11 17:19:42');

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
  `created_at` datetime DEFAULT current_timestamp(),
  `password_reset_token` varchar(255) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password`, `role`, `status`, `verification_code`, `code_expires_at`, `email_verified_at`, `created_at`, `password_reset_token`, `token_expiry`) VALUES
(1, 'aceplanetary0@gmail.com', '$2y$10$fquyx3HkPHTLLufrk7sW2OtJ.7/CUbUC6C/n1tpGP0uOoT57GT6wq', 'student', 'active', NULL, NULL, '2025-10-06 03:36:13', '2025-10-06 03:35:45', NULL, NULL),
(2, 'errorloading19990@gmail.com', '$2y$10$qfTnzMS3c0hMXbNxec5KHecE7i7TxdtiXNYJuR4IsSMXRPzkD.16q', 'employee', 'active', NULL, NULL, '2025-10-06 03:39:03', '2025-10-06 03:38:36', NULL, NULL),
(4, 'admin@ipmo.local', '$2y$10$FcSO4or9z9oUzIEIJW/87uNeKHnf9wrdWOdN2w6A/N6E.jHnK2owy', 'admin', 'active', NULL, NULL, '2025-10-06 03:55:43', '2025-10-06 03:55:43', NULL, NULL),
(5, 'inocentesraebv@gmail.com', '$2y$12$MvO.41oeGZKRmqJc6lM8Ueppkcugyq4/0lvZKR5m3ETVlNRPlvx2a', 'student', 'active', '9481d21b6c60ec81', '2025-10-07 22:30:53', NULL, '2025-10-06 22:30:53', NULL, NULL),
(6, 'thinkingwan00@gmail.com', '$2y$12$0y66PWURrNDe6JcjfLEKq./G20X66tAdMqJP3lC6LriVPAPfN4eAS', 'employee', 'active', 'd8b22737708b65f0', '2025-10-08 22:19:24', NULL, '2025-10-07 22:19:24', NULL, NULL),
(7, 'nadaone@gmail.com', '$2y$12$WPx18A4KGzbFRi4CtTJOMuuFtRE.ULuCNxYgklx7VA4S0TUBPL8hS', 'student', 'pending', '5517b8c4d76fa2bf', '2025-10-08 22:41:29', NULL, '2025-10-07 22:41:29', NULL, NULL),
(14, 'hellohihihi1234567890@gmail.com', '$2y$12$6.K2IbpZUjnJzyI3wzVihOb7mDlXiXwZ6tMRECzaXVmdZH9UjL54i', 'student', 'active', '1d501032ef650c70', '2025-10-08 23:58:05', NULL, '2025-10-07 23:58:05', NULL, NULL),
(17, 'markreinier.garcia@gmail.com', '$2y$10$hX2KZmrKJe3HQUKaFthhdOKW2RveycvSO/6/9WeX.7/nmje5d1LEG', 'employee', 'active', NULL, NULL, '2025-10-11 04:01:04', '2025-10-11 04:00:50', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_notifications`
--

CREATE TABLE `user_notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `meta` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_notifications`
--

INSERT INTO `user_notifications` (`id`, `user_id`, `title`, `message`, `meta`, `is_read`, `created_at`, `deleted_at`) VALUES
(12, 1, 'Documents require resubmission', 'Your application SRID-2025-20251010-10 requires resubmission of: approval_sheet. Note: kulang', '{\"submission_id\":129,\"submission_code\":\"SRID-2025-20251010-10\",\"doc_types\":[\"approval_sheet\"]}', 1, '2025-10-11 10:24:15', '2025-10-11 19:49:23'),
(13, 1, 'Documents require resubmission', 'Your application SRID-2025-20251011-4 requires resubmission of: approval_sheet.', '{\"submission_id\":133,\"submission_code\":\"SRID-2025-20251011-4\",\"doc_types\":[\"approval_sheet\"]}', 1, '2025-10-11 11:24:17', '2025-10-11 19:26:57'),
(14, 1, 'Test notification from user panel', 'This is a test notification created at 2025-10-11 18:49:42', '{\"test\":true}', 1, '2025-10-11 10:49:42', '2025-10-11 18:50:09'),
(15, 1, 'Test notification from user panel', 'This is a test notification created at 2025-10-11 18:49:45', '{\"test\":true}', 1, '2025-10-11 10:49:45', '2025-10-11 18:50:06'),
(16, 1, 'Test notification from user panel', 'This is a test notification created at 2025-10-11 18:49:48', '{\"test\":true}', 1, '2025-10-11 10:49:48', '2025-10-11 18:50:04'),
(17, 1, 'Test notification from user panel', 'This is a test notification created at 2025-10-11 18:52:41', '{\"test\":true}', 0, '2025-10-11 10:52:41', '2025-10-11 18:52:43'),
(18, 1, 'Test notification from user panel', 'This is a test notification created at 2025-10-11 18:54:35', '{\"test\":true}', 1, '2025-10-11 10:54:35', '2025-10-11 19:49:23'),
(19, 1, 'Test notification from user panel', 'This is a test notification created at 2025-10-11 19:06:48', '{\"test\":true}', 1, '2025-10-11 11:06:48', '2025-10-11 19:49:23'),
(20, 1, 'Test notification from user panel', 'This is a test notification created at 2025-10-11 19:06:49', '{\"test\":true}', 1, '2025-10-11 11:06:49', '2025-10-11 19:49:23'),
(21, 1, 'Test notification from user panel', 'This is a test notification created at 2025-10-11 19:06:49', '{\"test\":true}', 1, '2025-10-11 11:06:49', '2025-10-11 19:49:23'),
(22, 1, 'Application status updated', 'Your application SRID-2025-20251010-10 status changed to approved.', '{\"submission_id\":129,\"submission_code\":\"SRID-2025-20251010-10\",\"new_status\":\"approved\",\"old_status\":null}', 1, '2025-10-11 11:16:27', '2025-10-11 19:49:23'),
(23, 1, 'Test notification from user panel', 'This is a test notification created at 2025-10-11 19:22:57', '{\"test\":true}', 1, '2025-10-11 11:22:57', '2025-10-11 19:24:09'),
(24, 1, 'Test notification from user panel', 'This is a test notification created at 2025-10-11 19:23:03', '{\"test\":true}', 1, '2025-10-11 11:23:03', '2025-10-11 19:24:07'),
(25, 1, 'Test notification from user panel', 'This is a test notification created at 2025-10-11 19:23:04', '{\"test\":true}', 1, '2025-10-11 11:23:04', '2025-10-11 19:24:05'),
(26, 1, 'Documents require resubmission', 'Your application SRID-2025-20251011-4 requires resubmission of: journal_publication_format.', '{\"submission_id\":133,\"submission_code\":\"SRID-2025-20251011-4\",\"doc_types\":[\"journal_publication_format\"]}', 1, '2025-10-11 11:27:30', '2025-10-11 19:46:22'),
(27, 1, 'Test notification from user panel', 'This is a test notification created at 2025-10-11 19:35:38', '{\"test\":true}', 1, '2025-10-11 11:35:38', '2025-10-11 19:46:22'),
(28, 1, 'Test notification from user panel', 'This is a test notification created at 2025-10-11 19:35:41', '{\"test\":true}', 1, '2025-10-11 11:35:41', '2025-10-11 19:44:33'),
(29, 1, 'Test notification from user panel', 'This is a test notification created at 2025-10-11 19:35:50', '{\"test\":true}', 1, '2025-10-11 11:35:50', '2025-10-11 19:44:33'),
(30, 1, 'Documents require resubmission', 'Your application SRID-2025-20251011-4 requires resubmission of: notarized_coauthorship.', '{\"submission_id\":133,\"submission_code\":\"SRID-2025-20251011-4\",\"doc_types\":[\"notarized_coauthorship\"]}', 0, '2025-10-11 11:54:52', '2025-10-11 19:55:08'),
(31, 1, 'Documents require resubmission', 'Your application SRID-2025-20251011-4 requires resubmission of: journal_publication_format.', '{\"submission_id\":133,\"submission_code\":\"SRID-2025-20251011-4\",\"doc_types\":[\"journal_publication_format\"]}', 1, '2025-10-11 11:56:38', '2025-10-11 19:57:09'),
(32, 1, 'Documents require resubmission', 'Your application SRID-2025-20251011-4 requires resubmission of: journal_publication_format.', '{\"submission_id\":133,\"submission_code\":\"SRID-2025-20251011-4\",\"doc_types\":[\"journal_publication_format\"]}', 1, '2025-10-11 13:22:09', NULL),
(33, 1, 'Documents require resubmission', 'Your application SRID-2025-20251011-1 requires resubmission of: approval_sheet.', '{\"submission_id\":130,\"submission_code\":\"SRID-2025-20251011-1\",\"doc_types\":[\"approval_sheet\"]}', 1, '2025-10-11 13:21:22', NULL);

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
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_notification_type` (`notification_type`),
  ADD KEY `idx_created_at` (`created_at`);

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
-- Indexes for table `resubmission_audit`
--
ALTER TABLE `resubmission_audit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `submission_id` (`submission_id`),
  ADD KEY `user_id` (`user_id`);

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
-- Indexes for table `user_notifications`
--
ALTER TABLE `user_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `is_read` (`is_read`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=137;

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
-- AUTO_INCREMENT for table `employee_profiles`
--
ALTER TABLE `employee_profiles`
  MODIFY `profile_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `resubmission_audit`
--
ALTER TABLE `resubmission_audit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `student_profiles`
--
ALTER TABLE `student_profiles`
  MODIFY `profile_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `submission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- AUTO_INCREMENT for table `submission_authors`
--
ALTER TABLE `submission_authors`
  MODIFY `author_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `submission_documents`
--
ALTER TABLE `submission_documents`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=526;

--
-- AUTO_INCREMENT for table `submission_notes`
--
ALTER TABLE `submission_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `submission_notes_admin_views`
--
ALTER TABLE `submission_notes_admin_views`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `user_notifications`
--
ALTER TABLE `user_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

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

--
-- Constraints for table `submissions`
--
ALTER TABLE `submissions`
  ADD CONSTRAINT `fk_submissions_reviewer_id` FOREIGN KEY (`reviewer_id`) REFERENCES `admin_profiles` (`profile_id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
