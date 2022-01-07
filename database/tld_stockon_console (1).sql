-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 07, 2022 at 08:26 PM
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
-- Database: `tld_stockon_console`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `unique_id` varchar(30) NOT NULL,
  `firstname` varchar(30) NOT NULL,
  `lastname` varchar(30) NOT NULL,
  `email` varchar(60) NOT NULL,
  `password` text NOT NULL,
  `meta` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `unique_id`, `firstname`, `lastname`, `email`, `password`, `meta`) VALUES
(9, 'uid61d31206cf94d', 'Oluwaniyii', 'Ayodele', 'ayodeleyniyii@gmail.com', '$2y$10$y9IKwe9WhJPBpRp3/e9rd.BjUrKuRaHpfpnEFlCP7RefVcmfpbrOS', '2022-01-03 15:11:03'),
(12, 'uid61d31d6b701ce', 'Damilola', 'Ayodele', 'Dakolo@gmail.com', '$2y$10$y9IKwe9WhJPBpRp3/e9rd.BjUrKuRaHpfpnEFlCP7RefVcmfpbrOS', '2022-01-03 15:59:39');

-- --------------------------------------------------------

--
-- Table structure for table `apps`
--

CREATE TABLE `apps` (
  `id` int(11) NOT NULL,
  `unique_id` varchar(40) NOT NULL,
  `account_id` varchar(150) NOT NULL,
  `client_id` text NOT NULL,
  `app_type` varchar(30) NOT NULL,
  `client_secret` text NOT NULL,
  `app_name` varchar(300) NOT NULL,
  `app_description` text NOT NULL,
  `success_redirect_url` varchar(150) NOT NULL,
  `error_redirect_url` varchar(150) NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `apps`
--

INSERT INTO `apps` (`id`, `unique_id`, `account_id`, `client_id`, `app_type`, `client_secret`, `app_name`, `app_description`, `success_redirect_url`, `error_redirect_url`, `created`) VALUES
(25, '619b42257bd10', 'uid6185aa43a9d03', '41aca2fd4df8cb6c9e9d781e55c31e80', 'confidential', 'wltFdlRhDZKNtsFINElvZVxo8MryJriOxVxr1XUxvbzAYeQWK8axIPSfS0ib7L7M', 'Airtimeloan', 'Airtime loan grant app', 'http://artimeloan.com/auth', 'http://artimeloan.com/auth/error', '2021-11-22 07:09:25'),
(32, '61d4a82f02278', 'uid61d31d6b701ce', '10fc74795aa00b636ffe084c5508754e', 'authorization_code_grant', 'Gd54zv43JfTxW1eqOk35PfTIGMFtgFPteEh4KvRVdRGoesI5rZYpmLrLBl3oZ3fo', 'Airspend Tracker', 'An app to track airtime recharge and data usage', 'http://airspend.com/auth', 'http://airspend.com/auth/error', '2022-01-04 20:03:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `apps`
--
ALTER TABLE `apps`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `apps`
--
ALTER TABLE `apps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
