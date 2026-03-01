-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 06, 2026 at 07:20 PM
-- Server version: 8.0.45
-- PHP Version: 8.4.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kirti2erpgl_erp_db2`
--

-- --------------------------------------------------------

--
-- Table structure for table `tblPartyBrokerMaster`
--

CREATE TABLE `tblPartyBrokerMaster` (
  `id` int NOT NULL,
  `PlantID` varchar(10) NOT NULL,
  `AccountID` varchar(20) NOT NULL COMMENT 'Customer/Vendor AccountID',
  `BrokerID` varchar(20) NOT NULL COMMENT 'Broker AccountID',
  `BrokerContactID` int NOT NULL COMMENT 'Contact id from tblcontacts',
  `UserID` varchar(20) NOT NULL,
  `TransDate` datetime(6) NOT NULL,
  `UserID2` varchar(20) DEFAULT NULL,
  `Lupdate` datetime(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tblPartyBrokerMaster`
--
ALTER TABLE `tblPartyBrokerMaster`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tblPartyBrokerMaster`
--
ALTER TABLE `tblPartyBrokerMaster`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
