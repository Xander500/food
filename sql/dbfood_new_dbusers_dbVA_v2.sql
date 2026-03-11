-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 11, 2026 at 11:56 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `fooddb`
--

-- --------------------------------------------------------

--
-- Table structure for table `dbusers`
--

CREATE TABLE `dbusers` (
  `id` varchar(256) NOT NULL COMMENT 'id should be user''s username unique',
  `start_date` date NOT NULL DEFAULT current_timestamp(),
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` text NOT NULL,
  `role` varchar(20) NOT NULL,
  `semester` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dbusers`
--

INSERT INTO `dbusers` (`id`, `start_date`, `first_name`, `last_name`, `email`, `password`, `role`, `semester`) VALUES
('4', '2026-03-11', 'Joshua', 'LaMoy', 'jlamoy@mail.umw.edu', 'password', 'Student', 'Spring 2025'),
('madnan', '2026-03-11', 'Mustafa', 'Adnan', 'madnan@mail.umw.edu', 'password', 'Student', 'Spring 2025');

-- --------------------------------------------------------

--
-- Table structure for table `dbvolunteeractivity`
--

CREATE TABLE `dbvolunteeractivity` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `volunteerID` varchar(256) NOT NULL COMMENT 'referenced dbusers id (aka username)',
  `hours` float NOT NULL,
  `poundsOfFood` float DEFAULT NULL,
  `organizationID` int(11) NOT NULL,
  `location` varchar(1000) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dbvolunteeractivity`
--

INSERT INTO `dbvolunteeractivity` (`id`, `date`, `volunteerID`, `hours`, `poundsOfFood`, `organizationID`, `location`, `description`) VALUES
(7, '2026-03-02', 'madnan', 2, 3, 1, 'somewhere', 'something'),
(9, '2026-03-01', '4', 4, 2, 1, 'place', 'test');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dbusers`
--
ALTER TABLE `dbusers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dbvolunteeractivity`
--
ALTER TABLE `dbvolunteeractivity`
  ADD PRIMARY KEY (`id`),
  ADD KEY `valid user foreign key` (`volunteerID`),
  ADD KEY `valid organization foreign key` (`organizationID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dbvolunteeractivity`
--
ALTER TABLE `dbvolunteeractivity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dbvolunteeractivity`
--
ALTER TABLE `dbvolunteeractivity`
  ADD CONSTRAINT `valid organization foreign key` FOREIGN KEY (`organizationID`) REFERENCES `dborganizations` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `valid volunteerID foreign key` FOREIGN KEY (`volunteerID`) REFERENCES `dbusers` (`id`) ON UPDATE CASCADE;
COMMIT;
