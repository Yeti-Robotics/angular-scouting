-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Mar 27, 2018 at 11:40 PM
-- Server version: 10.1.9-MariaDB
-- PHP Version: 5.6.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `yeti_scouting`
--

--
-- Dumping data for table `scouters`
--

INSERT INTO `scouters` (`id`, `name`, `username`, `pswd`, `byteCoins`, `team_number`) VALUES
(2, 'Rahul Guha', 'rahulguha3', 'b76110a68e15d6cfa3fd052bb2f4098e', 200, 3506),
(3, 'Iman', 'dimanvb', '843ba9c2506ddaf5b000d31ba537b13f', 200, 3506),
(4, 'Madeline', 'Maddy', '935937a3cfc17c5556e77d65aa025a5a', 200, 3506),
(5, 'Antoine', 'antoine', '0e5091a25295e44fea9957638527301f', 200, 3506),
(6, 'Alex', 'attatt', '4015605f9a7f7cc22f74efed4db3d530', 200, 3506),
(7, 'Trenton DeSear', 'PhilSwift', 'f5083dfbcc1f875d34a9b37676fef159', 200, 3506),
(8, 'Jacob', 'JacobT', 'daa3e3765d5fb20bae9ddd92326fd91c', 200, 3506),
(9, 'Ethan', 'ethan', 'bad5f33780c42f2588878a9d07405083', 200, 3506),
(10, 'Kyle', 'BrightKnight', '2e270f864f514ecc6bd06caf7156810b', 200, 3506),
(11, 'Chalmer', 'ChalmerYung ( âœ§â‰– ÍœÊ–â‰–)', '142e82999359d78b8794334d19ca7598', 200, 4290),
(12, 'Marco Barrientos', 'marcobarrientos', '77a5a2e7fd54bf624ac76cf7e446fa3d', 200, 3506),
(13, '144', 'Sam D', '554ff1034ba717129a2f14c936f7c346', 200, 3506),
(14, 'Denali', 'Denali', '5f4dcc3b5aa765d61d8327deb882cf99', 200, 3506),
(15, 'Jonathan Gamble', 'jgamble', '94df3a32a434197a3425b244711a40d1', 200, 4290),
(16, 'Robbie', 'Robbie', '87b7cb79481f317bde90c116cf36084b', 200, 3506),
(17, 'Sid Agarwal', 'sidagarwal', '15acfd400b0f81a3ca39d4131e9779e8', 200, 3506),
(18, 'Finn', 'Finn', '070f17ca0cf78e295c94eb56825f8233', 200, 4290),
(19, 'Hussan pari', 'Hpari', 'd686ac39c89c97effea5f44bdbf11755', 200, 4290),
(20, 'Samantha', 'samosorio', '3cfd9644ba49c589630c390f307a0f8a', 200, 4290),
(21, 'Si', 'Sithu', '670b14728ad9902aecba32e22fa4f6bd', 200, 4290),
(22, 'Axel', 'AxeMax321', 'be75ae7a1a8137cae28578b29d15abbe', 200, 4290),
(23, 'Chalmer', 'ChalmerYung', '142e82999359d78b8794334d19ca7598', 200, 4290),
(24, 'Archibald Ademu-John', 'archie8321', '98883186bdcfe8c4cb88e7bca1cc9f75', 200, 4290),
(25, 'Jared Wagler', 'Jard', 'ac49412d58f2ecb9e588425336f5153c', 200, 3506),
(26, 'Sam Perlmutter', 'sperlmutter', '27289742c6611ec962143d977b8ac0cc', 200, 3506),
(27, 'Matthew Tanner', 'Mdanieltanner', 'f28d1bcd464cd762e267564535c4a13d', 200, 6894),
(28, 'Admin McCoolpants', 'admin', '5f4dcc3b5aa765d61d8327deb882cf99', 200, 4290),
(29, 'Nickie Tang', 'Nickie_tang', 'a7b27f2126c7df81fa95708c4cb616ca', 200, 4290),
(30, 'Conner', 'Icejava6894', 'be0de0e8b27086c1cc0b5c610163fc5e', 200, 6894),
(31, 'Conner', 'Iced java6894', 'be0de0e8b27086c1cc0b5c610163fc5e', 200, 6894),
(32, 'Conner', 'icedjava', '69ec5030f78a9b735402d133317bf5f6', 200, 6894),
(33, 'Bunni', 'Bunmi', 'e8d25b4208b80008a9e15c8698640e85', 200, 6894),
(34, 'Alexander Ademu-John', 'AlextheAfrican', '9727892038b07b6ddc477e4193adadf2', 200, 4290),
(35, 'Frederick C.', 'A human', 'fcbddb5c02ca0dd8828ea66cc1bd037b', 200, 4290),
(36, 'Anastasia', 'ADai', '1659063f5304bdf0008c758985ea2d87', 200, 3506),
(37, 'Kal', 'Kal Cummiskey', 'f8f66559809838a1f7385f1cb7fa0cc7', 200, 3506),
(38, 'YaSeen Von Briesen', 'Yaseenvn', 'de06726b3313ed5c61cadf72bb832fdf', 200, 3506),
(39, 'Denali', 'DenaliT', '5f4dcc3b5aa765d61d8327deb882cf99', 200, 3506),
(40, 'Jacob Termin', 'Jacob.Termin', 'daa3e3765d5fb20bae9ddd92326fd91c', 200, 3506),
(41, 'Yaseen', 'yaseenvb', 'de06726b3313ed5c61cadf72bb832fdf', 200, 3506),
(42, 'RahulGuha', 'RahulGuha', 'b76110a68e15d6cfa3fd052bb2f4098e', 200, 3506),
(43, 'Mahmoud Banawan', 'mahmoudbanawan', 'c39770f1cb7d77ce042b7c4d78482607', 200, 3506),
(44, 'Alex G', 'agray', 'e12c5078ba1af4ed83c0abe2bcc522e1', 200, 3506),
(45, 'Samuel', 'samsterwey', 'ec8afd83cb02651a24a3a5bcd9b09847', 200, 3506),
(46, 'Charlotte', 'CharlottePair', '41471e70880bdc238c1d7beb15376d07', 200, 3506),
(47, 'Colin Evans', 'ColinE', '4d79d8925965e454fad7f004132bffc5', 200, 3506),
(48, 'James', 'Ramich', 'b5f89fbf2524dc4f8295f3da0f2c5b18', 200, 3506),
(49, 'Sunny', 'itâ€™ssunnytoday', '41e014ba456d8f9d42b600f2610a2820', 200, 3506),
(50, 'Sunny', 'Sunnyboo', '533c5ba8368075db8f6ef201546bd71a', 200, 3506),
(51, 'Major Kirby', 's', '18fc9c6baeabe5f323c9dab371901e31', 200, 3506),
(52, 'Samuel dauber', 'Dauber 2.0', '554ff1034ba717129a2f14c936f7c346', 200, 3506),
(53, 'Anna', 'Annavanburen', '0b4c0c69a74e1d358bc361e44f51a1ae', 200, 3506),
(54, 'Kevin Colwell', 'colwellkr@gmail.com', '50d03b27f687c143e15c205b77831c21', 200, 3506);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
