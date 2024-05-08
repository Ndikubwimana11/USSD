-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 07, 2024 at 11:43 PM
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
-- Database: `uzima`
--

-- --------------------------------------------------------

--
-- Table structure for table `chicken_order`
--

CREATE TABLE `chicken_order` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `chicken_type` enum('Meat','Laying') DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `total_cost` decimal(10,2) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_status` enum('Paid','Unpaid') DEFAULT 'Unpaid',
  `delivery_status` varchar(50) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chicken_order`
--

INSERT INTO `chicken_order` (`order_id`, `user_id`, `chicken_type`, `quantity`, `total_cost`, `order_date`, `payment_status`, `delivery_status`) VALUES
(2, NULL, 'Meat', 10, 12000.00, '2024-05-07 21:16:43', 'Unpaid', 'Pending'),
(3, 17, 'Meat', 10, NULL, '2024-05-07 21:18:22', 'Unpaid', 'proved'),
(4, NULL, 'Meat', 10, 12000.00, '2024-05-07 21:18:37', 'Unpaid', 'Pending'),
(5, NULL, 'Meat', 10, 12000.00, '2024-05-07 21:22:06', 'Unpaid', 'Pending'),
(6, 17, 'Meat', 10, 12000.00, '2024-05-07 21:23:09', 'Unpaid', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `chicken_price`
--

CREATE TABLE `chicken_price` (
  `id` int(11) NOT NULL,
  `type` varchar(20) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chicken_price`
--

INSERT INTO `chicken_price` (`id`, `type`, `price`) VALUES
(1, 'meat', 1200.00),
(2, 'laying', 1500.00);

-- --------------------------------------------------------

--
-- Table structure for table `uzima_users`
--

CREATE TABLE `uzima_users` (
  `ID` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `national_id` int(16) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `address` varchar(20) NOT NULL,
  `pin` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `uzima_users`
--

INSERT INTO `uzima_users` (`ID`, `name`, `national_id`, `phone`, `address`, `pin`) VALUES
(15, 'uwayo', 12345, '0781157978', 'kabuga', '123'),
(16, 'sonia', 2002134567, '123', 'kabeza', '1234'),
(17, 'sonia', 2334, '0781157911', 'kabeza', '1234');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chicken_order`
--
ALTER TABLE `chicken_order`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `chicken_price`
--
ALTER TABLE `chicken_price`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `uzima_users`
--
ALTER TABLE `uzima_users`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chicken_order`
--
ALTER TABLE `chicken_order`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `chicken_price`
--
ALTER TABLE `chicken_price`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `uzima_users`
--
ALTER TABLE `uzima_users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chicken_order`
--
ALTER TABLE `chicken_order`
  ADD CONSTRAINT `chicken_order_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `uzima_users` (`ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
