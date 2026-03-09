-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 07, 2026 at 05:12 PM
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
-- Table structure for table `dborganizations`
--

CREATE TABLE `dborganizations` (
  `id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `email` varchar(256) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(256) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dborganizations`
--

INSERT INTO `dborganizations` (`id`, `name`, `email`, `description`, `location`) VALUES
(1, 'Volunteer Place', NULL, NULL, NULL),
(2, 'Another Org', 'another.org@email.com', 'Information about sbkhfasn stuff skhfns asjbdh ahdnia awehdi  godj vzihu fauhfaf aifn.', '18993 place dr\r\nsomewhere world 32413');

-- --------------------------------------------------------

--
-- Table structure for table `dbusers`
--


-- has username and password attributes as remnants from the previous code Persons schema.  
-- Left them there in case important.  If they don't get used we can remove them.
CREATE TABLE `dbusers` (
  `id` int(11) NOT NULL,
  `username` varchar(256) NOT NULL,
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

INSERT INTO `dbusers` (`id`, `username`, `start_date`, `first_name`, `last_name`, `email`, `password`, `role`, `semester`) VALUES
(1, 'blue', '2026-03-05', 'Laura', 'Rider', 'lr@email.com', 'blue', 'Student', 'Spring 2026'),
(2, 'green', '2026-03-05', 'Claire', 'Davis', 'cd@email.com', 'green', 'Student', 'Fall 2019'),
(3, 'purple', '2026-03-05', 'Mustafa', 'Adnan', 'ma@email.com', 'purple', 'Student', 'Fall 2025'),
(4, 'orange', '2026-03-05', 'Joshua', 'LaMoy', 'jl@email.com', 'orange', 'Student', 'Spring 2026'),
(5, 'red', '2026-03-05', 'Timothy', 'Moore', 'tm@email.com', 'red', 'Student', 'Fall 2019'),
(6, 'yellow', '2026-03-05', 'Erick', 'Niyonkuru', 'en@email.com', 'yellow', 'Student', 'Fall 2024');

-- --------------------------------------------------------

--
-- Table structure for table `dbvolunteeractivity`
--

CREATE TABLE `dbvolunteeractivity` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `volunteerID` int(11) NOT NULL,
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
(3, '2026-03-16', 3, 3, 2, 1, NULL, NULL),
(4, '2026-03-15', 5, 4, 2, 1, '389 place st\r\nsometwhere 123123', 'we did stuff'),
(5, '0000-00-00', 3, 3, 3, 2, '19393 somewhere\r\nanother place 32488', 'we did more stuff');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dborganizations`
--
ALTER TABLE `dborganizations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dbusers`
--
ALTER TABLE `dbusers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

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
-- AUTO_INCREMENT for table `dborganizations`
--
ALTER TABLE `dborganizations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `dbusers`
--
ALTER TABLE `dbusers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `dbvolunteeractivity`
--
ALTER TABLE `dbvolunteeractivity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dbvolunteeractivity`
--
ALTER TABLE `dbvolunteeractivity`
  ADD CONSTRAINT `valid organization foreign key` FOREIGN KEY (`organizationID`) REFERENCES `dborganizations` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `valid user foreign key` FOREIGN KEY (`volunteerID`) REFERENCES `dbusers` (`id`) ON UPDATE CASCADE;
COMMIT;
