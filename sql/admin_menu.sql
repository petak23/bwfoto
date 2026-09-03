-- Adminer 5.4.0 MariaDB 10.4.32-MariaDB dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `admin_menu`;
CREATE TABLE `admin_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `odkaz` varchar(50) NOT NULL COMMENT 'Odkaz',
  `nazov` varchar(100) NOT NULL COMMENT 'Názov položky',
  `id_user_roles` int(11) NOT NULL DEFAULT 4 COMMENT 'Id min úrovne registrácie',
  `avatar` varchar(200) DEFAULT NULL COMMENT 'Odkaz na avatar aj s relatívnou cestou od adresára www',
  `view` tinyint(4) NOT NULL DEFAULT 1 COMMENT 'Ak 1 položka sa zobrazí',
  `vue_link` varchar(30) DEFAULT NULL COMMENT 'Odkaz pre vue',
  PRIMARY KEY (`id`),
  KEY `id_registracia` (`id_user_roles`),
  CONSTRAINT `admin_menu_ibfk_2` FOREIGN KEY (`id_user_roles`) REFERENCES `user_roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_bin COMMENT='Administračné menu';

INSERT INTO `admin_menu` (`id`, `odkaz`, `nazov`, `id_user_roles`, `avatar`, `view`, `vue_link`) VALUES
(1,	'Homepage:',	'Úvod',	3,	'Matrilineare_icon_set/png/Places user home.png',	1,	NULL),
(2,	'Lang:',	'Editácia jazykov',	5,	'Matrilineare_icon_set/png/Apps gwibber.png',	1,	NULL),
(3,	'Slider:',	'Editácia slider-u',	4,	'Matrilineare_icon_set/png/Places folder pictures.png',	1,	'/slider'),
(4,	'User:',	'Editácia užívateľov',	5,	'Matrilineare_icon_set/png/Places folder publicshare.png',	1,	NULL),
(5,	'Verzie:',	'Verzie webu',	4,	'Matrilineare_icon_set/png/Apps terminator.png',	1,	NULL),
(6,	'Udaje:',	'Údaje webu',	4,	'Matrilineare_icon_set/png/Categories preferences desktop.png',	1,	'/udaje'),
(7,	'Oznam:',	'Aktuality(oznamy)',	5,	'Matrilineare_icon_set/png/Apps web browser.png',	0,	NULL),
(8,	'Products:setup',	'Nastavenie produktov',	4,	'Matrilineare_icon_set/png/Mimes package x generic.png',	1,	NULL),
(9,	'Nakup:',	'Nákupy',	4,	NULL,	1,	'/nakup');

-- 2026-01-14 13:57:44 UTC
