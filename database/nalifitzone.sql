-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 21, 2025 at 09:43 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nalifitzone`
--

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

DROP TABLE IF EXISTS `classes`;
CREATE TABLE IF NOT EXISTS `classes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `class_name` varchar(100) NOT NULL,
  `description` text,
  `trainer` varchar(100) DEFAULT NULL,
  `schedule_days` varchar(50) DEFAULT NULL,
  `schedule_time` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `duration` int DEFAULT '60',
  `capacity` int DEFAULT '20',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `class_name`, `description`, `trainer`, `schedule_days`, `schedule_time`, `price`, `duration`, `capacity`, `created_at`) VALUES
(1, 'Yoga for Beginners', 'Join us for a relaxing session focused on basic yoga poses and breathing techniques.', 'Sarah Johnson', 'Monday,Wednesday', '6:00 PM - 7:00 PM', 15.00, 70, 20, '2025-07-05 04:38:12'),
(2, 'High-Intensity Interval Training (HIIT)', 'A fast-paced class designed to improve strength and endurance with short bursts of intense exercise.', 'Mike Chen', 'Tuesday,Thursday', '7:00 PM - 8:00 PM', 20.00, 60, 15, '2025-07-05 04:38:12'),
(3, 'Strength Training', 'Build muscle and strength in this class, focusing on weights and resistance training techniques.', 'David Smith', 'Monday,Friday', '5:00 PM - 6:00 PM', 18.00, 60, 12, '2025-07-05 04:38:12'),
(4, 'Cardio Dance', 'Get your heart pumping with this fun and energetic dance class suitable for all levels.', 'Lisa Rodriguez', 'Saturday', '10:00 AM - 11:00 AM', 12.00, 60, 25, '2025-07-05 04:38:12');

-- --------------------------------------------------------

--
-- Table structure for table `class_registrations`
--

DROP TABLE IF EXISTS `class_registrations`;
CREATE TABLE IF NOT EXISTS `class_registrations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `class_id` int NOT NULL,
  `registration_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('registered','cancelled','attended') DEFAULT 'registered',
  `payment_status` enum('pending','paid','refunded') DEFAULT 'pending',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_class` (`user_id`,`class_id`),
  KEY `user_id` (`user_id`),
  KEY `class_id` (`class_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `class_registrations`
--

INSERT INTO `class_registrations` (`id`, `user_id`, `class_id`, `registration_date`, `status`, `payment_status`) VALUES
(1, 17, 2, '2025-07-05 04:49:55', 'registered', 'pending'),
(2, 17, 4, '2025-07-05 04:50:00', 'registered', 'pending'),
(3, 17, 1, '2025-07-05 05:22:53', 'registered', 'pending'),
(4, 17, 3, '2025-07-05 05:23:24', 'registered', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `class_schedules`
--

DROP TABLE IF EXISTS `class_schedules`;
CREATE TABLE IF NOT EXISTS `class_schedules` (
  `id` int NOT NULL AUTO_INCREMENT,
  `class_id` int NOT NULL,
  `trainer_id` int NOT NULL,
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `room` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `class_id` (`class_id`),
  KEY `trainer_id` (`trainer_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `class_schedules`
--

INSERT INTO `class_schedules` (`id`, `class_id`, `trainer_id`, `day_of_week`, `start_time`, `end_time`, `room`, `is_active`) VALUES
(1, 1, 1, 'Monday', '18:00:00', '19:00:00', 'Studio A', 1),
(2, 1, 1, 'Wednesday', '18:00:00', '19:00:00', 'Studio A', 1),
(3, 2, 2, 'Tuesday', '19:00:00', '20:00:00', 'Studio B', 1),
(4, 2, 2, 'Thursday', '19:00:00', '20:00:00', 'Studio B', 1),
(5, 3, 3, 'Monday', '17:00:00', '18:00:00', 'Weight Room', 1),
(6, 3, 3, 'Friday', '17:00:00', '18:00:00', 'Weight Room', 1),
(7, 4, 4, 'Saturday', '10:00:00', '11:00:00', 'Studio C', 1);

-- --------------------------------------------------------

--
-- Table structure for table `memberships`
--

DROP TABLE IF EXISTS `memberships`;
CREATE TABLE IF NOT EXISTS `memberships` (
  `membership_id` int NOT NULL AUTO_INCREMENT,
  `member_id` int DEFAULT NULL,
  `membership_type` varchar(50) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `duration_months` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` enum('Active','Expired','Pending Renewal') DEFAULT 'Active',
  `payment_method` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`membership_id`),
  KEY `member_id` (`member_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `memberships`
--

INSERT INTO `memberships` (`membership_id`, `member_id`, `membership_type`, `start_date`, `end_date`, `duration_months`, `price`, `status`, `payment_method`, `created_at`) VALUES
(1, 1, 'Monthly', '2024-11-01', '2024-12-01', 1, 50.00, 'Active', 'Credit Card', '2024-11-13 17:20:41'),
(2, 2, 'Annual', '2024-01-01', '2025-01-01', 12, 500.00, 'Active', 'Bank Transfer', '2024-11-13 17:20:41'),
(3, 3, 'Monthly', '2024-08-01', '2024-09-01', 1, 50.00, 'Expired', 'Cash', '2024-11-13 17:25:45'),
(4, 4, 'Quarterly', '2023-10-01', '2024-01-01', 3, 150.00, 'Expired', 'Credit Card', '2024-11-13 17:25:45');

-- --------------------------------------------------------

--
-- Table structure for table `membership_plans`
--

DROP TABLE IF EXISTS `membership_plans`;
CREATE TABLE IF NOT EXISTS `membership_plans` (
  `id` int NOT NULL AUTO_INCREMENT,
  `plan_name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `duration_months` int NOT NULL,
  `description` text,
  `features` text,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `membership_plans`
--

INSERT INTO `membership_plans` (`id`, `plan_name`, `price`, `duration_months`, `description`, `features`, `is_active`, `created_at`) VALUES
(1, 'Basic Plan', 29.99, 1, 'Perfect for beginners', 'Access to gym equipment, 2 group classes per month, Locker access', 1, '2025-07-05 04:46:11'),
(2, 'Premium Plan', 49.99, 1, 'Most popular choice', 'Access to gym equipment, Unlimited group classes, Personal training session (1 per month), Locker access, Nutrition consultation', 1, '2025-07-05 04:46:11'),
(3, 'Elite Plan', 79.99, 1, 'Complete fitness solution', 'Access to gym equipment, Unlimited group classes, Weekly personal training sessions, Priority locker access, Nutrition consultation, Spa access, Guest passes (2 per month)', 1, '2025-07-05 04:46:11');

-- --------------------------------------------------------

--
-- Table structure for table `trainers`
--

DROP TABLE IF EXISTS `trainers`;
CREATE TABLE IF NOT EXISTS `trainers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `specialization` varchar(200) DEFAULT NULL,
  `experience_years` int DEFAULT NULL,
  `bio` text,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `trainers`
--

INSERT INTO `trainers` (`id`, `name`, `email`, `phone`, `specialization`, `experience_years`, `bio`, `profile_image`, `created_at`) VALUES
(1, 'Sarah Johnson', 'sarah.johnson@fitzone.com', '+1-555-0101', 'Yoga, Meditation, Flexibility Training', 8, 'Certified yoga instructor with 8 years of experience in Hatha and Vinyasa yoga.', NULL, '2025-07-05 04:38:13'),
(2, 'Mike Chen', 'mike.chen@fitzone.com', '+1-555-0102', 'HIIT, CrossFit, Strength Training', 6, 'Former professional athlete specializing in high-intensity training and functional fitness.', NULL, '2025-07-05 04:38:13'),
(3, 'David Smith', 'david.smith@fitzone.com', '+1-555-0103', 'Powerlifting, Bodybuilding, Strength Training', 10, 'Certified personal trainer with expertise in strength training and muscle building.', NULL, '2025-07-05 04:38:13'),
(4, 'Lisa Rodriguez', 'lisa.rodriguez@fitzone.com', '+1-555-0104', 'Dance Fitness, Cardio, Zumba', 5, 'Professional dancer turned fitness instructor, specializing in cardio dance and group fitness.', NULL, '2025-07-05 04:38:13');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `date` date NOT NULL,
  `usertype` enum('Admin','Customer','Staff') NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `username`, `gender`, `date`, `usertype`, `password`) VALUES
(18, 'Gym staff', 'staffgym@gmail.com', 'gymstaff', 'Male', '2025-07-06', 'Staff', '$2y$10$S7BpS2ga2Wr8jWzHjLfdMu64.fqG2t3w8DUzNz0dgvZobCMsgLlIe'),
(19, 'user', 'user@gmail.com', 'user', 'Male', '2025-07-06', 'Customer', 'user'),
(17, 'drr', 'drr@gmail.com', 'drr@gmail.com', 'Male', '2025-07-22', 'Admin', 'drr@gmail.com');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
