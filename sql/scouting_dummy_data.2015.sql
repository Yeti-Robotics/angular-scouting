-- phpMyAdmin SQL Dump
-- version 3.3.10.4
-- http://www.phpmyadmin.net
--
-- Host: mysql.yetirobotics.org
-- Generation Time: Apr 19, 2015 at 01:09 PM
-- Server version: 5.1.56
-- PHP Version: 5.4.37

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `yetirobotics_org_scouting`
--

--
-- Dumping data for table `scout_data`
--

INSERT INTO `scout_data` (`scout_data_id`, `team`, `match_number`, `comments`, `robot_moved`, `totes_auto`, `cans_auto`, `coopertition`, `coopertition_totes`, `score`, `rating`, `timestamp`, `name`, `cans_from_middle`, `totes_from_landfill`, `totes_from_human`, `cans_auto_origin`) VALUES
(53, 392, 91, 'Doesn''t even try to cap', 1, 3, 0, 0, 0, 60, 1, '2015-04-19 12:57:43', 'Sam', 0, 1, 1, 0),
(54, 907, 12, 'Average match, slow stacker', 1, 1, 0, 0, 0, 200, 5, '2015-04-19 12:58:12', 'Antoine', 1, 0, 1, 0),
(55, 3506, 9, 'Can consistently cap stacks of 3-4 totes with litter', 1, 0, 1, 0, 0, 89, 7, '2015-04-19 12:58:47', 'Trevor', 1, 1, 1, 0),
(56, 2442, 1, 'BEST ROBOT EVARRR', 1, 3, 3, 0, 2, 212, 10, '2015-04-19 12:58:48', 'matt', 1, 1, 0, 0),
(57, 392, 92, 'Spent the whole match trying to do coopertition', 1, 1, 0, 0, 3, 100, 7, '2015-04-19 12:58:50', 'Sam', 0, 1, 0, 0),
(58, 2442, 2, 'Didn''t move, drive team cheered and moved the joysticks to whole time', 0, 0, 0, 0, 0, 12, 1, '2015-04-19 12:59:40', 'Matt', 0, 0, 0, 0),
(59, 907, 151, 'Average match, lots of small stacks this time', 0, 0, 0, 0, 2, 250, 6, '2015-04-19 13:00:28', 'Antoine', 1, 0, 1, 0),
(60, 2442, 3, 'Broke down halfway through', 1, 1, 2, 0, 1, 53, 7, '2015-04-19 13:00:37', 'Matt', 1, 1, 0, 2),
(61, 392, 93, 'Focused a lot on stacking, but it''s really good at it. Doesn''t seem to be able to cap', 1, 3, 0, 0, 0, 60, 10, '2015-04-19 13:01:00', 'Sam', 0, 1, 1, 0),
(62, 907, 789, 'Average match, wasted time trying to cap stacks', 1, 2, 0, 0, 2, 260, 6, '2015-04-19 13:01:20', 'Antoine', 1, 0, 1, 0),
(63, 2442, 4, 'BEST ROBOT EVARRRR', 1, 3, 3, 0, 2, 213, 10, '2015-04-19 13:01:43', 'Matt', 1, 1, 0, 0),
(64, 3506, 48, 'Great can burglar mechanism; able to retrieve 2 cans from step at once', 1, 0, 1, 0, 0, 79, 8, '2015-04-19 13:02:01', 'Trevor', 1, 1, 0, 1),
(65, 907, 999, 'Robot did not move battery died', 0, 0, 0, 0, 0, 0, 1, '2015-04-19 13:02:32', 'Antoine', 0, 0, 0, 0),
(66, 392, 94, 'Spent most of match getting coopertition, but still made 2 stacks of 4', 1, 3, 0, 0, 3, 150, 5, '2015-04-19 13:02:43', 'Sam', 0, 0, 0, 0),
(67, 3506, 57, 'Able to stack up to 4 totes with cans from step', 1, 0, 2, 0, 0, 101, 8, '2015-04-19 13:03:33', 'Trevor', 1, 1, 0, 2),
(68, 907, 1002, 'Average match, slow stacking, but stacks of 4 at least', 1, 0, 3, 0, 0, 200, 5, '2015-04-19 13:03:58', 'Antoine', 0, 1, 0, 0),
(69, 3506, 81, 'Focuses on capping with litter; Able to cap up to 4 totes', 1, 0, 1, 0, 0, 91, 7, '2015-04-19 13:05:09', 'Trevor', 1, 0, 0, 0);

--
-- Dumping data for table `stacks`
--

INSERT INTO `stacks` (`scout_data_id`, `totes`, `cap_state`) VALUES
(53, 3, 0),
(53, 5, 0),
(53, 6, 0),
(54, 4, 1),
(54, 3, 2),
(55, 3, 2),
(55, 3, 2),
(56, 6, 2),
(56, 4, 2),
(56, 4, 1),
(59, 2, 1),
(59, 2, 1),
(59, 2, 0),
(59, 3, 0),
(59, 2, 0),
(60, 2, 1),
(60, 3, 2),
(61, 5, 0),
(61, 5, 0),
(61, 6, 0),
(62, 4, 0),
(62, 3, 0),
(62, 4, 0),
(62, 4, 0),
(63, 6, 2),
(63, 4, 2),
(63, 4, 2),
(63, 4, 2),
(64, 4, 2),
(64, 3, 1),
(66, 4, 0),
(66, 4, 0),
(67, 4, 2),
(67, 4, 2),
(67, 1, 0),
(68, 4, 0),
(68, 4, 0),
(68, 4, 0),
(69, 3, 1),
(69, 4, 2);
