-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: sdb-81.hosting.stackcp.net
-- Generation Time: Apr 17, 2025 at 12:24 PM
-- Server version: 10.11.10-MariaDB-log
-- PHP Version: 8.3.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jumandigas-353038374f79`
--

-- --------------------------------------------------------

--
-- Table structure for table `alerts`
--

CREATE TABLE `alerts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','success','warning','error') NOT NULL DEFAULT 'info',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bike`
--

CREATE TABLE `bike` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `country` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL,
  `bike` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `currency` varchar(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bike`
--

INSERT INTO `bike` (`id`, `country`, `state`, `city`, `bike`, `price`, `currency`, `created_at`, `updated_at`) VALUES
(1, 'Nigeria', 'Lagos', 'Ikeja', '', 600.00, 'NGN (₦)', '2025-02-28 10:52:22', '2025-03-02 17:47:33');

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `complaint_type` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` enum('pending','resolved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `deposits`
--

CREATE TABLE `deposits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(50) NOT NULL,
  `payment_method` enum('card','bank','ussd') NOT NULL,
  `status` enum('pending','successful','failed') DEFAULT 'pending',
  `transaction_id` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `country` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `gas` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `currency` varchar(10) NOT NULL,
  `gas_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `country`, `state`, `gas`, `price`, `currency`, `gas_image`, `created_at`, `updated_at`) VALUES
(1, 'Nigeria', 'Lagos', '1kg', 200.00, 'NGN (₦)', '../uploads/gas_images/gas_67b40aed784b80.46646329.PNG', '2025-02-18 04:22:05', '2025-02-18 04:22:05');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `rider_id` bigint(20) UNSIGNED DEFAULT NULL,
  `cylinder_type` varchar(255) NOT NULL,
  `exchange` varchar(255) NOT NULL,
  `amount_kg` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `currency` varchar(50) NOT NULL,
  `tracking_id` varchar(255) NOT NULL,
  `status` enum('pending','processing','moving','delivered','canceled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `assigned_at` timestamp NULL DEFAULT NULL,
  `reassigned` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `vendor_id`, `rider_id`, `cylinder_type`, `exchange`, `amount_kg`, `total_price`, `currency`, `tracking_id`, `status`, `created_at`, `updated_at`, `assigned_at`, `reassigned`) VALUES
(11, 1, 3, 5, '1kg', 'exchange', 6, 1800.00, '₦', 'TRK67C49F3B39610', 'canceled', '2025-03-02 18:11:07', '2025-03-30 17:45:21', NULL, 0),
(12, 1, 3, 5, '1kg', 'exchange', 2, 1000.00, '₦', 'TRK67C4E0E844A2C', 'processing', '2025-03-02 22:51:20', '2025-03-30 17:43:33', NULL, 0),
(13, 1, 3, 5, '3kg', 'exchange', 3, 1200.00, '₦', 'TRK67C4E3026722A', 'delivered', '2025-03-02 23:00:18', '2025-03-30 17:45:39', NULL, 0),
(14, 1, 3, 5, '3kg', 'exchange', 1, 800.00, '?', 'TRKB66390BE3C', 'moving', '2025-03-30 10:30:15', '2025-03-30 17:52:18', NULL, 0),
(17, 1, 3, NULL, '1kg', 'exchange', 1, 800.00, 'NGN', 'TRK6A3C1D887C', 'pending', '2025-04-03 14:18:06', '2025-04-03 14:18:06', NULL, 0),
(18, 1, 3, NULL, '5kg', 'exchange', 5, 1600.00, 'NGN', 'TRKC3BBA66E70', 'pending', '2025-04-09 09:50:47', '2025-04-09 09:50:47', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `order_status_history`
--

CREATE TABLE `order_status_history` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_history`
--

CREATE TABLE `payment_history` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(10) NOT NULL,
  `tx_ref` varchar(50) NOT NULL,
  `status` enum('successful','failed') NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `payment_history`
--

INSERT INTO `payment_history` (`id`, `user_id`, `amount`, `currency`, `tx_ref`, `status`, `created_at`) VALUES
(1, 1, 1000.00, 'USD', 'TX_1741811254', 'successful', '2025-03-12 20:27:35'),
(2, 1, 600.00, 'USD', 'TX_1742052101', 'successful', '2025-03-15 15:21:41'),
(3, 1, 600.00, 'USD', 'TX_1742052196', 'successful', '2025-03-15 15:23:16'),
(4, 1, 600.00, 'NGN', 'TX_1742052712', 'successful', '2025-03-15 15:31:52'),
(7, 1, 2000.00, '?', 'TX_1743081376_1', '', '2025-03-27 13:16:16'),
(8, 1, 20000.00, '?', 'TX_1743081467_1', '', '2025-03-27 13:17:47'),
(9, 9, 2500.00, 'NGN', 'TX_1743223657', 'successful', '2025-03-29 04:47:37'),
(10, 1, 1000.00, '?', 'TX_1743307215_1', '', '2025-03-30 04:00:15'),
(14, 10, 100.00, 'NGN', 'TX_1743336325', 'successful', '2025-03-30 12:05:25'),
(15, 9, 2500.00, 'NGN', 'TX_1743355279', 'successful', '2025-03-30 17:21:20'),
(16, 9, 2500.00, 'NGN', 'TX_1743355318', 'successful', '2025-03-30 17:21:58'),
(17, 12, 10000.00, 'NGN', 'TX_1743360385', 'successful', '2025-03-30 18:46:25'),
(18, 13, 1000.00, 'NGN', 'TX_1743360419', 'successful', '2025-03-30 18:46:59'),
(19, 11, 1000.00, 'NGN', 'TX_1743360434', 'successful', '2025-03-30 18:47:14'),
(20, 12, 10000.00, 'NGN', 'TX_1743360439', 'successful', '2025-03-30 18:47:19'),
(21, 14, 100000.00, 'NGN', 'TX_1743360470', 'successful', '2025-03-30 18:47:50'),
(22, 13, 1000.00, 'NGN', 'TX_1743360540', 'successful', '2025-03-30 18:49:00'),
(23, 11, 1000.00, 'NGN', 'TX_1743360638', 'successful', '2025-03-30 18:50:38'),
(24, 11, 1000.00, 'NGN', 'TX_1743360727', 'successful', '2025-03-30 18:52:08'),
(25, 11, 1000.00, 'NGN', 'TX_1743360991', 'successful', '2025-03-30 18:56:31'),
(26, 1, 1000.00, 'NGN', 'TX_1743364582_1', '', '2025-03-30 19:56:22'),
(27, 1, 1000.00, 'NGN', 'TX_1743364607_1', '', '2025-03-30 19:56:47'),
(28, 1, 1000.00, 'NGN', 'TX_1743364982_1', '', '2025-03-30 20:03:02'),
(29, 1, 1000.00, 'NGN', 'TX_1744118654_1', '', '2025-04-08 13:24:14'),
(30, 1, 100.00, 'NGN', 'TX_1744439507_1', '', '2025-04-12 06:31:47'),
(31, 1, 1000.00, 'NGN', 'TX_1744439628_1', '', '2025-04-12 06:33:48'),
(32, 1, 1000.00, 'NGN', 'TX_1744439628_1', '', '2025-04-12 06:33:48'),
(33, 1, 1000.00, 'NGN', 'TX_1744442983_1', '', '2025-04-12 07:29:43'),
(34, 1, 1000.00, 'NGN', 'TX_1744444373_1', '', '2025-04-12 07:52:53'),
(35, 1, 1000.00, 'NGN', 'TX_1744444441_1', '', '2025-04-12 07:54:01'),
(36, 1, 1000.00, 'NGN', 'TX_1744444460_1', '', '2025-04-12 07:54:20'),
(37, 1, 1000.00, 'NGN', 'TX_1744444492_1', '', '2025-04-12 07:54:52'),
(38, 1, 1000.00, 'NGN', 'TX_1744444615_1', '', '2025-04-12 07:56:55'),
(39, 1, 1000.00, 'NGN', 'TX_1744444655_1', '', '2025-04-12 07:57:35'),
(40, 1, 1000.00, 'NGN', 'TX_1744444730_1', '', '2025-04-12 07:58:50'),
(41, 1, 1000.00, 'NGN', 'TX_1744446148_1', '', '2025-04-12 08:22:28'),
(42, 1, 1000.00, 'NGN', 'TX_1744446160_1', '', '2025-04-12 08:22:40'),
(43, 1, 1000.00, 'NGN', 'TX_1744446177_1', '', '2025-04-12 08:22:57'),
(44, 21, 2000.00, 'NGN', 'TX_1744572118', 'successful', '2025-04-13 19:21:58'),
(45, 1, 1000.00, 'NGN', 'TX_1744710353_1', '', '2025-04-15 09:45:53'),
(46, 1, 2000.00, 'NGN', 'TX_1744710377_1', '', '2025-04-15 09:46:17'),
(47, 1, 2000.00, 'NGN', 'TX_1744710431_1', '', '2025-04-15 09:47:11'),
(48, 1, 1000.00, 'NGN', 'TX_1744710723_1', '', '2025-04-15 09:52:03'),
(49, 1, 2000.00, 'NGN', 'TX_1744745081_1', '', '2025-04-15 19:24:41');

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `review` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `riders`
--

CREATE TABLE `riders` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `rider_id` int(11) DEFAULT NULL,
  `track_id` varchar(50) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `canceled_by_rider` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `state` varchar(255) NOT NULL,
  `role` enum('user','vendor','rider','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `city` varchar(255) NOT NULL,
  `currency` varchar(50) NOT NULL,
  `country` varchar(255) NOT NULL,
  `balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `online` tinyint(1) NOT NULL DEFAULT 0,
  `api_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `password`, `address`, `state`, `role`, `created_at`, `updated_at`, `city`, `currency`, `country`, `balance`, `online`, `api_token`) VALUES
(1, 'Ajose Moshood', 'amoshood06@gmail.com', '+2348146883083', '$2y$10$hBpaRM5ZX5AzT6gvpja3CudEN/tcRhByAKQLh49AQGz/Y3mh2Lzf6', 'Limca, Badagry, Lagos', 'Lagos', 'user', '2025-03-02 14:12:35', '2025-04-15 19:21:20', 'Ikeja', 'NGN', 'nigeria', 1000.00, 1, '5d634bcd9f4abc5435b3c44d100e309e9896b2292241ec0aaa1702ca1705df8f'),
(3, 'John John', 'vendor1@gmail.com', '+2348146883993', '$2y$10$eLcha4OoXK.GKtWpHx1vIeCYWNkZwqaGYHBZJZy4IKjn8fAKZHPuu', 'Limca, Badagry, Lagos', 'lagos', 'vendor', '2025-03-02 14:16:48', '2025-04-09 09:50:47', 'ikeja', 'NGN', 'nigeria', 103160.00, 0, 'fe99563e84edcb8e8ac4785bc5e5edae61dce55158fb3f1a70c8c4211bd46488'),
(5, 'David Michael', 'rider1@gmail.com', '+2340146883993', '$2y$10$83YndsMNxjeva3co3ErLsuyXs9U3Xonn7EK06pWyhqRkOjBRJwP3q', 'Limca, Badagry, Lagos', 'lagos', 'rider', '2025-03-02 22:27:43', '2025-04-17 11:19:34', 'ikeja', 'NGN', 'nigeria', 0.00, 1, '0183cb32cd0ec2a769aa09d7c002715477624f4884a661fb3d1cf57807f23676'),
(9, 'Chukwuma Nnedinma Rhema', 'chukwumarhema0@gmail.com', '09031238225', '$2y$10$idlouZ9x8SkzfSaXirr9uOr73moYaDy.QFG6uFX6kT6898Vu30BQq', 'Mbutu umuawuka', 'lagos', 'user', '2025-03-29 04:45:44', '2025-03-30 17:33:41', 'ikeja', 'NGN', 'nigeria', 0.00, 1, NULL),
(10, 'Joseph Mandiya', 'josephmandiya991@gmail.com', '+233542860550', '$2y$10$R07xRUQjvGX3i30fHzLj2.Eru8Acvh1EE4gx1bFkNnxXKa.n2/hfi', 'Salem Estate NS06 Malejor', 'greater accra', 'user', '2025-03-30 12:03:19', '2025-03-30 12:03:44', 'adenta', '₵', 'ghana', 0.00, 1, NULL),
(11, 'Moses Joshua Chinaecherem', 'iamjoshgrant0@gmail.com', '+2348160079852', '$2y$10$6ZoC/6JITRqoV6MC7nVlIOgDck6fkqK03ys0fzLJKTXbXbV6DpKiK', 'Emmanuel Suite Umuoma Extension Nekede', 'imo', 'user', '2025-03-30 18:45:24', '2025-03-30 18:45:58', '', '₦', 'nigeria', 0.00, 1, NULL),
(12, 'John Smart', 'johnnysmart48@gmail.com', '+2347034519262', '$2y$10$JOft5kmMJo7/GXY9WMohve4zl6oPNOFA8pAvyDeSPEeH0mwNtT/3a', 'Owerri Imo State', 'imo', 'user', '2025-03-30 18:45:26', '2025-03-30 18:45:45', 'owerri', '₦', 'nigeria', 0.00, 1, NULL),
(13, 'Enyio Prince', 'prinzyblaze1@gmail.com', '+2348039490643', '$2y$10$qkpyPxzQSqgFTm2d05Xqc.fbuX2QbXYnVPlZx.OKa8XTFuMLofb22', 'Naze ezeakiri Owerri imo state', 'imo', 'user', '2025-03-30 18:45:40', '2025-03-30 18:46:04', 'owerri', '₦', 'nigeria', 0.00, 1, NULL),
(14, 'Elijah', 'bayernfred42@gmail.com', '08142950843', '$2y$10$RMdfJsDv8MQzYJx00bSVDu9pMjoAz9iBtqXgRsJ6PdtktTZLIUXcC', 'Poly junction', 'imo', 'user', '2025-03-30 18:45:53', '2025-03-30 19:03:44', 'owerri', '₦', 'nigeria', 0.00, 1, '7e32d7995378a77a37960466288a84f97c18c37029d4aca9892675c1e786704e'),
(15, 'Love choice', 'choicelove28@gmail.com', '08145740104', '$2y$10$PVcHr3ga2vuF/UOpFJClwexOBckPGKmTC4XT5YbzD2d3xf6LfvUF.', 'Lucy lodge umuerim', 'imo', 'user', '2025-03-30 18:47:54', '2025-03-30 18:48:08', 'owerri', '₦', 'nigeria', 0.00, 1, NULL),
(16, 'Desmond chinedu', 'desmondpasi99@gmail.com', '08140917199', '$2y$10$6raSRLajJcHIqHC4NDH1ROoHFLa0aH.OAz29A5ZULU0FVFfHlZlBu', 'Nekede', 'imo', 'user', '2025-03-30 18:48:37', '2025-03-30 18:49:06', 'owerri', '₦', 'nigeria', 0.00, 1, NULL),
(17, 'OJIMADU MARYFAVOUR CHIDERA', 'ojimaduchidera5@gmail.com', '08088419668', '$2y$10$cko/lHUjCwdArjd6doULeelJ1ZcIa72umnIO5HVYnbZkkdZwQOB3O', 'Nekede back gate', 'imo', 'user', '2025-03-30 18:56:12', '2025-03-30 18:56:12', 'owerri', '₦', 'nigeria', 0.00, 0, NULL),
(18, 'Agor Onyenkwerechidiebube', 'ebubediamonde@gmail.com', '08069368589', '$2y$10$nWUGmBh8GvBgYrFOnLAB7OwWHQcu4y.5Wy6ubhJDm5rafDLMespqi', 'Umuezeta Rood', 'abia', '', '2025-03-30 19:06:26', '2025-03-30 19:06:46', '', '', 'nigeria', 0.00, 1, NULL),
(19, 'Samuel chinonso', 'innocentchinonso1342@gmail.com', '07038501364', '$2y$10$8COjojuESAI1F/03cATsV.MihiWDrRRmWPcc/PVn1JiO2IklB3D9m', 'St Jude estate nekede', 'imo', 'user', '2025-03-30 19:52:50', '2025-03-30 19:54:00', 'owerri', '₦', 'nigeria', 0.00, 1, NULL),
(20, 'Ebenezer Moses', 'ebenmoses512801@gmail.com', '+2348032813906', '$2y$10$s9onKGxUbgmqqvI5CLPYDumYIyB8QpzS5KP69et8Hv15HX2ba5iZC', '30 iboku street', 'abia', 'user', '2025-04-01 00:25:06', '2025-04-01 00:25:24', '', '₦', 'nigeria', 0.00, 1, NULL),
(21, 'Jude Nkemere', 'nkemsoftware@gmail.com', '+2348135742906', '$2y$10$HzZCSfMetSoZFXbnVAocbeQ1fVnF/Iocm7BduM9NJofKCtQG30Q86', '36 omalade street', 'imo', 'user', '2025-04-13 19:11:27', '2025-04-13 19:11:50', 'owerri', '₦', 'nigeria', 0.00, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `withdrawals`
--

CREATE TABLE `withdrawals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `bank` varchar(255) NOT NULL,
  `account_number` varchar(50) NOT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `withdrawals`
--

INSERT INTO `withdrawals` (`id`, `user_id`, `amount`, `bank`, `account_number`, `status`, `created_at`) VALUES
(1, 3, 20.00, 'access', '0077868968', 'pending', '2025-03-02 22:01:01'),
(2, 3, 20.00, 'access', '0077868968', 'pending', '2025-03-02 22:17:52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alerts`
--
ALTER TABLE `alerts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `is_read` (`is_read`);

--
-- Indexes for table `bike`
--
ALTER TABLE `bike`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `deposits`
--
ALTER TABLE `deposits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tracking_id` (`tracking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `vendor_id` (`vendor_id`),
  ADD KEY `rider_id` (`rider_id`);

--
-- Indexes for table `order_status_history`
--
ALTER TABLE `order_status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `payment_history`
--
ALTER TABLE `payment_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `vendor_id` (`vendor_id`);

--
-- Indexes for table `riders`
--
ALTER TABLE `riders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- Indexes for table `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alerts`
--
ALTER TABLE `alerts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bike`
--
ALTER TABLE `bike`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `deposits`
--
ALTER TABLE `deposits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `order_status_history`
--
ALTER TABLE `order_status_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_history`
--
ALTER TABLE `payment_history`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `riders`
--
ALTER TABLE `riders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `withdrawals`
--
ALTER TABLE `withdrawals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `complaints`
--
ALTER TABLE `complaints`
  ADD CONSTRAINT `complaints_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `deposits`
--
ALTER TABLE `deposits`
  ADD CONSTRAINT `deposits_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`vendor_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`rider_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `payment_history`
--
ALTER TABLE `payment_history`
  ADD CONSTRAINT `payment_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`vendor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD CONSTRAINT `withdrawals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
