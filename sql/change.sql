/**
 * Author:  Ing. Peter VOJTECH m
 * Created: 02.01.2018
 */
SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A] Index',
  `id_hlavne_menu` int(11) NOT NULL DEFAULT '1' COMMENT 'Id položky hl. menu ku ktorej patrí',
  `id_user_main` int(11) NOT NULL DEFAULT '1' COMMENT 'Id užívateľa',
  `id_user_roles` int(11) NOT NULL DEFAULT '0' COMMENT 'Id min úrovne registrácie pre zobrazenie',
  `name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT 'Názov pre daný produkt',
  `web_name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT 'Špecifický názov produktu pre URL',
  `description` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT 'Popis produktu',
  `main_file` varchar(255) COLLATE utf8_bin NOT NULL COMMENT 'Názov súboru produktu s relatívnou cestou',
  `thumb_file` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT 'Názov súboru náhľadu pre obrázky a iné',
  `change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Čas zmeny',
  PRIMARY KEY (`id`),
  KEY `id_hlavne_menu` (`id_hlavne_menu`),
  KEY `id_user_main` (`id_user_main`),
  KEY `id_user_roles` (`id_user_roles`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`id_hlavne_menu`) REFERENCES `hlavne_menu` (`id`),
  CONSTRAINT `products_ibfk_2` FOREIGN KEY (`id_user_main`) REFERENCES `user_main` (`id`),
  CONSTRAINT `products_ibfk_3` FOREIGN KEY (`id_user_roles`) REFERENCES `user_roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Produkty';

INSERT INTO `druh` (`id`, `druh`, `modul`, `presenter`, `popis`, `povolene`, `je_spec_naz`, `robots`) VALUES
(8,	'products',	NULL,	'Products',	'Produkty',	1,	0,	0);

ALTER TABLE `udaje`
CHANGE `nazov` `nazov` varchar(30) COLLATE 'utf8_bin' NOT NULL DEFAULT 'nazov' COMMENT 'Názov prvku' AFTER `id_udaje_typ`;

INSERT INTO `udaje` (`id_user_roles`, `id_druh`, `id_udaje_typ`, `nazov`, `text`, `comment`) VALUES
(4,	8,	1,	'product_main_x',	'1510',	'Šírka hlavného obrázku produktu'),
(4,	8,	1,	'product_main_y',	'1000',	'Výška hlavného obrázku produktu'),
(4,	8,	1,	'product_main_quality',	'80',	'Kvalita kompresie hlavného obrázku'),
(4,	8,	1,	'product_thumb_x',	'226',	'Šírka náhľadového obrázku produktu'),
(4,	8,	1,	'product_thumb_y',	'150',	'Výška náhľadového obrázku produktu'),
(4,	8,	1,	'product_thumb_quality',	'70',	'Kvalita kompresie náhľadového obrázku');
