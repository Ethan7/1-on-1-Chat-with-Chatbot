-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 01, 2019 at 11:05 PM
-- Server version: 5.7.26
-- PHP Version: 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `chatdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `chats`
--

DROP TABLE IF EXISTS `chats`;
CREATE TABLE IF NOT EXISTS `chats` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `startName` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
  `matchName` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
  `startDate` datetime DEFAULT NULL,
  `msgCount` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `chats`
--

INSERT INTO `chats` (`ID`, `startName`, `matchName`, `startDate`, `msgCount`) VALUES
(29, 'Ethan', 'Ethan', '2019-08-29 21:27:39', 0),
(30, 'Ethan', 'Ethan', '2019-08-29 21:28:57', 0),
(31, 'Ethan', NULL, '2019-08-29 21:41:38', 0),
(32, 'guygyg;', NULL, '2019-08-29 21:47:29', 0),
(33, 'hvuvcg', 'jinu', '2019-08-30 00:38:03', 8),
(34, 'Ethan', NULL, '2019-08-30 01:31:28', 5),
(35, 'Ethan', NULL, '2019-09-01 03:29:10', 0),
(36, 'erger', NULL, '2019-09-01 05:33:30', 1);

-- --------------------------------------------------------

--
-- Table structure for table `maxlen`
--

DROP TABLE IF EXISTS `maxlen`;
CREATE TABLE IF NOT EXISTS `maxlen` (
  `length` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `maxlen`
--

INSERT INTO `maxlen` (`length`) VALUES
(45);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `ChatID` int(11) DEFAULT NULL,
  `senderName` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mDate` datetime DEFAULT NULL,
  `message` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  KEY `FOREIGN KEY3` (`ChatID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`ChatID`, `senderName`, `mDate`, `message`) VALUES
(33, 'hvuvcg', '2019-08-30 00:53:00', 'Checking here that this works now'),
(33, 'hvuvcg', '2019-08-30 00:58:00', 'Yes it works now.'),
(33, 'hvuvcg', '2019-08-30 00:59:00', 'Good thing too.'),
(33, 'jinu', '2019-08-30 01:00:00', 'Did you come here to test AI?'),
(33, 'hvuvcg', '2019-08-30 01:01:00', 'Not really, I just like chatting with people.'),
(33, 'hvuvcg', '2019-08-30 01:01:00', 'Glad to talk to you, but I have to get going.'),
(33, 'jinu', '2019-08-30 01:02:00', 'Oh ok. See you!'),
(33, 'hvuvcg', '2019-08-30 01:02:00', 'Later!'),
(34, 'Ethan', '2019-08-30 01:34:00', 'How does this sound?'),
(34, 'Ethan', '2019-08-30 01:34:00', 'Is there nothing you want to say?'),
(34, 'Ethan', '2019-08-30 01:34:00', 'I hope you can respond to this'),
(34, 'Ethan', '2019-08-30 01:34:00', 'What does AI talk like?'),
(35, 'Ethan', '2019-09-01 03:29:00', 'Testing for testing sake'),
(36, 'erger', '2019-09-01 05:33:00', 'check check testing, fixing all forms');

-- --------------------------------------------------------

--
-- Table structure for table `waiting`
--

DROP TABLE IF EXISTS `waiting`;
CREATE TABLE IF NOT EXISTS `waiting` (
  `ChatID` int(11) DEFAULT NULL,
  `filename` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  KEY `FOREIGN KEY2` (`ChatID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `working`
--

DROP TABLE IF EXISTS `working`;
CREATE TABLE IF NOT EXISTS `working` (
  `ChatID` int(11) NOT NULL,
  `Memory` varchar(255) NOT NULL,
  KEY `FOREIGN KEY` (`ChatID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `FOREIGN KEY3` FOREIGN KEY (`ChatID`) REFERENCES `chats` (`ID`);

--
-- Constraints for table `waiting`
--
ALTER TABLE `waiting`
  ADD CONSTRAINT `FOREIGN KEY2` FOREIGN KEY (`ChatID`) REFERENCES `chats` (`ID`);

--
-- Constraints for table `working`
--
ALTER TABLE `working`
  ADD CONSTRAINT `FOREIGN KEY` FOREIGN KEY (`ChatID`) REFERENCES `chats` (`ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
