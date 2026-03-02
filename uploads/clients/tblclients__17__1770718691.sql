-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 06, 2026 at 06:42 PM
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
-- Table structure for table `tblclients`
--

CREATE TABLE `tblclients` (
  `userid` int NOT NULL,
  `PlantID` int NOT NULL,
  `AccountID` varchar(50) NOT NULL COMMENT 'Fourth Layer',
  `company` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `FavouringName` varchar(500) DEFAULT NULL,
  `ActMainGroupID` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT 'First Layer',
  `ActSubGroupID1` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT 'Second Layer',
  `ActSubGroupID2` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT 'Third Layer',
  `PAN` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `GSTIN` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `OrganisationType` varchar(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `GSTType` varchar(100) DEFAULT NULL,
  `billing_country` int DEFAULT NULL,
  `billing_state` varchar(100) DEFAULT NULL,
  `billing_city` varchar(100) DEFAULT NULL,
  `billing_zip` varchar(100) DEFAULT NULL,
  `billing_address` varchar(500) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `MobileNo` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `AltMobileNo` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `Email` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `IsTDS` varchar(2) NOT NULL DEFAULT 'N',
  `TDSSection` varchar(20) DEFAULT NULL,
  `TDSPer` varchar(20) DEFAULT NULL,
  `PaymentTerms` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `PaymentCycleType` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `PaymentCycle` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `GraceDay` int DEFAULT NULL,
  `CreditLimit` decimal(15,2) DEFAULT NULL,
  `FreightTerms` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `IsActive` varchar(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL COMMENT 'Y = Active, N = Deactive',
  `DistributorType` int DEFAULT NULL,
  `DeActiveReason` text,
  `TAN` varchar(50) DEFAULT NULL,
  `PriorityID` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `FSSAINo` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `FSSAIExpiry` date DEFAULT NULL,
  `TerritoryID` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `website` varchar(150) DEFAULT NULL,
  `Attachment` text,
  `AdditionalInfo` text,
  `longitude` varchar(191) DEFAULT NULL,
  `latitude` varchar(191) DEFAULT NULL,
  `default_language` varchar(40) DEFAULT NULL,
  `default_currency` int NOT NULL DEFAULT '0',
  `TransDate` datetime(6) DEFAULT NULL,
  `CreatedBy` varchar(20) DEFAULT NULL,
  `UserID2` varchar(50) DEFAULT NULL,
  `Lupdate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `tblclients`
--

INSERT INTO `tblclients` (`userid`, `PlantID`, `AccountID`, `company`, `FavouringName`, `ActMainGroupID`, `ActSubGroupID1`, `ActSubGroupID2`, `PAN`, `GSTIN`, `OrganisationType`, `GSTType`, `billing_country`, `billing_state`, `billing_city`, `billing_zip`, `billing_address`, `MobileNo`, `AltMobileNo`, `Email`, `IsTDS`, `TDSSection`, `TDSPer`, `PaymentTerms`, `PaymentCycleType`, `PaymentCycle`, `GraceDay`, `CreditLimit`, `FreightTerms`, `IsActive`, `DistributorType`, `DeActiveReason`, `TAN`, `PriorityID`, `FSSAINo`, `FSSAIExpiry`, `TerritoryID`, `website`, `Attachment`, `AdditionalInfo`, `longitude`, `latitude`, `default_language`, `default_currency`, `TransDate`, `CreatedBy`, `UserID2`, `Lupdate`) VALUES
(1, 0, 'C01396', 'KIRTI AGRI SOLUTIONS PRIVATE LIMITED', 'DVD', NULL, NULL, NULL, 'AAKCK7270R', '27AAKCK7270R1ZL', NULL, NULL, 0, 'MH', '551', '413512', '78 C, Kava Road, Agricultural Produce Marketing Committee Latur, Market Yard, Latur, Latur, Maharashtra, 413512', '9823830444', '', 'moreshend@gmail.com', 'N', '', '', 'Credit', 'Monthly', 'Document D', NULL, 1.00, NULL, 'Y', NULL, '1', '1', NULL, '1', '0000-00-00', '', '1', NULL, '1', NULL, NULL, NULL, 3, '2026-02-06 16:21:56.000000', '1', NULL, NULL),
(9, 0, 'C01397', 'KIRTI AGRI SOLUTIONS PRIVATE LIMITED', 'test', NULL, NULL, NULL, 'AAKCK7270R', '27AAKCK7270R1ZL', NULL, NULL, 0, 'MH', '551', '413512', '78 C, Kava Road, Agricultural Produce Marketing Committee Latur, Market Yard, Latur, Latur, Maharashtra, 413512', '9823830444', '', 'moreshend@gmail.com', 'N', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-02-06 17:06:53.000000', '1', NULL, NULL),
(10, 0, 'C0139', 'KIRTI AGRI SOLUTIONS PRIVATE LIMITED', 'DVD', NULL, NULL, NULL, 'AAKCK7270R', '27AAKCK7270R1ZL', NULL, NULL, 0, 'MH', '551', '413512', '78 C, Kava Road, Agricultural Produce Marketing Committee Latur, Market Yard, Latur, Latur, Maharashtra, 413512', '9823830444', '', 'moreshend@gmail.com', 'N', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4, '2026-02-06 17:18:52.000000', '1', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tblclients`
--
ALTER TABLE `tblclients`
  ADD PRIMARY KEY (`userid`,`PlantID`,`AccountID`),
  ADD UNIQUE KEY `AccountID` (`AccountID`),
  ADD KEY `foreign key` (`DistributorType`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tblclients`
--
ALTER TABLE `tblclients`
  MODIFY `userid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
