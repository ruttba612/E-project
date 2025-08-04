-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 31, 2025 at 03:25 PM
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
-- Database: `auranest_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `PROFILE_pic` varchar(255) DEFAULT NULL,
  `is_admin` tinyint(1) DEFAULT 0,
  `dark_mode` tinyint(1) DEFAULT 0,
  `profile_picture` varchar(255) DEFAULT 'default.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`, `status`, `created_at`, `updated_at`, `PROFILE_pic`, `is_admin`, `dark_mode`, `profile_picture`) VALUES
(1, 'bisma', 'admin@auranest.com', 'admin123', 'active', '2025-07-28 18:19:54', '2025-07-29 18:17:40', 'uploads/6887c3e422234_my png.PNG', 1, 1, 'default.jpg'),
(2, 'ruttba', 'ruttba@gmail.com', '$2y$10$RRbNny.OOxqSd.O/usa99uTTpJYxy.aZQXAoRbcoXUw6JSbqVwDXO', 'active', '2025-07-31 12:36:52', '2025-07-31 12:36:52', NULL, 0, 0, 'default.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

CREATE TABLE `banners` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `image` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `banners`
--

INSERT INTO `banners` (`id`, `title`, `image`, `link`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Summer Sale', '', '/promotions', 'active', '2025-07-28 07:15:15', '2025-07-30 16:05:59'),
(2, 'New Arrivals', '', '/products', 'active', '2025-07-28 07:15:15', '2025-07-30 16:05:59'),
(3, 'Summer Sale Banner', '', NULL, 'active', '2025-07-30 16:05:14', '2025-07-30 16:05:59'),
(4, 'New Collection Banner', '', NULL, 'inactive', '2025-07-30 16:05:14', '2025-07-30 16:05:59'),
(5, 'Summer Sale Banner', '', NULL, 'active', '2025-07-30 16:59:06', '2025-07-30 17:09:51'),
(6, 'New Collection Banner', '', NULL, 'inactive', '2025-07-30 16:59:06', '2025-07-30 17:09:51'),
(7, 'Summer Sale Banner', '', NULL, 'active', '2025-07-30 17:08:14', '2025-07-30 17:09:51'),
(8, 'New Collection Banner', '', NULL, 'inactive', '2025-07-30 17:08:14', '2025-07-30 17:09:51'),
(9, 'Summer Sale Banner', '', NULL, 'active', '2025-07-30 17:23:05', '2025-07-30 17:23:05'),
(10, 'New Collection Banner', '', NULL, 'inactive', '2025-07-30 17:23:05', '2025-07-30 17:23:05');

-- --------------------------------------------------------

--
-- Table structure for table `boxes`
--

CREATE TABLE `boxes` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `stock` int(11) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `boxes`
--

INSERT INTO `boxes` (`id`, `name`, `description`, `price`, `image`, `stock`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Gift Box - Small', 'Elegant small gift box', 5.00, 'uploads/gift_box_small.jpg', 50, 'active', '2025-07-28 07:15:15', '2025-07-28 07:15:15'),
(2, 'Gift Box - Large', 'Luxury large gift box', 10.00, 'uploads/gift_box_large.jpg', 30, 'active', '2025-07-28 07:15:15', '2025-07-28 07:15:15');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `image` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`, `updated_at`, `image`, `slug`, `status`) VALUES
(2, 'Beauty Essentials', 'Skincare, makeup, and beauty products', '2025-07-28 07:15:15', '2025-07-31 13:07:17', NULL, 'beauty-essentials', 'inactive'),
(3, 'Jewelry', 'Jewelry and accessories', '2025-07-28 07:15:15', '2025-07-31 13:06:00', NULL, 'jewelry', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `cms_pages`
--

CREATE TABLE `cms_pages` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `status` enum('published','draft','trashed') NOT NULL DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cms_pages`
--

INSERT INTO `cms_pages` (`id`, `title`, `slug`, `content`, `meta_title`, `meta_description`, `meta_keywords`, `status`, `created_at`, `updated_at`) VALUES
(1, 'About Us', 'about-us', '<h2>About Auranest</h2><p>Welcome to Auranest, your one-stop shop for fashion!</p>', 'About Auranest', 'Learn about Auranest, a leading fashion brand.', 'fashion, auranest, about', 'published', '2025-07-01 05:00:00', '2025-07-01 05:00:00'),
(2, 'Privacy Policy', 'privacy-policy', '<h2>Privacy Policy</h2><p>Your privacy is our priority.</p>', 'Auranest Privacy Policy', 'Read Auranests privacy policy.', 'privacy, policy, auranest', 'published', '2025-07-01 07:00:00', '2025-07-01 07:00:00'),
(3, 'Contact Us', 'contact', '<h2>Contact Us</h2><p>Reach us at support@auranest.com.</p>', 'Contact Auranest', 'Get in touch with Auranest support.', 'contact, support, auranest', 'draft', '2025-07-01 09:00:00', '2025-07-01 09:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `cms_page_versions`
--

CREATE TABLE `cms_page_versions` (
  `id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `status` enum('published','draft','trashed') NOT NULL,
  `version_timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cms_page_versions`
--

INSERT INTO `cms_page_versions` (`id`, `page_id`, `title`, `slug`, `content`, `meta_title`, `meta_description`, `meta_keywords`, `status`, `version_timestamp`) VALUES
(1, 1, 'About Us', 'about-us', '<h2>About Auranest</h2><p>Welcome to Auranest, your one-stop shop for fashion!</p>', 'About Auranest', 'Learn about Auranest, a leading fashion brand.', 'fashion, auranest, about', 'published', '2025-07-01 05:00:00'),
(2, 2, 'Privacy Policy', 'privacy-policy', '<h2>Privacy Policy</h2><p>Your privacy is our priority.</p>', 'Auranest Privacy Policy', 'Read Auranests privacy policy.', 'privacy, policy, auranest', 'published', '2025-07-01 07:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `total_spending` decimal(10,2) DEFAULT 0.00,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `email`, `password`, `total_spending`, `status`, `created_at`, `updated_at`, `address`) VALUES
(1, 'Sara Ali', 'sara@example.com', 'hashed_password1', 0.00, '', '2025-07-28 07:15:15', '2025-07-30 21:03:09', NULL),
(2, 'Ali Khan', 'ali@example.com', 'hashed_password2', 0.00, 'active', '2025-07-28 07:15:15', '2025-07-28 07:15:15', NULL),
(3, 'Fatima Ahmed', 'fatima@example.com', 'hashed_password3', 0.00, 'active', '2025-07-28 07:15:15', '2025-07-28 07:15:15', NULL),
(4, 'Fatima Noor', 'fatima.noor@example.com', '', 275.25, 'active', '2025-07-30 20:45:28', '2025-07-30 20:45:28', NULL),
(5, 'Zain Malik', 'zain.malik@example.com', '', 180.90, '', '2025-07-30 20:45:28', '2025-07-30 21:07:00', NULL),
(6, 'Ayesha Siddiqui', 'ayesha.siddiqui@example.com', '', 410.30, 'active', '2025-07-30 20:45:28', '2025-07-30 20:45:28', NULL),
(7, 'Omar Farooq', 'omar.farooq@example.com', '', 350.60, 'active', '2025-07-30 20:45:28', '2025-07-30 20:45:28', NULL),
(8, 'Maryam Iqbal', 'maryam.iqbal@example.com', '', 220.45, 'active', '2025-07-30 20:45:28', '2025-07-30 20:45:28', NULL),
(9, 'Usman Qureshi', 'usman.qureshi@example.com', '', 600.10, 'active', '2025-07-30 20:45:28', '2025-07-30 20:45:28', NULL),
(10, 'Sadia Khan', 'sadia.khan@example.com', '', 175.80, 'active', '2025-07-30 20:45:28', '2025-07-30 20:45:28', NULL),
(11, 'Bilal Ahmed', 'bilal.ahmed@example.com', '', 290.20, 'active', '2025-07-30 20:45:28', '2025-07-30 20:45:28', NULL),
(12, 'Hina Saeed', 'hina.saeed@example.com', '', 430.15, 'active', '2025-07-30 20:45:28', '2025-07-30 20:45:28', NULL),
(13, 'Ahmed Raza', 'ahmed.raza@example.com', '', 320.00, '', '2025-07-30 20:45:28', '2025-07-31 13:00:43', NULL),
(14, 'Zara Ali', 'zara.ali@example.com', '', 250.70, 'active', '2025-07-30 20:45:28', '2025-07-30 20:45:28', NULL),
(15, 'Ibrahim Shah', 'ibrahim.shah@example.com', '', 190.40, 'active', '2025-07-30 20:45:28', '2025-07-30 20:45:28', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `status` enum('pending','responded','closed') NOT NULL DEFAULT 'pending',
  `response` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `user_id`, `message`, `status`, `response`, `created_at`) VALUES
(3, 3, 'Shipping took too long for my order.', 'pending', NULL, '2025-03-31 09:00:00'),
(4, 4, 'Loved the red matte lipstick! Great quality.', 'pending', NULL, '2025-07-30 07:00:00'),
(5, 5, 'Georeous look! Great quality.', 'pending', NULL, '2025-03-30 07:00:00'),
(6, 1, 'Can you add more colors to the Blue Evening Dress?', 'pending', NULL, '2025-07-30 05:00:00'),
(7, 2, 'Loved the Diamond Stud Earrings! Great quality.', 'responded', 'Thank you for your feedback!', '2025-07-30 07:00:00'),
(8, 3, 'Shipping took too long for my order.', 'responded', 'thanks]', '2025-07-30 09:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_logs`
--

CREATE TABLE `maintenance_logs` (
  `id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `maintenance_logs`
--

INSERT INTO `maintenance_logs` (`id`, `action`, `details`, `admin_id`, `created_at`) VALUES
(1, 'Updated Products', 'Added 10 new products', 1, '2025-07-28 07:15:15'),
(2, 'Fixed Bug', 'Resolved checkout issue', 1, '2025-07-28 07:15:15');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','cancelled','delivered') DEFAULT 'pending',
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_id`, `customer_id`, `total_amount`, `status`, `order_date`, `updated_at`) VALUES
(1, '#AUN001', 1, 125.00, 'cancelled', '2025-07-28 07:15:15', '2025-07-30 17:56:02'),
(2, '#AUN002', 2, 250.50, 'cancelled', '2025-07-28 07:15:15', '2025-07-30 17:56:02'),
(3, '#AUN003', 3, 75.00, 'cancelled', '2025-07-28 07:15:15', '2025-07-30 17:56:02'),
(4, '1', 1, 89.97, 'pending', '2025-07-30 05:00:00', '2025-07-30 18:17:05'),
(5, '2', 2, 199.98, 'shipped', '2025-07-29 10:30:00', '2025-07-30 18:17:05'),
(6, '3', 1, 49.99, 'cancelled', '2025-07-28 07:00:00', '2025-07-30 18:17:05');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(4, 1, 1, 3, 29.99),
(5, 2, 2, 2, 99.99),
(6, 3, 1, 1, 49.99);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `stock`, `image`, `category_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Rose Gold Necklace', 'Elegant rose gold necklace with a minimalist pendant.', 49.99, 20, 'd7d921_6048f2aaedf345eebaa45ef85185f98c~mv2.png', 3, 'active', '2025-07-30 13:56:53', '2025-07-31 13:07:26'),
(2, 'Diamond Stud Earrings', 'Sparkling diamond stud earrings for special occasions.', 89.99, 15, NULL, 3, 'active', '2025-07-30 13:56:53', '2025-07-30 14:51:34'),
(3, 'Silver Charm Bracelet', 'Customizable silver bracelet with charm slots.', 39.99, 25, NULL, 3, 'active', '2025-07-30 13:56:53', '2025-07-30 14:51:34'),
(4, 'Pearl Drop Earrings', 'Classic pearl drop earrings for a timeless look.', 59.99, 18, NULL, 3, 'active', '2025-07-30 13:56:53', '2025-07-30 14:51:34'),
(5, 'Hydrating Face Serum', 'Lightweight serum with hyaluronic acid for hydration.', 29.99, 30, NULL, 2, 'active', '2025-07-30 13:56:53', '2025-07-30 14:51:34'),
(6, 'Matte Lipstick', 'Long-lasting matte lipstick in a vibrant coral shade.', 19.99, 40, NULL, 2, 'active', '2025-07-30 13:56:53', '2025-07-30 14:51:34'),
(7, 'Cleansing Balm', 'Gentle cleansing balm to remove makeup and impurities.', 24.99, 35, NULL, 2, 'active', '2025-07-30 13:56:53', '2025-07-30 14:51:34'),
(8, 'Moisturizing Cream', 'Rich moisturizer with shea butter for smooth skin.', 34.79, 27, NULL, 2, 'active', '2025-07-30 13:56:53', '2025-07-30 20:02:44');

-- --------------------------------------------------------

--
-- Table structure for table `promotions`
--

CREATE TABLE `promotions` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `discount_type` enum('percentage','fixed') NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `categories` text DEFAULT NULL,
  `products` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive','expired') NOT NULL,
  `site_wide` tinyint(1) DEFAULT 0,
  `user_group` varchar(50) DEFAULT 'all',
  `promo_code` varchar(20) NOT NULL,
  `banner` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `promotions`
--

INSERT INTO `promotions` (`id`, `title`, `discount_type`, `discount_amount`, `start_date`, `end_date`, `categories`, `products`, `description`, `status`, `site_wide`, `user_group`, `promo_code`, `banner`) VALUES
(2, 'Winter Deal', 'fixed', 500.00, '2025-08-01', '2025-08-15', '3', '4,5', 'Rs 500 off on Necklaces', 'active', 0, 'new_users', 'WINTER5678', ''),
(3, 'Eid Offer', 'percentage', 15.00, '2025-09-01', '2025-09-10', '', '', 'Site-wide 15% off', '', 1, 'top_customers', 'EID9012', 'uploads/promotions/eid_offer.jpg'),
(4, 'Summer Sale', 'percentage', 20.00, '2025-07-31', '2025-08-30', '1,2', '1,2,3', '20% off on Dresses and Earrings', 'expired', 1, 'all', 'COPY6544', 'uploads/promotions/summer_sale.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'site_title', 'Auranest', '2025-07-30 17:00:35'),
(2, 'contact_email', 'contact@auranest.com', '2025-07-30 17:00:35'),
(3, 'site_logo', NULL, '2025-07-30 17:00:35');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `boxes`
--
ALTER TABLE `boxes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `cms_pages`
--
ALTER TABLE `cms_pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `cms_page_versions`
--
ALTER TABLE `cms_page_versions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `page_id` (`page_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `maintenance_logs`
--
ALTER TABLE `maintenance_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_id`),
  ADD KEY `idx_customer` (`customer_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order` (`order_id`),
  ADD KEY `idx_product` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category_id`);

--
-- Indexes for table `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `promo_code` (`promo_code`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `banners`
--
ALTER TABLE `banners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `boxes`
--
ALTER TABLE `boxes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `cms_pages`
--
ALTER TABLE `cms_pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cms_page_versions`
--
ALTER TABLE `cms_page_versions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `maintenance_logs`
--
ALTER TABLE `maintenance_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `promotions`
--
ALTER TABLE `promotions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cms_page_versions`
--
ALTER TABLE `cms_page_versions`
  ADD CONSTRAINT `cms_page_versions_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `cms_pages` (`id`);

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `customers` (`id`);

--
-- Constraints for table `maintenance_logs`
--
ALTER TABLE `maintenance_logs`
  ADD CONSTRAINT `maintenance_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
