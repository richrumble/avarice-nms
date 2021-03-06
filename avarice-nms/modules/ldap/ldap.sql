-- Create database ava_ldap and insert the two tables below.  The rest will populate automatically.
--
-- Database: `ava_ldap`
--

CREATE TABLE IF NOT EXISTS `__archive` (
  `date_changed` datetime NOT NULL,
  `orig_date` datetime NOT NULL,
  `ObjectClass` varchar(32) NOT NULL,
  `DN` varchar(32) NOT NULL
) ENGINE=ARCHIVE DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `__module_config` (
  `parameter` varchar(64) NOT NULL,
  `value` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`parameter`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `__module_config` (`parameter`, `value`) VALUES
('last_updated', NULL),
('seeded', NULL);
