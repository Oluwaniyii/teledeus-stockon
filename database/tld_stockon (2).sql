-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 07, 2022 at 08:25 PM
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
-- Table structure for table `auth_token`
--

CREATE TABLE `auth_token` (
  `id` int(11) NOT NULL,
  `email` varchar(65) NOT NULL,
  `password` text NOT NULL,
  `selector` text NOT NULL,
  `is_expired` int(1) NOT NULL DEFAULT 0,
  `expiry` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
  `address_building` varchar(255) DEFAULT NULL,
  `address_city` varchar(255) DEFAULT NULL,
  `address_state` varchar(255) DEFAULT NULL,
  `address_zipcode` varchar(20) DEFAULT NULL,
  `joined` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `unique_id`, `username`, `email`, `phone`, `password`, `address_building`, `address_city`, `address_state`, `address_zipcode`, `joined`) VALUES
(77, 'uid61a8939c4f372', 'Oluwaniyii', 'ayodele@gmail.com', '07035100280', '$2y$10$dfzTrCB/Dvo4OWNtZdmsz.PZgcbPi/GPXbbbIP5ZS9gL63kRyig8K', '13, Oluwaseyi', 'iyana-ipaja', 'Lagos', '10011', '2021-12-02 10:36:28'),
(100, 'uid61cc1d156bd6f', 'Ayodeley', 'ayodeleyniyii@gmail.com', '09074237582', '$2y$10$DzA/BsvdTYKmbNt5AB5ZJenm29C9wPhJUHoWbykIolKSNBK0SCjtG', NULL, 'iyana-ipaja', 'Lagos', '10111', '2021-12-29 02:32:21'),
(101, 'uid61ced26f3f6cf', 'Dakolo', 'dakolo@gmail.com', '07035100280', '$2y$10$r5He7szRc8VPBPfdpGN6L.cFzdlhYAvz8/OOnmIKR670DdvOmHXKe', '13, Oluwaseyi', 'iyana-ipaja', 'Lagos', '10111', '2021-12-31 10:50:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `auth_token`
--
ALTER TABLE `auth_token`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `auth_token`
--
ALTER TABLE `auth_token`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
