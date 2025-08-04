-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 04, 2025 at 05:32 PM
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
(1, 'bisma', 'admin@auranest.com', 'admin123', 'active', '2025-07-28 18:19:54', '2025-08-04 12:26:31', 'uploads/6887c3e422234_my png.PNG', 1, 1, 'uploads/profile_1_1754239637.jpeg'),
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
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `prod_name` varchar(255) NOT NULL,
  `prod_price` int(11) NOT NULL,
  `prod_quantity` int(11) NOT NULL,
  `prod_img` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `prod_name`, `prod_price`, `prod_quantity`, `prod_img`) VALUES
(14, 'Hydrating Face Serum', 30, 2, 'cosmetic.jpg'),
(15, 'Matte Lipstick', 20, 1, 'WhatsApp Image 2025-07-18 at 4.28.40 PM.jpeg'),
(16, 'Moisturizing Cream', 35, 1, 'serum copy.jpg'),
(17, 'Rose Gold Necklace', 50, 1, 'lipstick.jpg'),
(18, 'Matte Lipstick', 20, 1, 'WhatsApp Image 2025-07-18 at 4.28.40 PM.jpeg');

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
-- Table structure for table `cosmetics_products`
--

CREATE TABLE `cosmetics_products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cosmetics_products`
--

INSERT INTO `cosmetics_products` (`id`, `name`, `price`) VALUES
(1, 'Rose Gold Necklace', 3200),
(2, 'Silver Ring', 1500),
(3, 'Elegant Earrings', 2200),
(4, 'Golden Bracelet', 2700),
(5, 'Diamond Pendant', 5000),
(6, 'Rose Gold Necklace', 3200),
(7, 'Silver Ring', 1500),
(8, 'Elegant Earrings', 2200),
(9, 'Golden Bracelet', 2700),
(10, 'Diamond Pendant', 5000),
(11, 'Luxe Matte Lipstick', 1200),
(12, 'Hydrating Foundation', 2200),
(13, 'Silk Finish Powder', 950),
(14, 'Volume Lash Mascara', 850),
(15, 'Jet Black Eyeliner', 700),
(16, 'Rose Blush Palette', 1350),
(17, 'Light Beige Concealer', 650),
(18, 'Coral Lip Crayon', 900),
(19, 'Dark Brown Eyebrow Pencil', 600),
(20, 'Pearl Glow Highlighter', 1100),
(21, 'Medium BB Cream', 1150),
(22, 'Nude Shine Lip Gloss', 750),
(23, 'Oil-Free Face Primer', 1300),
(24, 'Loose Setting Powder', 1400),
(25, 'Midnight Blue Gel Eyeliner', 780),
(26, 'Waterproof Mascara Plus', 980),
(27, 'Cherry Tinted Lip Balm', 550),
(28, 'Nude Tones Eyeshadow', 1950),
(29, 'Makeup Setting Spray', 1250),
(30, 'Warm Beige Foundation', 2100),
(31, 'Classic Red Nail Polish', 300),
(32, 'Cuticle Oil Pen', 350),
(33, 'Peach Glow Blusher', 820),
(34, 'Sun Kissed Bronzer', 980),
(35, 'Wine Matte Lipstick', 1250),
(36, 'Vitamin C Face Serum', 1850),
(37, 'Charcoal Peel-Off Mask', 600),
(38, 'Aloe Vera Cream', 950),
(39, 'Clear Eyebrow Gel', 560),
(40, 'Eye Primer Base', 740),
(41, 'Coral Bloom Liquid Blush', 1150),
(42, 'Light Tone CC Cream', 1050),
(43, 'High Coverage Concealer', 890),
(44, 'Intense Black Kajal', 420),
(45, 'Nude Pink Lip Liner', 480),
(46, 'Metal Grip Lash Curler', 450),
(47, 'Micellar Makeup Remover', 850),
(48, 'Smokey Eyeshadow Duo', 720),
(49, 'Nail Remover Pads', 300),
(50, 'Matte Nail Top Coat', 280),
(51, 'Watermelon Lip Tint', 580),
(52, 'Hyaluronic Glow Serum', 1750),
(53, 'Rose Water Face Mist', 700),
(54, 'Shimmer Eyeshadow Pot', 990),
(55, 'Coffee Brown Liquid Lipstick', 1050),
(56, 'Mini Lipstick Set (4)', 1800),
(57, 'Compact Travel Mirror', 250),
(58, 'Teardrop Makeup Sponge', 350),
(59, 'Medium Contour Stick', 940),
(60, 'Crystal Glitter Lip Gloss', 780),
(61, 'Aloe Vera Sheet Mask', 300),
(62, 'Acne Spot Gel', 1150),
(63, 'Witch Hazel Toner', 950),
(64, 'Deep Clean Oil Cleanser', 1050),
(65, 'Retinol Night Cream', 1650);

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
  `address` text DEFAULT NULL,
  `total_spent` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `email`, `password`, `total_spending`, `status`, `created_at`, `updated_at`, `address`, `total_spent`) VALUES
(1, 'Sara Ali', 'sara@example.com', '$2y$10$6z7X8Y9Z0A1B2C3D4E5F6G7H8I9J0K1L2M3N4O5P6Q7R8S9T0U1V', 0.00, '', '2025-07-28 07:15:15', '2025-08-03 18:01:39', NULL, 0.00),
(2, 'Ali Khan', 'ali@example.com', 'hashed_password2', 0.00, 'active', '2025-07-28 07:15:15', '2025-07-28 07:15:15', NULL, 0.00),
(3, 'Fatima Ahmed', 'fatima@example.com', 'hashed_password3', 0.00, 'active', '2025-07-28 07:15:15', '2025-07-28 07:15:15', NULL, 0.00),
(4, 'Fatima Noor', 'fatima.noor@example.com', '', 275.25, 'active', '2025-07-30 20:45:28', '2025-07-30 20:45:28', NULL, 0.00),
(5, 'Zain Malik', 'zain.malik@example.com', '', 180.90, '', '2025-07-30 20:45:28', '2025-07-30 21:07:00', NULL, 0.00),
(6, 'Ayesha Siddiqui', 'ayesha.siddiqui@example.com', '', 410.30, 'active', '2025-07-30 20:45:28', '2025-07-30 20:45:28', NULL, 0.00),
(7, 'Omar Farooq', 'omar.farooq@example.com', '', 350.60, 'active', '2025-07-30 20:45:28', '2025-07-30 20:45:28', NULL, 0.00),
(8, 'Maryam Iqbal', 'maryam.iqbal@example.com', '', 220.45, 'active', '2025-07-30 20:45:28', '2025-07-30 20:45:28', NULL, 0.00),
(9, 'Usman Qureshi', 'usman.qureshi@example.com', '', 600.10, 'active', '2025-07-30 20:45:28', '2025-07-30 20:45:28', NULL, 0.00),
(10, 'Sadia Khan', 'sadia.khan@example.com', '', 175.80, 'active', '2025-07-30 20:45:28', '2025-07-30 20:45:28', NULL, 0.00),
(11, 'Bilal Ahmed', 'bilal.ahmed@example.com', '', 290.20, 'active', '2025-07-30 20:45:28', '2025-07-30 20:45:28', NULL, 0.00),
(12, 'Hina Saeed', 'hina.saeed@example.com', '', 430.15, 'active', '2025-07-30 20:45:28', '2025-07-30 20:45:28', NULL, 0.00),
(13, 'Ahmed Raza', 'ahmed.raza@example.com', '', 320.00, '', '2025-07-30 20:45:28', '2025-07-31 13:00:43', NULL, 0.00),
(14, 'Zara Ali', 'zara.ali@example.com', '', 250.70, 'active', '2025-07-30 20:45:28', '2025-07-30 20:45:28', NULL, 0.00),
(15, 'Ibrahim Shah', 'ibrahim.shah@example.com', '', 190.40, 'active', '2025-07-30 20:45:28', '2025-07-30 20:45:28', NULL, 0.00),
(18, 'Sara Khan', 'amna@example.com', '$2y$10$...', 0.00, 'active', '2025-08-03 17:38:02', '2025-08-03 17:38:02', NULL, 150.00),
(19, 'Ayesha Malik', 'ayesha@example.com', '$2y$10$A1B2C3D4E5F6G7H8I9J0K1L2M3N4O5P6Q7R8S9T0U1V2W3X4Y5Z6', 0.00, 'active', '2025-08-03 17:38:02', '2025-08-03 18:01:39', NULL, 200.00),
(20, 'Bisma Khan', 'bisma@example.com', '$2y$10$X1Y2Z3A4B5C6D7E8F9G0H1I2J3K4L5M6N7O8P9Q0R1S2T3U4V5W6', 0.00, 'active', '2025-08-03 17:45:23', '2025-08-03 18:01:39', NULL, 0.00),
(21, 'hania', 'hania@auranest.com', '$2y$10$QDgDzACDpLIj/nc8kaJrdeNsa4w4yR8By5BvCTeCPeWyi4LDheD0i', 0.00, 'active', '2025-08-03 18:09:35', '2025-08-03 18:11:35', 'north karachi', 0.00),
(22, 'usman123', 'usman@gmail.com', '$2y$10$Ejfu1o8bkV7H3wJ6C/9gOe.uLL.ryLZJ4EyHM9scfikv9dv9tpBdm', 0.00, 'active', '2025-08-04 13:14:48', '2025-08-04 14:53:42', NULL, 419.94);

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
-- Table structure for table `feedback1`
--

CREATE TABLE `feedback1` (
  `id` int(11) NOT NULL,
  `username` varchar(250) NOT NULL,
  `email` varchar(250) NOT NULL,
  `message` varchar(250) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback1`
--

INSERT INTO `feedback1` (`id`, `username`, `email`, `message`, `created_at`) VALUES
(1, 'fahad', 'fahad@gmail.com', 'hellooooooooooooooooooo', '2025-07-31 09:43:51'),
(5, 'maleeha', 'maleeha@gmail.com', 'edfedf', '2025-08-03 12:35:04'),
(6, 'maleeha', 'maleeha@gmail.com', 'your products such a great', '2025-08-03 13:48:53');

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
(0, 7, NULL, 2, 3200.00),
(0, 8, NULL, 1, 29.99);

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
(4, 'AADIL KHAN', 'khaanaadil987654@gmail.com', '+923181215455', 'SEC 2', 'KARACHI', 'Card', '2025-07-30 18:53:36'),
(5, 'yusran', 'yusran@gmail.com', '03245789631', 'block2', 'islamabad', 'Card', '2025-07-31 19:08:16'),
(6, 'AADIL KHAN', 'misfar@gmail.com', '+923181215455', 'ggg', 'KARACHI', 'Cash on Delivery', '2025-08-01 21:02:25'),
(7, 'gggg', 'IMAD@GMAIL.COM', '+923181215455', 'ddd', 'KARACHI', 'Card', '2025-08-01 21:16:51'),
(8, 'swd', 'maleeha@gmail.com', '98765434', 'north karachi', 'karachi', 'Cash on Delivery', '2025-08-03 12:56:52');

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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_id`, `customer_id`, `total_amount`, `status`, `order_date`, `updated_at`, `name`, `email`, `phone`, `address`, `city`, `payment_method`) VALUES
(1, '#AUN001', 1, 125.00, 'cancelled', '2025-07-28 07:15:15', '2025-07-30 17:56:02', NULL, NULL, NULL, NULL, NULL, NULL),
(2, '#AUN002', 2, 250.50, 'cancelled', '2025-07-28 07:15:15', '2025-07-30 17:56:02', NULL, NULL, NULL, NULL, NULL, NULL),
(3, '#AUN003', 3, 75.00, 'cancelled', '2025-07-28 07:15:15', '2025-07-30 17:56:02', NULL, NULL, NULL, NULL, NULL, NULL),
(4, '1', 1, 89.97, 'pending', '2025-07-30 05:00:00', '2025-07-30 18:17:05', NULL, NULL, NULL, NULL, NULL, NULL),
(5, '2', 2, 199.98, 'shipped', '2025-07-29 10:30:00', '2025-07-30 18:17:05', NULL, NULL, NULL, NULL, NULL, NULL),
(6, '3', 1, 49.99, 'cancelled', '2025-07-28 07:00:00', '2025-07-30 18:17:05', NULL, NULL, NULL, NULL, NULL, NULL),
(10, '#AUN007', 22, 419.94, 'pending', '2025-08-04 14:53:42', '2025-08-04 14:53:42', 'maleeha', 'maleeha@gmail.com', '98765434', 'north karachi', 'karachi', 'Cash on Delivery'),
(11, '#AUN008', 22, 0.00, 'pending', '2025-08-04 14:54:42', '2025-08-04 14:54:42', 'maleeha', 'maleeha@gmail.com', '98765434', 'north karachi', 'karachi', 'Cash on Delivery'),
(12, '#AUN009', 22, 0.00, 'pending', '2025-08-04 14:55:25', '2025-08-04 14:55:25', 'maleeha', 'maleeha@gmail.com', '98765434', 'north karachi', 'karachi', 'Cash on Delivery'),
(13, '#AUN010', 22, 0.00, 'pending', '2025-08-04 14:55:50', '2025-08-04 14:55:50', 'maleeha', 'maleeha@gmail.com', '98765434', 'north karachi', 'karachi', 'Cash on Delivery'),
(14, '#AUN011', 22, 0.00, 'pending', '2025-08-04 15:05:10', '2025-08-04 15:05:10', 'maleeha', 'maleeha@gmail.com', '98765434', 'north karachi', 'karachi', 'Cash on Delivery'),
(15, '#AUN012', 22, 0.00, 'pending', '2025-08-04 15:08:21', '2025-08-04 15:08:21', 'maleeha', 'maleeha@gmail.com', '98765434', 'north karachi', 'karachi', 'Cash on Delivery'),
(16, '#AUN013', 22, 0.00, 'pending', '2025-08-04 15:09:05', '2025-08-04 15:09:05', 'maleeha', 'maleeha@gmail.com', '98765434', 'north karachi', 'karachi', 'Cash on Delivery'),
(17, '#AUN014', 22, 0.00, 'pending', '2025-08-04 15:13:39', '2025-08-04 15:13:39', 'maleeha', 'maleeha@gmail.com', '98765434', 'north karachi', 'karachi', 'Cash on Delivery'),
(18, '#AUN015', 22, 0.00, 'pending', '2025-08-04 15:24:10', '2025-08-04 15:24:10', 'maleeha', 'maleeha@gmail.com', '98765434', 'north karachi', 'karachi', 'Cash on Delivery');

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
(6, 3, 1, 1, 49.99),
(7, 10, 2, 3, 89.99),
(8, 10, 1, 3, 49.99);

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
(1, 'Rose Gold Necklace', 'Elegant rose gold necklace with a minimalist pendant.', 49.99, 20, 'lipstick.jpg', 3, 'active', '2025-07-30 13:56:53', '2025-08-02 09:01:55'),
(2, 'Diamond Stud Earrings', 'Sparkling diamond stud earrings for special occasions.', 89.99, 15, 'WhatsApp Image 2025-07-18 at 4.35.14 PM.jpeg', 3, 'active', '2025-07-30 13:56:53', '2025-08-02 09:03:32'),
(3, 'Silver Charm Bracelet', 'Customizable silver bracelet with charm slots.', 39.99, 25, 'makeup img 23.jpg', 3, 'active', '2025-07-30 13:56:53', '2025-08-02 09:04:17'),
(4, 'Pearl Drop Earrings', 'Classic pearl drop earrings for a timeless look.', 59.99, 18, 'aydin-ghadakchi-lamor-1BBlGVr2_Yw-unsplash copy.jpg', 3, 'active', '2025-07-30 13:56:53', '2025-08-02 09:04:56'),
(5, 'Hydrating Face Serum', 'Lightweight serum with hyaluronic acid for hydration.', 29.99, 30, 'cosmetic.jpg', 2, 'active', '2025-07-30 13:56:53', '2025-08-02 09:05:35'),
(6, 'Matte Lipstick', 'Long-lasting matte lipstick in a vibrant coral shade.', 19.99, 40, 'WhatsApp Image 2025-07-18 at 4.28.40 PM.jpeg', 2, 'active', '2025-07-30 13:56:53', '2025-08-02 09:08:44'),
(7, 'Cleansing Balm', 'Gentle cleansing balm to remove makeup and impurities.', 24.99, 35, 'makeup img2 copy.jpg', 2, 'active', '2025-07-30 13:56:53', '2025-08-02 09:09:32'),
(8, 'Moisturizing Cream', 'Rich moisturizer with shea butter for smooth skin.', 34.79, 27, 'serum copy.jpg', 2, 'active', '2025-07-30 13:56:53', '2025-08-02 09:11:03');

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
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `order_id`, `message`, `rating`, `status`, `created_at`, `updated_at`, `image`) VALUES
(1, 1, 1, 'Loved the red matte lipstick! Great quality.', 5, 'approved', '2025-08-03 15:00:00', '2025-08-03 16:45:25', NULL),
(2, 2, 2, 'The necklace is stunning, worth every penny!', 4, 'approved', '2025-08-03 15:15:00', '2025-08-03 16:45:52', NULL),
(3, 3, 3, 'Shipping was slow but product is good.', 3, 'approved', '2025-08-03 15:30:00', '2025-08-03 16:45:41', NULL),
(4, 4, 4, 'Amazing packaging and quality!', 5, 'approved', '2025-08-03 15:45:00', '2025-08-04 13:47:40', NULL);

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
(5, 'Diamond Pendant', 'Jewelry', 5000.00),
(6, 'Rose Gold Necklace', 'Jewelry', 3200.00),
(7, 'Silver Ring', 'Jewelry', 1500.00),
(8, 'Elegant Earrings', 'Jewelry', 2200.00),
(9, 'Golden Bracelet', 'Jewelry', 2700.00),
(10, 'Diamond Pendant', 'Jewelry', 5000.00),
(11, 'Luxe Matte Lipstick', 'Lipstick', 1200.00),
(12, 'Hydrating Foundation', 'Foundation', 2200.00),
(13, 'Silk Finish Powder', 'Powder', 950.00),
(14, 'Volume Lash Mascara', 'Mascara', 850.00),
(15, 'Jet Black Eyeliner', 'Eyeliner', 700.00),
(16, 'Rose Blush Palette', 'Blush', 1350.00),
(17, 'Light Beige Concealer', 'Concealer', 650.00),
(18, 'Coral Lip Crayon', 'Lipstick', 900.00),
(19, 'Dark Brown Eyebrow Pencil', 'Eyebrow', 600.00),
(20, 'Pearl Glow Highlighter', 'Highlighter', 1100.00),
(21, 'Medium BB Cream', 'BB Cream', 1150.00),
(22, 'Nude Shine Lip Gloss', 'Lip Gloss', 750.00),
(23, 'Oil-Free Face Primer', 'Primer', 1300.00),
(24, 'Loose Setting Powder', 'Powder', 1400.00),
(25, 'Midnight Blue Gel Eyeliner', 'Eyeliner', 780.00),
(26, 'Waterproof Mascara Plus', 'Mascara', 980.00),
(27, 'Cherry Tinted Lip Balm', 'Lip Balm', 550.00),
(28, 'Nude Tones Eyeshadow', 'Eyeshadow', 1950.00),
(29, 'Makeup Setting Spray', 'Setting Spray', 1250.00),
(30, 'Warm Beige Foundation', 'Foundation', 2100.00),
(31, 'Classic Red Nail Polish', 'Nail Polish', 300.00),
(32, 'Cuticle Oil Pen', 'Nail Care', 350.00),
(33, 'Peach Glow Blusher', 'Blush', 820.00),
(34, 'Sun Kissed Bronzer', 'Bronzer', 980.00),
(35, 'Wine Matte Lipstick', 'Lipstick', 1250.00),
(36, 'Vitamin C Face Serum', 'Skincare', 1850.00),
(37, 'Charcoal Peel-Off Mask', 'Skincare', 600.00),
(38, 'Aloe Vera Cream', 'Skincare', 950.00),
(39, 'Clear Eyebrow Gel', 'Eyebrow', 560.00),
(40, 'Eye Primer Base', 'Primer', 740.00),
(41, 'Coral Bloom Liquid Blush', 'Blush', 1150.00),
(42, 'Light Tone CC Cream', 'CC Cream', 1050.00),
(43, 'High Coverage Concealer', 'Concealer', 890.00),
(44, 'Intense Black Kajal', 'Kajal', 420.00),
(45, 'Nude Pink Lip Liner', 'Lip Liner', 480.00),
(46, 'Metal Grip Lash Curler', 'Accessories', 450.00),
(47, 'Micellar Makeup Remover', 'Cleanser', 850.00),
(48, 'Smokey Eyeshadow Duo', 'Eyeshadow', 720.00),
(49, 'Nail Remover Pads', 'Nail Care', 300.00),
(50, 'Matte Nail Top Coat', 'Nail Polish', 280.00),
(51, 'Watermelon Lip Tint', 'Lip Tint', 580.00),
(52, 'Hyaluronic Glow Serum', 'Skincare', 1750.00),
(53, 'Rose Water Face Mist', 'Skincare', 700.00),
(54, 'Shimmer Eyeshadow Pot', 'Eyeshadow', 990.00),
(55, 'Coffee Brown Liquid Lipstick', 'Lipstick', 1050.00),
(56, 'Mini Lipstick Set (4)', 'Lipstick', 1800.00),
(57, 'Compact Travel Mirror', 'Accessories', 250.00),
(58, 'Teardrop Makeup Sponge', 'Accessories', 350.00),
(59, 'Medium Contour Stick', 'Contour', 940.00),
(60, 'Crystal Glitter Lip Gloss', 'Lip Gloss', 780.00),
(61, 'Aloe Vera Sheet Mask', 'Skincare', 300.00),
(62, 'Acne Spot Gel', 'Skincare', 1150.00),
(63, 'Witch Hazel Toner', 'Skincare', 950.00),
(64, 'Deep Clean Oil Cleanser', 'Cleanser', 1050.00),
(65, 'Retinol Night Cream', 'Skincare', 1650.00),
(66, 'Rose Gold Necklace', 'Jewelry', 3200.00),
(67, 'Silver Ring', 'Jewelry', 1500.00),
(68, 'Elegant Earrings', 'Jewelry', 2200.00),
(69, 'Golden Bracelet', 'Jewelry', 2700.00),
(70, 'Diamond Pendant', 'Jewelry', 5000.00),
(71, 'Luxe Matte Lipstick', 'Lipstick', 1200.00),
(72, 'Hydrating Foundation', 'Foundation', 2200.00),
(73, 'Silk Finish Powder', 'Powder', 950.00),
(74, 'Volume Lash Mascara', 'Mascara', 850.00),
(75, 'Jet Black Eyeliner', 'Eyeliner', 700.00),
(76, 'Rose Blush Palette', 'Blush', 1350.00),
(77, 'Light Beige Concealer', 'Concealer', 650.00),
(78, 'Coral Lip Crayon', 'Lipstick', 900.00),
(79, 'Dark Brown Eyebrow Pencil', 'Eyebrow', 600.00),
(80, 'Pearl Glow Highlighter', 'Highlighter', 1100.00),
(81, 'Medium BB Cream', 'BB Cream', 1150.00),
(82, 'Nude Shine Lip Gloss', 'Lip Gloss', 750.00),
(83, 'Oil-Free Face Primer', 'Primer', 1300.00),
(84, 'Loose Setting Powder', 'Powder', 1400.00),
(85, 'Midnight Blue Gel Eyeliner', 'Eyeliner', 780.00),
(86, 'Waterproof Mascara Plus', 'Mascara', 980.00),
(87, 'Cherry Tinted Lip Balm', 'Lip Balm', 550.00),
(88, 'Nude Tones Eyeshadow', 'Eyeshadow', 1950.00),
(89, 'Makeup Setting Spray', 'Setting Spray', 1250.00),
(90, 'Warm Beige Foundation', 'Foundation', 2100.00),
(91, 'Classic Red Nail Polish', 'Nail Polish', 300.00),
(92, 'Cuticle Oil Pen', 'Nail Care', 350.00),
(93, 'Peach Glow Blusher', 'Blush', 820.00),
(94, 'Sun Kissed Bronzer', 'Bronzer', 980.00),
(95, 'Wine Matte Lipstick', 'Lipstick', 1250.00),
(96, 'Vitamin C Face Serum', 'Skincare', 1850.00),
(97, 'Charcoal Peel-Off Mask', 'Skincare', 600.00),
(98, 'Aloe Vera Cream', 'Skincare', 950.00),
(99, 'Clear Eyebrow Gel', 'Eyebrow', 560.00),
(100, 'Eye Primer Base', 'Primer', 740.00),
(101, 'Coral Bloom Liquid Blush', 'Blush', 1150.00),
(102, 'Light Tone CC Cream', 'CC Cream', 1050.00),
(103, 'High Coverage Concealer', 'Concealer', 890.00),
(104, 'Intense Black Kajal', 'Kajal', 420.00),
(105, 'Nude Pink Lip Liner', 'Lip Liner', 480.00),
(106, 'Metal Grip Lash Curler', 'Accessories', 450.00),
(107, 'Micellar Makeup Remover', 'Cleanser', 850.00),
(108, 'Smokey Eyeshadow Duo', 'Eyeshadow', 720.00),
(109, 'Nail Remover Pads', 'Nail Care', 300.00),
(110, 'Matte Nail Top Coat', 'Nail Polish', 280.00),
(111, 'Watermelon Lip Tint', 'Lip Tint', 580.00),
(112, 'Hyaluronic Glow Serum', 'Skincare', 1750.00),
(113, 'Rose Water Face Mist', 'Skincare', 700.00),
(114, 'Shimmer Eyeshadow Pot', 'Eyeshadow', 990.00),
(115, 'Coffee Brown Liquid Lipstick', 'Lipstick', 1050.00),
(116, 'Mini Lipstick Set (4)', 'Lipstick', 1800.00),
(117, 'Compact Travel Mirror', 'Accessories', 250.00),
(118, 'Teardrop Makeup Sponge', 'Accessories', 350.00),
(119, 'Medium Contour Stick', 'Contour', 940.00),
(120, 'Crystal Glitter Lip Gloss', 'Lip Gloss', 780.00),
(121, 'Aloe Vera Sheet Mask', 'Skincare', 300.00),
(122, 'Acne Spot Gel', 'Skincare', 1150.00),
(123, 'Witch Hazel Toner', 'Skincare', 950.00),
(124, 'Deep Clean Oil Cleanser', 'Cleanser', 1050.00),
(125, 'Retinol Night Cream', 'Skincare', 1650.00),
(126, 'Rose Gold Necklace', 'Jewelry', 3200.00),
(127, 'Silver Ring', 'Jewelry', 1500.00),
(128, 'Elegant Earrings', 'Jewelry', 2200.00),
(129, 'Golden Bracelet', 'Jewelry', 2700.00),
(130, 'Diamond Pendant', 'Jewelry', 5000.00),
(131, 'Luxe Matte Lipstick', 'Lipstick', 1200.00),
(132, 'Hydrating Foundation', 'Foundation', 2200.00),
(133, 'Silk Finish Powder', 'Powder', 950.00),
(134, 'Volume Lash Mascara', 'Mascara', 850.00),
(135, 'Jet Black Eyeliner', 'Eyeliner', 700.00),
(136, 'Rose Blush Palette', 'Blush', 1350.00),
(137, 'Light Beige Concealer', 'Concealer', 650.00),
(138, 'Coral Lip Crayon', 'Lipstick', 900.00),
(139, 'Dark Brown Eyebrow Pencil', 'Eyebrow', 600.00),
(140, 'Pearl Glow Highlighter', 'Highlighter', 1100.00),
(141, 'Medium BB Cream', 'BB Cream', 1150.00),
(142, 'Nude Shine Lip Gloss', 'Lip Gloss', 750.00),
(143, 'Oil-Free Face Primer', 'Primer', 1300.00),
(144, 'Loose Setting Powder', 'Powder', 1400.00),
(145, 'Midnight Blue Gel Eyeliner', 'Eyeliner', 780.00),
(146, 'Waterproof Mascara Plus', 'Mascara', 980.00),
(147, 'Cherry Tinted Lip Balm', 'Lip Balm', 550.00),
(148, 'Nude Tones Eyeshadow', 'Eyeshadow', 1950.00),
(149, 'Makeup Setting Spray', 'Setting Spray', 1250.00),
(150, 'Warm Beige Foundation', 'Foundation', 2100.00),
(151, 'Classic Red Nail Polish', 'Nail Polish', 300.00),
(152, 'Cuticle Oil Pen', 'Nail Care', 350.00),
(153, 'Peach Glow Blusher', 'Blush', 820.00),
(154, 'Sun Kissed Bronzer', 'Bronzer', 980.00),
(155, 'Wine Matte Lipstick', 'Lipstick', 1250.00),
(156, 'Vitamin C Face Serum', 'Skincare', 1850.00),
(157, 'Charcoal Peel-Off Mask', 'Skincare', 600.00),
(158, 'Aloe Vera Cream', 'Skincare', 950.00),
(159, 'Clear Eyebrow Gel', 'Eyebrow', 560.00),
(160, 'Eye Primer Base', 'Primer', 740.00),
(161, 'Coral Bloom Liquid Blush', 'Blush', 1150.00),
(162, 'Light Tone CC Cream', 'CC Cream', 1050.00),
(163, 'High Coverage Concealer', 'Concealer', 890.00),
(164, 'Intense Black Kajal', 'Kajal', 420.00),
(165, 'Nude Pink Lip Liner', 'Lip Liner', 480.00),
(166, 'Metal Grip Lash Curler', 'Accessories', 450.00),
(167, 'Micellar Makeup Remover', 'Cleanser', 850.00),
(168, 'Smokey Eyeshadow Duo', 'Eyeshadow', 720.00),
(169, 'Nail Remover Pads', 'Nail Care', 300.00),
(170, 'Matte Nail Top Coat', 'Nail Polish', 280.00),
(171, 'Watermelon Lip Tint', 'Lip Tint', 580.00),
(172, 'Hyaluronic Glow Serum', 'Skincare', 1750.00),
(173, 'Rose Water Face Mist', 'Skincare', 700.00),
(174, 'Shimmer Eyeshadow Pot', 'Eyeshadow', 990.00),
(175, 'Coffee Brown Liquid Lipstick', 'Lipstick', 1050.00),
(176, 'Mini Lipstick Set (4)', 'Lipstick', 1800.00),
(177, 'Compact Travel Mirror', 'Accessories', 250.00),
(178, 'Teardrop Makeup Sponge', 'Accessories', 350.00),
(179, 'Medium Contour Stick', 'Contour', 940.00),
(180, 'Crystal Glitter Lip Gloss', 'Lip Gloss', 780.00),
(181, 'Aloe Vera Sheet Mask', 'Skincare', 300.00),
(182, 'Acne Spot Gel', 'Skincare', 1150.00),
(183, 'Witch Hazel Toner', 'Skincare', 950.00),
(184, 'Deep Clean Oil Cleanser', 'Cleanser', 1050.00),
(185, 'Retinol Night Cream', 'Skincare', 1650.00);

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

-- --------------------------------------------------------

--
-- Table structure for table `wallet`
--

CREATE TABLE `wallet` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `points` int(11) NOT NULL DEFAULT 0,
  `last_checkin_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wallet`
--

INSERT INTO `wallet` (`id`, `user_id`, `balance`, `points`, `last_checkin_date`, `created_at`, `updated_at`) VALUES
(1, 1, 50.00, 100, NULL, '2025-08-03 17:13:03', '2025-08-03 17:13:03'),
(2, 2, 20.00, 50, NULL, '2025-08-03 17:13:03', '2025-08-03 17:13:03');

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
-- Indexes for table `cart`
--
ALTER TABLE `cart`
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
-- Indexes for table `cosmetics_products`
--
ALTER TABLE `cosmetics_products`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `feedback1`
--
ALTER TABLE `feedback1`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `maintenance_logs`
--
ALTER TABLE `maintenance_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `moon`
--
ALTER TABLE `moon`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `search`
--
ALTER TABLE `search`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `wallet`
--
ALTER TABLE `wallet`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

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
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

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
-- AUTO_INCREMENT for table `cosmetics_products`
--
ALTER TABLE `cosmetics_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `feedback1`
--
ALTER TABLE `feedback1`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `maintenance_logs`
--
ALTER TABLE `maintenance_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `moon`
--
ALTER TABLE `moon`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `search`
--
ALTER TABLE `search`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=186;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `wallet`
--
ALTER TABLE `wallet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wallet`
--
ALTER TABLE `wallet`
  ADD CONSTRAINT `wallet_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
