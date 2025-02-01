-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 01, 2025 at 09:46 AM
-- Server version: 8.0.39
-- PHP Version: 8.2.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `events_management_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `event_date` datetime NOT NULL,
  `user_limit` int NOT NULL,
  `user_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `name`, `description`, `image`, `event_date`, `user_limit`, `user_id`, `created_at`) VALUES
(20, 'Emily Larson', 'Repellendus Volupta', NULL, '1980-10-05 09:50:00', 762, 9, '2025-01-26 15:02:59'),
(21, 'Alika Curry sss', 'Illo consequatur Ut', '/uploads/events/1737903799_IMG20240703112003.jpg', '2005-12-14 17:48:00', 34, 9, '2025-01-26 15:03:19'),
(54, 'Quin Powers sss ddd', 'Nesciunt anim itaqu', NULL, '2013-01-12 10:05:00', 837, 10, '2025-02-01 07:35:59');

-- --------------------------------------------------------

--
-- Table structure for table `event_bookings`
--

CREATE TABLE `event_bookings` (
  `id` int NOT NULL,
  `event_id` int NOT NULL,
  `user_id` int NOT NULL,
  `quantity` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_bookings`
--

INSERT INTO `event_bookings` (`id`, `event_id`, `user_id`, `quantity`, `created_at`) VALUES
(1, 21, 3, 1, '2025-01-29 17:36:49'),
(6, 21, 3, 1, '2025-01-29 17:47:44'),
(7, 21, 3, 1, '2025-01-29 17:50:19'),
(8, 21, 3, 1, '2025-01-29 17:52:08'),
(9, 21, 3, 10, '2025-01-29 17:52:15'),
(12, 21, 3, 19, '2025-01-29 18:03:28'),
(13, 21, 10, 1, '2025-01-31 14:30:02');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `token`, `created_at`) VALUES
(3, 'test', 'test@gmail.com', '$2y$10$zyyGW6XmR/cBdt2hk0m25.eZR2MrmylnziKlo7GCZWT79Ca.eTcH.', 'user', 'f9d01956f0e54c33eee7ad74620fb3c65dedfe8639bd1ea495d39ceffd2e6aca', '2025-01-25 15:00:08'),
(4, 'test2', 'test2@gmail.com', '$2y$10$lRplTMNqAwkahubp.Bllze1VqLg7oqhixY81ybPXAjOOZ/PxLYTq.', 'user', '057bc182dae846ef5f32f3ba7dd81e6c3dafd873ddedaa86bc3e6908b04ef326', '2025-01-26 06:38:41'),
(5, 'test3', 'test3@gmail.com', '$2y$10$a4XC8/TlL4EKjXi.lmrePOzmJYQ57rj1SHFCyUvoHpaPQXeDUcPhK', 'user', '6a373cf3c40b331e23f2a948b1ea8abe9ec2033767bba535232e6795666c3f5d', '2025-01-26 06:39:37'),
(6, 'test4', 'test4@gmail.com', '$2y$10$FhxtgCWxCdTMYGwtk8668OIArFURJh7Vuh3W96MWXhi8UJWNA3.Ve', 'user', NULL, '2025-01-26 06:40:55'),
(7, 'test5', 'test5@gmail.com', '$2y$10$TqhIGVHn1GSpeQLdKBKSqujNX3ydFWClLndOQiHiS1O.NZjWPyOMS', 'user', '530ddd78bcaca8225d41315f58e6ecf19870a7a6e6cb8d9897501d4dcc17704a', '2025-01-26 06:42:56'),
(8, 'test6', 'test6@gmail.com', '$2y$10$IX2mhxP9Z404czpwoAvyNOzYtZ.KWrf90E3bCxG/F2XGe8Zfk45BS', 'user', NULL, '2025-01-26 06:47:37'),
(9, 'test50', 'test50@gmail.com', '$2y$10$exAIA/LTpOO1oD2Yu5cctuYlGgyyzXX5aYjsPz4P4z/a.JrM2pLXq', 'user', '9f82db6e2bc8a9f5609502257f5c9a7abd05fa613b0081cd8de1aab00157c877', '2025-01-26 15:00:03'),
(10, 'admin', 'admin@gmail.com', '$2y$10$dbEF63JD7mWXtTlU3GhBUOU9zvMzfT1YiO1f8/HnpdzhwVtpv0Q7W', 'admin', '1e4521f07a40784626092347baf94844165464a9d0879d09b20f0a847e542de6', '2025-01-31 12:39:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `event_bookings`
--
ALTER TABLE `event_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `event_id` (`event_id`) USING BTREE;

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `event_bookings`
--
ALTER TABLE `event_bookings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `event_bookings`
--
ALTER TABLE `event_bookings`
  ADD CONSTRAINT `event_bookings_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `event_bookings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
