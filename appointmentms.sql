-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 07, 2025 at 11:33 AM
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
-- Database: `appointmentms`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact` varchar(15) NOT NULL,
  `password` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `name`, `email`, `contact`, `password`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@gmail.com', '9876543210', '$2y$10$XV0veQ61n2QktVEtZicWX.CoaAPUXoWQvljP3bYb.sFlsmLY4tSZO', '2025-01-09 07:19:17', '2025-01-31 13:03:19');

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE `appointment` (
  `a_id` int(200) NOT NULL,
  `p_id` int(200) NOT NULL,
  `d_id` int(200) NOT NULL,
  `date` date NOT NULL,
  `shift` enum('Morning','Evening') NOT NULL,
  `status` enum('Confirmed','Pending','Cancelled') NOT NULL DEFAULT 'Pending',
  `create_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `rescheduled` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointment`
--

INSERT INTO `appointment` (`a_id`, `p_id`, `d_id`, `date`, `shift`, `status`, `create_at`, `rescheduled`) VALUES
(100, 101, 101, '2025-02-20', 'Morning', 'Pending', '2025-02-07 10:28:51', 0),
(101, 101, 103, '2025-02-25', 'Evening', 'Pending', '2025-02-07 10:29:03', 0),
(102, 101, 107, '2025-02-21', 'Evening', 'Confirmed', '2025-02-07 10:29:15', 0);

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `d_id` int(200) NOT NULL,
  `name` varchar(100) NOT NULL,
  `specialize` varchar(100) NOT NULL,
  `m_start` time NOT NULL,
  `m_end` time NOT NULL,
  `e_start` time NOT NULL,
  `e_end` time NOT NULL,
  `fees` int(200) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`d_id`, `name`, `specialize`, `m_start`, `m_end`, `e_start`, `e_end`, `fees`, `created_at`, `updated_at`, `status`) VALUES
(100, 'Ritu Pandey', 'Psychiatrist', '10:00:00', '12:00:00', '08:00:00', '11:00:00', 500, '2025-02-01 06:40:26', '2025-02-01 06:40:26', 'Active'),
(101, 'Aditya', 'Pediatrician', '10:00:00', '02:00:00', '00:00:00', '00:00:00', 300, '2025-02-01 06:44:08', '2025-02-01 06:44:08', 'Active'),
(102, 'Manjesh', 'General Physician', '09:00:00', '03:00:00', '06:00:00', '12:00:00', 600, '2025-02-01 06:46:37', '2025-02-01 06:46:37', 'Inactive'),
(103, 'Keshav', 'Cardiologist', '09:00:00', '03:00:00', '06:00:00', '12:00:00', 450, '2025-02-01 06:47:41', '2025-02-01 06:47:41', 'Active'),
(104, 'Pranab', 'Cardiologist', '09:00:00', '03:00:00', '06:00:00', '12:00:00', 450, '2025-02-01 06:47:58', '2025-02-01 06:47:58', 'Active'),
(105, 'Ritesh', 'Dermatologist', '09:00:00', '03:00:00', '06:00:00', '12:00:00', 500, '2025-02-01 06:49:55', '2025-02-01 06:49:55', 'Active'),
(106, 'sweety', 'Ophthalmologist', '09:00:00', '03:00:00', '06:00:00', '12:00:00', 300, '2025-02-01 06:53:21', '2025-02-01 06:53:21', 'Active'),
(107, 'Dhruv', 'Neurologist', '00:00:00', '00:00:00', '20:30:00', '23:30:00', 500, '2025-02-02 13:55:37', '2025-02-02 13:55:37', 'Active'),
(108, 'Sourabh', 'Nephrologist', '00:00:00', '00:00:00', '10:30:00', '13:30:00', 600, '2025-02-02 13:57:09', '2025-02-02 13:57:09', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE `patient` (
  `p_id` int(200) NOT NULL,
  `name` varchar(100) NOT NULL,
  `gander` enum('Male','Female','Other') NOT NULL DEFAULT 'Male',
  `contact` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `update_at` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`p_id`, `name`, `gander`, `contact`, `email`, `password`, `update_at`) VALUES
(101, 'user', 'Male', '6789012345', 'user@gmail.com', '$2y$10$kIhoYWdJVhiiSfpFjeQIoOIfb1OuMPDvBc4kiH7XQ7kUKGfhFQF0q', '2025-02-07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`a_id`),
  ADD KEY `fk_patient` (`p_id`),
  ADD KEY `fk_doctor` (`d_id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`d_id`);

--
-- Indexes for table `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`p_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT for table `appointment`
--
ALTER TABLE `appointment`
  MODIFY `a_id` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=137;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `d_id` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- AUTO_INCREMENT for table `patient`
--
ALTER TABLE `patient`
  MODIFY `p_id` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointment`
--
ALTER TABLE `appointment`
  ADD CONSTRAINT `fk_doctor` FOREIGN KEY (`d_id`) REFERENCES `doctors` (`d_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_patient` FOREIGN KEY (`p_id`) REFERENCES `patient` (`p_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
