-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jan 29, 2016 at 02:26 AM
-- Server version: 10.1.8-MariaDB
-- PHP Version: 5.6.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `scouting`
--

-- --------------------------------------------------------

--
-- Table structure for table `defenses`
--

DROP TABLE IF EXISTS `defenses`;
CREATE TABLE `defenses` (
  `id` int(11) NOT NULL,
  `low_bar` int(11) NOT NULL,
  `portcullis` int(11) NOT NULL,
  `cheval_de_frise` int(11) NOT NULL,
  `moat` int(11) NOT NULL,
  `ramparts` int(11) NOT NULL,
  `drawbridge` int(11) NOT NULL,
  `sally_port` int(11) NOT NULL,
  `rock_wall` int(11) NOT NULL,
  `rough_terrain` int(11) NOT NULL,
  `gametime` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pit_comments`
--

DROP TABLE IF EXISTS `pit_comments`;
CREATE TABLE `pit_comments` (
  `pit_scout_data_id` int(11) NOT NULL,
  `team_number` int(11) NOT NULL,
  `pit_comments` mediumtext NOT NULL,
  `scouter_name` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pit_pictures`
--

DROP TABLE IF EXISTS `pit_pictures`;
CREATE TABLE `pit_pictures` (
  `pit_scout_data_id` int(11) NOT NULL,
  `team_number` int(11) NOT NULL,
  `pic_num` int(11) DEFAULT NULL,
  `scouter_name` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `scouters`
--

DROP TABLE IF EXISTS `scouters`;
CREATE TABLE `scouters` (
  `id` int(6) NOT NULL,
  `name` tinytext NOT NULL,
  `username` text NOT NULL,
  `pswd` tinytext NOT NULL,
  `byteCoins` int(11) NOT NULL DEFAULT '200'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `scout_data`
--

DROP TABLE IF EXISTS `scout_data`;
CREATE TABLE `scout_data` (
  `scout_data_id` int(11) NOT NULL,
  `name` text NOT NULL,
  `match_number` int(11) NOT NULL,
  `team` int(11) NOT NULL,
  `robot_moved` tinyint(1) NOT NULL,
  `auto_balls_crossed` int(11) NOT NULL,
  `auto_balls_high` int(11) NOT NULL,
  `auto_balls_low` int(11) NOT NULL,
  `teleop_balls_high` int(11) NOT NULL,
  `teleop_balls_low` int(11) NOT NULL,
  `robot_defended` tinyint(1) NOT NULL,
  `end_game` text NOT NULL,
  `rating` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `comments` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` int(11) NOT NULL,
  `token` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `team_info`
--

DROP TABLE IF EXISTS `team_info`;
CREATE TABLE `team_info` (
  `team_number` int(11) DEFAULT NULL,
  `team_name` text NOT NULL,
  `robot_name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `wagers`
--

DROP TABLE IF EXISTS `wagers`;
CREATE TABLE `wagers` (
  `associatedId` int(11) NOT NULL,
  `wagerType` text NOT NULL,
  `wageredByteCoins` int(11) NOT NULL,
  `matchPredicted` int(11) NOT NULL,
  `alliancePredicted` text,
  `withenPoints` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `defenses`
--
ALTER TABLE `defenses`
  ADD KEY `id` (`id`);

--
-- Indexes for table `pit_comments`
--
ALTER TABLE `pit_comments`
  ADD PRIMARY KEY (`pit_scout_data_id`);

--
-- Indexes for table `pit_pictures`
--
ALTER TABLE `pit_pictures`
  ADD PRIMARY KEY (`pit_scout_data_id`);

--
-- Indexes for table `scouters`
--
ALTER TABLE `scouters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `scout_data`
--
ALTER TABLE `scout_data`
  ADD PRIMARY KEY (`scout_data_id`),
  ADD KEY `team` (`team`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pit_comments`
--
ALTER TABLE `pit_comments`
  MODIFY `pit_scout_data_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `pit_pictures`
--
ALTER TABLE `pit_pictures`
  MODIFY `pit_scout_data_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `scouters`
--
ALTER TABLE `scouters`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `scout_data`
--
ALTER TABLE `scout_data`
  MODIFY `scout_data_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
