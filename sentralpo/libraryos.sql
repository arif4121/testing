-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 19, 2025 at 11:47 AM
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
-- Database: `libraryos`
--

-- --------------------------------------------------------

--
-- Table structure for table `file`
--

CREATE TABLE `file` (
  `barcode` varchar(20) NOT NULL,
  `episode` varchar(50) NOT NULL,
  `nama_file` varchar(100) NOT NULL,
  `tgl_tayang` date DEFAULT NULL,
  `nama_crew` varchar(50) DEFAULT NULL,
  `nama_user` varchar(50) DEFAULT NULL,
  `tanggal_kirim` date DEFAULT NULL,
  `jam_kirim` time DEFAULT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `file`
--

INSERT INTO `file` (`barcode`, `episode`, `nama_file`, `tgl_tayang`, `nama_crew`, `nama_user`, `tanggal_kirim`, `jam_kirim`, `keterangan`) VALUES
('PG1002', '1002', 'INSERT', NULL, 'ARIF', 'qc', '2025-02-16', '07:08:00', ''),
('pg111', '1000', 'insert', NULL, 'arif', 'MCR', '2025-02-14', '19:27:00', '');

-- --------------------------------------------------------

--
-- Table structure for table `penampung`
--

CREATE TABLE `penampung` (
  `id` int(11) NOT NULL,
  `barcode` varchar(20) DEFAULT NULL,
  `status` enum('received','rejected') NOT NULL,
  `examiner` varchar(255) DEFAULT NULL,
  `next_step` enum('Library','MCR','pending') DEFAULT 'pending',
  `processed_by` varchar(100) DEFAULT NULL,
  `processed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `usl`
--

CREATE TABLE `usl` (
  `nama_user` varchar(15) NOT NULL,
  `passwd` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usl`
--

INSERT INTO `usl` (`nama_user`, `passwd`) VALUES
('Library', '$2y$10$Jw/rI40CSLBdRZYXOQ30sOgtxUmXM4j3GriKQ6OpZpLCIJs5kqgxW'),
('MCR', '$2y$10$CHyjxAbEX2r.4t4me8IFguiqdy8vNRlQRCu1FMVC7bJZMxp3DLWUG'),
('QC', '$2y$10$YRVujOu2pfBmu/tSaeSKZ.i1zsvDHhWcs7ABrAc/.0FvSv36i6mnu');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `file`
--
ALTER TABLE `file`
  ADD PRIMARY KEY (`barcode`),
  ADD KEY `nama_penerima` (`nama_user`);

--
-- Indexes for table `penampung`
--
ALTER TABLE `penampung`
  ADD PRIMARY KEY (`id`),
  ADD KEY `barcode` (`barcode`);

--
-- Indexes for table `usl`
--
ALTER TABLE `usl`
  ADD PRIMARY KEY (`nama_user`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `penampung`
--
ALTER TABLE `penampung`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `file`
--
ALTER TABLE `file`
  ADD CONSTRAINT `file_ibfk_1` FOREIGN KEY (`nama_user`) REFERENCES `usl` (`nama_user`);

--
-- Constraints for table `penampung`
--
ALTER TABLE `penampung`
  ADD CONSTRAINT `penampung_ibfk_1` FOREIGN KEY (`barcode`) REFERENCES `file` (`barcode`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
