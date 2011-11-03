-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 06, 2010 at 02:31 PM
-- Server version: 5.1.36
-- PHP Version: 5.3.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `csslibrary`
--

-- --------------------------------------------------------

--
-- Table structure for table `ksg_comment`
--

CREATE TABLE IF NOT EXISTS `ksg_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cssid` int(11) NOT NULL,
  `un` varchar(30) NOT NULL,
  `comment` varchar(200) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cssid` (`cssid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ksg_cssdetail`
--

CREATE TABLE IF NOT EXISTS `ksg_cssdetail` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `desc` varchar(100) NOT NULL,
  `uploader` varchar(30) NOT NULL,
  `comments` int(4) NOT NULL,
  `link` varchar(100) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `liked` int(4) NOT NULL DEFAULT '0',
  `date` date NOT NULL,
  `validated` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ksg_liked`
--

CREATE TABLE IF NOT EXISTS `ksg_liked` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `un` varchar(30) NOT NULL,
  `cssid` int(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `un` (`un`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ksg_log`
--

CREATE TABLE IF NOT EXISTS `ksg_log` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `log` varchar(100) NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ksg_online`
--

CREATE TABLE IF NOT EXISTS `ksg_online` (
  `sid` varchar(50) NOT NULL,
  `un` varchar(30) NOT NULL,
  `lastact` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ksg_users`
--

CREATE TABLE IF NOT EXISTS `ksg_users` (
  `un` varchar(30) NOT NULL,
  `ban` tinyint(1) NOT NULL DEFAULT '0',
  `csscount` int(3) NOT NULL DEFAULT '0',
  `isAdmin` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `un` (`un`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
