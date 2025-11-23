-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 23, 2025 at 08:44 AM
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
-- Database: `travel_agency_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `guests`
--

CREATE TABLE `guests` (
  `id` int(11) NOT NULL,
  `trip_id` int(11) NOT NULL,
  `type` varchar(10) NOT NULL,
  `name1` varchar(255) NOT NULL,
  `name2` varchar(255) DEFAULT NULL,
  `passport1` varchar(100) DEFAULT NULL,
  `passport2` varchar(100) DEFAULT NULL,
  `dob1` date DEFAULT NULL,
  `dob2` date DEFAULT NULL,
  `country1` varchar(100) DEFAULT NULL,
  `country2` varchar(100) DEFAULT NULL,
  `remark1` varchar(255) DEFAULT NULL,
  `remark2` varchar(255) DEFAULT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guests`
--

INSERT INTO `guests` (`id`, `trip_id`, `type`, `name1`, `name2`, `passport1`, `passport2`, `dob1`, `dob2`, `country1`, `country2`, `remark1`, `remark2`, `display_order`) VALUES
(13, 109, 'couple', 'qwe', 'weqw', 'weq', 'weqe', '2025-10-24', '2025-10-24', 'Nepal', 'Nepal', NULL, NULL, 1),
(14, 109, 'single', 'sdsd', NULL, 'asd', NULL, '2025-10-24', NULL, 'dfsd', NULL, NULL, NULL, 2),
(21, 110, 'couple', 'ram', 'sita', '1425', '1425', '2025-10-26', '2025-10-26', 'Nepal', 'Nepal', NULL, NULL, 1),
(22, 110, 'single', 'hari', NULL, '1245', NULL, '2025-10-26', NULL, 'Nepal', NULL, NULL, NULL, 2);

-- --------------------------------------------------------

--
-- Table structure for table `guides`
--

CREATE TABLE `guides` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `language` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `availability_status` enum('Available','Not Available','On Trip') DEFAULT 'Available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hotels`
--

CREATE TABLE `hotels` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `room_types` varchar(255) DEFAULT NULL,
  `services_provided` varchar(255) DEFAULT NULL,
  `availability` enum('Available','Not Available') DEFAULT 'Available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hotels`
--

INSERT INTO `hotels` (`id`, `name`, `email`, `room_types`, `services_provided`, `availability`, `created_at`, `updated_at`) VALUES
(5, 'Kongde Peak Lodge', 'info.hotel@gmail.com', 'Twin, Double', 'B, L, D', 'Available', '2025-10-12 03:45:50', '2025-10-15 03:49:21'),
(6, 'Panorama Lodge Namche', 'info.hotel@gmail.com', 'Twin, Double, Dormitory', 'B, L, D', 'Available', '2025-10-12 03:45:50', '2025-10-15 03:49:21'),
(7, 'Himalayan Lodge Tengboche', 'info.hotel@gmail.com', 'Twin, Double', 'B, L, D', 'Available', '2025-10-12 03:45:50', '2025-10-15 03:49:21'),
(8, 'Peak 38 View Lodge Dingboche', 'info.hotel@gmail.com', 'Twin, Double, Suite', 'B, L, D', 'Available', '2025-10-12 03:45:50', '2025-10-15 03:49:21'),
(9, 'Oxygen Lodge Lobuche', 'info.hotel@gmail.com', 'Twin, Double', 'B, L, D', 'Available', '2025-10-12 03:45:50', '2025-10-15 03:49:21'),
(10, 'Buddha Lodge Gorakh Shep', 'info.hotel@gmail.com', 'Twin, Double, Dormitory', 'B, L, D', 'Available', '2025-10-12 03:45:50', '2025-10-15 03:49:21'),
(11, 'Shree Dawa Lodge Pangboche', 'info.hotel@gmail.com', 'Twin, Double', 'B, L, D', 'Available', '2025-10-12 03:45:50', '2025-10-15 03:49:21'),
(12, 'Buddha Lodge Lukla', 'info.hotel@gmail.com', 'Twin, Double, Standard', 'B, L, D', 'Available', '2025-10-12 03:45:50', '2025-10-15 03:49:21'),
(14, 'Sweethome Bhaktapur', 'sweethomebhakatpur@gmail.com', 'Standard, Deluxe, Budget', 'B', 'Available', '2025-10-15 02:37:56', '2025-11-04 02:30:11'),
(15, 'Trekking Lodges', 'pratyush.dulal@kavyaschool.edu.np', 'Twin, Double', 'B, L, D', 'Available', '2025-10-15 02:37:56', '2025-10-15 03:49:21'),
(16, 'Pokhara Lake View Resort', 'pratyushcollege68@gmail.com', 'Twin, Double, Suite', 'B, L', 'Available', '2025-10-15 02:37:56', '2025-10-15 03:49:21'),
(17, 'Shambala Hotel', 'mahesh@shambalahotel.com', 'Double, Single', 'B, D', 'Available', '2025-10-15 02:37:56', '2025-11-02 03:23:28'),
(30, 'Trekking Lodge', 'pratyush.dulal@kavyaschool.edu.np', 'Basic, Shared', 'B, L, D', 'Available', '2025-10-15 03:02:29', '2025-10-15 03:49:21'),
(33, 'Patan Royal Cafe', NULL, '', 'B, L, D', 'Available', '2025-10-17 03:52:14', '2025-10-24 06:33:42'),
(34, 'Hotel Heritage', NULL, 'double, single', 'B, L, D', 'Available', '2025-10-24 06:30:58', '2025-10-24 06:30:58'),
(35, 'Summit River Lodge', NULL, 'Double, Single', 'B, L, D', 'Available', '2025-10-24 06:31:37', '2025-10-24 06:31:37'),
(36, 'Ghorkha Gau Ghar', NULL, 'Double, Single', 'B, L, D', 'Available', '2025-10-24 06:35:13', '2025-10-24 06:35:13'),
(37, 'Dhampush Village Eco Lodge Pvt. Ltd.', 'dhampusecolodge@gmail.com', 'Double, Single', 'B, L, D', 'Available', '2025-10-24 06:36:10', '2025-11-02 03:22:50'),
(38, 'Barahi Hotel', 'barahi@gmail.com', 'Double, Twin, Single, Triple', 'B, L, D', 'Available', '2025-10-24 06:53:05', '2025-10-24 06:53:05'),
(39, 'Safari Narayani Lodge', '', 'Double, Twin, Single, Triple', 'B, L, D', 'Available', '2025-10-24 06:53:47', '2025-10-24 06:53:47'),
(40, 'New Tibet', '', '', '', 'Available', '2025-10-24 07:54:53', '2025-10-24 07:54:53'),
(41, 'Hotel Fort Resort', '', 'Double, Twin, Single, Triple', 'B, L, D', 'Available', '2025-10-26 07:19:07', '2025-10-26 07:19:07'),
(42, 'Hotel Hukum Durbar', '', 'Double, Twin, Single, Triple', 'B, L, D', 'Available', '2025-10-26 07:19:58', '2025-10-26 07:19:58'),
(43, 'Matina Chhen', 'matinachenbandipur@gmail.com', 'Double, Twin, Single, Triple', '', 'Available', '2025-10-26 07:20:32', '2025-11-02 03:22:17'),
(44, 'Ghandruk Eco Lodge', '', 'Double, Twin, Single, Triple', '', 'Available', '2025-10-26 07:23:03', '2025-10-26 07:23:03'),
(45, 'Trekkers Sanctuary', '', 'Double, Twin, Single, Triple', 'B, L, D', 'Available', '2025-10-26 07:23:45', '2025-10-26 07:23:45'),
(46, 'Sunny Lodge', '', 'Double, Twin, Single, Triple', 'B, L, D', 'Available', '2025-10-26 07:24:10', '2025-10-26 07:24:10'),
(47, 'Dhulikhel Lodge Resort Pvt.Ltd', 'dlrdhuli@gmail.com', 'Double, Twin, Single, Triple', '', 'Available', '2025-10-31 06:28:05', '2025-10-31 06:28:05'),
(48, 'barahi Jungle Lodge, Chitwan', 'bjl@barahi.com', 'Double, Twin, Single, Triple', '', 'Available', '2025-10-31 06:33:30', '2025-10-31 06:33:30'),
(49, 'Rural Heritage', 'res@rural-heritage.com', 'Double, Twin, Single, Triple', '', 'Available', '2025-10-31 06:36:45', '2025-10-31 06:36:45'),
(50, 'Temple Tree Resort Pvt.Ltd', 'sales@templetreenepal.com', 'Double, Twin, Single, Triple', '', 'Available', '2025-10-31 06:39:18', '2025-10-31 06:39:18'),
(51, 'Hotel Soaltee', 'rojina.maharjan@soaltee.com', 'Double, Twin, Single, Triple', '', 'Available', '2025-10-31 06:41:46', '2025-10-31 06:41:46'),
(52, 'Monastery', 'dulalpratyush@gmail.com', 'Double, Twin, Single, Triple', '', 'Available', '2025-11-02 02:57:34', '2025-11-02 02:57:34'),
(53, 'Lodge', 'dulalpratyush@gmail.com', 'Double, Twin', '', 'Available', '2025-11-02 03:02:48', '2025-11-02 03:02:48'),
(54, 'lake view resort', 'info@lakeviewpokhara.com', 'Double, Twin', '', 'Available', '2025-11-02 03:14:38', '2025-11-02 03:14:38');

-- --------------------------------------------------------

--
-- Table structure for table `hotel_email_logs`
--

CREATE TABLE `hotel_email_logs` (
  `id` int(11) NOT NULL,
  `trip_id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `email_type` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hotel_email_logs`
--

INSERT INTO `hotel_email_logs` (`id`, `trip_id`, `hotel_id`, `email_type`, `created_at`) VALUES
(1, 165, 14, 'amendment', '2025-10-28 01:07:48'),
(2, 165, 17, 'amendment', '2025-10-28 01:07:53'),
(3, 165, 14, 'amendment', '2025-10-28 01:13:02'),
(4, 165, 17, 'amendment', '2025-10-28 01:13:08'),
(5, 165, 14, 'amendment', '2025-10-28 01:17:59'),
(6, 165, 17, 'amendment', '2025-10-28 01:18:03'),
(7, 165, 14, 'amendment', '2025-10-28 01:25:28'),
(8, 165, 17, 'amendment', '2025-10-28 01:25:34'),
(9, 165, 14, 'amendment', '2025-10-28 01:29:55'),
(10, 165, 17, 'amendment', '2025-10-28 01:30:01'),
(11, 165, 14, 'amendment', '2025-10-28 01:32:34'),
(12, 165, 17, 'amendment', '2025-10-28 01:32:39'),
(13, 165, 14, 'amendment', '2025-10-28 05:51:55'),
(14, 165, 17, 'amendment', '2025-10-28 05:51:59'),
(15, 166, 14, 'amendment', '2025-10-28 08:32:51'),
(16, 166, 17, 'amendment', '2025-10-28 08:32:56'),
(17, 166, 14, 'amendment', '2025-10-28 09:31:14'),
(18, 166, 17, 'amendment', '2025-10-28 09:31:19'),
(19, 166, 14, 'amendment', '2025-10-29 09:43:46'),
(20, 166, 17, 'amendment', '2025-10-29 09:43:51'),
(21, 171, 14, 'initial', '2025-10-31 07:16:25'),
(22, 171, 17, 'initial', '2025-10-31 07:16:29'),
(23, 171, 14, 'amendment', '2025-10-31 07:16:53'),
(24, 171, 17, 'amendment', '2025-10-31 07:16:57'),
(25, 172, 14, 'initial', '2025-11-02 03:24:39'),
(26, 172, 52, 'initial', '2025-11-02 03:24:44'),
(27, 172, 43, 'initial', '2025-11-02 03:24:48'),
(28, 172, 37, 'initial', '2025-11-02 03:24:53'),
(29, 172, 53, 'initial', '2025-11-02 03:24:58'),
(30, 172, 54, 'initial', '2025-11-02 03:25:02'),
(31, 172, 17, 'initial', '2025-11-02 03:25:06'),
(32, 172, 14, 'amendment', '2025-11-02 10:35:53'),
(33, 172, 52, 'amendment', '2025-11-02 10:35:58'),
(34, 172, 43, 'amendment', '2025-11-02 10:36:03'),
(35, 172, 37, 'amendment', '2025-11-02 10:36:07'),
(36, 172, 53, 'amendment', '2025-11-02 10:36:12'),
(37, 172, 54, 'amendment', '2025-11-02 10:36:16'),
(38, 172, 17, 'amendment', '2025-11-02 10:36:21'),
(39, 173, 53, 'initial', '2025-11-04 01:23:09'),
(40, 173, 53, 'initial', '2025-11-04 01:23:09'),
(41, 173, 53, 'initial', '2025-11-04 01:23:11'),
(42, 173, 53, 'initial', '2025-11-04 01:23:12'),
(43, 173, 52, 'initial', '2025-11-04 01:23:13'),
(44, 173, 52, 'initial', '2025-11-04 01:23:14'),
(45, 173, 52, 'initial', '2025-11-04 01:23:15'),
(46, 173, 52, 'initial', '2025-11-04 01:23:17'),
(47, 174, 53, 'initial', '2025-11-04 10:25:54'),
(48, 174, 52, 'initial', '2025-11-04 10:25:59'),
(49, 174, 53, 'amendment', '2025-11-04 10:27:29'),
(50, 174, 52, 'amendment', '2025-11-04 10:27:34'),
(51, 174, 53, 'amendment', '2025-11-05 09:11:52'),
(52, 174, 52, 'amendment', '2025-11-05 09:11:57');

-- --------------------------------------------------------

--
-- Table structure for table `itinerary_days`
--

CREATE TABLE `itinerary_days` (
  `id` int(11) NOT NULL,
  `trip_id` int(11) NOT NULL,
  `day_date` date NOT NULL,
  `day_type` varchar(20) DEFAULT 'regular' COMMENT 'Type of day: regular, arrival, or departure',
  `guide_id` int(11) DEFAULT NULL,
  `vehicle_id` int(11) DEFAULT NULL,
  `hotel_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `services_provided` varchar(50) DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `room_type_id` int(11) DEFAULT NULL,
  `room_type_data` text DEFAULT NULL,
  `hotel_informed` tinyint(1) NOT NULL DEFAULT 0,
  `guide_informed` tinyint(1) NOT NULL DEFAULT 0,
  `vehicle_informed` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `itinerary_days`
--

INSERT INTO `itinerary_days` (`id`, `trip_id`, `day_date`, `day_type`, `guide_id`, `vehicle_id`, `hotel_id`, `notes`, `services_provided`, `created_at`, `updated_at`, `room_type_id`, `room_type_data`, `hotel_informed`, `guide_informed`, `vehicle_informed`) VALUES
(1893, 145, '2026-03-05', 'normal', NULL, NULL, NULL, 'Departure from home', '', '2025-10-27 02:59:59', '2025-10-27 03:00:25', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(1894, 145, '2026-03-06', 'normal', NULL, NULL, 14, 'Arrival in Kathmandu and transfer to Bhaktapur', '', '2025-10-27 02:59:59', '2025-10-27 03:00:25', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1895, 145, '2026-03-07', 'normal', NULL, NULL, 14, 'Bhaktapur Overnight Stay - Overnight in Bhaktapur', '', '2025-10-27 02:59:59', '2025-10-27 03:00:25', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1896, 145, '2026-03-08', 'normal', NULL, NULL, 5, 'Transfer to Ktm airport and Fly to Lukla', 'B, L, D', '2025-10-27 02:59:59', '2025-10-27 03:00:25', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1897, 145, '2026-03-09', 'normal', NULL, NULL, 6, 'Trek to Namche Bazar', 'B, L, D', '2025-10-27 02:59:59', '2025-10-27 03:00:25', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1898, 145, '2026-03-10', 'normal', NULL, NULL, 6, 'Rest in Namche', 'B, L, D', '2025-10-27 02:59:59', '2025-10-27 03:00:25', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1899, 145, '2026-03-11', 'normal', NULL, NULL, 7, 'Trek from Namche to Tengboche', 'B, L, D', '2025-10-27 02:59:59', '2025-10-27 03:00:25', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1900, 145, '2026-03-12', 'normal', NULL, NULL, 8, 'Trek from Tengboche to Dingboche', 'B, L, D', '2025-10-27 02:59:59', '2025-10-27 03:00:25', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1901, 145, '2026-03-13', 'normal', NULL, NULL, 8, 'Rest Day in Dingboche - Acclimatization Day', 'B, L, D', '2025-10-27 02:59:59', '2025-10-27 03:00:25', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1902, 145, '2026-03-14', 'normal', NULL, NULL, 9, 'Trek Dingboche to Lobuche', 'B, L, D', '2025-10-27 02:59:59', '2025-10-27 03:00:25', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1903, 145, '2026-03-15', 'normal', NULL, NULL, 10, 'Trek to Base Camp - Gorakh Shep', 'B, L, D', '2025-10-27 02:59:59', '2025-10-27 03:00:25', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1904, 145, '2026-03-16', 'normal', NULL, NULL, 9, 'Trek Kalapathar and back to Lobuche', 'B, L, D', '2025-10-27 02:59:59', '2025-10-27 03:00:25', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1905, 145, '2026-03-17', 'normal', NULL, NULL, 11, 'Trek from Lobuche to Pangboche', 'B, L, D', '2025-10-27 02:59:59', '2025-10-27 03:00:25', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1906, 145, '2026-03-18', 'normal', NULL, NULL, 6, 'Trek from Pangboche to Namche', 'B, L, D', '2025-10-27 02:59:59', '2025-10-27 03:00:25', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1907, 145, '2026-03-19', 'normal', NULL, NULL, 10, 'Trek from Namche to Lukla', 'B, L, D', '2025-10-27 02:59:59', '2025-10-27 03:00:25', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1908, 145, '2026-03-20', 'normal', NULL, NULL, 17, 'Fly to Kathmandu and transfer to Hotel', 'B', '2025-10-27 02:59:59', '2025-10-27 03:00:25', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1909, 145, '2026-03-21', 'normal', NULL, NULL, 17, 'Kathmandu Sightseeing and Dinner', 'B, D', '2025-10-27 02:59:59', '2025-10-27 03:00:25', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1910, 145, '2026-03-22', 'normal', NULL, NULL, 17, 'Kathmandu rest day, Departure home', 'B', '2025-10-27 02:59:59', '2025-10-27 03:00:25', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1911, 146, '2026-04-16', 'normal', NULL, NULL, NULL, 'Departure from home', '', '2025-10-27 03:00:28', '2025-10-27 03:04:52', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(1912, 146, '2026-04-17', 'normal', NULL, NULL, 14, 'Arrival in Kathmandu and transfer to Bhaktapur', '', '2025-10-27 03:00:28', '2025-10-27 03:04:52', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1913, 146, '2026-04-18', 'normal', NULL, NULL, 14, 'Bhaktapur Overnight Stay - Overnight in Bhaktapur', '', '2025-10-27 03:00:28', '2025-10-27 03:04:52', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1914, 146, '2026-04-19', 'normal', NULL, NULL, 5, 'Transfer to Ktm airport and Fly to Lukla', 'B, L, D', '2025-10-27 03:00:28', '2025-10-27 03:04:52', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1915, 146, '2026-04-20', 'normal', NULL, NULL, 6, 'Trek to Namche Bazar', 'B, L, D', '2025-10-27 03:00:28', '2025-10-27 03:04:52', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1916, 146, '2026-04-21', 'normal', NULL, NULL, 6, 'Rest in Namche', 'B, L, D', '2025-10-27 03:00:28', '2025-10-27 03:04:52', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1917, 146, '2026-04-22', 'normal', NULL, NULL, 7, 'Trek from Namche to Tengboche', 'B, L, D', '2025-10-27 03:00:28', '2025-10-27 03:04:52', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1918, 146, '2026-04-23', 'normal', NULL, NULL, 8, 'Trek from Tengboche to Dingboche', 'B, L, D', '2025-10-27 03:00:28', '2025-10-27 03:04:52', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1919, 146, '2026-04-24', 'normal', NULL, NULL, 8, 'Rest Day in Dingboche - Acclimatization Day', 'B, L, D', '2025-10-27 03:00:28', '2025-10-27 03:04:52', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1920, 146, '2026-04-25', 'normal', NULL, NULL, 9, 'Trek Dingboche to Lobuche', 'B, L, D', '2025-10-27 03:00:28', '2025-10-27 03:04:52', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1921, 146, '2026-04-26', 'normal', NULL, NULL, 10, 'Trek to Base Camp - Gorakh Shep', 'B, L, D', '2025-10-27 03:00:28', '2025-10-27 03:04:52', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1922, 146, '2026-04-27', 'normal', NULL, NULL, 9, 'Trek Kalapathar and back to Lobuche', 'B, L, D', '2025-10-27 03:00:28', '2025-10-27 03:04:52', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1923, 146, '2026-04-28', 'normal', NULL, NULL, 11, 'Trek from Lobuche to Pangboche', 'B, L, D', '2025-10-27 03:00:28', '2025-10-27 03:04:52', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1924, 146, '2026-04-29', 'normal', NULL, NULL, 6, 'Trek from Pangboche to Namche', 'B, L, D', '2025-10-27 03:00:28', '2025-10-27 03:04:52', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1925, 146, '2026-04-30', 'normal', NULL, NULL, 10, 'Trek from Namche to Lukla', 'B, L, D', '2025-10-27 03:00:28', '2025-10-27 03:04:52', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1926, 146, '2026-05-01', 'normal', NULL, NULL, 17, 'Fly to Kathmandu and transfer to Hotel', 'B', '2025-10-27 03:00:28', '2025-10-27 03:04:52', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1927, 146, '2026-05-02', 'normal', NULL, NULL, 17, 'Kathmandu Sightseeing and Dinner', 'B, D', '2025-10-27 03:00:28', '2025-10-27 03:04:52', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1928, 146, '2026-05-03', 'normal', NULL, NULL, 17, 'Kathmandu rest day, Departure home', 'B', '2025-10-27 03:00:28', '2025-10-27 03:04:52', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1929, 147, '2026-10-04', 'normal', NULL, NULL, NULL, 'Departure from home', '', '2025-10-27 03:04:55', '2025-10-27 03:09:15', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(1930, 147, '2026-10-05', 'normal', NULL, NULL, 14, 'Arrival in Kathmandu and transfer to Bhaktapur', '', '2025-10-27 03:04:55', '2025-10-27 03:09:15', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1931, 147, '2026-10-06', 'normal', NULL, NULL, 14, 'Bhaktapur Overnight Stay - Overnight in Bhaktapur', '', '2025-10-27 03:04:55', '2025-10-27 03:09:15', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1932, 147, '2026-10-07', 'normal', NULL, NULL, 5, 'Transfer to Ktm airport and Fly to Lukla', 'B, L, D', '2025-10-27 03:04:55', '2025-10-27 03:09:15', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1933, 147, '2026-10-08', 'normal', NULL, NULL, 6, 'Trek to Namche Bazar', 'B, L, D', '2025-10-27 03:04:55', '2025-10-27 03:09:15', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1934, 147, '2026-10-09', 'normal', NULL, NULL, 6, 'Rest in Namche', 'B, L, D', '2025-10-27 03:04:55', '2025-10-27 03:09:15', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1935, 147, '2026-10-10', 'normal', NULL, NULL, 7, 'Trek from Namche to Tengboche', 'B, L, D', '2025-10-27 03:04:55', '2025-10-27 03:09:15', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1936, 147, '2026-10-11', 'normal', NULL, NULL, 8, 'Trek from Tengboche to Dingboche', 'B, L, D', '2025-10-27 03:04:55', '2025-10-27 03:09:15', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1937, 147, '2026-10-12', 'normal', NULL, NULL, 8, 'Rest Day in Dingboche - Acclimatization Day', 'B, L, D', '2025-10-27 03:04:55', '2025-10-27 03:09:15', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1938, 147, '2026-10-13', 'normal', NULL, NULL, 9, 'Trek Dingboche to Lobuche', 'B, L, D', '2025-10-27 03:04:55', '2025-10-27 03:09:15', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1939, 147, '2026-10-14', 'normal', NULL, NULL, 10, 'Trek to Base Camp - Gorakh Shep', 'B, L, D', '2025-10-27 03:04:55', '2025-10-27 03:09:15', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1940, 147, '2026-10-15', 'normal', NULL, NULL, 9, 'Trek Kalapathar and back to Lobuche', 'B, L, D', '2025-10-27 03:04:55', '2025-10-27 03:09:15', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1941, 147, '2026-10-16', 'normal', NULL, NULL, 11, 'Trek from Lobuche to Pangboche', 'B, L, D', '2025-10-27 03:04:55', '2025-10-27 03:09:15', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1942, 147, '2026-10-17', 'normal', NULL, NULL, 6, 'Trek from Pangboche to Namche', 'B, L, D', '2025-10-27 03:04:55', '2025-10-27 03:09:15', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1943, 147, '2026-10-18', 'normal', NULL, NULL, 10, 'Trek from Namche to Lukla', 'B, L, D', '2025-10-27 03:04:55', '2025-10-27 03:09:15', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1944, 147, '2026-10-19', 'normal', NULL, NULL, 17, 'Fly to Kathmandu and transfer to Hotel', 'B', '2025-10-27 03:04:55', '2025-10-27 03:09:15', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1945, 147, '2026-10-20', 'normal', NULL, NULL, 17, 'Kathmandu Sightseeing and Dinner', 'B, D', '2025-10-27 03:04:55', '2025-10-27 03:09:15', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1946, 147, '2026-10-21', 'normal', NULL, NULL, 17, 'Kathmandu rest day, Departure home', 'B', '2025-10-27 03:04:55', '2025-10-27 03:09:15', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1947, 148, '2026-10-18', 'normal', NULL, NULL, NULL, 'Departure from home', '', '2025-10-27 03:09:19', '2025-10-27 03:09:52', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(1948, 148, '2026-10-19', 'normal', NULL, NULL, 14, 'Arrival in Kathmandu and transfer to Bhaktapur', '', '2025-10-27 03:09:19', '2025-10-27 03:09:52', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1949, 148, '2026-10-20', 'normal', NULL, NULL, 14, 'Bhaktapur Overnight Stay - Overnight in Bhaktapur', '', '2025-10-27 03:09:19', '2025-10-27 03:09:52', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1950, 148, '2026-10-21', 'normal', NULL, NULL, 5, 'Transfer to Ktm airport and Fly to Lukla', 'B, L, D', '2025-10-27 03:09:19', '2025-10-27 03:09:52', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1951, 148, '2026-10-22', 'normal', NULL, NULL, 6, 'Trek to Namche Bazar', 'B, L, D', '2025-10-27 03:09:19', '2025-10-27 03:09:52', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1952, 148, '2026-10-23', 'normal', NULL, NULL, 6, 'Rest in Namche', 'B, L, D', '2025-10-27 03:09:19', '2025-10-27 03:09:52', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1953, 148, '2026-10-24', 'normal', NULL, NULL, 7, 'Trek from Namche to Tengboche', 'B, L, D', '2025-10-27 03:09:19', '2025-10-27 03:09:52', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1954, 148, '2026-10-25', 'normal', NULL, NULL, 8, 'Trek from Tengboche to Dingboche', 'B, L, D', '2025-10-27 03:09:19', '2025-10-27 03:09:52', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1955, 148, '2026-10-26', 'normal', NULL, NULL, 8, 'Rest Day in Dingboche - Acclimatization Day', 'B, L, D', '2025-10-27 03:09:19', '2025-10-27 03:09:52', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1956, 148, '2026-10-27', 'normal', NULL, NULL, 9, 'Trek Dingboche to Lobuche', 'B, L, D', '2025-10-27 03:09:19', '2025-10-27 03:09:52', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1957, 148, '2026-10-28', 'normal', NULL, NULL, 10, 'Trek to Base Camp - Gorakh Shep', 'B, L, D', '2025-10-27 03:09:19', '2025-10-27 03:09:52', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1958, 148, '2026-10-29', 'normal', NULL, NULL, 9, 'Trek Kalapathar and back to Lobuche', 'B, L, D', '2025-10-27 03:09:19', '2025-10-27 03:09:52', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1959, 148, '2026-10-30', 'normal', NULL, NULL, 11, 'Trek from Lobuche to Pangboche', 'B, L, D', '2025-10-27 03:09:19', '2025-10-27 03:09:52', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1960, 148, '2026-10-31', 'normal', NULL, NULL, 6, 'Trek from Pangboche to Namche', 'B, L, D', '2025-10-27 03:09:19', '2025-10-27 03:09:52', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1961, 148, '2026-11-01', 'normal', NULL, NULL, 10, 'Trek from Namche to Lukla', 'B, L, D', '2025-10-27 03:09:19', '2025-10-27 03:09:52', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1962, 148, '2026-11-02', 'normal', NULL, NULL, 17, 'Fly to Kathmandu and transfer to Hotel', 'B', '2025-10-27 03:09:19', '2025-10-27 03:09:52', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1963, 148, '2026-11-03', 'normal', NULL, NULL, 17, 'Kathmandu Sightseeing and Dinner', 'B, D', '2025-10-27 03:09:19', '2025-10-27 03:09:52', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1964, 148, '2026-11-04', 'normal', NULL, NULL, 17, 'Kathmandu rest day, Departure home', 'B', '2025-10-27 03:09:19', '2025-10-27 03:09:52', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1965, 149, '2026-11-08', 'normal', NULL, NULL, NULL, 'Departure from home', '', '2025-10-27 03:09:56', '2025-10-27 03:10:16', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(1966, 149, '2026-11-09', 'normal', NULL, NULL, 14, 'Arrival in Kathmandu and transfer to Bhaktapur', '', '2025-10-27 03:09:56', '2025-10-27 03:10:16', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1967, 149, '2026-11-10', 'normal', NULL, NULL, 14, 'Bhaktapur Overnight Stay - Overnight in Bhaktapur', '', '2025-10-27 03:09:56', '2025-10-27 03:10:16', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1968, 149, '2026-11-11', 'normal', NULL, NULL, 5, 'Transfer to Ktm airport and Fly to Lukla', 'B, L, D', '2025-10-27 03:09:56', '2025-10-27 03:10:16', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1969, 149, '2026-11-12', 'normal', NULL, NULL, 6, 'Trek to Namche Bazar', 'B, L, D', '2025-10-27 03:09:56', '2025-10-27 03:10:16', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1970, 149, '2026-11-13', 'normal', NULL, NULL, 6, 'Rest in Namche', 'B, L, D', '2025-10-27 03:09:56', '2025-10-27 03:10:16', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1971, 149, '2026-11-14', 'normal', NULL, NULL, 7, 'Trek from Namche to Tengboche', 'B, L, D', '2025-10-27 03:09:56', '2025-10-27 03:10:16', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1972, 149, '2026-11-15', 'normal', NULL, NULL, 8, 'Trek from Tengboche to Dingboche', 'B, L, D', '2025-10-27 03:09:56', '2025-10-27 03:10:16', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1973, 149, '2026-11-16', 'normal', NULL, NULL, 8, 'Rest Day in Dingboche - Acclimatization Day', 'B, L, D', '2025-10-27 03:09:56', '2025-10-27 03:10:16', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1974, 149, '2026-11-17', 'normal', NULL, NULL, 9, 'Trek Dingboche to Lobuche', 'B, L, D', '2025-10-27 03:09:56', '2025-10-27 03:10:16', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1975, 149, '2026-11-18', 'normal', NULL, NULL, 10, 'Trek to Base Camp - Gorakh Shep', 'B, L, D', '2025-10-27 03:09:56', '2025-10-27 03:10:16', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1976, 149, '2026-11-19', 'normal', NULL, NULL, 9, 'Trek Kalapathar and back to Lobuche', 'B, L, D', '2025-10-27 03:09:56', '2025-10-27 03:10:16', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1977, 149, '2026-11-20', 'normal', NULL, NULL, 11, 'Trek from Lobuche to Pangboche', 'B, L, D', '2025-10-27 03:09:56', '2025-10-27 03:10:16', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1978, 149, '2026-11-21', 'normal', NULL, NULL, 6, 'Trek from Pangboche to Namche', 'B, L, D', '2025-10-27 03:09:56', '2025-10-27 03:10:16', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1979, 149, '2026-11-22', 'normal', NULL, NULL, 10, 'Trek from Namche to Lukla', 'B, L, D', '2025-10-27 03:09:56', '2025-10-27 03:10:16', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1980, 149, '2026-11-23', 'normal', NULL, NULL, 17, 'Fly to Kathmandu and transfer to Hotel', 'B', '2025-10-27 03:09:56', '2025-10-27 03:10:16', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1981, 149, '2026-11-24', 'normal', NULL, NULL, 17, 'Kathmandu Sightseeing and Dinner', 'B, D', '2025-10-27 03:09:56', '2025-10-27 03:10:16', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1982, 149, '2026-11-25', 'normal', NULL, NULL, 17, 'Kathmandu rest day, Departure home', 'B', '2025-10-27 03:09:56', '2025-10-27 03:10:16', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1983, 150, '2026-02-23', 'normal', NULL, NULL, NULL, 'Departure From Home', '', '2025-10-27 03:11:24', '2025-11-04 04:47:40', NULL, '{\"double\":1,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1984, 150, '2026-02-24', 'normal', NULL, NULL, 34, 'Arrival and transfer to Bhaktapur.', 'D', '2025-10-27 03:11:24', '2025-11-04 04:47:40', NULL, '{\"double\":1,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1985, 150, '2026-02-25', 'normal', NULL, NULL, 34, 'Drive to Namobuddha visit monastery and evenig sightseeing at Bhaktapur.', 'B', '2025-10-27 03:11:24', '2025-11-04 04:47:40', NULL, '{\"double\":1,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1986, 150, '2026-02-26', 'normal', NULL, NULL, 35, 'Drive from Bhaktapur to Charaudi and rafting to Kurintar and short walk to resort.', 'B, D', '2025-10-27 03:11:24', '2025-11-04 04:47:40', NULL, '{\"double\":1,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1987, 150, '2026-02-27', 'normal', NULL, NULL, 36, 'Walk back to the highway and drive from Kurintar to Gorkha.', 'B, D', '2025-10-27 03:11:24', '2025-11-04 04:47:40', NULL, '{\"double\":1,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1988, 150, '2026-02-28', 'normal', NULL, NULL, 37, 'Drive from Gorkha to Pokhara and further upto Dhampus Phedi and hike to Dhampus.', 'B, D', '2025-10-27 03:11:24', '2025-11-04 04:47:40', NULL, '{\"double\":1,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1989, 150, '2026-03-01', 'normal', NULL, NULL, 37, 'Hike from Dhampus to Potana and to Australian camp and back to lodge.Evenign Momo Class.', 'B, D', '2025-10-27 03:11:24', '2025-11-04 04:47:40', NULL, '{\"double\":1,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1990, 150, '2026-03-02', 'normal', NULL, NULL, 38, 'After breakfast hike to Gurung village and lunch with Locals and hike to Suiket phedi and drive to Pokhara.', 'B, L, D', '2025-10-27 03:11:24', '2025-11-04 04:47:40', NULL, '{\"double\":1,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1991, 150, '2026-03-03', 'normal', NULL, NULL, 38, 'Boating over Phewa lake and hike to Peace stupa and back to hotel.', 'B, D', '2025-10-27 03:11:24', '2025-11-04 04:47:40', NULL, '{\"double\":1,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1992, 150, '2026-03-04', 'normal', NULL, NULL, 39, 'Drive from Pokhara to Chitwan .', 'B, L, D', '2025-10-27 03:11:24', '2025-11-04 04:47:40', NULL, '{\"double\":1,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1993, 150, '2026-03-05', 'normal', NULL, NULL, 39, 'Jungle activities in Chitwan National Park', 'B, L, D', '2025-10-27 03:11:24', '2025-11-04 04:47:40', NULL, '{\"double\":1,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1994, 150, '2026-03-06', 'normal', NULL, NULL, 17, 'Drive from Chitwan to Kathmandu and sightseeing at Pashupati.', 'B, D', '2025-10-27 03:11:24', '2025-11-04 04:47:40', NULL, '{\"double\":1,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1995, 150, '2026-03-07', 'normal', NULL, NULL, 17, 'Explore Ason Market and drive to Childrens home.', 'B', '2025-10-27 03:11:24', '2025-11-04 04:47:40', NULL, '{\"double\":1,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1996, 150, '2026-03-08', 'normal', NULL, NULL, NULL, 'Departure Transfer to airport.', 'B', '2025-10-27 03:11:24', '2025-11-04 04:47:40', NULL, '{\"double\":1,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(1997, 151, '2026-03-09', 'normal', NULL, NULL, NULL, 'Departure From Home', '', '2025-10-27 03:17:47', '2025-11-04 04:48:46', NULL, '{\"double\":1,\"twin\":0,\"single\":2,\"triple\":0}', 0, 0, 0),
(1998, 151, '2026-03-10', 'normal', NULL, NULL, 34, 'Arrival and transfer to Bhaktapur.', 'D', '2025-10-27 03:17:47', '2025-11-04 04:48:46', NULL, '{\"double\":1,\"twin\":0,\"single\":2,\"triple\":0}', 0, 0, 0),
(1999, 151, '2026-03-11', 'normal', NULL, NULL, 34, 'Drive to Namobuddha visit monastery and evenig sightseeing at Bhaktapur.', 'B', '2025-10-27 03:17:47', '2025-11-04 04:48:46', NULL, '{\"double\":1,\"twin\":0,\"single\":2,\"triple\":0}', 0, 0, 0),
(2000, 151, '2026-03-12', 'normal', NULL, NULL, 35, 'Drive from Bhaktapur to Charaudi and rafting to Kurintar and short walk to resort.', 'B, D', '2025-10-27 03:17:47', '2025-11-04 04:48:46', NULL, '{\"double\":1,\"twin\":0,\"single\":2,\"triple\":0}', 0, 0, 0),
(2001, 151, '2026-03-13', 'normal', NULL, NULL, 36, 'Walk back to the highway and drive from Kurintar to Gorkha.', 'B, D', '2025-10-27 03:17:47', '2025-11-04 04:48:46', NULL, '{\"double\":1,\"twin\":0,\"single\":2,\"triple\":0}', 0, 0, 0),
(2002, 151, '2026-03-14', 'normal', NULL, NULL, 37, 'Drive from Gorkha to Pokhara and further upto Dhampus Phedi and hike to Dhampus.', 'B, D', '2025-10-27 03:17:47', '2025-11-04 04:48:46', NULL, '{\"double\":1,\"twin\":0,\"single\":2,\"triple\":0}', 0, 0, 0),
(2003, 151, '2026-03-15', 'normal', NULL, NULL, 37, 'Hike from Dhampus to Potana and to Australian camp and back to lodge.Evenign Momo Class.', 'B, D', '2025-10-27 03:17:47', '2025-11-04 04:48:46', NULL, '{\"double\":1,\"twin\":0,\"single\":2,\"triple\":0}', 0, 0, 0),
(2004, 151, '2026-03-16', 'normal', NULL, NULL, 38, 'After breakfast hike to Gurung village and lunch with Locals and hike to Suiket phedi and drive to Pokhara.', 'B, L, D', '2025-10-27 03:17:47', '2025-11-04 04:48:46', NULL, '{\"double\":1,\"twin\":0,\"single\":2,\"triple\":0}', 0, 0, 0),
(2005, 151, '2026-03-17', 'normal', NULL, NULL, 38, 'Boating over Phewa lake and hike to Peace stupa and back to hotel.', 'B, D', '2025-10-27 03:17:47', '2025-11-04 04:48:46', NULL, '{\"double\":1,\"twin\":0,\"single\":2,\"triple\":0}', 0, 0, 0),
(2006, 151, '2026-03-18', 'normal', NULL, NULL, 39, 'Drive from Pokhara to Chitwan .', 'B, L, D', '2025-10-27 03:17:47', '2025-11-04 04:48:46', NULL, '{\"double\":1,\"twin\":0,\"single\":2,\"triple\":0}', 0, 0, 0),
(2007, 151, '2026-03-19', 'normal', NULL, NULL, 39, 'Jungle activities in Chitwan National Park', 'B, L, D', '2025-10-27 03:17:47', '2025-11-04 04:48:46', NULL, '{\"double\":1,\"twin\":0,\"single\":2,\"triple\":0}', 0, 0, 0),
(2008, 151, '2026-03-20', 'normal', NULL, NULL, 17, 'Drive from Chitwan to Kathmandu and sightseeing at Pashupati.', 'B, D', '2025-10-27 03:17:47', '2025-11-04 04:48:46', NULL, '{\"double\":1,\"twin\":0,\"single\":2,\"triple\":0}', 0, 0, 0),
(2009, 151, '2026-03-21', 'normal', NULL, NULL, 17, 'Explore Ason Market and drive to Childrens home.', 'B', '2025-10-27 03:17:47', '2025-11-04 04:48:46', NULL, '{\"double\":1,\"twin\":0,\"single\":2,\"triple\":0}', 0, 0, 0),
(2010, 151, '2026-03-22', 'normal', NULL, NULL, NULL, '', 'B', '2025-10-27 03:17:47', '2025-11-04 04:48:46', NULL, '{\"double\":1,\"twin\":0,\"single\":2,\"triple\":0}', 0, 0, 0),
(2011, 152, '2026-03-29', 'normal', NULL, NULL, NULL, 'Departure From Home', '', '2025-10-27 03:18:49', '2025-10-27 03:42:21', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(2012, 152, '2026-03-30', 'normal', NULL, NULL, 34, 'Arrival and transfer to Bhaktapur.', 'D', '2025-10-27 03:18:49', '2025-10-27 03:42:22', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2013, 152, '2026-03-31', 'normal', NULL, NULL, 34, 'Drive to Namobuddha visit monastery and evenig sightseeing at Bhaktapur.', 'B', '2025-10-27 03:18:49', '2025-10-27 03:42:22', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2014, 152, '2026-04-01', 'normal', NULL, NULL, 35, 'Drive from Bhaktapur to Charaudi and rafting to Kurintar and short walk to resort.', 'B, D', '2025-10-27 03:18:49', '2025-10-27 03:42:22', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2015, 152, '2026-04-02', 'normal', NULL, NULL, 36, 'Walk back to the highway and drive from Kurintar to Gorkha.', 'B, D', '2025-10-27 03:18:49', '2025-10-27 03:42:22', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2016, 152, '2026-04-03', 'normal', NULL, NULL, 37, 'Drive from Gorkha to Pokhara and further upto Dhampus Phedi and hike to Dhampus.', 'B, D', '2025-10-27 03:18:49', '2025-10-27 03:42:22', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2017, 152, '2026-04-04', 'normal', NULL, NULL, 37, 'Hike from Dhampus to Potana and to Australian camp and back to lodge.Evenign Momo Class.', 'B, D', '2025-10-27 03:18:49', '2025-10-27 03:42:22', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2018, 152, '2026-04-05', 'normal', NULL, NULL, 38, 'After breakfast hike to Gurung village and lunch with Locals and hike to Suiket phedi and drive to Pokhara.', 'B, L, D', '2025-10-27 03:18:49', '2025-10-27 03:42:22', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2019, 152, '2026-04-06', 'normal', NULL, NULL, 38, 'Boating over Phewa lake and hike to Peace stupa and back to hotel.', 'B, D', '2025-10-27 03:18:49', '2025-10-27 03:42:22', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2020, 152, '2026-04-07', 'normal', NULL, NULL, 39, 'Drive from Pokhara to Chitwan .', 'B, L, D', '2025-10-27 03:18:49', '2025-10-27 03:42:22', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2021, 152, '2026-04-08', 'normal', NULL, NULL, 39, 'Jungle activities in Chitwan National Park', 'B, L, D', '2025-10-27 03:18:49', '2025-10-27 03:42:22', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2022, 152, '2026-04-09', 'normal', NULL, NULL, 17, 'Drive from Chitwan to Kathmandu and sightseeing at Pashupati.', 'B, D', '2025-10-27 03:18:49', '2025-10-27 03:42:22', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2023, 152, '2026-04-10', 'normal', NULL, NULL, 17, 'Explore Ason Market and drive to Childrens home.', 'B', '2025-10-27 03:18:49', '2025-10-27 03:42:22', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2024, 152, '2026-04-11', 'normal', NULL, NULL, NULL, '', 'B', '2025-10-27 03:18:49', '2025-10-27 03:42:21', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(2025, 153, '2026-04-13', 'normal', NULL, NULL, NULL, 'Departure From Home', '', '2025-10-27 03:19:26', '2025-10-27 03:42:39', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(2026, 153, '2026-04-14', 'normal', NULL, NULL, 34, 'Arrival and transfer to Bhaktapur.', 'D', '2025-10-27 03:19:26', '2025-10-27 03:42:40', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2027, 153, '2026-04-15', 'normal', NULL, NULL, 34, 'Drive to Namobuddha visit monastery and evenig sightseeing at Bhaktapur.', 'B', '2025-10-27 03:19:26', '2025-10-27 03:42:40', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2028, 153, '2026-04-16', 'normal', NULL, NULL, 35, 'Drive from Bhaktapur to Charaudi and rafting to Kurintar and short walk to resort.', 'B, D', '2025-10-27 03:19:26', '2025-10-27 03:42:40', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2029, 153, '2026-04-17', 'normal', NULL, NULL, 36, 'Walk back to the highway and drive from Kurintar to Gorkha.', 'B, D', '2025-10-27 03:19:26', '2025-10-27 03:42:40', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2030, 153, '2026-04-18', 'normal', NULL, NULL, 37, 'Drive from Gorkha to Pokhara and further upto Dhampus Phedi and hike to Dhampus.', 'B, D', '2025-10-27 03:19:26', '2025-10-27 03:42:40', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2031, 153, '2026-04-19', 'normal', NULL, NULL, 37, 'Hike from Dhampus to Potana and to Australian camp and back to lodge.Evenign Momo Class.', 'B, D', '2025-10-27 03:19:26', '2025-10-27 03:42:40', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2032, 153, '2026-04-20', 'normal', NULL, NULL, 38, 'After breakfast hike to Gurung village and lunch with Locals and hike to Suiket phedi and drive to Pokhara.', 'B, L, D', '2025-10-27 03:19:26', '2025-10-27 03:42:40', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2033, 153, '2026-04-21', 'normal', NULL, NULL, 38, 'Boating over Phewa lake and hike to Peace stupa and back to hotel.', 'B, D', '2025-10-27 03:19:26', '2025-10-27 03:42:40', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2034, 153, '2026-04-22', 'normal', NULL, NULL, 39, 'Drive from Pokhara to Chitwan .', 'B, L, D', '2025-10-27 03:19:26', '2025-10-27 03:42:40', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2035, 153, '2026-04-23', 'normal', NULL, NULL, 39, 'Jungle activities in Chitwan National Park', 'B, L, D', '2025-10-27 03:19:26', '2025-10-27 03:42:40', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2036, 153, '2026-04-24', 'normal', NULL, NULL, 17, 'Drive from Chitwan to Kathmandu and sightseeing at Pashupati.', 'B, D', '2025-10-27 03:19:26', '2025-10-27 03:42:40', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2037, 153, '2026-04-25', 'normal', NULL, NULL, 17, 'Explore Ason Market and drive to Childrens home.', 'B', '2025-10-27 03:19:26', '2025-10-27 03:42:40', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2038, 153, '2026-04-26', 'normal', NULL, NULL, NULL, '', 'B', '2025-10-27 03:19:26', '2025-10-27 03:42:39', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(2039, 154, '2026-09-28', 'normal', NULL, NULL, NULL, 'Departure From Home', '', '2025-10-27 03:20:06', '2025-10-27 03:42:47', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(2040, 154, '2026-09-29', 'normal', NULL, NULL, 34, 'Arrival and transfer to Bhaktapur.', 'D', '2025-10-27 03:20:06', '2025-10-27 03:42:47', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2041, 154, '2026-09-30', 'normal', NULL, NULL, 34, 'Drive to Namobuddha visit monastery and evenig sightseeing at Bhaktapur.', 'B', '2025-10-27 03:20:06', '2025-10-27 03:42:47', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2042, 154, '2026-10-01', 'normal', NULL, NULL, 35, 'Drive from Bhaktapur to Charaudi and rafting to Kurintar and short walk to resort.', 'B, D', '2025-10-27 03:20:06', '2025-10-27 03:42:47', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2043, 154, '2026-10-02', 'normal', NULL, NULL, 36, 'Walk back to the highway and drive from Kurintar to Gorkha.', 'B, D', '2025-10-27 03:20:06', '2025-10-27 03:42:47', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2044, 154, '2026-10-03', 'normal', NULL, NULL, 37, 'Drive from Gorkha to Pokhara and further upto Dhampus Phedi and hike to Dhampus.', 'B, D', '2025-10-27 03:20:06', '2025-10-27 03:42:47', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2045, 154, '2026-10-04', 'normal', NULL, NULL, 37, 'Hike from Dhampus to Potana and to Australian camp and back to lodge.Evenign Momo Class.', 'B, D', '2025-10-27 03:20:06', '2025-10-27 03:42:47', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2046, 154, '2026-10-05', 'normal', NULL, NULL, 38, 'After breakfast hike to Gurung village and lunch with Locals and hike to Suiket phedi and drive to Pokhara.', 'B, L, D', '2025-10-27 03:20:06', '2025-10-27 03:42:47', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2047, 154, '2026-10-06', 'normal', NULL, NULL, 38, 'Boating over Phewa lake and hike to Peace stupa and back to hotel.', 'B, D', '2025-10-27 03:20:06', '2025-10-27 03:42:47', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2048, 154, '2026-10-07', 'normal', NULL, NULL, 39, 'Drive from Pokhara to Chitwan .', 'B, L, D', '2025-10-27 03:20:06', '2025-10-27 03:42:47', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2049, 154, '2026-10-08', 'normal', NULL, NULL, 39, 'Jungle activities in Chitwan National Park', 'B, L, D', '2025-10-27 03:20:06', '2025-10-27 03:42:47', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2050, 154, '2026-10-09', 'normal', NULL, NULL, 17, 'Drive from Chitwan to Kathmandu and sightseeing at Pashupati.', 'B, D', '2025-10-27 03:20:06', '2025-10-27 03:42:47', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2051, 154, '2026-10-10', 'normal', NULL, NULL, 17, 'Explore Ason Market and drive to Childrens home.', 'B', '2025-10-27 03:20:06', '2025-10-27 03:42:47', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2052, 154, '2026-10-11', 'normal', NULL, NULL, NULL, '', 'B', '2025-10-27 03:20:06', '2025-10-27 03:42:47', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(2053, 155, '2026-10-19', 'normal', NULL, NULL, NULL, 'Departure From Home', '', '2025-10-27 03:20:38', '2025-10-27 03:42:57', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(2054, 155, '2026-10-20', 'normal', NULL, NULL, 34, 'Arrival and transfer to Bhaktapur.', 'D', '2025-10-27 03:20:38', '2025-10-27 03:42:59', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2055, 155, '2026-10-21', 'normal', NULL, NULL, 34, 'Drive to Namobuddha visit monastery and evenig sightseeing at Bhaktapur.', 'B', '2025-10-27 03:20:38', '2025-10-27 03:42:59', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2056, 155, '2026-10-22', 'normal', NULL, NULL, 35, 'Drive from Bhaktapur to Charaudi and rafting to Kurintar and short walk to resort.', 'B, D', '2025-10-27 03:20:38', '2025-10-27 03:42:59', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2057, 155, '2026-10-23', 'normal', NULL, NULL, 36, 'Walk back to the highway and drive from Kurintar to Gorkha.', 'B, D', '2025-10-27 03:20:38', '2025-10-27 03:42:59', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2058, 155, '2026-10-24', 'normal', NULL, NULL, 37, 'Drive from Gorkha to Pokhara and further upto Dhampus Phedi and hike to Dhampus.', 'B, D', '2025-10-27 03:20:38', '2025-10-27 03:42:59', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2059, 155, '2026-10-25', 'normal', NULL, NULL, 37, 'Hike from Dhampus to Potana and to Australian camp and back to lodge.Evenign Momo Class.', 'B, D', '2025-10-27 03:20:38', '2025-10-27 03:42:59', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2060, 155, '2026-10-26', 'normal', NULL, NULL, 38, 'After breakfast hike to Gurung village and lunch with Locals and hike to Suiket phedi and drive to Pokhara.', 'B, L, D', '2025-10-27 03:20:38', '2025-10-27 03:42:59', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2061, 155, '2026-10-27', 'normal', NULL, NULL, 38, 'Boating over Phewa lake and hike to Peace stupa and back to hotel.', 'B, D', '2025-10-27 03:20:38', '2025-10-27 03:42:59', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2062, 155, '2026-10-28', 'normal', NULL, NULL, 39, 'Drive from Pokhara to Chitwan .', 'B, L, D', '2025-10-27 03:20:38', '2025-10-27 03:42:59', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2063, 155, '2026-10-29', 'normal', NULL, NULL, 39, 'Jungle activities in Chitwan National Park', 'B, L, D', '2025-10-27 03:20:38', '2025-10-27 03:42:59', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2064, 155, '2026-10-30', 'normal', NULL, NULL, 17, 'Drive from Chitwan to Kathmandu and sightseeing at Pashupati.', 'B, D', '2025-10-27 03:20:38', '2025-10-27 03:42:59', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2065, 155, '2026-10-31', 'normal', NULL, NULL, 17, 'Explore Ason Market and drive to Childrens home.', 'B', '2025-10-27 03:20:38', '2025-10-27 03:42:59', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2066, 155, '2026-11-01', 'normal', NULL, NULL, NULL, '', 'B', '2025-10-27 03:20:38', '2025-10-27 03:42:57', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(2067, 156, '2026-11-09', 'normal', NULL, NULL, NULL, 'Departure From Home', '', '2025-10-27 03:21:05', '2025-10-27 03:43:13', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(2068, 156, '2026-11-10', 'normal', NULL, NULL, 34, 'Arrival and transfer to Bhaktapur.', 'D', '2025-10-27 03:21:05', '2025-10-27 03:43:14', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2069, 156, '2026-11-11', 'normal', NULL, NULL, 34, 'Drive to Namobuddha visit monastery and evenig sightseeing at Bhaktapur.', 'B', '2025-10-27 03:21:05', '2025-10-27 03:43:14', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2070, 156, '2026-11-12', 'normal', NULL, NULL, 35, 'Drive from Bhaktapur to Charaudi and rafting to Kurintar and short walk to resort.', 'B, D', '2025-10-27 03:21:05', '2025-10-27 03:43:14', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2071, 156, '2026-11-13', 'normal', NULL, NULL, 36, 'Walk back to the highway and drive from Kurintar to Gorkha.', 'B, D', '2025-10-27 03:21:05', '2025-10-27 03:43:14', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2072, 156, '2026-11-14', 'normal', NULL, NULL, 37, 'Drive from Gorkha to Pokhara and further upto Dhampus Phedi and hike to Dhampus.', 'B, D', '2025-10-27 03:21:05', '2025-10-27 03:43:14', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2073, 156, '2026-11-15', 'normal', NULL, NULL, 37, 'Hike from Dhampus to Potana and to Australian camp and back to lodge.Evenign Momo Class.', 'B, D', '2025-10-27 03:21:05', '2025-10-27 03:43:14', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2074, 156, '2026-11-16', 'normal', NULL, NULL, 38, 'After breakfast hike to Gurung village and lunch with Locals and hike to Suiket phedi and drive to Pokhara.', 'B, L, D', '2025-10-27 03:21:05', '2025-10-27 03:43:14', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2075, 156, '2026-11-17', 'normal', NULL, NULL, 38, 'Boating over Phewa lake and hike to Peace stupa and back to hotel.', 'B, D', '2025-10-27 03:21:05', '2025-10-27 03:43:14', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2076, 156, '2026-11-18', 'normal', NULL, NULL, 39, 'Drive from Pokhara to Chitwan .', 'B, L, D', '2025-10-27 03:21:05', '2025-10-27 03:43:14', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2077, 156, '2026-11-19', 'normal', NULL, NULL, 39, 'Jungle activities in Chitwan National Park', 'B, L, D', '2025-10-27 03:21:05', '2025-10-27 03:43:14', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2078, 156, '2026-11-20', 'normal', NULL, NULL, 17, 'Drive from Chitwan to Kathmandu and sightseeing at Pashupati.', 'B, D', '2025-10-27 03:21:05', '2025-10-27 03:43:14', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2079, 156, '2026-11-21', 'normal', NULL, NULL, 17, 'Explore Ason Market and drive to Childrens home.', 'B', '2025-10-27 03:21:05', '2025-10-27 03:43:14', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2080, 156, '2026-11-22', 'normal', NULL, NULL, NULL, '', 'B', '2025-10-27 03:21:05', '2025-10-27 03:43:13', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(2081, 157, '2026-12-28', 'normal', NULL, NULL, NULL, 'Departure From Home', '', '2025-10-27 03:22:24', '2025-10-27 03:43:25', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(2082, 157, '2026-12-29', 'normal', NULL, NULL, 34, 'Arrival and transfer to Bhaktapur.', 'D', '2025-10-27 03:22:24', '2025-10-27 03:43:26', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2083, 157, '2026-12-30', 'normal', NULL, NULL, 34, 'Drive to Namobuddha visit monastery and evenig sightseeing at Bhaktapur.', 'B', '2025-10-27 03:22:24', '2025-10-27 03:43:26', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2084, 157, '2026-12-31', 'normal', NULL, NULL, 35, 'Drive from Bhaktapur to Charaudi and rafting to Kurintar and short walk to resort.', 'B, D', '2025-10-27 03:22:24', '2025-10-27 03:43:26', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2085, 157, '2027-01-01', 'normal', NULL, NULL, 36, 'Walk back to the highway and drive from Kurintar to Gorkha.', 'B, D', '2025-10-27 03:22:24', '2025-10-27 03:43:26', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2086, 157, '2027-01-02', 'normal', NULL, NULL, 37, 'Drive from Gorkha to Pokhara and further upto Dhampus Phedi and hike to Dhampus.', 'B, D', '2025-10-27 03:22:24', '2025-10-27 03:43:26', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2087, 157, '2027-01-03', 'normal', NULL, NULL, 37, 'Hike from Dhampus to Potana and to Australian camp and back to lodge.Evenign Momo Class.', 'B, D', '2025-10-27 03:22:24', '2025-10-27 03:43:26', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2088, 157, '2027-01-04', 'normal', NULL, NULL, 38, 'After breakfast hike to Gurung village and lunch with Locals and hike to Suiket phedi and drive to Pokhara.', 'B, L, D', '2025-10-27 03:22:24', '2025-10-27 03:43:26', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2089, 157, '2027-01-05', 'normal', NULL, NULL, 38, 'Boating over Phewa lake and hike to Peace stupa and back to hotel.', 'B, D', '2025-10-27 03:22:24', '2025-10-27 03:43:26', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2090, 157, '2027-01-06', 'normal', NULL, NULL, 39, 'Drive from Pokhara to Chitwan .', 'B, L, D', '2025-10-27 03:22:24', '2025-10-27 03:43:26', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2091, 157, '2027-01-07', 'normal', NULL, NULL, 39, 'Jungle activities in Chitwan National Park', 'B, L, D', '2025-10-27 03:22:24', '2025-10-27 03:43:26', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2092, 157, '2027-01-08', 'normal', NULL, NULL, 17, 'Drive from Chitwan to Kathmandu and sightseeing at Pashupati.', 'B, D', '2025-10-27 03:22:24', '2025-10-27 03:43:26', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2093, 157, '2027-01-09', 'normal', NULL, NULL, 17, 'Explore Ason Market and drive to Childrens home.', 'B', '2025-10-27 03:22:24', '2025-10-27 03:43:26', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2094, 157, '2027-01-10', 'normal', NULL, NULL, NULL, '', 'B', '2025-10-27 03:22:24', '2025-10-27 03:43:25', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(2095, 158, '2026-11-23', 'normal', NULL, NULL, NULL, 'Departure From Home', '', '2025-10-27 03:23:21', '2025-10-27 03:43:38', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(2096, 158, '2026-11-24', 'normal', NULL, NULL, 34, 'Arrival and transfer to Bhaktapur.', 'D', '2025-10-27 03:23:21', '2025-10-27 03:43:39', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2097, 158, '2026-11-25', 'normal', NULL, NULL, 34, 'Drive to Namobuddha visit monastery and evenig sightseeing at Bhaktapur.', 'B', '2025-10-27 03:23:21', '2025-10-27 03:43:39', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2098, 158, '2026-11-26', 'normal', NULL, NULL, 35, 'Drive from Bhaktapur to Charaudi and rafting to Kurintar and short walk to resort.', 'B, D', '2025-10-27 03:23:21', '2025-10-27 03:43:39', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2099, 158, '2026-11-27', 'normal', NULL, NULL, 36, 'Walk back to the highway and drive from Kurintar to Gorkha.', 'B, D', '2025-10-27 03:23:21', '2025-10-27 03:43:39', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2100, 158, '2026-11-28', 'normal', NULL, NULL, 37, 'Drive from Gorkha to Pokhara and further upto Dhampus Phedi and hike to Dhampus.', 'B, D', '2025-10-27 03:23:21', '2025-10-27 03:43:39', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2101, 158, '2026-11-29', 'normal', NULL, NULL, 37, 'Hike from Dhampus to Potana and to Australian camp and back to lodge.Evenign Momo Class.', 'B, D', '2025-10-27 03:23:21', '2025-10-27 03:43:39', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2102, 158, '2026-11-30', 'normal', NULL, NULL, 38, 'After breakfast hike to Gurung village and lunch with Locals and hike to Suiket phedi and drive to Pokhara.', 'B, L, D', '2025-10-27 03:23:21', '2025-10-27 03:43:39', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2103, 158, '2026-12-01', 'normal', NULL, NULL, 38, 'Boating over Phewa lake and hike to Peace stupa and back to hotel.', 'B, D', '2025-10-27 03:23:21', '2025-10-27 03:43:39', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2104, 158, '2026-12-02', 'normal', NULL, NULL, 39, 'Drive from Pokhara to Chitwan .', 'B, L, D', '2025-10-27 03:23:21', '2025-10-27 03:43:39', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2105, 158, '2026-12-03', 'normal', NULL, NULL, 39, 'Jungle activities in Chitwan National Park', 'B, L, D', '2025-10-27 03:23:21', '2025-10-27 03:43:39', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2106, 158, '2026-12-04', 'normal', NULL, NULL, 17, 'Drive from Chitwan to Kathmandu and sightseeing at Pashupati.', 'B, D', '2025-10-27 03:23:21', '2025-10-27 03:43:39', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2107, 158, '2026-12-05', 'normal', NULL, NULL, 17, 'Explore Ason Market and drive to Childrens home.', 'B', '2025-10-27 03:23:21', '2025-10-27 03:43:39', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2108, 158, '2026-12-06', 'normal', NULL, NULL, NULL, '', 'B', '2025-10-27 03:23:21', '2025-10-27 03:43:38', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(2109, 159, '2026-03-04', 'normal', NULL, NULL, NULL, 'Departure From Home', '', '2025-10-27 03:25:20', '2025-10-27 11:06:13', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(2110, 159, '2026-03-05', 'normal', NULL, NULL, 14, 'Arrival and Transfer to Hotel at Bhaktapur', 'D', '2025-10-27 03:25:20', '2025-10-27 11:06:17', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2111, 159, '2026-03-06', 'normal', NULL, NULL, 14, 'Sightseeing of Royal City Bhaktapur', 'B', '2025-10-27 03:25:20', '2025-10-27 11:06:17', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2112, 159, '2026-03-07', 'normal', NULL, NULL, 41, 'Drive from Bhaktapur to Changunarayan and hike to Telko and drive to Nagarkot', 'B', '2025-10-27 03:25:20', '2025-10-27 11:06:17', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0);
INSERT INTO `itinerary_days` (`id`, `trip_id`, `day_date`, `day_type`, `guide_id`, `vehicle_id`, `hotel_id`, `notes`, `services_provided`, `created_at`, `updated_at`, `room_type_id`, `room_type_data`, `hotel_informed`, `guide_informed`, `vehicle_informed`) VALUES
(2113, 159, '2026-03-08', 'normal', NULL, NULL, 42, 'Hike from Nagarkot to Telkot and drive to Kathmandu', 'B', '2025-10-27 03:25:20', '2025-10-27 11:06:17', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2114, 159, '2026-03-09', 'normal', NULL, NULL, 43, 'Drive from Kathmandu to Bandipur', 'B', '2025-10-27 03:25:20', '2025-10-27 11:06:17', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2115, 159, '2026-03-10', 'normal', NULL, NULL, 37, 'Drive from Bandipur to Dhampus Phedi and hike to Dhampus', 'B, L, D', '2025-10-27 03:25:20', '2025-10-27 11:06:17', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2116, 159, '2026-03-11', 'normal', NULL, NULL, 40, 'Trek from Dhampus to Landruk (1,640 m)', 'B, L, D', '2025-10-27 03:25:20', '2025-10-27 11:06:17', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2117, 159, '2026-03-12', 'normal', NULL, NULL, 44, 'Trek from Landruk to Ghandruk ( 2,050 m)', 'B, L, D', '2025-10-27 03:25:20', '2025-10-27 11:06:17', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2118, 159, '2026-03-13', 'normal', NULL, NULL, 45, 'Trek from Ghandruk to Tadapani (2, 685 m)', 'B, L, D', '2025-10-27 03:25:20', '2025-10-27 11:06:17', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2119, 159, '2026-03-14', 'normal', NULL, NULL, 46, 'Trek from Tadapani to Ghorepani (2,870 m)', 'B, L, D', '2025-10-27 03:25:20', '2025-10-27 11:06:17', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2120, 159, '2026-03-15', 'normal', NULL, NULL, 38, 'Early morning hike to Poonhill for sunrise and back to Ghorpania to Ulleri and drive to Pokhara.', 'B, L, D', '2025-10-27 03:25:20', '2025-10-27 11:06:17', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2121, 159, '2026-03-16', 'normal', NULL, NULL, 38, 'Exploration day at Pokhara', 'B', '2025-10-27 03:25:20', '2025-10-27 11:06:17', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2122, 159, '2026-03-17', 'normal', NULL, NULL, 39, 'Drive from Pokhara to Chitwan National Park', 'B, L, D', '2025-10-27 03:25:20', '2025-10-27 11:06:17', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2123, 159, '2026-03-18', 'normal', NULL, NULL, 39, 'Jungle Activities at Chitwan National Park', 'B, L, D', '2025-10-27 03:25:20', '2025-10-27 11:06:17', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2124, 159, '2026-03-19', 'normal', NULL, NULL, 42, 'Drive back from Chitwan to Kathmandu and evening Pashupati and Boudhnath sightseeing, farewell dinner and drive back to Hotel.', 'B, D', '2025-10-27 03:25:20', '2025-10-27 11:06:17', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2125, 159, '2026-03-20', 'normal', NULL, NULL, NULL, 'Departure Transfer to airport.', 'B', '2025-10-27 03:25:20', '2025-10-27 11:06:13', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(2126, 160, '2026-04-08', 'normal', NULL, NULL, NULL, 'Departure From Home', '', '2025-10-27 03:25:35', '2025-10-27 03:43:50', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(2127, 160, '2026-04-09', 'normal', NULL, NULL, 14, 'Arrival and Transfer to Hotel at Bhaktapur', 'D', '2025-10-27 03:25:35', '2025-10-27 03:43:51', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2128, 160, '2026-04-10', 'normal', NULL, NULL, 14, 'Sightseeing of Royal City Bhaktapur', 'B', '2025-10-27 03:25:35', '2025-10-27 03:43:51', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2129, 160, '2026-04-11', 'normal', NULL, NULL, 41, 'Drive from Bhaktapur to Changunarayan and hike to Telko and drive to Nagarkot', 'B', '2025-10-27 03:25:35', '2025-10-27 03:43:51', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2130, 160, '2026-04-12', 'normal', NULL, NULL, 42, 'Hike from Nagarkot to Telkot and drive to Kathmandu', 'B', '2025-10-27 03:25:35', '2025-10-27 03:43:51', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2131, 160, '2026-04-13', 'normal', NULL, NULL, 43, 'Drive from Kathmandu to Bandipur', 'B', '2025-10-27 03:25:35', '2025-10-27 03:43:51', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2132, 160, '2026-04-14', 'normal', NULL, NULL, 37, 'Drive from Bandipur to Dhampus Phedi and hike to Dhampus', 'B, L, D', '2025-10-27 03:25:35', '2025-10-27 03:43:51', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2133, 160, '2026-04-15', 'normal', NULL, NULL, 40, 'Trek from Dhampus to Landruk (1,640 m)', 'B, L, D', '2025-10-27 03:25:35', '2025-10-27 03:43:51', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2134, 160, '2026-04-16', 'normal', NULL, NULL, 44, 'Trek from Landruk to Ghandruk ( 2,050 m)', 'B, L, D', '2025-10-27 03:25:35', '2025-10-27 03:43:51', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2135, 160, '2026-04-17', 'normal', NULL, NULL, 45, 'Trek from Ghandruk to Tadapani (2, 685 m)', 'B, L, D', '2025-10-27 03:25:35', '2025-10-27 03:43:51', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2136, 160, '2026-04-18', 'normal', NULL, NULL, 46, 'Trek from Tadapani to Ghorepani (2,870 m)', 'B, L, D', '2025-10-27 03:25:35', '2025-10-27 03:43:51', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2137, 160, '2026-04-19', 'normal', NULL, NULL, 38, 'Early morning hike to Poonhill for sunrise and back to Ghorpania to Ulleri and drive to Pokhara.', 'B, L, D', '2025-10-27 03:25:35', '2025-10-27 03:43:51', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2138, 160, '2026-04-20', 'normal', NULL, NULL, 38, 'Exploration day at Pokhara', 'B', '2025-10-27 03:25:35', '2025-10-27 03:43:51', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2139, 160, '2026-04-21', 'normal', NULL, NULL, 39, 'Drive from Pokhara to Chitwan National Park', 'B, L, D', '2025-10-27 03:25:35', '2025-10-27 03:43:51', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2140, 160, '2026-04-22', 'normal', NULL, NULL, 39, 'Jungle Activities at Chitwan National Park', 'B, L, D', '2025-10-27 03:25:35', '2025-10-27 03:43:51', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2141, 160, '2026-04-23', 'normal', NULL, NULL, 42, 'Drive back from Chitwan to Kathmandu and evening Pashupati and Boudhnath sightseeing, farewell dinner and drive back to Hotel.', 'B, D', '2025-10-27 03:25:35', '2025-10-27 03:43:51', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2142, 160, '2026-04-24', 'normal', NULL, NULL, NULL, 'Departure Transfer to airport.', 'B', '2025-10-27 03:25:35', '2025-10-27 03:43:50', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(2143, 161, '2026-10-21', 'normal', NULL, NULL, NULL, 'Departure From Home', '', '2025-10-27 03:26:35', '2025-10-27 03:43:59', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(2144, 161, '2026-10-22', 'normal', NULL, NULL, 14, 'Arrival and Transfer to Hotel at Bhaktapur', 'D', '2025-10-27 03:26:35', '2025-10-27 03:44:00', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2145, 161, '2026-10-23', 'normal', NULL, NULL, 14, 'Sightseeing of Royal City Bhaktapur', 'B', '2025-10-27 03:26:35', '2025-10-27 03:44:00', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2146, 161, '2026-10-24', 'normal', NULL, NULL, 41, 'Drive from Bhaktapur to Changunarayan and hike to Telko and drive to Nagarkot', 'B', '2025-10-27 03:26:35', '2025-10-27 03:44:00', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2147, 161, '2026-10-25', 'normal', NULL, NULL, 42, 'Hike from Nagarkot to Telkot and drive to Kathmandu', 'B', '2025-10-27 03:26:35', '2025-10-27 03:44:00', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2148, 161, '2026-10-26', 'normal', NULL, NULL, 43, 'Drive from Kathmandu to Bandipur', 'B', '2025-10-27 03:26:35', '2025-10-27 03:44:00', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2149, 161, '2026-10-27', 'normal', NULL, NULL, 37, 'Drive from Bandipur to Dhampus Phedi and hike to Dhampus', 'B, L, D', '2025-10-27 03:26:35', '2025-10-27 03:44:00', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2150, 161, '2026-10-28', 'normal', NULL, NULL, 40, 'Trek from Dhampus to Landruk (1,640 m)', 'B, L, D', '2025-10-27 03:26:35', '2025-10-27 03:44:00', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2151, 161, '2026-10-29', 'normal', NULL, NULL, 44, 'Trek from Landruk to Ghandruk ( 2,050 m)', 'B, L, D', '2025-10-27 03:26:35', '2025-10-27 03:44:00', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2152, 161, '2026-10-30', 'normal', NULL, NULL, 45, 'Trek from Ghandruk to Tadapani (2, 685 m)', 'B, L, D', '2025-10-27 03:26:35', '2025-10-27 03:44:00', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2153, 161, '2026-10-31', 'normal', NULL, NULL, 46, 'Trek from Tadapani to Ghorepani (2,870 m)', 'B, L, D', '2025-10-27 03:26:35', '2025-10-27 03:44:00', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2154, 161, '2026-11-01', 'normal', NULL, NULL, 38, 'Early morning hike to Poonhill for sunrise and back to Ghorpania to Ulleri and drive to Pokhara.', 'B, L, D', '2025-10-27 03:26:35', '2025-10-27 03:44:00', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2155, 161, '2026-11-02', 'normal', NULL, NULL, 38, 'Exploration day at Pokhara', 'B', '2025-10-27 03:26:35', '2025-10-27 03:44:00', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2156, 161, '2026-11-03', 'normal', NULL, NULL, 39, 'Drive from Pokhara to Chitwan National Park', 'B, L, D', '2025-10-27 03:26:35', '2025-10-27 03:44:00', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2157, 161, '2026-11-04', 'normal', NULL, NULL, 39, 'Jungle Activities at Chitwan National Park', 'B, L, D', '2025-10-27 03:26:35', '2025-10-27 03:44:00', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2158, 161, '2026-11-05', 'normal', NULL, NULL, 42, 'Drive back from Chitwan to Kathmandu and evening Pashupati and Boudhnath sightseeing, farewell dinner and drive back to Hotel.', 'B, D', '2025-10-27 03:26:35', '2025-10-27 03:44:00', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2159, 161, '2026-11-06', 'normal', NULL, NULL, NULL, 'Departure Transfer to airport.', 'B', '2025-10-27 03:26:35', '2025-10-27 03:43:59', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(2160, 162, '2026-11-11', 'normal', NULL, NULL, NULL, 'Departure From Home', '', '2025-10-27 03:27:07', '2025-10-27 03:44:07', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(2161, 162, '2026-11-12', 'normal', NULL, NULL, 14, 'Arrival and Transfer to Hotel at Bhaktapur', 'D', '2025-10-27 03:27:07', '2025-10-27 03:44:08', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2162, 162, '2026-11-13', 'normal', NULL, NULL, 14, 'Sightseeing of Royal City Bhaktapur', 'B', '2025-10-27 03:27:07', '2025-10-27 03:44:08', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2163, 162, '2026-11-14', 'normal', NULL, NULL, 41, 'Drive from Bhaktapur to Changunarayan and hike to Telko and drive to Nagarkot', 'B', '2025-10-27 03:27:07', '2025-10-27 03:44:08', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2164, 162, '2026-11-15', 'normal', NULL, NULL, 42, 'Hike from Nagarkot to Telkot and drive to Kathmandu', 'B', '2025-10-27 03:27:07', '2025-10-27 03:44:08', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2165, 162, '2026-11-16', 'normal', NULL, NULL, 43, 'Drive from Kathmandu to Bandipur', 'B', '2025-10-27 03:27:07', '2025-10-27 03:44:08', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2166, 162, '2026-11-17', 'normal', NULL, NULL, 37, 'Drive from Bandipur to Dhampus Phedi and hike to Dhampus', 'B, L, D', '2025-10-27 03:27:07', '2025-10-27 03:44:08', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2167, 162, '2026-11-18', 'normal', NULL, NULL, 40, 'Trek from Dhampus to Landruk (1,640 m)', 'B, L, D', '2025-10-27 03:27:07', '2025-10-27 03:44:08', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2168, 162, '2026-11-19', 'normal', NULL, NULL, 44, 'Trek from Landruk to Ghandruk ( 2,050 m)', 'B, L, D', '2025-10-27 03:27:07', '2025-10-27 03:44:08', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2169, 162, '2026-11-20', 'normal', NULL, NULL, 45, 'Trek from Ghandruk to Tadapani (2, 685 m)', 'B, L, D', '2025-10-27 03:27:07', '2025-10-27 03:44:08', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2170, 162, '2026-11-21', 'normal', NULL, NULL, 46, 'Trek from Tadapani to Ghorepani (2,870 m)', 'B, L, D', '2025-10-27 03:27:07', '2025-10-27 03:44:08', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2171, 162, '2026-11-22', 'normal', NULL, NULL, 38, 'Early morning hike to Poonhill for sunrise and back to Ghorpania to Ulleri and drive to Pokhara.', 'B, L, D', '2025-10-27 03:27:07', '2025-10-27 03:44:08', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2172, 162, '2026-11-23', 'normal', NULL, NULL, 38, 'Exploration day at Pokhara', 'B', '2025-10-27 03:27:07', '2025-10-27 03:44:08', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2173, 162, '2026-11-24', 'normal', NULL, NULL, 39, 'Drive from Pokhara to Chitwan National Park', 'B, L, D', '2025-10-27 03:27:07', '2025-10-27 03:44:08', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2174, 162, '2026-11-25', 'normal', NULL, NULL, 39, 'Jungle Activities at Chitwan National Park', 'B, L, D', '2025-10-27 03:27:07', '2025-10-27 03:44:08', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2175, 162, '2026-11-26', 'normal', NULL, NULL, 42, 'Drive back from Chitwan to Kathmandu and evening Pashupati and Boudhnath sightseeing, farewell dinner and drive back to Hotel.', 'B, D', '2025-10-27 03:27:07', '2025-10-27 03:44:08', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2176, 162, '2026-11-27', 'normal', NULL, NULL, NULL, 'Departure Transfer to airport.', 'B', '2025-10-27 03:27:07', '2025-10-27 03:44:07', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(2194, 164, '2026-12-21', 'normal', NULL, NULL, NULL, 'Departure From Home', '', '2025-10-27 05:50:30', '2025-10-27 05:55:01', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(2195, 164, '2026-12-22', 'normal', NULL, NULL, 14, 'Arrival and Transfer to Hotel at Bhaktapur', 'D', '2025-10-27 05:50:30', '2025-10-27 05:56:37', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 1, 0, 0),
(2196, 164, '2026-12-23', 'normal', NULL, NULL, 14, 'Sightseeing of Royal City Bhaktapur', 'B', '2025-10-27 05:50:30', '2025-10-27 05:56:37', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 1, 0, 0),
(2197, 164, '2026-12-24', 'normal', NULL, NULL, 41, 'Drive from Bhaktapur to Changunarayan and hike to Telko and drive to Nagarkot', 'B', '2025-10-27 05:50:30', '2025-10-27 05:55:01', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2198, 164, '2026-12-25', 'normal', NULL, NULL, 42, 'Hike from Nagarkot to Telkot and drive to Kathmandu', 'B', '2025-10-27 05:50:30', '2025-10-27 05:55:01', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2199, 164, '2026-12-26', 'normal', NULL, NULL, 43, 'Drive from Kathmandu to Bandipur', 'B', '2025-10-27 05:50:30', '2025-10-27 05:55:01', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2200, 164, '2026-12-27', 'normal', NULL, NULL, 37, 'Drive from Bandipur to Dhampus Phedi and hike to Dhampus', 'B, L, D', '2025-10-27 05:50:30', '2025-10-27 05:55:01', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2201, 164, '2026-12-28', 'normal', NULL, NULL, 40, 'Trek from Dhampus to Landruk (1,640 m)', 'B, L, D', '2025-10-27 05:50:30', '2025-10-27 05:55:01', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2202, 164, '2026-12-29', 'normal', NULL, NULL, 44, 'Trek from Landruk to Ghandruk ( 2,050 m)', 'B, L, D', '2025-10-27 05:50:30', '2025-10-27 05:55:01', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2203, 164, '2026-12-30', 'normal', NULL, NULL, 45, 'Trek from Ghandruk to Tadapani (2, 685 m)', 'B, L, D', '2025-10-27 05:50:30', '2025-10-27 05:55:01', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2204, 164, '2026-12-31', 'normal', NULL, NULL, 46, 'Trek from Tadapani to Ghorepani (2,870 m)', 'B, L, D', '2025-10-27 05:50:30', '2025-10-27 05:55:01', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2205, 164, '2027-01-01', 'normal', NULL, NULL, 38, 'Early morning hike to Poonhill for sunrise and back to Ghorpania to Ulleri and drive to Pokhara.', 'B, L, D', '2025-10-27 05:50:30', '2025-10-27 05:56:37', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 1, 0, 0),
(2206, 164, '2027-01-02', 'normal', NULL, NULL, 38, 'Exploration day at Pokhara', 'B', '2025-10-27 05:50:30', '2025-10-27 05:56:37', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 1, 0, 0),
(2207, 164, '2027-01-03', 'normal', NULL, NULL, 39, 'Drive from Pokhara to Chitwan National Park', 'B, L, D', '2025-10-27 05:50:30', '2025-10-27 05:55:01', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2208, 164, '2027-01-04', 'normal', NULL, NULL, 39, 'Jungle Activities at Chitwan National Park', 'B, L, D', '2025-10-27 05:50:30', '2025-10-27 05:55:01', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2209, 164, '2027-01-05', 'normal', NULL, NULL, 42, 'Drive back from Chitwan to Kathmandu and evening Pashupati and Boudhnath sightseeing, farewell dinner and drive back to Hotel.', 'B, D', '2025-10-27 05:50:30', '2025-10-27 05:55:01', NULL, '{\"double\":5,\"twin\":0,\"single\":4,\"triple\":0}', 0, 0, 0),
(2210, 164, '2027-01-06', 'normal', NULL, NULL, NULL, 'Departure Transfer to airport.', 'B', '2025-10-27 05:50:30', '2025-10-27 05:55:01', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(2233, 168, '2025-12-27', 'normal', NULL, NULL, NULL, 'Sightseeing of pashupatinath and Boudha and Farewell dinner Boudha stupa', '', '2025-10-31 06:44:53', '2025-10-31 06:47:18', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(2234, 168, '2025-12-28', 'normal', NULL, NULL, 47, 'Sightseeing of pashupatinath and Boudha and Farewell dinner Boudha stupa', 'D', '2025-10-31 06:44:53', '2025-10-31 06:48:23', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(2235, 168, '2025-12-29', 'normal', NULL, NULL, 47, 'Sightseeing of pashupatinath and Boudha and Farewell dinner Boudha stupa', 'B', '2025-10-31 06:44:53', '2025-10-31 06:48:23', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(2236, 168, '2025-12-30', 'normal', NULL, NULL, 34, 'Sightseeing of pashupatinath and Boudha and Farewell dinner Boudha stupa', 'B', '2025-10-31 06:44:53', '2025-10-31 06:48:23', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(2237, 168, '2025-12-31', 'normal', NULL, NULL, 34, 'Sightseeing of pashupatinath and Boudha and Farewell dinner Boudha stupa', 'B', '2025-10-31 06:44:53', '2025-10-31 06:48:23', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(2238, 168, '2026-01-01', 'normal', NULL, NULL, 48, 'Sightseeing of pashupatinath and Boudha and Farewell dinner Boudha stupa', 'B, L, D', '2025-10-31 06:44:53', '2025-10-31 06:48:23', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(2239, 168, '2026-01-02', 'normal', NULL, NULL, 48, 'Sightseeing of pashupatinath and Boudha and Farewell dinner Boudha stupa', 'B, L, D', '2025-10-31 06:44:53', '2025-10-31 06:48:23', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(2240, 168, '2026-01-03', 'normal', NULL, NULL, 49, 'Sightseeing of pashupatinath and Boudha and Farewell dinner Boudha stupa', 'B', '2025-10-31 06:44:53', '2025-10-31 06:48:23', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(2241, 168, '2026-01-04', 'normal', NULL, NULL, 37, 'Sightseeing of pashupatinath and Boudha and Farewell dinner Boudha stupa', 'B, D', '2025-10-31 06:44:53', '2025-10-31 06:48:23', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(2242, 168, '2026-01-05', 'normal', NULL, NULL, 50, 'Sightseeing of pashupatinath and Boudha and Farewell dinner Boudha stupa', 'B, L', '2025-10-31 06:44:53', '2025-10-31 06:48:23', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(2243, 168, '2026-01-06', 'normal', NULL, NULL, 50, 'Sightseeing of pashupatinath and Boudha and Farewell dinner Boudha stupa', 'B', '2025-10-31 06:44:53', '2025-10-31 06:48:23', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(2244, 168, '2026-01-07', 'normal', NULL, NULL, 51, 'Sightseeing of pashupatinath and Boudha and Farewell dinner Boudha stupa', 'B, D', '2025-10-31 06:44:53', '2025-10-31 06:48:23', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(2245, 168, '2026-01-08', 'normal', NULL, NULL, 51, 'Sightseeing of pashupatinath and Boudha and Farewell dinner Boudha stupa', 'B', '2025-10-31 06:44:53', '2025-10-31 06:48:23', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(2246, 168, '2026-01-09', 'normal', NULL, NULL, NULL, 'Depature tansfer to airport', 'B', '2025-10-31 06:44:53', '2025-10-31 06:47:18', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(2247, 169, '2025-12-19', 'normal', NULL, NULL, NULL, 'Departure from home', '', '2025-10-31 06:53:03', '2025-10-31 06:53:03', NULL, NULL, 0, 0, 0),
(2248, 169, '2025-12-20', 'normal', NULL, NULL, 14, 'Arrival and Transfer to Hotel at Bhaktapur', 'D', '2025-10-31 06:53:03', '2025-10-31 06:53:03', NULL, NULL, 0, 0, 0),
(2249, 169, '2025-12-21', 'normal', NULL, NULL, 14, 'Sightseeing of Royal City Bhaktapur', 'B', '2025-10-31 06:53:03', '2025-10-31 06:53:03', NULL, NULL, 0, 0, 0),
(2250, 169, '2025-12-22', 'normal', NULL, NULL, 41, 'Drive from Bhaktapur to Changunarayan and hike to Telko and drive to Nagarkot', 'B', '2025-10-31 06:53:03', '2025-10-31 06:53:03', NULL, NULL, 0, 0, 0),
(2251, 169, '2025-12-23', 'normal', NULL, NULL, 42, 'Hike from Nagarkot to Telkot and drive to Kathmandu', 'B', '2025-10-31 06:53:03', '2025-10-31 06:53:03', NULL, NULL, 0, 0, 0),
(2252, 169, '2025-12-24', 'normal', NULL, NULL, 43, 'Drive from Kathmandu to Bandipur', 'B', '2025-10-31 06:53:03', '2025-10-31 06:53:03', NULL, NULL, 0, 0, 0),
(2253, 169, '2025-12-25', 'normal', NULL, NULL, 37, 'Drive from Bandipur to Dhampus Phedi and hike to Dhampus', 'B, L, D', '2025-10-31 06:53:03', '2025-10-31 06:53:03', NULL, NULL, 0, 0, 0),
(2254, 169, '2025-12-26', 'normal', NULL, NULL, 40, '', 'B, L, D', '2025-10-31 06:53:03', '2025-10-31 06:53:03', NULL, NULL, 0, 0, 0),
(2255, 169, '2025-12-27', 'normal', NULL, NULL, 44, '', 'B, L, D', '2025-10-31 06:53:03', '2025-10-31 06:53:03', NULL, NULL, 0, 0, 0),
(2256, 169, '2025-12-28', 'normal', NULL, NULL, 45, '', 'B, L, D', '2025-10-31 06:53:03', '2025-10-31 06:53:03', NULL, NULL, 0, 0, 0),
(2257, 169, '2025-12-29', 'normal', NULL, NULL, 46, '', 'B, L, D', '2025-10-31 06:53:03', '2025-10-31 06:53:03', NULL, NULL, 0, 0, 0),
(2258, 169, '2025-12-30', 'normal', NULL, NULL, 38, 'Early morning hike to Poonhill for sunrise and back to Ghorpania to Ulleri and drive to Pokhara.', 'B, L, D', '2025-10-31 06:53:03', '2025-10-31 06:53:03', NULL, NULL, 0, 0, 0),
(2259, 169, '2025-12-31', 'normal', NULL, NULL, 38, 'Exploration day at Pokhara', 'B', '2025-10-31 06:53:03', '2025-10-31 06:53:03', NULL, NULL, 0, 0, 0),
(2260, 169, '2026-01-01', 'normal', NULL, NULL, 39, 'Drive from Pokhara to Chitwan National Park', 'B, L, D', '2025-10-31 06:53:03', '2025-10-31 06:53:03', NULL, NULL, 0, 0, 0),
(2261, 169, '2026-01-02', 'normal', NULL, NULL, 39, 'Jungle Activities at Chitwan National Park', 'B, L, D', '2025-10-31 06:53:03', '2025-10-31 06:53:03', NULL, NULL, 0, 0, 0),
(2262, 169, '2026-01-03', 'normal', NULL, NULL, 42, '', 'B, D', '2025-10-31 06:53:03', '2025-10-31 06:53:03', NULL, NULL, 0, 0, 0),
(2263, 169, '2026-01-04', 'normal', NULL, NULL, NULL, 'Departure Transfer to airport.', 'B', '2025-10-31 06:53:03', '2025-10-31 06:53:03', NULL, NULL, 0, 0, 0),
(2264, 170, '2025-12-25', 'normal', NULL, NULL, NULL, 'Departure from home', '', '2025-10-31 06:54:27', '2025-11-04 04:48:22', NULL, '{\"double\":0,\"twin\":1,\"single\":0,\"triple\":0}', 0, 0, 0),
(2265, 170, '2025-12-26', 'normal', NULL, NULL, 34, 'Arrival and transfer to Bhaktapur.', 'D', '2025-10-31 06:54:27', '2025-11-04 04:48:22', NULL, '{\"double\":0,\"twin\":1,\"single\":0,\"triple\":0}', 0, 0, 0),
(2266, 170, '2025-12-27', 'normal', NULL, NULL, 34, 'Drive to Namobuddha visit monastery and evenig sightseeing at Bhaktapur.', 'B', '2025-10-31 06:54:27', '2025-11-04 04:48:22', NULL, '{\"double\":0,\"twin\":1,\"single\":0,\"triple\":0}', 0, 0, 0),
(2267, 170, '2025-12-28', 'normal', NULL, NULL, 35, 'Drive from Bhaktapur to Charaudi and rafting to Kurintar and short walk to resort.', 'B, D', '2025-10-31 06:54:27', '2025-11-04 04:48:22', NULL, '{\"double\":0,\"twin\":1,\"single\":0,\"triple\":0}', 0, 0, 0),
(2268, 170, '2025-12-29', 'normal', NULL, NULL, 36, 'Walk back to the highway and drive from Kurintar to Gorkha.', 'B, D', '2025-10-31 06:54:27', '2025-11-04 04:48:22', NULL, '{\"double\":0,\"twin\":1,\"single\":0,\"triple\":0}', 0, 0, 0),
(2269, 170, '2025-12-30', 'normal', NULL, NULL, 37, 'Drive from Gorkha to Pokhara and further upto Dhampus Phedi and hike to Dhampus.', 'B, D', '2025-10-31 06:54:27', '2025-11-04 04:48:22', NULL, '{\"double\":0,\"twin\":1,\"single\":0,\"triple\":0}', 0, 0, 0),
(2270, 170, '2025-12-31', 'normal', NULL, NULL, 37, 'Hike from Dhampus to Potana and to Australian camp and back to lodge.Evenign Momo Class.', 'B, D', '2025-10-31 06:54:27', '2025-11-04 04:48:22', NULL, '{\"double\":0,\"twin\":1,\"single\":0,\"triple\":0}', 0, 0, 0),
(2271, 170, '2026-01-01', 'normal', NULL, NULL, 38, 'After breakfast hike to Gurung village and lunch with Locals and hike to Suiket phedi and drive to Pokhara.', 'B, L, D', '2025-10-31 06:54:27', '2025-11-04 04:48:22', NULL, '{\"double\":0,\"twin\":1,\"single\":0,\"triple\":0}', 0, 0, 0),
(2272, 170, '2026-01-02', 'normal', NULL, NULL, 38, 'Boating over Phewa lake and hike to Peace stupa and back to hotel.', 'B, D', '2025-10-31 06:54:27', '2025-11-04 04:48:22', NULL, '{\"double\":0,\"twin\":1,\"single\":0,\"triple\":0}', 0, 0, 0),
(2273, 170, '2026-01-03', 'normal', NULL, NULL, 39, 'Drive from Pokhara to Chitwan .', 'B, L, D', '2025-10-31 06:54:27', '2025-11-04 04:48:22', NULL, '{\"double\":0,\"twin\":1,\"single\":0,\"triple\":0}', 0, 0, 0),
(2274, 170, '2026-01-04', 'normal', NULL, NULL, 39, 'Jungle activities in Chitwan National Park', 'B, L, D', '2025-10-31 06:54:27', '2025-11-04 04:48:22', NULL, '{\"double\":0,\"twin\":1,\"single\":0,\"triple\":0}', 0, 0, 0),
(2275, 170, '2026-01-05', 'normal', NULL, NULL, 17, 'Drive from Chitwan to Kathmandu and sightseeing at Pashupati.', 'B, D', '2025-10-31 06:54:27', '2025-11-04 04:48:22', NULL, '{\"double\":0,\"twin\":1,\"single\":0,\"triple\":0}', 0, 0, 0),
(2276, 170, '2026-01-06', 'normal', NULL, NULL, 17, 'Explore Ason Market and drive to Childrens home.', 'B', '2025-10-31 06:54:27', '2025-11-04 04:48:22', NULL, '{\"double\":0,\"twin\":1,\"single\":0,\"triple\":0}', 0, 0, 0),
(2277, 170, '2026-01-07', 'normal', NULL, NULL, NULL, 'Departure Transfer to airport.', 'B', '2025-10-31 06:54:27', '2025-11-04 04:48:22', NULL, '{\"double\":0,\"twin\":1,\"single\":0,\"triple\":0}', 0, 0, 0),
(2280, 172, '2025-11-05', 'normal', NULL, NULL, NULL, 'Departure from home', '', '2025-11-02 03:20:58', '2025-11-03 11:12:02', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(2281, 172, '2025-11-06', 'normal', NULL, NULL, 14, 'Arrival and transfer to Bhaktapur.', '', '2025-11-02 03:20:58', '2025-11-02 10:34:25', NULL, '{\"double\":0,\"twin\":0,\"single\":1,\"triple\":0}', 1, 0, 0),
(2282, 172, '2025-11-07', 'normal', NULL, NULL, 52, 'After breakfast: Sightseeing in the old historical city of Bhaktapur. Afternoon: Drive to the monastery. Visit the Buddhist center. Overnight stay at the monastery.', 'B', '2025-11-02 03:20:58', '2025-11-02 10:34:25', NULL, '{\"double\":0,\"twin\":0,\"single\":1,\"triple\":0}', 1, 0, 0),
(2283, 172, '2025-11-08', 'normal', NULL, NULL, 43, 'Drive from Kathmandu to Bandipur', 'B', '2025-11-02 03:20:58', '2025-11-02 10:34:25', NULL, '{\"double\":0,\"twin\":0,\"single\":1,\"triple\":0}', 1, 0, 0),
(2284, 172, '2025-11-09', 'normal', NULL, NULL, 37, 'Drive to Dhampus. Witness the mountains and sunrise over the Himalayas. Breathtaking panorama in clear weather; otherwise, explore the beautiful Gurung village.', 'B, D', '2025-11-02 03:20:58', '2025-11-02 10:34:25', NULL, '{\"double\":0,\"twin\":0,\"single\":1,\"triple\":0}', 1, 0, 0),
(2285, 172, '2025-11-10', 'normal', NULL, NULL, 53, 'Trek from Dhampus to Landruk (1 640 m)', 'B, L, D', '2025-11-02 03:20:58', '2025-11-02 10:34:25', NULL, '{\"double\":0,\"twin\":0,\"single\":1,\"triple\":0}', 1, 0, 0),
(2286, 172, '2025-11-11', 'normal', NULL, NULL, 53, 'Hike to Ghandruk . Afternoon: Explore the village. Walking time: 4 hours.', 'B, L, D', '2025-11-02 03:20:58', '2025-11-02 10:34:25', NULL, '{\"double\":0,\"twin\":0,\"single\":1,\"triple\":0}', 1, 0, 0),
(2287, 172, '2025-11-12', 'normal', NULL, NULL, 53, 'Hike up to Tadapani and continue to Banthanti . Walking time: approx. 6 hours.', 'B, L, D', '2025-11-02 03:20:58', '2025-11-02 10:34:25', NULL, '{\"double\":0,\"twin\":0,\"single\":1,\"triple\":0}', 1, 0, 0),
(2288, 172, '2025-11-13', 'normal', NULL, NULL, 53, 'Hike to the tour highlight, Ghorepani . Walking time: 4 hours.', 'B, L, D', '2025-11-02 03:20:58', '2025-11-02 10:34:25', NULL, '{\"double\":0,\"twin\":0,\"single\":1,\"triple\":0}', 1, 0, 0),
(2289, 172, '2025-11-14', 'normal', NULL, NULL, 54, 'Sunrise ascent to Poon Hill . Hike down to Hile. Drive back to Pokhara. Afternoon: Free at leisure by Phewa Lake.', 'B', '2025-11-02 03:20:58', '2025-11-02 10:34:25', NULL, '{\"double\":0,\"twin\":0,\"single\":1,\"triple\":0}', 1, 0, 0),
(2290, 172, '2025-11-15', 'normal', NULL, NULL, 54, 'Pokhara on your own. Sightseeing as desired - cycling, hiking - or engaging with YOGA and Ayurveda.', 'B', '2025-11-02 03:20:58', '2025-11-02 10:34:25', NULL, '{\"double\":0,\"twin\":0,\"single\":1,\"triple\":0}', 1, 0, 0),
(2291, 172, '2025-11-16', 'normal', NULL, NULL, 17, 'Kathmandu Sightseeing and Dinner', '', '2025-11-02 03:20:58', '2025-11-02 10:34:25', NULL, '{\"double\":0,\"twin\":0,\"single\":1,\"triple\":0}', 1, 0, 0),
(2292, 172, '2025-11-17', 'normal', NULL, NULL, NULL, 'Departure Transfer to airport.', 'B', '2025-11-02 03:20:58', '2025-11-03 11:12:02', NULL, '{\"double\":0,\"twin\":0,\"single\":0,\"triple\":0}', 0, 0, 0),
(2293, 173, '2025-11-05', 'normal', NULL, NULL, 53, '', 'B, L, D', '2025-11-04 01:22:08', '2025-11-04 01:23:13', NULL, '{\"double\":1,\"twin\":0,\"single\":0,\"triple\":0}', 1, 0, 0),
(2294, 173, '2025-11-06', 'normal', NULL, NULL, 52, '', 'B, L, D', '2025-11-04 01:22:08', '2025-11-04 01:23:13', NULL, '{\"double\":1,\"twin\":0,\"single\":0,\"triple\":0}', 1, 0, 0),
(2295, 174, '2025-11-13', 'normal', NULL, NULL, 53, '', 'B, L, D', '2025-11-04 10:24:51', '2025-11-05 09:11:38', NULL, '{\"double\":6,\"twin\":0,\"single\":1,\"triple\":0}', 1, 0, 0),
(2296, 174, '2025-11-14', 'normal', NULL, NULL, 52, '', 'B, L, D', '2025-11-04 10:24:51', '2025-11-05 09:11:38', NULL, '{\"double\":6,\"twin\":0,\"single\":1,\"triple\":0}', 1, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `package_activities`
--

CREATE TABLE `package_activities` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `package_activities`
--

INSERT INTO `package_activities` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Departure from home', '2025-10-28 03:37:14', '2025-10-28 03:37:14'),
(2, 'Arrival in Kathmandu and transfer to Bhaktapur', '2025-10-28 03:37:14', '2025-10-28 03:37:14'),
(3, 'Bhaktapur Overnight Stay - Overnight in Bhaktapur', '2025-10-28 03:37:14', '2025-10-28 03:37:14'),
(4, 'Transfer to Ktm airport and Fly to Lukla', '2025-10-28 03:37:14', '2025-10-28 03:37:14'),
(5, 'Trek to Namche Bazar', '2025-10-28 03:37:14', '2025-10-28 03:37:14'),
(6, 'Rest in Namche', '2025-10-28 03:37:14', '2025-10-28 03:37:14'),
(7, 'Trek from Namche to Tengboche', '2025-10-28 03:37:14', '2025-10-28 03:37:14'),
(8, 'Trek from Tengboche to Dingboche', '2025-10-28 03:37:14', '2025-10-28 03:37:14'),
(9, 'Rest Day in Dingboche - Acclimatization Day', '2025-10-28 03:37:14', '2025-10-28 03:37:14'),
(10, 'Trek Dingboche to Lobuche', '2025-10-28 03:37:14', '2025-10-28 03:37:14'),
(11, 'Trek to Base Camp - Gorakh Shep', '2025-10-28 03:37:14', '2025-10-28 03:37:14'),
(12, 'Trek Kalapathar and back to Lobuche', '2025-10-28 03:37:14', '2025-10-28 03:37:14'),
(13, 'Trek from Lobuche to Pangboche', '2025-10-28 03:37:14', '2025-10-28 03:37:14'),
(14, 'Trek from Pangboche to Namche', '2025-10-28 03:37:14', '2025-10-28 03:37:14'),
(15, 'Trek from Namche to Lukla', '2025-10-28 03:37:14', '2025-10-28 03:37:14'),
(16, 'Fly to Kathmandu and transfer to Hotel', '2025-10-28 03:37:14', '2025-10-28 03:37:14'),
(17, 'Kathmandu Sightseeing and Dinner', '2025-10-28 03:37:14', '2025-10-28 03:37:14'),
(18, 'Kathmandu rest day Departure home', '2025-10-28 03:37:14', '2025-10-28 03:37:14'),
(20, 'Arrival and transfer to Bhaktapur.', '2025-10-28 03:59:20', '2025-10-28 03:59:20'),
(21, 'Drive to Namobuddha visit monastery and evenig sightseeing at Bhaktapur.', '2025-10-28 03:59:20', '2025-10-28 03:59:20'),
(22, 'Drive from Bhaktapur to Charaudi and rafting to Kurintar and short walk to resort.', '2025-10-28 03:59:20', '2025-10-28 03:59:20'),
(23, 'Walk back to the highway and drive from Kurintar to Gorkha.', '2025-10-28 03:59:20', '2025-10-28 03:59:20'),
(24, 'Drive from Gorkha to Pokhara and further upto Dhampus Phedi and hike to Dhampus.', '2025-10-28 03:59:20', '2025-10-28 03:59:20'),
(25, 'Hike from Dhampus to Potana and to Australian camp and back to lodge.Evenign Momo Class.', '2025-10-28 03:59:20', '2025-10-28 03:59:20'),
(26, 'After breakfast hike to Gurung village and lunch with Locals and hike to Suiket phedi and drive to Pokhara.', '2025-10-28 03:59:20', '2025-10-28 03:59:20'),
(27, 'Boating over Phewa lake and hike to Peace stupa and back to hotel.', '2025-10-28 03:59:20', '2025-10-28 03:59:20'),
(28, 'Drive from Pokhara to Chitwan .', '2025-10-28 03:59:20', '2025-10-28 03:59:20'),
(29, 'Jungle activities in Chitwan National Park', '2025-10-28 03:59:20', '2025-10-28 03:59:20'),
(30, 'Drive from Chitwan to Kathmandu and sightseeing at Pashupati.', '2025-10-28 03:59:20', '2025-10-28 03:59:20'),
(31, 'Explore Ason Market and drive to Childrens home.', '2025-10-28 03:59:20', '2025-10-28 03:59:20'),
(32, 'Depature tansfer to airport', '2025-10-28 03:59:20', '2025-10-28 03:59:20'),
(33, 'Arrival and Transfer to Hotel at Bhaktapur', '2025-10-28 03:59:20', '2025-10-28 03:59:20'),
(34, 'Sightseeing of Royal City Bhaktapur', '2025-10-28 03:59:20', '2025-10-28 03:59:20'),
(35, 'Drive from Bhaktapur to Changunarayan and hike to Telko and drive to Nagarkot', '2025-10-28 03:59:20', '2025-10-28 03:59:20'),
(36, 'Hike from Nagarkot to Telkot and drive to Kathmandu', '2025-10-28 03:59:20', '2025-10-28 03:59:20'),
(37, 'Drive from Kathmandu to Bandipur', '2025-10-28 03:59:20', '2025-10-28 03:59:20'),
(38, 'Drive from Bandipur to Dhampus Phedi and hike to Dhampus', '2025-10-28 03:59:20', '2025-10-28 03:59:20'),
(39, 'Trek from Dhampus to Landruk (1 640 m)', '2025-10-28 03:59:20', '2025-10-28 03:59:20'),
(40, 'Trek from Landruk to Ghandruk ( 2 050 m)', '2025-10-28 03:59:20', '2025-10-28 03:59:20'),
(41, 'Trek from Ghandruk to Tadapani (2 685 m)', '2025-10-28 03:59:20', '2025-10-28 03:59:20'),
(42, 'Trek from Tadapani to Ghorepani (2 870 m)', '2025-10-28 03:59:20', '2025-10-28 03:59:20'),
(43, 'Early morning hike to Poonhill for sunrise and back to Ghorpania to Ulleri and drive to Pokhara.', '2025-10-28 03:59:20', '2025-10-28 03:59:20'),
(44, 'Exploration day at Pokhara', '2025-10-28 03:59:20', '2025-10-28 03:59:20'),
(45, 'Drive from Pokhara to Chitwan National Park', '2025-10-28 03:59:20', '2025-10-28 03:59:20'),
(46, 'Jungle Activities at Chitwan National Park', '2025-10-28 03:59:20', '2025-10-28 03:59:20'),
(47, 'Drive back from Chitwan to Kathmandu and evening Pashupati and Boudhnath sightseeing farewell dinner and drive back to Hotel.', '2025-10-28 03:59:20', '2025-10-28 03:59:20'),
(48, 'Departure Transfer to airport.', '2025-10-28 03:59:20', '2025-10-28 03:59:20'),
(4129, 'Arrival and Transfer to Dhulikhel', '2025-10-31 06:28:57', '2025-10-31 06:28:57'),
(4190, 'Hike from Dhulikhel to Namobuddha monastry and back to hotel', '2025-10-31 06:30:31', '2025-10-31 06:30:31'),
(4251, 'Drive from Dhulokhel to Nagarkot and hike to telkot and drive to changunarayan and back to bhakatapur sightseeing', '2025-10-31 06:31:47', '2025-10-31 06:31:47'),
(4312, 'Drive from Bhaktapur to chitwan National Park', '2025-10-31 06:34:28', '2025-10-31 06:34:28'),
(4403, 'Drive from chitwan to bandipur', '2025-10-31 06:37:19', '2025-10-31 06:37:19'),
(4464, 'Hike from Dhampus to Dhital Gau and drive from Highway to Pokhara', '2025-10-31 06:40:12', '2025-10-31 06:40:12'),
(4525, 'Fly back from Pokhara to Kathmandu and Swayambhu Sightseeing', '2025-10-31 06:42:34', '2025-10-31 06:42:34'),
(4586, 'Sightseeing of pashupatinath and Boudha and Farewell dinner Boudha stupa', '2025-10-31 06:43:36', '2025-10-31 06:43:36'),
(5127, 'After breakfast: Sightseeing in the old historical city of Bhaktapur. Afternoon: Drive to the monastery. Visit the Buddhist center. Overnight stay at the monastery.', '2025-11-02 02:59:46', '2025-11-02 02:59:46'),
(5188, 'Drive to Kathmandu. Drive to Bandipur.', '2025-11-02 03:00:57', '2025-11-02 03:00:57'),
(5249, 'Drive to Dhampus ($1650\\text{m}$). Witness the mountains and sunrise over the Himalayas. Breathtaking panorama in clear weather; otherwise, explore the beautiful Gurung village.', '2025-11-02 03:01:50', '2025-11-02 03:01:50'),
(5310, 'Drive to Dhampus. Witness the mountains and sunrise over the Himalayas. Breathtaking panorama in clear weather; otherwise, explore the beautiful Gurung village.', '2025-11-02 03:02:13', '2025-11-02 03:02:13'),
(5371, 'Hike to Ghandruk. Afternoon: Explore the village. Walking time: 4 hours.', '2025-11-02 03:04:09', '2025-11-02 03:04:09'),
(5432, 'trek to landruk', '2025-11-02 03:05:50', '2025-11-02 03:05:50'),
(5493, 'Hike to Ghandruk . Afternoon: Explore the village. Walking time: 4 hours.', '2025-11-02 03:07:09', '2025-11-02 03:07:09'),
(5584, 'Hike up to Tadapani and continue to Banthanti . Walking time: approx. 6 hours.', '2025-11-02 03:07:47', '2025-11-02 03:07:47'),
(5675, 'Sunrise ascent to Poon Hill . Hike down to Hile. Drive back to Pokhara. Afternoon: Free at leisure by Phewa Lake.', '2025-11-02 03:08:29', '2025-11-02 03:08:29'),
(5736, 'Pokhara on your own. Sightseeing as desired - cycling, hiking - or engaging with YOGA and Ayurveda.', '2025-11-02 03:08:47', '2025-11-02 03:08:47'),
(5797, 'Hike to the tour highlight, Ghorepani . Walking time: 4 hours.', '2025-11-02 03:15:26', '2025-11-02 03:15:26');

-- --------------------------------------------------------

--
-- Table structure for table `package_day_requirements`
--

CREATE TABLE `package_day_requirements` (
  `id` int(11) NOT NULL,
  `trip_package_id` int(11) NOT NULL,
  `day_number` int(11) NOT NULL,
  `hotel_id` int(11) DEFAULT NULL,
  `guide_required` tinyint(1) DEFAULT 0,
  `vehicle_required` tinyint(1) DEFAULT 0,
  `vehicle_type` enum('tour','arrival','departure','intercity','other') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `day_services` varchar(50) DEFAULT NULL,
  `day_notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `package_day_requirements`
--

INSERT INTO `package_day_requirements` (`id`, `trip_package_id`, `day_number`, `hotel_id`, `guide_required`, `vehicle_required`, `vehicle_type`, `created_at`, `updated_at`, `day_services`, `day_notes`) VALUES
(586, 24, 1, NULL, 0, 0, NULL, '2025-10-29 03:56:50', '2025-10-29 03:56:50', NULL, 'Departure from home'),
(587, 24, 2, 34, 1, 1, 'arrival', '2025-10-29 03:56:50', '2025-10-29 03:56:50', 'D', 'Arrival and transfer to Bhaktapur.'),
(588, 24, 3, 34, 1, 1, 'tour', '2025-10-29 03:56:50', '2025-10-29 03:56:50', 'B', 'Drive to Namobuddha visit monastery and evenig sightseeing at Bhaktapur.'),
(589, 24, 4, 35, 1, 1, 'intercity', '2025-10-29 03:56:50', '2025-10-29 03:56:50', 'B, D', 'Drive from Bhaktapur to Charaudi and rafting to Kurintar and short walk to resort.'),
(590, 24, 5, 36, 1, 1, 'intercity', '2025-10-29 03:56:50', '2025-10-29 03:56:50', 'B, D', 'Walk back to the highway and drive from Kurintar to Gorkha.'),
(591, 24, 6, 37, 1, 1, 'intercity', '2025-10-29 03:56:50', '2025-10-29 03:56:50', 'B, D', 'Drive from Gorkha to Pokhara and further upto Dhampus Phedi and hike to Dhampus.'),
(592, 24, 7, 37, 1, 0, NULL, '2025-10-29 03:56:50', '2025-10-29 03:56:50', 'B, D', 'Hike from Dhampus to Potana and to Australian camp and back to lodge.Evenign Momo Class.'),
(593, 24, 8, 38, 1, 1, 'tour', '2025-10-29 03:56:50', '2025-10-29 03:56:50', 'B, L, D', 'After breakfast hike to Gurung village and lunch with Locals and hike to Suiket phedi and drive to Pokhara.'),
(594, 24, 9, 38, 1, 0, NULL, '2025-10-29 03:56:50', '2025-10-29 03:56:50', 'B, D', 'Boating over Phewa lake and hike to Peace stupa and back to hotel.'),
(595, 24, 10, 39, 1, 1, 'intercity', '2025-10-29 03:56:50', '2025-10-29 03:56:50', 'B, L, D', 'Drive from Pokhara to Chitwan .'),
(596, 24, 11, 39, 1, 1, 'intercity', '2025-10-29 03:56:50', '2025-10-29 03:56:50', 'B, L, D', 'Jungle activities in Chitwan National Park'),
(597, 24, 12, 17, 1, 1, 'intercity', '2025-10-29 03:56:50', '2025-10-29 03:56:50', 'B, D', 'Drive from Chitwan to Kathmandu and sightseeing at Pashupati.'),
(598, 24, 13, 17, 1, 1, 'tour', '2025-10-29 03:56:50', '2025-10-29 03:56:50', 'B', 'Explore Ason Market and drive to Childrens home.'),
(599, 24, 14, NULL, 1, 1, 'departure', '2025-10-29 03:56:50', '2025-10-29 03:56:50', 'B', 'Departure Transfer to airport.'),
(600, 25, 1, NULL, 0, 0, NULL, '2025-10-29 03:57:05', '2025-10-29 03:57:05', NULL, 'Departure from home'),
(601, 25, 2, 14, 1, 1, 'arrival', '2025-10-29 03:57:05', '2025-10-29 03:57:05', 'D', 'Arrival and Transfer to Hotel at Bhaktapur'),
(602, 25, 3, 14, 1, 1, 'intercity', '2025-10-29 03:57:05', '2025-10-29 03:57:05', 'B', 'Sightseeing of Royal City Bhaktapur'),
(603, 25, 4, 41, 1, 1, 'tour', '2025-10-29 03:57:05', '2025-10-29 03:57:05', 'B', 'Drive from Bhaktapur to Changunarayan and hike to Telko and drive to Nagarkot'),
(604, 25, 5, 42, 1, 1, 'intercity', '2025-10-29 03:57:05', '2025-10-29 03:57:05', 'B', 'Hike from Nagarkot to Telkot and drive to Kathmandu'),
(605, 25, 6, 43, 1, 1, 'intercity', '2025-10-29 03:57:05', '2025-10-29 03:57:05', 'B', 'Drive from Kathmandu to Bandipur'),
(606, 25, 7, 37, 1, 1, 'intercity', '2025-10-29 03:57:05', '2025-10-29 03:57:05', 'B, L, D', 'Drive from Bandipur to Dhampus Phedi and hike to Dhampus'),
(607, 25, 8, 40, 1, 0, NULL, '2025-10-29 03:57:05', '2025-10-29 03:57:05', 'B, L, D', NULL),
(608, 25, 9, 44, 1, 0, NULL, '2025-10-29 03:57:05', '2025-10-29 03:57:05', 'B, L, D', NULL),
(609, 25, 10, 45, 1, 0, NULL, '2025-10-29 03:57:05', '2025-10-29 03:57:05', 'B, L, D', NULL),
(610, 25, 11, 46, 1, 0, NULL, '2025-10-29 03:57:05', '2025-10-29 03:57:05', 'B, L, D', NULL),
(611, 25, 12, 38, 1, 1, 'intercity', '2025-10-29 03:57:05', '2025-10-29 03:57:05', 'B, L, D', 'Early morning hike to Poonhill for sunrise and back to Ghorpania to Ulleri and drive to Pokhara.'),
(612, 25, 13, 38, 1, 0, NULL, '2025-10-29 03:57:05', '2025-10-29 03:57:05', 'B', 'Exploration day at Pokhara'),
(613, 25, 14, 39, 1, 1, 'intercity', '2025-10-29 03:57:05', '2025-10-29 03:57:05', 'B, L, D', 'Drive from Pokhara to Chitwan National Park'),
(614, 25, 15, 39, 1, 0, NULL, '2025-10-29 03:57:05', '2025-10-29 03:57:05', 'B, L, D', 'Jungle Activities at Chitwan National Park'),
(615, 25, 16, 42, 1, 1, 'tour', '2025-10-29 03:57:05', '2025-10-29 03:57:05', 'B, D', NULL),
(616, 25, 17, NULL, 0, 1, 'departure', '2025-10-29 03:57:05', '2025-10-29 03:57:05', 'B', 'Departure Transfer to airport.'),
(766, 28, 1, NULL, 0, 0, NULL, '2025-11-02 03:20:09', '2025-11-02 03:20:09', NULL, 'Departure from home'),
(767, 28, 2, 14, 1, 1, 'arrival', '2025-11-02 03:20:09', '2025-11-02 03:20:09', NULL, 'Arrival and transfer to Bhaktapur.'),
(768, 28, 3, 52, 1, 1, 'intercity', '2025-11-02 03:20:09', '2025-11-02 03:20:09', 'B', 'After breakfast: Sightseeing in the old historical city of Bhaktapur. Afternoon: Drive to the monastery. Visit the Buddhist center. Overnight stay at the monastery.'),
(769, 28, 4, 43, 1, 1, 'intercity', '2025-11-02 03:20:09', '2025-11-02 03:20:09', 'B', 'Drive from Kathmandu to Bandipur'),
(770, 28, 5, 37, 1, 1, 'intercity', '2025-11-02 03:20:09', '2025-11-02 03:20:09', 'B, D', 'Drive to Dhampus. Witness the mountains and sunrise over the Himalayas. Breathtaking panorama in clear weather; otherwise, explore the beautiful Gurung village.'),
(771, 28, 6, 53, 1, 0, NULL, '2025-11-02 03:20:09', '2025-11-02 03:20:09', 'B, L, D', 'Trek from Dhampus to Landruk (1 640 m)'),
(772, 28, 7, 53, 1, 0, NULL, '2025-11-02 03:20:09', '2025-11-02 03:20:09', 'B, L, D', 'Hike to Ghandruk . Afternoon: Explore the village. Walking time: 4 hours.'),
(773, 28, 8, 53, 1, 0, NULL, '2025-11-02 03:20:09', '2025-11-02 03:20:09', 'B, L, D', 'Hike up to Tadapani and continue to Banthanti . Walking time: approx. 6 hours.'),
(774, 28, 9, 53, 1, 0, NULL, '2025-11-02 03:20:09', '2025-11-02 03:20:09', 'B, L, D', 'Hike to the tour highlight, Ghorepani . Walking time: 4 hours.'),
(775, 28, 10, 54, 1, 1, 'intercity', '2025-11-02 03:20:09', '2025-11-02 03:20:09', 'B', 'Sunrise ascent to Poon Hill . Hike down to Hile. Drive back to Pokhara. Afternoon: Free at leisure by Phewa Lake.'),
(776, 28, 11, 54, 1, 0, NULL, '2025-11-02 03:20:09', '2025-11-02 03:20:09', 'B', 'Pokhara on your own. Sightseeing as desired - cycling, hiking - or engaging with YOGA and Ayurveda.'),
(777, 28, 12, 17, 1, 1, NULL, '2025-11-02 03:20:09', '2025-11-02 03:20:09', NULL, 'Kathmandu Sightseeing and Dinner'),
(778, 28, 13, NULL, 0, 1, 'departure', '2025-11-02 03:20:09', '2025-11-02 03:20:09', 'B', 'Departure Transfer to airport.'),
(779, 26, 1, 53, 1, 0, NULL, '2025-11-04 01:21:54', '2025-11-04 01:21:54', 'B, L, D', NULL),
(780, 26, 2, 52, 0, 0, NULL, '2025-11-04 01:21:54', '2025-11-04 01:21:54', 'B, L, D', NULL),
(781, 27, 1, NULL, 0, 0, NULL, '2025-11-04 04:53:00', '2025-11-04 04:53:00', NULL, 'Sightseeing of pashupatinath and Boudha and Farewell dinner Boudha stupa'),
(782, 27, 2, 47, 1, 1, 'arrival', '2025-11-04 04:53:00', '2025-11-04 04:53:00', 'D', 'Sightseeing of pashupatinath and Boudha and Farewell dinner Boudha stupa'),
(783, 27, 3, 47, 1, 1, 'tour', '2025-11-04 04:53:00', '2025-11-04 04:53:00', 'B', 'Sightseeing of pashupatinath and Boudha and Farewell dinner Boudha stupa'),
(784, 27, 4, 34, 1, 1, 'tour', '2025-11-04 04:53:00', '2025-11-04 04:53:00', 'B', 'Sightseeing of pashupatinath and Boudha and Farewell dinner Boudha stupa'),
(785, 27, 5, 34, 1, 0, NULL, '2025-11-04 04:53:00', '2025-11-04 04:53:00', 'B', 'Sightseeing of pashupatinath and Boudha and Farewell dinner Boudha stupa'),
(786, 27, 6, 48, 1, 1, 'intercity', '2025-11-04 04:53:00', '2025-11-04 04:53:00', 'B, L, D', 'Sightseeing of pashupatinath and Boudha and Farewell dinner Boudha stupa'),
(787, 27, 7, 48, 1, 0, NULL, '2025-11-04 04:53:00', '2025-11-04 04:53:00', 'B, L, D', 'Sightseeing of pashupatinath and Boudha and Farewell dinner Boudha stupa'),
(788, 27, 8, 49, 1, 1, 'intercity', '2025-11-04 04:53:00', '2025-11-04 04:53:00', 'B', 'Sightseeing of pashupatinath and Boudha and Farewell dinner Boudha stupa'),
(789, 27, 9, 37, 1, 1, 'intercity', '2025-11-04 04:53:00', '2025-11-04 04:53:00', 'B, D', 'Sightseeing of pashupatinath and Boudha and Farewell dinner Boudha stupa'),
(790, 27, 10, 50, 1, 1, 'intercity', '2025-11-04 04:53:00', '2025-11-04 04:53:00', 'B, L', 'Sightseeing of pashupatinath and Boudha and Farewell dinner Boudha stupa'),
(791, 27, 11, 50, 1, 1, 'tour', '2025-11-04 04:53:00', '2025-11-04 04:53:00', 'B', 'Sightseeing of pashupatinath and Boudha and Farewell dinner Boudha stupa'),
(792, 27, 12, 51, 1, 1, 'intercity', '2025-11-04 04:53:00', '2025-11-04 04:53:00', 'B, D', 'Sightseeing of pashupatinath and Boudha and Farewell dinner Boudha stupa'),
(793, 27, 13, 51, 1, 1, 'tour', '2025-11-04 04:53:00', '2025-11-04 04:53:00', 'B', 'Sightseeing of pashupatinath and Boudha and Farewell dinner Boudha stupa'),
(794, 27, 14, NULL, 1, 1, 'departure', '2025-11-04 04:53:00', '2025-11-04 04:53:00', 'B', 'Depature tansfer to airport'),
(795, 17, 1, NULL, 0, 0, NULL, '2025-11-04 10:23:08', '2025-11-04 10:23:08', NULL, 'Departure from home'),
(796, 17, 2, 14, 1, 1, 'arrival', '2025-11-04 10:23:08', '2025-11-04 10:23:08', NULL, 'Arrival in Kathmandu and transfer to Bhaktapur'),
(797, 17, 3, 14, 1, 0, NULL, '2025-11-04 10:23:08', '2025-11-04 10:23:08', NULL, 'Bhaktapur Overnight Stay - Overnight in Bhaktapur'),
(798, 17, 4, 5, 1, 1, 'departure', '2025-11-04 10:23:08', '2025-11-04 10:23:08', 'B, L, D', 'Transfer to Ktm airport and Fly to Lukla'),
(799, 17, 5, 6, 1, 0, NULL, '2025-11-04 10:23:08', '2025-11-04 10:23:08', 'B, L, D', 'Trek to Namche Bazar'),
(800, 17, 6, 6, 1, 0, NULL, '2025-11-04 10:23:08', '2025-11-04 10:23:08', 'B, L, D', 'Rest in Namche'),
(801, 17, 7, 7, 1, 0, NULL, '2025-11-04 10:23:08', '2025-11-04 10:23:08', 'B, L, D', 'Trek from Namche to Tengboche'),
(802, 17, 8, 8, 1, 0, NULL, '2025-11-04 10:23:08', '2025-11-04 10:23:08', 'B, L, D', 'Trek from Tengboche to Dingboche'),
(803, 17, 9, 8, 1, 0, NULL, '2025-11-04 10:23:08', '2025-11-04 10:23:08', 'B, L, D', 'Rest Day in Dingboche - Acclimatization Day'),
(804, 17, 10, 9, 1, 0, NULL, '2025-11-04 10:23:08', '2025-11-04 10:23:08', 'B, L, D', 'Trek Dingboche to Lobuche'),
(805, 17, 11, 10, 1, 0, NULL, '2025-11-04 10:23:08', '2025-11-04 10:23:08', 'B, L, D', 'Trek to Base Camp - Gorakh Shep'),
(806, 17, 12, 9, 1, 0, NULL, '2025-11-04 10:23:08', '2025-11-04 10:23:08', 'B, L, D', 'Trek Kalapathar and back to Lobuche'),
(807, 17, 13, 11, 1, 0, NULL, '2025-11-04 10:23:08', '2025-11-04 10:23:08', 'B, L, D', 'Trek from Lobuche to Pangboche'),
(808, 17, 14, 6, 1, 0, NULL, '2025-11-04 10:23:08', '2025-11-04 10:23:08', 'B, L, D', 'Trek from Pangboche to Namche'),
(809, 17, 15, 10, 1, 0, NULL, '2025-11-04 10:23:08', '2025-11-04 10:23:08', 'B, L, D', 'Trek from Namche to Lukla'),
(810, 17, 16, 17, 0, 1, 'arrival', '2025-11-04 10:23:08', '2025-11-04 10:23:08', 'B', 'Fly to Kathmandu and transfer to Hotel'),
(811, 17, 17, 17, 1, 1, 'tour', '2025-11-04 10:23:08', '2025-11-04 10:23:08', 'B, D', 'Kathmandu Sightseeing and Dinner'),
(812, 17, 18, 17, 0, 1, 'departure', '2025-11-04 10:23:08', '2025-11-04 10:23:08', 'B', 'Kathmandu rest day Departure home');

-- --------------------------------------------------------

--
-- Table structure for table `package_hotel_assignments`
--

CREATE TABLE `package_hotel_assignments` (
  `id` int(11) NOT NULL,
  `trip_package_id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `services_provided` varchar(50) DEFAULT '',
  `day_number` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pax_amendments`
--

CREATE TABLE `pax_amendments` (
  `id` int(11) NOT NULL,
  `trip_id` int(11) NOT NULL,
  `room_type` varchar(20) NOT NULL,
  `old_value` int(11) NOT NULL,
  `new_value` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pax_amendments`
--

INSERT INTO `pax_amendments` (`id`, `trip_id`, `room_type`, `old_value`, `new_value`, `user_id`, `user_name`, `created_at`) VALUES
(33, 168, 'double', 0, 5, 8, 'samjana karki', '2025-10-31 06:47:18'),
(34, 168, 'single', 0, 4, 8, 'samjana karki', '2025-10-31 06:47:18'),
(35, 168, 'double', 5, 0, 8, 'samjana karki', '2025-10-31 06:48:23'),
(36, 168, 'single', 4, 0, 8, 'samjana karki', '2025-10-31 06:48:23'),
(43, 172, 'double', 5, 1, 6, 'Pratyush2', '2025-11-02 03:24:26'),
(44, 172, 'single', 4, 0, 6, 'Pratyush2', '2025-11-02 03:24:28'),
(45, 172, 'double', 1, 0, 6, 'Pratyush2', '2025-11-02 03:25:27'),
(46, 172, 'single', 0, 1, 6, 'Pratyush2', '2025-11-02 03:25:28'),
(47, 150, 'double', 5, 1, 2, 'admin', '2025-11-04 04:47:40'),
(48, 170, 'double', 3, 0, 2, 'admin', '2025-11-04 04:48:21'),
(49, 170, 'single', 3, 0, 2, 'admin', '2025-11-04 04:48:21'),
(50, 170, 'twin', 0, 1, 2, 'admin', '2025-11-04 04:48:21'),
(51, 174, 'single', 0, 1, 9, 'Samir', '2025-11-04 10:25:29'),
(52, 174, 'double', 1, 2, 9, 'Samir', '2025-11-04 10:27:15'),
(53, 174, 'double', 2, 3, 9, 'Samir', '2025-11-04 10:41:50'),
(54, 174, 'double', 3, 6, 2, 'admin', '2025-11-05 09:11:38');

-- --------------------------------------------------------

--
-- Table structure for table `pax_details`
--

CREATE TABLE `pax_details` (
  `id` int(11) NOT NULL,
  `trip_id` int(11) NOT NULL,
  `double_rooms` int(11) DEFAULT 0,
  `single_rooms` int(11) DEFAULT 0,
  `triple_rooms` int(11) DEFAULT 0,
  `twin_rooms` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pax_details`
--

INSERT INTO `pax_details` (`id`, `trip_id`, `double_rooms`, `single_rooms`, `triple_rooms`, `twin_rooms`, `created_at`, `updated_at`) VALUES
(37, 145, 5, 4, 0, 0, '2025-10-28 01:20:45', '2025-11-04 10:43:52'),
(49, 159, 5, 4, 0, 0, '2025-10-28 03:48:29', '2025-10-28 03:51:51'),
(50, 150, 1, 4, 0, 0, '2025-10-28 03:48:38', '2025-11-04 04:47:40'),
(65, 168, 0, 0, 0, 0, '2025-10-31 06:46:06', '2025-10-31 06:48:23'),
(72, 170, 0, 0, 0, 1, '2025-10-31 07:00:51', '2025-11-04 04:48:21'),
(99, 172, 0, 1, 0, 0, '2025-11-02 03:24:21', '2025-11-05 03:56:36'),
(116, 173, 1, 0, 0, 0, '2025-11-04 01:22:43', '2025-11-04 01:22:45'),
(124, 151, 1, 2, 0, 0, '2025-11-04 04:48:46', '2025-11-04 04:48:46'),
(126, 174, 6, 1, 0, 0, '2025-11-04 10:25:26', '2025-11-05 09:11:38'),
(139, 162, 5, 4, 0, 0, '2025-11-04 10:41:24', '2025-11-04 10:41:24');

-- --------------------------------------------------------

--
-- Table structure for table `room_types`
--

CREATE TABLE `room_types` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_types`
--

INSERT INTO `room_types` (`id`, `name`, `description`, `capacity`, `created_at`) VALUES
(1, 'Standard', 'Basic room with essential amenities', 1, '2025-10-12 03:47:57'),
(2, 'Deluxe', 'Upgraded room with better comfort', 2, '2025-10-12 03:47:57'),
(3, 'Twin', 'Two separate beds', 2, '2025-10-12 03:47:57'),
(4, 'Double', 'One large bed', 2, '2025-10-12 03:47:57'),
(5, 'Suite', 'Spacious room with living area', 3, '2025-10-12 03:47:57'),
(6, 'Dormitory', 'Shared room with multiple beds', 4, '2025-10-12 03:47:57'),
(7, 'Single', 'One bed for single occupancy', 1, '2025-10-12 03:47:57'),
(8, 'Budget', 'Economy room', 1, '2025-10-12 03:47:57');

-- --------------------------------------------------------

--
-- Table structure for table `trips`
--

CREATE TABLE `trips` (
  `id` int(11) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `customer_name` varchar(255) NOT NULL,
  `tour_code` varchar(100) DEFAULT NULL,
  `trip_package_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('Pending','Active','Completed') DEFAULT 'Pending',
  `total_price` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `company` varchar(50) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `passport_no` varchar(50) DEFAULT NULL,
  `arrival_date` date DEFAULT NULL,
  `arrival_time` time DEFAULT NULL,
  `arrival_flight` varchar(50) DEFAULT NULL,
  `departure_date` date DEFAULT NULL,
  `departure_time` time DEFAULT NULL,
  `departure_flight` varchar(50) DEFAULT NULL,
  `total_pax` int(11) DEFAULT NULL,
  `couples_count` int(11) DEFAULT NULL,
  `singles_count` int(11) DEFAULT NULL,
  `guest_status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trips`
--

INSERT INTO `trips` (`id`, `file_name`, `customer_name`, `tour_code`, `trip_package_id`, `start_date`, `end_date`, `status`, `total_price`, `created_at`, `updated_at`, `company`, `country`, `address`, `passport_no`, `arrival_date`, `arrival_time`, `arrival_flight`, `departure_date`, `departure_time`, `departure_flight`, `total_pax`, `couples_count`, `singles_count`, `guest_status`) VALUES
(144, 'EBC-26-1014', 'EBC-26-1014', 'NPKTM002-0004', 17, '2026-10-18', '2026-11-04', '', 0.00, '2025-10-27 02:58:40', '2025-10-27 03:00:04', 'ASI', '', NULL, NULL, '2026-10-18', '00:00:00', '', '2026-11-04', '00:00:00', '', 0, 0, 0, NULL),
(145, 'EBC-26-311', 'EBC-26-311', 'NPKTM002-26-311', 17, '2026-03-05', '2026-03-22', 'Pending', 0.00, '2025-10-27 02:59:59', '2025-10-27 03:00:25', 'ASI', '', NULL, NULL, '2026-03-05', '00:00:00', '', '2026-03-22', '00:00:00', '', 0, 0, 0, NULL),
(146, 'EBC-26-408', 'EBC-26-408', 'NPKTM002-26-408', 17, '2026-04-16', '2026-05-03', 'Pending', 0.00, '2025-10-27 03:00:28', '2025-10-27 03:04:52', 'ASI', '', NULL, NULL, '2026-04-16', '00:00:00', '', '2026-05-03', '00:00:00', '', 0, 0, 0, NULL),
(147, 'EBC-26-1004', 'EBC-26-1004', 'NPKTM002-26-1004', 17, '2026-10-04', '2026-10-21', 'Pending', 0.00, '2025-10-27 03:04:55', '2025-10-27 03:09:15', 'ASI', '', NULL, NULL, '2026-10-04', '00:00:00', '', '2026-10-21', '00:00:00', '', 0, 0, 0, NULL),
(148, 'EBC-26-1011', 'EBC-26-1011', 'NPKTM002-26-1011', 17, '2026-10-18', '2026-11-04', 'Pending', 0.00, '2025-10-27 03:09:19', '2025-10-27 03:09:52', 'ASI', '', NULL, NULL, '2026-10-18', '00:00:00', '', '2026-11-04', '00:00:00', '', 0, 0, 0, NULL),
(149, 'EBC-26-1103', 'EBC-26-1103', 'NPKTM002-26-1103', 17, '2026-11-08', '2026-11-25', 'Pending', 0.00, '2025-10-27 03:09:56', '2025-10-27 03:10:16', 'ASI', '', NULL, NULL, '2026-11-08', '00:00:00', '', '2026-11-25', '00:00:00', '', 0, 0, 0, NULL),
(150, 'NHE-26-201', 'NHE-26-201', 'NPKTM01E-26-201', 24, '2026-02-23', '2026-03-08', 'Pending', 0.00, '2025-10-27 03:11:24', '2025-10-27 03:11:24', 'ASI', '', NULL, NULL, '2026-02-23', '00:00:00', '', '2026-03-08', '00:00:00', '', 0, 0, 0, NULL),
(151, 'NHE-26-301', 'NHE-26-301', 'NPKTM01E-26-301', 24, '2026-03-09', '2026-03-22', 'Pending', 0.00, '2025-10-27 03:17:47', '2025-10-27 03:18:13', 'ASI', '', NULL, NULL, '2026-03-09', '00:00:00', '', '2026-03-22', '00:00:00', '', 0, 0, 0, NULL),
(152, 'NHE-26-317', 'NHE-26-317', 'NPKTM01E-26-317', 24, '2026-03-29', '2026-04-11', 'Pending', 0.00, '2025-10-27 03:18:49', '2025-10-27 03:19:23', 'ASI', '', NULL, NULL, '2026-03-29', '00:00:00', '', '2026-04-11', '00:00:00', '', 0, 0, 0, NULL),
(153, 'NHE-26-405', 'NHE-26-405', 'NPKTM01E-26-405', 24, '2026-04-13', '2026-04-26', 'Pending', 0.00, '2025-10-27 03:19:26', '2025-10-27 03:19:57', 'ASI', '', NULL, NULL, '2026-04-13', '00:00:00', '', '2026-04-26', '00:00:00', '', 0, 0, 0, NULL),
(154, 'NHE-26-901', 'NHE-26-901', 'NPKTM01E-26-901', 24, '2026-09-28', '2026-10-11', 'Pending', 0.00, '2025-10-27 03:20:06', '2025-10-27 03:20:35', 'ASI', '', NULL, NULL, '2026-09-28', '00:00:00', '', '2026-10-11', '00:00:00', '', 0, 0, 0, NULL),
(155, 'NHE-26-1014', 'NHE-26-1014', 'NPKTM01E-26-1014', 24, '2026-10-19', '2026-11-01', 'Pending', 0.00, '2025-10-27 03:20:38', '2025-10-27 03:21:02', 'ASI', '', NULL, NULL, '2026-10-19', '00:00:00', '', '2026-11-01', '00:00:00', '', 0, 0, 0, NULL),
(156, 'NHE-26-1106', 'NHE-26-1106', 'NPKTM01E-26-1106', 24, '2026-11-09', '2026-11-22', 'Pending', 0.00, '2025-10-27 03:21:05', '2025-10-27 03:22:21', 'ASI', '', NULL, NULL, '2026-11-09', '00:00:00', '', '2026-11-22', '00:00:00', '', 0, 0, 0, NULL),
(157, 'NHE-26-1203', 'NHE-26-1203', 'NPKTM01E-26-1203', 24, '2026-12-28', '2027-01-10', 'Pending', 0.00, '2025-10-27 03:22:24', '2025-10-27 03:23:40', 'ASI', '', NULL, NULL, '2026-12-28', '00:00:00', '', '2027-01-10', '00:00:00', '', 0, 0, 0, NULL),
(158, 'NHE-26-1111', 'NHE-26-1111', 'NPKTM01E-0001', 24, '2026-11-23', '2026-12-06', 'Pending', 0.00, '2025-10-27 03:23:21', '2025-10-27 03:23:21', 'ASI', '', NULL, NULL, '2026-11-23', '00:00:00', '', '2026-12-06', '00:00:00', '', 0, 0, 0, NULL),
(159, 'PanaromaWandren-26-305', 'PanaromaWandren-26-305', 'NPG-01-26-301', 25, '2026-03-04', '2026-03-20', 'Pending', 0.00, '2025-10-27 03:25:20', '2025-10-27 03:25:20', 'WWW', '', NULL, NULL, '2026-03-04', '00:00:00', '', '2026-03-20', '00:00:00', '', 0, 0, 0, NULL),
(160, 'PanaromaWandren-26-402', 'PanaromaWandren-26-402', 'NPG-01-26-402', 25, '2026-04-08', '2026-04-24', 'Pending', 0.00, '2025-10-27 03:25:35', '2025-10-27 03:28:20', 'WWW', '', NULL, NULL, '2026-04-08', '00:00:00', '', '2026-04-24', '00:00:00', '', 0, 0, 0, NULL),
(161, 'PanaromaWandren-26-1017', 'PanaromaWandren-26-1017', 'NPG-01-26-1017', 25, '2026-10-21', '2026-11-06', 'Pending', 0.00, '2025-10-27 03:26:35', '2025-10-31 02:46:40', 'WWW', NULL, NULL, NULL, '0000-00-00', '00:00:00', '', '0000-00-00', '00:00:00', '', 0, NULL, NULL, NULL),
(162, 'PanaromaWandren-26-1109', 'PanaromaWandren-26-1109', 'NPG-01-26-1109', 25, '2026-11-11', '2026-11-27', 'Pending', 0.00, '2025-10-27 03:27:07', '2025-10-27 03:29:18', 'WWW', '', NULL, NULL, '2026-11-11', '00:00:00', '', '2026-11-27', '00:00:00', '', 0, 0, 0, NULL),
(164, 'PanaromaWandren-26-1201', 'PanaromaWandren-26-1201', 'NPG-01- 120126', 25, '2026-12-21', '2027-01-06', 'Pending', 0.00, '2025-10-27 05:50:30', '2025-10-29 09:34:48', 'WWW', '', NULL, NULL, '0000-00-00', '00:00:00', '', '0000-00-00', '00:00:00', '', 0, 0, 0, NULL),
(168, '1204', '1204', 'MPKTM010-1204', 27, '2025-12-27', '2026-01-09', 'Pending', 0.00, '2025-10-31 06:44:53', '2025-10-31 06:44:53', 'ASI', '', NULL, NULL, '2025-12-27', '00:00:00', '', '2026-01-09', '00:00:00', '', 0, 0, 0, NULL),
(169, '1201', '1201', 'NPG-01-1201', 25, '2025-12-19', '2026-01-04', 'Pending', 0.00, '2025-10-31 06:53:03', '2025-10-31 06:53:03', 'WWW', '', NULL, NULL, '2025-12-19', '00:00:00', '', '2026-01-04', '00:00:00', '', 0, 0, 0, NULL),
(170, '1203', '1203', 'NPKTM01E-1203', 24, '2025-12-25', '2026-01-07', 'Pending', 0.00, '2025-10-31 06:54:27', '2025-10-31 06:54:27', 'ASI', '', NULL, NULL, '2025-12-25', '00:00:00', '', '2026-01-07', '00:00:00', '', 0, 0, 0, NULL),
(172, 'WDT-Kultur-1106', 'WDT-Kultur-1106', 'WDT-11-06', 28, '2025-11-05', '2025-11-17', 'Pending', 0.00, '2025-11-02 03:20:58', '2025-11-02 10:34:25', 'Others', '', NULL, NULL, '2025-11-05', '00:00:00', '', '2025-11-17', '00:00:00', '', 0, 0, 0, NULL),
(173, 'test123', 'test123', 'TEST123-0001', 26, '2025-11-05', '2025-11-06', 'Pending', 0.00, '2025-11-04 01:22:08', '2025-11-04 01:22:08', '', '', NULL, NULL, '2025-11-05', '00:00:00', '', '2025-11-06', '00:00:00', '', 0, 0, 0, NULL),
(174, 'samir', 'samir', 'TEST123-0002', 26, '2025-11-13', '2025-11-14', 'Pending', 0.00, '2025-11-04 10:24:51', '2025-11-04 10:24:51', '', '', NULL, NULL, '2025-11-13', '00:00:00', '', '2025-11-14', '00:00:00', '', 0, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `trip_arrivals`
--

CREATE TABLE `trip_arrivals` (
  `id` int(11) NOT NULL,
  `trip_id` int(11) NOT NULL,
  `arrival_date` date NOT NULL,
  `arrival_time` time DEFAULT NULL,
  `flight_no` varchar(100) DEFAULT NULL,
  `pax_count` int(11) DEFAULT 0,
  `pickup_location` varchar(255) DEFAULT NULL,
  `drop_hotel_id` int(11) DEFAULT NULL,
  `vehicle_id` int(11) DEFAULT NULL,
  `guide_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `vehicle_informed` tinyint(1) DEFAULT 0,
  `guide_informed` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trip_arrivals`
--

INSERT INTO `trip_arrivals` (`id`, `trip_id`, `arrival_date`, `arrival_time`, `flight_no`, `pax_count`, `pickup_location`, `drop_hotel_id`, `vehicle_id`, `guide_id`, `notes`, `vehicle_informed`, `guide_informed`) VALUES
(2, 92, '2025-10-23', '21:11:00', NULL, 2, 'tim\nrim', NULL, 4, NULL, NULL, 0, 0),
(3, 92, '2025-10-23', '22:13:00', NULL, 3, 'sim\ndim\nhim', NULL, 4, NULL, NULL, 0, 0),
(4, 94, '2025-10-23', '21:29:00', NULL, 2, 'rdfg\nfddg', 14, 4, 6, 'xvs', 1, 1),
(5, 94, '2025-10-23', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(6, 97, '2025-10-24', NULL, NULL, 2, 'sdf\nfdggdfg', NULL, NULL, NULL, NULL, 0, 0),
(7, 97, '2025-10-24', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(11, 99, '2025-10-24', NULL, NULL, 3, 'Ram\nsh\nka', 14, NULL, NULL, NULL, 0, 0),
(12, 99, '2025-10-24', NULL, NULL, 3, 'hari\nsita\nrada', 14, NULL, NULL, NULL, 0, 0),
(13, 98, '2025-10-23', NULL, NULL, 3, 'rim\ndim\ndasd', 14, NULL, NULL, NULL, 0, 0),
(14, 98, '2025-10-23', NULL, NULL, 2, 'asdm\ndsds', 14, NULL, NULL, NULL, 0, 0),
(41, 101, '2025-10-26', NULL, NULL, 3, 'ram\nhari\nrami', 14, 1, 4, NULL, 0, 0),
(42, 101, '2025-10-26', NULL, NULL, 2, 'shyam\nmaya', 14, 1, 4, NULL, 0, 0),
(53, 100, '2025-10-25', '18:42:00', 'RA-', 4, 'B1\nB2\nS1\nS2', 14, 4, 6, NULL, 0, 0),
(54, 100, '2025-10-25', '22:42:00', 'QR-', 4, 'A1\nA2\nC1\nC2', 14, 4, 6, NULL, 0, 0),
(57, 102, '2025-10-25', '18:42:00', 'RA-', 4, 'B1\nB2\nS1\nS2', 14, 4, 6, NULL, 0, 0),
(58, 102, '2025-10-25', '22:42:00', 'QR-', 4, 'A1\nA2\nC1\nC2', 14, 4, 6, NULL, 0, 0),
(59, 103, '2025-10-25', '18:42:00', 'RA-', 4, 'B1\nB2\nS1\nS2', 14, 4, 6, NULL, 0, 0),
(60, 103, '2025-10-25', '22:42:00', 'QR-', 4, 'A1\nA2\nC1\nC2', 14, 4, 6, NULL, 0, 0),
(61, 104, '2025-10-25', '18:42:00', 'RA-', 4, 'B1\nB2\nS1\nS2', 14, 4, 6, NULL, 0, 0),
(62, 104, '2025-10-25', '22:42:00', 'QR-', 4, 'A1\nA2\nC1\nC2', 14, 4, 6, NULL, 0, 0),
(65, 105, '2025-10-23', NULL, NULL, 3, 'ges\ntdg\nhgf', 14, 1, NULL, NULL, 0, 0),
(66, 105, '2025-10-23', NULL, NULL, 2, 'gtd\nhgd', 14, 1, NULL, NULL, 0, 0),
(67, 106, '2025-11-01', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(68, 107, '2025-10-18', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(69, 108, '2025-10-25', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(70, 109, '2025-10-25', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(75, 110, '2025-10-27', NULL, NULL, 2, NULL, NULL, NULL, NULL, 'ram & sita', 0, 0),
(76, 110, '2025-10-27', NULL, NULL, 1, NULL, NULL, NULL, NULL, 'hari', 0, 0),
(77, 111, '2026-10-18', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(78, 112, '2025-10-25', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(79, 113, '2025-10-27', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(80, 114, '2025-10-27', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(81, 115, '2025-10-27', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(82, 116, '2025-10-25', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(83, 117, '2025-10-25', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(84, 118, '2025-10-27', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(85, 119, '2025-10-21', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(86, 120, '2025-10-16', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(87, 121, '2025-10-27', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(88, 122, '2025-10-27', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(90, 123, '2025-11-01', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(91, 124, '2026-03-05', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(95, 126, '2026-04-16', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(96, 127, '2026-04-16', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(97, 128, '2026-10-04', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(98, 129, '2026-10-18', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(101, 125, '2026-03-11', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(102, 131, '2025-10-27', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(103, 132, '2025-10-27', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(118, 140, '2026-04-16', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(121, 143, '2026-10-04', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(122, 142, '2026-10-18', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(124, 141, '2026-10-18', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(126, 145, '2026-03-05', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(128, 146, '2026-04-16', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(130, 147, '2026-10-04', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(132, 148, '2026-10-18', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(134, 149, '2026-11-08', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(135, 150, '2026-02-23', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(137, 151, '2026-03-09', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(139, 152, '2026-03-29', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(141, 153, '2026-04-13', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(143, 154, '2026-09-28', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(145, 155, '2026-10-19', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(147, 156, '2026-11-09', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(150, 158, '2026-11-23', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(151, 157, '2026-12-28', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(152, 159, '2026-03-04', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(158, 160, '2026-04-08', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(160, 162, '2026-11-11', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(163, 163, '2026-12-21', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(168, 165, '2025-10-30', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(169, 166, '2025-10-23', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(199, 168, '2025-12-27', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(200, 169, '2025-12-19', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(201, 170, '2025-12-25', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(202, 171, '2025-11-01', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(204, 172, '2025-11-05', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(205, 173, '2025-11-05', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0),
(206, 174, '2025-11-13', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `trip_departures`
--

CREATE TABLE `trip_departures` (
  `id` int(11) NOT NULL,
  `trip_id` int(11) NOT NULL,
  `departure_date` date NOT NULL,
  `departure_time` time DEFAULT NULL,
  `flight_no` varchar(100) DEFAULT NULL,
  `pax_count` int(11) DEFAULT 0,
  `pickup_location` varchar(255) DEFAULT NULL,
  `pickup_hotel_id` int(11) DEFAULT NULL,
  `vehicle_id` int(11) DEFAULT NULL,
  `guide_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `vehicle_informed` tinyint(1) DEFAULT 0,
  `guide_informed` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trip_departures`
--

INSERT INTO `trip_departures` (`id`, `trip_id`, `departure_date`, `departure_time`, `flight_no`, `pax_count`, `pickup_location`, `pickup_hotel_id`, `vehicle_id`, `guide_id`, `notes`, `vehicle_informed`, `guide_informed`) VALUES
(19, 101, '2025-11-12', NULL, NULL, 3, 'ram\nhari\nrami', 17, 2, 6, NULL, 0, 0),
(20, 101, '2025-11-12', NULL, NULL, 2, 'shyam\nmaya', 17, 2, 6, NULL, 0, 0),
(31, 100, '2025-11-11', NULL, NULL, 4, 'B1\nB2\nS1\nS2', 17, NULL, NULL, NULL, 0, 0),
(32, 100, '2025-11-11', NULL, NULL, 4, 'A1\nA2\nC1\nC2', 17, NULL, NULL, NULL, 0, 0),
(35, 102, '2025-11-11', NULL, NULL, 4, 'B1\nB2\nS1\nS2', 17, NULL, NULL, NULL, 0, 0),
(36, 102, '2025-11-11', NULL, NULL, 4, 'A1\nA2\nC1\nC2', 17, NULL, NULL, NULL, 0, 0),
(37, 104, '2025-11-11', NULL, NULL, 4, 'B1\nB2\nS1\nS2', 17, NULL, NULL, NULL, 0, 0),
(38, 104, '2025-11-11', NULL, NULL, 4, 'A1\nA2\nC1\nC2', 17, NULL, NULL, NULL, 0, 0),
(41, 105, '2025-12-19', NULL, NULL, 3, 'ges\ntdg\nhgf', NULL, NULL, NULL, NULL, 0, 0),
(42, 105, '2025-12-19', NULL, NULL, 2, 'gtd\nhgd', NULL, NULL, NULL, NULL, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `trip_guests`
--

CREATE TABLE `trip_guests` (
  `id` int(11) NOT NULL,
  `trip_id` int(11) NOT NULL,
  `type` varchar(10) NOT NULL,
  `name1` varchar(255) NOT NULL,
  `name2` varchar(255) DEFAULT NULL,
  `passport1` varchar(100) DEFAULT NULL,
  `passport2` varchar(100) DEFAULT NULL,
  `dob1` date DEFAULT NULL,
  `dob2` date DEFAULT NULL,
  `country1` varchar(100) DEFAULT NULL,
  `country2` varchar(100) DEFAULT NULL,
  `remark1` varchar(255) DEFAULT NULL,
  `remark2` varchar(255) DEFAULT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trip_guests`
--

INSERT INTO `trip_guests` (`id`, `trip_id`, `type`, `name1`, `name2`, `passport1`, `passport2`, `dob1`, `dob2`, `country1`, `country2`, `remark1`, `remark2`, `display_order`) VALUES
(4, 92, 'couple', 'tim', 'rim', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(5, 92, 'couple', 'sim', 'dim', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2),
(6, 92, 'single', 'him', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3),
(7, 94, 'couple', 'rdfg', 'fddg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(8, 94, 'couple', 'df', 'sdf', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2),
(9, 94, 'single', 'sdf', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3),
(10, 97, 'couple', 'sdf', 'fdggdfg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(11, 97, 'couple', 'dfdf', 'vbfd', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2),
(12, 97, 'single', 'dbdf', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3),
(19, 99, 'couple', 'Ram', 'sh', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(20, 99, 'couple', 'hari', 'sita', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2),
(21, 99, 'single', 'rada', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3),
(22, 99, 'single', 'ka', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4),
(74, 101, 'couple', 'ram', 'hari', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(75, 101, 'couple', 'shyam', 'maya', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2),
(76, 101, 'single', 'rami', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3),
(102, 100, 'couple', 'A1', 'A2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(103, 100, 'couple', 'B1', 'B2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2),
(104, 100, 'couple', 'C1', 'C2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3),
(105, 100, 'single', 'S1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4),
(106, 100, 'single', 'S2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5),
(112, 102, 'couple', 'A1', 'A2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(113, 102, 'couple', 'B1', 'B2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2),
(114, 102, 'couple', 'C1', 'C2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3),
(115, 102, 'single', 'S1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4),
(116, 102, 'single', 'S2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5),
(117, 104, 'couple', 'A1', 'A2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(118, 104, 'couple', 'B1', 'B2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2),
(119, 104, 'couple', 'C1', 'C2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3),
(120, 104, 'single', 'S1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4),
(121, 104, 'single', 'S2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5),
(125, 105, 'couple', 'ges', 'tdg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(126, 105, 'couple', 'gtd', 'hgd', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2),
(127, 105, 'single', 'hgf', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3);

-- --------------------------------------------------------

--
-- Table structure for table `trip_packages`
--

CREATE TABLE `trip_packages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `No_of_Days` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `code` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trip_packages`
--

INSERT INTO `trip_packages` (`id`, `name`, `description`, `No_of_Days`, `total_price`, `created_at`, `updated_at`, `code`) VALUES
(17, 'Nepal Everest Base Camp', '', 18, 0.00, '2025-10-21 07:10:30', '2025-10-21 07:28:09', 'NPKTM002'),
(24, 'Nepals Highlights erleben', '', 14, 0.00, '2025-10-24 06:28:58', '2025-10-24 07:13:04', 'NPKTM01E'),
(25, 'Panaroma Wandern And Kulture Safari', '', 17, 0.00, '2025-10-26 07:16:06', '2025-10-26 07:16:23', 'NPG-01'),
(26, 'Test-package', '', 2, 0.00, '2025-10-27 11:20:15', '2025-10-27 11:20:15', 'TEST123'),
(27, 'Silvester In Nepal', '', 14, 0.00, '2025-10-31 06:29:26', '2025-11-04 04:53:00', 'NPKTM010'),
(28, 'WDT-kultur undwanden', '', 13, 0.00, '2025-11-02 02:59:15', '2025-11-02 02:59:15', 'WDT-11');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `must_change_password` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `is_active`, `created_at`, `last_login`, `must_change_password`) VALUES
(1, 'Admin User', 'admin@travelagency.com', 'admin123', 1, '2025-10-16 05:26:41', NULL, 0),
(2, 'admin', 'dulalpratyush@gmail.com', '$2y$10$C5Q7sTm.JvC5WgyxzParRuaG/2R9H/NWOFkuYIan7U2.pWYLe30.a', 1, '2025-10-16 05:42:45', '2025-11-23 01:29:07', 0),
(3, 'Parash Dulal', 'parashdulal@gmail.com', '$2y$10$5GqD0IM29crn2jal4lsnP.pgvEdHR1CHltNGZcLZKpxBml1dwW9aW', 1, '2025-10-24 03:14:46', '2025-11-05 03:55:46', 0),
(4, 'Rashmi Maharajan', 'athrashmi@gmail.com', '$2y$10$QzVKe7NLVp4k7rv4k/fq4.HqRVP51KzQnjYgNiITLQ2Tp9rFDfdbC', 1, '2025-10-24 05:46:22', '2025-10-24 05:51:01', 0),
(5, 'Sudama Karki', 'athsudama@gmail.com', '$2y$10$nvYH/YFbdXMlbAxQKyZ8lurSmKpuXn3jCPCBdxWHMtYZzeiVKfRCO', 1, '2025-10-25 11:01:33', '2025-10-27 05:43:58', 0),
(6, 'Pratyush2', 'pratyushcollege68@gmail.com', '$2y$10$t8vzDkw60U2LlKh4fR/T2.VVtHtb7h8H8WUez9CQG/YcgMUxyatB6', 1, '2025-10-26 02:51:15', '2025-11-04 02:28:27', 0),
(8, 'samjana karki', 'athsamjana@gmail.com', '$2y$10$bhLAQNS4nUqTbFHgm8GZLeIrjib6I5MkDperyDFKvAymjC0jwFgi.', 1, '2025-10-31 06:23:14', '2025-11-04 04:44:25', 0),
(9, 'Samir', 'samir@aroundthehimalayas.com', '$2y$10$CN4g7k7KxrfG8W5EFTJgpOd0aU65Rvqk8EPUqflScwxhGN790h32.', 1, '2025-11-04 10:18:33', '2025-11-04 10:21:21', 0);

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` int(11) NOT NULL,
  `vehicle_name` varchar(255) NOT NULL,
  `capacity` int(11) NOT NULL DEFAULT 1,
  `availability` enum('Available','Not Available','On Trip') DEFAULT 'Available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `email` varchar(255) DEFAULT NULL,
  `number_plate` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `vehicle_name`, `capacity`, `availability`, `created_at`, `updated_at`, `email`, `number_plate`) VALUES
(5, 'Hiace Kapil Gurung', 15, 'Available', '2025-10-24 07:27:31', '2025-10-27 09:14:52', '', 'BA-1-PA-1526'),
(6, 'Hiace Dipak Shrestha', 15, 'Available', '2025-10-24 07:27:57', '2025-10-27 09:15:52', '', 'BA-1-PA-175'),
(7, 'Hiace Dil Bahadur Tamang', 15, 'Available', '2025-10-24 07:28:16', '2025-10-27 09:15:31', '', 'BA-1-PA-1755'),
(8, 'Jeep Bhim Bahadur Gurung', 9, 'Available', '2025-10-24 07:28:42', '2025-10-27 09:16:08', '', 'BA-1-A1493'),
(9, 'EV Naexon', 4, 'Available', '2025-10-27 09:16:43', '2025-10-27 09:16:43', '', ''),
(10, 'Taxi', 4, 'Available', '2025-11-23 01:29:42', '2025-11-23 01:29:42', '', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `guests`
--
ALTER TABLE `guests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trip_id` (`trip_id`),
  ADD KEY `type` (`type`);

--
-- Indexes for table `guides`
--
ALTER TABLE `guides`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_name` (`name`);

--
-- Indexes for table `hotels`
--
ALTER TABLE `hotels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_name` (`name`);

--
-- Indexes for table `hotel_email_logs`
--
ALTER TABLE `hotel_email_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_trip_hotel` (`trip_id`,`hotel_id`);

--
-- Indexes for table `itinerary_days`
--
ALTER TABLE `itinerary_days`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guide_id` (`guide_id`),
  ADD KEY `vehicle_id` (`vehicle_id`),
  ADD KEY `hotel_id` (`hotel_id`),
  ADD KEY `idx_trip_id` (`trip_id`),
  ADD KEY `idx_day_date` (`day_date`);

--
-- Indexes for table `package_activities`
--
ALTER TABLE `package_activities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_name` (`name`);

--
-- Indexes for table `package_day_requirements`
--
ALTER TABLE `package_day_requirements`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_package_day` (`trip_package_id`,`day_number`),
  ADD KEY `hotel_id` (`hotel_id`),
  ADD KEY `idx_trip_package` (`trip_package_id`),
  ADD KEY `idx_day_number` (`day_number`);

--
-- Indexes for table `package_hotel_assignments`
--
ALTER TABLE `package_hotel_assignments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_package_day` (`trip_package_id`,`day_number`),
  ADD KEY `hotel_id` (`hotel_id`);

--
-- Indexes for table `pax_amendments`
--
ALTER TABLE `pax_amendments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_trip_created` (`trip_id`,`created_at`);

--
-- Indexes for table `pax_details`
--
ALTER TABLE `pax_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_trip` (`trip_id`);

--
-- Indexes for table `room_types`
--
ALTER TABLE `room_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trips`
--
ALTER TABLE `trips`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trip_package_id` (`trip_package_id`),
  ADD KEY `idx_customer_name` (`customer_name`),
  ADD KEY `idx_start_date` (`start_date`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `trip_arrivals`
--
ALTER TABLE `trip_arrivals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trip_id` (`trip_id`),
  ADD KEY `arrival_date` (`arrival_date`),
  ADD KEY `vehicle_id` (`vehicle_id`),
  ADD KEY `guide_id` (`guide_id`);

--
-- Indexes for table `trip_departures`
--
ALTER TABLE `trip_departures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trip_id` (`trip_id`),
  ADD KEY `departure_date` (`departure_date`),
  ADD KEY `vehicle_id` (`vehicle_id`),
  ADD KEY `guide_id` (`guide_id`);

--
-- Indexes for table `trip_guests`
--
ALTER TABLE `trip_guests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trip_id` (`trip_id`),
  ADD KEY `type` (`type`);

--
-- Indexes for table `trip_packages`
--
ALTER TABLE `trip_packages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_vehicle_name` (`vehicle_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `guests`
--
ALTER TABLE `guests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `guides`
--
ALTER TABLE `guides`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `hotels`
--
ALTER TABLE `hotels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `hotel_email_logs`
--
ALTER TABLE `hotel_email_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `itinerary_days`
--
ALTER TABLE `itinerary_days`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2297;

--
-- AUTO_INCREMENT for table `package_activities`
--
ALTER TABLE `package_activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7298;

--
-- AUTO_INCREMENT for table `package_day_requirements`
--
ALTER TABLE `package_day_requirements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=813;

--
-- AUTO_INCREMENT for table `package_hotel_assignments`
--
ALTER TABLE `package_hotel_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `pax_amendments`
--
ALTER TABLE `pax_amendments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `pax_details`
--
ALTER TABLE `pax_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=145;

--
-- AUTO_INCREMENT for table `room_types`
--
ALTER TABLE `room_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `trips`
--
ALTER TABLE `trips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=175;

--
-- AUTO_INCREMENT for table `trip_arrivals`
--
ALTER TABLE `trip_arrivals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=207;

--
-- AUTO_INCREMENT for table `trip_departures`
--
ALTER TABLE `trip_departures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `trip_guests`
--
ALTER TABLE `trip_guests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=128;

--
-- AUTO_INCREMENT for table `trip_packages`
--
ALTER TABLE `trip_packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `itinerary_days`
--
ALTER TABLE `itinerary_days`
  ADD CONSTRAINT `itinerary_days_ibfk_1` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `itinerary_days_ibfk_2` FOREIGN KEY (`guide_id`) REFERENCES `guides` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `itinerary_days_ibfk_3` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `itinerary_days_ibfk_4` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `package_day_requirements`
--
ALTER TABLE `package_day_requirements`
  ADD CONSTRAINT `package_day_requirements_ibfk_1` FOREIGN KEY (`trip_package_id`) REFERENCES `trip_packages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `package_day_requirements_ibfk_2` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `package_hotel_assignments`
--
ALTER TABLE `package_hotel_assignments`
  ADD CONSTRAINT `package_hotel_assignments_ibfk_1` FOREIGN KEY (`trip_package_id`) REFERENCES `trip_packages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `package_hotel_assignments_ibfk_2` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pax_amendments`
--
ALTER TABLE `pax_amendments`
  ADD CONSTRAINT `pax_amendments_ibfk_1` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pax_details`
--
ALTER TABLE `pax_details`
  ADD CONSTRAINT `pax_details_ibfk_1` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `trips`
--
ALTER TABLE `trips`
  ADD CONSTRAINT `trips_ibfk_1` FOREIGN KEY (`trip_package_id`) REFERENCES `trip_packages` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
