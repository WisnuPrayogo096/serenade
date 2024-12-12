-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 08, 2024 at 10:09 AM
-- Server version: 8.0.30
-- PHP Version: 8.3.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tiket-konser`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `day_event`
--

CREATE TABLE `day_event` (
  `id` int NOT NULL,
  `id_event` int NOT NULL,
  `no_day` smallint NOT NULL COMMENT 'day1/day2/day3/etc',
  `tanggal_perform` date NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `img_day` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event_konser`
--

CREATE TABLE `event_konser` (
  `id` int NOT NULL,
  `nama_event` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `lokasi_event` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `waktu_mulai` time DEFAULT NULL,
  `waktu_selesai` time DEFAULT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `kuota` mediumint DEFAULT NULL,
  `img_banner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `norek` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `presale_ticket`
--

CREATE TABLE `presale_ticket` (
  `id` int NOT NULL,
  `id_event` int NOT NULL,
  `id_day` int NOT NULL,
  `no_presale` smallint NOT NULL,
  `harga_ticket` decimal(10,0) NOT NULL,
  `kuota_ticket` mediumint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_user`
--

CREATE TABLE `purchase_user` (
  `id` int NOT NULL,
  `id_user` int NOT NULL,
  `id_event` int NOT NULL,
  `id_day` int NOT NULL,
  `id_presale` int NOT NULL,
  `kode_ticket` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `jumlah_tiket` mediumint NOT NULL,
  `total` decimal(10,0) NOT NULL,
  `kode_unik` mediumint NOT NULL,
  `total_akhir` decimal(10,0) NOT NULL,
  `ref_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status_pembayaran` enum('pending','success') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending',
  `id_payment` int DEFAULT NULL,
  `qrcode` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `telp` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `no_identitas` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `jenis_kelamin` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `usia` smallint DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'inactive',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `day_event`
--
ALTER TABLE `day_event`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `id_event` (`id_event`);

--
-- Indexes for table `event_konser`
--
ALTER TABLE `event_konser`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `presale_ticket`
--
ALTER TABLE `presale_ticket`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `idx_event_id` (`id_event`),
  ADD KEY `id_day` (`id_day`);

--
-- Indexes for table `purchase_user`
--
ALTER TABLE `purchase_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD UNIQUE KEY `kode_ticket` (`kode_ticket`),
  ADD UNIQUE KEY `kode_unik` (`kode_unik`),
  ADD UNIQUE KEY `ref_id` (`ref_id`),
  ADD KEY `idx_user_id` (`id_user`),
  ADD KEY `id_presale` (`id_presale`),
  ADD KEY `id_payment` (`id_payment`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `day_event`
--
ALTER TABLE `day_event`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `event_konser`
--
ALTER TABLE `event_konser`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `presale_ticket`
--
ALTER TABLE `presale_ticket`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `purchase_user`
--
ALTER TABLE `purchase_user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `day_event`
--
ALTER TABLE `day_event`
  ADD CONSTRAINT `day_event_ibfk_1` FOREIGN KEY (`id_event`) REFERENCES `event_konser` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `event_konser`
--
ALTER TABLE `event_konser`
  ADD CONSTRAINT `event_konser_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `admin` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `presale_ticket`
--
ALTER TABLE `presale_ticket`
  ADD CONSTRAINT `presale_ticket_ibfk_1` FOREIGN KEY (`id_day`) REFERENCES `day_event` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `purchase_user`
--
ALTER TABLE `purchase_user`
  ADD CONSTRAINT `purchase_user_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `purchase_user_ibfk_2` FOREIGN KEY (`id_presale`) REFERENCES `presale_ticket` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `purchase_user_ibfk_3` FOREIGN KEY (`id_payment`) REFERENCES `payment` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
