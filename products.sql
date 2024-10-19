-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 19, 2024 at 07:42 AM
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
-- Database: `boutique_shop`
--

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image_url`, `category`) VALUES
(22, 'T-shirt', 'A comfortable cotton T-shirt', 19.99, 'https://example.com/images/tshirt.jpg', 'Clothing'),
(23, 'Jeans', 'Stylish blue denim jeans', 49.99, 'https://example.com/images/jeans.jpg', 'Clothing'),
(24, 'Sneakers', 'Casual sneakers with rubber sole', 59.99, 'https://example.com/images/sneakers.jpg', 'Footwear'),
(25, 'Wristwatch', 'Elegant wristwatch with leather strap', 129.99, 'https://example.com/images/watch.jpg', 'Accessories'),
(26, 'Sunglasses', 'UV protection polarized sunglasses', 39.99, 'https://example.com/images/sunglasses.jpg', 'Accessories'),
(28, 'Laptop', 'Lightweight laptop with 16GB RAM', 999.99, 'https://example.com/images/laptop.jpg', 'Electronics'),
(29, 'Smartphone', '5G-enabled smartphone with 128GB storage', 799.99, 'https://example.com/images/smartphone.jpg', 'Electronics');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
