-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 18, 2010 at 04:07 AM
-- Server version: 5.1.36
-- PHP Version: 5.3.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `avarice_nms`
--

-- --------------------------------------------------------

--
-- Table structure for table `config`
--

CREATE TABLE IF NOT EXISTS `config` (
  `parameter` varchar(64) NOT NULL,
  `value` varchar(128) NOT NULL,
  PRIMARY KEY (`parameter`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `groupID` int(11) NOT NULL AUTO_INCREMENT,
  `fkgroupID` int(11) DEFAULT NULL,
  `groupName` varchar(64) NOT NULL,
  `memberOf` varchar(128) DEFAULT NULL,
  `memberOfOriginal` varchar(128) DEFAULT NULL,
  `members` varchar(64) DEFAULT NULL,
  `membersOriginal` int(11) DEFAULT NULL,
  `authorizationLevel` varchar(16) NOT NULL,
  `usersourceID` int(11) NOT NULL,
  PRIMARY KEY (`groupID`),
  UNIQUE KEY `fkgroupID` (`fkgroupID`),
  KEY `usersourceID` (`usersourceID`),
  KEY `groupName` (`groupName`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE IF NOT EXISTS `modules` (
  `module` varchar(64) NOT NULL,
  `db_name` varchar(32) NOT NULL,
  `active` varchar(1) NOT NULL,
  PRIMARY KEY (`module`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `userID` int(11) NOT NULL AUTO_INCREMENT,
  `fkuserID` int(11) DEFAULT NULL,
  `username` varchar(64) NOT NULL,
  `memberOf` varchar(128) DEFAULT NULL,
  `memberOfOriginal` varchar(128) DEFAULT NULL,
  `emailAddress` varchar(64) DEFAULT NULL,
  `manager` int(11) DEFAULT NULL,
  `authorizationLevel` varchar(16) NOT NULL,
  `usersourceID` int(11) NOT NULL,
  PRIMARY KEY (`userID`),
  UNIQUE KEY `fkuserID` (`fkuserID`),
  KEY `username` (`username`,`manager`,`authorizationLevel`),
  KEY `usersourceID` (`usersourceID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `usersource_type`
--

CREATE TABLE IF NOT EXISTS `usersource_type` (
  `usersourceID` int(11) NOT NULL AUTO_INCREMENT,
  `source` varchar(64) NOT NULL,
  PRIMARY KEY (`usersourceID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `groups`
--
ALTER TABLE `groups`
  ADD CONSTRAINT `groups_ibfk_1` FOREIGN KEY (`usersourceID`) REFERENCES `usersource_type` (`usersourceID`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`usersourceID`) REFERENCES `usersource_type` (`usersourceID`);
