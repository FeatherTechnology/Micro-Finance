-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 02, 2024 at 02:05 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `finance`
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
(1, 'Master', 'master', 'cw'),
(2, 'Administration', 'admin', 'globe'),
(3, 'Loan Entry', 'loan_entry', 'globe'),
(4, 'Approval', 'approval', 'globe'),
(5, 'Loan Issue', 'loan_issue', 'globe'),
(6, 'Collection', 'collection', 'globe'),
(7, 'Closed', 'closed', 'globe'),
(8, 'NOC', 'noc', 'globe'),
(9, 'Accounts', 'accounts', 'globe'),
(10, 'Update', 'update', 'globe'),
(11, 'Customer Data', 'customer_data', 'globe'),
(12, 'Search', 'search', 'globe'),
(13, 'Reports', 'reports', 'globe');

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
(1, 1, 'Company Creation', 'company_creation', 'upload-to-cloud'),
(2, 1, 'Branch Creation', 'branch_creation_list', 'upload-to-cloud'),
(3, 1, 'Loan Category Creation', 'loan_category_creation_list', 'upload-to-cloud'),
(4, 1, 'Area Creation', 'area_creation_list', 'upload-to-cloud'),
(5, 2, 'Bank Creation', 'bank_creation_list', 'upload-to-cloud'),
(6, 2, 'Agent Creation', 'agent_creation_list', 'upload-to-cloud'),
(7, 2, 'User Creation', 'user_creation_list', 'upload-to-cloud'),
(8, 3, 'Loan Entry', 'loan_entry_list', 'upload-to-cloud'),
(9, 4, 'Approval', 'approval_list', 'upload-to-cloud'),
(10, 5, 'Loan Issue', 'loan_issue_list', 'upload-to-cloud'),
(11, 6, 'Collection', 'collection_list', 'upload-to-cloud'),
(12, 7, 'Closed', 'closed_list', 'upload-to-cloud'),
(13, 8, 'NOC', 'noc_list', 'upload-to-cloud'),
(14, 9, 'Accounts', 'accounts_list', 'upload-to-cloud'),
(15, 10, 'Customer', 'update_customer_list', 'upload-to-cloud'),
(16, 11, 'Customer Data', 'customer_data_list', 'upload-to-cloud'),
(17, 12, 'Search', 'search_list', 'upload-to-cloud'),
(18, 13, 'Reports', 'reports_list', 'upload-to-cloud'),
(19, 10, 'Document', 'update_document_list', 'cloud');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `user_code` varchar(100) NOT NULL,
  `role` varchar(100) NOT NULL,
  `designation` varchar(100) NOT NULL,
  `address` varchar(100) NOT NULL,
  `place` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mobile` varchar(100) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `branch` varchar(255) NOT NULL,
  `loan_category` varchar(255) NOT NULL,
  `line` varchar(255) NOT NULL,
  `screens` varchar(255) NOT NULL,
  `insert_login_id` varchar(100) NOT NULL,
  `update_login_id` varchar(100) NOT NULL,
  `created_on` date NOT NULL,
  `updated_on` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='All the users will be stored here with screen access details';

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `user_code`, `role`, `designation`, `address`, `place`, `email`, `mobile`, `user_name`, `password`, `branch`, `loan_category`, `line`, `screens`, `insert_login_id`, `update_login_id`, `created_on`, `updated_on`) VALUES
(1, 'Arun', 'US-001', 'Developer', 'Software Developer', '', '', '', '', 'arun', '123', '1,5,6', '2,4,9', '1,2,5,8,9,11', '1,2,3,4,5,6,7,15,19', '', '', '2024-05-02', '2024-05-02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `menu_list`
--
ALTER TABLE `menu_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sub_menu_list`
--
ALTER TABLE `sub_menu_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Main menu id` (`main_menu`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `menu_list`
--
ALTER TABLE `menu_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `sub_menu_list`
--
ALTER TABLE `sub_menu_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `sub_menu_list`
--
ALTER TABLE `sub_menu_list`
  ADD CONSTRAINT `Main menu id` FOREIGN KEY (`main_menu`) REFERENCES `menu_list` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
