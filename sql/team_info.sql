-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: mysql.yetirobotics.org
-- Generation Time: Feb 03, 2018 at 12:48 PM
-- Server version: 5.6.34-log
-- PHP Version: 7.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `yetirobotics_org_scouting`
--

--
-- Dumping data for table `team_info`
--

INSERT IGNORE INTO `team_info` VALUES(8, 'Team 8');
INSERT IGNORE INTO `team_info` VALUES(25, 'Raider Robotix');
INSERT IGNORE INTO `team_info` VALUES(166, 'Chop Shop');
INSERT IGNORE INTO `team_info` VALUES(191, 'X-CATS');
INSERT IGNORE INTO `team_info` VALUES(238, 'Cruisin\' Crusaders');
INSERT IGNORE INTO `team_info` VALUES(287, 'Floyd');
INSERT IGNORE INTO `team_info` VALUES(321, 'RoboLancers');
INSERT IGNORE INTO `team_info` VALUES(339, 'Kilroy Robotics');
INSERT IGNORE INTO `team_info` VALUES(379, 'RoboCats');
INSERT IGNORE INTO `team_info` VALUES(422, 'Mech Tech Dragons');
INSERT IGNORE INTO `team_info` VALUES(435, 'Robodogs');
INSERT IGNORE INTO `team_info` VALUES(587, 'Hedgehogs');
INSERT IGNORE INTO `team_info` VALUES(639, 'Code Red Robotics');
INSERT IGNORE INTO `team_info` VALUES(694, 'StuyPulse');
INSERT IGNORE INTO `team_info` VALUES(836, 'The RoboBees');
INSERT IGNORE INTO `team_info` VALUES(858, 'Demons');
INSERT IGNORE INTO `team_info` VALUES(876, 'Thunder Robotics');
INSERT IGNORE INTO `team_info` VALUES(900, 'â™žThe Zebracornsâ™ž');
INSERT IGNORE INTO `team_info` VALUES(955, 'CV Robotics');
INSERT IGNORE INTO `team_info` VALUES(1073, 'The Force Team');
INSERT IGNORE INTO `team_info` VALUES(1089, 'Team Mercury');
INSERT IGNORE INTO `team_info` VALUES(1123, 'AIM Robotics');
INSERT IGNORE INTO `team_info` VALUES(1225, 'Gorillas');
INSERT IGNORE INTO `team_info` VALUES(1261, 'Robo  Lions');
INSERT IGNORE INTO `team_info` VALUES(1389, 'The Body Electric');
INSERT IGNORE INTO `team_info` VALUES(1511, 'Rolling Thunder');
INSERT IGNORE INTO `team_info` VALUES(1533, 'Triple Strange');
INSERT IGNORE INTO `team_info` VALUES(1600, 'ROBO KINGS AND QUEENS');
INSERT IGNORE INTO `team_info` VALUES(1662, 'Raptor Force Engineering');
INSERT IGNORE INTO `team_info` VALUES(1868, 'Space Cookies');
INSERT IGNORE INTO `team_info` VALUES(1885, 'ILITE Robotics');
INSERT IGNORE INTO `team_info` VALUES(1983, 'Skunk Works Robotics');
INSERT IGNORE INTO `team_info` VALUES(1991, 'Dragons');
INSERT IGNORE INTO `team_info` VALUES(2059, 'The Hitchhikers');
INSERT IGNORE INTO `team_info` VALUES(2080, 'Torbotics');
INSERT IGNORE INTO `team_info` VALUES(2168, 'Aluminum Falcons');
INSERT IGNORE INTO `team_info` VALUES(2175, 'The Fighting Calculators');
INSERT IGNORE INTO `team_info` VALUES(2219, 'Megahurts');
INSERT IGNORE INTO `team_info` VALUES(2220, 'Blue Twilight');
INSERT IGNORE INTO `team_info` VALUES(2557, 'SOTAbots');
INSERT IGNORE INTO `team_info` VALUES(2640, 'HOTBOTZ');
INSERT IGNORE INTO `team_info` VALUES(2642, 'Pitt Pirates');
INSERT IGNORE INTO `team_info` VALUES(2655, 'The Flying Platypi');
INSERT IGNORE INTO `team_info` VALUES(2682, 'Boneyard Robotics');
INSERT IGNORE INTO `team_info` VALUES(2848, 'The All Sparks');
INSERT IGNORE INTO `team_info` VALUES(2883, 'F.R.E.D (Fighting Rednecks Engineering and Design)');
INSERT IGNORE INTO `team_info` VALUES(2974, 'Walton Robotics');
INSERT IGNORE INTO `team_info` VALUES(2980, 'The Whidbey Island Wild Cats');
INSERT IGNORE INTO `team_info` VALUES(3005, 'RoboChargers');
INSERT IGNORE INTO `team_info` VALUES(3147, 'Munster HorsePower');
INSERT IGNORE INTO `team_info` VALUES(3196, 'Team SPORK');
INSERT IGNORE INTO `team_info` VALUES(3197, 'HexHounds');
INSERT IGNORE INTO `team_info` VALUES(3229, 'Hawktimus Prime');
INSERT IGNORE INTO `team_info` VALUES(3230, 'PrototypeX');
INSERT IGNORE INTO `team_info` VALUES(3234, 'Red Arrows');
INSERT IGNORE INTO `team_info` VALUES(3310, 'Black Hawk Robotics');
INSERT IGNORE INTO `team_info` VALUES(3336, 'Zimanators');
INSERT IGNORE INTO `team_info` VALUES(3339, 'BumbleB');
INSERT IGNORE INTO `team_info` VALUES(3374, 'RoboBroncs');
INSERT IGNORE INTO `team_info` VALUES(3402, 'ROBOMonkeys');
INSERT IGNORE INTO `team_info` VALUES(3459, 'Team PyroTech');
INSERT IGNORE INTO `team_info` VALUES(3506, 'YETI Robotics');
INSERT IGNORE INTO `team_info` VALUES(3546, 'Buc\'n\'Gears');
INSERT IGNORE INTO `team_info` VALUES(3618, 'Petoskey Paladins');
INSERT IGNORE INTO `team_info` VALUES(3641, 'The Flying Toasters');
INSERT IGNORE INTO `team_info` VALUES(3656, 'Dreadbots');
INSERT IGNORE INTO `team_info` VALUES(3661, 'RoboWolves');
INSERT IGNORE INTO `team_info` VALUES(3680, 'Elemental Dragons');
INSERT IGNORE INTO `team_info` VALUES(3737, 'Roto-Raptors');
INSERT IGNORE INTO `team_info` VALUES(3763, '4-H WildCards');
INSERT IGNORE INTO `team_info` VALUES(3770, 'BlitzCreek');
INSERT IGNORE INTO `team_info` VALUES(3822, 'Neon Jets');
INSERT IGNORE INTO `team_info` VALUES(3845, 'Yellow Jackets');
INSERT IGNORE INTO `team_info` VALUES(3925, 'Circuit of Life');
INSERT IGNORE INTO `team_info` VALUES(3930, 'SMART  Spruce Mountain Area Robotics Team');
INSERT IGNORE INTO `team_info` VALUES(3965, 'Sultans');
INSERT IGNORE INTO `team_info` VALUES(3971, 'Kai Orbus');
INSERT IGNORE INTO `team_info` VALUES(3990, 'Tech for Kids');
INSERT IGNORE INTO `team_info` VALUES(4010, 'Nautilus');
INSERT IGNORE INTO `team_info` VALUES(4061, 'SciBorgs');
INSERT IGNORE INTO `team_info` VALUES(4087, 'Falcon Robotics');
INSERT IGNORE INTO `team_info` VALUES(4180, 'Iron Riders');
INSERT IGNORE INTO `team_info` VALUES(4188, 'Columbus Space Program');
INSERT IGNORE INTO `team_info` VALUES(4290, 'Bots of War');
INSERT IGNORE INTO `team_info` VALUES(4291, 'KerrBots (pronounced CarBots)');
INSERT IGNORE INTO `team_info` VALUES(4329, 'Lutheran Roboteers');
INSERT IGNORE INTO `team_info` VALUES(4335, 'Metallic Clouds');
INSERT IGNORE INTO `team_info` VALUES(4455, 'The Burger Bots');
INSERT IGNORE INTO `team_info` VALUES(4469, 'R.A.I.D. (Raider Artificial Intelligence DIvision)');
INSERT IGNORE INTO `team_info` VALUES(4534, 'Wired Wizards');
INSERT IGNORE INTO `team_info` VALUES(4561, 'TerrorBytes');
INSERT IGNORE INTO `team_info` VALUES(4646, 'Team ASAP');
INSERT IGNORE INTO `team_info` VALUES(4767, 'C4');
INSERT IGNORE INTO `team_info` VALUES(4795, 'EastBots');
INSERT IGNORE INTO `team_info` VALUES(4816, 'Gadget Girls');
INSERT IGNORE INTO `team_info` VALUES(4828, 'RoboEagles');
INSERT IGNORE INTO `team_info` VALUES(4829, 'Titanium Tigers');
INSERT IGNORE INTO `team_info` VALUES(4910, 'East Cobb Robotics');
INSERT IGNORE INTO `team_info` VALUES(4929, 'Maroon Monsoon');
INSERT IGNORE INTO `team_info` VALUES(4935, 'T-Rex');
INSERT IGNORE INTO `team_info` VALUES(5160, 'Chargers');
INSERT IGNORE INTO `team_info` VALUES(5190, 'Green Hope Falcons');
INSERT IGNORE INTO `team_info` VALUES(5199, 'Robot Dolphins From Outer Space');
INSERT IGNORE INTO `team_info` VALUES(5279, 'Bionic Eagles');
INSERT IGNORE INTO `team_info` VALUES(5406, 'Celt-X');
INSERT IGNORE INTO `team_info` VALUES(5446, 'Pink Detectives');
INSERT IGNORE INTO `team_info` VALUES(5511, 'Cortechs Robotics');
INSERT IGNORE INTO `team_info` VALUES(5518, 'Techno Wolves');
INSERT IGNORE INTO `team_info` VALUES(5544, 'SWIFT Robotics');
INSERT IGNORE INTO `team_info` VALUES(5607, 'Team Firewall');
INSERT IGNORE INTO `team_info` VALUES(5679, 'Girls on Fire');
INSERT IGNORE INTO `team_info` VALUES(5727, 'REaCH Omegabytes');
INSERT IGNORE INTO `team_info` VALUES(5762, 'Franklinbots');
INSERT IGNORE INTO `team_info` VALUES(5803, 'Apex Robotics');
INSERT IGNORE INTO `team_info` VALUES(5834, 'R3P2');
INSERT IGNORE INTO `team_info` VALUES(5837, 'Unity4Tech');
INSERT IGNORE INTO `team_info` VALUES(5854, 'GLITCH');
INSERT IGNORE INTO `team_info` VALUES(5892, 'High Energy');
INSERT IGNORE INTO `team_info` VALUES(5919, 'JoCo RoBos');
INSERT IGNORE INTO `team_info` VALUES(5933, 'JudgeMent Call');
INSERT IGNORE INTO `team_info` VALUES(5940, 'B.R.E.A.D.');
INSERT IGNORE INTO `team_info` VALUES(5976, 'CyberSaders');
INSERT IGNORE INTO `team_info` VALUES(5979, 'Apex');
INSERT IGNORE INTO `team_info` VALUES(6003, 'S.U.M. (Singularly Unified Madness)');
INSERT IGNORE INTO `team_info` VALUES(6004, 'f(x) Robotics');
INSERT IGNORE INTO `team_info` VALUES(6016, 'Tiger Robotics');
INSERT IGNORE INTO `team_info` VALUES(6034, 'Eagle Storms');
INSERT IGNORE INTO `team_info` VALUES(6214, 'Los Creadores');
INSERT IGNORE INTO `team_info` VALUES(6215, 'Armored Eagles');
INSERT IGNORE INTO `team_info` VALUES(6332, 'Bull City Botics');
INSERT IGNORE INTO `team_info` VALUES(6500, 'GearCats');
INSERT IGNORE INTO `team_info` VALUES(6502, 'DARC SIDE');
INSERT IGNORE INTO `team_info` VALUES(6565, 'Team Bobcat');
INSERT IGNORE INTO `team_info` VALUES(6639, 'Mechanical Minds');
INSERT IGNORE INTO `team_info` VALUES(6729, 'RECHS Eagles');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
