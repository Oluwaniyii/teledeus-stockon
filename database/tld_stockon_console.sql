-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 08, 2021 at 08:36 AM
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
  `phone` varchar(30) NOT NULL,
  `password` text NOT NULL,
  `meta` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `unique_id`, `firstname`, `lastname`, `email`, `phone`, `password`, `meta`) VALUES
(1, 'uid6185aa43a9d03', 'John', 'Doe', 'johndoe@gmail.com', '+234-111-222-3333', '$2y$10$CAUboVlbDqmBz7GBh3oboO4O1JW.urTpE1UaacJ8BiyqubtALtVq2', '2021-11-05 22:03:47'),
(2, 'uid6185ab322b1b0', 'Jane', 'Smith', 'janeysmith@gmail.com', '+1-222-333-4444', '$2y$10$v8U1jHiXu5Qe63I1OlElJuMuAe01QMpXPaKtzZ.KhAKdQmt5YaRVq', '2021-11-05 22:07:46');

-- --------------------------------------------------------

--
-- Table structure for table `apps`
--

CREATE TABLE `apps` (
  `id` int(11) NOT NULL,
  `unique_id` varchar(40) NOT NULL,
  `account_id` varchar(150) NOT NULL,
  `client_id` text NOT NULL,
  `client_secret` text NOT NULL,
  `app_name` varchar(300) NOT NULL,
  `app_description` text NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `apps`
--

INSERT INTO `apps` (`id`, `unique_id`, `account_id`, `client_id`, `client_secret`, `app_name`, `app_description`, `created`) VALUES
(16, '6186fc34543d5', 'uid6185aa43a9d03', '684f49b39b62e4f66bab34c58c2d0f18', 'tKNIH3Epl2hQHzCo05Nkj3kMiTFIY7J5fdiVJtIfND3fEzABhIg24yYztQtrJB4q', 'Airtime_buy', 'airtime seller', '2021-11-06 22:05:40'),
(18, '61879039e073b', 'uid6185aa43a9d03', '8bdbc994871f503371edab59170233a7', 'YfOpk1mJZJLXVsj482fg7lOonzvyRzrFvtCI48DpSPLrMGZGNUT4Klu6qQDaVoqM', 'Airtime_loan', 'Airtime loan grant app', '2021-11-07 08:37:13');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `apps`
--
ALTER TABLE `apps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
