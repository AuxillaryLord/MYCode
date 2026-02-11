-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 17, 2025 at 04:31 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nshare_lite_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user','trainee','director','instructor','training') NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `display_name`, `password`, `role`, `is_active`) VALUES
(1, 'admin', 'Lieutenant A Prashanth Selvam', '$2y$10$AwunGz.rb9LhCOAaz7/50es9uzXcf7CtnvbnBzzud5aemL.q.I9T6', 'admin', 1),
(5, 'tdec', '', '$2y$10$b78.pjYdrt.yopq4xzcfi.l7wTkmJgkPi6CKyybQG5juB7CnLRrey', 'user', 1),
(6, 'training', '', '$2y$10$TDO8j2NdbCgTDXtW9F4NHuQnOo4J.B8/vvGc7tSSQDdL02bg0vug.', 'user', 1),
(7, '600', '', '$2y$10$8DhVOK0XRCJy50Saog0jreH1lT8TCBg4JHeT8uLv3zIBn4pvjOjIC', 'training', 1),
(8, 'arfaculty', '', '$2y$10$bkEH7c10HEVVZ/pSCXct4e0Kczv1ZDXkV59JaXUoM9VUC1qzL3m4e', 'user', 1),
(9, 'alfaculty', '', '$2y$10$dxi9vGzXkG6xMudLon2.P.FREIMD749trarUiZfOG/NWKsn6rd7xi', 'user', 1),
(10, 'aefaculty', '', '$2y$10$ZhsqjzinmyY84p9eBi/xG.IyQI.5HuqZpTICw/D62/6KA5MmFxoR2', 'user', 1),
(11, 'aofaculty', '', '$2y$10$NJFXFg7F3lObDVJfvDSQ2umrv07DKPWSFtpjhg6F/4b6n2n8mu.zu', 'user', 1),
(12, 'trg01', '', '$2y$10$IZSudRjusRaD1wuKY0Jk..rN7EiB0vHWqCQVcU2WdB8e1pZ1i9pYi', 'trainee', 1),
(13, '1', 'Captain Vinod Mattam', '$2y$10$vH6E2qRvensyxmJCYLQ0TeVh6M2tSL7vdXE5cfSmlbnBHhjL3QJbe', 'director', 1),
(14, '500', 'Shardul Thakur', '$2y$10$bTAJ7dqb.uQ1fc9s12sPHuCMN/G3unj6COAFKckics0sb1gyNkj.m', 'instructor', 1),
(24, '501', '', '$2y$10$vH6E2qRvensyxmJCYLQ0TeVh6M2tSL7vdXE5cfSmlbnBHhjL3QJbe', 'instructor', 1),
(25, '502', '', '$2y$10$vH6E2qRvensyxmJCYLQ0TeVh6M2tSL7vdXE5cfSmlbnBHhjL3QJbe', 'instructor', 1),
(26, '503', '', '$2y$10$vH6E2qRvensyxmJCYLQ0TeVh6M2tSL7vdXE5cfSmlbnBHhjL3QJbe', 'instructor', 1),
(28, '504', '', '$2y$10$vH6E2qRvensyxmJCYLQ0TeVh6M2tSL7vdXE5cfSmlbnBHhjL3QJbe', 'instructor', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
