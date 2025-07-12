-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 12, 2025 at 06:13 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `marking_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `marks`
--

CREATE TABLE `marks` (
  `id` int NOT NULL,
  `student_id` int NOT NULL,
  `assignment` decimal(5,2) NOT NULL,
  `exam` decimal(5,2) NOT NULL,
  `total` decimal(5,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `marks`
--

INSERT INTO `marks` (`id`, `student_id`, `assignment`, `exam`, `total`, `created_at`) VALUES
(5, 6, '25.00', '55.00', '46.00', '2025-07-12 05:54:27'),
(6, 7, '30.00', '70.00', '58.00', '2025-07-12 05:56:17'),
(7, 2, '100.00', '100.00', '100.00', '2025-07-12 06:02:25');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('lecturer','student') NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `name`, `created_at`) VALUES
(1, '205152', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'lecturer', 'Dr. Hasiah', '2025-07-12 05:43:34'),
(2, '2023001', '$2y$10$hvJ1/MxkFsaNlTk.q0XvYeF8bCmwQBbgb4ld6hfE6v0wKSygWbgfK', 'student', 'Ahmad Ali', '2025-07-12 05:43:34'),
(3, '2023002', '$2y$10$5DBz4wAlWEnpcUIrCTo6fe2cFwUQI2KOOog6lcUIqB2cmTKh/etUW', 'student', 'Siti Fatimah', '2025-07-12 05:43:34'),
(4, '2023003', '$2y$10$gxAGfxRVccIBLLdrih9n3uFJ6GLboIQYXi4vdHjZtv3/cgtNTPrcW', 'student', 'Mohammed Hassan', '2025-07-12 05:43:34'),
(6, '2023004', '$2y$10$e6AxKS6YMQ7wB9/Q6Ud8HOLck5ZO2AJ8zwhxuhQCNtSTuRnZnP6He', 'student', 'Muhammad', '2025-07-12 05:51:41'),
(7, '2023006', '$2y$10$MO.5Vj86soWoLsbCjMr4A.NVHDw0bDw6aux9llbXEywgv0iVe3PRG', 'student', 'Muhammad Faris', '2025-07-12 05:55:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `marks`
--
ALTER TABLE `marks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `marks`
--
ALTER TABLE `marks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `marks`
--
ALTER TABLE `marks`
  ADD CONSTRAINT `marks_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
