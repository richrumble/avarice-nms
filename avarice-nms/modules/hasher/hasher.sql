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

