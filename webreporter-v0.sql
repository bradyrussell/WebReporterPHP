SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `webreporter` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `webreporter`;

CREATE TABLE `manager_sessions` (
  `session_id` int(11) NOT NULL,
  `username` varchar(32) NOT NULL,
  `session_key` varchar(64) NOT NULL,
  `ip` varchar(64) NOT NULL,
  `session_state` int(11) NOT NULL DEFAULT '1',
  `login_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `reports` (
  `report_id` int(11) NOT NULL,
  `category` varchar(255) NOT NULL DEFAULT '',
  `status` varchar(32) NOT NULL,
  `reported_username` varchar(32) NOT NULL,
  `reported_uuid` varchar(40) NOT NULL,
  `reporter_username` varchar(32) NOT NULL,
  `reporter_uuid` varchar(40) NOT NULL,
  `reported_location` varchar(128) NOT NULL,
  `reporter_location` varchar(128) NOT NULL,
  `manager` varchar(64) NOT NULL DEFAULT '',
  `resolution` text,
  `details` text,
  `reported_inv` text NOT NULL,
  `form_key` varchar(16) NOT NULL,
  `report_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reporter_ip` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `report_categories` (
  `category_id` int(11) NOT NULL,
  `category_identifier` varchar(32) NOT NULL,
  `category_display_name` varchar(128) NOT NULL,
  `subcategories` varchar(256) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `report_categories` (`category_id`, `category_identifier`, `category_display_name`, `subcategories`) VALUES
(1, 'adminabuse', 'Admin Abuse', 'PvP Abuse|Giving Items|God Mode|WorldEdit / Creative Mode Abuse'),
(2, 'hacking', 'Hacks and Cheats', 'Duping|Kill Aura|Aimbot|X-Ray'),
(3, 'Harassment', 'Harassment', 'IRL Threats|Personal Attacks'),
(4, 'UnfairGameplay', 'Unfair Gameplay', 'Combat Logging|AFK Farming|Boosting');


ALTER TABLE `manager_sessions`
  ADD PRIMARY KEY (`session_id`);

ALTER TABLE `reports`
  ADD PRIMARY KEY (`report_id`);

ALTER TABLE `report_categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_identifier` (`category_identifier`);


ALTER TABLE `manager_sessions`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
ALTER TABLE `reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
ALTER TABLE `report_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
