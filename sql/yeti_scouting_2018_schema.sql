-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 17, 2018 at 09:19 PM
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

DROP TABLE IF EXISTS `form_data`;
CREATE TABLE `form_data` (
  `id` int(11) NOT NULL,
  `auto_check` tinyint(1) NOT NULL DEFAULT '0',
  `auto_defend` tinyint(1) NOT NULL DEFAULT '0',
  `auto_scale` tinyint(1) NOT NULL DEFAULT '0',
  `auto_speed` int(11) NOT NULL DEFAULT '1',
  `bar_climb` tinyint(1) NOT NULL DEFAULT '0',
  `comment` varchar(500) NOT NULL,
  `cube_ranking` int(11) NOT NULL,
  `enemy_switch_cubes` int(11) NOT NULL DEFAULT '0',
  `help_climb` tinyint(1) NOT NULL DEFAULT '0',
  `match_number` int(11) NOT NULL,
  `other_climb` varchar(255) NOT NULL,
  `ramp_climb` tinyint(1) NOT NULL DEFAULT '0',
  `scale_cubes` int(11) NOT NULL DEFAULT '0',
  `score` int(11) NOT NULL,
  `switch_cubes` int(11) NOT NULL DEFAULT '0',
  `team_number` int(11) NOT NULL,
  `tele_check` tinyint(1) NOT NULL DEFAULT '0',
  `tele_defense` tinyint(1) NOT NULL DEFAULT '0',
  `tele_speed` int(11) NOT NULL DEFAULT '1',
  `scouter_id` int(11) NOT NULL
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
  `scouter_id` int(11) NOT NULL,
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
  `scouter_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `rankings`
--

DROP TABLE IF EXISTS `rankings`;
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

DROP TABLE IF EXISTS `scouters`;
CREATE TABLE `scouters` (
  `id` int(6) NOT NULL,
  `name` tinytext NOT NULL,
  `username` varchar(50) NOT NULL,
  `pswd` tinytext NOT NULL,
  `byteCoins` int(11) NOT NULL DEFAULT '200'
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
  `team_number` int(11) NOT NULL DEFAULT '0',
  `team_name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `team_info`
--

INSERT INTO `team_info` (`team_number`, `team_name`) VALUES
(8, 'Team 8'),
(25, 'Raider Robotix'),
(166, 'Chop Shop'),
(191, 'X-CATS'),
(238, 'Cruisin\' Crusaders'),
(287, 'Floyd'),
(321, 'RoboLancers'),
(339, 'Kilroy Robotics'),
(379, 'RoboCats'),
(422, 'Mech Tech Dragons'),
(435, 'Robodogs'),
(587, 'Hedgehogs'),
(639, 'Code Red Robotics'),
(694, 'StuyPulse'),
(836, 'The RoboBees'),
(858, 'Demons'),
(876, 'Thunder Robotics'),
(900, 'â™žThe Zebracornsâ™ž'),
(955, 'CV Robotics'),
(1073, 'The Force Team'),
(1089, 'Team Mercury'),
(1123, 'AIM Robotics'),
(1225, 'Gorillas'),
(1261, 'Robo  Lions'),
(1389, 'The Body Electric'),
(1511, 'Rolling Thunder'),
(1533, 'Triple Strange'),
(1600, 'ROBO KINGS AND QUEENS'),
(1662, 'Raptor Force Engineering'),
(1868, 'Space Cookies'),
(1885, 'ILITE Robotics'),
(1983, 'Skunk Works Robotics'),
(1991, 'Dragons'),
(2059, 'The Hitchhikers'),
(2080, 'Torbotics'),
(2168, 'Aluminum Falcons'),
(2175, 'The Fighting Calculators'),
(2219, 'Megahurts'),
(2220, 'Blue Twilight'),
(2557, 'SOTAbots'),
(2640, 'HOTBOTZ'),
(2642, 'Pitt Pirates'),
(2655, 'The Flying Platypi'),
(2682, 'Boneyard Robotics'),
(2848, 'The All Sparks'),
(2883, 'F.R.E.D (Fighting Rednecks Engineering and Design)'),
(2974, 'Walton Robotics'),
(2980, 'The Whidbey Island Wild Cats'),
(3005, 'RoboChargers'),
(3147, 'Munster HorsePower'),
(3196, 'Team SPORK'),
(3197, 'HexHounds'),
(3229, 'Hawktimus Prime'),
(3230, 'PrototypeX'),
(3234, 'Red Arrows'),
(3310, 'Black Hawk Robotics'),
(3336, 'Zimanators'),
(3339, 'BumbleB'),
(3374, 'RoboBroncs'),
(3402, 'ROBOMonkeys'),
(3459, 'Team PyroTech'),
(3506, 'YETI Robotics'),
(3546, 'Buc\'n\'Gears'),
(3618, 'Petoskey Paladins'),
(3641, 'The Flying Toasters'),
(3656, 'Dreadbots'),
(3661, 'RoboWolves'),
(3680, 'Elemental Dragons'),
(3737, 'Roto-Raptors'),
(3763, '4-H WildCards'),
(3770, 'BlitzCreek'),
(3822, 'Neon Jets'),
(3845, 'Yellow Jackets'),
(3925, 'Circuit of Life'),
(3930, 'SMART  Spruce Mountain Area Robotics Team'),
(3965, 'Sultans'),
(3971, 'Kai Orbus'),
(3990, 'Tech for Kids'),
(4010, 'Nautilus'),
(4061, 'SciBorgs'),
(4087, 'Falcon Robotics'),
(4180, 'Iron Riders'),
(4188, 'Columbus Space Program'),
(4290, 'Bots of War'),
(4291, 'KerrBots (pronounced CarBots)'),
(4329, 'Lutheran Roboteers'),
(4335, 'Metallic Clouds'),
(4455, 'The Burger Bots'),
(4469, 'R.A.I.D. (Raider Artificial Intelligence DIvision)'),
(4534, 'Wired Wizards'),
(4561, 'TerrorBytes'),
(4646, 'Team ASAP'),
(4767, 'C4'),
(4795, 'EastBots'),
(4816, 'Gadget Girls'),
(4828, 'RoboEagles'),
(4829, 'Titanium Tigers'),
(4910, 'East Cobb Robotics'),
(4929, 'Maroon Monsoon'),
(4935, 'T-Rex'),
(5160, 'Chargers'),
(5190, 'Green Hope Falcons'),
(5199, 'Robot Dolphins From Outer Space'),
(5279, 'Bionic Eagles'),
(5406, 'Celt-X'),
(5446, 'Pink Detectives'),
(5511, 'Cortechs Robotics'),
(5518, 'Techno Wolves'),
(5544, 'SWIFT Robotics'),
(5607, 'Team Firewall'),
(5679, 'Girls on Fire'),
(5727, 'REaCH Omegabytes'),
(5762, 'Franklinbots'),
(5803, 'Apex Robotics'),
(5834, 'R3P2'),
(5837, 'Unity4Tech'),
(5854, 'GLITCH'),
(5892, 'High Energy'),
(5919, 'JoCo RoBos'),
(5933, 'JudgeMent Call'),
(5940, 'B.R.E.A.D.'),
(5976, 'CyberSaders'),
(5979, 'Apex'),
(6003, 'S.U.M. (Singularly Unified Madness)'),
(6004, 'f(x) Robotics'),
(6016, 'Tiger Robotics'),
(6034, 'Eagle Storms'),
(6214, 'Los Creadores'),
(6215, 'Armored Eagles'),
(6332, 'Bull City Botics'),
(6500, 'GearCats'),
(6502, 'DARC SIDE'),
(6565, 'Team Bobcat'),
(6639, 'Mechanical Minds'),
(6729, 'RECHS Eagles');

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
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
