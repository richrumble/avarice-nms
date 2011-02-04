-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 03, 2011 at 10:01 PM
-- Server version: 5.1.36
-- PHP Version: 5.2.11

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `avarice_nms`
--

-- --------------------------------------------------------

--
-- Table structure for table `inv__config_misc`
--

CREATE TABLE IF NOT EXISTS `inv__config_misc` (
  `parameter` varchar(128) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`parameter`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `inv__config_misc`
--

INSERT INTO `inv__config_misc` (`parameter`, `value`) VALUES
('xml_path', 'C:\\wamp\\www\\avarice_nms_svn\\modules\\invertory\\xml');

-- --------------------------------------------------------

--
-- Table structure for table `inv__config_templates`
--

CREATE TABLE IF NOT EXISTS `inv__config_templates` (
  `templateID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `template` text NOT NULL,
  PRIMARY KEY (`templateID`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `inv__config_templates`
--

INSERT INTO `inv__config_templates` (`templateID`, `name`, `template`) VALUES
(1, 'testing_Win7', '<inventory>\r\n <asset>\r\n  <property method="wmi" namespace="CIM_ComputerSystem" property="Name" name="name" />\r\n  <property method="self" name="ScanStarted" />\r\n  <category name="harddrive" type="multiple" />\r\n  <property method="self" name="ScanEnded" />\r\n   <instance>\r\n    <property method="wmi" namespace="Win32_LogicalDisk" property="Description" name="description" />\r\n    <property method="wmi" namespace="Win32_LogicalDisk" property="DeviceID" name="deviceid" />\r\n    <property method="wmi" namespace="Win32_LogicalDisk" property="Name" name="name" />\r\n    <property method="wmi" namespace="Win32_LogicalDisk" property="ProviderName" name="providername" />\r\n    <property method="wmi" namespace="Win32_LogicalDisk" property="Size" name="size" />\r\n    <property method="wmi" namespace="Win32_LogicalDisk" property="VolumeName" name="volumename" />\r\n    <property method="wmi" namespace="Win32_LogicalDisk" property="VolumeSerialNumber" name="volumeserialnumber" />\r\n   </instance>\r\n  </category>\r\n  <category name="operatingsystem" type="single">\r\n   <property method="wmi" namespace="Win32_OperatingSystem" property="Caption" name="caption" type="general" />\r\n   <property method="wmi" namespace="Win32_OperatingSystem" property="CountryCode" name="countrycode" type="general" />\r\n   <property method="wmi" namespace="Win32_OperatingSystem" property="CSDVersion" name="csdversion" type="general" />\r\n   <property method="wmi" namespace="Win32_OperatingSystem" property="CSName" name="csname" type="general" />\r\n   <property method="wmi" namespace="Win32_OperatingSystem" property="CurrentTimeZone" name="currenttimezone" />\r\n   <property method="wmi" namespace="Win32_OperatingSystem" property="InstallDate" name="installdate" />\r\n   <property method="wmi" namespace="Win32_OperatingSystem" property="LastBootUpTime" name="lastbootuptime" />\r\n   <property method="wmi" namespace="Win32_OperatingSystem" property="LocalDateTime" name="localdatetime" />\r\n   <property method="wmi" namespace="Win32_OperatingSystem" property="Locale" name="locale" type="general" />\r\n   <property method="wmi" namespace="Win32_OperatingSystem" property="Name" name="name" type="general" />\r\n   <property method="wmi" namespace="Win32_OperatingSystem" property="Organization" name="organization" />\r\n   <property method="wmi" namespace="Win32_OperatingSystem" property="OSLanguage" name="oslanguage" type="general" />\r\n   <property method="wmi" namespace="Win32_OperatingSystem" property="OSProductSuite" name="osproductsuite" type="general" />\r\n   <property method="wmi" namespace="Win32_OperatingSystem" property="OSType" name="ostype" type="general" />\r\n   <property method="wmi" namespace="Win32_OperatingSystem" property="ProductType" name="producttype" type="general" />\r\n   <property method="wmi" namespace="Win32_OperatingSystem" property="RegisteredUser" name="registereduser" />\r\n   <property method="wmi" namespace="Win32_OperatingSystem" property="SerialNumber" name="serialnumber" />\r\n   <property method="wmi" namespace="Win32_OperatingSystem" property="ServicePackMajorVersion" name="servicepackmajorversion" type="general" />\r\n   <property method="wmi" namespace="Win32_OperatingSystem" property="ServicePackMinorVersion" name="servicepackminorversion" type="general" />\r\n   <property method="wmi" namespace="Win32_OperatingSystem" property="Version" name="version" type="general" />\r\n  </category>\r\n  <category name="computer" type="single">\r\n   <property method="wmi" namespace="Win32_ComputerSystem" property="Caption" name="caption" />\r\n   <property method="wmi" namespace="Win32_ComputerSystem" property="CurrentTimeZone" name="currenttimezone" />\r\n   <property method="wmi" namespace="Win32_ComputerSystem" property="DaylightInEffect" name="daylightineffect" />\r\n   <property method="wmi" namespace="Win32_ComputerSystem" property="Description" name="description" />\r\n   <property method="wmi" namespace="Win32_ComputerSystem" property="Domain" name="domain" type="normalized" />\r\n   <property method="wmi" namespace="Win32_ComputerSystem" property="DomainRole" name="domainrole" type="normalized" />\r\n   <property method="wmi" namespace="Win32_ComputerSystem" property="Manufacturer" name="manufacturer" type="general" />\r\n   <property method="wmi" namespace="Win32_ComputerSystem" property="Model" name="model" type="general" />\r\n   <property method="wmi" namespace="Win32_ComputerSystem" property="Name" name="name" />\r\n   <property method="wmi" namespace="Win32_ComputerSystem" property="PrimaryOwnerContact" name="primaryownercontact" />\r\n   <property method="wmi" namespace="Win32_ComputerSystem" property="PrimaryOwnerName" name=primaryownername" />\r\n   <property method="wmi" namespace="Win32_OperatingSystem" property="SystemDrive" name="systemdrive" />\r\n   <property method="wmi" namespace="Win32_ComputerSystem" property="TotalPhysicalMemory" name="totalphysicalmemory" />\r\n   <property method="wmi" namespace="Win32_OperatingSystem" property="TotalVirtualMemorySize" name="totalvirtualmemorysize" />\r\n   <property method="wmi" namespace="Win32_OperatingSystem" property="TotalVisibleMemorySize" name="totalvisiblememorysize" />\r\n   <property method="wmi" namespace="Win32_ComputerSystem" property="UserName" name="username" />\r\n  </category>\r\n </Asset>\r\n</Inventory>');

-- --------------------------------------------------------

--
-- Table structure for table `inv__hash`
--

CREATE TABLE IF NOT EXISTS `inv__hash` (
  `hash_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `hash` varchar(255) NOT NULL,
  PRIMARY KEY (`hash_ID`),
  KEY `hash` (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `inv__hash`
--

