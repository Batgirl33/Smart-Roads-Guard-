-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 22, 2025 at 08:36 PM
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
-- Database: `smartroadsguard3`
--

-- --------------------------------------------------------

--
-- Table structure for table `alert`
--

CREATE TABLE `alert` (
  `alert_id` int(11) NOT NULL,
  `flashlight_id` int(11) DEFAULT NULL,
  `obstacle_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `alert`
--

INSERT INTO `alert` (`alert_id`, `flashlight_id`, `obstacle_id`) VALUES
(1, 5, 99),
(12, 5, 100);

-- --------------------------------------------------------

--
-- Table structure for table `camera`
--

CREATE TABLE `camera` (
  `camera_id` int(11) NOT NULL,
  `latitude` decimal(9,6) NOT NULL,
  `longitude` decimal(10,6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `camera`
--

INSERT INTO `camera` (`camera_id`, `latitude`, `longitude`) VALUES
(1, 0.000000, 0.000000),
(2, 0.000000, 0.000000),
(3, 0.000000, 0.000000);

-- --------------------------------------------------------

--
-- Table structure for table `detection_event`
--

CREATE TABLE `detection_event` (
  `detection_id` int(11) NOT NULL,
  `detection_time` datetime NOT NULL,
  `alert_id` int(11) DEFAULT NULL,
  `obstacle_id` int(11) DEFAULT NULL,
  `report_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detection_event`
--

INSERT INTO `detection_event` (`detection_id`, `detection_time`, `alert_id`, `obstacle_id`, `report_id`) VALUES
(111, '2025-02-19 22:27:52', 1, 99, 12),
(112, '2025-03-12 20:35:26', 12, 100, 10);

-- --------------------------------------------------------

--
-- Table structure for table `flashlight`
--

CREATE TABLE `flashlight` (
  `flashlight_id` int(11) NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp(),
  `camera_id` int(11) DEFAULT NULL,
  `type_flashlight` enum('normal','foglight') NOT NULL DEFAULT 'normal',
  `active` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `flashlight`
--

INSERT INTO `flashlight` (`flashlight_id`, `timestamp`, `camera_id`, `type_flashlight`, `active`) VALUES
(5, '2025-03-24 15:03:36', 1, 'normal', '1'),
(6, '2025-04-09 21:15:10', 2, 'foglight', '1'),
(7, '2025-04-21 21:15:27', 3, 'normal', '1');

-- --------------------------------------------------------

--
-- Table structure for table `fog_light`
--

CREATE TABLE `fog_light` (
  `fog_light_id` int(11) NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp(),
  `solar_panel_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fog_light`
--

INSERT INTO `fog_light` (`fog_light_id`, `timestamp`, `solar_panel_id`) VALUES
(90, '2025-03-27 07:03:09', 13);

-- --------------------------------------------------------

--
-- Table structure for table `noted_report`
--

CREATE TABLE `noted_report` (
  `id_noted` int(11) NOT NULL,
  `noted` text NOT NULL,
  `id_report` int(11) NOT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `noted_report`
--

INSERT INTO `noted_report` (`id_noted`, `noted`, `id_report`, `id_user`) VALUES
(2, 'aaa', 12, 1),
(4, 'vereeeee good  ', 10, 2),
(5, ' ok    ', 10, 1);

-- --------------------------------------------------------

--
-- Table structure for table `obstacle`
--

CREATE TABLE `obstacle` (
  `obstacle_id` int(11) NOT NULL,
  `classification` enum('landslide','accident_class0','accident_class1','fallen_tree','buffle','guepard','hippopotame','lion','loup','rhinoceros','tigre','other') NOT NULL DEFAULT 'other',
  `obstacle_picture` varchar(255) DEFAULT NULL,
  `camera_id` int(11) DEFAULT NULL,
  `detection_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `obstacle`
--

INSERT INTO `obstacle` (`obstacle_id`, `classification`, `obstacle_picture`, `camera_id`, `detection_time`) VALUES
(99, 'other', NULL, 1, '2025-02-19 22:27:52'),
(100, 'other', NULL, 1, '2025-03-12 20:35:26');

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

CREATE TABLE `report` (
  `report_id` int(11) NOT NULL,
  `status` enum('In Progress','Pending','Completed') DEFAULT 'Pending',
  `report_submission_datetime` datetime DEFAULT current_timestamp(),
  `userid` int(11) DEFAULT NULL,
  `alert_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `report`
--

INSERT INTO `report` (`report_id`, `status`, `report_submission_datetime`, `userid`, `alert_id`) VALUES
(10, 'Pending', '2025-03-31 15:25:41', 2, 12),
(12, 'Pending', '2025-02-20 00:30:34', 1, 12);

-- --------------------------------------------------------

--
-- Table structure for table `solar_panel`
--

CREATE TABLE `solar_panel` (
  `solar_panel_id` int(11) NOT NULL,
  `camera_id` int(11) DEFAULT NULL,
  `fog_light_id` int(11) DEFAULT NULL,
  `flashlight_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `solar_panel`
--

INSERT INTO `solar_panel` (`solar_panel_id`, `camera_id`, `fog_light_id`, `flashlight_id`) VALUES
(13, 1, 90, 5);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` char(255) NOT NULL,
  `account_status` enum('Active','Inactive','Suspended') DEFAULT 'Active',
  `email` varchar(255) DEFAULT NULL,
  `verifi_code` int(10) DEFAULT NULL,
  `type_user` enum('service','admin') NOT NULL DEFAULT 'service'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `account_status`, `email`, `verifi_code`, `type_user`) VALUES
(1, 'Bayan Ali', '$2y$10$OFMC8qIYJhFQrrHnJ4elcuA8iIiiwFebFb1Rs8f9nKlH9uCDC/CTG', 'Active', 'Traffic@gamil.com', 29269405, 'service'),
(2, 'Musa Jaffar', '$2y$10$qtFbNToNutRTnv47gjz2gurC7nhRbXfeGHsB0YdAEUKlYX5U66xeu', 'Inactive', 'slb@gmail.com', NULL, 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alert`
--
ALTER TABLE `alert`
  ADD PRIMARY KEY (`alert_id`),
  ADD KEY `idx_flashlight_id_alert` (`flashlight_id`),
  ADD KEY `idx_obstacle_id_alert` (`obstacle_id`);

--
-- Indexes for table `camera`
--
ALTER TABLE `camera`
  ADD PRIMARY KEY (`camera_id`);

--
-- Indexes for table `detection_event`
--
ALTER TABLE `detection_event`
  ADD PRIMARY KEY (`detection_id`,`detection_time`),
  ADD KEY `idx_alert_id_detection_event` (`alert_id`),
  ADD KEY `idx_obstacle_id_detection_event` (`obstacle_id`),
  ADD KEY `idx_report_id_detection_event` (`report_id`);

--
-- Indexes for table `flashlight`
--
ALTER TABLE `flashlight`
  ADD PRIMARY KEY (`flashlight_id`),
  ADD KEY `idx_camera_id_flashlight` (`camera_id`),
  ADD KEY `flashlight_id` (`flashlight_id`,`timestamp`,`camera_id`,`type_flashlight`,`active`);

--
-- Indexes for table `fog_light`
--
ALTER TABLE `fog_light`
  ADD PRIMARY KEY (`fog_light_id`),
  ADD KEY `idx_solar_panel_id_fog_light` (`solar_panel_id`);

--
-- Indexes for table `noted_report`
--
ALTER TABLE `noted_report`
  ADD PRIMARY KEY (`id_noted`),
  ADD KEY `noted_report` (`id_report`),
  ADD KEY `user_node` (`id_user`);

--
-- Indexes for table `obstacle`
--
ALTER TABLE `obstacle`
  ADD PRIMARY KEY (`obstacle_id`),
  ADD KEY `idx_camera_id_obstacle` (`camera_id`),
  ADD KEY `idx_detection_obstacle` (`detection_time`);

--
-- Indexes for table `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `idx_report_submission_datetime` (`report_submission_datetime`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_admin_id_report` (`userid`),
  ADD KEY `idx_alert_id_report` (`alert_id`);

--
-- Indexes for table `solar_panel`
--
ALTER TABLE `solar_panel`
  ADD PRIMARY KEY (`solar_panel_id`),
  ADD KEY `idx_camera_id_solar_panel` (`camera_id`),
  ADD KEY `idx_fog_light_id_solar_panel` (`fog_light_id`),
  ADD KEY `idx_flashlight_id_solar_panel` (`flashlight_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alert`
--
ALTER TABLE `alert`
  MODIFY `alert_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=695;

--
-- AUTO_INCREMENT for table `camera`
--
ALTER TABLE `camera`
  MODIFY `camera_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- AUTO_INCREMENT for table `flashlight`
--
ALTER TABLE `flashlight`
  MODIFY `flashlight_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `fog_light`
--
ALTER TABLE `fog_light`
  MODIFY `fog_light_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `noted_report`
--
ALTER TABLE `noted_report`
  MODIFY `id_noted` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `obstacle`
--
ALTER TABLE `obstacle`
  MODIFY `obstacle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=791;

--
-- AUTO_INCREMENT for table `report`
--
ALTER TABLE `report`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `solar_panel`
--
ALTER TABLE `solar_panel`
  MODIFY `solar_panel_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `alert`
--
ALTER TABLE `alert`
  ADD CONSTRAINT `fk_flashlight_alert` FOREIGN KEY (`flashlight_id`) REFERENCES `flashlight` (`flashlight_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_obstacle_alert` FOREIGN KEY (`obstacle_id`) REFERENCES `obstacle` (`obstacle_id`) ON DELETE CASCADE;

--
-- Constraints for table `detection_event`
--
ALTER TABLE `detection_event`
  ADD CONSTRAINT `fk_alert_detection_event` FOREIGN KEY (`alert_id`) REFERENCES `alert` (`alert_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_obstacle_detection_event` FOREIGN KEY (`obstacle_id`) REFERENCES `obstacle` (`obstacle_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_report_detection_event` FOREIGN KEY (`report_id`) REFERENCES `report` (`report_id`) ON DELETE CASCADE;

--
-- Constraints for table `flashlight`
--
ALTER TABLE `flashlight`
  ADD CONSTRAINT `fk_camera_flashlight` FOREIGN KEY (`camera_id`) REFERENCES `camera` (`camera_id`) ON DELETE CASCADE;

--
-- Constraints for table `fog_light`
--
ALTER TABLE `fog_light`
  ADD CONSTRAINT `fk_solar_panel_fog_light` FOREIGN KEY (`solar_panel_id`) REFERENCES `solar_panel` (`solar_panel_id`) ON DELETE CASCADE;

--
-- Constraints for table `noted_report`
--
ALTER TABLE `noted_report`
  ADD CONSTRAINT `noted_report` FOREIGN KEY (`id_report`) REFERENCES `report` (`report_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_node` FOREIGN KEY (`id_user`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `obstacle`
--
ALTER TABLE `obstacle`
  ADD CONSTRAINT `fk_camera_obstacle` FOREIGN KEY (`camera_id`) REFERENCES `camera` (`camera_id`) ON DELETE CASCADE;

--
-- Constraints for table `report`
--
ALTER TABLE `report`
  ADD CONSTRAINT `fk_alert_report` FOREIGN KEY (`alert_id`) REFERENCES `alert` (`alert_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user_report` FOREIGN KEY (`userid`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `solar_panel`
--
ALTER TABLE `solar_panel`
  ADD CONSTRAINT `fk_camera_solar_panel` FOREIGN KEY (`camera_id`) REFERENCES `camera` (`camera_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_flashlight_solar_panel` FOREIGN KEY (`flashlight_id`) REFERENCES `flashlight` (`flashlight_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_fog_light_solar_panel` FOREIGN KEY (`fog_light_id`) REFERENCES `fog_light` (`fog_light_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
