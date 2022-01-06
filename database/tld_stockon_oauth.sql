-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 29, 2021 at 08:14 PM
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
-- Database: `tld_stockon_oauth`
--

-- --------------------------------------------------------

--
-- Table structure for table `access_token`
--

CREATE TABLE `access_token` (
  `id` int(11) NOT NULL,
  `token_string` varchar(60) NOT NULL,
  `client_id` varchar(100) NOT NULL,
  `user_identity` varchar(60) NOT NULL,
  `issued_at` varchar(60) NOT NULL,
  `expiration_time` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `token_code`
--

CREATE TABLE `token_code` (
  `id` int(11) NOT NULL,
  `code_string` varchar(60) NOT NULL,
  `client_id` varchar(60) NOT NULL,
  `client_redirect_url` varchar(100) NOT NULL,
  `user_identity` varchar(100) NOT NULL,
  `issued_at` varchar(60) NOT NULL,
  `expiration_time` varchar(30) NOT NULL,
  `meta` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `token_code`
--

INSERT INTO `token_code` (`id`, `code_string`, `client_id`, `client_redirect_url`, `user_identity`, `issued_at`, `expiration_time`, `meta`) VALUES
(28, 'CDT61ccb2e9303a7nYuPB8W5nR8KcgpwpRUUkUNJG', '41aca2fd4df8cb6c9e9d781e55c31e80', 'http://artimeloan.com/auth', 'ayodeleyniyii@gmail.com', '1640805097', '120', '2021-12-29 13:11:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `access_token`
--
ALTER TABLE `access_token`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `token_code`
--
ALTER TABLE `token_code`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `scope` (`expiration_time`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `access_token`
--
ALTER TABLE `access_token`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `token_code`
--
ALTER TABLE `token_code`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
