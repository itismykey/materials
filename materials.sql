-- phpMyAdmin SQL Dump
-- version 3.4.9
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2025 年 07 月 22 日 16:30
-- 服务器版本: 5.5.20
-- PHP 版本: 5.3.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `materials_db`
--

-- --------------------------------------------------------

--
-- 表的结构 `materials`
--

CREATE TABLE IF NOT EXISTS `materials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ReceiptDate` date NOT NULL,
  `Type` varchar(255) NOT NULL,
  `DeviceName` varchar(255) NOT NULL,
  `Specification` varchar(255) NOT NULL,
  `PartNumber` varchar(255) NOT NULL,
  `Barcode` varchar(255) NOT NULL,
  `Status` varchar(255) NOT NULL,
  `Location` varchar(255) NOT NULL,
  `Owner` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- 转存表中的数据 `materials`
--

INSERT INTO `materials` (`id`, `ReceiptDate`, `Type`, `DeviceName`, `Specification`, `PartNumber`, `Barcode`, `Status`, `Location`, `Owner`) VALUES
(3, '0000-00-00', 'GPU', 'Navi48', 'G29502 Board A0 XTX AiB Typical/Corner Samsung', '102-G29502-00', 'NA', 'On rock', 'C1-3', 'Roger Chang'),
(4, '0000-00-00', 'GPU', 'Navi48', 'G29502 Board A0 XTX AiB Typical/Corner Samsung', '102-G29502-00', 'NA', 'AI deployment', 'C4-3', 'James Sung'),
(6, '0000-00-00', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
