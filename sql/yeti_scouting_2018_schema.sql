-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 03, 2018 at 08:47 PM
-- Server version: 10.1.30-MariaDB
-- PHP Version: 7.2.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `yeti_scouting`
--

-- --------------------------------------------------------

--
-- Table structure for table `form_data`
--

CREATE TABLE `form_data` (
  `id` int(11) NOT NULL,
  `auto_check` tinyint(1) NOT NULL DEFAULT '0',
  `auto_defend` tinyint(1) DEFAULT '0',
  `auto_scale` tinyint(1) DEFAULT '0',
  `auto_speed` int(11) DEFAULT '1',
  `bar_climb` tinyint(1) DEFAULT '0',
  `comment` varchar(500) NOT NULL,
  `cube_ranking` int(11) NOT NULL,
  `enemy_switch_cubes` int(11) DEFAULT '0',
  `help_climb` tinyint(1) DEFAULT '0',
  `match_number` int(11) NOT NULL,
  `other_climb` varchar(255) DEFAULT NULL,
  `ramp_climb` tinyint(1) DEFAULT '0',
  `scale_cubes` int(11) NOT NULL DEFAULT '0',
  `score` int(11) NOT NULL,
  `switch_cubes` int(11) NOT NULL DEFAULT '0',
  `team_number` int(11) NOT NULL,
  `tele_check` tinyint(1) NOT NULL DEFAULT '0',
  `tele_defense` tinyint(1) DEFAULT '0',
  `tele_speed` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pit_comments`
--

CREATE TABLE `pit_comments` (
  `pit_scout_data_id` int(11) NOT NULL,
  `team_number` int(11) NOT NULL,
  `pit_comments` mediumtext NOT NULL,
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pit_pictures`
--

CREATE TABLE `pit_pictures` (
  `pit_scout_data_id` int(11) NOT NULL,
  `team_number` int(11) NOT NULL,
  `pic_num` int(11) DEFAULT NULL,
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `rankings`
--

CREATE TABLE `rankings` (
  `team_number` int(11) NOT NULL,
  `event_key` tinytext,
  `team_name` text NOT NULL,
  `event_name` text NOT NULL,
  `qual_rank` smallint(6) NOT NULL,
  `wins` tinyint(4) NOT NULL,
  `losses` tinyint(4) NOT NULL,
  `ties` tinyint(4) NOT NULL,
  `next_match_number` tinyint(4) DEFAULT NULL,
  `next_match_time` datetime DEFAULT NULL,
  `lastmodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `scouters`
--

CREATE TABLE `scouters` (
  `id` int(6) NOT NULL,
  `name` tinytext NOT NULL,
  `username` text NOT NULL,
  `pswd` tinytext NOT NULL,
  `byteCoins` int(11) NOT NULL DEFAULT '200'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` int(11) NOT NULL,
  `token` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `team_info`
--

CREATE TABLE `team_info` (
  `team_number` int(11) NOT NULL DEFAULT '0',
  `team_name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `wagers`
--

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
-- Indexes for table `form_data`
--
ALTER TABLE `form_data`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `team_info`
--
ALTER TABLE `team_info`
  ADD PRIMARY KEY (`team_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `form_data`
--
ALTER TABLE `form_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `pit_comments`
--
ALTER TABLE `pit_comments`
  MODIFY `pit_scout_data_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pit_pictures`
--
ALTER TABLE `pit_pictures`
  MODIFY `pit_scout_data_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `scouters`
--
ALTER TABLE `scouters`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
