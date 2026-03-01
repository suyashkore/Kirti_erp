-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 09, 2026 at 06:53 PM
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
-- Table structure for table `tblAccountSubGroup2`
--

CREATE TABLE `tblAccountSubGroup2` (
  `SubActGroupID1` varchar(100) NOT NULL,
  `SubActGroupID` varchar(11) NOT NULL,
  `SubActGroupName` varchar(100) NOT NULL,
  `ShortCode` varchar(10) DEFAULT NULL,
  `IsAccountHead` varchar(10) NOT NULL DEFAULT 'N',
  `IsCustomer` varchar(10) NOT NULL DEFAULT 'N',
  `IsVendor` varchar(10) NOT NULL DEFAULT 'N',
  `IsStaff` varchar(10) NOT NULL DEFAULT 'N',
  `IsBroker` varchar(10) NOT NULL DEFAULT 'N',
  `IsTransporter` varchar(10) NOT NULL DEFAULT 'N',
  `IsEditYN` varchar(1) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'Y' COMMENT 'Y = Allow to Edit, N = Not Allowed to Edit',
  `IsActive` varchar(20) NOT NULL COMMENT 'Y=Yes N=No',
  `UserID` varchar(20) DEFAULT NULL,
  `TransDate` datetime(6) DEFAULT NULL,
  `UserID2` varchar(50) DEFAULT NULL,
  `Lupdate` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tblAccountSubGroup2`
--

INSERT INTO `tblAccountSubGroup2` (`SubActGroupID1`, `SubActGroupID`, `SubActGroupName`, `ShortCode`, `IsAccountHead`, `IsCustomer`, `IsVendor`, `IsStaff`, `IsBroker`, `IsTransporter`, `IsEditYN`, `IsActive`, `UserID`, `TransDate`, `UserID2`, `Lupdate`) VALUES
('100000', '1000000', 'SHARE CAPITAL', NULL, 'Y', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, 'GIC', '2024-10-07 10:35:40'),
('100001', '1000001', 'CASH AND CASH EQUIVALENTS', NULL, 'N', 'N', 'N', 'N', 'Y', 'N', 'N', 'Y', NULL, NULL, 'GIC', '2024-02-09 16:52:50'),
('100001', '1000002', 'DEFERRED TAX ASSETS (NET)', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100001', '1000003', 'FDR INVESTMENT', NULL, 'Y', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, '2026-01-27 11:23:46'),
('100001', '1000004', 'INCOME TAX', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100001', '1000005', 'INVENTORIES', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100001', '1000007', 'MISC ASSETS (ASSET)', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100001', '1000008', 'OTHER CURRENT ASSETS', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100001', '1000009', 'SECURITY DEPOSITS', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100001', '1000010', 'SHORT TERM INVESTMENTS', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100001', '1000011', 'SHORT-TERM LOANS AND ADVANCES', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100001', '1000012', 'SUNDRY DEBITORS', NULL, 'N', 'Y', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, 'GIC', '2024-12-26 11:22:58'),
('100003', '1000013', 'INTANGIBLE ASSETS UNDER DEVELOPMENT', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100003', '1000014', 'CAPITAL WORK IN PROGRESS', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100003', '1000016', 'INTANGIBLE ASSETS', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100003', '1000017', 'LONG TERM INVESTMENTS', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100003', '1000018', 'LONG-TERM LOANS AND ADVANCES', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100015', '1000019', 'BONUS (PLANT)', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100015', '1000020', 'INCENTIVE (PLANT)', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100009', '1000021', 'COMMISSION', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100016', '1000022', 'CURIERE CHARGES', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100017', '1000023', 'CUSTOM DUTY', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100007', '1000024', 'AMORTIZATION', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100007', '1000025', 'DEPRECIATION', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100011', '1000026', 'ELECTRICITY CHARGES', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100019', '1000027', 'EPF ADMIN CHARGES', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100018', '1000028', 'EMPLOYER ESI CONTRIBUTION', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100013', '1000029', 'FREIGHT CHARGES INWARD', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100013', '1000030', 'FREIGHT CHARGES INWARD AGAINST GTA', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100013', '1000031', 'FREIGHT CHARGES OUTWARD', NULL, 'Y', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, 'GIC', '2025-02-04 12:23:16'),
('100013', '1000032', 'FREIGHT CHARGES OUTWARD AGAINST GTA', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100013', '1000033', 'PACKING & FORWARDING CHARGES', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100020', '1000034', 'INTERNET & COMMUNICATION', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100012', '1000035', 'ISO CERIFICATION FEES', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100008', '1000036', 'LOSS ON SALE OF ASSET', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100004', '1000037', 'JOB WORK', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100004', '1000038', 'PLANT - HIRED MANPOWER', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100006', '1000039', 'REPAIR & MAINTENANCE (PLANT)', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100014', '1000040', 'INSURANCE ON RAW MATERIALS', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100005', '1000041', 'LABOUR WELFARE EXPENSE', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100005', '1000042', 'WAGES & SALARY', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100021', '1000043', 'SERVICE CHARGES', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100010', '1000044', 'CONTRACTOR EXPENSES', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100010', '1000045', 'SITE EXPENSES', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100022', '1000046', 'DIRECT INCOME', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100000', '1000047', 'BRANCH & DIVISION', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100000', '1000048', 'RESERVES AND SURPLUS', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100000', '1000049', 'SHARE APPLICATION MONEY', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100023', '1000050', 'DUTIES & TAXES', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100023', '1000052', 'PROVISIONS', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100023', '1000053', 'RENT PAYABLE', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100023', '1000054', 'SALARY PAYABLE STAFF', NULL, 'N', 'N', 'N', 'Y', 'N', 'N', 'N', 'Y', NULL, NULL, 'GIC', '2025-02-06 18:55:31'),
('100023', '1000055', 'SHORT-TERM BORROWINGS', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100023', '1000056', 'SHORT-TERM PROVISIONS', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100023', '1000057', 'SUSPENCE ACCOUNT', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100023', '1000058', 'TRADE PAYABLES', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100024', '1000059', 'BANK OCC A/C', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100024', '1000060', 'SECURED LOANS', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100024', '1000061', 'UNSECURED LOANS', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100002', '1000062', 'LONG-TERM BORROWINGS', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100002', '1000063', 'LONG-TERM PROVISIONS', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100002', '1000064', 'OTHER LONG-TERM LIABILITIES', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100030', '1000065', 'BANK CHARGES', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100028', '1000066', 'DEPRECIATION (OFFICE)', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100029', '1000067', 'INTEREST ON LOAN', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100004', '1000068', 'HOUSEKEEPING HIRED MANPOWER', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100039', '1000069', 'LEGAL AND PROFESSIONAL EXP', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100035', '1000070', 'DISCOUNT ALLOWED', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100041', '1000071', 'STAFF FUEL CHARGES EXP', NULL, 'Y', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, 'GIC', '2025-02-04 12:25:02'),
('100035', '1000072', 'LOADING & UNLOADING CHARGES', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100035', '1000073', 'OFFICE EXPENSE', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100035', '1000074', 'OTHER EXPENSES', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100035', '1000075', 'PATROLLING CHARGES', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100035', '1000076', 'RECRUITMENT SERVICES', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100034', '1000077', 'REGISTRATION & NOC EXP', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100037', '1000078', 'OTHER INSURANCE', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100032', '1000079', 'PRIELIMINARY EXPENCES', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100036', '1000080', 'OFFICE RENT', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100036', '1000081', 'RENT ON LAND', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100036', '1000082', 'RENT ON PG BUILDING', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100027', '1000083', 'REPAIR & MAINTENANCE (OFFICE)', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100025', '1000084', 'FESTIVAL EXPENSES', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100025', '1000085', 'OFFICE STAFF WELFARE', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100025', '1000086', 'SALARY & WAGES (OFFICE)', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100033', '1000087', 'SECURITY AND SAFETY EXP', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100038', '1000088', 'STATIONARY & OFFICE SUPPLY', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100040', '1000089', 'SUBSCRIPTION FEES', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100041', '1000090', 'BUSINESS PROMOTION EXPENSES', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100041', '1000091', 'TOUR & TRAVEL EXP', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100046', '1000092', 'DEFERRED TAX', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100043', '1000093', 'DIVIDEND ON SHARE', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100044', '1000094', 'INTEREST EARED IN DEBENTURE', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100042', '1000095', 'BANK INTEREST RECEIVED', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100042', '1000096', 'COMMISSION/BROKERAGE RECEIVED', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100042', '1000097', 'DISCOUNT RECEIVED', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100042', '1000098', 'RENT RECEIVED', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100045', '1000099', 'PROFIT ON SALE OF ASSETS', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100047', '1000100', 'BOILER CHEMICAL EXPENSE', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100047', '1000101', 'BOILER FLUEL', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100047', '1000102', 'CREDIT DEBIT NOTE', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100047', '1000103', 'PURCHASE ACCOUNTS', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100047', '1000104', 'PURCHASE RETURN', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100047', '1000105', 'RAW MATERIAL', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100047', '1000106', 'STICHING WIRE', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100060', '1000107', 'SALES ACCOUNTS', NULL, 'Y', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, 'GIC', '2025-02-04 12:29:02'),
('100049', '1000108', 'SALES RETURN', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100050', '1000109', 'TRANSFER INWARD', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100050', '1000110', 'TRANSFER OUTWARD', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100055', '1000111', 'ADVANCE TO STAFF', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100001', '1000114', 'TRADE RECEIVABLES', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, 'GIC', '2024-06-06 12:37:58'),
('100056', '1000115', 'BASEN SUPPLIER', NULL, 'N', 'Y', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, 'GIC', '2025-01-27 13:32:59'),
('100001', '1000116', 'BANK ACCOUNTS', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100024', '1000117', 'BANK OD A/C', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100056', '1000118', 'DOC SUPPLIER', NULL, 'N', 'Y', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100056', '1000119', 'DAAL SUPPLIER', NULL, 'N', 'Y', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100000', '1000122', 'CAPITAL ACCOUNTS', NULL, 'Y', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, 'GIC', '2025-02-04 11:39:37'),
('100001', '1000123', 'CASH-IN-HAND', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100023', '1000143', 'SALARY PAYABLE LABOUR(Manufacturing)', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100001', '1000149', 'MISC EXPENSES (ASSET)', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100041', '1000152', 'PETROL EXP TO STAFF', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100049', '1000165', 'Sale @ 5%', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100049', '1000166', 'Sale @12%', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100049', '1000167', 'Sale @18%', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100054', '1000168', 'Security Deposit', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100023', '1000181', 'TDS PAYABLE', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100023', '1000182', 'TCS PAYABLES', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100013', '1000183', 'LOADING / UNLOADING EXP', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, 'GIC', '2024-05-13 19:36:04'),
('100042', '1000184', 'OTHER INCOME', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100058', '1000185', 'DISCOUNT ALLOWED', NULL, 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, 'GIC7', '2024-06-15 18:24:55'),
('100048', '1000186', 'RAW MATERIAL VENDOR', 'GFRMV', 'N', 'N', 'Y', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100048', '1000187', 'FINISHED GOODS (FG) VENDOR', 'GFFGV', 'N', 'N', 'Y', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100048', '1000188', 'PACKING MATERIAL (PM) VENDOR', 'GFPMV', 'N', 'N', 'Y', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100048', '1000189', 'MACHINERY VENDOR', 'GFMVV', 'N', 'N', 'Y', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100026', '1000205', 'SALES / MARKETING EXP', NULL, 'N', 'N', 'N', 'Y', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100006', '1000206', 'FACTORY HYEGINE AND SANITATION ', NULL, 'Y', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100004', '1000207', 'PRODUCTION LABOUR', NULL, 'N', 'N', 'N', 'Y', 'N', 'N', 'N', 'Y', NULL, NULL, 'GIC', '2025-02-04 12:26:52'),
('100023', '1000211', 'SALES/MARKETING', NULL, 'N', 'N', 'N', 'Y', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100057', '1000214', 'LAND / BUILDING ASSETS', NULL, 'Y', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100023', '1000220', 'OTHER', NULL, 'N', 'N', 'Y', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100063', '1000221', 'INCOME TAX', NULL, 'Y', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100063', '1000222', 'GST TAX EXPENSES', NULL, 'Y', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100048', '1000223', 'EXPENSE VENDOR', NULL, 'N', 'N', 'Y', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100000', '1000224', 'FDR INVESTMENT', NULL, 'Y', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', NULL, NULL, NULL, NULL),
('100005', '1000225', 'TEST', NULL, 'N', 'N', 'N', 'N', 'N', 'Y', 'Y', 'Y', 'GIC', '2026-02-09 12:35:27.000000', 'GIC', '2026-02-09 12:51:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tblAccountSubGroup2`
--
ALTER TABLE `tblAccountSubGroup2`
  ADD PRIMARY KEY (`SubActGroupID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
