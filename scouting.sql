-- phpMyAdmin SQL Dump
-- version 4.4.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Nov 07, 2015 at 05:38 PM
-- Server version: 5.6.26
-- PHP Version: 5.6.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;

--
-- Database: `scouting`
--

-- --------------------------------------------------------

--
-- Table structure for table `pit_comments`
--

CREATE TABLE IF NOT EXISTS `pit_comments` (
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

CREATE TABLE IF NOT EXISTS `pit_pictures` (
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

CREATE TABLE IF NOT EXISTS `scouters` (
  `id` smallint(6) NOT NULL,
  `name` tinytext NOT NULL,
  `pswd` tinytext NOT NULL,
  `byteCoins` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `scout_data`
--

CREATE TABLE IF NOT EXISTS `scout_data` (
  `scout_data_id` int(11) NOT NULL,
  `team` int(11) NOT NULL,
  `match_number` int(11) DEFAULT NULL,
  `comments` text,
  `robot_moved` tinyint(1) NOT NULL,
  `totes_auto` int(11) NOT NULL,
  `cans_auto` int(11) NOT NULL,
  `coopertition` tinyint(1) NOT NULL,
  `coopertition_totes` int(11) NOT NULL,
  `score` int(11) DEFAULT NULL,
  `rating` int(11) NOT NULL DEFAULT '1',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `name` text NOT NULL,
  `cans_from_middle` tinyint(1) NOT NULL,
  `totes_from_landfill` tinyint(1) NOT NULL,
  `totes_from_human` tinyint(1) NOT NULL,
  `cans_auto_origin` tinyint(4) NOT NULL,
  `in_auto_zone` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `stacks`
--

CREATE TABLE IF NOT EXISTS `stacks` (
  `scout_data_id` int(11) NOT NULL,
  `totes` int(11) NOT NULL,
  `cap_state` int(11) NOT NULL,
  `cap_height` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

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
-- Indexes for table `scout_data`
--
ALTER TABLE `scout_data`
  ADD PRIMARY KEY (`scout_data_id`),
  ADD KEY `team` (`team`);

--
-- Indexes for table `stacks`
--
ALTER TABLE `stacks`
  ADD KEY `scout_data_id` (`scout_data_id`);

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
-- AUTO_INCREMENT for table `scout_data`
--
ALTER TABLE `scout_data`
  MODIFY `scout_data_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `stacks`
--
ALTER TABLE `stacks`
  ADD CONSTRAINT `stacks_ibfk_1` FOREIGN KEY (`scout_data_id`) REFERENCES `scout_data` (`scout_data_id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
