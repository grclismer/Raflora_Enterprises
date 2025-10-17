-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 17, 2025 at 08:36 AM
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
-- Database: `raflora_enterprises`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts_tbl`
--

CREATE TABLE `accounts_tbl` (
  `user_id` bigint(50) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mobile_number` bigint(50) NOT NULL,
  `role` varchar(50) NOT NULL,
  `profile_picture` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounts_tbl`
--

INSERT INTO `accounts_tbl` (`user_id`, `first_name`, `last_name`, `user_name`, `password`, `address`, `email`, `mobile_number`, `role`, `profile_picture`) VALUES
(4, '', '', 'admin_user', '$2y$10$rplwzyOazn8xVHdnyKQZUO.p79ZPzAnxEr.jbv.c2QxEtairNlzuS', '', 'rafloraenterprises14@gmail.com', 0, 'admin_type', ''),
(5, 'justine', 'salido', 'justinesalido22', '$2y$10$zHJ9FzHTkjJi2u5KaLpVM.ENgJWB6lDUXmPJSoX3SDgrMyCaiYXRO', 'blk 11 lot 6 kawal caloocan', 'justinemedice17@gmail.com', 9668662989, 'client_type', ''),
(7, 'Lismer', 'Nadonza', 'lismernadonza24', '$2y$10$mP.mOlUpPn5yITNFWorAjuCrv7kv38yP8OZiSTsbbg3tpqaEM3KNe', '4224 blk Caloocan City', 'lismernadonza09@gmail.com', 9773436195, 'client_type', 'uploads/profile_pictures/profile_7_1760642657.jpg'),
(8, 'lismer', 'Palce', 'lismerpalce24', '$2y$10$vqxaIcJtd/BJllsEI1IbzuolLr3qqk8CyvNwQZndRS32CrF1Jalqa', '4224 blk69 Sawata kaloocan city', 'lismerjohnnadonza@gmail.com', 9773436195, 'client_type', '');

-- --------------------------------------------------------

--
-- Table structure for table `booking_tbl`
--

CREATE TABLE `booking_tbl` (
  `booking_id` int(11) NOT NULL,
  `user_id` bigint(50) NOT NULL,
  `package_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `mobile_number` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `event_theme` varchar(50) NOT NULL,
  `packages` varchar(100) NOT NULL,
  `event_date` date NOT NULL,
  `event_time` time NOT NULL,
  `recommendations` text DEFAULT NULL,
  `design_document_path` varchar(255) DEFAULT NULL,
  `price_details` text DEFAULT NULL,
  `final_price` decimal(10,2) NOT NULL,
  `amount_due` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `payment_details` varchar(100) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `payment_type` varchar(50) NOT NULL,
  `booking_status` varchar(50) NOT NULL DEFAULT 'UNPAID',
  `reference_number` varchar(100) DEFAULT NULL,
  `payment_proof_path` varchar(255) DEFAULT NULL,
  `amount_paid_submitted` decimal(10,2) DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `rejection_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_tbl`
--

INSERT INTO `booking_tbl` (`booking_id`, `user_id`, `package_id`, `full_name`, `mobile_number`, `email`, `address`, `event_theme`, `packages`, `event_date`, `event_time`, `recommendations`, `design_document_path`, `price_details`, `final_price`, `amount_due`, `payment_method`, `payment_details`, `total_price`, `payment_type`, `booking_status`, `reference_number`, `payment_proof_path`, `amount_paid_submitted`, `payment_date`, `created_at`, `updated_at`, `rejection_reason`) VALUES
(119, 7, 4, 'lismernadonza24', '09773436195', 'lismernadonza@gmail.com', 'Caloocan City', 'birthday party', 'Royal Gala', '2025-10-15', '06:05:00', 'Test 123', 'assets/uploads/default.jpg', NULL, 0.00, 15000.00, '', 'Not Applicable', 30000.00, '', 'REJECTED', '121212121212121', NULL, NULL, NULL, '2025-10-14 22:11:20', '2025-10-14 22:17:26', 'cancel'),
(120, 4, 2, 'Lismer Nadonza', '09773436195', 'lismernadonza@gmail.com', 'Caloocan City', 'wedding', 'Emerald Package', '2025-10-06', '23:11:00', 'New test', 'assets/uploads/4_1760480344.pdf', NULL, 0.00, 42500.00, '', 'Not Applicable', 85000.00, '', 'APPROVED', '1212434323124r4132', NULL, NULL, NULL, '2025-10-14 22:19:04', '2025-10-14 23:10:36', NULL),
(121, 7, 8, 'lismer palce', '09773436195', 'lismerjohnnadonza@gmail.com', '4224 caloocan city', 'reunion', 'Basic Photo Op', '2025-10-17', '02:01:00', 'SEFD', 'assets/uploads/default.jpg', NULL, 0.00, 4000.00, 'Online Bank', 'Metrobank', 8000.00, 'Down Payment', 'COMPLETED', '3R56234DTY46455633FFFR44444', NULL, NULL, NULL, '2025-10-16 18:01:34', '2025-10-16 23:49:07', NULL),
(122, 7, 3, 'lismer palce', '09773436195', 'lismerjohnnadonza@gmail.com', '4224 caloocan city', 'birthday party', 'Grand Duke', '2025-10-17', '03:26:00', 'awd', 'assets/uploads/default.jpg', NULL, 0.00, 15000.00, 'Online Bank', 'Landbank', 15000.00, 'Full Payment', 'APPROVED', '222222222222222222222222', NULL, NULL, NULL, '2025-10-16 19:26:13', '2025-10-16 21:58:43', NULL),
(123, 7, 6, 'justine salido', '55555555555', 'lismerjohnnadonza@gmail.com', '4224 caloocan city', 'hotel / corporate', 'Gold Corporate Setup', '2025-10-17', '03:35:00', 'ad', 'assets/uploads/default.jpg', NULL, 0.00, 22500.00, 'Online Bank', '', 45000.00, 'Down Payment', 'PENDING_ORDER_CONFIRMATION', NULL, NULL, NULL, NULL, '2025-10-16 19:35:43', '2025-10-16 19:35:43', NULL),
(124, 7, 7, 'justine salido', '55555555555', 'lismerjohnnadonza@gmail.com', '4224 caloocan city', 'hotel / corporate', 'Silver Business Setup', '2025-10-17', '03:35:00', 'ad', 'assets/uploads/default.jpg', NULL, 0.00, 12500.00, 'Online Bank', '', 25000.00, 'Down Payment', 'PENDING_ORDER_CONFIRMATION', NULL, NULL, NULL, NULL, '2025-10-16 19:36:41', '2025-10-16 19:36:41', NULL),
(125, 7, 7, 'justine salido', '55555555555', 'lismerjohnnadonza@gmail.com', '4224 caloocan city', 'hotel / corporate', 'Silver Business Setup', '2025-10-17', '03:35:00', 'ad', 'assets/uploads/default.jpg', NULL, 0.00, 12500.00, 'Online Bank', 'Paypal', 25000.00, 'Down Payment', 'APPROVED', '1514514541542', NULL, NULL, NULL, '2025-10-16 19:36:59', '2025-10-17 01:10:46', NULL),
(126, 7, 3, 'justine salido', '55555555555', 'lismerjohnnadonza@gmail.com', '4224 caloocan city', 'birthday party', 'Grand Duke', '2025-10-17', '03:38:00', 'awd', 'assets/uploads/default.jpg', NULL, 0.00, 7500.00, 'Online Bank', 'UnionBank', 15000.00, 'Down Payment', 'APPROVED', '151451454154', NULL, NULL, NULL, '2025-10-16 19:38:46', '2025-10-17 01:10:42', NULL),
(127, 7, 5, 'lismer palce', '09773436195', 'lismerjohnnadonza@gmail.com', '4224 caloocan city', 'reunion', 'Standard Reunion', '2025-10-17', '06:07:00', 'Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking\r\n\r\nLismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking\r\n\r\nLismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking\r\n\r\nLismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking\r\n\r\nLismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking\r\n\r\nLismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking\r\n\r\nLismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking\r\n\r\nLismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking\r\n\r\nLismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking\r\n\r\nLismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking\r\n\r\nLismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking\r\n\r\nLismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking\r\n\r\nLismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking\r\n\r\nLismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking\r\n\r\nLismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking\r\n\r\nLismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking\r\n\r\nLismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking\r\n\r\nLismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking\r\n\r\nLismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking\r\n\r\nLismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking\r\n\r\nLismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking\r\n\r\nLismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking\r\n\r\nLismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking\r\n\r\nLismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking\r\n\r\nLismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking\r\n\r\nLismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking\r\n\r\nLismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking\r\n\r\nLismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking\r\n\r\nLismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking\r\n\r\nLismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking\r\n\r\nLismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking\r\n\r\nLismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking Lismer Test Booking', 'assets/uploads/default.jpg', NULL, 0.00, 12000.00, 'E-Wallet', '', 12000.00, 'Full Payment', 'PENDING_ORDER_CONFIRMATION', NULL, NULL, NULL, NULL, '2025-10-16 22:08:30', '2025-10-16 22:08:30', NULL),
(128, 7, 5, 'lismer palce', '09773436195', 'lismerjohnnadonza@gmail.com', '4224 caloocan city', 'reunion', 'Standard Reunion', '2025-10-17', '14:17:00', 'awd awd', 'assets/uploads/default.jpg', NULL, 0.00, 6000.00, 'Online Bank', 'UnionBank', 12000.00, 'Down Payment', 'PENDING_PAYMENT_VERIFICATION', '5514554515154', NULL, NULL, NULL, '2025-10-17 06:17:37', '2025-10-17 06:17:56', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `inventory_tbl`
--

CREATE TABLE `inventory_tbl` (
  `id` int(11) NOT NULL,
  `item_id` varchar(10) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `category` enum('tools','equipment','supplies') NOT NULL,
  `status` enum('available','unavailable') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_tbl`
--

INSERT INTO `inventory_tbl` (`id`, `item_id`, `item_name`, `quantity`, `category`, `status`, `created_at`, `updated_at`) VALUES
(1, '01', 'Wire Cutter', 15, 'tools', 'available', '2025-10-14 22:08:53', '2025-10-14 23:31:11'),
(2, '02', 'Hammer', 10, 'tools', 'available', '2025-10-14 22:08:53', '2025-10-14 23:31:11'),
(3, '03', 'Glue Gun', 8, 'tools', 'available', '2025-10-14 22:08:53', '2025-10-14 23:31:11'),
(4, '04', 'Clipper', 12, 'tools', 'available', '2025-10-14 22:08:53', '2025-10-14 23:31:11'),
(5, '05', 'Tucker (Staple Gun)', 6, 'tools', 'available', '2025-10-14 22:08:53', '2025-10-14 23:31:11'),
(6, '06', 'Scissors', 20, 'equipment', 'available', '2025-10-14 22:08:53', '2025-10-14 23:31:11'),
(7, '07', 'Ladder', 10, 'equipment', 'available', '2025-10-14 22:08:53', '2025-10-16 18:40:13'),
(8, '08', 'Floral Tape', 50, 'supplies', 'available', '2025-10-14 22:08:53', '2025-10-14 23:31:11'),
(9, '09', 'Leafshine Spray', 25, 'supplies', 'available', '2025-10-14 22:08:53', '2025-10-14 23:31:11'),
(10, '10', 'Floral Spray', 30, 'supplies', 'available', '2025-10-14 22:08:53', '2025-10-14 23:31:11'),
(11, '11', 'Ribbon', 40, 'supplies', 'available', '2025-10-14 22:08:53', '2025-10-14 23:31:11'),
(12, '12', 'Chicken Wire', 18, 'supplies', 'available', '2025-10-14 22:08:53', '2025-10-14 23:31:11'),
(13, '13', 'Cable Wire', 22, 'supplies', 'available', '2025-10-14 22:08:53', '2025-10-14 23:31:11'),
(14, '14', 'Cable Tie', 35, 'supplies', 'available', '2025-10-14 22:08:53', '2025-10-14 23:31:11'),
(15, '15', 'Floral Paper', 28, 'supplies', 'available', '2025-10-14 22:08:53', '2025-10-14 23:31:11'),
(16, '16', 'Floral Foam', 32, 'supplies', 'available', '2025-10-14 22:08:53', '2025-10-14 23:31:11');

-- --------------------------------------------------------

--
-- Table structure for table `package_details_tbl`
--

CREATE TABLE `package_details_tbl` (
  `package_id` int(11) NOT NULL,
  `package_name` varchar(100) NOT NULL,
  `event_type` varchar(50) NOT NULL,
  `fixed_price` decimal(10,2) NOT NULL,
  `inclusions` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `package_details_tbl`
--

INSERT INTO `package_details_tbl` (`package_id`, `package_name`, `event_type`, `fixed_price`, `inclusions`, `is_active`, `created_at`) VALUES
(1, 'Sapphire Package', 'Wedding', 50000.00, 'Floral Arch Backdrop\nBridal Bouquet and Entourage Flowers\nBasic Table Centerpieces\nStandard Venue Decor', 1, '2025-10-04 12:11:07'),
(2, 'Emerald Package', 'Wedding', 85000.00, 'Premium Stage and Aisle Decor\nCustomized Bridal Bouquet\nLuxury Floral Centerpieces (Max 10 tables)\nCeiling Swags and Lights', 1, '2025-10-04 12:11:07'),
(3, 'Grand Duke', 'Birthday Party', 15000.00, 'Balloon Arch or Basic Backdrop\nCake Table Setup\n10 Pax Table Centerpieces\nBasic Party Lights', 1, '2025-10-04 12:11:07'),
(4, 'Royal Gala', 'Birthday Party', 30000.00, 'Custom Theme Backdrop (Themed)\nFull Dessert Buffet Decor\n3D Character Cutouts\nEnhanced Venue Lighting', 1, '2025-10-04 12:11:07'),
(5, 'Standard Reunion', 'Reunion', 12000.00, 'Simple Photo Booth Backdrop\nGuest Registration Table Decor\nBasic Lighting', 1, '2025-10-04 12:11:07'),
(6, 'Gold Corporate Setup', 'Hotel / Corporate', 45000.00, 'Stage Design with Logo Placeholder\nPodium Floral Arrangement\nRed Carpet and Aisle Decor\nStandard Lighting Package', 1, '2025-10-04 12:11:07'),
(7, 'Silver Business Setup', 'Hotel / Corporate', 25000.00, 'Basic Stage Banner\nSimple Entryway Floral', 1, '2025-10-04 12:11:07'),
(8, 'Basic Photo Op', 'Reunion', 8000.00, 'Simple Floral/Balloon Wall for Photos', 1, '2025-10-04 12:11:07');

-- --------------------------------------------------------

--
-- Table structure for table `payments_tbl`
--

CREATE TABLE `payments_tbl` (
  `payment_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `payment_type` enum('down_payment','final_payment','full_payment') NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `payment_channel` varchar(100) NOT NULL,
  `reference_number` varchar(100) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','completed','failed','cancelled') DEFAULT 'pending',
  `verified_by` int(11) DEFAULT NULL COMMENT 'References users table',
  `verified_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments_tbl`
--

INSERT INTO `payments_tbl` (`payment_id`, `booking_id`, `amount_paid`, `payment_type`, `payment_method`, `payment_channel`, `reference_number`, `payment_date`, `status`, `verified_by`, `verified_at`, `notes`, `created_at`, `updated_at`) VALUES
(1, 121, 4000.00, 'down_payment', 'Online Bank', 'Metrobank', '3R56234DTY46455633FFFR44444', '2025-10-16 18:01:49', 'completed', NULL, NULL, NULL, '2025-10-16 18:01:49', '2025-10-16 23:49:07'),
(2, 122, 15000.00, 'full_payment', 'Online Bank', 'Landbank', '222222222222222222222222', '2025-10-16 19:26:19', '', NULL, NULL, NULL, '2025-10-16 19:26:19', '2025-10-16 21:58:43'),
(3, 126, 7500.00, 'down_payment', 'Online Bank', 'UnionBank', '151451454154', '2025-10-17 01:10:11', '', NULL, NULL, NULL, '2025-10-17 01:10:11', '2025-10-17 01:10:42'),
(4, 125, 12500.00, 'down_payment', 'Online Bank', 'Paypal', '1514514541542', '2025-10-17 01:10:24', '', NULL, NULL, NULL, '2025-10-17 01:10:24', '2025-10-17 01:10:46'),
(5, 128, 6000.00, 'down_payment', 'Online Bank', 'UnionBank', '5514554515154', '2025-10-17 06:17:56', 'pending', NULL, NULL, NULL, '2025-10-17 06:17:56', '2025-10-17 06:17:56');

-- --------------------------------------------------------

--
-- Table structure for table `qr_login_sessions`
--

CREATE TABLE `qr_login_sessions` (
  `session_id` varchar(64) NOT NULL,
  `user_id` bigint(50) NOT NULL,
  `qr_code_data` text NOT NULL,
  `status` enum('pending','used','expired') DEFAULT 'pending',
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `qr_login_sessions`
--

INSERT INTO `qr_login_sessions` (`session_id`, `user_id`, `qr_code_data`, `status`, `expires_at`, `created_at`) VALUES
('a2fd5d8b4b47e62de197995e6067ac34dfdfbf3b646641f751b8a5a1ab0db6a3', 7, '{\"session_id\":\"a2fd5d8b4b47e62de197995e6067ac34dfdfbf3b646641f751b8a5a1ab0db6a3\",\"user_id\":7,\"action\":\"login\",\"timestamp\":1760645733}', 'pending', '2025-10-16 22:20:33', '2025-10-16 20:15:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts_tbl`
--
ALTER TABLE `accounts_tbl`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `booking_tbl`
--
ALTER TABLE `booking_tbl`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `package_id` (`package_id`);

--
-- Indexes for table `inventory_tbl`
--
ALTER TABLE `inventory_tbl`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `item_id` (`item_id`);

--
-- Indexes for table `package_details_tbl`
--
ALTER TABLE `package_details_tbl`
  ADD PRIMARY KEY (`package_id`),
  ADD UNIQUE KEY `package_name_unique` (`package_name`);

--
-- Indexes for table `payments_tbl`
--
ALTER TABLE `payments_tbl`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `idx_booking_id` (`booking_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_payment_date` (`payment_date`),
  ADD KEY `idx_reference_number` (`reference_number`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `qr_login_sessions`
--
ALTER TABLE `qr_login_sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts_tbl`
--
ALTER TABLE `accounts_tbl`
  MODIFY `user_id` bigint(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `booking_tbl`
--
ALTER TABLE `booking_tbl`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=129;

--
-- AUTO_INCREMENT for table `inventory_tbl`
--
ALTER TABLE `inventory_tbl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `package_details_tbl`
--
ALTER TABLE `package_details_tbl`
  MODIFY `package_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `payments_tbl`
--
ALTER TABLE `payments_tbl`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking_tbl`
--
ALTER TABLE `booking_tbl`
  ADD CONSTRAINT `fk_booking_package` FOREIGN KEY (`package_id`) REFERENCES `package_details_tbl` (`package_id`),
  ADD CONSTRAINT `fk_booking_user` FOREIGN KEY (`user_id`) REFERENCES `accounts_tbl` (`user_id`),
  ADD CONSTRAINT `fk_booking_user_account` FOREIGN KEY (`user_id`) REFERENCES `accounts_tbl` (`user_id`);

--
-- Constraints for table `payments_tbl`
--
ALTER TABLE `payments_tbl`
  ADD CONSTRAINT `fk_payment_booking` FOREIGN KEY (`booking_id`) REFERENCES `booking_tbl` (`booking_id`);

--
-- Constraints for table `qr_login_sessions`
--
ALTER TABLE `qr_login_sessions`
  ADD CONSTRAINT `qr_login_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `accounts_tbl` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
