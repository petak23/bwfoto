-- Adminer 4.6.1 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `admin_menu`;
CREATE TABLE `admin_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `odkaz` varchar(50) COLLATE utf8_bin NOT NULL COMMENT 'Odkaz',
  `nazov` varchar(100) COLLATE utf8_bin NOT NULL COMMENT 'Názov položky',
  `id_user_roles` int(11) NOT NULL DEFAULT '4' COMMENT 'Id min úrovne registrácie',
  `avatar` varchar(200) COLLATE utf8_bin DEFAULT NULL COMMENT 'Odkaz na avatar aj s relatívnou cestou od adresára www',
  `view` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'Ak 1 položka sa zobrazí',
  PRIMARY KEY (`id`),
  KEY `id_registracia` (`id_user_roles`),
  CONSTRAINT `admin_menu_ibfk_2` FOREIGN KEY (`id_user_roles`) REFERENCES `user_roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Administračné menu';

INSERT INTO `admin_menu` (`id`, `odkaz`, `nazov`, `id_user_roles`, `avatar`, `view`) VALUES
(1,	'Homepage:',	'Úvod',	3,	'ikonky/Matrilineare_icon_set/png/Places user home.png',	1),
(2,	'Lang:',	'Editácia jazykov',	5,	'ikonky/Matrilineare_icon_set/png/Apps gwibber.png',	1),
(3,	'Slider:',	'Editácia slider-u',	4,	'ikonky/Matrilineare_icon_set/png/Places folder pictures.png',	1),
(4,	'User:',	'Editácia užívateľov',	5,	'ikonky/Matrilineare_icon_set/png/Places folder publicshare.png',	1),
(5,	'Verzie:',	'Verzie webu',	4,	'ikonky/Matrilineare_icon_set/png/Apps terminator.png',	1),
(6,	'Udaje:',	'Údaje webu',	4,	'ikonky/Matrilineare_icon_set/png/Categories preferences desktop.png',	1),
(7,	'Oznam:',	'Aktuality(oznamy)',	5,	'ikonky/Matrilineare_icon_set/png/Apps web browser.png',	0),
(8,	'Products:setup',	'Nastavenie produktov',	4,	'ikonky/Matrilineare_icon_set/png/Mimes package x generic.png',	1);

-- 2018-07-11 10:20:51
