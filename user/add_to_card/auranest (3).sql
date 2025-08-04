-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 30, 2025 at 08:56 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `auranest`
--

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_name` varchar(100) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `order_id`, `product_name`, `quantity`, `price`) VALUES
(1, 1, NULL, 1, 1500.00),
(2, 2, NULL, 1, 1500.00),
(3, 2, NULL, 1, 3200.00),
(4, 4, NULL, 1, 1500.00),
(5, 4, NULL, 1, 2200.00);

-- --------------------------------------------------------

--
-- Table structure for table `moon`
--

CREATE TABLE `moon` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `moon`
--

INSERT INTO `moon` (`id`, `name`, `email`, `phone`, `address`, `city`, `payment_method`, `created_at`) VALUES
(1, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-30 18:37:50'),
(2, 'AADIL KHAN', 'misfar@gmail.com', '+923181215455', 'sec 2', 'KARACHI', 'Cash on Delivery', '2025-07-30 18:43:17'),
(3, NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-30 18:44:05'),
(4, 'AADIL KHAN', 'khaanaadil987654@gmail.com', '+923181215455', 'SEC 2', 'KARACHI', 'Card', '2025-07-30 18:53:36');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_date`) VALUES
(1, '2025-07-26 01:45:35'),
(2, '2025-07-26 01:47:09'),
(3, '2025-07-26 01:48:56'),
(4, '2025-07-26 01:51:38'),
(5, '2025-07-26 01:51:58'),
(6, '2025-07-26 01:52:24'),
(7, '2025-07-26 01:56:20'),
(8, '2025-07-26 02:05:19'),
(9, '2025-07-30 21:52:20'),
(10, '2025-07-30 21:56:15'),
(11, '2025-07-30 22:06:05'),
(12, '2025-07-30 23:14:45');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`, `subtotal`) VALUES
(1, 1, 1, 10, 3.00, 30.00),
(2, 1, 2, 1, 1.00, 1.00),
(3, 1, 3, 4, 2200.00, 8800.00),
(4, 2, 5, 1, 5000.00, 5000.00),
(5, 3, 2, 1, 1500.00, 1500.00),
(6, 4, 1, 3, 3200.00, 9600.00),
(7, 4, 2, 1, 1500.00, 1500.00),
(8, 5, 2, 1, 1500.00, 1500.00),
(9, 6, 2, 1, 1500.00, 1500.00),
(10, 6, 3, 1, 2200.00, 2200.00),
(11, 7, 2, 1, 1500.00, 1500.00),
(12, 8, 2, 1, 1500.00, 1500.00),
(13, 9, 3, 1, 2200.00, 2200.00),
(14, 9, 5, 1, 5000.00, 5000.00),
(15, 10, 3, 1, 2200.00, 2200.00),
(16, 11, 4, 1, 2700.00, 2700.00),
(17, 12, 4, 1, 2700.00, 2700.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`) VALUES
(1, 'Rose Gold Necklace', 3200),
(2, 'Silver Ring', 1500),
(3, 'Elegant Earrings', 2200),
(4, 'Golden Bracelet', 2700),
(5, 'Diamond Pendant', 5000);

-- --------------------------------------------------------

--
-- Table structure for table `search`
--

CREATE TABLE `search` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `search`
--

INSERT INTO `search` (`id`, `name`, `category`, `price`) VALUES
(1, 'Rose Gold Necklace', 'Jewelry', 3200.00),
(2, 'Silver Ring', 'Jewelry', 1500.00),
(3, 'Elegant Earrings', 'Jewelry', 2200.00),
(4, 'Golden Bracelet', 'Jewelry', 2700.00),
(5, 'Diamond Pendant', 'Jewelry', 5000.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `moon`
--
ALTER TABLE `moon`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `search`
--
ALTER TABLE `search`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `moon`
--
ALTER TABLE `moon`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `search`
--
ALTER TABLE `search`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
