-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 02, 2026 at 03:48 PM
-- Server version: 8.0.45
-- PHP Version: 8.4.16

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
-- Table structure for table `tblPlantLocationDetails`
--

CREATE TABLE `tblPlantLocationDetails` (
  `id` int NOT NULL,
  `PlantID` int NOT NULL COMMENT 'id get from rootcompany table',
  `comp_short` varchar(100) NOT NULL COMMENT 'Get comp_short From rootcomany table',
  `StateCode` varchar(10) NOT NULL,
  `CityID` int NOT NULL,
  `LocationName` varchar(200) NOT NULL COMMENT 'Village / town name',
  `Address` varchar(250) NOT NULL,
  `PinCode` int NOT NULL,
  `MobileNo` varchar(20) NOT NULL,
  `fssai_no` varchar(50) DEFAULT NULL,
  `fssai_no_expiry` date DEFAULT NULL,
  `IsActive` varchar(2) NOT NULL DEFAULT 'Y' COMMENT 'Y = Active \r\nN = Deactive',
  `UserID` varchar(50) CHARACTER SET utf8mb4 NOT NULL COMMENT 'created by',
  `TransDate` datetime(6) NOT NULL COMMENT 'Created date',
  `UserID2` varchar(50) DEFAULT NULL,
  `LupDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tblPlantLocationDetails`
--
ALTER TABLE `tblPlantLocationDetails`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tblPlantLocationDetails`
--
ALTER TABLE `tblPlantLocationDetails`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
