-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 31, 2021 at 09:38 AM
-- Server version: 10.4.6-MariaDB
-- PHP Version: 7.3.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tld_stockon`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `unique_id` varchar(100) NOT NULL,
  `username` varchar(30) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` text NOT NULL,
  `address_building` varchar(255) NOT NULL,
  `address_city` varchar(255) NOT NULL,
  `address_state` varchar(255) NOT NULL,
  `address_zipcode` varchar(255) NOT NULL,
  `joined` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `unique_id`, `username`, `email`, `phone`, `password`, `address_building`, `address_city`, `address_state`, `address_zipcode`, `joined`) VALUES
(48, 'uid61765588c93ff', 'Tom Felton', 'tomfelton@gmail.com', '+234-111-222-3334', '$2y$10$GS50hcLx9QyBCNyGL3ZOYeSgFN9T2xkZsmkvtvdhNQZTY8ksvA9QC', '39,B5', 'gowon,isolo', 'Lagos', '10011', '2021-10-25 07:58:16'),
(71, 'uid617d80fee42f5', 'Chris Joe', 'chrisjoe@gmail.com', '+1-100-321-3456', '$2y$10$bNRMY3f5KGObcPJN5mrLwuVGctUySoqHAmlf0V0Awm/IhjGBj.Ji6', 'aberleigh', 'campbridge', 'london', '11111', '2021-10-30 18:29:35'),
(74, 'uid617dca91ac497', 'Barry Allen', 'barry@gmail.com', '+1-100-321-3456', '$2y$10$9z/0IpKt1tgbKnuSAsjD7ejvM/gFdIHJuKdWY33oBvmfZaZeszr1y', 'aberleigh', 'campbridge', 'london', '11111', '2021-10-30 23:43:29');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
