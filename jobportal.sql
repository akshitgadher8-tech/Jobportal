-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 01, 2026 at 03:47 PM
-- Server version: 5.6.12-log
-- PHP Version: 5.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `jobportal`
--
CREATE DATABASE IF NOT EXISTS `jobportal` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `jobportal`;

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE IF NOT EXISTS `applications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `applicant_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `job_title` varchar(100) NOT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  `applied_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `applicant_name`, `email`, `job_title`, `status`, `applied_at`) VALUES
(1, 'user', 'user@example.com', 'Ux Designer', 'Accepted', '2026-01-31 18:10:12'),
(2, 'user', 'user@example.com', 'Account', 'Accepted', '2026-01-31 18:19:14'),
(3, 'user', 'user@example.com', 'HR', 'Accepted', '2026-01-31 18:37:55'),
(4, 'user', 'user@example.com', 'full stack dev', 'Accepted', '2026-02-01 07:35:06');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(4, 'Finance'),
(1, 'IT / Software'),
(3, 'Management'),
(5, 'Marketing'),
(2, 'Teaching');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE IF NOT EXISTS `jobs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `type` varchar(50) DEFAULT 'Full Time',
  `salary` varchar(50) DEFAULT 'Negotiable',
  `posted_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `title`, `category`, `type`, `salary`, `posted_at`) VALUES
(1, 'Ux Designer', 'IT / Software', 'Full Time', '8 LPA', '2026-01-31 17:39:50'),
(2, 'Account', 'Teaching', 'Full Time', '4 LPA', '2026-01-31 18:18:47'),
(3, 'HR', 'Management', 'Full Time', '6 LPA', '2026-01-31 18:37:10'),
(4, 'full stack dev', 'IT / Software', 'Full Time', '10 LPA', '2026-02-01 07:34:09');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(3, 'user', '123', 'user'),
(4, 'admin', 'admin123', 'admin'),
(6, 'akshit', 'akshitking', 'user');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
