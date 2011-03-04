-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 04, 2011 at 08:43 AM
-- Server version: 5.1.36
-- PHP Version: 5.2.11

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `avarice_nms`
--

-- --------------------------------------------------------

--
-- Table structure for table `inv__config_client`
--

CREATE TABLE IF NOT EXISTS `inv__config_client` (
  `parameter` varchar(128) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`parameter`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `inv__config_client`
--

INSERT INTO `inv__config_client` (`parameter`, `value`) VALUES
('method', 'http'),
('url', 'http://localhost/avarice_nms_svn/modules/inventory/checkin.php'),
('xml_path', '\\\\in2079.et.local\\c$\\wamp\\www\\avarice_nms_svn\\modules\\invertory\\xml');

-- --------------------------------------------------------

--
-- Table structure for table `inv__config_server`
--

CREATE TABLE IF NOT EXISTS `inv__config_server` (
  `parameter` varchar(128) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`parameter`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `inv__config_server`
--

INSERT INTO `inv__config_server` (`parameter`, `value`) VALUES
('xml_path', 'c:\\wamp\\www\\avarice_nms_svn\\modules\\invertory\\xml');

-- --------------------------------------------------------

--
-- Table structure for table `inv__config_templates`
--

CREATE TABLE IF NOT EXISTS `inv__config_templates` (
  `templateID` int(11) NOT NULL AUTO_INCREMENT,
  `hash_ID` bigint(20) NOT NULL,
  `os` varchar(64) NOT NULL,
  `release` varchar(64) NOT NULL,
  `version` varchar(64) NOT NULL,
  `template` text NOT NULL,
  PRIMARY KEY (`templateID`),
  KEY `hash_ID` (`hash_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `inv__config_templates`
--

INSERT INTO `inv__config_templates` (`templateID`, `hash_ID`, `os`, `release`, `version`, `template`) VALUES
(1, 2, 'Windows NT', '6.1', 'build 7600 ((null))', '<inventory>\r\n <asset>\r\n  <property method="wmi" root="winmgmts:{impersonationLevel=impersonate}//./root/cimv2" namespace="CIM_ComputerSystem" property="Name" name="name" />\r\n  <property method="self" name="ScanStarted" />\r\n  <property method="self" name="ScanEnded" />\r\n  <category name="harddrive" type="multiple">\r\n   <instance>\r\n    <property method="wmi" root="winmgmts:{impersonationLevel=impersonate}//./root/cimv2" namespace="Win32_LogicalDisk" property="Description" name="description" />\r\n    <property method="wmi" root="winmgmts:{impersonationLevel=impersonate}//./root/cimv2" namespace="Win32_LogicalDisk" property="DeviceID" name="deviceid" />\r\n    <property method="wmi" root="winmgmts:{impersonationLevel=impersonate}//./root/cimv2" namespace="Win32_LogicalDisk" property="Name" name="name" />\r\n    <property method="wmi" root="winmgmts:{impersonationLevel=impersonate}//./root/cimv2" namespace="Win32_LogicalDisk" property="ProviderName" name="providername" />\r\n    <property method="wmi" root="winmgmts:{impersonationLevel=impersonate}//./root/cimv2" namespace="Win32_LogicalDisk" property="Size" name="size" />\r\n    <property method="wmi" root="winmgmts:{impersonationLevel=impersonate}//./root/cimv2" namespace="Win32_LogicalDisk" property="VolumeName" name="volumename" />\r\n    <property method="wmi" root="winmgmts:{impersonationLevel=impersonate}//./root/cimv2" namespace="Win32_LogicalDisk" property="VolumeSerialNumber" name="volumeserialnumber" />\r\n   </instance>\r\n  </category>\r\n  <category name="operatingsystem" type="single">\r\n   <property method="wmi" root="winmgmts:{impersonationLevel=impersonate}//./root/cimv2" namespace="Win32_OperatingSystem" property="Caption" name="caption" type="general" />\r\n   <property method="wmi" root="winmgmts:{impersonationLevel=impersonate}//./root/cimv2" namespace="Win32_OperatingSystem" property="CountryCode" name="countrycode" type="general" />\r\n   <property method="wmi" root="winmgmts:{impersonationLevel=impersonate}//./root/cimv2" namespace="Win32_OperatingSystem" property="CSDVersion" name="csdversion" type="general" />\r\n   <property method="wmi" root="winmgmts:{impersonationLevel=impersonate}//./root/cimv2" namespace="Win32_OperatingSystem" property="CSName" name="csname" type="general" />\r\n   <property method="wmi" root="winmgmts:{impersonationLevel=impersonate}//./root/cimv2" namespace="Win32_OperatingSystem" property="CurrentTimeZone" name="currenttimezone" />\r\n   <property method="wmi" root="winmgmts:{impersonationLevel=impersonate}//./root/cimv2" namespace="Win32_OperatingSystem" property="InstallDate" name="installdate" />\r\n   <property method="wmi" root="winmgmts:{impersonationLevel=impersonate}//./root/cimv2" namespace="Win32_OperatingSystem" property="LastBootUpTime" name="lastbootuptime" />\r\n   <property method="wmi" root="winmgmts:{impersonationLevel=impersonate}//./root/cimv2" namespace="Win32_OperatingSystem" property="LocalDateTime" name="localdatetime" />\r\n   <property method="wmi" root="winmgmts:{impersonationLevel=impersonate}//./root/cimv2" namespace="Win32_OperatingSystem" property="Locale" name="locale" type="general" />\r\n   <property method="wmi" root="winmgmts:{impersonationLevel=impersonate}//./root/cimv2" namespace="Win32_OperatingSystem" property="Name" name="name" type="general" />\r\n   <property method="wmi" root="winmgmts:{impersonationLevel=impersonate}//./root/cimv2" namespace="Win32_OperatingSystem" property="Organization" name="organization" />\r\n   <property method="wmi" root="winmgmts:{impersonationLevel=impersonate}//./root/cimv2" namespace="Win32_OperatingSystem" property="OSLanguage" name="oslanguage" type="general" />\r\n   <property method="wmi" root="winmgmts:{impersonationLevel=impersonate}//./root/cimv2" namespace="Win32_OperatingSystem" property="OSProductSuite" name="osproductsuite" type="general" />\r\n   <property method="wmi" root="winmgmts:{impersonationLevel=impersonate}//./root/cimv2" namespace="Win32_OperatingSystem" property="OSType" name="ostype" type="general" />\r\n   <property method="wmi" root="winmgmts:{impersonationLevel=impersonate}//./root/cimv2" namespace="Win32_OperatingSystem" property="ProductType" name="producttype" type="general" />\r\n   <property method="wmi" root="winmgmts:{impersonationLevel=impersonate}//./root/cimv2" namespace="Win32_OperatingSystem" property="RegisteredUser" name="registereduser" />\r\n   <property method="wmi" root="winmgmts:{impersonationLevel=impersonate}//./root/cimv2" namespace="Win32_OperatingSystem" property="SerialNumber" name="serialnumber" />\r\n   <property method="wmi" root="winmgmts:{impersonationLevel=impersonate}//./root/cimv2" namespace="Win32_OperatingSystem" property="ServicePackMajorVersion" name="servicepackmajorversion" type="general" />\r\n   <property method="wmi" root="winmgmts:{impersonationLevel=impersonate}//./root/cimv2" namespace="Win32_OperatingSystem" property="ServicePackMinorVersion" name="servicepackminorversion" type="general" />\r\n   <property method="wmi" root="winmgmts:{impersonationLevel=impersonate}//./root/cimv2" namespace="Win32_OperatingSystem" property="Version" name="version" type="general" />\r\n  </category>\r\n  <category name="computer" type="single">\r\n   <property method="wmi" root="winmgmts:{impersonationLevel=impersonate}//./root/cimv2" namespace="Win32_ComputerSystem" property="Caption" name="caption" />\r\n   <property method="wmi" root="winmgmts:{impersonationLevel=impersonate}//./root/cimv2" namespace="Win32_ComputerSystem" property="CurrentTimeZone" name="currenttimezone" />\r\n   <property method="wmi" root="winmgmts:{impersonationLevel=impersonate}//./root/cimv2" namespace="Win32_ComputerSystem" property="DaylightInEffect" name="daylightineffect" />\r\n   <property method="wmi" root="winmgmts:{impersonationLevel=impersonate}//./root/cimv2" namespace="Win32_ComputerSystem" property="Description" name="description" />\r\n   <property method="wmi" root="winmgmts:{impersonationLevel=impersonate}//./root/cimv2" namespace="Win32_ComputerSystem" property="Domain" name="domain" type="normalized" />\r\n   <property method="wmi" root="winmgmts:{impersonationLevel=impersonate}//./root/cimv2" namespace="Win32_ComputerSystem" property="DomainRole" name="domainrole" type="normalized" />\r\n   <property method="wmi" root="winmgmts:{impersonationLevel=impersonate}//./root/cimv2" namespace="Win32_ComputerSystem" property="Manufacturer" name="manufacturer" type="general" />\r\n   <property method="wmi" root="winmgmts:{impersonationLevel=impersonate}//./root/cimv2" namespace="Win32_ComputerSystem" property="Model" name="model" type="general" />\r\n   <property method="wmi" root="winmgmts:{impersonationLevel=impersonate}//./root/cimv2" namespace="Win32_ComputerSystem" property="Name" name="name" />\r\n   <property method="wmi" root="winmgmts:{impersonationLevel=impersonate}//./root/cimv2" namespace="Win32_ComputerSystem" property="PrimaryOwnerContact" name="primaryownercontact" />\r\n   <property method="wmi" root="winmgmts:{impersonationLevel=impersonate}//./root/cimv2" namespace="Win32_ComputerSystem" property="PrimaryOwnerName" name="primaryownername" />\r\n   <property method="wmi" root="winmgmts:{impersonationLevel=impersonate}//./root/cimv2" namespace="Win32_OperatingSystem" property="SystemDrive" name="systemdrive" />\r\n   <property method="wmi" root="winmgmts:{impersonationLevel=impersonate}//./root/cimv2" namespace="Win32_ComputerSystem" property="TotalPhysicalMemory" name="totalphysicalmemory" />\r\n   <property method="wmi" root="winmgmts:{impersonationLevel=impersonate}//./root/cimv2" namespace="Win32_OperatingSystem" property="TotalVirtualMemorySize" name="totalvirtualmemorysize" />\r\n   <property method="wmi" root="winmgmts:{impersonationLevel=impersonate}//./root/cimv2" namespace="Win32_OperatingSystem" property="TotalVisibleMemorySize" name="totalvisiblememorysize" />\r\n   <property method="wmi" root="winmgmts:{impersonationLevel=impersonate}//./root/cimv2" namespace="Win32_ComputerSystem" property="UserName" name="username" />\r\n  </category>\r\n </asset>\r\n</inventory>');

-- --------------------------------------------------------

--
-- Table structure for table `inv__hash`
--

CREATE TABLE IF NOT EXISTS `inv__hash` (
  `hash_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `hash` varchar(255) NOT NULL,
  PRIMARY KEY (`hash_ID`),
  KEY `hash` (`hash`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `inv__hash`
--

INSERT INTO `inv__hash` (`hash_ID`, `hash`) VALUES
(1, 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855'),
(2, 'cba0b6fb99c0783ebd3005316c787748d4244ce1a1d35f7df4d5c552115d03a6');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `inv__config_templates`
--
ALTER TABLE `inv__config_templates`
  ADD CONSTRAINT `inv__config_templates_ibfk_1` FOREIGN KEY (`hash_ID`) REFERENCES `inv__hash` (`hash_ID`);

