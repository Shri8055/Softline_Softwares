-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:4306
-- Generation Time: Jun 08, 2025 at 07:17 PM
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
-- Database: `ss`
--

-- --------------------------------------------------------

--
-- Table structure for table `career`
--

CREATE TABLE `career` (
  `id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `jobtype` varchar(50) DEFAULT NULL,
  `currstatus` varchar(50) DEFAULT NULL,
  `resume_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `career`
--

INSERT INTO `career` (`id`, `name`, `email`, `jobtype`, `currstatus`, `resume_path`) VALUES
(1, 'Shrinivas', 'shrinivaskangralkar8055@gmail.com', 'Full stack Web Dev', 'Student', 'uploads/resumes/6826c10c5296a.pdf'),
(2, 'Shrinivas', 'shrinivaskangralkar8055@gmail.com', 'Full stack Web Dev', 'Student', 'uploads/resumes/6826c16796f6f.pdf'),
(3, 'Shrinivas', 'shrinivaskangralkar8055@gmail.com', 'Full stack Web Dev', 'Student', 'uploads/resumes/6826c2725a51f.pdf'),
(4, 'Shrinivas Kangralkar', 'shrinivaskangralkar8055@gmail.com', 'Full stack Web Dev', 'Student', 'uploads/resumes/6826c2abc5e05.pdf'),
(5, 'Shrinivas ', 'shrinivaskangralkar8055@gmail.com', 'Backend Developer', 'Employed', 'uploads/resumes/682cc5fdb2574.docx');

-- --------------------------------------------------------

--
-- Table structure for table `contact_queries`
--

CREATE TABLE `contact_queries` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `idea` text DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_queries`
--

INSERT INTO `contact_queries` (`id`, `full_name`, `phone`, `email`, `idea`, `submitted_at`) VALUES
(1, 'Shrinivas Kangralkar', '09307856854', 'shrinivaskangralkar8055@gmail.com', 'Want to make NFC card', '2025-05-16 04:55:44');

-- --------------------------------------------------------

--
-- Table structure for table `daily_visits`
--

CREATE TABLE `daily_visits` (
  `visit_date` date NOT NULL,
  `count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `daily_visits`
--

INSERT INTO `daily_visits` (`visit_date`, `count`) VALUES
('2025-05-19', 1),
('2025-05-20', 1),
('2025-05-22', 12),
('2025-06-04', 18),
('2025-06-07', 20),
('2025-06-08', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `career`
--
ALTER TABLE `career`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_queries`
--
ALTER TABLE `contact_queries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `daily_visits`
--
ALTER TABLE `daily_visits`
  ADD PRIMARY KEY (`visit_date`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `career`
--
ALTER TABLE `career`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `contact_queries`
--
ALTER TABLE `contact_queries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
