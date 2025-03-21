-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 21, 2025 at 09:39 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mikrotik_cloud`
--

-- --------------------------------------------------------

--
-- Table structure for table `client_reports`
--

CREATE TABLE `client_reports` (
  `report_id` int(11) NOT NULL,
  `report_code` varchar(250) DEFAULT NULL,
  `report_title` varchar(2000) DEFAULT NULL,
  `report_description` mediumtext DEFAULT NULL,
  `client_id` varchar(200) DEFAULT NULL,
  `admin_reporter` varchar(200) DEFAULT NULL,
  `admin_attender` varchar(200) DEFAULT NULL,
  `report_date` varchar(200) DEFAULT NULL,
  `resolve_time` varchar(200) DEFAULT NULL,
  `status` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `client_reports`
--

INSERT INTO `client_reports` (`report_id`, `report_code`, `report_title`, `report_description`, `client_id`, `admin_reporter`, `admin_attender`, `report_date`, `resolve_time`, `status`) VALUES
(5, 'ACJ001', 'Unstable Internet', 'Faulty router!', '31', '1', NULL, '20250307171508', NULL, 'pending'),
(7, 'ACK001', 'Faulty router', 'Faulty router.', '33', '1', 'Ranje Ngige', '20250320174330', '20250320194300', 'cleared');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `client_reports`
--
ALTER TABLE `client_reports`
  ADD PRIMARY KEY (`report_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `client_reports`
--
ALTER TABLE `client_reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
