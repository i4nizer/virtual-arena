-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 01, 2024 at 05:21 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `virtual_arena`
--

-- --------------------------------------------------------

--
-- Table structure for table `match`
--

CREATE TABLE `match` (
  `id` int(11) NOT NULL,
  `p1_id` int(11) NOT NULL,
  `p2_id` int(11) NOT NULL,
  `start_dt` datetime DEFAULT NULL,
  `end_dt` datetime DEFAULT NULL,
  `auto_end` tinyint(1) NOT NULL DEFAULT 1,
  `round_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `match`
--

INSERT INTO `match` (`id`, `p1_id`, `p2_id`, `start_dt`, `end_dt`, `auto_end`, `round_id`) VALUES
(43, 56, 54, '2024-06-01 22:53:40', NULL, 1, 34),
(44, 58, 57, NULL, NULL, 1, 34),
(45, 58, 54, NULL, NULL, 1, 35);

-- --------------------------------------------------------

--
-- Stand-in structure for view `match_view`
-- (See below for the actual view)
--
CREATE TABLE `match_view` (
`tourna_id` int(11)
,`team1` varchar(255)
,`team2` varchar(255)
);

-- --------------------------------------------------------

--
-- Table structure for table `player`
--

CREATE TABLE `player` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact_no` varchar(50) NOT NULL,
  `score` int(11) NOT NULL,
  `wins` int(11) NOT NULL,
  `loses` int(11) NOT NULL,
  `creation_dt` datetime NOT NULL,
  `team_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `player`
--

INSERT INTO `player` (`id`, `name`, `email`, `contact_no`, `score`, `wins`, `loses`, `creation_dt`, `team_id`) VALUES
(47, 'P1', 'p@gmail.com', '09737583966', 0, 0, 0, '2024-06-01 22:51:16', 54),
(48, 'P2', 'p@gmail.com', '092813781723', 0, 0, 0, '2024-06-01 22:51:26', 54);

-- --------------------------------------------------------

--
-- Table structure for table `result`
--

CREATE TABLE `result` (
  `id` int(11) NOT NULL,
  `match_id` int(11) NOT NULL,
  `winner_id` int(11) NOT NULL,
  `loser_id` int(11) NOT NULL,
  `conclusion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `result`
--

INSERT INTO `result` (`id`, `match_id`, `winner_id`, `loser_id`, `conclusion`) VALUES
(17, 43, 54, 56, NULL),
(18, 44, 58, 57, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `round`
--

CREATE TABLE `round` (
  `id` int(11) NOT NULL,
  `start_dt` datetime DEFAULT NULL,
  `end_dt` datetime DEFAULT NULL,
  `tourna_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `round`
--

INSERT INTO `round` (`id`, `start_dt`, `end_dt`, `tourna_id`) VALUES
(34, '2024-05-31 14:24:46', '2024-06-01 14:26:07', 16),
(35, '2024-05-31 14:26:48', '2024-06-01 14:28:46', 16);

-- --------------------------------------------------------

--
-- Table structure for table `team`
--

CREATE TABLE `team` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `round` int(11) NOT NULL DEFAULT 1,
  `creation_dt` datetime NOT NULL,
  `tourna_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `team`
--

INSERT INTO `team` (`id`, `name`, `round`, `creation_dt`, `tourna_id`) VALUES
(54, 'Team1', 2, '2024-05-31 14:24:33', 16),
(55, 'Team2', 2, '2024-05-31 14:24:36', 16),
(56, 'Team3', 1, '2024-05-31 14:24:39', 16),
(57, 'Team4', 1, '2024-05-31 14:24:42', 16),
(58, 'Team5', 2, '2024-05-31 14:24:46', 16);

-- --------------------------------------------------------

--
-- Stand-in structure for view `team_top_player`
-- (See below for the actual view)
--
CREATE TABLE `team_top_player` (
`tourna_id` int(11)
,`team_id` int(11)
,`team_name` varchar(255)
,`team_wins` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `top10_teams`
-- (See below for the actual view)
--
CREATE TABLE `top10_teams` (
`id` int(11)
,`name` varchar(255)
,`tourna_id` int(11)
,`wins` decimal(32,0)
);

-- --------------------------------------------------------

--
-- Table structure for table `tourna`
--

CREATE TABLE `tourna` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `timezone` varchar(255) NOT NULL,
  `start_dt` datetime NOT NULL,
  `end_dt` datetime NOT NULL,
  `description` text NOT NULL,
  `creator_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tourna`
--

INSERT INTO `tourna` (`id`, `title`, `timezone`, `start_dt`, `end_dt`, `description`, `creator_id`) VALUES
(16, 'Badmint', 'Asia/Singapore', '2024-06-13 19:33:23', '2024-06-30 19:33:23', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tourna_editor`
--

CREATE TABLE `tourna_editor` (
  `id` int(11) NOT NULL,
  `role` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tourna_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tourna_editor`
--

INSERT INTO `tourna_editor` (`id`, `role`, `user_id`, `tourna_id`) VALUES
(1, 'Editor', 3, 16);

-- --------------------------------------------------------

--
-- Table structure for table `tourna_setup`
--

CREATE TABLE `tourna_setup` (
  `id` int(11) NOT NULL,
  `format` varchar(255) NOT NULL,
  `max_entry` int(11) NOT NULL,
  `max_entry_player` int(11) NOT NULL,
  `pairing` varchar(255) NOT NULL,
  `is_public` tinyint(1) NOT NULL,
  `is_open` tinyint(1) NOT NULL,
  `tourna_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tourna_setup`
--

INSERT INTO `tourna_setup` (`id`, `format`, `max_entry`, `max_entry_player`, `pairing`, `is_public`, `is_open`, `tourna_id`) VALUES
(11, 'Single Elimination', 5, 2, 'Random', 0, 0, 16);

-- --------------------------------------------------------

--
-- Table structure for table `va_user`
--

CREATE TABLE `va_user` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `psk` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `va_user`
--

INSERT INTO `va_user` (`id`, `name`, `email`, `psk`) VALUES
(1, 'ian', 'ian@gmail.com', 'b1d096f1bb57e734f8362fa66fcea92860ee9213012005ee5d7f6505de98d861'),
(3, 'mc', 'mc@gmail.com', '2c8f2c7b1be6dd82ed06b677893711e3a8be78d0aec3408e7434a3da5f237a58'),
(4, 'pia', 'pia@gmail.com', '48d06944afb3da2add7bb9f0f8dbcda6804c77de6d3d7cb12529dc07244c1452');

-- --------------------------------------------------------

--
-- Structure for view `match_view`
--
DROP TABLE IF EXISTS `match_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `match_view`  AS SELECT `t1`.`tourna_id` AS `tourna_id`, `t1`.`name` AS `team1`, `t2`.`name` AS `team2` FROM ((`match` `m` join `team` `t1` on(`m`.`p1_id` = `t1`.`id`)) join `team` `t2` on(`m`.`p2_id` = `t2`.`id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `team_top_player`
--
DROP TABLE IF EXISTS `team_top_player`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `team_top_player`  AS   (select `team`.`tourna_id` AS `tourna_id`,`team`.`id` AS `team_id`,`team`.`name` AS `team_name`,max(`player`.`wins`) AS `team_wins` from (`team` join `player` on(`player`.`team_id` = `team`.`id`)))  ;

-- --------------------------------------------------------

--
-- Structure for view `top10_teams`
--
DROP TABLE IF EXISTS `top10_teams`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `top10_teams`  AS   (select `team`.`id` AS `id`,`team`.`name` AS `name`,`team`.`tourna_id` AS `tourna_id`,sum(`player`.`wins`) AS `wins` from (`team` join `player` on(`player`.`team_id` = `team`.`id`)) group by `player`.`team_id` order by sum(`player`.`wins`) desc limit 10)  ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `match`
--
ALTER TABLE `match`
  ADD PRIMARY KEY (`id`),
  ADD KEY `p1id_fk` (`p1_id`),
  ADD KEY `p2id_fk` (`p2_id`),
  ADD KEY `matchrid_fk` (`round_id`);

--
-- Indexes for table `player`
--
ALTER TABLE `player`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teamidfk` (`team_id`);

--
-- Indexes for table `result`
--
ALTER TABLE `result`
  ADD PRIMARY KEY (`id`),
  ADD KEY `matchid_fk` (`match_id`),
  ADD KEY `winnerid_fk` (`winner_id`),
  ADD KEY `loserid_fk` (`loser_id`);

--
-- Indexes for table `round`
--
ALTER TABLE `round`
  ADD PRIMARY KEY (`id`),
  ADD KEY `roundtid_fk` (`tourna_id`);

--
-- Indexes for table `team`
--
ALTER TABLE `team`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tournaid_fk` (`tourna_id`);

--
-- Indexes for table `tourna`
--
ALTER TABLE `tourna`
  ADD PRIMARY KEY (`id`),
  ADD KEY `creatorid_fk` (`creator_id`);

--
-- Indexes for table `tourna_editor`
--
ALTER TABLE `tourna_editor`
  ADD PRIMARY KEY (`id`),
  ADD KEY `editoruid_fk` (`user_id`),
  ADD KEY `editortid_fk` (`tourna_id`);

--
-- Indexes for table `tourna_setup`
--
ALTER TABLE `tourna_setup`
  ADD PRIMARY KEY (`id`),
  ADD KEY `setuptid_fk` (`tourna_id`);

--
-- Indexes for table `va_user`
--
ALTER TABLE `va_user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `match`
--
ALTER TABLE `match`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `player`
--
ALTER TABLE `player`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `result`
--
ALTER TABLE `result`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `round`
--
ALTER TABLE `round`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `team`
--
ALTER TABLE `team`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `tourna`
--
ALTER TABLE `tourna`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `tourna_editor`
--
ALTER TABLE `tourna_editor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tourna_setup`
--
ALTER TABLE `tourna_setup`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `va_user`
--
ALTER TABLE `va_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `match`
--
ALTER TABLE `match`
  ADD CONSTRAINT `matchrid_fk` FOREIGN KEY (`round_id`) REFERENCES `round` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `p1id_fk` FOREIGN KEY (`p1_id`) REFERENCES `team` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `p2id_fk` FOREIGN KEY (`p2_id`) REFERENCES `team` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `player`
--
ALTER TABLE `player`
  ADD CONSTRAINT `teamidfk` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `result`
--
ALTER TABLE `result`
  ADD CONSTRAINT `loserid_fk` FOREIGN KEY (`loser_id`) REFERENCES `team` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `matchid_fk` FOREIGN KEY (`match_id`) REFERENCES `match` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `winnerid_fk` FOREIGN KEY (`winner_id`) REFERENCES `team` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `round`
--
ALTER TABLE `round`
  ADD CONSTRAINT `roundtid_fk` FOREIGN KEY (`tourna_id`) REFERENCES `tourna` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `team`
--
ALTER TABLE `team`
  ADD CONSTRAINT `tournaid_fk` FOREIGN KEY (`tourna_id`) REFERENCES `tourna` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tournaidfk` FOREIGN KEY (`tourna_id`) REFERENCES `tourna` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tourna`
--
ALTER TABLE `tourna`
  ADD CONSTRAINT `creatorid_fk` FOREIGN KEY (`creator_id`) REFERENCES `va_user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tourna_editor`
--
ALTER TABLE `tourna_editor`
  ADD CONSTRAINT `editortid_fk` FOREIGN KEY (`tourna_id`) REFERENCES `tourna` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `editoruid_fk` FOREIGN KEY (`user_id`) REFERENCES `va_user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tourna_setup`
--
ALTER TABLE `tourna_setup`
  ADD CONSTRAINT `setuptid_fk` FOREIGN KEY (`tourna_id`) REFERENCES `tourna` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
