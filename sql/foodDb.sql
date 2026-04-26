-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 26, 2026 at 03:42 PM
-- Server version: 8.4.6-6
-- PHP Version: 8.2.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `foodDB.sql`
--

-- --------------------------------------------------------

--
-- Table structure for table `dborganizations`
--

CREATE TABLE `dborganizations` (
  `id` int NOT NULL,
  `name` varchar(256) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(256) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `location` varchar(256) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `archived` tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbusers`
--

CREATE TABLE `dbusers` (
  `id` varchar(256) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'id should be user''s username unique',
  `start_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `first_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` text COLLATE utf8mb4_general_ci NOT NULL,
  `role` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `semester` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `archived` tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbvolunteeractivity`
--

CREATE TABLE `dbvolunteeractivity` (
  `id` int NOT NULL,
  `date` date NOT NULL,
  `volunteerID` varchar(256) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'referenced dbusers id (aka username)',
  `hours` float NOT NULL,
  `poundsOfFood` float DEFAULT NULL,
  `organizationID` int NOT NULL,
  `location` varchar(1000) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `archived` tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dborganizations`
--
ALTER TABLE `dborganizations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `dbusers`
--
ALTER TABLE `dbusers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `email_2` (`email`);

--
-- Indexes for table `dbvolunteeractivity`
--
ALTER TABLE `dbvolunteeractivity`
  ADD PRIMARY KEY (`id`),
  ADD KEY `valid_volunteerID` (`volunteerID`),
  ADD KEY `valid_organizationID` (`organizationID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dborganizations`
--
ALTER TABLE `dborganizations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dbvolunteeractivity`
--
ALTER TABLE `dbvolunteeractivity`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dbvolunteeractivity`
--
ALTER TABLE `dbvolunteeractivity`
  ADD CONSTRAINT `valid_organizationID` FOREIGN KEY (`organizationID`) REFERENCES `dborganizations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `valid_volunteerID` FOREIGN KEY (`volunteerID`) REFERENCES `dbusers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
