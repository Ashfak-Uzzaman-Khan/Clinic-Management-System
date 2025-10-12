-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 12, 2025 at 05:18 PM
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
-- Database: `hdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `adminlog`
--

CREATE TABLE `adminlog` (
  `admin_ID` int(255) NOT NULL,
  `admin_name` varchar(100) DEFAULT NULL,
  `admin_email` varchar(100) NOT NULL,
  `admin_pass` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `adminlog`
--

INSERT INTO `adminlog` (`admin_ID`, `admin_name`, `admin_email`, `admin_pass`) VALUES
(1, 'admin1', 'admin1@gmail.com', '1234a1'),
(2, 'admin2', 'admin2@gmail.com', '1234a2');

-- --------------------------------------------------------

--
-- Table structure for table `ambulanced`
--

CREATE TABLE `ambulanced` (
  `a_ID` int(100) NOT NULL,
  `patient_name` varchar(100) NOT NULL,
  `patient_email` varchar(100) NOT NULL,
  `situation_case` varchar(100) NOT NULL,
  `a_type` varchar(100) NOT NULL,
  `patient_contactNO` varchar(100) NOT NULL,
  `a_address` varchar(100) NOT NULL,
  `a_status` varchar(100) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ambulanced`
--

INSERT INTO `ambulanced` (`a_ID`, `patient_name`, `patient_email`, `situation_case`, `a_type`, `patient_contactNO`, `a_address`, `a_status`) VALUES
(5, 'p1', 'p1@gmail.com', 'normal', 'Oxygen', '01775415121', 'sada', 'rejected'),
(12, 'p1', 'p2@gmail.com', 'normal', 'Oxygen', '01775415121', 'sada', 'approved'),
(13, 'p1', 'p3@gmail.com', 'normal', 'Oxygen', '01775415121', 'ihdg', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `appointment_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `patient_name` varchar(100) NOT NULL,
  `patient_email` varchar(100) NOT NULL,
  `patient_contact` varchar(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `doctor_name` varchar(100) NOT NULL,
  `doctor_email` varchar(100) NOT NULL,
  `doctor_department` varchar(50) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` varchar(50) NOT NULL,
  `reason` text NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`appointment_id`, `patient_id`, `patient_name`, `patient_email`, `patient_contact`, `doctor_id`, `doctor_name`, `doctor_email`, `doctor_department`, `appointment_date`, `appointment_time`, `reason`, `status`, `created_at`) VALUES
(1, 1, 'p1', 'p1@gmail.com', '01701567890', 1, 'd1', 'd1@gmail.com', 'Neurology', '2025-10-17', '09:00 AM - 10:00 AM', 'Severe Cough', 'approved', '2025-10-12 01:30:56'),
(2, 1, 'p1', 'p1@gmail.com', '01701567899', 1, 'd1', 'd1@gmail.com', 'Neurology', '2025-10-12', '11:00 AM - 12:00 PM', 'severe acne', 'pending', '2025-10-12 02:04:31');

-- --------------------------------------------------------

--
-- Table structure for table `chat`
--

CREATE TABLE `chat` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `sender_type` enum('patient','doctor','admin') NOT NULL,
  `receiver_type` enum('patient','doctor','admin') NOT NULL,
  `message` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('sent','delivered','read') DEFAULT 'sent'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat`
--

INSERT INTO `chat` (`id`, `sender_id`, `receiver_id`, `sender_type`, `receiver_type`, `message`, `timestamp`, `status`) VALUES
(1, 1, 1, 'patient', 'patient', 'p1', '2025-10-12 04:26:25', 'sent'),
(2, 1, 2, 'patient', 'doctor', 'hello d2 cardio', '2025-10-12 04:27:03', 'sent'),
(3, 1, 1, 'patient', 'patient', 'ka', '2025-10-12 04:27:25', 'sent'),
(4, 1, 2, 'patient', 'doctor', 'hai', '2025-10-12 04:27:34', 'sent'),
(5, 1, 2, 'patient', 'patient', 'hai', '2025-10-12 04:28:57', 'sent'),
(6, 1, 1, 'patient', 'patient', 'hai', '2025-10-12 04:29:11', 'sent'),
(7, 1, 2, 'patient', 'patient', 'hai', '2025-10-12 04:29:18', 'sent'),
(8, 1, 2, 'patient', 'patient', 'hello', '2025-10-12 04:29:32', 'sent'),
(9, 1, 1, 'patient', 'patient', 'he', '2025-10-12 04:29:39', 'sent'),
(10, 1, 1, 'patient', 'patient', 'ka', '2025-10-12 04:29:46', 'sent'),
(11, 1, 1, 'patient', 'patient', 'ka', '2025-10-12 09:18:58', 'sent'),
(12, 1, 1, 'patient', 'patient', 'p1', '2025-10-12 09:19:14', 'sent'),
(13, 1, 1, 'doctor', 'patient', 'hello patient 1', '2025-10-12 09:43:03', 'sent'),
(14, 1, 1, 'doctor', 'patient', 'its doc 1', '2025-10-12 09:43:22', 'sent'),
(15, 1, 1, 'doctor', 'patient', '1', '2025-10-12 09:43:42', 'sent'),
(16, 1, 1, 'doctor', 'patient', 'wq', '2025-10-12 09:45:19', 'sent'),
(17, 1, 1, 'doctor', 'patient', '1', '2025-10-12 09:50:05', 'sent'),
(18, 1, 1, 'patient', 'doctor', 'i am p2', '2025-10-12 10:26:13', 'sent'),
(19, 1, 2, 'patient', 'doctor', 's', '2025-10-12 10:26:33', 'sent');

-- --------------------------------------------------------

--
-- Table structure for table `doctorlog`
--

CREATE TABLE `doctorlog` (
  `doctor_ID` int(11) NOT NULL,
  `doc_name` varchar(100) NOT NULL,
  `doc_email` varchar(100) NOT NULL,
  `doc_pass` varchar(255) NOT NULL,
  `doc_ContactNO` varchar(11) NOT NULL,
  `doc_age` int(11) NOT NULL,
  `doc_gender` varchar(100) NOT NULL,
  `doc_bloodGrp` varchar(5) NOT NULL,
  `doc_dob` date NOT NULL,
  `doc_address` text NOT NULL,
  `doc_emgContactNo` varchar(11) NOT NULL,
  `doc_dept` varchar(50) NOT NULL,
  `doc_quali` varchar(100) NOT NULL,
  `doc_exp` int(11) NOT NULL,
  `doc_college` varchar(100) NOT NULL,
  `doc_licenceNo` varchar(50) NOT NULL,
  `doc_shift` varchar(100) NOT NULL,
  `doc_fees` int(11) NOT NULL,
  `application_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(100) DEFAULT 'pending',
  `doc_statusT` varchar(100) NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctorlog`
--

INSERT INTO `doctorlog` (`doctor_ID`, `doc_name`, `doc_email`, `doc_pass`, `doc_ContactNO`, `doc_age`, `doc_gender`, `doc_bloodGrp`, `doc_dob`, `doc_address`, `doc_emgContactNo`, `doc_dept`, `doc_quali`, `doc_exp`, `doc_college`, `doc_licenceNo`, `doc_shift`, `doc_fees`, `application_date`, `status`, `doc_statusT`) VALUES
(1, 'd1', 'd1@gmail.com', '1234d1', '01711519801', 23, 'Female', '0+', '2001-11-11', 'Bandar, N.ganj -', '01123456789', 'Neurology', 'FCPS, MD, MBBS', 5, 'Dhaka Medical College', '123-A3', 'Morning', 500, '2025-10-11 00:43:32', 'approved', 'On-leave'),
(2, 'd2', 'd2@gmail.com', '1234d2', '01711519810', 23, 'Male', '0+', '2007-02-21', 'Sadar , N.ganj', '01123456789', 'Cardiology', 'MD, MBBS', 3, 'SHMC', '123-A20', 'Night', 300, '2025-10-12 03:40:15', 'approved', 'On-leave');

-- --------------------------------------------------------

--
-- Table structure for table `patientlog`
--

CREATE TABLE `patientlog` (
  `patient_ID` int(11) NOT NULL,
  `patient_name` varchar(100) DEFAULT NULL,
  `patient_email` varchar(100) NOT NULL,
  `patient_pass` varchar(100) DEFAULT NULL,
  `patient_gender` varchar(100) DEFAULT NULL,
  `patient_contactNO` varchar(100) DEFAULT NULL,
  `patient_emNo` varchar(100) DEFAULT NULL,
  `patient_age` int(11) DEFAULT NULL,
  `patient_bloodGrp` varchar(100) DEFAULT NULL,
  `patient_address` varchar(100) DEFAULT NULL,
  `patient_dob` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patientlog`
--

INSERT INTO `patientlog` (`patient_ID`, `patient_name`, `patient_email`, `patient_pass`, `patient_gender`, `patient_contactNO`, `patient_emNo`, `patient_age`, `patient_bloodGrp`, `patient_address`, `patient_dob`) VALUES
(1, 'p1', 'p1@gmail.com', '1234p1', 'Male', '01711519810', '01123456789', 23, 'B+', 'Bandar, N.ganj', '2001-11-01');

-- --------------------------------------------------------

--
-- Table structure for table `testop`
--

CREATE TABLE `testop` (
  `test_ID` int(100) NOT NULL,
  `patient_name` varchar(100) NOT NULL,
  `patient_email` varchar(100) NOT NULL,
  `test_type` varchar(100) NOT NULL,
  `test_date` date NOT NULL,
  `test_time` time(6) NOT NULL,
  `test_status` varchar(100) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `testop`
--

INSERT INTO `testop` (`test_ID`, `patient_name`, `patient_email`, `test_type`, `test_date`, `test_time`, `test_status`) VALUES
(1, 'p1', 'p1@gmail.com', 'MRI, X-Ray, CT-scan', '2025-10-12', '16:29:00.000000', 'pending');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adminlog`
--
ALTER TABLE `adminlog`
  ADD PRIMARY KEY (`admin_ID`),
  ADD UNIQUE KEY `admin_email` (`admin_email`);

--
-- Indexes for table `ambulanced`
--
ALTER TABLE `ambulanced`
  ADD PRIMARY KEY (`a_ID`),
  ADD UNIQUE KEY `patient_email` (`patient_email`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `doctorlog`
--
ALTER TABLE `doctorlog`
  ADD PRIMARY KEY (`doctor_ID`),
  ADD UNIQUE KEY `doc_email` (`doc_email`);

--
-- Indexes for table `patientlog`
--
ALTER TABLE `patientlog`
  ADD PRIMARY KEY (`patient_ID`),
  ADD UNIQUE KEY `patient_email` (`patient_email`);

--
-- Indexes for table `testop`
--
ALTER TABLE `testop`
  ADD PRIMARY KEY (`test_ID`),
  ADD UNIQUE KEY `patient_email` (`patient_email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `adminlog`
--
ALTER TABLE `adminlog`
  MODIFY `admin_ID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ambulanced`
--
ALTER TABLE `ambulanced`
  MODIFY `a_ID` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `chat`
--
ALTER TABLE `chat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `doctorlog`
--
ALTER TABLE `doctorlog`
  MODIFY `doctor_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `patientlog`
--
ALTER TABLE `patientlog`
  MODIFY `patient_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `testop`
--
ALTER TABLE `testop`
  MODIFY `test_ID` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patientlog` (`patient_ID`),
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctorlog` (`doctor_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
