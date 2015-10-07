-- phpMyAdmin SQL Dump
-- version 4.4.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Sep 29, 2015 at 02:57 AM
-- Server version: 5.6.26
-- PHP Version: 5.6.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `scouting`
--

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

--
-- Dumping data for table `scouters`
--

INSERT INTO `scouters` (`name`, `id`, `pswd`, `byteCoins`) VALUES
('Katie Beekman',1,'447',200)
,('Burgin Bentley',3,'354',200)
,('Jonathon Benton',4,'628',200)
,('Mitch Berthelot',5,'531',200)
,('Asher Bond',2,'166',200)
,('Tyler Crump',8,'445',200)
,('Trevor Daino',9,'329',200)
,('Chris Dauber',6,'910',200)
,('Josiah Dumitrescu',11,'795',200)
,('Delaney Dunlap',12,'958',200)
,('Paul Keller',17,'871',200)
,('Caehlin Kelly',7,'474',200)
,('Kyla Kelly',10,'317',200)
,('JooHyun (James) Koh',18,'252',200)
,('Garren Lamprinakos',13,'373',200)
,('Chris Lee',14,'870',200)
,('Matthew Leonard',19,'881',200)
,('Antonio Mendoza',15,'485',200)
,('Nathen Munyak',16,'245',200)
,('Tanisha Paul',22,'435',200)
,('Sam Perlmutter',23,'556',200)
,('Sophia Schwinghammer',25,'912',200)
,('Ben Sellers',20,'968',200)
,('William Sommerville',26,'915',200)
,('Arthur Valdman',28,'540',200)
,('JP van Buren',29,'363',200)
,('Isabella von Briesen',30,'822',200)
,('Iman von Briesen',31,'321',200)
,('Jared Wagler',21,'880',200)
,('JR Walker',33,'864',200)
,('Maddy Yara',34,'157',200)
,('tester testman',0,'0',999);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
