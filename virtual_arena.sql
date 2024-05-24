-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 24, 2024 at 08:17 AM
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
  `ref_id` int(11) NOT NULL,
  `p1_id` int(11) NOT NULL,
  `p2_id` int(11) NOT NULL,
  `start_dt` datetime NOT NULL,
  `tourna_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `team_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `result`
--

CREATE TABLE `result` (
  `id` int(11) NOT NULL,
  `match_id` int(11) NOT NULL,
  `duration` int(11) NOT NULL,
  `winner_id` int(11) NOT NULL,
  `loser_id` int(11) NOT NULL,
  `conclusion` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `team`
--

CREATE TABLE `team` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `tourna_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `team`
--

INSERT INTO `team` (`id`, `name`, `tourna_id`) VALUES
(6, 'RCS', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tournament`
--

CREATE TABLE `tournament` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `format` varchar(255) NOT NULL,
  `max_entry` int(11) NOT NULL,
  `max_entry_player` int(11) NOT NULL,
  `pairing` varchar(255) NOT NULL,
  `is_public` tinyint(1) NOT NULL,
  `is_open` tinyint(1) NOT NULL,
  `description` text NOT NULL,
  `creator_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tournament`
--

INSERT INTO `tournament` (`id`, `title`, `format`, `max_entry`, `max_entry_player`, `pairing`, `is_public`, `is_open`, `description`, `creator_id`) VALUES
(1, 'Pubgm India Finals', 'Single Elimination', 2, 1, 'Random', 0, 1, 'Lezz go india waaaahhh dahek hahahkdog', 1);

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
(2, 'ian', 'ian@gmail.com', 'b1d096f1bb57e734f8362fa66fcea92860ee9213012005ee5d7f6505de98d861');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `match`
--
ALTER TABLE `match`
  ADD PRIMARY KEY (`id`),
  ADD KEY `refidfk` (`tourna_id`),
  ADD KEY `refid_fk` (`ref_id`),
  ADD KEY `p1id_fk` (`p1_id`),
  ADD KEY `p2id_fk` (`p2_id`);

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
-- Indexes for table `team`
--
ALTER TABLE `team`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tournaid_fk` (`tourna_id`);

--
-- Indexes for table `tournament`
--
ALTER TABLE `tournament`
  ADD PRIMARY KEY (`id`),
  ADD KEY `creatorid_fk` (`creator_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `player`
--
ALTER TABLE `player`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `result`
--
ALTER TABLE `result`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `team`
--
ALTER TABLE `team`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tournament`
--
ALTER TABLE `tournament`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `va_user`
--
ALTER TABLE `va_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `match`
--
ALTER TABLE `match`
  ADD CONSTRAINT `p1id_fk` FOREIGN KEY (`p1_id`) REFERENCES `team` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `p2id_fk` FOREIGN KEY (`p2_id`) REFERENCES `team` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `refid_fk` FOREIGN KEY (`ref_id`) REFERENCES `va_user` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `team`
--
ALTER TABLE `team`
  ADD CONSTRAINT `tournaid_fk` FOREIGN KEY (`tourna_id`) REFERENCES `tournament` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tournaidfk` FOREIGN KEY (`tourna_id`) REFERENCES `tournament` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tournament`
--
ALTER TABLE `tournament`
  ADD CONSTRAINT `creatorid_fk` FOREIGN KEY (`creator_id`) REFERENCES `va_user` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
