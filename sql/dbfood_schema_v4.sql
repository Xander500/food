-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 05, 2026 at 05:27 AM
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
-- Database: `fooddb`
--

-- --------------------------------------------------------

--
-- Table structure for table `dborganizations`
--

CREATE TABLE `dborganizations` (
  `id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `email` varchar(256) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(256) DEFAULT NULL,
  `archived` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dborganizations`
--

INSERT INTO `dborganizations` (`id`, `name`, `email`, `description`, `location`, `archived`) VALUES
(1, 'Volunteer Place', NULL, NULL, NULL, 0),
(2, 'Another Org', 'another.org@email.com', 'Information about sbkhfasn stuff skhfns asjbdh ahdnia awehdi  godj vzihu fauhfaf aifn.', '18993 place dr\r\nsomewhere world 32413', 0);

-- --------------------------------------------------------

--
-- Table structure for table `dbusers`
--

CREATE TABLE `dbusers` (
  `id` varchar(256) NOT NULL COMMENT 'id should be user''s username unique',
  `start_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` text NOT NULL,
  `role` varchar(20) NOT NULL,
  `semester` varchar(20) NOT NULL,
  `archived` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dbusers`
--

INSERT INTO `dbusers` (`id`, `start_date`, `first_name`, `last_name`, `email`, `password`, `role`, `semester`, `archived`) VALUES
('4', '2026-03-11 00:00:00', 'Joshua', 'LaMoy', 'jlamoy@mail.umw.edu', 'password', 'Student', 'Spring 2025', 0),
('joesmoe', '2026-03-15 00:00:00', 'joe', 'smoe', 'joesmoe24@bleh.com', '$2y$10$aCvYp93s6MwvU/PKjdIRp.z27mKUlHfEnnSutnMrXVO2hdxt4roiu', 'Student', 'Spring 2026', 0),
('johnSmith123', '2026-04-02 04:00:00', 'john', 'smith', 'johnsmith@test.com', '$2y$10$Fjb9tt3RlnPsaDjlQLpg/uKRPoVqEGNYQ26azoXxS/VV3bWhDIxtm', 'Instructor', 'Spring 2026', 0),
('madnan', '2026-03-11 00:00:00', 'Mustafa', 'Adnan', 'madnan@mail.umw.edu', 'password', 'Student', 'Spring 2025', 0),
('tmoore8', '2026-03-25 00:00:00', 'Timothy', 'Moore', 'tmoore8@gmail.com', '$2y$10$P.VFhPAKpUVzLIp44Ilt1Odvu6lOowqAUiWTyU4cFepO1PHqTncBa', 'Student', 'Spring 2026', 0),
('vmsroot', '2026-03-15 00:00:00', 'vmsroot', 'vmsroot', '', '$2y$10$.3p8xvmUqmxNztEzMJQRBesLDwdiRU3xnt/HOcJtsglwsbUk88VTO', '', '', 0);

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
  `description` text DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `archived` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dbvolunteeractivity`
--

INSERT INTO `dbvolunteeractivity` (`id`, `date`, `volunteerID`, `hours`, `poundsOfFood`, `organizationID`, `location`, `description`, `latitude`, `longitude`, `archived`) VALUES
(7, '2026-03-02', 'joesmoe', 2, 3, 1, 'somewhere', 'something', NULL, NULL, 0),
(9, '2026-03-01', 'joesmoe', 4, 2, 1, 'place', 'test', NULL, NULL, 0),
(12, '2026-03-25', 'tmoore8', 2, 3, 1, 'Fredericksburg', 'Class', NULL, NULL, 0),
(14, '2026-03-30', 'tmoore8', 5, 1, 2, 'King George', '', NULL, NULL, 0),
(15, '2026-04-02', 'joesmoe', 5, 2, 2, 'King George', 'Testing date', NULL, NULL, 0),
(16, '2026-04-04', 'johnSmith123', 6, 14.4, 1, 'Fredericksburg', 'Sample log', NULL, NULL, 0);

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
-- AUTO_INCREMENT for table `dbvolunteeractivity`
--
ALTER TABLE `dbvolunteeractivity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

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
