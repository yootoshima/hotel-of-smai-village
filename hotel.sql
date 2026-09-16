-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3307
-- Generation Time: Sep 16, 2026 at 12:59 PM
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
-- Database: `hotel`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `book_id` int(11) NOT NULL,
  `cus_id` int(11) DEFAULT NULL,
  `room_id` int(11) DEFAULT NULL,
  `check_in` date DEFAULT NULL,
  `check_out` date DEFAULT NULL,
  `total_price` int(11) DEFAULT NULL,
  `status` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `cus_id` int(11) NOT NULL,
  `cus_name` varchar(100) DEFAULT NULL,
  `phone` int(10) DEFAULT NULL,
  `id_card` varchar(13) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','member') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`cus_id`, `cus_name`, `phone`, `id_card`, `password`, `role`) VALUES
(1, 'Test1', 123456789, '2147483647', '$2y$10$1dzTnyywRr83/MTiXdSP/uG4D4sfW1QuGlvOxmbjhaGcytlfueYA.', 'member'),
(4, 'tester1', 1234567890, '2147483647', '$2y$10$HSfpdU5POrnlRUw3cl9NP.BmGr4cMXXnVwBXTL1m35OlPC2GFTUze', 'member'),
(5, 'tester2', 123456789, '1234567890123', '$2y$10$TX/OPWrhrrvZ2O1aiVr6OeKX237ECkD90.5AtGg9q0qyeywPjngty', 'member'),
(6, 'tester3', 1234567890, '1234567890123', '$2y$10$Ru8pUrefADw7BKT1okNF/.rjwCbwXtti2gb4H6jFagXhZoFz8pvQ.', 'member'),
(7, 'tester4', 1234567890, '1234567890123', '$2y$10$JkHBFx92l7lxQH234o3L..v1B7pLKkBo9SzazgEDewQPr6wUlM1C6', 'member'),
(8, 'tester4', 1234567890, '1234567890123', '$2y$10$TDGBtuAQyrG4C20CdzQ8bOFuiF8V3osJaosFeVFUx9kjbSu9k6KNu', 'member'),
(9, 'tester4', 1234567890, '1234567890123', '$2y$10$2YWJd8jhreRjTzVLFOYee.DKzeIVuD/E1UZil5ddVFWrI3R2X/QVe', 'member');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `room_id` int(11) NOT NULL,
  `room_number` int(11) DEFAULT NULL,
  `room_type` varchar(100) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`book_id`),
  ADD KEY `FOREIGNKEY` (`cus_id`) USING BTREE,
  ADD KEY `FOREIGNKEY1` (`room_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`cus_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `cus_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
