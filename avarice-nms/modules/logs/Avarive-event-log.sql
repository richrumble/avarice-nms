-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 13, 2012 at 04:00 AM
-- Server version: 5.5.16
-- PHP Version: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `avarice_nms`
--

-- --------------------------------------------------------

--
-- Table structure for table `log__asset_data`
--

CREATE TABLE IF NOT EXISTS `log__asset_data` (
  `assetID` bigint(11) NOT NULL AUTO_INCREMENT,
  `adminPasswordStatus` tinyint(4) NOT NULL,
  `automaticManagedPagefile` bit(1) NOT NULL,
  `automaticResetBootOption` bit(1) NOT NULL,
  `automaticResetCapability` bit(1) NOT NULL,
  `bootOptionOnLimit` varchar(64) DEFAULT NULL,
  `bootOptionOnWatchDog` varchar(64) DEFAULT NULL,
  `bootROMSupported` bit(1) NOT NULL,
  `bootupState` varchar(64) DEFAULT NULL,
  `caption` varchar(256) DEFAULT NULL,
  `chassisBootupState` varchar(64) DEFAULT NULL,
  `creationClassName` varchar(64) DEFAULT NULL,
  `currentTimeZone` varchar(64) DEFAULT NULL,
  `daylightInEffect` bit(1) NOT NULL,
  `description` text,
  `DNSHostName` varchar(64) DEFAULT NULL,
  `domain` varchar(64) DEFAULT NULL,
  `domainRole` varchar(64) DEFAULT NULL,
  `enableDaylightSavingsTime` bit(1) NOT NULL,
  `frontPanelResetStatus` varchar(64) DEFAULT NULL,
  `infraredSupported` bit(1) NOT NULL,
  `initialLoadInfo` varchar(64) DEFAULT NULL,
  `keyboardPasswordStatus` varchar(64) DEFAULT NULL,
  `lastLoadInfo` varchar(64) DEFAULT NULL,
  `manufacturer` varchar(64) DEFAULT NULL,
  `model` varchar(64) DEFAULT NULL,
  `name` varchar(64) DEFAULT NULL,
  `nameFormat` varchar(64) DEFAULT NULL,
  `networkServerModeEnabled` bit(1) NOT NULL,
  `numberOfLogicalProcessors` tinyint(4) NOT NULL,
  `numberOfProcessors` tinyint(4) NOT NULL,
  `OEMLogoBitmap` varchar(64) DEFAULT NULL,
  `OEMStringArray` varchar(64) DEFAULT NULL,
  `partOfDomain` bit(1) NOT NULL,
  `pauseAfterReset` varchar(64) DEFAULT NULL,
  `PCSystemType` varchar(64) DEFAULT NULL,
  `powerManagementCapabilities` varchar(64) DEFAULT NULL,
  `powerManagementSupported` varchar(64) DEFAULT NULL,
  `powerOnPasswordStatus` varchar(64) DEFAULT NULL,
  `powerState` varchar(64) DEFAULT NULL,
  `powerSupplyState` varchar(64) DEFAULT NULL,
  `primaryOwnerContact` varchar(64) DEFAULT NULL,
  `primaryOwnerName` varchar(64) DEFAULT NULL,
  `resetCapability` varchar(64) DEFAULT NULL,
  `resetCount` varchar(64) DEFAULT NULL,
  `resetLimit` varchar(64) DEFAULT NULL,
  `roles` varchar(64) DEFAULT NULL,
  `status` varchar(64) DEFAULT NULL,
  `supportContactDescription` varchar(64) DEFAULT NULL,
  `systemStartupDelay` varchar(64) DEFAULT NULL,
  `systemStartupOptions` varchar(64) DEFAULT NULL,
  `systemStartupSetting` varchar(64) DEFAULT NULL,
  `systemType` varchar(64) DEFAULT NULL,
  `thermalState` varchar(64) DEFAULT NULL,
  `totalPhysicalMemory` varchar(64) DEFAULT NULL,
  `userName` varchar(64) DEFAULT NULL,
  `wakeUpType` varchar(64) DEFAULT NULL,
  `workgroup` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`assetID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `log__event_categories`
--

CREATE TABLE IF NOT EXISTS `log__event_categories` (
  `categoryID` bigint(20) NOT NULL AUTO_INCREMENT,
  `category` varchar(255) NOT NULL,
  `categoryName` varchar(255) NOT NULL,
  PRIMARY KEY (`categoryID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `log__event_data`
--

CREATE TABLE IF NOT EXISTS `log__event_data` (
  `eventDataID` bigint(20) NOT NULL AUTO_INCREMENT,
  `templateID` bigint(20) NOT NULL,
  `assetID` bigint(20) NOT NULL,
  `insertionStrings` text NOT NULL,
  `messageData` text NOT NULL,
  `recordNumber` bigint(20) NOT NULL,
  `timeGenerated` datetime NOT NULL,
  `timeWritten` datetime NOT NULL,
  `timeZoneID` bigint(11) NOT NULL,
  `MD5Hash` text NOT NULL,
  `Sha1Hash` text NOT NULL,
  `trueTime` varchar(64) NOT NULL,
  `timeAbberation` bit(1) NOT NULL,
  PRIMARY KEY (`eventDataID`),
  KEY `templateID` (`templateID`),
  KEY `assetID` (`assetID`),
  KEY `timeZoneID` (`timeZoneID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `log__event_templates`
--

CREATE TABLE IF NOT EXISTS `log__event_templates` (
  `templateID` bigint(20) NOT NULL AUTO_INCREMENT,
  `jenkinsHash1` bigint(20) NOT NULL,
  `jenkinsHash2` bigint(20) NOT NULL,
  `categoryID` bigint(20) NOT NULL,
  `eventCode` varchar(64) NOT NULL,
  `eventIdentifier` varchar(64) NOT NULL,
  `eventType` varchar(32) NOT NULL,
  `logFileID` int(11) NOT NULL,
  `message` text NOT NULL,
  `sourceID` int(11) NOT NULL,
  `typeID` int(11) NOT NULL,
  PRIMARY KEY (`templateID`),
  KEY `categoryID` (`categoryID`),
  KEY `logFileID` (`logFileID`),
  KEY `sourceID` (`sourceID`),
  KEY `typeID` (`typeID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `log__local_groups`
--

CREATE TABLE IF NOT EXISTS `log__local_groups` (
  `groupID` bigint(20) NOT NULL AUTO_INCREMENT,
  `assetID` int(11) NOT NULL,
  `caption` varchar(256) NOT NULL,
  `description` text,
  `domain` varchar(64) DEFAULT NULL,
  `localAccount` bit(1) NOT NULL,
  `name` varchar(64) NOT NULL,
  `SID` varchar(256) NOT NULL,
  `SIDType` tinyint(4) NOT NULL,
  `status` varchar(64) NOT NULL,
  PRIMARY KEY (`groupID`),
  KEY `assetID` (`assetID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `log__local_users`
--

CREATE TABLE IF NOT EXISTS `log__local_users` (
  `userID` bigint(20) NOT NULL AUTO_INCREMENT,
  `assetID` int(11) NOT NULL,
  `accountType` varchar(64) NOT NULL,
  `caption` varchar(256) NOT NULL,
  `description` text,
  `disabled` bit(1) NOT NULL,
  `domain` varchar(64) DEFAULT NULL,
  `fullName` varchar(128) DEFAULT NULL,
  `localAccount` bit(1) NOT NULL,
  `lockout` bit(1) NOT NULL,
  `name` varchar(128) DEFAULT NULL,
  `passwordChangeable` bit(1) NOT NULL,
  `passwordExpires` bit(1) NOT NULL,
  `passwordRequired` bit(1) NOT NULL,
  `SID` varchar(256) NOT NULL,
  `SIDType` tinyint(4) NOT NULL,
  `status` varchar(64) NOT NULL,
  PRIMARY KEY (`userID`),
  KEY `assetID` (`assetID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `log__logfiles`
--

CREATE TABLE IF NOT EXISTS `log__logfiles` (
  `logFileID` int(11) NOT NULL AUTO_INCREMENT,
  `logFile` varchar(255) NOT NULL,
  PRIMARY KEY (`logFileID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `log__sources`
--

CREATE TABLE IF NOT EXISTS `log__sources` (
  `sourceID` int(11) NOT NULL AUTO_INCREMENT,
  `source` varchar(255) NOT NULL,
  PRIMARY KEY (`sourceID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `log__time_zones`
--

CREATE TABLE IF NOT EXISTS `log__time_zones` (
  `timeZoneID` bigint(11) NOT NULL AUTO_INCREMENT,
  `bias` smallint(6) NOT NULL,
  `caption` varchar(64) NOT NULL,
  `daylightBias` smallint(6) NOT NULL,
  `daylightDay` smallint(6) NOT NULL,
  `daylightDayOfWeek` tinyint(4) NOT NULL,
  `daylightHour` tinyint(4) NOT NULL,
  `daylightMinute` tinyint(4) NOT NULL,
  `daylightMonth` tinyint(4) NOT NULL,
  `daylightName` varchar(64) NOT NULL,
  `daylightSecond` tinyint(4) NOT NULL,
  `daylightYear` smallint(6) NOT NULL,
  `description` text NOT NULL,
  `settingID` varchar(128) NOT NULL,
  `standardBias` smallint(6) NOT NULL,
  `standardDay` smallint(6) NOT NULL,
  `standardDayOfWeek` tinyint(4) NOT NULL,
  `standardHour` tinyint(4) NOT NULL,
  `standardMinute` tinyint(4) NOT NULL,
  `standardMonth` tinyint(4) NOT NULL,
  `standardName` varchar(128) NOT NULL,
  `standardSecond` tinyint(4) NOT NULL,
  `standardYear` smallint(6) NOT NULL,
  PRIMARY KEY (`timeZoneID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `log__types`
--

CREATE TABLE IF NOT EXISTS `log__types` (
  `typeID` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  PRIMARY KEY (`typeID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `log__event_data`
--
ALTER TABLE `log__event_data`
  ADD CONSTRAINT `log__event_data_ibfk_2` FOREIGN KEY (`templateID`) REFERENCES `log__event_templates` (`templateID`),
  ADD CONSTRAINT `log__event_data_ibfk_3` FOREIGN KEY (`assetID`) REFERENCES `log__asset_data` (`assetID`),
  ADD CONSTRAINT `log__event_data_ibfk_4` FOREIGN KEY (`timeZoneID`) REFERENCES `log__time_zones` (`timeZoneID`);

--
-- Constraints for table `log__event_templates`
--
ALTER TABLE `log__event_templates`
  ADD CONSTRAINT `log__event_templates_ibfk_2` FOREIGN KEY (`categoryID`) REFERENCES `log__event_categories` (`categoryID`),
  ADD CONSTRAINT `log__event_templates_ibfk_3` FOREIGN KEY (`logFileID`) REFERENCES `log__logfiles` (`logFileID`),
  ADD CONSTRAINT `log__event_templates_ibfk_4` FOREIGN KEY (`sourceID`) REFERENCES `log__sources` (`sourceID`),
  ADD CONSTRAINT `log__event_templates_ibfk_5` FOREIGN KEY (`typeID`) REFERENCES `log__types` (`typeID`);

