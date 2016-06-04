-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jun 04, 2016 at 05:54 PM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `crawler`
--

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE IF NOT EXISTS `files` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'A unique id for the entry',
  `link_id` bigint(20) NOT NULL COMMENT 'Unique id of the URL',
  `time_taken` float NOT NULL COMMENT 'The time taken to find the number of img tags in this url',
  `filesize` decimal(10,0) NOT NULL COMMENT 'Total file size',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Stores the count of images in a link' AUTO_INCREMENT=23 ;

--
-- Dumping data for table `files`
--

INSERT INTO `files` (`id`, `link_id`, `time_taken`, `filesize`) VALUES
(1, 14, 2.00219, '32825'),
(2, 15, 1.23855, '10232'),
(3, 16, 2.04624, '11177'),
(4, 17, 2.92592, '35497'),
(5, 18, 2.05174, '30746'),
(6, 19, 2.78208, '31308'),
(7, 20, 5.93612, '28331'),
(8, 21, 1.22513, '8122'),
(9, 22, 2.26562, '37814'),
(10, 23, 0.983645, '4392'),
(11, 24, 2.83564, '35042'),
(12, 25, 0.983125, '4703'),
(13, 26, 2.80377, '33451'),
(14, 27, 0.985142, '4452'),
(15, 28, 2.28302, '33109'),
(16, 29, 1.20215, '3439'),
(17, 30, 5.25126, '58475'),
(18, 31, 0.733441, '2684'),
(19, 32, 2.9359, '55276'),
(20, 33, 1.7429, '25597'),
(21, 34, 2.40907, '45315'),
(22, 35, 0.836352, '3646');

-- --------------------------------------------------------

--
-- Table structure for table `links`
--

CREATE TABLE IF NOT EXISTS `links` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'A unique id for the entry',
  `url` varchar(255) COLLATE utf8_bin NOT NULL COMMENT 'The unique URL obtained',
  `created` date NOT NULL COMMENT 'The time at whioch this entry was made',
  `filetype` varchar(20) COLLATE utf8_bin DEFAULT NULL COMMENT 'file extension of the link ',
  `issize` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table to store urls' AUTO_INCREMENT=36 ;

--
-- Dumping data for table `links`
--

INSERT INTO `links` (`id`, `url`, `created`, `filetype`, `issize`) VALUES
(1, 'http://litebreeze.com/david', '2016-06-04', NULL, 0),
(2, 'http://litebreeze.com/portfolio/150-easy-recruit', '2016-06-04', NULL, 0),
(3, 'http://litebreeze.com/portfolio/164-saas-application', '2016-06-04', NULL, 0),
(4, 'http://litebreeze.com/portfolio/126-fire-and-safety-consultancy', '2016-06-04', NULL, 0),
(5, 'http://litebreeze.com/portfolio/128-safepac-e-learning', '2016-06-04', NULL, 0),
(6, 'http://litebreeze.com/portfolio/127-delafee-ecommerce-edible-gold', '2016-06-04', NULL, 0),
(7, 'http://litebreeze.com/portfolio/141-kids-friendly-accomodations-directory-and-booking-facility', '2016-06-04', NULL, 0),
(8, 'http://litebreeze.com/portfolio/142-cms-and-cryptocurrency-transaction', '2016-06-04', NULL, 0),
(9, 'http://litebreeze.com/portfolio/134-crm-booking-system-for-event-planner', '2016-06-04', NULL, 0),
(10, 'http://litebreeze.com/portfolio/162-hatecgroupcom', '2016-06-04', NULL, 0),
(11, 'http://litebreeze.com/portfolio/163-bagerikassense', '2016-06-04', NULL, 0),
(12, 'http://litebreeze.com/portfolio', '2016-06-04', NULL, 0),
(13, 'http://litebreeze.com/privacy', '2016-06-04', NULL, 0),
(14, 'http://litebreeze.com/css/custom.css', '2016-06-04', 'css', 1),
(15, 'http://litebreeze.com/js/custom.js', '2016-06-04', 'js', 1),
(16, 'http://litebreeze.com/images/logo1.png', '2016-06-04', 'image', 1),
(17, 'http://litebreeze.com/images/team-photo-compressed.jpg', '2016-06-04', 'image', 1),
(18, 'http://litebreeze.com/images/easy_recruit-logo-large.jpg', '2016-06-04', 'image', 1),
(19, 'http://litebreeze.com/images/torbjorn-skjelde.jpg', '2016-06-04', 'image', 1),
(20, 'http://litebreeze.com/images/online-aspire-logo.jpg', '2016-06-04', 'image', 1),
(21, 'http://litebreeze.com/images/online-aspire-client-dp.jpg', '2016-06-04', 'image', 1),
(22, 'http://litebreeze.com/images/case_study_aptum-logo.jpg', '2016-06-04', 'image', 1),
(23, 'http://litebreeze.com/images/img3.jpg', '2016-06-04', 'image', 1),
(24, 'http://litebreeze.com/images/case_study_safepac.jpg', '2016-06-04', 'image', 1),
(25, 'http://litebreeze.com/images/img2.jpg', '2016-06-04', 'image', 1),
(26, 'http://litebreeze.com/images/case_study_delafee.jpg', '2016-06-04', 'image', 1),
(27, 'http://litebreeze.com/images/img1.jpg', '2016-06-04', 'image', 1),
(28, 'http://litebreeze.com/images/okidokidz_large.jpg', '2016-06-04', 'image', 1),
(29, 'http://litebreeze.com/images/okido.jpg', '2016-06-04', 'image', 1),
(30, 'http://litebreeze.com/images/case_study_lxc_coin.jpg', '2016-06-04', 'image', 1),
(31, 'http://litebreeze.com/images/henrick.jpg', '2016-06-04', 'image', 1),
(32, 'http://litebreeze.com/images/case_study_funcruises.jpg', '2016-06-04', 'image', 1),
(33, 'http://litebreeze.com/images/hatecgmbh-de_94180.jpg', '2016-06-04', 'image', 1),
(34, 'http://litebreeze.com/images/bagerikassen-logo-small.jpg', '2016-06-04', 'image', 1),
(35, 'http://litebreeze.com/images/baggerikassen-profile.jpg', '2016-06-04', 'image', 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
