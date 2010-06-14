-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 14, 2010 at 01:56 PM
-- Server version: 5.1.36
-- PHP Version: 5.2.11

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `test`
--
CREATE DATABASE `test` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `test`;

-- --------------------------------------------------------

--
-- Table structure for table `drive_data`
--

CREATE TABLE IF NOT EXISTS `drive_data` (
  `DateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `driveID` int(11) NOT NULL,
  `usedMB` int(11) DEFAULT NULL,
  `freePerc` int(11) DEFAULT NULL,
  `IOWaits` int(11) DEFAULT NULL,
  UNIQUE KEY `DateTime` (`DateTime`,`driveID`),
  KEY `driveID` (`driveID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `drive_data`
--

INSERT INTO `drive_data` (`DateTime`, `driveID`, `usedMB`, `freePerc`, `IOWaits`) VALUES
('2010-06-14 13:55:17', 1, 123, 99, 2),
('2010-06-14 13:55:37', 1, 123, 4, 4),
('2010-06-14 13:56:21', 1, 123, 87, 2),
('2010-06-14 13:56:21', 2, 456, 86, 1);

-- --------------------------------------------------------

--
-- Table structure for table `drive_info`
--

CREATE TABLE IF NOT EXISTS `drive_info` (
  `driveID` int(11) NOT NULL AUTO_INCREMENT,
  `computerID` int(11) NOT NULL,
  `manufacturer` varchar(64) DEFAULT NULL,
  `size` varchar(64) NOT NULL,
  `speed` varchar(64) NOT NULL,
  PRIMARY KEY (`driveID`),
  KEY `computerID` (`computerID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `drive_info`
--

INSERT INTO `drive_info` (`driveID`, `computerID`, `manufacturer`, `size`, `speed`) VALUES
(1, 1, 'Bubba''s', 'Eleventy Billion Bytes', '120 MPH'),
(2, 1, 'Bubba''s2', 'Eleventy Billion Bytes', '120 MPH');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `drive_data`
--
ALTER TABLE `drive_data`
  ADD CONSTRAINT `drive_data_ibfk_1` FOREIGN KEY (`driveID`) REFERENCES `drive_info` (`driveID`);
