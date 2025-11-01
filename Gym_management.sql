-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 31, 2025 at 09:24 PM
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
-- Database: `Gym_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `Member`
--

CREATE TABLE `Member` (
  `member_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `join_date` date NOT NULL,
  `plan_id` int(11) NOT NULL,
  `address` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL,
  `age` int(11) NOT NULL,
  `amount_to_pay` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Member`
--

INSERT INTO `Member` (`member_id`, `name`, `email`, `phone`, `gender`, `join_date`, `plan_id`, `address`, `created_at`, `age`, `amount_to_pay`) VALUES
(1, 'Khushi', 'khushi@gmail.com', '9876543210', 'Female', '2025-10-30', 1, 'Raipur', '2025-10-31 00:22:02', 21, 0.00),
(3, 'Priyanshi', 'priyanshi@gmail.com', '9123456780', 'Female', '2025-10-31', 3, 'Thano Road', '2025-10-31 09:31:13', 21, 4500.00),
(4, 'Sumit', 'sumit@gmail.com', '8989898989', 'Male', '2025-10-31', 2, 'Rajpur Road', '2025-10-31 09:31:54', 24, 2500.00),
(5, 'Shivank', 'shivank@gmail.com', '7500718539', 'Male', '2025-10-31', 4, 'Khalanga', '2025-10-31 09:32:22', 22, 4000.00),
(6, 'Ankit', 'ankit@gmail.com', '9768679432', 'Male', '2025-10-31', 4, 'Raipur Road', '2025-10-31 09:32:55', 22, 8000.00),
(7, 'Aditi', 'aditi@gmail.com', '7015714321', 'Female', '2025-10-31', 4, 'Raipur Road', '2025-10-31 09:34:28', 18, 7000.00),
(8, 'Harshita', 'harshita@gmail.com', '8765432190', 'Female', '2025-10-31', 2, 'Rajpur', '2025-10-31 09:48:39', 18, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `Payment`
--

CREATE TABLE `Payment` (
  `payment_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `payment_date` date NOT NULL,
  `next_due_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Payment`
--

INSERT INTO `Payment` (`payment_id`, `member_id`, `plan_id`, `amount_paid`, `payment_date`, `next_due_date`) VALUES
(1, 5, 4, 4000.00, '2025-10-31', '2025-11-30'),
(2, 8, 2, 2500.00, '2025-10-31', '2025-11-30'),
(3, 1, 1, 1000.00, '2025-10-31', '2025-11-30'),
(8, 7, 4, 1000.00, '2025-10-31', '2025-11-30');

-- --------------------------------------------------------

--
-- Table structure for table `Plan`
--

CREATE TABLE `Plan` (
  `plan_id` int(11) NOT NULL,
  `plan_name` varchar(100) NOT NULL,
  `duration_month` int(11) NOT NULL,
  `price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Plan`
--

INSERT INTO `Plan` (`plan_id`, `plan_name`, `duration_month`, `price`) VALUES
(1, 'Basic FIt', 1, 1000),
(2, 'Silver Strength', 3, 2500),
(3, 'Gold Transformation', 6, 4500),
(4, 'Platinum Pro', 12, 8000);

-- --------------------------------------------------------

--
-- Table structure for table `User`
--

CREATE TABLE `User` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `User`
--

INSERT INTO `User` (`user_id`, `username`, `password`, `created_at`) VALUES
(1, 'Khushi', 'khushi123', '2025-10-30 21:35:32'),
(2, 'Sumit', 'sumit123', '2025-10-30 21:35:32'),
(3, 'Priyanshi', 'priyanshi123', '2025-10-30 21:35:32'),
(4, 'Admin', 'admin', '2025-11-01 01:50:49');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Member`
--
ALTER TABLE `Member`
  ADD PRIMARY KEY (`member_id`),
  ADD KEY `fk_member_plan` (`plan_id`);

--
-- Indexes for table `Payment`
--
ALTER TABLE `Payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `fk_payment_member` (`member_id`),
  ADD KEY `fk_payment_plan` (`plan_id`);

--
-- Indexes for table `Plan`
--
ALTER TABLE `Plan`
  ADD PRIMARY KEY (`plan_id`);

--
-- Indexes for table `User`
--
ALTER TABLE `User`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Member`
--
ALTER TABLE `Member`
  MODIFY `member_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `Payment`
--
ALTER TABLE `Payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `Plan`
--
ALTER TABLE `Plan`
  MODIFY `plan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `User`
--
ALTER TABLE `User`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Member`
--
ALTER TABLE `Member`
  ADD CONSTRAINT `fk_member_plan` FOREIGN KEY (`plan_id`) REFERENCES `Plan` (`plan_id`);

--
-- Constraints for table `Payment`
--
ALTER TABLE `Payment`
  ADD CONSTRAINT `fk_payment_member` FOREIGN KEY (`member_id`) REFERENCES `Member` (`member_id`),
  ADD CONSTRAINT `fk_payment_plan` FOREIGN KEY (`plan_id`) REFERENCES `Plan` (`plan_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
