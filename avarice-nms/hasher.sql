-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 17, 2010 at 03:32 PM
-- Server version: 5.1.36
-- PHP Version: 5.3.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `hasher`
--

-- --------------------------------------------------------

--
-- Table structure for table `file-to-hash`
--

DROP TABLE IF EXISTS `file-to-hash`;
CREATE TABLE IF NOT EXISTS `file-to-hash` (
  `file_to_hash` int(64) NOT NULL,
  `file_ID` int(64) NOT NULL,
  `hash_ID` int(64) NOT NULL,
  `comments` varchar(255) NOT NULL,
  UNIQUE KEY `file_to_hash` (`file_to_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `file-to-hash`
--

INSERT INTO `file-to-hash` (`file_to_hash`, `file_ID`, `hash_ID`, `comments`) VALUES
(99, 34, 453, ''),
(100, 35, 453, ''),
(101, 36, 454, '');

-- --------------------------------------------------------

--
-- Table structure for table `filenames`
--

DROP TABLE IF EXISTS `filenames`;
CREATE TABLE IF NOT EXISTS `filenames` (
  `file_ID` int(64) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `comments` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `filenames`
--

INSERT INTO `filenames` (`file_ID`, `file_name`, `comments`) VALUES
(34, 'readme.bak', ''),
(35, 'readme.txt', ''),
(36, 'setup.exe', '');

-- --------------------------------------------------------

--
-- Table structure for table `hashes`
--

DROP TABLE IF EXISTS `hashes`;
CREATE TABLE IF NOT EXISTS `hashes` (
  `hash_ID` int(64) NOT NULL,
  `md5` varchar(32) NOT NULL,
  `sha1` varchar(40) NOT NULL,
  `sha256` varchar(64) NOT NULL,
  `comments` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `hashes`
--

INSERT INTO `hashes` (`hash_ID`, `md5`, `sha1`, `sha256`, `comments`) VALUES
(453, 'ef939e96693797489bec5fff64663232', '39d656c8c0c80d03d5a11bb35871dd14e7b43fe7', 'fb2f9da6aa23a05ed27a190cbb92b422b9de554a92be98082643d59c9e915eec', 0),
(454, '74455b492ca3c466f5168062637df2ed', 'f013c5d144e8847c7d2e47cf49597b489f269cc0', '1cf5183b0f2504c6073768bb302df79b43c30ceadb925629aa4a266c27ab8863', 0);

-- --------------------------------------------------------

--
-- Table structure for table `machine`
--

DROP TABLE IF EXISTS `machine`;
CREATE TABLE IF NOT EXISTS `machine` (
  `machine_ID` int(64) NOT NULL,
  `machine_serial` varchar(255) NOT NULL,
  `machine_UUID` varchar(255) NOT NULL,
  `machine_name` varchar(64) NOT NULL,
  `comments` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `machine`
--

INSERT INTO `machine` (`machine_ID`, `machine_serial`, `machine_UUID`, `machine_name`, `comments`) VALUES
(14, '7hcx98v', '4C4C4544-0033-3310-8042-B7C04F534631', 'laptop_311', '');

-- --------------------------------------------------------

--
-- Table structure for table `path-to-hash`
--

DROP TABLE IF EXISTS `path-to-hash`;
CREATE TABLE IF NOT EXISTS `path-to-hash` (
  `machine_ID` int(64) NOT NULL,
  `root_ID` varchar(64) NOT NULL,
  `path_ID` int(64) NOT NULL,
  `file_ID` int(64) NOT NULL,
  `comments` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `path-to-hash`
--

INSERT INTO `path-to-hash` (`machine_ID`, `root_ID`, `path_ID`, `file_ID`, `comments`) VALUES
(14, '3', 22, 99, ''),
(14, '3', 22, 100, ''),
(14, '3', 22, 101, '');

-- --------------------------------------------------------

--
-- Table structure for table `paths`
--

DROP TABLE IF EXISTS `paths`;
CREATE TABLE IF NOT EXISTS `paths` (
  `path_ID` int(64) NOT NULL,
  `path_name` varchar(4096) NOT NULL,
  `comments` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `paths`
--


-- --------------------------------------------------------

--
-- Table structure for table `root`
--

DROP TABLE IF EXISTS `root`;
CREATE TABLE IF NOT EXISTS `root` (
  `root_ID` int(64) NOT NULL,
  `root_name` varchar(64) NOT NULL,
  `comments` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `root`
--

