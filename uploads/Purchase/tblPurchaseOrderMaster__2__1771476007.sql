-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 12, 2026 at 03:51 PM
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
-- Table structure for table `tblPurchaseOrderMaster`
--

CREATE TABLE `tblPurchaseOrderMaster` (
  `id` int NOT NULL,
  `PlantID` varchar(10) NOT NULL,
  `FY` varchar(5) NOT NULL,
  `PurchaseLocation` varchar(10) NOT NULL,
  `PurchID` varchar(20) NOT NULL,
  `TransDate` datetime(6) NOT NULL,
  `TransDate2` datetime(6) NOT NULL,
  `ItemType` varchar(5) NOT NULL COMMENT 'Item Type/ Service ID',
  `ItemCategory` varchar(5) NOT NULL COMMENT 'Item Category ID',
  `QuatationID` varchar(20) DEFAULT NULL,
  `AccountID` varchar(20) NOT NULL COMMENT 'Vendor AccountID',
  `DeliveryLocation` varchar(20) NOT NULL COMMENT 'Vendor Dispatched From Location ID',
  `DeliveryFrom` datetime(6) NOT NULL COMMENT 'Delivery From date',
  `DeliveryTo` datetime(6) NOT NULL COMMENT 'Delivery To Date',
  `VendorDocNo` varchar(50) DEFAULT NULL COMMENT 'Vendor Doc No',
  `VendorDocDate` datetime(6) DEFAULT NULL COMMENT 'Vendor Doc Date',
  `PaymentTerms` varchar(10) NOT NULL COMMENT 'Payment Term ID',
  `FreightTerms` varchar(10) NOT NULL COMMENT 'Freight Tems ID',
  `GSTIN` varchar(50) DEFAULT NULL COMMENT 'Vendor GST No',
  `TotalWeight` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Total Weight',
  `TotalQuantity` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Total Quantity',
  `ItemAmt` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Total Item Amt',
  `DiscAmt` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Total Disc Amt',
  `TaxableAmt` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Total Taxable amt',
  `CGSTAmt` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'CGST amt',
  `SGSTAmt` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'SGST amt',
  `IGSTAmt` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'IGST Amt',
  `TDSSection` varchar(20) DEFAULT NULL COMMENT 'TDS Section',
  `TDSPercentage` decimal(15,3) DEFAULT NULL COMMENT 'TDS Percentage',
  `TDSAmt` decimal(15,2) DEFAULT NULL COMMENT 'Total TDS Amt',
  `RoundOffAmt` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Round Off amt',
  `NetAmt` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Total Purchase Amt',
  `UserID` varchar(20) NOT NULL COMMENT 'Created By',
  `UserID2` varchar(20) DEFAULT NULL COMMENT 'Updated By',
  `Lupdate` datetime(6) DEFAULT NULL COMMENT 'Update at'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tblPurchaseOrderMaster`
--
ALTER TABLE `tblPurchaseOrderMaster`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tblPurchaseOrderMaster`
--
ALTER TABLE `tblPurchaseOrderMaster`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
