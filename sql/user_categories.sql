-- Adminer 4.6.2 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `user_categories`;
CREATE TABLE `user_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A] Index',
  `name` varchar(60) COLLATE utf8_bin NOT NULL COMMENT 'Názov',
  `shortcut` varchar(6) COLLATE utf8_bin NOT NULL COMMENT 'Skratka',
  `main_category` enum('V','R','O') COLLATE utf8_bin NOT NULL DEFAULT 'R' COMMENT 'Hlavný druh kategórie(V-Vedenie; R-rodičia; O-ostatné',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS `user_in_categories`;
CREATE TABLE `user_in_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A] Index',
  `id_user_main` int(11) NOT NULL COMMENT 'Id užívateľa',
  `id_user_categories` int(11) NOT NULL COMMENT 'Id_kategórie',
  PRIMARY KEY (`id`),
  KEY `id_user_main` (`id_user_main`),
  KEY `id_user_categories` (`id_user_categories`),
  CONSTRAINT `user_in_categories_ibfk_1` FOREIGN KEY (`id_user_main`) REFERENCES `user_main` (`id`),
  CONSTRAINT `user_in_categories_ibfk_2` FOREIGN KEY (`id_user_categories`) REFERENCES `user_categories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


-- 2018-07-17 11:56:09
