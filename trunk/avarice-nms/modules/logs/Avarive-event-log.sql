-- phpMyAdmin SQL Dump
-- version 3.4.10.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 02, 2012 at 02:21 AM
-- Server version: 5.5.20
-- PHP Version: 5.3.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT=0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `avarice`
--

-- --------------------------------------------------------

--
-- Table structure for table `inv.asset`
--

CREATE TABLE IF NOT EXISTS `inv.asset` (
  `assetID` bigint(11) aNOT NULL AUTO_INCREMENT,
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
-- Table structure for table `inv.localgroup`
--

CREATE TABLE IF NOT EXISTS `inv.localgroup` (
  `groupID` bigint(20) NOT NULL AUTO_INCREMENT,
  `assetID` bigint(11) NOT NULL,
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
-- Table structure for table `inv.localuser`
--

CREATE TABLE IF NOT EXISTS `inv.localuser` (
  `userID` bigint(20) NOT NULL AUTO_INCREMENT,
  `assetID` bigint(11) NOT NULL,
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
-- Table structure for table `inv.timezone`
--

CREATE TABLE IF NOT EXISTS `inv.timezone` (
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
  PRIMARY KEY (`timeZoneID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `log.category`
--

CREATE TABLE IF NOT EXISTS `log.category` (
  `categoryID` bigint(20) NOT NULL AUTO_INCREMENT,
  `category` varchar(255) NOT NULL,
  `categoryName` varchar(255) NOT NULL,
  PRIMARY KEY (`categoryID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `log.data`
--

CREATE TABLE IF NOT EXISTS `log.data` (
  `eventDataID` bigint(20) NOT NULL AUTO_INCREMENT,
  `templateID` bigint(20) NOT NULL,
  `assetID` bigint(20) NOT NULL,
  `insertionStrings` text NOT NULL,
  `recordNumber` bigint(20) NOT NULL,
  `timeGenerated` datetime NOT NULL,
  `timeWritten` datetime NOT NULL,
  `MD5Hash` varchar(32) NOT NULL,
  `Sha1Hash` varchar(40) NOT NULL,
  PRIMARY KEY (`eventDataID`),
  KEY `templateID` (`templateID`),
  KEY `assetID` (`assetID`),
  KEY `MD5Hash` (`MD5Hash`),
  KEY `Sha1Hash` (`Sha1Hash`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `log.logfile`
--

CREATE TABLE IF NOT EXISTS `log.logfile` (
  `logFileID` int(11) NOT NULL AUTO_INCREMENT,
  `logFile` varchar(255) NOT NULL,
  PRIMARY KEY (`logFileID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `log.msgfile`
--

CREATE TABLE IF NOT EXISTS `log.msgfile` (
  `msgFileID` bigint(20) NOT NULL AUTO_INCREMENT,
  `company` varchar(128) NOT NULL,
  `fileVersion` varchar(24) NOT NULL,
  `internalName` varchar(128) NOT NULL,
  `language` varchar(64) NOT NULL,
  `originalFileName` varchar(128) NOT NULL,
  `productName` text NOT NULL,
  `productVersion` varchar(24) NOT NULL,
  `description` text,
  `copyright` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`msgFileID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `log.source`
--

CREATE TABLE IF NOT EXISTS `log.source` (
  `sourceID` int(11) NOT NULL AUTO_INCREMENT,
  `source` varchar(255) NOT NULL,
  PRIMARY KEY (`sourceID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `log.template`
--

CREATE TABLE IF NOT EXISTS `log.template` (
  `templateID` bigint(20) NOT NULL AUTO_INCREMENT,
  `jenkinsHash1` varchar(8) NOT NULL,
  `jenkinsHash2` varchar(8) NOT NULL,
  `categoryID` bigint(20) NOT NULL,
  `eventCode` varchar(64) NOT NULL,
  `eventIdentifier` varchar(64) NOT NULL,
  `eventType` varchar(32) NOT NULL,
  `logFileID` int(11) NOT NULL,
  `message` text NOT NULL,
  `sourceID` int(11) NOT NULL,
  `typeID` int(11) NOT NULL,
  `msgFileID` bigint(20) NOT NULL,
  PRIMARY KEY (`templateID`),
  KEY `categoryID` (`categoryID`),
  KEY `logFileID` (`logFileID`),
  KEY `sourceID` (`sourceID`),
  KEY `typeID` (`typeID`),
  KEY `msgFileID` (`msgFileID`),
  KEY `jenkinsHash1` (`jenkinsHash1`),
  KEY `jenkinsHash2` (`jenkinsHash2`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `log.type`
--

CREATE TABLE IF NOT EXISTS `log.type` (
  `typeID` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  PRIMARY KEY (`typeID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `inv.localgroup`
--
ALTER TABLE `inv.localgroup`
  ADD CONSTRAINT `inv@002elocalgroup_ibfk_1` FOREIGN KEY (`assetID`) REFERENCES `inv.asset` (`assetID`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `inv.localuser`
--
ALTER TABLE `inv.localuser`
  ADD CONSTRAINT `inv@002elocaluser_ibfk_1` FOREIGN KEY (`assetID`) REFERENCES `inv.asset` (`assetID`) ON DELETE CASCADE;

--
-- Constraints for table `log.data`
--
ALTER TABLE `log.data`
  ADD CONSTRAINT `log.data_ibfk_2` FOREIGN KEY (`templateID`) REFERENCES `log.template` (`templateID`),
  ADD CONSTRAINT `log.data_ibfk_3` FOREIGN KEY (`assetID`) REFERENCES `inv.asset` (`assetID`);

--
-- Constraints for table `log.template`
--
ALTER TABLE `log.template`
  ADD CONSTRAINT `log@002etemplate_ibfk_1` FOREIGN KEY (`msgFileID`) REFERENCES `log.msgfile` (`msgFileID`),
  ADD CONSTRAINT `log.template_ibfk_2` FOREIGN KEY (`categoryID`) REFERENCES `log.category` (`categoryID`),
  ADD CONSTRAINT `log.template_ibfk_3` FOREIGN KEY (`logFileID`) REFERENCES `log.logfile` (`logFileID`),
  ADD CONSTRAINT `log.template_ibfk_4` FOREIGN KEY (`sourceID`) REFERENCES `log.source` (`sourceID`),
  ADD CONSTRAINT `log.template_ibfk_5` FOREIGN KEY (`typeID`) REFERENCES `log.type` (`typeID`);
COMMIT;
