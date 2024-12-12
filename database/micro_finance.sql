-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 12, 2024 at 07:24 AM
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
-- Database: `micro_finance`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `Add_Menu` (IN `menu_name` VARCHAR(100), IN `menu_link` VARCHAR(100), IN `menu_icon` VARCHAR(100))   BEGIN
    INSERT INTO menu_list (menu, link,icon) VALUES (menu_name, menu_link,menu_icon);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `Add_Sub_Menu` (IN `main_menu_id` INT, IN `sub_menu_name` VARCHAR(100), IN `sub_menu_link` VARCHAR(100), IN `sub_menu_icon` VARCHAR(100))   BEGIN
    DECLARE menu_exists INT;
    
    -- Check if the main_menu_id exists in the menu_list table
    SELECT COUNT(*) INTO menu_exists FROM menu_list WHERE id = main_menu_id;
    
    IF menu_exists = 1 THEN
        -- Insert into sub_menu_list table with a valid main_menu_id
        INSERT INTO sub_menu_list (main_menu, sub_menu, link,icon) VALUES (main_menu_id, sub_menu_name, sub_menu_link,sub_menu_icon);
        SELECT 'Sub menu added successfully.';
    ELSE
        SELECT 'Main menu does not exist. Cannot add sub menu.';
    END IF;
    
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `area_creation`
--

CREATE TABLE `area_creation` (
  `id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `area_id` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_on` datetime NOT NULL DEFAULT current_timestamp(),
  `update_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `area_creation`
--

INSERT INTO `area_creation` (`id`, `branch_id`, `area_id`, `status`, `insert_login_id`, `update_login_id`, `created_on`, `update_on`) VALUES
(4, 1, '9', 1, 1, NULL, '2024-12-03 16:19:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `area_name_creation`
--

CREATE TABLE `area_name_creation` (
  `id` int(11) NOT NULL,
  `areaname` varchar(200) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_on` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `area_name_creation`
--

INSERT INTO `area_name_creation` (`id`, `areaname`, `branch_id`, `status`, `insert_login_id`, `update_login_id`, `created_on`, `updated_on`) VALUES
(9, 'JJ', 1, 1, 1, 1, '2024-12-03 16:18:41', '2024-12-03');

-- --------------------------------------------------------

--
-- Table structure for table `bank_creation`
--

CREATE TABLE `bank_creation` (
  `id` int(11) NOT NULL,
  `bank_name` varchar(250) DEFAULT NULL,
  `bank_short_name` varchar(100) DEFAULT NULL,
  `account_number` varchar(100) DEFAULT NULL,
  `ifsc_code` varchar(100) DEFAULT NULL,
  `branch_name` varchar(100) DEFAULT NULL,
  `gpay` varchar(100) DEFAULT NULL,
  `under_branch` varchar(100) DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  `insert_login_id` varchar(100) DEFAULT NULL,
  `update_login_id` varchar(100) DEFAULT NULL,
  `delete_login_id` varchar(100) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `updated_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branch_creation`
--

CREATE TABLE `branch_creation` (
  `id` int(11) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `branch_code` varchar(50) NOT NULL,
  `branch_name` varchar(100) NOT NULL,
  `address` varchar(100) NOT NULL,
  `state` int(11) NOT NULL,
  `district` int(11) NOT NULL,
  `taluk` int(11) NOT NULL,
  `place` varchar(100) NOT NULL,
  `pincode` varchar(100) NOT NULL,
  `email_id` varchar(100) NOT NULL,
  `mobile_number` varchar(100) NOT NULL,
  `whatsapp` varchar(100) NOT NULL,
  `landline_code` varchar(50) DEFAULT NULL,
  `landline` varchar(100) NOT NULL,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branch_creation`
--

INSERT INTO `branch_creation` (`id`, `company_name`, `branch_code`, `branch_name`, `address`, `state`, `district`, `taluk`, `place`, `pincode`, `email_id`, `mobile_number`, `whatsapp`, `landline_code`, `landline`, `insert_login_id`, `update_login_id`, `created_date`, `updated_date`) VALUES
(1, 'Th', 'T-101', 'Pondy', 'kk ', 2, 1, 1, 'Pondy', '605110', 'hhh', '', '7765443333', '665', '756455', 1, 0, '2024-12-03 16:16:21', NULL),
(7, 'Feather Technology', 'F-102', 'pondi', 'Bussy Street', 2, 39, 315, 'Puducherry', '65001', 'feath', '8764531321', '9879846545', '54445', '54564654', 1, 0, '2024-12-03 17:59:16', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `centre_creation`
--

CREATE TABLE `centre_creation` (
  `id` int(11) NOT NULL,
  `centre_id` varchar(100) NOT NULL,
  `centre_no` varchar(100) NOT NULL,
  `centre_name` varchar(100) NOT NULL,
  `mobile1` varchar(100) NOT NULL,
  `mobile2` varchar(100) DEFAULT NULL,
  `area` varchar(255) NOT NULL,
  `branch` int(11) NOT NULL,
  `pic` varchar(100) DEFAULT NULL,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_on` date NOT NULL,
  `updated_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `centre_creation`
--

INSERT INTO `centre_creation` (`id`, `centre_id`, `centre_no`, `centre_name`, `mobile1`, `mobile2`, `area`, `branch`, `pic`, `insert_login_id`, `update_login_id`, `created_on`, `updated_on`) VALUES
(1, 'M-101', '7867867', 'VSI-1', '8678567657', '', '9', 7, '', 1, 1, '2024-12-04', '2024-12-10'),
(2, 'M-102', '123', 'Sasd', '6787686786', '', '9', 1, '', 1, NULL, '2024-12-05', NULL),
(3, 'M-103', '5464564', 'Gh', '7897675675', '', '9', 1, '', 1, 1, '2024-12-09', '2024-12-09');

-- --------------------------------------------------------

--
-- Table structure for table `company_creation`
--

CREATE TABLE `company_creation` (
  `id` int(11) NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `state` int(11) DEFAULT NULL,
  `district` int(11) DEFAULT NULL,
  `taluk` int(11) DEFAULT NULL,
  `place` varchar(255) DEFAULT NULL,
  `pincode` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `mailid` varchar(255) DEFAULT NULL,
  `mobile` varchar(255) DEFAULT NULL,
  `whatsapp` varchar(255) DEFAULT NULL,
  `landline_code` varchar(100) DEFAULT NULL,
  `landline` varchar(255) DEFAULT NULL,
  `pan` varchar(250) DEFAULT NULL,
  `tan` varchar(250) DEFAULT NULL,
  `tin` varchar(250) DEFAULT NULL,
  `cin` varchar(250) DEFAULT NULL,
  `License_No` varchar(250) DEFAULT NULL,
  `gst` varchar(250) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `insert_user_id` int(11) DEFAULT NULL,
  `update_user_id` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `updated_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_creation`
--

INSERT INTO `company_creation` (`id`, `company_name`, `address`, `state`, `district`, `taluk`, `place`, `pincode`, `website`, `mailid`, `mobile`, `whatsapp`, `landline_code`, `landline`, `pan`, `tan`, `tin`, `cin`, `License_No`, `gst`, `status`, `insert_user_id`, `update_user_id`, `created_date`, `updated_date`) VALUES
(1, 'Feather Technology', 'Bussy Street', 1, 34, 278, 'Cheyyar', '65001', 'feather.com', 'feather@gmail.com', '9984865146', '9878978978', '87987', '89789464', '8948944465', '987984645645 ', '8448489983564', '544467887687', '87654554', '45678695', 1, 1, NULL, '2024-12-03 17:49:02', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `company_document`
--

CREATE TABLE `company_document` (
  `s_no` int(50) NOT NULL,
  `document_name` varchar(250) DEFAULT NULL,
  `file` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_document`
--

INSERT INTO `company_document` (`s_no`, `document_name`, `file`) VALUES
(2, 'Aadhar card', '67581a2a573ed.png');

-- --------------------------------------------------------

--
-- Table structure for table `customer_creation`
--

CREATE TABLE `customer_creation` (
  `id` int(11) NOT NULL,
  `cus_id` varchar(100) NOT NULL,
  `aadhar_number` varchar(100) NOT NULL,
  `cus_data` varchar(100) NOT NULL,
  `cus_status` varchar(100) DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `area` varchar(11) NOT NULL,
  `mobile1` varchar(100) NOT NULL,
  `mobile2` varchar(100) DEFAULT NULL,
  `whatsapp` varchar(100) DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `occ_detail` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `native_address` varchar(255) DEFAULT NULL,
  `pic` varchar(100) DEFAULT NULL,
  `multiple_loan` varchar(100) NOT NULL,
  `insert_login_id` int(11) DEFAULT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_on` date DEFAULT NULL,
  `updated_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_creation`
--

INSERT INTO `customer_creation` (`id`, `cus_id`, `aadhar_number`, `cus_data`, `cus_status`, `first_name`, `last_name`, `dob`, `age`, `area`, `mobile1`, `mobile2`, `whatsapp`, `occupation`, `occ_detail`, `address`, `native_address`, `pic`, `multiple_loan`, `insert_login_id`, `update_login_id`, `created_on`, `updated_on`) VALUES
(3, 'C-102', '312312312312', 'Existing', 'Additional', 'Dheepa', 'S B', '2001-01-04', 23, '9', '8787687686', '', '8787687686', 'Teacher', 'ssv school', 'Pondy', 'Pondy', '', '0', 1, 1, '2024-12-04', '2024-12-12'),
(4, 'C-103', '867876867867', 'New', '', 'Ravi ', '', '0000-00-00', 0, '9', '8909008908', '', '', '', '', '', '', '', '1', 1, NULL, '2024-12-05', NULL),
(6, 'C-104', '123123123213', 'Existing', 'Renewal', 'Muthu', '', '0000-00-00', 0, '9', '7567567567', '7896786786', '7567567567', '', '', '', 'Pondy', '', '1', 1, 1, '2024-12-10', '2024-12-12'),
(10, 'C-105', '456456456456', 'Existing', '', 'Raghu', '', '0000-00-00', 0, '9', '7867867855', '', '', '', '', 'Pondy', 'Pondy', '', '1', 1, 1, '2024-12-12', '2024-12-12'),
(12, 'C-106', '546345634564', 'Existing', '', 'Ramya', '', '0000-00-00', 0, '9', '7867867867', '', '', '', '', '', '', '', '0', 1, 1, '2024-12-12', '2024-12-12');

-- --------------------------------------------------------

--
-- Table structure for table `districts`
--

CREATE TABLE `districts` (
  `id` int(11) NOT NULL,
  `state_id` int(11) NOT NULL,
  `district_name` varchar(50) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `districts`
--

INSERT INTO `districts` (`id`, `state_id`, `district_name`, `status`) VALUES
(1, 1, 'Ariyalur', 1),
(2, 1, 'Chennai', 1),
(3, 1, 'Chengalpattu', 1),
(4, 1, 'Coimbatore', 1),
(5, 1, 'Cuddalore', 1),
(6, 1, 'Dharmapuri', 1),
(7, 1, 'Dindigul', 1),
(8, 1, 'Erode', 1),
(9, 1, 'Kallakurichi', 1),
(10, 1, 'Kancheepuram', 1),
(11, 1, 'Kanniyakumari', 1),
(12, 1, 'Karur', 1),
(13, 1, 'Krishnagiri', 1),
(14, 1, 'Madurai', 1),
(15, 1, 'Mayiladuthurai', 1),
(16, 1, 'Nagapattinam', 1),
(17, 1, 'Namakkal', 1),
(18, 1, 'Nilgiris', 1),
(19, 1, 'Perambalur', 1),
(20, 1, 'Pudukkottai', 1),
(21, 1, 'Ramanathapuram', 1),
(22, 1, 'Ranipet', 1),
(23, 1, 'Salem', 1),
(24, 1, 'Sivaganga', 1),
(25, 1, 'Tenkasi', 1),
(26, 1, 'Thanjavur', 1),
(27, 1, 'Theni', 1),
(28, 1, 'Thoothukudi', 1),
(29, 1, 'Tiruchirappalli', 1),
(30, 1, 'Tirunelveli', 1),
(31, 1, 'Tiruppur', 1),
(32, 1, 'Tirupathur', 1),
(33, 1, 'Tiruvallur', 1),
(34, 1, 'Tiruvannamalai', 1),
(35, 1, 'Tiruvarur', 1),
(36, 1, 'Vellore', 1),
(37, 1, 'Viluppuram', 1),
(38, 1, 'Virudhunagar', 1),
(39, 2, 'Puducherry', 1);

-- --------------------------------------------------------

--
-- Table structure for table `document_info`
--

CREATE TABLE `document_info` (
  `id` int(11) NOT NULL,
  `loan_id` varchar(100) NOT NULL,
  `doc_id` varchar(100) NOT NULL,
  `cus_id` int(11) NOT NULL,
  `doc_name` varchar(100) NOT NULL,
  `doc_type` int(11) NOT NULL,
  `count` int(11) NOT NULL,
  `remark` varchar(100) DEFAULT NULL,
  `upload` varchar(100) NOT NULL DEFAULT '0',
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_on` date DEFAULT NULL,
  `updated_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `document_info`
--

INSERT INTO `document_info` (`id`, `loan_id`, `doc_id`, `cus_id`, `doc_name`, `doc_type`, `count`, `remark`, `upload`, `insert_login_id`, `update_login_id`, `created_on`, `updated_on`) VALUES
(6, 'L-102', 'D-103', 5, 'Aadhar card', 2, 1, '', '675823c7c1a10.png', 1, NULL, '2024-12-10', NULL),
(7, 'L-101', 'D-104', 8, 'Aadhar card', 1, 2, '1dsf', '', 1, NULL, '2024-12-10', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `family_info`
--

CREATE TABLE `family_info` (
  `id` int(11) NOT NULL,
  `cus_id` varchar(100) NOT NULL,
  `fam_name` varchar(100) NOT NULL,
  `fam_relationship` varchar(100) NOT NULL,
  `fam_age` varchar(100) NOT NULL,
  `fam_occupation` varchar(100) NOT NULL,
  `fam_aadhar` varchar(100) NOT NULL,
  `fam_mobile` varchar(100) NOT NULL,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_on` date DEFAULT NULL,
  `updated_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `family_info`
--

INSERT INTO `family_info` (`id`, `cus_id`, `fam_name`, `fam_relationship`, `fam_age`, `fam_occupation`, `fam_aadhar`, `fam_mobile`, `insert_login_id`, `update_login_id`, `created_on`, `updated_on`) VALUES
(3, 'C-101', 'gfhfgh', 'Father', '21', 'Teacher', '654645645645', '6798908909', 1, 1, '2024-12-04', '2024-12-04'),
(4, 'C-102', 'Anbu', 'Mother', '', '', '564564564564', '8766576576', 1, 0, '2024-12-04', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `loan_category`
--

CREATE TABLE `loan_category` (
  `id` int(11) NOT NULL,
  `loan_category` varchar(150) NOT NULL,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_on` date DEFAULT NULL,
  `updated_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loan_category`
--

INSERT INTO `loan_category` (`id`, `loan_category`, `insert_login_id`, `update_login_id`, `created_on`, `updated_on`) VALUES
(4, 'Personal', 1, NULL, '2024-12-03', NULL),
(5, 'Car', 1, NULL, '2024-12-05', NULL),
(6, 'Bike', 1, NULL, '2024-12-06', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `loan_category_creation`
--

CREATE TABLE `loan_category_creation` (
  `id` int(11) NOT NULL,
  `loan_category` int(11) NOT NULL,
  `loan_limit` varchar(100) NOT NULL,
  `profit_type` varchar(100) NOT NULL,
  `due_method` varchar(50) NOT NULL,
  `due_type` varchar(50) NOT NULL,
  `benefit_method` varchar(100) NOT NULL,
  `interest_rate_min` varchar(50) DEFAULT NULL,
  `interest_rate_max` varchar(50) DEFAULT NULL,
  `due_period_min` varchar(50) DEFAULT NULL,
  `due_period_max` varchar(50) DEFAULT NULL,
  `doc_charge_min` varchar(50) DEFAULT NULL,
  `doc_charge_max` varchar(50) DEFAULT NULL,
  `processing_fee_min` varchar(50) DEFAULT NULL,
  `processing_fee_max` varchar(100) DEFAULT NULL,
  `overdue_penalty` varchar(100) DEFAULT NULL,
  `penalty_type` varchar(100) DEFAULT NULL,
  `scheme_name` varchar(150) NOT NULL,
  `status` int(100) NOT NULL,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_on` date DEFAULT NULL,
  `updated_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loan_category_creation`
--

INSERT INTO `loan_category_creation` (`id`, `loan_category`, `loan_limit`, `profit_type`, `due_method`, `due_type`, `benefit_method`, `interest_rate_min`, `interest_rate_max`, `due_period_min`, `due_period_max`, `doc_charge_min`, `doc_charge_max`, `processing_fee_min`, `processing_fee_max`, `overdue_penalty`, `penalty_type`, `scheme_name`, `status`, `insert_login_id`, `update_login_id`, `created_on`, `updated_on`) VALUES
(5, 4, '2000000', '1,2', '1', 'EMI', '1', '2', '6', '2', '3', '2', '3', '2', '3', '2', 'rupee', '1,2', 1, 1, 1, '2024-12-03', '2024-12-05'),
(6, 5, '9000000', '1,2', '1', 'EMI', '1', '5', '6', '2', '3', '2', '3', '2', '3', '2', 'percent', '1,2', 1, 1, 1, '2024-12-03', '2024-12-05'),
(7, 5, '2000000', '2', '1', 'EMI', '', '', '', '', '', '', '', '', '', '', '', '1', 1, 1, 1, '2024-12-05', '2024-12-05'),
(8, 6, '500000', '2,1', '1', 'EMI', '2', '1', '2', '1', '2', '2', '3', '2', '3', '40', 'rupee', '4', 1, 1, 1, '2024-12-06', '2024-12-06');

-- --------------------------------------------------------

--
-- Table structure for table `loan_cus_mapping`
--

CREATE TABLE `loan_cus_mapping` (
  `id` int(11) NOT NULL,
  `loan_id` varchar(100) NOT NULL,
  `cus_id` int(11) NOT NULL,
  `customer_mapping` int(11) NOT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `issue_status` varchar(100) DEFAULT NULL,
  `inserted_login_id` int(11) NOT NULL,
  `created_on` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loan_cus_mapping`
--

INSERT INTO `loan_cus_mapping` (`id`, `loan_id`, `cus_id`, `customer_mapping`, `designation`, `issue_status`, `inserted_login_id`, `created_on`) VALUES
(5, 'L-102', 4, 1, '', '', 1, '2024-12-10'),
(8, 'L-101', 3, 1, '', '', 1, '2024-12-10'),
(9, 'L-101', 4, 3, 'dsfs', '', 1, '2024-12-10'),
(11, 'L-102', 6, 1, '', NULL, 1, '2024-12-11'),
(12, 'L-103', 10, 1, '', NULL, 1, '2024-12-12');

-- --------------------------------------------------------

--
-- Table structure for table `loan_entry_loan_calculation`
--

CREATE TABLE `loan_entry_loan_calculation` (
  `id` int(11) NOT NULL,
  `centre_id` varchar(100) NOT NULL,
  `loan_id` varchar(50) NOT NULL,
  `loan_category` int(11) NOT NULL,
  `loan_amount` int(50) DEFAULT NULL,
  `total_customer` int(50) DEFAULT NULL,
  `loan_amt_per_cus` int(50) DEFAULT NULL,
  `profit_type` varchar(50) DEFAULT NULL,
  `due_month` varchar(50) DEFAULT NULL,
  `benefit_method` varchar(100) DEFAULT NULL,
  `scheme_day_calc` int(11) DEFAULT NULL,
  `interest_rate` int(150) NOT NULL,
  `due_period` int(100) NOT NULL,
  `doc_charge` int(100) NOT NULL,
  `processing_fees` int(100) NOT NULL,
  `scheme_name` varchar(150) DEFAULT NULL,
  `scheme_date` varchar(150) DEFAULT NULL,
  `loan_amount_calc` int(150) NOT NULL,
  `principal_amount_calc` int(150) NOT NULL,
  `intrest_amount_calc` int(11) NOT NULL,
  `total_amount_calc` int(11) NOT NULL,
  `due_amount_calc` int(11) NOT NULL,
  `document_charge_cal` int(11) NOT NULL,
  `processing_fees_cal` int(100) DEFAULT NULL,
  `net_cash_calc` int(150) DEFAULT NULL,
  `due_start` date DEFAULT NULL,
  `due_end` date DEFAULT NULL,
  `loan_date` date DEFAULT NULL,
  `loan_status` int(11) NOT NULL DEFAULT 0,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_on` date DEFAULT NULL,
  `updated_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loan_entry_loan_calculation`
--

INSERT INTO `loan_entry_loan_calculation` (`id`, `centre_id`, `loan_id`, `loan_category`, `loan_amount`, `total_customer`, `loan_amt_per_cus`, `profit_type`, `due_month`, `benefit_method`, `scheme_day_calc`, `interest_rate`, `due_period`, `doc_charge`, `processing_fees`, `scheme_name`, `scheme_date`, `loan_amount_calc`, `principal_amount_calc`, `intrest_amount_calc`, `total_amount_calc`, `due_amount_calc`, `document_charge_cal`, `processing_fees_cal`, `net_cash_calc`, `due_start`, `due_end`, `loan_date`, `loan_status`, `insert_login_id`, `update_login_id`, `created_on`, `updated_on`) VALUES
(1, 'M-101', 'L-101', 6, 100000, 2, 50000, '1', '1', '1', 0, 5, 2, 2, 3, '', '', 100000, 100000, 10000, 110000, 55000, 2000, 3000, 95000, '2025-01-10', '2025-02-10', '2024-12-12', 8, 1, 1, '2024-12-10', '2024-12-12'),
(2, 'M-102', 'L-102', 6, 700000, 2, 350000, '2', '2', '1', 4, 1, 2, 1, 2, '2', '6', 700000, 700000, 14000, 714000, 357000, 7000, 14000, 679000, '2024-12-10', '2024-12-19', NULL, 9, 1, 1, '2024-12-10', '2024-12-11'),
(3, 'M-103', 'L-103', 5, 120000, 1, 120000, '1', '1', '1', 0, 2, 2, 2, 2, '', '', 120000, 120000, 4800, 124800, 62400, 2400, 2400, 115200, '2024-12-12', '2025-01-12', NULL, 2, 1, 1, '2024-12-12', '2024-12-12');

-- --------------------------------------------------------

--
-- Table structure for table `loan_issue`
--

CREATE TABLE `loan_issue` (
  `id` int(11) NOT NULL,
  `cus_mapping_id` varchar(255) NOT NULL,
  `loan_id` varchar(11) NOT NULL,
  `loan_amnt` int(11) NOT NULL,
  `net_cash` int(11) NOT NULL,
  `payment_mode` int(11) NOT NULL,
  `issue_type` varchar(100) NOT NULL,
  `issue_amount` int(100) NOT NULL,
  `transaction_id` varchar(50) DEFAULT NULL,
  `cheque_no` varchar(50) DEFAULT NULL,
  `issue_date` date NOT NULL,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `updated_on` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loan_issue`
--

INSERT INTO `loan_issue` (`id`, `cus_mapping_id`, `loan_id`, `loan_amnt`, `net_cash`, `payment_mode`, `issue_type`, `issue_amount`, `transaction_id`, `cheque_no`, `issue_date`, `insert_login_id`, `update_login_id`, `created_on`, `updated_on`) VALUES
(1, '8', 'L-101', 100000, 47500, 1, '1', 43000, NULL, NULL, '2024-12-12', 1, NULL, '2024-12-12 09:58:18', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `menu_list`
--

CREATE TABLE `menu_list` (
  `id` int(11) NOT NULL,
  `menu` varchar(100) NOT NULL,
  `link` varchar(100) NOT NULL,
  `icon` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='All Main Menu''s will be placed here';

--
-- Dumping data for table `menu_list`
--

INSERT INTO `menu_list` (`id`, `menu`, `link`, `icon`) VALUES
(1, 'Dashboard', 'dashboard', 'developer_board'),
(2, 'Master', 'master', 'camera1'),
(3, 'Administration', 'admin', 'layers'),
(4, 'Profile', 'profile', 'archive'),
(5, 'Loan Entry', 'loan_entry', 'user-check'),
(6, 'Approval', 'approval', 'wallet'),
(7, 'Loan Issue', 'loan_issue', 'credit'),
(8, 'Closed', 'closed', 'uninstall'),
(9, 'NOC', 'noc', 'export'),
(10, 'Accounts', 'accounts', 'domain'),
(11, 'Update', 'update', 'share1'),
(12, 'Customer Data', 'customer_data', 'folder_shared'),
(13, 'Search', 'search', 'magnifying-glass'),
(14, 'Reports', 'reports', 'assignment_turned_in'),
(15, 'Bulk Upload', 'bulk_upload', 'cloud_upload');

-- --------------------------------------------------------

--
-- Table structure for table `representative_info`
--

CREATE TABLE `representative_info` (
  `id` int(11) NOT NULL,
  `centre_id` varchar(100) NOT NULL,
  `rep_name` varchar(100) NOT NULL,
  `rep_aadhar` varchar(100) NOT NULL,
  `rep_occupation` varchar(100) DEFAULT NULL,
  `rep_mobile` varchar(100) NOT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `remark` varchar(100) DEFAULT NULL,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_on` date NOT NULL,
  `updated_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `representative_info`
--

INSERT INTO `representative_info` (`id`, `centre_id`, `rep_name`, `rep_aadhar`, `rep_occupation`, `rep_mobile`, `designation`, `remark`, `insert_login_id`, `update_login_id`, `created_on`, `updated_on`) VALUES
(5, 'M-101', 'ghfg', '567567567567', '', '7898987786', '', '', 1, NULL, '2024-12-04', NULL),
(6, 'M-102', 'Kumar', '123567567567', '', '6786786786', '', '', 1, NULL, '2024-12-05', NULL),
(7, 'M-103', 'Kamal', '757657567567', '', '8676575675', '', '', 1, NULL, '2024-12-09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `id` int(11) NOT NULL,
  `role` varchar(100) DEFAULT NULL,
  `insert_login_id` int(11) DEFAULT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_on` date DEFAULT NULL,
  `updated_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `scheme`
--

CREATE TABLE `scheme` (
  `id` int(11) NOT NULL,
  `scheme_name` varchar(150) NOT NULL,
  `due_method` varchar(50) NOT NULL,
  `benefit_method` varchar(20) NOT NULL,
  `interest_rate_percent_min` varchar(10) NOT NULL,
  `interest_rate_percent_max` varchar(10) NOT NULL,
  `due_period_percent_min` varchar(10) NOT NULL,
  `due_period_percent_max` varchar(10) NOT NULL,
  `overdue_penalty_percent` varchar(10) NOT NULL,
  `scheme_penalty_type` varchar(10) NOT NULL,
  `doc_charge_min` varchar(10) NOT NULL,
  `doc_charge_max` varchar(10) NOT NULL,
  `processing_fee_min` varchar(10) NOT NULL,
  `processing_fee_max` varchar(10) NOT NULL,
  `insert_login_id` int(11) NOT NULL,
  `update_login_id` int(11) DEFAULT NULL,
  `created_on` date DEFAULT NULL,
  `updated_on` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scheme`
--

INSERT INTO `scheme` (`id`, `scheme_name`, `due_method`, `benefit_method`, `interest_rate_percent_min`, `interest_rate_percent_max`, `due_period_percent_min`, `due_period_percent_max`, `overdue_penalty_percent`, `scheme_penalty_type`, `doc_charge_min`, `doc_charge_max`, `processing_fee_min`, `processing_fee_max`, `insert_login_id`, `update_login_id`, `created_on`, `updated_on`) VALUES
(1, '2A', '1', '1', '1', '3', '10', '30', '2', 'rupee', '1', '2', '1', '2', 1, 1, '2024-12-03', '2024-12-06'),
(2, 'Daily', '2', '1', '1', '3', '2', '8', '2', 'percent', '2', '3', '2', '3', 1, NULL, '2024-12-03', NULL),
(4, 'LT', '1', '2', '1', '2', '1', '12', '3', 'percent', '1', '2', '1', '2', 1, 1, '2024-12-06', '2024-12-06');

-- --------------------------------------------------------

--
-- Table structure for table `states`
--

CREATE TABLE `states` (
  `id` int(11) NOT NULL,
  `state_name` varchar(50) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `states`
--

INSERT INTO `states` (`id`, `state_name`, `status`) VALUES
(1, 'Tamil Nadu', 1),
(2, 'Puducherry', 1);

-- --------------------------------------------------------

--
-- Table structure for table `sub_menu_list`
--

CREATE TABLE `sub_menu_list` (
  `id` int(11) NOT NULL,
  `main_menu` int(11) NOT NULL,
  `sub_menu` varchar(100) NOT NULL,
  `link` varchar(100) NOT NULL,
  `icon` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='All Sub menu of the project should be placed here';

--
-- Dumping data for table `sub_menu_list`
--

INSERT INTO `sub_menu_list` (`id`, `main_menu`, `sub_menu`, `link`, `icon`) VALUES
(1, 1, 'Dashboard', 'dashboard', 'view_comfy'),
(2, 2, 'Company Creation', 'company_creation', 'domain'),
(3, 2, 'Branch Creation', 'branch_creation', 'add-to-list'),
(4, 2, 'Loan Category Creation', 'loan_category_creation', 'recent_actors'),
(5, 2, 'Area Creation', 'area_creation', 'location'),
(6, 3, 'Bank Creation', 'bank_creation', 'store_mall_directory'),
(7, 3, 'User Creation', 'user_creation', 'person_add'),
(8, 4, 'Customer Creation', 'customer_creation', 'group_add'),
(9, 4, 'Centre Creation', 'centre_creation', 'local_library'),
(10, 4, 'Customer Data', 'customer_data', 'offline_pin'),
(11, 4, 'Centre Summary', 'centre_summary', 'credit-card'),
(12, 5, 'Loan Entry', 'loan_entry', 'devices_other'),
(13, 6, 'Approval', 'approval', 'circle-with-cross'),
(14, 7, 'Loan Issue', 'loan_issue', 'book'),
(15, 10, 'Accounts', 'accounts', 'rate_review'),
(16, 10, 'Bank Clearance', 'bank_clearance', 'assignment'),
(17, 10, 'Balance Sheet', 'balance_sheet', 'colours'),
(18, 11, 'Update Customer', 'update_customer', 'cloud_upload'),
(19, 12, 'Customer Data', 'customer_data', 'person_pin'),
(20, 13, 'Search', 'search_screen', 'search'),
(21, 14, 'Loan Issue Report', 'loan_issue_report', 'area-graph'),
(22, 14, 'Collection Report', 'collection_report', 'event_note'),
(23, 14, 'Balance Report', 'balance_report', 'event_available'),
(24, 14, 'Closed Report', 'closed_report', 'erase'),
(25, 15, 'Bulk Upload Report', 'bulk_upload', 'cloud_done'),
(26, 14, 'Ledger View Report', 'ledger_view_report', 'terrain');

-- --------------------------------------------------------

--
-- Table structure for table `taluks`
--

CREATE TABLE `taluks` (
  `id` int(11) NOT NULL,
  `state_id` int(11) NOT NULL,
  `district_id` int(11) NOT NULL,
  `taluk_name` varchar(50) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `taluks`
--

INSERT INTO `taluks` (`id`, `state_id`, `district_id`, `taluk_name`, `status`) VALUES
(1, 1, 1, 'Ariyalur', 1),
(2, 1, 1, 'Andimadam', 1),
(3, 1, 1, 'Sendurai', 1),
(4, 1, 1, 'Udaiyarpalayam', 1),
(5, 1, 2, 'Alandur', 1),
(6, 1, 2, 'Ambattur', 1),
(7, 1, 2, 'Aminjikarai', 1),
(8, 1, 2, 'Ayanavaram', 1),
(9, 1, 2, 'Egmore', 1),
(10, 1, 2, 'Guindy', 1),
(11, 1, 2, 'Madhavaram', 1),
(12, 1, 2, 'Madhuravoyal', 1),
(13, 1, 2, 'Mambalam', 1),
(14, 1, 2, 'Mylapore', 1),
(15, 1, 2, 'Perambur', 1),
(16, 1, 2, 'Purasavakkam', 1),
(17, 1, 2, 'Sholinganallur', 1),
(18, 1, 2, 'Thiruvottriyur', 1),
(19, 1, 2, 'Tondiarpet', 1),
(20, 1, 2, 'Velacherry', 1),
(21, 1, 3, 'Chengalpattu', 1),
(22, 1, 3, 'Cheyyur', 1),
(23, 1, 3, 'Maduranthakam', 1),
(24, 1, 3, 'Pallavaram', 1),
(25, 1, 3, 'Tambaram', 1),
(26, 1, 3, 'Thirukalukundram', 1),
(27, 1, 3, 'Tiruporur', 1),
(28, 1, 3, 'Vandalur', 1),
(29, 1, 4, 'Aanaimalai', 1),
(30, 1, 4, 'Annur', 1),
(31, 1, 4, 'Coimbatore(North)', 1),
(32, 1, 4, 'Coimbatore(South)', 1),
(33, 1, 4, 'Kinathukadavu', 1),
(34, 1, 4, 'Madukarai', 1),
(35, 1, 4, 'Mettupalayam', 1),
(36, 1, 4, 'Perur', 1),
(37, 1, 4, 'Pollachi', 1),
(38, 1, 4, 'Sulur', 1),
(39, 1, 4, 'Valparai', 1),
(40, 1, 5, 'Cuddalore', 1),
(41, 1, 5, 'Bhuvanagiri', 1),
(42, 1, 5, 'Chidambaram', 1),
(43, 1, 5, 'Kattumannarkoil', 1),
(44, 1, 5, 'Kurinjipadi', 1),
(45, 1, 5, 'Panruti', 1),
(46, 1, 5, 'Srimushnam', 1),
(47, 1, 5, 'Thittakudi', 1),
(48, 1, 5, 'Veppur', 1),
(49, 1, 5, 'Virudhachalam', 1),
(50, 1, 6, 'Dharmapuri', 1),
(51, 1, 6, 'Harur', 1),
(52, 1, 6, 'Karimangalam', 1),
(53, 1, 6, 'Nallampalli', 1),
(54, 1, 6, 'Palacode', 1),
(55, 1, 6, 'Pappireddipatti', 1),
(56, 1, 6, 'Pennagaram', 1),
(57, 1, 7, 'Atthur', 1),
(58, 1, 7, 'Dindigul(East)', 1),
(59, 1, 7, 'Dindigul(West)', 1),
(60, 1, 7, 'Guziliyamparai', 1),
(61, 1, 7, 'Kodaikanal', 1),
(62, 1, 7, 'Natham', 1),
(63, 1, 7, 'Nilakottai', 1),
(64, 1, 7, 'Oddanchatram', 1),
(65, 1, 7, 'Palani', 1),
(66, 1, 7, 'Vedasandur', 1),
(67, 1, 8, 'Erode', 1),
(68, 1, 8, 'Anthiyur', 1),
(69, 1, 8, 'Bhavani', 1),
(70, 1, 8, 'Gobichettipalayam', 1),
(71, 1, 8, 'Kodumudi', 1),
(72, 1, 8, 'Modakurichi', 1),
(73, 1, 8, 'Nambiyur', 1),
(74, 1, 8, 'Perundurai', 1),
(75, 1, 8, 'Sathiyamangalam', 1),
(76, 1, 8, 'Thalavadi', 1),
(77, 1, 9, 'Kallakurichi', 1),
(78, 1, 9, 'Chinnaselam', 1),
(79, 1, 9, 'Kalvarayan Hills', 1),
(80, 1, 9, 'Sankarapuram', 1),
(81, 1, 9, 'Tirukoilur', 1),
(82, 1, 9, 'Ulundurpet', 1),
(83, 1, 10, 'Kancheepuram', 1),
(84, 1, 10, 'Kundrathur', 1),
(85, 1, 10, 'Sriperumbudur', 1),
(86, 1, 10, 'Uthiramerur', 1),
(87, 1, 10, 'Walajabad', 1),
(88, 1, 11, 'Agasteeswaram', 1),
(89, 1, 11, 'Kalkulam', 1),
(90, 1, 11, 'Killiyur', 1),
(91, 1, 11, 'Thiruvatar', 1),
(92, 1, 11, 'Thovalai', 1),
(93, 1, 11, 'Vilavankodu', 1),
(94, 1, 12, 'Karur', 1),
(95, 1, 12, 'Aravakurichi', 1),
(96, 1, 12, 'Kadavur', 1),
(97, 1, 12, 'Krishnarayapuram', 1),
(98, 1, 12, 'Kulithalai', 1),
(99, 1, 12, 'Manmangalam', 1),
(100, 1, 12, 'Pugalur', 1),
(101, 1, 13, 'Krishnagiri', 1),
(102, 1, 13, 'Anjetty', 1),
(103, 1, 13, 'Bargur', 1),
(104, 1, 13, 'Hosur', 1),
(105, 1, 13, 'Pochampalli', 1),
(106, 1, 13, 'Sulagiri', 1),
(107, 1, 13, 'Thenkanikottai', 1),
(108, 1, 13, 'Uthangarai', 1),
(109, 1, 14, 'Kallikudi', 1),
(110, 1, 14, 'Madurai (East)', 1),
(111, 1, 14, 'Madurai (North)', 1),
(112, 1, 14, 'Madurai (South)', 1),
(113, 1, 14, 'Madurai (West)', 1),
(114, 1, 14, 'Melur', 1),
(115, 1, 14, 'Peraiyur', 1),
(116, 1, 14, 'Thirumangalam', 1),
(117, 1, 14, 'Thiruparankundram', 1),
(118, 1, 14, 'Usilampatti', 1),
(119, 1, 14, 'Vadipatti', 1),
(120, 1, 15, 'Mayiladuthurai', 1),
(121, 1, 15, 'Kuthalam', 1),
(122, 1, 15, 'Sirkali', 1),
(123, 1, 15, 'Tharangambadi', 1),
(124, 1, 16, 'Nagapattinam', 1),
(125, 1, 16, 'Kilvelur', 1),
(126, 1, 16, 'Thirukkuvalai', 1),
(127, 1, 16, 'Vedaranyam', 1),
(128, 1, 17, 'Namakkal', 1),
(129, 1, 17, 'Kholli Hills', 1),
(130, 1, 17, 'Kumarapalayam', 1),
(131, 1, 17, 'Mohanoor', 1),
(132, 1, 17, 'Paramathi Velur', 1),
(133, 1, 17, 'Rasipuram', 1),
(134, 1, 17, 'Senthamangalam', 1),
(135, 1, 17, 'Tiruchengode', 1),
(136, 1, 18, 'Udagamandalam', 1),
(137, 1, 18, 'Coonoor', 1),
(138, 1, 18, 'Gudalur', 1),
(139, 1, 18, 'Kothagiri', 1),
(140, 1, 18, 'Kundah', 1),
(141, 1, 18, 'Pandalur', 1),
(142, 1, 19, 'Perambalur', 1),
(143, 1, 19, 'Alathur', 1),
(144, 1, 19, 'Kunnam', 1),
(145, 1, 19, 'Veppanthattai', 1),
(146, 1, 20, 'Pudukottai', 1),
(147, 1, 20, 'Alangudi', 1),
(148, 1, 20, 'Aranthangi', 1),
(149, 1, 20, 'Avudiyarkoil', 1),
(150, 1, 20, 'Gandarvakottai', 1),
(151, 1, 20, 'Iluppur', 1),
(152, 1, 20, 'Karambakudi', 1),
(153, 1, 20, 'Kulathur', 1),
(154, 1, 20, 'Manamelkudi', 1),
(155, 1, 20, 'Ponnamaravathi', 1),
(156, 1, 20, 'Thirumayam', 1),
(157, 1, 20, 'Viralimalai', 1),
(158, 1, 21, 'Ramanathapuram', 1),
(159, 1, 21, 'Kadaladi', 1),
(160, 1, 21, 'Kamuthi', 1),
(161, 1, 21, 'Kezhakarai', 1),
(162, 1, 21, 'Mudukulathur', 1),
(163, 1, 21, 'Paramakudi', 1),
(164, 1, 21, 'Rajasingamangalam', 1),
(165, 1, 21, 'Rameswaram', 1),
(166, 1, 21, 'Tiruvadanai', 1),
(167, 1, 22, 'Arakkonam', 1),
(168, 1, 22, 'Arcot', 1),
(169, 1, 22, 'Kalavai', 1),
(170, 1, 22, 'Nemili', 1),
(171, 1, 22, 'Sholingur', 1),
(172, 1, 22, 'Walajah', 1),
(173, 1, 23, 'Salem', 1),
(174, 1, 23, 'Attur', 1),
(175, 1, 23, 'Edapadi', 1),
(176, 1, 23, 'Gangavalli', 1),
(177, 1, 23, 'Kadaiyampatti', 1),
(178, 1, 23, 'Mettur', 1),
(179, 1, 23, 'Omalur', 1),
(180, 1, 23, 'Pethanayakanpalayam', 1),
(181, 1, 23, 'Salem South', 1),
(182, 1, 23, 'Salem West', 1),
(183, 1, 23, 'Sankari', 1),
(184, 1, 23, 'Vazhapadi', 1),
(185, 1, 23, 'Yercaud', 1),
(186, 1, 24, 'Sivagangai', 1),
(187, 1, 24, 'Devakottai', 1),
(188, 1, 24, 'Ilayankudi', 1),
(189, 1, 24, 'Kalaiyarkovil', 1),
(190, 1, 24, 'Karaikudi', 1),
(191, 1, 24, 'Manamadurai', 1),
(192, 1, 24, 'Singampunari', 1),
(193, 1, 24, 'Thirupuvanam', 1),
(194, 1, 24, 'Tirupathur', 1),
(195, 1, 25, 'Tenkasi', 1),
(196, 1, 25, 'Alangulam', 1),
(197, 1, 25, 'Kadayanallur', 1),
(198, 1, 25, 'Sankarankovil', 1),
(199, 1, 25, 'Shenkottai', 1),
(200, 1, 25, 'Sivagiri', 1),
(201, 1, 25, 'Thiruvengadam', 1),
(202, 1, 25, 'Veerakeralampudur', 1),
(203, 1, 26, 'Thanjavur', 1),
(204, 1, 26, 'Boothalur', 1),
(205, 1, 26, 'Kumbakonam', 1),
(206, 1, 26, 'Orathanadu', 1),
(207, 1, 26, 'Papanasam', 1),
(208, 1, 26, 'Pattukottai', 1),
(209, 1, 26, 'Peravurani', 1),
(210, 1, 26, 'Thiruvaiyaru', 1),
(211, 1, 26, 'Thiruvidaimaruthur', 1),
(212, 1, 27, 'Theni', 1),
(213, 1, 27, 'Aandipatti', 1),
(214, 1, 27, 'Bodinayakanur', 1),
(215, 1, 27, 'Periyakulam', 1),
(216, 1, 27, 'Uthamapalayam', 1),
(217, 1, 28, 'Thoothukudi', 1),
(218, 1, 28, 'Eral', 1),
(219, 1, 28, 'Ettayapuram', 1),
(220, 1, 28, 'Kayathar', 1),
(221, 1, 28, 'Kovilpatti', 1),
(222, 1, 28, 'Ottapidaram', 1),
(223, 1, 28, 'Sattankulam', 1),
(224, 1, 28, 'Srivaikundam', 1),
(225, 1, 28, 'Tiruchendur', 1),
(226, 1, 28, 'Vilathikulam', 1),
(227, 1, 29, 'Lalgudi', 1),
(228, 1, 29, 'Manachanallur', 1),
(229, 1, 29, 'Manapparai', 1),
(230, 1, 29, 'Marungapuri', 1),
(231, 1, 29, 'Musiri', 1),
(232, 1, 29, 'Srirangam', 1),
(233, 1, 29, 'Thottiam', 1),
(234, 1, 29, 'Thuraiyur', 1),
(235, 1, 29, 'Tiruchirapalli (West)', 1),
(236, 1, 29, 'Tiruchirappalli (East)', 1),
(237, 1, 29, 'Tiruverumbur', 1),
(238, 1, 30, 'Tirunelveli', 1),
(239, 1, 30, 'Ambasamudram', 1),
(240, 1, 30, 'Cheranmahadevi', 1),
(241, 1, 30, 'Manur', 1),
(242, 1, 30, 'Nanguneri', 1),
(243, 1, 30, 'Palayamkottai', 1),
(244, 1, 30, 'Radhapuram', 1),
(245, 1, 30, 'Thisayanvilai', 1),
(246, 1, 31, 'Avinashi', 1),
(247, 1, 31, 'Dharapuram', 1),
(248, 1, 31, 'Kangeyam', 1),
(249, 1, 31, 'Madathukkulam', 1),
(250, 1, 31, 'Oothukuli', 1),
(251, 1, 31, 'Palladam', 1),
(252, 1, 31, 'Tiruppur (North)', 1),
(253, 1, 31, 'Tiruppur (South)', 1),
(254, 1, 31, 'Udumalaipettai', 1),
(255, 1, 32, 'Tirupathur\"', 1),
(256, 1, 32, 'Ambur', 1),
(257, 1, 32, 'Natrampalli', 1),
(258, 1, 32, 'Vaniyambadi', 1),
(259, 1, 33, 'Thiruvallur', 1),
(260, 1, 33, 'Avadi', 1),
(261, 1, 33, 'Gummidipoondi', 1),
(262, 1, 33, 'Pallipattu', 1),
(263, 1, 33, 'Ponneri', 1),
(264, 1, 33, 'Poonamallee', 1),
(265, 1, 33, 'R.K. Pet', 1),
(266, 1, 33, 'Tiruthani', 1),
(267, 1, 33, 'Uthukottai', 1),
(268, 1, 34, 'Thiruvannamalai', 1),
(269, 1, 34, 'Arni', 1),
(270, 1, 34, 'Chengam', 1),
(271, 1, 34, 'Chetpet', 1),
(272, 1, 34, 'Cheyyar', 1),
(273, 1, 34, 'Jamunamarathur', 1),
(274, 1, 34, 'Kalasapakkam', 1),
(275, 1, 34, 'Kilpennathur', 1),
(276, 1, 34, 'Polur', 1),
(277, 1, 34, 'Thandramet', 1),
(278, 1, 34, 'Vandavasi', 1),
(279, 1, 34, 'Vembakkam', 1),
(280, 1, 35, 'Thiruvarur', 1),
(281, 1, 35, 'Kodavasal', 1),
(282, 1, 35, 'Koothanallur', 1),
(283, 1, 35, 'Mannargudi', 1),
(284, 1, 35, 'Nannilam', 1),
(285, 1, 35, 'Needamangalam', 1),
(286, 1, 35, 'Thiruthuraipoondi', 1),
(287, 1, 35, 'Valangaiman', 1),
(288, 1, 36, 'Vellore', 1),
(289, 1, 36, 'Aanikattu', 1),
(290, 1, 36, 'Gudiyatham', 1),
(291, 1, 36, 'K V Kuppam', 1),
(292, 1, 36, 'Katpadi', 1),
(293, 1, 36, 'Pernambut', 1),
(294, 1, 37, 'Villupuram', 1),
(295, 1, 37, 'Gingee', 1),
(296, 1, 37, 'Kandachipuram', 1),
(297, 1, 37, 'Marakanam', 1),
(298, 1, 37, 'Melmalaiyanur', 1),
(299, 1, 37, 'Thiruvennainallur', 1),
(300, 1, 37, 'Tindivanam', 1),
(301, 1, 37, 'Vanur', 1),
(302, 1, 37, 'Vikravandi', 1),
(303, 1, 38, 'Virudhunagar', 1),
(304, 1, 38, 'Aruppukottai', 1),
(305, 1, 38, 'Kariyapatti', 1),
(306, 1, 38, 'Rajapalayam', 1),
(307, 1, 38, 'Sathur', 1),
(308, 1, 38, 'Sivakasi', 1),
(309, 1, 38, 'Srivilliputhur', 1),
(310, 1, 38, 'Tiruchuli', 1),
(311, 1, 38, 'Vembakottai', 1),
(312, 1, 38, 'Watrap', 1),
(313, 2, 39, 'Puducherry', 1),
(314, 2, 39, 'Oulgaret', 1),
(315, 2, 39, 'Villianur', 1),
(316, 2, 39, 'Bahour', 1),
(317, 2, 39, 'Karaikal', 1),
(318, 2, 39, 'Thirunallar', 1),
(319, 2, 39, 'Mahe', 1),
(320, 2, 39, 'Yanam', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `user_code` varchar(100) NOT NULL,
  `role` int(11) NOT NULL,
  `designation` int(11) NOT NULL,
  `address` varchar(100) NOT NULL,
  `place` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mobile` varchar(100) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `branch` varchar(255) NOT NULL,
  `loan_category` varchar(255) NOT NULL,
  `line` varchar(255) NOT NULL,
  `collection_access` int(11) NOT NULL,
  `download_access` int(11) NOT NULL,
  `screens` varchar(255) NOT NULL,
  `insert_login_id` varchar(100) NOT NULL,
  `update_login_id` varchar(100) NOT NULL,
  `created_on` date NOT NULL,
  `updated_on` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='All the users will be stored here with screen access details';

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `user_code`, `role`, `designation`, `address`, `place`, `email`, `mobile`, `user_name`, `password`, `branch`, `loan_category`, `line`, `collection_access`, `download_access`, `screens`, `insert_login_id`, `update_login_id`, `created_on`, `updated_on`) VALUES
(1, 'Super Admin', 'US-001', 1, 1, '', '', '', '', 'admin', '123', '1,2,3', '3,4,5,6,7,8', '1,2,6,8,10', 1, 1, '1,2,3,4,5,6,7,8,9,12,13,14', '1', '1', '2024-06-13', '2024-09-17');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `area_creation`
--
ALTER TABLE `area_creation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `branch` (`branch_id`),
  ADD KEY `area_id` (`area_id`);

--
-- Indexes for table `area_name_creation`
--
ALTER TABLE `area_name_creation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `branchid` (`branch_id`);

--
-- Indexes for table `bank_creation`
--
ALTER TABLE `bank_creation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `branch_creation`
--
ALTER TABLE `branch_creation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `state_id` (`state`),
  ADD KEY `district_id` (`district`),
  ADD KEY `taluk_id` (`taluk`);

--
-- Indexes for table `centre_creation`
--
ALTER TABLE `centre_creation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `branch_id` (`branch`),
  ADD KEY `area_id` (`area`);

--
-- Indexes for table `company_creation`
--
ALTER TABLE `company_creation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `State ids` (`state`),
  ADD KEY `District ids` (`district`),
  ADD KEY `Taluk ids` (`taluk`);

--
-- Indexes for table `company_document`
--
ALTER TABLE `company_document`
  ADD PRIMARY KEY (`s_no`);

--
-- Indexes for table `customer_creation`
--
ALTER TABLE `customer_creation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `districts`
--
ALTER TABLE `districts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `State id` (`state_id`);

--
-- Indexes for table `document_info`
--
ALTER TABLE `document_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `family_info`
--
ALTER TABLE `family_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loan_category`
--
ALTER TABLE `loan_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loan_category_creation`
--
ALTER TABLE `loan_category_creation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Loan Category` (`loan_category`);

--
-- Indexes for table `loan_cus_mapping`
--
ALTER TABLE `loan_cus_mapping`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cus_id` (`cus_id`);

--
-- Indexes for table `loan_entry_loan_calculation`
--
ALTER TABLE `loan_entry_loan_calculation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loan_issue`
--
ALTER TABLE `loan_issue`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_list`
--
ALTER TABLE `menu_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `representative_info`
--
ALTER TABLE `representative_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `scheme`
--
ALTER TABLE `scheme`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `states`
--
ALTER TABLE `states`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sub_menu_list`
--
ALTER TABLE `sub_menu_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Main menu id` (`main_menu`);

--
-- Indexes for table `taluks`
--
ALTER TABLE `taluks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `District id` (`district_id`),
  ADD KEY `States id` (`state_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Role id` (`role`),
  ADD KEY `Designation id` (`designation`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `area_creation`
--
ALTER TABLE `area_creation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `area_name_creation`
--
ALTER TABLE `area_name_creation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `bank_creation`
--
ALTER TABLE `bank_creation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `branch_creation`
--
ALTER TABLE `branch_creation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `centre_creation`
--
ALTER TABLE `centre_creation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `company_creation`
--
ALTER TABLE `company_creation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `company_document`
--
ALTER TABLE `company_document`
  MODIFY `s_no` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `customer_creation`
--
ALTER TABLE `customer_creation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `districts`
--
ALTER TABLE `districts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `document_info`
--
ALTER TABLE `document_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `family_info`
--
ALTER TABLE `family_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `loan_category`
--
ALTER TABLE `loan_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `loan_category_creation`
--
ALTER TABLE `loan_category_creation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `loan_cus_mapping`
--
ALTER TABLE `loan_cus_mapping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `loan_entry_loan_calculation`
--
ALTER TABLE `loan_entry_loan_calculation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `loan_issue`
--
ALTER TABLE `loan_issue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `menu_list`
--
ALTER TABLE `menu_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `representative_info`
--
ALTER TABLE `representative_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `scheme`
--
ALTER TABLE `scheme`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `states`
--
ALTER TABLE `states`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sub_menu_list`
--
ALTER TABLE `sub_menu_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `taluks`
--
ALTER TABLE `taluks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=321;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `area_name_creation`
--
ALTER TABLE `area_name_creation`
  ADD CONSTRAINT `branchid` FOREIGN KEY (`branch_id`) REFERENCES `branch_creation` (`id`) ON UPDATE NO ACTION;

--
-- Constraints for table `branch_creation`
--
ALTER TABLE `branch_creation`
  ADD CONSTRAINT `district_id` FOREIGN KEY (`district`) REFERENCES `districts` (`id`),
  ADD CONSTRAINT `state_id` FOREIGN KEY (`state`) REFERENCES `states` (`id`),
  ADD CONSTRAINT `taluk_id` FOREIGN KEY (`taluk`) REFERENCES `taluks` (`id`);

--
-- Constraints for table `centre_creation`
--
ALTER TABLE `centre_creation`
  ADD CONSTRAINT `area_id` FOREIGN KEY (`area`) REFERENCES `area_creation` (`area_id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `branch_id` FOREIGN KEY (`branch`) REFERENCES `branch_creation` (`id`) ON UPDATE NO ACTION;

--
-- Constraints for table `company_creation`
--
ALTER TABLE `company_creation`
  ADD CONSTRAINT `District ids` FOREIGN KEY (`district`) REFERENCES `districts` (`id`),
  ADD CONSTRAINT `State ids` FOREIGN KEY (`state`) REFERENCES `states` (`id`),
  ADD CONSTRAINT `Taluk ids` FOREIGN KEY (`taluk`) REFERENCES `taluks` (`id`);

--
-- Constraints for table `districts`
--
ALTER TABLE `districts`
  ADD CONSTRAINT `State id` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`);

--
-- Constraints for table `loan_category_creation`
--
ALTER TABLE `loan_category_creation`
  ADD CONSTRAINT `Loan Category` FOREIGN KEY (`loan_category`) REFERENCES `loan_category` (`id`) ON UPDATE NO ACTION;

--
-- Constraints for table `loan_cus_mapping`
--
ALTER TABLE `loan_cus_mapping`
  ADD CONSTRAINT `cus_id` FOREIGN KEY (`cus_id`) REFERENCES `customer_creation` (`id`) ON UPDATE NO ACTION;

--
-- Constraints for table `sub_menu_list`
--
ALTER TABLE `sub_menu_list`
  ADD CONSTRAINT `Main menu id` FOREIGN KEY (`main_menu`) REFERENCES `menu_list` (`id`);

--
-- Constraints for table `taluks`
--
ALTER TABLE `taluks`
  ADD CONSTRAINT `District id` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`),
  ADD CONSTRAINT `States id` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
