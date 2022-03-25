-- Adminer 4.8.1 MySQL 5.5.5-10.5.13-MariaDB-1:10.5.13+maria~focal dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `slider`;
CREATE TABLE `slider` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `poradie` int(11) NOT NULL DEFAULT 1 COMMENT 'Určuje poradie obrázkov v slidery',
  `nadpis` varchar(50) COLLATE utf8_bin DEFAULT NULL COMMENT 'Nadpis obrázku',
  `popis` varchar(150) COLLATE utf8_bin DEFAULT NULL COMMENT 'Popis obrázku slideru vypisovaný v dolnej časti',
  `subor` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '*.jpg' COMMENT 'Názov obrázku slideru aj s relatívnou cestou',
  `zobrazenie` varchar(200) COLLATE utf8_bin DEFAULT NULL COMMENT 'Kedy sa obrázok zobrazí',
  `id_hlavne_menu` int(11) DEFAULT NULL COMMENT 'Odkaz na položku hlavného menu',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Popis obrázkou slideru aj s názvami súborov';

INSERT INTO `slider` (`id`, `poradie`, `nadpis`, `popis`, `subor`, `zobrazenie`, `id_hlavne_menu`) VALUES
(30,	3,	'HLAVNÉ PANORAMA 1',	NULL,	'bg-site-x.jpeg',	'1',	NULL),
(39,	-1,	NULL,	NULL,	'STROMY-1.jpeg',	NULL,	NULL),
(40,	0,	NULL,	NULL,	'TRAVA-6.jpeg',	'38',	NULL),
(41,	1,	NULL,	NULL,	'TRAVA-7.jpeg',	'34',	NULL);

-- 2022-03-24 06:07:47
