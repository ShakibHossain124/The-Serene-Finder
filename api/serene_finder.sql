
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `provider_id` int(11) NOT NULL,
  `issue_description` text DEFAULT NULL,
  `scheduled_date` datetime DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `estimated_time` decimal(5,2) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `bookings` (`id`, `customer_id`, `provider_id`, `issue_description`, `scheduled_date`, `address`, `estimated_time`, `total_price`, `status`) VALUES
(1, 1, 3, 'need fixing ', '2026-04-10 00:00:00', '123 serene lake, dhaka 123', 1.00, 100.00, 'completed'),
(2, 1, 3, '', '2026-04-11 00:00:00', '123 serene lake, dhaka 123', 1.00, 100.00, 'completed'),
(3, 1, 4, 'need some fixing to do', '2026-04-12 00:00:00', '123 serene lake, dhaka 123', 1.00, 100.00, 'completed'),
(4, 1, 3, 'fix', '2026-04-18 00:00:00', '123 serene lake, dhaka 123', 3.00, 175.00, 'confirmed'),
(5, 2, 4, 'dfsfd', '2026-04-16 00:00:00', '123 serene lake, dhaka 123', 4.00, 145.00, 'confirmed'),
(6, 2, 4, 'na', '2026-04-18 00:00:00', '123 serene lake, dhaka 123', 2.00, 85.00, 'confirmed'),
(7, 2, 3, 'fg', '2026-04-21 00:00:00', '123 serene lake, dhaka 123', 4.00, 225.00, 'pending'),
(8, 1, 4, '', '2026-04-10 00:00:00', '123 serene lake, dhaka 123', 5.00, 175.00, 'confirmed');

CREATE TABLE `provider_profiles` (
  `user_id` int(11) NOT NULL,
  `specialty` varchar(50) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `hourly_rate` decimal(10,2) NOT NULL,
  `rating` decimal(2,1) DEFAULT 0.0,
  `reviews_count` int(11) DEFAULT 0,
  `bio` text DEFAULT NULL,
  `location` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `provider_profiles` (`user_id`, `specialty`, `category`, `hourly_rate`, `rating`, `reviews_count`, `bio`, `location`) VALUES
(3, 'Electrician ', 'Electri', 50.00, 4.5, 2, 'An expert electrician', 'dhaka'),
(4, 'plumber', 'HVAC', 30.00, 5.0, 1, 'An expert plumber', 'dhaka');

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `provider_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `reviews` (`id`, `booking_id`, `provider_id`, `customer_id`, `rating`, `comment`, `created_at`) VALUES
(1, 1, 3, 1, 5, 'did a good job', '2026-04-10 00:35:26'),
(2, 2, 3, 1, 4, 'ok', '2026-04-10 17:40:51'),
(3, 3, 4, 1, 5, 'okey', '2026-04-10 17:48:06');

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('customer','provider') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`id`, `full_name`, `email`, `password_hash`, `role`, `created_at`) VALUES
(1, 'customer1', 'customer1@gmail.com', '$2y$10$tPowfghwIpdysYGr2cnrDujt11P9D857GF1dSPadwGtDVp/vUHASK', 'customer', '2026-04-10 00:30:34'),
(2, 'customer2', 'customer2@gmail.com', '$2y$10$k/Y3Awk3PmBv0XkwiEw6YeQmxRJhEkPEM7lX44GbmeB1dBLhxIVK6', 'customer', '2026-04-10 00:30:45'),
(3, 'expert1', 'expert1@gmail.com', '$2y$10$eiHTJOCVLCadyfAhsNAeZOAb4Ch9xpyKkvIkQj75vw4z7mfedjwim', 'provider', '2026-04-10 00:30:59'),
(4, 'expert2', 'expert2@gmail.com', '$2y$10$XIb7fBbVhbCwrE.95TW8mO6B3PoQftp1DIRjgCLB73LdjCtSNx3Ge', 'provider', '2026-04-10 00:31:13');
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `provider_id` (`provider_id`);
ALTER TABLE `provider_profiles`
  ADD PRIMARY KEY (`user_id`);
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`provider_id`) REFERENCES `users` (`id`);
ALTER TABLE `provider_profiles`
  ADD CONSTRAINT `provider_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;
