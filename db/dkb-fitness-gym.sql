-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 30, 2025 at 10:06 AM
-- Server version: 10.6.15-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dkb-fitness-gym`
--

-- --------------------------------------------------------

--
-- Table structure for table `users_auth`
--

CREATE TABLE `users_auth` (
  `auth_id` int(11) UNSIGNED NOT NULL,
  `auth_username` varchar(255) NOT NULL,
  `auth_password` varchar(255) NOT NULL,
  `auth_role` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users_auth`
--

INSERT INTO `users_auth` (`auth_id`, `auth_username`, `auth_password`, `auth_role`) VALUES
(1, 'admin', '$2y$10$mMLSptmWmjnp2aCJvOwAXuExFL6jmRyCrP1DreCTY2Vmx/nVNNi0K', 1),
(2, 'admin2', '$2y$10$mMLSptmWmjnp2aCJvOwAXuExFL6jmRyCrP1DreCTY2Vmx/nVNNi0K', 1),
(3, 'admin3', '$2y$10$9dp1VzeMN/TTIex0dfOD5.1MFr5.43Apz3vL2NxPooiE5jTlYLFj2', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users_log`
--

CREATE TABLE `users_log` (
  `log_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `log_time_in` datetime NOT NULL,
  `log_time_out` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users_log`
--

INSERT INTO `users_log` (`log_id`, `user_id`, `log_time_in`, `log_time_out`) VALUES
(4, 10, '2025-08-29 22:51:40', '2025-08-29 22:52:28'),
(6, 10, '2025-08-30 08:21:06', '2025-08-30 14:13:27');

-- --------------------------------------------------------

--
-- Table structure for table `users_membership`
--

CREATE TABLE `users_membership` (
  `mem_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `mem_type` varchar(255) NOT NULL,
  `mem_start_date` date NOT NULL,
  `mem_end_date` date NOT NULL,
  `reminder_sent` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users_membership`
--

INSERT INTO `users_membership` (`mem_id`, `user_id`, `mem_type`, `mem_start_date`, `mem_end_date`, `reminder_sent`) VALUES
(1, 1, 'Monthly', '2025-08-15', '2025-09-16', 0),
(2, 2, 'Monthly', '2025-07-29', '2025-08-29', 0),
(3, 3, 'Monthly', '2025-07-30', '2025-08-30', 0),
(4, 4, 'Monthly', '2025-07-29', '2025-08-29', 0),
(5, 5, 'Monthly', '2025-08-22', '2025-09-22', 0),
(6, 6, 'Monthly', '2025-07-30', '2025-08-30', 0),
(7, 7, 'Monthly', '2025-07-21', '2025-08-21', 0),
(8, 8, 'Monthly', '2025-08-19', '2025-09-19', 0),
(9, 9, 'Monthly', '2025-07-14', '2025-08-14', 0),
(10, 10, 'Monthly', '2025-07-31', '2026-07-31', 0),
(11, 11, 'Monthly', '2025-08-09', '2025-09-09', 0),
(12, 12, 'Monthly', '2025-07-30', '2025-08-30', 0),
(13, 13, 'Monthly', '2025-07-11', '2025-08-11', 0),
(14, 14, 'Monthly', '2025-07-14', '2025-08-14', 0),
(15, 15, 'Monthly', '2025-07-28', '2025-08-30', 0),
(16, 16, 'Monthly', '2025-07-30', '2025-08-30', 0),
(17, 17, 'Monthly', '2025-07-21', '2025-08-21', 0),
(18, 18, 'Monthly', '2025-07-28', '2025-08-30', 0),
(19, 19, 'Monthly', '2025-07-28', '2025-08-28', 0),
(20, 20, 'Monthly', '2025-07-23', '2025-08-30', 0),
(21, 21, 'Monthly', '2025-08-01', '2025-09-01', 0),
(22, 22, 'Monthly', '2025-07-29', '2025-08-30', 0),
(23, 23, 'Monthly', '2025-08-25', '2025-09-25', 0),
(24, 24, 'Monthly', '2025-08-07', '2025-09-12', 0),
(25, 25, 'Monthly', '2025-08-01', '2025-09-01', 0),
(26, 26, 'Monthly', '2025-07-13', '2025-09-13', 0),
(27, 27, 'Monthly', '2025-07-07', '2025-08-07', 0),
(28, 28, 'Monthly', '2025-08-29', '2025-09-29', 0),
(29, 29, 'Monthly', '2025-08-25', '2025-09-30', 0),
(30, 30, 'Monthly', '2025-08-09', '2025-09-09', 0),
(31, 31, 'Monthly', '2025-08-04', '2025-09-04', 0),
(32, 32, 'Monthly', '2025-08-28', '2025-09-28', 0),
(33, 33, 'Monthly', '2025-08-03', '2025-09-03', 0),
(34, 34, 'Monthly', '2025-08-02', '2025-09-02', 0),
(35, 35, 'Monthly', '2025-08-03', '2025-09-03', 0),
(36, 36, 'Monthly', '2025-08-18', '2025-09-18', 0),
(37, 37, 'Monthly', '2025-08-20', '2025-09-20', 0),
(38, 38, 'Monthly', '2025-01-01', '2033-01-01', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users_user`
--

CREATE TABLE `users_user` (
  `user_id` int(11) UNSIGNED NOT NULL,
  `user_fname` varchar(255) DEFAULT NULL,
  `user_mname` varchar(255) DEFAULT NULL,
  `user_lname` varchar(255) DEFAULT NULL,
  `user_suffix` varchar(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `user_height` int(50) NOT NULL,
  `user_weight` int(50) NOT NULL,
  `user_birthday` varchar(255) NOT NULL,
  `user_gender` varchar(11) NOT NULL,
  `user_address` varchar(255) NOT NULL,
  `user_contact` varchar(255) NOT NULL,
  `user_status` varchar(100) NOT NULL,
  `user_image` varchar(500) NOT NULL,
  `user_rfid` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users_user`
--

INSERT INTO `users_user` (`user_id`, `user_fname`, `user_mname`, `user_lname`, `user_suffix`, `user_email`, `user_height`, `user_weight`, `user_birthday`, `user_gender`, `user_address`, `user_contact`, `user_status`, `user_image`, `user_rfid`) VALUES
(1, 'Niel Andrew', 'N', 'De Guzman', '', 'nielandreweguzman@gmail.com', 167, 55, '2005-06-19', 'Male', 'Licaong', '9297923332', 'inactive', 'default.png', '1258803490'),
(2, 'Ardee', 'V', 'Daniel', '', 'ardeev.daniel@gmail.com', 171, 82, '2006-05-16', 'Male', 'Brgy. Licaong, Science City of Munoz', '9276717748', 'inactive', 'default.png', '1262756050'),
(3, 'Christian Jairus', '', 'Sanchez', '', 'christiabjairuz9@gmail.com', 172, 74, '2005-10-21', 'Male', 'BUKANG LIWAYWAY, SCM', '9565912076', 'inactive', 'default.png', '1260864194'),
(4, 'Cyy', '', 'Biendima', '', 'calyx15@ymail.com', 163, 65, '1993-03-15', 'Female', 'Abar 1st, San Jose City, Nueva Ecij', '9278298540', 'inactive', 'default.png', '711350788'),
(5, 'Joshua', '', 'Superio', '', 'superiojosh@gmail.com', 168, 75, '1994-08-02', 'Male', 'BANTUG', '9618353660', 'inactive', 'default.png', '3878600964'),
(6, 'Keenan Barry', 'S', 'Tablizo', '', 'keenantablizo0961@gmail.com', 170, 73, '2002-03-19', 'Male', 'brgy. Tayabo San Jose City nueva Ecija', '9614143213', 'inactive', 'default.png', '3526519300'),
(7, 'Lance', '', 'Albino', '', 'apsalbino15@gmail.com', 510, 74, '1997-07-15', 'Male', 'clsu old trailer', '9661560900', 'inactive', 'default.png', '3871794116'),
(8, 'Gem', 'M', 'Inigo', '', 'gemchelle05091927@gmail.com', 55, 68, '1990-09-14', 'Male', 'PNR CLSU', '9391622614', 'inactive', 'default.png', '1079014806'),
(9, 'Khylle', 'G', 'Nugoy', '', 'nugoykhylleg20@gmail.com', 179, 89, '2006-06-20', 'Male', 'Bagong Sikat  Munoz', '9616154253', 'inactive', 'default.png', '1259726578'),
(10, 'Joel Carl', '', 'Salazar', '', 'saazafjc@gmail.com', 175, 80, '2006-04-23', 'Male', 'Magtanggol', '9944137980', 'active', 'default.png', '123123'),
(11, 'Emmanuel', '', 'Corpuz', '', 'corpuzemmanuelbaguio@gmail.com', 170, 64, '2002-08-19', 'Male', 'Bagong Sikat', '9152800815', 'inactive', 'default.png', '1598744758'),
(12, 'Joaquin Raphael', '', 'Diaz', '', 'diaz.joquin@clsu2.edu.ph', 173, 86, '2001-06-03', 'Male', 'Imus, Cavite', '9232869182', 'inactive', 'default.png', '352255010'),
(13, 'Jules', 'Gerald M,', 'Jose', '', 'jose.jules@clsu2.edu.ph', 178, 77, '2004-07-26', 'Male', 'Cavite, guimba, Nueva Ecija', '9610198141', 'inactive', 'default.png', '3326489086'),
(14, 'Raymond', '', 'Luna', '', 'lunaaaraymond@gmail.com', 180, 78, '2003-09-15', 'Male', 'Cabanatuan City', '9560727864', 'inactive', 'default.png', '1887066301'),
(15, 'Louise Zane', '', 'De Jesus', '', 'louis.dejesus@clsu2.edu.ph', 170, 70, '2003-06-24', 'Male', 'Gapan', '9976938684', 'inactive', 'default.png', '4158367933'),
(16, 'Mark Gerald', '', 'Murillo', '', 'markgerald.murillo@gmail.com', 56, 85, '1995-08-12', 'Male', 'STO. DOMINGO', '9669761992', 'inactive', 'default.png', '3870776612'),
(17, 'Wyenne Lenard', 'D.C.', 'Villanueva', '', 'wyennevillanueava123@gmail.com', 169, 73, '2008-09-26', 'Male', 'Bagong Sikat Science City of Munoz, Nueva Ecija', '9666313399', 'inactive', 'default.png', '3870321412'),
(18, 'Princess Mika', 'O', 'Flores', '', '027FMIKAY@GMAIL.COM', 52, 47, '2006-02-27', 'Female', 'PINGOL BOARDING HOUSE, BUKANG LIWAYWAY, BANTUG', '9667018758', 'inactive', 'default.png', '1256200754'),
(19, 'Edrian Malcolm', 'D', 'Agustin', '', 'AGUSTINEDRIAN539@GMAIL.COM', 53, 56, '2006-09-04', 'Male', 'PINGOL BOARDING HOUSE BUKANG LIWAYWAY BANTUG', '9928248148', 'inactive', 'default.png', '3871396628'),
(20, 'Loewin Jon', '', 'Villanueva', '', 'lj0bsididian@gmail.com', 170, 77, '2003-07-20', 'Male', 'Science City of Munoz Nueva Ecija', '9641621923', 'inactive', 'default.png', '4158067837'),
(21, 'Martin Amiel', 'A', 'Ysais', '', 'MARTINAMIELYSAIS@GMAIL.COM', 170, 69, '2006-07-25', 'Male', 'STA. MONICA, CONCEPCION, TARLAC', '9930650452', 'inactive', 'default.png', '367320466'),
(22, 'John Andrei', '', 'Bautista', '', 'JOHNANDREI136@GMAIL.COM', 183, 65, '2006-02-04', 'Male', 'CONCEPCION TARLAC', '9924671180', 'inactive', 'default.png', '1261376962'),
(23, 'Jayvee', 'A', 'Garcia', '', 'jayveegarcia94@gmail.com', 160, 55, '1994-04-28', 'Male', 'MALIGAYA, N.E.', '9615963724', 'inactive', 'default.png', '263479556'),
(24, 'Nes Linn Paul', '', 'Viloria', '', 'NESLINN@ICLOUD.COM', 150, 70, '1998-07-12', 'Male', '378 D. DELOS SANTOS ST, MUNOZ', '9997124012', 'inactive', 'default.png', '1077833814'),
(25, 'Klein Arjay', 'L', 'Candelario', '', 'kleinarjaycandelario@gmail.com', 158, 56, '2007-04-04', 'Male', 'BAGONG SIKAT, SCM', '9618491093', 'inactive', 'default.png', '3871046916'),
(26, 'Matt Calgae', 'S', 'Feria', '', 'MATTFERIA777@GMAIL.COM', 164, 89, '2003-12-03', 'Male', 'ROSEVILLE, SAN JOSE CITY', '9663992453', 'inactive', 'default.png', '1887767469'),
(27, 'Daniel', 'T', 'Manuel', '', 'LUZMANUEL5380@GMAIL.COM', 183, 85, '2006-12-03', 'Male', 'CLSU DORM 6', '9916151104', 'inactive', 'default.png', '3871361092'),
(28, 'Rence Jio Smith', 'D', 'Bal-ot', '', 'rennce.jio.smith@gmail.com', 170, 66, '2004-03-02', 'Male', 'LICAONG SCM', '09534271345', 'inactive', '28-j0yLzP.jpg', '123'),
(29, 'Jason', 'Dc', 'Rabang', '', 'rabangjason57@gmail.com', 1722, 78, '2004-10-28', 'Male', '10 ZONE 1,ABAR 2ND,SJC, NE', '9947323858', 'inactive', 'default.png', '3959267625'),
(30, 'Jerico', 'L', 'Gammad', '', 'JERICO.GAMMAD.05.03@GMAIL.COM', 163, 75, '2003-11-05', 'Male', 'BANTUG SCIENCE CITY OF MUNOZ, NE', '9608398189', 'inactive', 'default.png', '2060409485'),
(31, 'Elijah Lee', '', 'Marcelo', '', 'gwenjoseelijah.marcelo@clsu2.edu.ph', 511, 79, '2005-03-20', 'Male', 'Zone 1, Sto Tomas, SJC', '9455223937', 'inactive', 'default.png', '2015431260'),
(32, 'Almira Rose', '', 'Corpuz', '', 'CORPUSRALMIRAROSE16@GMAIL.COM', 180, 60, '2006-06-09', 'Female', 'BANTUG, SCM, N.E', '9673022882', 'inactive', 'default.png', '1255666530'),
(33, 'Kyra Sophia', '', 'Dumale', '', 'KYRASOPHIAD@MAIL.COM', 162, 64, '2000-03-05', 'Female', 'ZONE 2, LICAONG, SCM, NE', '9278614962', 'inactive', 'default.png', '3871319028'),
(34, 'Earl John Andrew', 'F', 'Valentos', '', 'VALENTOS.EARL@CLSU2.EDU.PH', 170, 74, '2005-02-02', 'Male', 'BAGONG SIKAT, MUNOZ', '9916151397', 'inactive', 'default.png', '3959139737'),
(35, 'Ruvina', 'T', 'Castillo', '', 'CASTILLORUVINA08@GMAIL.COM', 154, 63, '2000-10-08', 'Female', 'BAGONG SIKAT, SCM', '9283079846', 'inactive', 'default.png', '1077985958'),
(36, 'Brian Reiley', '', 'Santiago', '', 'REILEY.SANTIAGO@GMAIL.COM', 172, 80, '2002-12-07', 'Male', 'FRANZA', '9294924603', 'inactive', 'default.png', '3979370756'),
(37, 'Christopher', '', 'Geranta', '', 'CGGERANTA@GMAIL.COM', 173, 87, '1998-12-04', 'Male', 'TALAVERA', '9605832618', 'inactive', 'default.png', '415506692'),
(38, 'Arnel', '', 'Nueda', '', 'ARNELNUEDA006@GMAIL.COM', 175, 73, '1991-08-06', 'Male', 'CLSU', '9563676153', 'inactive', 'default.png', '479920388');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users_auth`
--
ALTER TABLE `users_auth`
  ADD PRIMARY KEY (`auth_id`);

--
-- Indexes for table `users_log`
--
ALTER TABLE `users_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `fk_users_log_user` (`user_id`);

--
-- Indexes for table `users_membership`
--
ALTER TABLE `users_membership`
  ADD PRIMARY KEY (`mem_id`),
  ADD KEY `fk_users_mem_user` (`user_id`);

--
-- Indexes for table `users_user`
--
ALTER TABLE `users_user`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users_auth`
--
ALTER TABLE `users_auth`
  MODIFY `auth_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `users_log`
--
ALTER TABLE `users_log`
  MODIFY `log_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users_membership`
--
ALTER TABLE `users_membership`
  MODIFY `mem_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `users_user`
--
ALTER TABLE `users_user`
  MODIFY `user_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=158;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `users_log`
--
ALTER TABLE `users_log`
  ADD CONSTRAINT `fk_users_log_user` FOREIGN KEY (`user_id`) REFERENCES `users_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users_membership`
--
ALTER TABLE `users_membership`
  ADD CONSTRAINT `fk_users_mem_user` FOREIGN KEY (`user_id`) REFERENCES `users_user` (`user_id`) ON DELETE CASCADE ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
