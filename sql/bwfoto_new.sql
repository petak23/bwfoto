-- Adminer 4.7.7 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `admin_menu`;
CREATE TABLE `admin_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `odkaz` varchar(50) COLLATE utf8_bin NOT NULL COMMENT 'Odkaz',
  `nazov` varchar(100) COLLATE utf8_bin NOT NULL COMMENT 'Názov položky',
  `id_user_roles` int(11) NOT NULL DEFAULT 4 COMMENT 'Id min úrovne registrácie',
  `avatar` varchar(200) COLLATE utf8_bin DEFAULT NULL COMMENT 'Odkaz na avatar aj s relatívnou cestou od adresára www',
  `view` tinyint(4) NOT NULL DEFAULT 1 COMMENT 'Ak 1 položka sa zobrazí',
  PRIMARY KEY (`id`),
  KEY `id_registracia` (`id_user_roles`),
  CONSTRAINT `admin_menu_ibfk_2` FOREIGN KEY (`id_user_roles`) REFERENCES `user_roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Administračné menu';

INSERT INTO `admin_menu` (`id`, `odkaz`, `nazov`, `id_user_roles`, `avatar`, `view`) VALUES
(1,	'Homepage:',	'Úvod',	3,	'Matrilineare_icon_set/png/Places user home.png',	1),
(2,	'Lang:',	'Editácia jazykov',	5,	'Matrilineare_icon_set/png/Apps gwibber.png',	1),
(3,	'Slider:',	'Editácia slider-u',	4,	'Matrilineare_icon_set/png/Places folder pictures.png',	1),
(4,	'User:',	'Editácia užívateľov',	5,	'Matrilineare_icon_set/png/Places folder publicshare.png',	1),
(5,	'Verzie:',	'Verzie webu',	4,	'Matrilineare_icon_set/png/Apps terminator.png',	1),
(6,	'Udaje:',	'Údaje webu',	4,	'Matrilineare_icon_set/png/Categories preferences desktop.png',	1),
(7,	'Oznam:',	'Aktuality(oznamy)',	5,	'Matrilineare_icon_set/png/Apps web browser.png',	0),
(8,	'Products:setup',	'Nastavenie produktov',	4,	'Matrilineare_icon_set/png/Mimes package x generic.png',	1);

DROP TABLE IF EXISTS `clanok_komponenty`;
CREATE TABLE `clanok_komponenty` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `id_hlavne_menu` int(11) NOT NULL COMMENT 'Id hl. menu, ktorému je komponenta pripojená',
  `spec_nazov` varchar(30) COLLATE utf8_bin DEFAULT NULL COMMENT 'Špecifický názov komponenty',
  `parametre` varchar(100) COLLATE utf8_bin DEFAULT NULL COMMENT 'Parametre komponenty',
  PRIMARY KEY (`id`),
  KEY `id_clanok` (`id_hlavne_menu`),
  CONSTRAINT `clanok_komponenty_ibfk_3` FOREIGN KEY (`id_hlavne_menu`) REFERENCES `hlavne_menu` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Zoznam komponent, ktoré sú priradené k článku';


DROP TABLE IF EXISTS `clanok_lang`;
CREATE TABLE `clanok_lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `id_lang` int(11) NOT NULL DEFAULT 1 COMMENT 'Id jazyka',
  `text` text COLLATE utf8_bin DEFAULT NULL,
  `anotacia` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT 'Anotácia článku v danom jazyku',
  PRIMARY KEY (`id`),
  KEY `id_lang` (`id_lang`),
  CONSTRAINT `clanok_lang_ibfk_2` FOREIGN KEY (`id_lang`) REFERENCES `lang` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Jazyková mutácia článku';

INSERT INTO `clanok_lang` (`id`, `id_lang`, `text`, `anotacia`) VALUES
(1,	1,	'Popis časti BW fotografie.',	NULL),
(2,	1,	'Popis časti fotografií architektúry.',	NULL),
(3,	1,	'Popis časti RGB fotografie.',	NULL),
(4,	1,	'Táto časť predstavuje ponuku našich produktov.',	'Táto časť predstavuje ponuku našich produktov.'),
(5,	1,	'1)  *Niečo nové* - Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus quis lectus metus, at posuere neque. Sed pharetra nibh eget orci.\n2)  Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus quis lectus metus, at posuere neque. Sed pharetra nibh eget orci.\n3)  **Novinka X** - Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus quis lectus metus, at posuere neque. Sed pharetra nibh eget orci.\n\n\n',	'Tu nájdete všetky novinky.'),
(6,	1,	'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus quis lectus metus, at posuere neque. Sed pharetra nibh eget orci convallis at posuere leo convallis. Sed blandit augue vitae augue scelerisque bibendum. Vivamus sit amet libero turpis, non venenatis urna.\n\nIn blandit, odio convallis suscipit venenatis, ante ipsum cursus augue, et mollis nunc diam eget sapien. Nulla facilisi. Etiam feugiat imperdiet rhoncus. Sed suscipit bibendum enim, sed volutpat tortor malesuada non. Morbi fringilla dui non purus porttitor mattis. Suspendisse quis.\n\nVýstava\n-------\n\nVulputate risus. Phasellus erat velit, sagittis sed varius volutpat, placerat nec urna. Nam eu metus vitae dolor fringilla feugiat. Nulla facilisi. Etiam enim metus, luctus in adipiscing at, consectetur quis sapien. Duis imperdiet egestas ligula, quis hendrerit ipsum ullamcorper et.\n\n',	'Tu nás môžete vidieť naživo.'),
(9,	1,	'Niečo k tejto časti. A tiež to bude radšej dlhé:\n\nLorem ipsum dolor sit amet, **consectetur** adipiscing elit. Phasellus quis lectus metus, at posuere neque. Sed adaf((toto je vysvetlenie)) \npharetra nibh eget orci convallis at posuere leo convallis. Sed blandit augue vitae augue scelerisque bibendum. Vivamus sit amet libero turpis, non venenatis urna. In blandit, odio convallis suscipit venenatis, ante ipsum cursus augue.\n\n',	'Anotácia časti A-1, ktorá bude veľmi dlhá aby sme videli, či to naozaj dobre zalamuje text.'),
(10,	1,	'Niečo k tejto časti',	NULL),
(11,	1,	'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus quis lectus metus, at posuere neque. Sed pharetra nibh eget orci convallis at posuere leo convallis. Sed blandit augue vitae augue scelerisque bibendum. Vivamus sit amet libero turpis, non venenatis urna. In blandit, odio convallis suscipit venenatis, ante ipsum cursus augue.\n\nEt mollis nunc diam eget sapien. Nulla facilisi. Etiam feugiat imperdiet rhoncus. Sed suscipit bibendum enim, sed volutpat tortor malesuada non. Morbi fringilla dui non purus porttitor mattis. Suspendisse quis vulputate risus. Phasellus erat velit, sagittis sed varius volutpat, placerat nec urna. Nam eu metus vitae dolor fringilla feugiat. Nulla.\n\nFacilisi. Etiam enim metus, luctus in adipiscing at, consectetur quis sapien. Duis imperdiet egestas ligula, quis hendrerit ipsum ullamcorper et. Phasellus id tristique orci. Proin consequat mi at felis scelerisque ullamcorper. Etiam tempus, felis vel eleifend porta, velit nunc mattis urna, at ullamcorper erat diam dignissim ante. Pellentesque justo risus.\n\nRutrum ac semper a, faucibus nec lorem. Nullam eget quam tellus, eget sagittis justo.Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Proin ante enim, tincidunt ut interdum in, adipiscing quis tortor. Nulla turpis lacus, rutrum in adipiscing ut, porttitor ac ante. Sed euismod, mauris a.\n\n',	''),
(12,	1,	'**Juraj Zámečník**\n\nSpišské Bystré 256\n\n0905 150 400\n\nbwfoto@bwfoto.sk',	'pokusná anotácia'),
(13,	1,	'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus quis lectus metus, at posuere neque. Sed pharetra nibh eget orci convallis at posuere leo convallis. Sed blandit augue vitae augue scelerisque bibendum. Vivamus sit amet libero turpis, non venenatis urna. In blandit, odio convallis suscipit venenatis, ante ipsum cursus augue.\n\n-------------------\n\nEt mollis nunc diam eget sapien. Nulla facilisi. Etiam feugiat imperdiet rhoncus. Sed suscipit bibendum enim, sed volutpat tortor malesuada non. Morbi fringilla dui non purus porttitor mattis. Suspendisse quis vulputate risus. Phasellus erat velit, sagittis sed varius volutpat, placerat nec urna. Nam eu metus vitae dolor fringilla feugiat. Nulla.\n\n|* A	| 12	| 0.125	|\n|* B	| 13	| 1.45	|\n|* C	| 19	| 2.14	|\n|* D	| 22	| 0.658	|\n|* E	| 11	| 3.14	|\n\n\n\n',	''),
(14,	1,	'> Úvodný popis stránky na domovskej stránke.\nNam aliquet augue a augue posuere, eget dapibus enim pharetra. Aliquam tempus metus sed sodales malesuada. Sed mattis metus id arcu rutrum congue. Fusce in est eget sapien tristique commodo. Aliquam erat volutpat. Nunc tincidunt fermentum dui, ut tempor turpis imperdiet quis. Mauris ultricies metus ut elit porttitor pellentesue. Nunc luctus sagittis bibendum. Donec iaculis nec arcu in feugiat.',	''),
(15,	1,	'Nam aliquet augue a augue posuere, eget dapibus enim pharetra. Aliquam tempus metus sed sodales malesuada. Sed mattis metus id arcu rutrum congue. Fusce in est eget sapien tristique commodo. Aliquam erat volutpat. Nunc tincidunt fermentum dui, ut tempor turpis imperdiet quis. Mauris ultricies metus ut elit porttitor pellentesue. Nunc luctus sagittis bibendum. Donec iaculis nec arcu in feugiat.\n\n',	''),
(16,	1,	'Toto je časť A3',	'Anotácia pre časť A3'),
(19,	1,	'Lorem ipsum dolor sit amet, consectetur adipiscing elit. \n\n*Phasellus quis lectus metus, at posuere neque*. \n8-O\nSed pharetra nibh eget orci convallis at posuere leo convallis. ΩSed blandit augue vitae augue scelerisque bibendum. VIVAMUS SIT amet libero turpis, non venenatis urna. In blandit, odio convallis suscipit venenatis, ante ipsum cursus augue.\n\n> Lorem ipsum dolor sit amet, consectetur adipiscing elit. \n\n\n\n',	'Ešte niečo iné'),
(20,	1,	'Fotografie krajiny',	''),
(21,	1,	'Fotografie kostolov',	'fotografie rôznych kostolov'),
(22,	1,	'Fotografie stromov',	''),
(23,	1,	'Fotografie budov',	''),
(24,	1,	'Fotografie v protisvetle',	''),
(25,	1,	'',	''),
(26,	1,	'fsgslakjfghalksjfhalskjfghalskjfghalskjfgaslkjfghaslkjghaslkjghaslkjgfhaslkgjhaslkgjhalkgjha',	'A XXX 2'),
(27,	1,	'',	'AHOJ'),
(28,	1,	'',	'A'),
(29,	1,	'',	'.');

DROP TABLE IF EXISTS `dlzka_novinky`;
CREATE TABLE `dlzka_novinky` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `nazov` varchar(30) COLLATE utf8_bin NOT NULL COMMENT 'Zobrazený názov',
  `dlzka` int(11) NOT NULL COMMENT 'Počet dní, v ktorých je novinka',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Tabuľka pre hodnoty dĺžky noviniek';

INSERT INTO `dlzka_novinky` (`id`, `nazov`, `dlzka`) VALUES
(1,	'Nesleduje sa',	0),
(2,	'Deň',	1),
(3,	'Týždeň',	7),
(4,	'Mesiac(30 dní)',	30),
(5,	'Štvrť roka(91 dní)',	91),
(6,	'Pol roka(182 dní)',	182),
(7,	'Rok',	365);

DROP TABLE IF EXISTS `dokumenty`;
CREATE TABLE `dokumenty` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_hlavne_menu` int(11) NOT NULL DEFAULT 1 COMMENT 'Id položky hl. menu ku ktorej patrí',
  `id_user_main` int(11) NOT NULL DEFAULT 1 COMMENT 'Id užívateľa',
  `id_user_roles` int(11) NOT NULL DEFAULT 0 COMMENT 'Id min úrovne registrácie pre zobrazenie',
  `znacka` varchar(20) COLLATE utf8_bin DEFAULT NULL COMMENT 'Značka súboru pre vloženie do textu',
  `name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT 'Názov titulku pre daný dokument',
  `pripona` varchar(20) COLLATE utf8_bin NOT NULL COMMENT 'Prípona súboru',
  `web_name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT 'Špecifický názov dokumentu pre URL',
  `description` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT 'Popis dokumentu',
  `main_file` varchar(255) COLLATE utf8_bin NOT NULL COMMENT 'Názov súboru s relatívnou cestou',
  `thumb_file` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT 'Názov súboru thumb pre obrázky a iné ',
  `change` datetime NOT NULL COMMENT 'Dátum uloženia alebo opravy - časová pečiatka',
  `zobraz_v_texte` tinyint(4) NOT NULL DEFAULT 1 COMMENT 'Zobrazenie obrázku v texte',
  `type` tinyint(4) NOT NULL DEFAULT 1 COMMENT 'Typ prílohy',
  `pocitadlo` int(11) NOT NULL DEFAULT 0 COMMENT 'Počítadlo stiahnutí',
  PRIMARY KEY (`id`),
  UNIQUE KEY `spec_nazov` (`web_name`),
  KEY `id_user_profiles` (`id_user_main`),
  KEY `id_registracia` (`id_user_roles`),
  KEY `id_hlavne_menu` (`id_hlavne_menu`),
  CONSTRAINT `dokumenty_ibfk_1` FOREIGN KEY (`id_user_main`) REFERENCES `user_main` (`id`),
  CONSTRAINT `dokumenty_ibfk_3` FOREIGN KEY (`id_hlavne_menu`) REFERENCES `hlavne_menu` (`id`),
  CONSTRAINT `dokumenty_ibfk_4` FOREIGN KEY (`id_user_roles`) REFERENCES `user_roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Prílohy k článkom';

INSERT INTO `dokumenty` (`id`, `id_hlavne_menu`, `id_user_main`, `id_user_roles`, `znacka`, `name`, `pripona`, `web_name`, `description`, `main_file`, `thumb_file`, `change`, `zobraz_v_texte`, `type`, `pocitadlo`) VALUES
(43,	24,	1,	0,	'#I-43#',	'protisvelo2',	'jpg',	'protisvelo2',	NULL,	'files/prilohy/protisvelo2.jpg',	'files/prilohy/tb_protisvelo2.jpg',	'2017-12-18 08:48:37',	1,	2,	0),
(44,	24,	1,	0,	'#I-44#',	'protisvetlo1',	'JPG',	'protisvetlo1',	NULL,	'files/prilohy/protisvetlo1.JPG',	'files/prilohy/tb_protisvetlo1.JPG',	'2017-12-18 08:48:37',	1,	2,	0),
(49,	9,	2,	0,	'#I-49#',	'ATELIER-ZAMECNIK-01-zmena-velikosti',	'jpg',	'atelier-zamecnik-01-zmena-velikosti',	NULL,	'files/prilohy/ATELIER-ZAMECNIK-01-zmena-velikosti.jpg',	'files/prilohy/tb_ATELIER-ZAMECNIK-01-zmena-velikosti.jpg',	'2020-03-21 18:31:14',	1,	2,	0),
(50,	9,	2,	0,	'#I-50#',	'ATELIER-ZAMECNIK-02-zmena-velikosti',	'jpg',	'atelier-zamecnik-02-zmena-velikosti',	NULL,	'files/prilohy/ATELIER-ZAMECNIK-02-zmena-velikosti.jpg',	'files/prilohy/tb_ATELIER-ZAMECNIK-02-zmena-velikosti.jpg',	'2020-03-21 18:31:14',	1,	2,	0),
(51,	9,	2,	0,	'#I-51#',	'ATELIER-ZAMECNIK-03-zmena-velikosti',	'jpg',	'atelier-zamecnik-03-zmena-velikosti',	NULL,	'files/prilohy/ATELIER-ZAMECNIK-03-zmena-velikosti.jpg',	'files/prilohy/tb_ATELIER-ZAMECNIK-03-zmena-velikosti.jpg',	'2020-03-21 18:31:14',	1,	2,	0),
(52,	9,	2,	0,	'#I-52#',	'ATELIER-ZAMECNIK-04-zmena-velikosti',	'jpg',	'atelier-zamecnik-04-zmena-velikosti',	NULL,	'files/prilohy/ATELIER-ZAMECNIK-04-zmena-velikosti.jpg',	'files/prilohy/tb_ATELIER-ZAMECNIK-04-zmena-velikosti.jpg',	'2020-03-21 18:31:14',	1,	2,	0),
(53,	9,	2,	0,	'#I-53#',	'ATELIER-ZAMECNIK-05-zmena-velikosti',	'jpg',	'atelier-zamecnik-05-zmena-velikosti',	NULL,	'files/prilohy/ATELIER-ZAMECNIK-05-zmena-velikosti.jpg',	'files/prilohy/tb_ATELIER-ZAMECNIK-05-zmena-velikosti.jpg',	'2020-03-21 18:31:14',	1,	2,	0),
(54,	9,	2,	0,	'#I-54#',	'ATELIER-ZAMECNIK-06-zmena-velikosti',	'jpg',	'atelier-zamecnik-06-zmena-velikosti',	NULL,	'files/prilohy/ATELIER-ZAMECNIK-06-zmena-velikosti.jpg',	'files/prilohy/tb_ATELIER-ZAMECNIK-06-zmena-velikosti.jpg',	'2020-03-21 18:31:14',	1,	2,	0),
(55,	9,	2,	0,	'#I-55#',	'ATELIER-ZAMECNIK-07-zmena-velikosti',	'jpg',	'atelier-zamecnik-07-zmena-velikosti',	NULL,	'files/prilohy/ATELIER-ZAMECNIK-07-zmena-velikosti.jpg',	'files/prilohy/tb_ATELIER-ZAMECNIK-07-zmena-velikosti.jpg',	'2020-03-21 18:31:14',	1,	2,	0),
(56,	9,	2,	0,	'#I-56#',	'ATELIER-ZAMECNIK-08-zmena-velikosti',	'jpg',	'atelier-zamecnik-08-zmena-velikosti',	NULL,	'files/prilohy/ATELIER-ZAMECNIK-08-zmena-velikosti.jpg',	'files/prilohy/tb_ATELIER-ZAMECNIK-08-zmena-velikosti.jpg',	'2020-03-21 18:31:14',	1,	2,	0),
(57,	9,	2,	0,	'#I-57#',	'ATELIER-ZAMECNIK-09-zmena-velikosti',	'jpg',	'atelier-zamecnik-09-zmena-velikosti',	NULL,	'files/prilohy/ATELIER-ZAMECNIK-09-zmena-velikosti.jpg',	'files/prilohy/tb_ATELIER-ZAMECNIK-09-zmena-velikosti.jpg',	'2020-03-21 18:31:14',	1,	2,	0),
(58,	9,	2,	0,	'#I-58#',	'ATELIER-ZAMECNIK-10-zmena-velikosti',	'jpg',	'atelier-zamecnik-10-zmena-velikosti',	NULL,	'files/prilohy/ATELIER-ZAMECNIK-10-zmena-velikosti.jpg',	'files/prilohy/tb_ATELIER-ZAMECNIK-10-zmena-velikosti.jpg',	'2020-03-21 18:31:14',	1,	2,	0),
(59,	9,	2,	0,	'#I-59#',	'ATELIER-ZAMECNIK-11-zmena-velikosti',	'jpg',	'atelier-zamecnik-11-zmena-velikosti',	NULL,	'files/prilohy/ATELIER-ZAMECNIK-11-zmena-velikosti.jpg',	'files/prilohy/tb_ATELIER-ZAMECNIK-11-zmena-velikosti.jpg',	'2020-03-21 18:31:14',	1,	2,	0),
(60,	9,	2,	0,	'#I-60#',	'ATELIER-ZAMECNIK-12-zmena-velikosti',	'jpg',	'atelier-zamecnik-12-zmena-velikosti',	NULL,	'files/prilohy/ATELIER-ZAMECNIK-12-zmena-velikosti.jpg',	'files/prilohy/tb_ATELIER-ZAMECNIK-12-zmena-velikosti.jpg',	'2020-03-21 18:31:14',	1,	2,	0),
(61,	10,	2,	0,	'#I-61#',	'ATELIER-ZAMECNIK-13-zmena-velikosti',	'jpg',	'atelier-zamecnik-13-zmena-velikosti',	NULL,	'files/prilohy/ATELIER-ZAMECNIK-13-zmena-velikosti.jpg',	'files/prilohy/tb_ATELIER-ZAMECNIK-13-zmena-velikosti.jpg',	'2020-03-21 18:51:42',	1,	2,	0),
(62,	10,	2,	0,	'#I-62#',	'ATELIER-ZAMECNIK-14-zmena-velikosti',	'jpg',	'atelier-zamecnik-14-zmena-velikosti',	NULL,	'files/prilohy/ATELIER-ZAMECNIK-14-zmena-velikosti.jpg',	'files/prilohy/tb_ATELIER-ZAMECNIK-14-zmena-velikosti.jpg',	'2020-03-21 18:51:42',	1,	2,	0),
(63,	10,	2,	0,	'#I-63#',	'ATELIER-ZAMECNIK-15-zmena-velikosti',	'jpg',	'atelier-zamecnik-15-zmena-velikosti',	NULL,	'files/prilohy/ATELIER-ZAMECNIK-15-zmena-velikosti.jpg',	'files/prilohy/tb_ATELIER-ZAMECNIK-15-zmena-velikosti.jpg',	'2020-03-21 18:51:42',	1,	2,	0),
(64,	10,	2,	0,	'#I-64#',	'ATELIER-ZAMECNIK-16-zmena-velikosti',	'jpg',	'atelier-zamecnik-16-zmena-velikosti',	NULL,	'files/prilohy/ATELIER-ZAMECNIK-16-zmena-velikosti.jpg',	'files/prilohy/tb_ATELIER-ZAMECNIK-16-zmena-velikosti.jpg',	'2020-03-21 18:51:42',	1,	2,	0),
(65,	10,	2,	0,	'#I-65#',	'ATELIER-ZAMECNIK-17-zmena-velikosti',	'jpg',	'atelier-zamecnik-17-zmena-velikosti',	NULL,	'files/prilohy/ATELIER-ZAMECNIK-17-zmena-velikosti.jpg',	'files/prilohy/tb_ATELIER-ZAMECNIK-17-zmena-velikosti.jpg',	'2020-03-21 18:51:42',	1,	2,	0),
(66,	10,	2,	0,	'#I-66#',	'ATELIER-ZAMECNIK-18-zmena-velikosti',	'jpg',	'atelier-zamecnik-18-zmena-velikosti',	NULL,	'files/prilohy/ATELIER-ZAMECNIK-18-zmena-velikosti.jpg',	'files/prilohy/tb_ATELIER-ZAMECNIK-18-zmena-velikosti.jpg',	'2020-03-21 18:51:42',	1,	2,	0),
(67,	10,	2,	0,	'#I-67#',	'ATELIER-ZAMECNIK-19',	'jpg',	'atelier-zamecnik-19',	NULL,	'files/prilohy/ATELIER-ZAMECNIK-19.jpg',	'files/prilohy/tb_ATELIER-ZAMECNIK-19.jpg',	'2020-03-21 18:51:42',	1,	2,	0),
(68,	10,	2,	0,	'#I-68#',	'ATELIER-ZAMECNIK-20',	'jpg',	'atelier-zamecnik-20',	NULL,	'files/prilohy/ATELIER-ZAMECNIK-20.jpg',	'files/prilohy/tb_ATELIER-ZAMECNIK-20.jpg',	'2020-03-21 18:51:42',	1,	2,	0),
(69,	10,	2,	0,	'#I-69#',	'ATELIER-ZAMECNIK-21',	'jpg',	'atelier-zamecnik-21',	NULL,	'files/prilohy/ATELIER-ZAMECNIK-21.jpg',	'files/prilohy/tb_ATELIER-ZAMECNIK-21.jpg',	'2020-03-21 18:51:42',	1,	2,	0),
(70,	10,	2,	0,	'#I-70#',	'ATELIER-ZAMECNIK-22',	'jpg',	'atelier-zamecnik-22',	NULL,	'files/prilohy/ATELIER-ZAMECNIK-22.jpg',	'files/prilohy/tb_ATELIER-ZAMECNIK-22.jpg',	'2020-03-21 18:51:42',	1,	2,	0),
(71,	10,	2,	0,	'#I-71#',	'ATELIER-ZAMECNIK-23',	'jpg',	'atelier-zamecnik-23',	NULL,	'files/prilohy/ATELIER-ZAMECNIK-23.jpg',	'files/prilohy/tb_ATELIER-ZAMECNIK-23.jpg',	'2020-03-21 18:51:42',	1,	2,	0),
(72,	10,	2,	0,	'#I-72#',	'ATELIER-ZAMECNIK-24',	'jpg',	'atelier-zamecnik-24',	NULL,	'files/prilohy/ATELIER-ZAMECNIK-24.jpg',	'files/prilohy/tb_ATELIER-ZAMECNIK-24.jpg',	'2020-03-21 18:51:42',	1,	2,	0),
(73,	10,	2,	0,	'#I-73#',	'ATELIER-ZAMECNIK-25',	'jpg',	'atelier-zamecnik-25',	NULL,	'files/prilohy/ATELIER-ZAMECNIK-25.jpg',	'files/prilohy/tb_ATELIER-ZAMECNIK-25.jpg',	'2020-03-21 18:51:42',	1,	2,	0),
(74,	10,	2,	0,	'#I-74#',	'ATELIER-ZAMECNIK-26',	'jpg',	'atelier-zamecnik-26',	NULL,	'files/prilohy/ATELIER-ZAMECNIK-26.jpg',	'files/prilohy/tb_ATELIER-ZAMECNIK-26.jpg',	'2020-03-21 18:51:42',	1,	2,	0),
(75,	10,	2,	0,	'#I-75#',	'ATELIER-ZAMECNIK-27',	'jpg',	'atelier-zamecnik-27',	NULL,	'files/prilohy/ATELIER-ZAMECNIK-27.jpg',	'files/prilohy/tb_ATELIER-ZAMECNIK-27.jpg',	'2020-03-21 18:51:42',	1,	2,	0),
(76,	10,	2,	0,	'#I-76#',	'ATELIER-ZAMECNIK-28',	'jpg',	'atelier-zamecnik-28',	NULL,	'files/prilohy/ATELIER-ZAMECNIK-28.jpg',	'files/prilohy/tb_ATELIER-ZAMECNIK-28.jpg',	'2020-03-21 18:51:42',	1,	2,	0),
(77,	10,	2,	0,	'#I-77#',	'ATELIER-ZAMECNIK-28X',	'jpg',	'atelier-zamecnik-28x',	NULL,	'files/prilohy/ATELIER-ZAMECNIK-28X.jpg',	'files/prilohy/tb_ATELIER-ZAMECNIK-28X.jpg',	'2020-03-21 18:51:42',	1,	2,	0),
(78,	10,	2,	0,	'#I-78#',	'ATELIER-ZAMECNIK-28X-zmena-velikosti',	'jpg',	'atelier-zamecnik-28x-zmena-velikosti',	NULL,	'files/prilohy/ATELIER-ZAMECNIK-28X-zmena-velikosti.jpg',	'files/prilohy/tb_ATELIER-ZAMECNIK-28X-zmena-velikosti.jpg',	'2020-03-21 18:51:42',	1,	2,	0),
(79,	10,	2,	0,	'#I-79#',	'ATELIER-ZAMECNIK-29',	'jpg',	'atelier-zamecnik-29',	NULL,	'files/prilohy/ATELIER-ZAMECNIK-29.jpg',	'files/prilohy/tb_ATELIER-ZAMECNIK-29.jpg',	'2020-03-21 18:51:42',	1,	2,	0),
(80,	10,	2,	0,	'#I-80#',	'ATELIER-ZAMECNIK-30',	'jpg',	'atelier-zamecnik-30',	NULL,	'files/prilohy/ATELIER-ZAMECNIK-30.jpg',	'files/prilohy/tb_ATELIER-ZAMECNIK-30.jpg',	'2020-03-21 18:51:42',	1,	2,	0),
(81,	26,	2,	0,	'#I-81#',	'ATELIER-ZAMECNIK-51',	'jpg',	'atelier-zamecnik-51',	NULL,	'files/prilohy/ATELIER-ZAMECNIK-51.jpg',	'files/prilohy/tb_ATELIER-ZAMECNIK-51.jpg',	'2020-03-22 01:18:50',	1,	2,	0),
(82,	26,	2,	0,	'#I-82#',	'ATELIER-ZAMECNIK-52',	'jpg',	'atelier-zamecnik-52',	NULL,	'files/prilohy/ATELIER-ZAMECNIK-52.jpg',	'files/prilohy/tb_ATELIER-ZAMECNIK-52.jpg',	'2020-03-22 01:18:50',	1,	2,	0),
(83,	26,	2,	0,	'#I-83#',	'Pohlad-JZx',	'jpg',	'pohlad-jzx',	NULL,	'files/prilohy/Pohlad-JZx.jpg',	'files/prilohy/tb_Pohlad-JZx.jpg',	'2020-03-22 01:18:50',	1,	2,	0),
(84,	26,	2,	0,	'#I-84#',	'Pohlad-Zx',	'jpg',	'pohlad-zx',	NULL,	'files/prilohy/Pohlad-Zx.jpg',	'files/prilohy/tb_Pohlad-Zx.jpg',	'2020-03-22 01:18:50',	1,	2,	0),
(85,	26,	2,	0,	'#I-85#',	'xxxx-1',	'jpg',	'xxxx-1',	NULL,	'files/prilohy/xxxx-1.jpg',	'files/prilohy/tb_xxxx-1.jpg',	'2020-03-22 01:18:50',	1,	2,	0),
(86,	26,	2,	0,	'#I-86#',	'xxxxxxxxx2',	'jpg',	'xxxxxxxxx2',	NULL,	'files/prilohy/xxxxxxxxx2.jpg',	'files/prilohy/tb_xxxxxxxxx2.jpg',	'2020-03-22 01:18:50',	1,	2,	0),
(87,	28,	2,	0,	'#I-87#',	'P1267879',	'JPG',	'p1267879',	NULL,	'files/prilohy/P1267879.JPG',	'files/prilohy/tb_P1267879.JPG',	'2020-03-23 17:58:59',	1,	2,	0),
(88,	28,	2,	0,	'#I-88#',	'P1312310',	'JPG',	'p1312310',	NULL,	'files/prilohy/P1312310.JPG',	'files/prilohy/tb_P1312310.JPG',	'2020-03-23 17:58:59',	1,	2,	0),
(89,	28,	2,	0,	'#I-89#',	'P2022382',	'jpg',	'p2022382',	NULL,	'files/prilohy/P2022382.jpg',	'files/prilohy/tb_P2022382.jpg',	'2020-03-23 17:58:59',	1,	2,	0),
(90,	28,	2,	0,	'#I-90#',	'P2022416',	'JPG',	'p2022416',	NULL,	'files/prilohy/P2022416.JPG',	'files/prilohy/tb_P2022416.JPG',	'2020-03-23 17:58:59',	1,	2,	0),
(91,	28,	2,	0,	'#I-91#',	'P2022418',	'JPG',	'p2022418',	NULL,	'files/prilohy/P2022418.JPG',	'files/prilohy/tb_P2022418.JPG',	'2020-03-23 17:58:59',	1,	2,	0),
(92,	28,	2,	0,	'#I-92#',	'P2022481',	'jpg',	'p2022481',	NULL,	'files/prilohy/P2022481.jpg',	'files/prilohy/tb_P2022481.jpg',	'2020-03-23 17:58:59',	1,	2,	0),
(93,	28,	2,	0,	'#I-93#',	'P3153081',	'JPG',	'p3153081',	NULL,	'files/prilohy/P3153081.JPG',	'files/prilohy/tb_P3153081.JPG',	'2020-03-23 17:58:59',	1,	2,	0),
(94,	28,	2,	0,	'#I-94#',	'P3308275',	'JPG',	'p3308275',	NULL,	'files/prilohy/P3308275.JPG',	'files/prilohy/tb_P3308275.JPG',	'2020-03-23 17:58:59',	1,	2,	0),
(95,	28,	2,	0,	'#I-95#',	'P4083766',	'JPG',	'p4083766',	NULL,	'files/prilohy/P4083766.JPG',	'files/prilohy/tb_P4083766.JPG',	'2020-03-23 17:58:59',	1,	2,	0),
(96,	28,	2,	0,	'#I-96#',	'P4093894',	'JPG',	'p4093894',	NULL,	'files/prilohy/P4093894.JPG',	'files/prilohy/tb_P4093894.JPG',	'2020-03-23 17:58:59',	1,	2,	0),
(97,	28,	2,	0,	'#I-97#',	'P4183288',	'JPG',	'p4183288',	NULL,	'files/prilohy/P4183288.JPG',	'files/prilohy/tb_P4183288.JPG',	'2020-03-23 17:58:59',	1,	2,	0),
(98,	28,	2,	0,	'#I-98#',	'P4183300',	'JPG',	'p4183300',	NULL,	'files/prilohy/P4183300.JPG',	'files/prilohy/tb_P4183300.JPG',	'2020-03-23 17:58:59',	1,	2,	0),
(99,	28,	2,	0,	'#I-99#',	'P5109025',	'jpg',	'p5109025',	NULL,	'files/prilohy/P5109025.jpg',	'files/prilohy/tb_P5109025.jpg',	'2020-03-23 17:58:59',	1,	2,	0),
(100,	28,	2,	0,	'#I-100#',	'P5184446',	'jpg',	'p5184446',	NULL,	'files/prilohy/P5184446.jpg',	'files/prilohy/tb_P5184446.jpg',	'2020-03-23 17:58:59',	1,	2,	0),
(101,	28,	2,	0,	'#I-101#',	'P5184688',	'JPG',	'p5184688',	NULL,	'files/prilohy/P5184688.JPG',	'files/prilohy/tb_P5184688.JPG',	'2020-03-23 17:58:59',	1,	2,	0),
(102,	28,	2,	0,	'#I-102#',	'P5299104',	'JPG',	'p5299104',	NULL,	'files/prilohy/P5299104.JPG',	'files/prilohy/tb_P5299104.JPG',	'2020-03-23 17:58:59',	1,	2,	0),
(103,	28,	2,	0,	'#I-103#',	'P6194984',	'JPG',	'p6194984',	NULL,	'files/prilohy/P6194984.JPG',	'files/prilohy/tb_P6194984.JPG',	'2020-03-23 17:58:59',	1,	2,	0),
(104,	28,	2,	0,	'#I-104#',	'P6245064',	'JPG',	'p6245064',	NULL,	'files/prilohy/P6245064.JPG',	'files/prilohy/tb_P6245064.JPG',	'2020-03-23 17:58:59',	1,	2,	0),
(105,	28,	2,	0,	'#I-105#',	'P7283714',	'JPG',	'p7283714',	NULL,	'files/prilohy/P7283714.JPG',	'files/prilohy/tb_P7283714.JPG',	'2020-03-23 17:58:59',	1,	2,	0),
(106,	28,	2,	0,	'#I-106#',	'P8195781',	'JPG',	'p8195781',	NULL,	'files/prilohy/P8195781.JPG',	'files/prilohy/tb_P8195781.JPG',	'2020-03-23 17:58:59',	1,	2,	0),
(107,	28,	2,	0,	'#I-107#',	'PA108829',	'JPG',	'pa108829',	NULL,	'files/prilohy/PA108829.JPG',	'files/prilohy/tb_PA108829.JPG',	'2020-03-23 18:03:17',	1,	2,	0),
(108,	28,	2,	0,	'#I-108#',	'P9140012',	'JPG',	'p9140012',	NULL,	'files/prilohy/P9140012.JPG',	'files/prilohy/tb_P9140012.JPG',	'2020-03-23 18:03:17',	1,	2,	0),
(109,	28,	2,	0,	'#I-109#',	'P9191871',	'JPG',	'p9191871',	NULL,	'files/prilohy/P9191871.JPG',	'files/prilohy/tb_P9191871.JPG',	'2020-03-23 18:03:17',	1,	2,	0),
(110,	28,	2,	0,	'#I-110#',	'P9191899',	'JPG',	'p9191899',	NULL,	'files/prilohy/P9191899.JPG',	'files/prilohy/tb_P9191899.JPG',	'2020-03-23 18:03:17',	1,	2,	0),
(111,	28,	2,	0,	'#I-111#',	'P9192032',	'JPG',	'p9192032',	NULL,	'files/prilohy/P9192032.JPG',	'files/prilohy/tb_P9192032.JPG',	'2020-03-23 18:03:17',	1,	2,	0),
(112,	28,	2,	0,	'#I-112#',	'P9192046',	'JPG',	'p9192046',	NULL,	'files/prilohy/P9192046.JPG',	'files/prilohy/tb_P9192046.JPG',	'2020-03-23 18:03:17',	1,	2,	0),
(113,	28,	2,	0,	'#I-113#',	'P9226071',	'JPG',	'p9226071',	NULL,	'files/prilohy/P9226071.JPG',	'files/prilohy/tb_P9226071.JPG',	'2020-03-23 18:03:17',	1,	2,	0),
(114,	28,	2,	0,	'#I-114#',	'P9236182',	'JPG',	'p9236182',	NULL,	'files/prilohy/P9236182.JPG',	'files/prilohy/tb_P9236182.JPG',	'2020-03-23 18:03:17',	1,	2,	0),
(115,	28,	2,	0,	'#I-115#',	'P9240260',	'JPG',	'p9240260',	NULL,	'files/prilohy/P9240260.JPG',	'files/prilohy/tb_P9240260.JPG',	'2020-03-23 18:03:17',	1,	2,	0),
(116,	28,	2,	0,	'#I-116#',	'P9262273',	'JPG',	'p9262273',	NULL,	'files/prilohy/P9262273.JPG',	'files/prilohy/tb_P9262273.JPG',	'2020-03-23 18:03:17',	1,	2,	0),
(117,	28,	2,	0,	'#I-117#',	'PA032629',	'JPG',	'pa032629',	NULL,	'files/prilohy/PA032629.JPG',	'files/prilohy/tb_PA032629.JPG',	'2020-03-23 18:03:17',	1,	2,	0),
(118,	28,	2,	0,	'#I-118#',	'PA032662',	'JPG',	'pa032662',	NULL,	'files/prilohy/PA032662.JPG',	'files/prilohy/tb_PA032662.JPG',	'2020-03-23 18:03:17',	1,	2,	0),
(119,	28,	2,	0,	'#I-119#',	'PA061362',	'JPG',	'pa061362',	NULL,	'files/prilohy/PA061362.JPG',	'files/prilohy/tb_PA061362.JPG',	'2020-03-23 18:03:17',	1,	2,	0),
(120,	28,	2,	0,	'#I-120#',	'PA061441',	'JPG',	'pa061441',	NULL,	'files/prilohy/PA061441.JPG',	'files/prilohy/tb_PA061441.JPG',	'2020-03-23 18:03:17',	1,	2,	0),
(121,	28,	2,	0,	'#I-121#',	'PA078437',	'JPG',	'pa078437',	NULL,	'files/prilohy/PA078437.JPG',	'files/prilohy/tb_PA078437.JPG',	'2020-03-23 18:03:17',	1,	2,	0),
(122,	28,	2,	0,	'#I-122#',	'PA090594',	'JPG',	'pa090594',	NULL,	'files/prilohy/PA090594.JPG',	'files/prilohy/tb_PA090594.JPG',	'2020-03-23 18:03:17',	1,	2,	0),
(123,	28,	2,	0,	'#I-123#',	'PA090681',	'JPG',	'pa090681',	NULL,	'files/prilohy/PA090681.JPG',	'files/prilohy/tb_PA090681.JPG',	'2020-03-23 18:03:17',	1,	2,	0),
(124,	28,	2,	0,	'#I-124#',	'PA098743',	'JPG',	'pa098743',	NULL,	'files/prilohy/PA098743.JPG',	'files/prilohy/tb_PA098743.JPG',	'2020-03-23 18:03:17',	1,	2,	0),
(125,	28,	2,	0,	'#I-125#',	'PA098753',	'JPG',	'pa098753',	NULL,	'files/prilohy/PA098753.JPG',	'files/prilohy/tb_PA098753.JPG',	'2020-03-23 18:03:17',	1,	2,	0),
(126,	28,	2,	0,	'#I-126#',	'PA106606',	'JPG',	'pa106606',	NULL,	'files/prilohy/PA106606.JPG',	'files/prilohy/tb_PA106606.JPG',	'2020-03-23 18:03:17',	1,	2,	0),
(127,	28,	2,	0,	'#I-127#',	'PA108897',	'JPG',	'pa108897',	NULL,	'files/prilohy/PA108897.JPG',	'files/prilohy/tb_PA108897.JPG',	'2020-03-23 18:19:33',	1,	2,	0),
(128,	28,	2,	0,	'#I-128#',	'PA111479',	'JPG',	'pa111479',	NULL,	'files/prilohy/PA111479.JPG',	'files/prilohy/tb_PA111479.JPG',	'2020-03-23 18:19:33',	1,	2,	0),
(129,	28,	2,	0,	'#I-129#',	'PA111496',	'JPG',	'pa111496',	NULL,	'files/prilohy/PA111496.JPG',	'files/prilohy/tb_PA111496.JPG',	'2020-03-23 18:19:33',	1,	2,	0),
(130,	28,	2,	0,	'#I-130#',	'PA150799',	'JPG',	'pa150799',	NULL,	'files/prilohy/PA150799.JPG',	'files/prilohy/tb_PA150799.JPG',	'2020-03-23 18:19:33',	1,	2,	0),
(131,	28,	2,	0,	'#I-131#',	'PA150820',	'jpg',	'pa150820',	NULL,	'files/prilohy/PA150820.jpg',	'files/prilohy/tb_PA150820.jpg',	'2020-03-23 18:19:33',	1,	2,	0),
(132,	28,	2,	0,	'#I-132#',	'PA181845',	'JPG',	'pa181845',	NULL,	'files/prilohy/PA181845.JPG',	'files/prilohy/tb_PA181845.JPG',	'2020-03-23 18:19:33',	1,	2,	0),
(133,	28,	2,	0,	'#I-133#',	'PA261078',	'JPG',	'pa261078',	NULL,	'files/prilohy/PA261078.JPG',	'files/prilohy/tb_PA261078.JPG',	'2020-03-23 18:19:33',	1,	2,	0),
(134,	28,	2,	0,	'#I-134#',	'PA266842',	'JPG',	'pa266842',	NULL,	'files/prilohy/PA266842.JPG',	'files/prilohy/tb_PA266842.JPG',	'2020-03-23 18:19:33',	1,	2,	0),
(135,	28,	2,	0,	'#I-135#',	'PA266885',	'JPG',	'pa266885',	NULL,	'files/prilohy/PA266885.JPG',	'files/prilohy/tb_PA266885.JPG',	'2020-03-23 18:19:33',	1,	2,	0),
(136,	28,	2,	0,	'#I-136#',	'PA281104',	'JPG',	'pa281104',	NULL,	'files/prilohy/PA281104.JPG',	'files/prilohy/tb_PA281104.JPG',	'2020-03-23 18:19:33',	1,	2,	0),
(137,	28,	2,	0,	'#I-137#',	'PA291190',	'JPG',	'pa291190',	NULL,	'files/prilohy/PA291190.JPG',	'files/prilohy/tb_PA291190.JPG',	'2020-03-23 18:19:33',	1,	2,	0),
(138,	28,	2,	0,	'#I-138#',	'PA313119',	'JPG',	'pa313119',	NULL,	'files/prilohy/PA313119.JPG',	'files/prilohy/tb_PA313119.JPG',	'2020-03-23 18:19:33',	1,	2,	0),
(139,	28,	2,	0,	'#I-139#',	'PB013188',	'JPG',	'pb013188',	NULL,	'files/prilohy/PB013188.JPG',	'files/prilohy/tb_PB013188.JPG',	'2020-03-23 18:19:33',	1,	2,	0),
(140,	28,	2,	0,	'#I-140#',	'PB013206',	'JPG',	'pb013206',	NULL,	'files/prilohy/PB013206.JPG',	'files/prilohy/tb_PB013206.JPG',	'2020-03-23 18:19:33',	1,	2,	0),
(141,	28,	2,	0,	'#I-141#',	'PB172160',	'JPG',	'pb172160',	NULL,	'files/prilohy/PB172160.JPG',	'files/prilohy/tb_PB172160.JPG',	'2020-03-23 18:19:33',	1,	2,	0),
(142,	28,	2,	0,	'#I-142#',	'PC161697',	'JPG',	'pc161697',	NULL,	'files/prilohy/PC161697.JPG',	'files/prilohy/tb_PC161697.JPG',	'2020-03-23 18:19:33',	1,	2,	0),
(143,	28,	2,	0,	'#I-143#',	'PC197461',	'JPG',	'pc197461',	NULL,	'files/prilohy/PC197461.JPG',	'files/prilohy/tb_PC197461.JPG',	'2020-03-23 18:19:33',	1,	2,	0),
(144,	28,	2,	0,	'#I-144#',	'PC217515',	'JPG',	'pc217515',	NULL,	'files/prilohy/PC217515.JPG',	'files/prilohy/tb_PC217515.JPG',	'2020-03-23 18:19:33',	1,	2,	0),
(145,	28,	2,	0,	'#I-145#',	'PC237633',	'JPG',	'pc237633',	NULL,	'files/prilohy/PC237633.JPG',	'files/prilohy/tb_PC237633.JPG',	'2020-03-23 18:19:33',	1,	2,	0),
(146,	28,	2,	0,	'#I-146#',	'PC237650',	'JPG',	'pc237650',	NULL,	'files/prilohy/PC237650.JPG',	'files/prilohy/tb_PC237650.JPG',	'2020-03-23 18:19:33',	1,	2,	0),
(147,	29,	2,	0,	'#I-147#',	'D-DR-002',	'jpg',	'd-dr-002',	NULL,	'files/prilohy/D-DR-002.jpg',	'files/prilohy/tb_D-DR-002.jpg',	'2020-03-24 01:01:20',	1,	2,	0),
(148,	29,	2,	0,	'#I-148#',	'D-DR-003',	'jpg',	'd-dr-003',	NULL,	'files/prilohy/D-DR-003.jpg',	'files/prilohy/tb_D-DR-003.jpg',	'2020-03-24 01:01:20',	1,	2,	0),
(149,	29,	2,	0,	'#I-149#',	'D-DR-004',	'jpg',	'd-dr-004',	NULL,	'files/prilohy/D-DR-004.jpg',	'files/prilohy/tb_D-DR-004.jpg',	'2020-03-24 01:01:20',	1,	2,	0),
(150,	29,	2,	0,	'#I-150#',	'D-DR-005',	'jpg',	'd-dr-005',	NULL,	'files/prilohy/D-DR-005.jpg',	'files/prilohy/tb_D-DR-005.jpg',	'2020-03-24 01:01:20',	1,	2,	0),
(151,	29,	2,	0,	'#I-151#',	'D-DR-006',	'jpg',	'd-dr-006',	NULL,	'files/prilohy/D-DR-006.jpg',	'files/prilohy/tb_D-DR-006.jpg',	'2020-03-24 01:01:20',	1,	2,	0),
(152,	29,	2,	0,	'#I-152#',	'D-DR-007',	'jpg',	'd-dr-007',	NULL,	'files/prilohy/D-DR-007.jpg',	'files/prilohy/tb_D-DR-007.jpg',	'2020-03-24 01:01:20',	1,	2,	0),
(153,	29,	2,	0,	'#I-153#',	'D-DR-008',	'jpg',	'd-dr-008',	NULL,	'files/prilohy/D-DR-008.jpg',	'files/prilohy/tb_D-DR-008.jpg',	'2020-03-24 01:01:20',	1,	2,	0),
(154,	29,	2,	0,	'#I-154#',	'D-DR-009',	'jpg',	'd-dr-009',	NULL,	'files/prilohy/D-DR-009.jpg',	'files/prilohy/tb_D-DR-009.jpg',	'2020-03-24 01:01:20',	1,	2,	0),
(155,	29,	2,	0,	'#I-155#',	'D-DR-010',	'jpg',	'd-dr-010',	NULL,	'files/prilohy/D-DR-010.jpg',	'files/prilohy/tb_D-DR-010.jpg',	'2020-03-24 01:01:20',	1,	2,	0),
(156,	29,	2,	0,	'#I-156#',	'D-DR-011',	'jpg',	'd-dr-011',	NULL,	'files/prilohy/D-DR-011.jpg',	'files/prilohy/tb_D-DR-011.jpg',	'2020-03-24 01:01:20',	1,	2,	0),
(157,	29,	2,	0,	'#I-157#',	'D-DR-012',	'jpg',	'd-dr-012',	NULL,	'files/prilohy/D-DR-012.jpg',	'files/prilohy/tb_D-DR-012.jpg',	'2020-03-24 01:01:20',	1,	2,	0),
(158,	29,	2,	0,	'#I-158#',	'D-DR-013',	'jpg',	'd-dr-013',	NULL,	'files/prilohy/D-DR-013.jpg',	'files/prilohy/tb_D-DR-013.jpg',	'2020-03-24 01:01:20',	1,	2,	0),
(159,	29,	2,	0,	'#I-159#',	'D-DR-014',	'jpg',	'd-dr-014',	NULL,	'files/prilohy/D-DR-014.jpg',	'files/prilohy/tb_D-DR-014.jpg',	'2020-03-24 01:01:20',	1,	2,	0),
(160,	29,	2,	0,	'#I-160#',	'D-DR-015',	'jpg',	'd-dr-015',	NULL,	'files/prilohy/D-DR-015.jpg',	'files/prilohy/tb_D-DR-015.jpg',	'2020-03-24 01:01:20',	1,	2,	0),
(161,	29,	2,	0,	'#I-161#',	'D-DR-016',	'jpg',	'd-dr-016',	NULL,	'files/prilohy/D-DR-016.jpg',	'files/prilohy/tb_D-DR-016.jpg',	'2020-03-24 01:01:20',	1,	2,	0),
(162,	29,	2,	0,	'#I-162#',	'D-DR-017',	'jpg',	'd-dr-017',	NULL,	'files/prilohy/D-DR-017.jpg',	'files/prilohy/tb_D-DR-017.jpg',	'2020-03-24 01:01:20',	1,	2,	0),
(163,	29,	2,	0,	'#I-163#',	'D-DR-018',	'jpg',	'd-dr-018',	NULL,	'files/prilohy/D-DR-018.jpg',	'files/prilohy/tb_D-DR-018.jpg',	'2020-03-24 01:01:20',	1,	2,	0),
(164,	29,	2,	0,	'#I-164#',	'D-DR-019',	'jpg',	'd-dr-019',	NULL,	'files/prilohy/D-DR-019.jpg',	'files/prilohy/tb_D-DR-019.jpg',	'2020-03-24 01:01:20',	1,	2,	0),
(165,	29,	2,	0,	'#I-165#',	'D-DR-020',	'jpg',	'd-dr-020',	NULL,	'files/prilohy/D-DR-020.jpg',	'files/prilohy/tb_D-DR-020.jpg',	'2020-03-24 01:01:20',	1,	2,	0),
(166,	29,	2,	0,	'#I-166#',	'D-DR-021',	'jpg',	'd-dr-021',	NULL,	'files/prilohy/D-DR-021.jpg',	'files/prilohy/tb_D-DR-021.jpg',	'2020-03-24 01:01:20',	1,	2,	0),
(167,	29,	2,	0,	'#I-167#',	'D-DR-025',	'jpg',	'd-dr-025',	NULL,	'files/prilohy/D-DR-025.jpg',	'files/prilohy/tb_D-DR-025.jpg',	'2020-03-24 01:03:24',	1,	2,	0),
(168,	29,	2,	0,	'#I-168#',	'D-DR-026',	'jpg',	'd-dr-026',	NULL,	'files/prilohy/D-DR-026.jpg',	'files/prilohy/tb_D-DR-026.jpg',	'2020-03-24 01:03:24',	1,	2,	0),
(169,	30,	2,	0,	'#I-169#',	'D-DR-001',	'jpg',	'd-dr-001',	NULL,	'files/prilohy/D-DR-001.jpg',	'files/prilohy/tb_D-DR-001.jpg',	'2020-03-24 22:29:41',	1,	2,	0),
(170,	30,	2,	0,	'#I-170#',	'D-DR-0021',	'jpg',	'd-dr-0021',	NULL,	'files/prilohy/D-DR-0021.jpg',	'files/prilohy/tb_D-DR-0021.jpg',	'2020-03-24 22:29:41',	1,	2,	0),
(171,	30,	2,	0,	'#I-171#',	'D-DR-0031',	'jpg',	'd-dr-0031',	NULL,	'files/prilohy/D-DR-0031.jpg',	'files/prilohy/tb_D-DR-0031.jpg',	'2020-03-24 22:29:41',	1,	2,	0),
(172,	30,	2,	0,	'#I-172#',	'D-DR-0041',	'jpg',	'd-dr-0041',	NULL,	'files/prilohy/D-DR-0041.jpg',	'files/prilohy/tb_D-DR-0041.jpg',	'2020-03-24 22:29:41',	1,	2,	0),
(173,	30,	2,	0,	'#I-173#',	'D-DR-0051',	'jpg',	'd-dr-0051',	NULL,	'files/prilohy/D-DR-0051.jpg',	'files/prilohy/tb_D-DR-0051.jpg',	'2020-03-24 22:29:41',	1,	2,	0),
(174,	30,	2,	0,	'#I-174#',	'D-DR-0061',	'jpg',	'd-dr-0061',	NULL,	'files/prilohy/D-DR-0061.jpg',	'files/prilohy/tb_D-DR-0061.jpg',	'2020-03-24 22:29:41',	1,	2,	0),
(175,	30,	2,	0,	'#I-175#',	'D-DR-0071',	'jpg',	'd-dr-0071',	NULL,	'files/prilohy/D-DR-0071.jpg',	'files/prilohy/tb_D-DR-0071.jpg',	'2020-03-24 22:29:41',	1,	2,	0),
(176,	30,	2,	0,	'#I-176#',	'D-DR-0081',	'jpg',	'd-dr-0081',	NULL,	'files/prilohy/D-DR-0081.jpg',	'files/prilohy/tb_D-DR-0081.jpg',	'2020-03-24 22:29:41',	1,	2,	0),
(177,	30,	2,	0,	'#I-177#',	'D-DR-0091',	'jpg',	'd-dr-0091',	NULL,	'files/prilohy/D-DR-0091.jpg',	'files/prilohy/tb_D-DR-0091.jpg',	'2020-03-24 22:29:41',	1,	2,	0),
(178,	30,	2,	0,	'#I-178#',	'D-DR-0101',	'jpg',	'd-dr-0101',	NULL,	'files/prilohy/D-DR-0101.jpg',	'files/prilohy/tb_D-DR-0101.jpg',	'2020-03-24 22:29:41',	1,	2,	0),
(179,	30,	2,	0,	'#I-179#',	'D-DR-0111',	'jpg',	'd-dr-0111',	NULL,	'files/prilohy/D-DR-0111.jpg',	'files/prilohy/tb_D-DR-0111.jpg',	'2020-03-24 22:29:41',	1,	2,	0),
(180,	30,	2,	0,	'#I-180#',	'D-DR-0121',	'jpg',	'd-dr-0121',	NULL,	'files/prilohy/D-DR-0121.jpg',	'files/prilohy/tb_D-DR-0121.jpg',	'2020-03-24 22:29:41',	1,	2,	0),
(181,	30,	2,	0,	'#I-181#',	'D-DR-0131',	'jpg',	'd-dr-0131',	NULL,	'files/prilohy/D-DR-0131.jpg',	'files/prilohy/tb_D-DR-0131.jpg',	'2020-03-24 22:29:41',	1,	2,	0),
(182,	30,	2,	0,	'#I-182#',	'D-DR-0141',	'jpg',	'd-dr-0141',	NULL,	'files/prilohy/D-DR-0141.jpg',	'files/prilohy/tb_D-DR-0141.jpg',	'2020-03-24 22:29:41',	1,	2,	0),
(183,	30,	2,	0,	'#I-183#',	'D-DR-0151',	'jpg',	'd-dr-0151',	NULL,	'files/prilohy/D-DR-0151.jpg',	'files/prilohy/tb_D-DR-0151.jpg',	'2020-03-24 22:29:41',	1,	2,	0),
(184,	30,	2,	0,	'#I-184#',	'D-DR-0161',	'jpg',	'd-dr-0161',	NULL,	'files/prilohy/D-DR-0161.jpg',	'files/prilohy/tb_D-DR-0161.jpg',	'2020-03-24 22:29:41',	1,	2,	0),
(185,	30,	2,	0,	'#I-185#',	'D-DR-0171',	'jpg',	'd-dr-0171',	NULL,	'files/prilohy/D-DR-0171.jpg',	'files/prilohy/tb_D-DR-0171.jpg',	'2020-03-24 22:29:41',	1,	2,	0),
(186,	30,	2,	0,	'#I-186#',	'D-DR-0181',	'jpg',	'd-dr-0181',	NULL,	'files/prilohy/D-DR-0181.jpg',	'files/prilohy/tb_D-DR-0181.jpg',	'2020-03-24 22:29:41',	1,	2,	0),
(187,	30,	2,	0,	'#I-187#',	'D-DR-0191',	'jpg',	'd-dr-0191',	NULL,	'files/prilohy/D-DR-0191.jpg',	'files/prilohy/tb_D-DR-0191.jpg',	'2020-03-24 22:29:41',	1,	2,	0),
(188,	30,	2,	0,	'#I-188#',	'D-DR-0201',	'jpg',	'd-dr-0201',	NULL,	'files/prilohy/D-DR-0201.jpg',	'files/prilohy/tb_D-DR-0201.jpg',	'2020-03-24 22:29:41',	1,	2,	0),
(189,	30,	2,	0,	'#I-189#',	'D-DR-0211',	'jpg',	'd-dr-0211',	NULL,	'files/prilohy/D-DR-0211.jpg',	'files/prilohy/tb_D-DR-0211.jpg',	'2020-03-24 22:33:10',	1,	2,	0),
(190,	30,	2,	0,	'#I-190#',	'D-DR-022',	'jpg',	'd-dr-022',	NULL,	'files/prilohy/D-DR-022.jpg',	'files/prilohy/tb_D-DR-022.jpg',	'2020-03-24 22:33:10',	1,	2,	0),
(191,	30,	2,	0,	'#I-191#',	'D-DR-023',	'jpg',	'd-dr-023',	NULL,	'files/prilohy/D-DR-023.jpg',	'files/prilohy/tb_D-DR-023.jpg',	'2020-03-24 22:33:10',	1,	2,	0),
(192,	30,	2,	0,	'#I-192#',	'D-DR-024',	'jpg',	'd-dr-024',	NULL,	'files/prilohy/D-DR-024.jpg',	'files/prilohy/tb_D-DR-024.jpg',	'2020-03-24 22:33:10',	1,	2,	0),
(193,	30,	2,	0,	'#I-193#',	'D-DR-0251',	'jpg',	'd-dr-0251',	NULL,	'files/prilohy/D-DR-0251.jpg',	'files/prilohy/tb_D-DR-0251.jpg',	'2020-03-24 22:33:10',	1,	2,	0),
(194,	30,	2,	0,	'#I-194#',	'D-DR-0261',	'jpg',	'd-dr-0261',	NULL,	'files/prilohy/D-DR-0261.jpg',	'files/prilohy/tb_D-DR-0261.jpg',	'2020-03-24 22:33:10',	1,	2,	0);

DROP TABLE IF EXISTS `druh`;
CREATE TABLE `druh` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Id položiek',
  `druh` varchar(20) COLLATE utf8_bin NOT NULL COMMENT 'Názov druhu stredného stĺpca',
  `modul` varchar(20) COLLATE utf8_bin DEFAULT NULL COMMENT 'Názov špecifického modulu ak NULL vždy',
  `presenter` varchar(30) COLLATE utf8_bin NOT NULL COMMENT 'Názov prezenteru pre Nette',
  `popis` varchar(255) COLLATE utf8_bin DEFAULT 'Popis' COMMENT 'Popis bloku',
  `povolene` tinyint(4) NOT NULL DEFAULT 1 COMMENT 'Ak 1 tak daná položka je povolená',
  `je_spec_naz` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Ak 1 tak daný druh potrebuje špecif. názov',
  `robots` tinyint(4) NOT NULL DEFAULT 1 COMMENT 'Ak 1 tak je povolené indexovanie daného druhu',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `druh` (`id`, `druh`, `modul`, `presenter`, `popis`, `povolene`, `je_spec_naz`, `robots`) VALUES
(1,	'clanky',	NULL,	'Clanky',	'Články - Stredná časť je ako článok, alebo je sub-menu',	1,	1,	1),
(3,	'menupol',	NULL,	'Menu',	'Položka menu - nerobí nič, len zobrazí všetky položky, ktoré sú v nej zaradené',	1,	1,	1),
(5,	'oznam',	NULL,	'Oznam',	'Vypísanie oznamov',	0,	0,	1),
(7,	'dokumenty',	NULL,	'Dokumenty',	'Vkladanie dokumentov do stránky',	0,	0,	0),
(8,	'products',	NULL,	'Products',	'Produkty',	1,	0,	0);

DROP TABLE IF EXISTS `faktury`;
CREATE TABLE `faktury` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_hlavne_menu` int(11) NOT NULL DEFAULT 1 COMMENT 'Ku ktorej položke patrí',
  `nazov` varchar(50) COLLATE utf8_bin DEFAULT NULL COMMENT 'Názov titulku pre daný dokument',
  `cislo` varchar(20) COLLATE utf8_bin DEFAULT NULL COMMENT 'Číslo faktúry, zmluvy, objednávky',
  `predmet` varchar(200) COLLATE utf8_bin DEFAULT NULL COMMENT 'Predmet faktúry, zmluvy, objednávky',
  `cena` float(15,2) DEFAULT NULL COMMENT 'Cena faktúry, zmluvy, objednávky',
  `subjekt` varchar(200) COLLATE utf8_bin DEFAULT NULL COMMENT 'Subjekt faktúry,  objednávky - (dodávateľ), zmluvy(Zmluvná strana)',
  `datum_vystavenia` date DEFAULT NULL COMMENT 'Dátum vystavenia pri faktúre a objednávke pri zmluve dátum uzatvorenia',
  `datum_ukoncenia` date DEFAULT NULL COMMENT 'Dátum ukoncenia zmluvy',
  `subor` varchar(50) COLLATE utf8_bin NOT NULL COMMENT 'Názov súboru s relatívnou cestou',
  `id_user_main` int(11) NOT NULL DEFAULT 1 COMMENT 'Kto pridal dokument',
  `id_reg` int(11) NOT NULL DEFAULT 0 COMMENT 'Úroveň registrácie',
  `kedy` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Dátum uloženia alebo opravy - časová pečiatka',
  `pocitadlo` int(11) NOT NULL DEFAULT 0 COMMENT 'Počítadlo stiahnutí',
  `id_skupina` int(11) NOT NULL DEFAULT 0 COMMENT 'Id článku do ktorej časti dokument patrí',
  `id_rok` int(11) NOT NULL DEFAULT 0 COMMENT 'Id roku do ktorého sa má zaradiť',
  PRIMARY KEY (`id`),
  KEY `id_hlavne_menu` (`id_hlavne_menu`),
  KEY `id_user_profiles` (`id_user_main`),
  CONSTRAINT `faktury_ibfk_1` FOREIGN KEY (`id_hlavne_menu`) REFERENCES `hlavne_menu` (`id`),
  CONSTRAINT `faktury_ibfk_3` FOREIGN KEY (`id_user_main`) REFERENCES `user_main` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS `hlavicka`;
CREATE TABLE `hlavicka` (
  `id` int(11) NOT NULL COMMENT '[A]Index',
  `nazov` varchar(40) COLLATE utf8_bin NOT NULL DEFAULT 'Veľká' COMMENT 'Zobrazený názov pre daný typ hlavičky',
  `pripona` varchar(10) COLLATE utf8_bin DEFAULT NULL COMMENT 'Prípona názvu súborov',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `hlavicka` (`id`, `nazov`, `pripona`) VALUES
(0,	'Nerozhoduje',	' '),
(1,	'Veľká',	'normal'),
(2,	'Malá',	'small'),
(3,	'Veľká bez ponuky podčlánkov',	'normal'),
(4,	'Malá bez ponuky podčlánkov',	NULL);

DROP TABLE IF EXISTS `hlavne_menu`;
CREATE TABLE `hlavne_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[5]Id položky hlavného menu',
  `spec_nazov` varchar(100) COLLATE utf8_bin DEFAULT NULL COMMENT 'Názov položky menu pre URL',
  `id_hlavne_menu_cast` int(11) NOT NULL DEFAULT 1 COMMENT '[5]Ku ktorej časti hl. menu patrí položka',
  `id_user_roles` int(11) NOT NULL DEFAULT 0 COMMENT 'Id min úrovne registrácie pre zobrazenie',
  `id_ikonka` int(11) DEFAULT NULL COMMENT '[4]Názov súboru ikonky aj s koncovkou',
  `id_druh` int(11) NOT NULL DEFAULT 1 COMMENT '[5]Výber druhu priradenej položky. Ak 1 tak je možné priradiť článok v náväznosti na tab. druh',
  `uroven` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Úroveň položky menu',
  `id_nadradenej` int(11) DEFAULT NULL COMMENT 'Id nadradenej položky menu z tejto tabuľky ',
  `id_user_main` int(11) NOT NULL DEFAULT 1 COMMENT 'Id užívateľa',
  `poradie` int(11) NOT NULL DEFAULT 1 COMMENT 'Poradie v zobrazení',
  `poradie_podclankov` int(11) NOT NULL DEFAULT 0 COMMENT 'Poradie podčlánkov ak sú: 0 - od 1-9, 1 - od 9-1',
  `id_hlavicka` int(11) NOT NULL DEFAULT 0 COMMENT '[5]Druh hlavičky podľa tabuľky hlavicka. 1 - velka',
  `id_hlavne_menu_opravnenie` int(11) NOT NULL DEFAULT 0 COMMENT 'Povolenie pre nevlastníkov (0-žiadne,1- podčlánky,2-editacia,4-všetko)',
  `zvyrazni` tinyint(4) NOT NULL DEFAULT 0 COMMENT '[5]Zvýraznenie položky menu pri pridaní obsahu',
  `pocitadlo` int(11) NOT NULL DEFAULT 0 COMMENT '[R]Počítadlo kliknutí na položku',
  `nazov_ul_sub` varchar(20) COLLATE utf8_bin DEFAULT NULL COMMENT '[5]Názov pomocnej triedy ul-elsementu sub menu',
  `id_hlavne_menu_template` int(11) NOT NULL DEFAULT 1 COMMENT 'Vzhľad šablóny',
  `absolutna` varchar(100) COLLATE utf8_bin DEFAULT NULL COMMENT 'Absolútna adresa',
  `ikonka` varchar(30) COLLATE utf8_bin DEFAULT NULL COMMENT 'Názov css ikonky',
  `avatar` varchar(300) COLLATE utf8_bin DEFAULT NULL COMMENT 'Názov a cesta k titulnému obrázku',
  `komentar` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Povolenie komentárov',
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Posledná zmena',
  `datum_platnosti` date DEFAULT NULL COMMENT 'Platnosť',
  `aktualny_projekt` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Označenie aktuálneho projektu',
  `redirect_id` int(11) DEFAULT NULL COMMENT 'Id článku na ktorý sa má presmerovať',
  `id_dlzka_novinky` int(11) NOT NULL DEFAULT 1 COMMENT 'Do kedy je to novinka',
  `border_a` varchar(15) COLLATE utf8_bin DEFAULT NULL COMMENT 'Okraj A: ffffff|hhh f-farba h-hrúbka',
  `border_b` varchar(15) COLLATE utf8_bin DEFAULT NULL COMMENT 'Okraj B: ffffff|hhh f-farba h-hrúbka',
  `border_c` varchar(15) COLLATE utf8_bin DEFAULT NULL COMMENT 'Okraj C: ffffff|hhh f-farba h-hrúbka',
  `id_user_categories` int(11) DEFAULT NULL COMMENT 'Opravnenie podľa kategórie',
  PRIMARY KEY (`id`),
  KEY `id_reg` (`id_user_roles`),
  KEY `druh` (`id_druh`),
  KEY `id_ikonka` (`id_ikonka`),
  KEY `id_hlavicka` (`id_hlavicka`),
  KEY `id_hlavne_menu_cast` (`id_hlavne_menu_cast`),
  KEY `id_user_profiles` (`id_user_main`),
  KEY `id_dlzka_novinky` (`id_dlzka_novinky`),
  KEY `id_hlavne_menu_opravnenie` (`id_hlavne_menu_opravnenie`),
  KEY `id_hlavne_menu_template` (`id_hlavne_menu_template`),
  KEY `id_user_categories` (`id_user_categories`),
  CONSTRAINT `hlavne_menu_ibfk_10` FOREIGN KEY (`id_user_roles`) REFERENCES `user_roles` (`id`),
  CONSTRAINT `hlavne_menu_ibfk_11` FOREIGN KEY (`id_hlavne_menu_opravnenie`) REFERENCES `hlavne_menu_opravnenie` (`id`),
  CONSTRAINT `hlavne_menu_ibfk_12` FOREIGN KEY (`id_hlavne_menu_template`) REFERENCES `hlavne_menu_template` (`id`),
  CONSTRAINT `hlavne_menu_ibfk_13` FOREIGN KEY (`id_user_categories`) REFERENCES `user_categories` (`id`),
  CONSTRAINT `hlavne_menu_ibfk_2` FOREIGN KEY (`id_ikonka`) REFERENCES `ikonka` (`id`),
  CONSTRAINT `hlavne_menu_ibfk_4` FOREIGN KEY (`id_hlavicka`) REFERENCES `hlavicka` (`id`),
  CONSTRAINT `hlavne_menu_ibfk_5` FOREIGN KEY (`id_hlavne_menu_cast`) REFERENCES `hlavne_menu_cast` (`id`),
  CONSTRAINT `hlavne_menu_ibfk_6` FOREIGN KEY (`id_druh`) REFERENCES `druh` (`id`),
  CONSTRAINT `hlavne_menu_ibfk_8` FOREIGN KEY (`id_dlzka_novinky`) REFERENCES `dlzka_novinky` (`id`),
  CONSTRAINT `hlavne_menu_ibfk_9` FOREIGN KEY (`id_user_main`) REFERENCES `user_main` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Položky hlavného menu';

INSERT INTO `hlavne_menu` (`id`, `spec_nazov`, `id_hlavne_menu_cast`, `id_user_roles`, `id_ikonka`, `id_druh`, `uroven`, `id_nadradenej`, `id_user_main`, `poradie`, `poradie_podclankov`, `id_hlavicka`, `id_hlavne_menu_opravnenie`, `zvyrazni`, `pocitadlo`, `nazov_ul_sub`, `id_hlavne_menu_template`, `absolutna`, `ikonka`, `avatar`, `komentar`, `modified`, `datum_platnosti`, `aktualny_projekt`, `redirect_id`, `id_dlzka_novinky`, `border_a`, `border_b`, `border_c`, `id_user_categories`) VALUES
(1,	'bw-fotografia',	1,	0,	NULL,	1,	0,	NULL,	2,	1,	1,	1,	0,	0,	0,	NULL,	3,	NULL,	NULL,	'zqqzqharq0m1h9j.jpg',	0,	'2020-03-24 10:45:22',	NULL,	0,	NULL,	1,	NULL,	NULL,	NULL,	NULL),
(2,	'architekura',	1,	0,	NULL,	1,	0,	NULL,	2,	3,	1,	1,	0,	0,	0,	NULL,	3,	NULL,	NULL,	'93eca27ct6k1szj.jpg',	0,	'2020-03-22 00:33:31',	NULL,	0,	NULL,	1,	NULL,	NULL,	NULL,	NULL),
(3,	'foto-rgb',	1,	0,	NULL,	1,	0,	NULL,	2,	5,	0,	1,	0,	0,	0,	NULL,	3,	NULL,	NULL,	'jlhbvumzttwtah7.jpg',	0,	'2017-11-03 07:57:10',	NULL,	0,	NULL,	1,	NULL,	NULL,	NULL,	NULL),
(4,	'produkty',	1,	0,	NULL,	1,	0,	NULL,	2,	2,	0,	2,	0,	0,	0,	NULL,	1,	NULL,	NULL,	'5663at38ptotbps.png',	0,	'2017-11-03 07:50:20',	NULL,	0,	NULL,	1,	NULL,	NULL,	NULL,	NULL),
(5,	'novinky',	1,	0,	NULL,	1,	0,	NULL,	2,	4,	0,	2,	0,	0,	0,	NULL,	1,	NULL,	NULL,	'adpkihqqxy4pihf.png',	0,	'2017-11-03 07:49:47',	NULL,	0,	NULL,	1,	NULL,	NULL,	NULL,	NULL),
(6,	'vystavy',	1,	0,	NULL,	1,	0,	NULL,	2,	6,	0,	2,	0,	0,	0,	NULL,	1,	NULL,	NULL,	's5nqbzkyc7tp40x.png',	0,	'2020-03-26 08:40:26',	NULL,	0,	NULL,	1,	NULL,	NULL,	NULL,	NULL),
(9,	'cast-a-1',	1,	0,	NULL,	1,	1,	2,	2,	1,	0,	1,	0,	0,	0,	NULL,	2,	NULL,	NULL,	'3zjqsfzl5baff44.jpg',	0,	'2017-10-05 05:42:11',	NULL,	0,	NULL,	1,	NULL,	NULL,	NULL,	NULL),
(10,	'cast-a-2',	1,	0,	NULL,	1,	1,	2,	2,	2,	0,	1,	0,	0,	0,	NULL,	2,	NULL,	NULL,	'qejapifs8ada1do.JPG',	0,	'2017-10-05 05:42:11',	NULL,	0,	NULL,	1,	NULL,	NULL,	NULL,	NULL),
(11,	'obchodne-podmienky',	2,	0,	NULL,	1,	0,	NULL,	2,	1,	0,	0,	0,	0,	0,	NULL,	1,	NULL,	NULL,	NULL,	0,	'2017-10-05 05:54:09',	NULL,	0,	NULL,	1,	NULL,	NULL,	NULL,	NULL),
(12,	'kontakt',	2,	0,	NULL,	1,	0,	NULL,	2,	2,	0,	0,	0,	0,	0,	NULL,	1,	NULL,	NULL,	'x8jx3wr9rxs47fc.jpg',	0,	'2018-07-19 09:19:35',	NULL,	0,	NULL,	1,	NULL,	NULL,	NULL,	NULL),
(13,	'kalibracia',	2,	0,	NULL,	1,	0,	NULL,	2,	3,	0,	0,	0,	0,	0,	NULL,	1,	NULL,	NULL,	NULL,	0,	'2017-10-05 06:14:20',	NULL,	0,	NULL,	1,	NULL,	NULL,	NULL,	NULL),
(14,	'homepage',	3,	0,	NULL,	1,	0,	NULL,	2,	1,	0,	0,	0,	0,	0,	NULL,	4,	NULL,	NULL,	NULL,	0,	'2017-11-23 11:49:33',	NULL,	0,	NULL,	1,	NULL,	NULL,	NULL,	NULL),
(15,	'homepage-dole',	3,	0,	NULL,	1,	0,	NULL,	2,	2,	0,	0,	0,	0,	0,	NULL,	5,	NULL,	NULL,	NULL,	0,	'2017-11-23 11:49:33',	NULL,	0,	NULL,	1,	NULL,	NULL,	NULL,	NULL),
(16,	'cast-a-3',	1,	0,	NULL,	1,	1,	2,	2,	3,	0,	1,	0,	0,	0,	NULL,	2,	NULL,	NULL,	'kp0x78f0j5xot9x.jpg',	0,	'2017-11-23 11:49:33',	NULL,	0,	NULL,	1,	NULL,	NULL,	NULL,	NULL),
(19,	'cast-a-1-1',	1,	0,	NULL,	1,	2,	9,	2,	1,	0,	1,	0,	0,	0,	NULL,	2,	NULL,	NULL,	NULL,	0,	'2017-11-23 11:49:33',	NULL,	0,	NULL,	1,	NULL,	NULL,	NULL,	NULL),
(20,	'krajina',	1,	0,	NULL,	1,	1,	3,	2,	1,	0,	1,	0,	0,	0,	NULL,	3,	NULL,	NULL,	'yb8ebcruulh8d50.jpg',	0,	'2020-03-24 00:12:45',	NULL,	0,	NULL,	1,	NULL,	NULL,	NULL,	NULL),
(21,	'kostoly',	1,	0,	NULL,	1,	1,	3,	2,	2,	0,	1,	0,	0,	0,	NULL,	2,	NULL,	NULL,	'hi0a9wovlqrbgdv.JPG',	0,	'2017-11-23 11:49:33',	NULL,	0,	NULL,	1,	NULL,	NULL,	NULL,	NULL),
(22,	'stromy',	1,	0,	NULL,	1,	1,	3,	2,	3,	0,	1,	0,	0,	0,	NULL,	2,	NULL,	NULL,	'lebld9nagwmbdn3.JPG',	0,	'2020-03-25 10:29:24',	NULL,	0,	NULL,	1,	'#80ff00|3',	'#454545|5',	'#dfd95b|2',	NULL),
(23,	'budovy',	1,	0,	NULL,	1,	1,	3,	2,	4,	0,	1,	0,	0,	0,	NULL,	2,	NULL,	NULL,	'rubrr55ww87q8z2.JPG',	0,	'2017-12-27 11:24:02',	NULL,	0,	NULL,	1,	'#525252|1',	'#5063e4|2',	'#9b9b00|3',	NULL),
(24,	'protisvetlo',	1,	0,	NULL,	1,	1,	3,	2,	5,	0,	1,	0,	0,	0,	NULL,	2,	NULL,	NULL,	'9e1gm84fckjtsos.JPG',	0,	'2017-11-23 11:49:33',	NULL,	0,	NULL,	1,	NULL,	NULL,	NULL,	NULL),
(26,	'cast-a-4',	1,	0,	NULL,	1,	1,	2,	2,	4,	0,	0,	0,	0,	0,	NULL,	2,	NULL,	NULL,	'i2ipr8grdovqlll.jpg',	0,	'2020-03-23 12:41:45',	NULL,	0,	NULL,	1,	NULL,	NULL,	NULL,	NULL),
(27,	'a-gggddd',	1,	0,	NULL,	1,	1,	2,	2,	5,	0,	0,	0,	0,	0,	NULL,	1,	NULL,	NULL,	NULL,	0,	'2020-03-23 12:49:59',	NULL,	0,	NULL,	1,	NULL,	NULL,	NULL,	NULL),
(28,	'test-male-rozlisenie',	1,	0,	NULL,	1,	2,	20,	2,	1,	0,	0,	0,	0,	0,	NULL,	2,	NULL,	NULL,	'c3aj8pi5751a260.JPG',	0,	'2020-03-23 17:11:36',	NULL,	0,	NULL,	1,	NULL,	NULL,	NULL,	NULL),
(29,	'test-bw-2',	1,	0,	NULL,	1,	1,	1,	2,	2,	0,	0,	0,	0,	0,	NULL,	2,	NULL,	NULL,	'5djsadnnsinbj0q.jpg',	0,	'2020-04-02 15:52:24',	NULL,	0,	NULL,	1,	'#000000|5',	'#dddddd|20',	'#808080|5',	NULL),
(30,	'test-bw-ramceky-1',	1,	0,	NULL,	1,	1,	1,	2,	3,	0,	0,	0,	0,	0,	NULL,	2,	NULL,	NULL,	NULL,	0,	'2020-03-24 21:25:57',	NULL,	0,	NULL,	1,	NULL,	NULL,	NULL,	NULL);

DROP TABLE IF EXISTS `hlavne_menu_cast`;
CREATE TABLE `hlavne_menu_cast` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `view_name` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT 'Časť' COMMENT 'Názov časti',
  `id_user_roles` int(11) NOT NULL DEFAULT 5 COMMENT 'Id min úrovne registrácie pre editáciu',
  `mapa_stranky` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Ak 1 tak je časť zahrnutá do mapy',
  PRIMARY KEY (`id`),
  KEY `id_registracia` (`id_user_roles`),
  CONSTRAINT `hlavne_menu_cast_ibfk_2` FOREIGN KEY (`id_user_roles`) REFERENCES `user_roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Časti hlavného menu';

INSERT INTO `hlavne_menu_cast` (`id`, `view_name`, `id_user_roles`, `mapa_stranky`) VALUES
(1,	'Hlavná ponuka',	4,	1),
(2,	'Druhá časť',	4,	1),
(3,	'Home',	5,	0);

DROP TABLE IF EXISTS `hlavne_menu_lang`;
CREATE TABLE `hlavne_menu_lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `id_lang` int(11) NOT NULL DEFAULT 1 COMMENT 'Id Jazyka',
  `id_hlavne_menu` int(11) NOT NULL COMMENT 'Id hlavného menu, ku ktorému patrí',
  `id_clanok_lang` int(11) DEFAULT NULL COMMENT 'Id jazka článku ak ho má',
  `menu_name` varchar(100) COLLATE utf8_bin DEFAULT NULL COMMENT 'Názov položky v hlavnom menu pre daný jazyk',
  `h1part2` varchar(100) COLLATE utf8_bin DEFAULT NULL COMMENT 'Druhá časť názvu pre daný jazyk',
  `view_name` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT 'Zobrazený názov položky pre daný jazyk',
  PRIMARY KEY (`id`),
  KEY `id_hlavne_menu` (`id_hlavne_menu`),
  KEY `id_lang` (`id_lang`),
  KEY `id_clanok_lang` (`id_clanok_lang`),
  CONSTRAINT `hlavne_menu_lang_ibfk_1` FOREIGN KEY (`id_hlavne_menu`) REFERENCES `hlavne_menu` (`id`),
  CONSTRAINT `hlavne_menu_lang_ibfk_2` FOREIGN KEY (`id_lang`) REFERENCES `lang` (`id`),
  CONSTRAINT `hlavne_menu_lang_ibfk_3` FOREIGN KEY (`id_clanok_lang`) REFERENCES `clanok_lang` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Popis položiek hlavného menu pre iný jazyk';

INSERT INTO `hlavne_menu_lang` (`id`, `id_lang`, `id_hlavne_menu`, `id_clanok_lang`, `menu_name`, `h1part2`, `view_name`) VALUES
(1,	1,	1,	1,	'Čiernobiela fotografia',	NULL,	'Čiernobiela fotografia'),
(2,	1,	2,	2,	'Architektúra',	NULL,	'Architektúra'),
(3,	1,	3,	3,	'Farebná fotografia',	NULL,	'Farebná fotografia'),
(4,	1,	4,	4,	'Produkty',	NULL,	'Produkty'),
(5,	1,	5,	5,	'Novinky',	NULL,	'Novinky'),
(6,	1,	6,	6,	'Výstavy',	NULL,	'Výstavy'),
(9,	1,	9,	9,	'Časť A-1',	NULL,	'Časť A-1'),
(10,	1,	10,	10,	'Časť A-2',	NULL,	'Časť A-2'),
(11,	1,	11,	11,	'Obchodné podmienky',	NULL,	'Naše obchodné podmienky'),
(12,	1,	12,	12,	'Kontakt',	NULL,	'Kontakt'),
(13,	1,	13,	13,	'Kalibrácia',	NULL,	'Kalibrácia'),
(14,	1,	14,	14,	'Homepage hore',	NULL,	'Ateliér architektúry a fotografie'),
(15,	1,	15,	15,	'Homepage dole',	NULL,	'Popis dole'),
(16,	1,	16,	16,	'Časť A-3',	NULL,	'Časť A-3'),
(19,	1,	19,	19,	'Časť A-1-1',	NULL,	'Časť A-1-1'),
(20,	1,	20,	20,	'Krajina',	NULL,	'Krajina'),
(21,	1,	21,	21,	'Kostoly',	NULL,	'Kostoly'),
(22,	1,	22,	22,	'Stromy',	NULL,	'Stromy'),
(23,	1,	23,	23,	'Budovy',	NULL,	'Budovy'),
(24,	1,	24,	24,	'Protisvetlo',	NULL,	'Protisvetlo'),
(26,	1,	26,	25,	'TEST 1',	NULL,	'TEST X1'),
(27,	1,	27,	26,	'A GGGDDD',	'AAA',	'A GGGDDD'),
(28,	1,	28,	27,	'TEST male rozlisenie',	NULL,	'TEST male rozlisenie'),
(29,	1,	29,	28,	'TEST BW 2',	NULL,	'TEST BW 2'),
(30,	1,	30,	29,	'TEST BW ramceky 1',	NULL,	'TEST BW ramceky 1');

DROP TABLE IF EXISTS `hlavne_menu_opravnenie`;
CREATE TABLE `hlavne_menu_opravnenie` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `nazov` varchar(40) COLLATE utf8_bin NOT NULL COMMENT 'Názov oprávnenia',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Oprávnenia nevlastníkov položiek hlavného menu';

INSERT INTO `hlavne_menu_opravnenie` (`id`, `nazov`) VALUES
(0,	'Žiadne'),
(1,	'Pridávanie podčlánkov'),
(2,	'Editácia položky'),
(3,	'Pridávanie podčlánkov a editácia položky');

DROP TABLE IF EXISTS `hlavne_menu_template`;
CREATE TABLE `hlavne_menu_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `name` varchar(20) COLLATE utf8_bin NOT NULL COMMENT 'Názov vzhľadu',
  `description` varchar(100) COLLATE utf8_bin NOT NULL COMMENT 'Popis vzhľadu',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Vzhľad šablón pre položky menu';

INSERT INTO `hlavne_menu_template` (`id`, `name`, `description`) VALUES
(1,	'default',	'Základný vzhľad'),
(2,	'BWfoto_foto_album',	'Obsah foto albumu'),
(3,	'BWfoto_foto_section',	'Zoznam foto albumov danej časti'),
(4,	'BWfoto_homepage_up',	'Článok na vrchu homepage'),
(5,	'BWfoto_homepage_dwn',	'Článok na spodku homepage');

DROP TABLE IF EXISTS `ikonka`;
CREATE TABLE `ikonka` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `nazov` varchar(30) COLLATE utf8_bin NOT NULL DEFAULT 'ikonka' COMMENT 'Kmeňová časť názvu súboru ikonky',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Ikonky';

INSERT INTO `ikonka` (`id`, `nazov`) VALUES
(0,	'---'),
(1,	'info'),
(2,	'kniha'),
(3,	'kvietok'),
(4,	'lienka'),
(5,	'list_ceruza'),
(6,	'list'),
(7,	'listok'),
(8,	'lupa'),
(9,	'pocasie'),
(10,	'slnko'),
(11,	'smerovnik'),
(12,	'topanka'),
(13,	'vykricnik');

DROP TABLE IF EXISTS `lang`;
CREATE TABLE `lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `skratka` varchar(3) COLLATE utf8_bin NOT NULL DEFAULT 'sk' COMMENT 'Skratka jazyka',
  `nazov` varchar(15) COLLATE utf8_bin NOT NULL DEFAULT 'Slovenčina' COMMENT 'Miestny názov jazyka',
  `nazov_en` varchar(15) COLLATE utf8_bin NOT NULL DEFAULT 'Slovak' COMMENT 'Anglický názov jazyka',
  `prijaty` tinyint(4) DEFAULT NULL COMMENT 'Ak je > 0 jazyk je možné použiť na Frond',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Jazyky pre web';

INSERT INTO `lang` (`id`, `skratka`, `nazov`, `nazov_en`, `prijaty`) VALUES
(1,	'sk',	'Slovenčina',	'Slovak',	1);

DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `text` text COLLATE utf8_bin NOT NULL COMMENT 'Text novinky',
  `created` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Dátum novinky',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS `oznam`;
CREATE TABLE `oznam` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `id_user_main` int(11) NOT NULL DEFAULT 1 COMMENT 'Id užívateľa',
  `id_user_roles` int(11) NOT NULL DEFAULT 0 COMMENT 'Id min úrovne registrácie pre zobrazenie',
  `id_ikonka` int(11) DEFAULT NULL COMMENT 'Id použitej ikonky',
  `datum_platnosti` date NOT NULL DEFAULT '0000-00-00' COMMENT 'Dátum platnosti',
  `datum_zadania` date NOT NULL DEFAULT '0000-00-00' COMMENT 'Dátum zadania oznamu',
  `nazov` varchar(50) DEFAULT NULL COMMENT 'Názov oznamu',
  `text` text DEFAULT NULL COMMENT 'Text oznamu',
  `oznam_kluc` varchar(10) DEFAULT NULL COMMENT 'Kľúč pre potvrdenie účasti',
  `title_image` varchar(200) DEFAULT NULL COMMENT 'Názov titulného obrázku',
  `title_fa_class` varchar(20) DEFAULT NULL COMMENT 'Názov ikonky pre font awesome',
  `title_image_url` varchar(200) DEFAULT NULL COMMENT 'Odkaz titulného obrázka',
  PRIMARY KEY (`id`),
  KEY `id_user_profiles` (`id_user_main`),
  KEY `id_registracia` (`id_user_roles`),
  KEY `id_ikonka` (`id_ikonka`),
  CONSTRAINT `oznam_ibfk_3` FOREIGN KEY (`id_ikonka`) REFERENCES `ikonka` (`id`),
  CONSTRAINT `oznam_ibfk_4` FOREIGN KEY (`id_user_main`) REFERENCES `user_main` (`id`),
  CONSTRAINT `oznam_ibfk_5` FOREIGN KEY (`id_user_roles`) REFERENCES `user_roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Oznamy';

INSERT INTO `oznam` (`id`, `id_user_main`, `id_user_roles`, `id_ikonka`, `datum_platnosti`, `datum_zadania`, `nazov`, `text`, `oznam_kluc`, `title_image`, `title_fa_class`, `title_image_url`) VALUES
(1,	1,	0,	NULL,	'2017-09-21',	'2017-09-18',	'pokus',	'Toto je len pokus.\n\n#REG2# Tak toto chcem aby videl len reg 2 a viac #REG-A2# Tak toto chcem aby videl reg 1 a menej #/REG2#',	'tq7v9ja10t',	NULL,	NULL,	NULL);

DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A] Index',
  `id_hlavne_menu` int(11) NOT NULL DEFAULT 1 COMMENT 'Id položky hl. menu ku ktorej patrí',
  `id_user_main` int(11) NOT NULL DEFAULT 1 COMMENT 'Id užívateľa',
  `id_user_roles` int(11) NOT NULL DEFAULT 0 COMMENT 'Id min úrovne registrácie pre zobrazenie',
  `name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT 'Názov pre daný produkt',
  `web_name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT 'Špecifický názov produktu pre URL',
  `description` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT 'Popis produktu',
  `main_file` varchar(255) COLLATE utf8_bin NOT NULL COMMENT 'Názov súboru produktu s relatívnou cestou',
  `thumb_file` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT 'Názov súboru náhľadu pre obrázky a iné',
  `change` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Čas zmeny',
  `saved` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Ak 1 položka bola korektne nahratá na server',
  PRIMARY KEY (`id`),
  KEY `id_hlavne_menu` (`id_hlavne_menu`),
  KEY `id_user_main` (`id_user_main`),
  KEY `id_user_roles` (`id_user_roles`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`id_hlavne_menu`) REFERENCES `hlavne_menu` (`id`),
  CONSTRAINT `products_ibfk_2` FOREIGN KEY (`id_user_main`) REFERENCES `user_main` (`id`),
  CONSTRAINT `products_ibfk_3` FOREIGN KEY (`id_user_roles`) REFERENCES `user_roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Produkty';

INSERT INTO `products` (`id`, `id_hlavne_menu`, `id_user_main`, `id_user_roles`, `name`, `web_name`, `description`, `main_file`, `thumb_file`, `change`, `saved`) VALUES
(1,	23,	1,	0,	'budovy11',	'budovy11',	NULL,	'files/prilohy/budovy11.JPG',	'files/prilohy/tb_budovy11.JPG',	'2020-07-14 03:40:01',	1),
(10,	23,	1,	0,	'Naša búda',	'dedina12',	'adf ysdfdsf yxvyyasdfgasd ysdfgysy sd dsxfysdydfv afgsd a a',	'files/prilohy/dedina12.JPG',	'files/prilohy/tb_dedina12.JPG',	'2020-07-14 03:40:01',	1),
(11,	23,	1,	0,	'dedina11 sdfg',	'dedina11',	'ghjgh dfdd',	'files/prilohy/dedina11.JPG',	'files/prilohy/tb_dedina11.JPG',	'2020-07-14 03:40:01',	1),
(15,	22,	1,	0,	'k01001',	'krajina1',	'Tam pod Kriváňom je...',	'files/prilohy/krajina1.JPG',	'files/prilohy/tb_krajina1.JPG',	'2020-07-14 03:40:01',	1),
(16,	22,	1,	0,	'k01002',	'obr07',	'Ovocný sad',	'files/prilohy/obr07.jpg',	'files/prilohy/tb_obr07.jpg',	'2020-07-14 03:40:01',	1),
(17,	26,	2,	0,	'Pohlad-JZx1',	'pohlad-jzx1',	NULL,	'files/prilohy/Pohlad-JZx1.jpg',	'files/prilohy/tb_Pohlad-JZx1.jpg',	'2020-07-14 03:40:01',	1),
(18,	26,	2,	0,	'Pohlad-Zx1',	'pohlad-zx1',	NULL,	'files/prilohy/Pohlad-Zx1.jpg',	'files/prilohy/tb_Pohlad-Zx1.jpg',	'2020-07-14 03:40:01',	1),
(19,	26,	2,	0,	'xxxx-11',	'xxxx-11',	NULL,	'files/prilohy/xxxx-11.jpg',	'files/prilohy/tb_xxxx-11.jpg',	'2020-07-14 03:40:01',	1),
(20,	26,	2,	0,	'xxxxxxxxx21',	'xxxxxxxxx21',	NULL,	'files/prilohy/xxxxxxxxx21.jpg',	'files/prilohy/tb_xxxxxxxxx21.jpg',	'2020-07-14 03:40:01',	1);

DROP TABLE IF EXISTS `slider`;
CREATE TABLE `slider` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `poradie` int(11) NOT NULL DEFAULT 1 COMMENT 'Určuje poradie obrázkov v slidery',
  `nadpis` varchar(50) COLLATE utf8_bin DEFAULT NULL COMMENT 'Nadpis obrázku',
  `popis` varchar(150) COLLATE utf8_bin DEFAULT NULL COMMENT 'Popis obrázku slideru vypisovaný v dolnej časti',
  `subor` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '*.jpg' COMMENT 'Názov obrázku slideru aj s relatívnou cestou',
  `zobrazenie` varchar(20) COLLATE utf8_bin DEFAULT NULL COMMENT 'Kedy sa obrázok zobrazí',
  `id_hlavne_menu` int(11) DEFAULT NULL COMMENT 'Odkaz na položku hlavného menu',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Popis obrázkou slideru aj s názvami súborov';

INSERT INTO `slider` (`id`, `poradie`, `nadpis`, `popis`, `subor`, `zobrazenie`, `id_hlavne_menu`) VALUES
(6,	1,	NULL,	NULL,	'bg-site1.jpg',	NULL,	NULL),
(7,	2,	'',	'',	'bg-bwb.png',	'2',	NULL),
(12,	3,	NULL,	NULL,	'TITULK-A-TEST-2020-11-zmena-velikosti2.jpg',	'11',	NULL),
(13,	4,	NULL,	NULL,	'TITULK-A-TEST-2020-5-zmena-velikosti.jpg',	'12',	NULL);

DROP TABLE IF EXISTS `udaje`;
CREATE TABLE `udaje` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `id_user_roles` int(11) NOT NULL DEFAULT 5 COMMENT 'Id min úrovne pre editáciu',
  `id_druh` int(11) DEFAULT NULL COMMENT 'Druhová skupina pre nastavenia',
  `id_udaje_typ` int(11) NOT NULL DEFAULT 1 COMMENT 'Typ input-u',
  `nazov` varchar(30) COLLATE utf8_bin NOT NULL DEFAULT 'nazov' COMMENT 'Názov prvku',
  `text` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT 'Definícia' COMMENT 'Hodnota prvku',
  `comment` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT 'Komentár k hodnote',
  `separate_settings` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Ak 1 tak má položka vlastnú časť nastavení',
  PRIMARY KEY (`id`),
  KEY `id_reg` (`id_user_roles`),
  KEY `id_druh` (`id_druh`),
  KEY `id_udaje_typ` (`id_udaje_typ`),
  CONSTRAINT `udaje_ibfk_2` FOREIGN KEY (`id_druh`) REFERENCES `druh` (`id`),
  CONSTRAINT `udaje_ibfk_3` FOREIGN KEY (`id_udaje_typ`) REFERENCES `udaje_typ` (`id`),
  CONSTRAINT `udaje_ibfk_4` FOREIGN KEY (`id_user_roles`) REFERENCES `user_roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Tabuľka na uschovanie základných údajov o stránke';

INSERT INTO `udaje` (`id`, `id_user_roles`, `id_druh`, `id_udaje_typ`, `nazov`, `text`, `comment`, `separate_settings`) VALUES
(1,	4,	NULL,	1,	'titulka-sk',	'BW Foto - Ateliér architektúry a fotografie',	'Názov zobrazený v titulke',	0),
(2,	5,	NULL,	1,	'titulka_2-sk',	'',	'Druhá časť titulky pre jazyk: sk',	0),
(3,	5,	NULL,	1,	'titulka_citat_enable',	'0',	'Povolenie zobrazenia citátu',	0),
(4,	5,	NULL,	1,	'titulka_citat_podpis',	'',	'Podpis pod citát na titulke',	0),
(5,	5,	NULL,	1,	'titulka_citat-sk',	'',	'Text citátu, ktorý sa zobrazí na titulke pre jazyk: sk',	0),
(6,	4,	NULL,	1,	'keywords-sk',	'BW Foto, Spišské Bystré, ateliér,  architektúra, fotografie',	'Kľúčové slová',	0),
(7,	5,	NULL,	1,	'autor',	'Ing. Peter VOJTECH ml. - VZ',	'Autor stránky',	0),
(8,	5,	NULL,	1,	'log_out-sk',	'Odhlás sa...',	'Text pre odkaz na odhlásenie sa',	0),
(9,	5,	NULL,	1,	'log_in-sk',	'Prihlás sa',	'Text pre odkaz na prihlásenie sa',	0),
(10,	5,	NULL,	1,	'forgot_password-sk',	'Zabudnuté heslo?',	'Text pre odkaz na zabudnuté heslo',	0),
(11,	5,	NULL,	1,	'register-sk',	'Registrácia',	'Text pre odkaz na registráciu',	0),
(12,	5,	NULL,	1,	'last_update-sk',	'Posledná aktualizácia',	'Text pre odkaz na poslednú aktualizáciu',	0),
(13,	4,	NULL,	1,	'spravca-sk',	'Správca obsahu',	'Text pre odkaz na správcu',	0),
(14,	4,	NULL,	1,	'copy',	'BW Foto',	'Text, ktorý sa vypíše za znakom copyright-u',	0),
(15,	5,	NULL,	1,	'no_exzist-sk',	'To čo hľadáte nie je ešte v tomto jazyku vytvorené!',	'Text ak položka v danom jazyku neexzistuje pre jazyk:sk',	0),
(16,	5,	NULL,	1,	'nazov_uvod-sk',	'Úvod',	'Text pre odkaz na východziu stránku pre jazyk:sk',	0),
(17,	5,	NULL,	3,	'komentare',	'0',	'Globálne povolenie komentárov',	0),
(18,	5,	NULL,	3,	'registracia_enabled',	'0',	'Globálne registrácie(ak 1 tak áno, ak 0 tak nie)',	0),
(19,	4,	1,	1,	'clanok_hlavicka',	'0',	'Nastavuje, ktoré hodnoty sa zobrazia v hlavičke článku Front modulu. Výsledok je súčet čísel.[1=Dátum, 2=Zadávateľ, 4=Počet zobrazení]',	0),
(21,	4,	5,	3,	'oznam_komentare',	'0',	'Povolenie komentárov k aktualitám(oznamom).',	0),
(22,	5,	5,	2,	'oznam_usporiadanie',	'1',	'Usporiadanie aktualít podľa dátumu platnosti. [1=od najstaršieho; 0=od najmladšieho]',	0),
(23,	4,	5,	3,	'oznam_ucast',	'0',	'Povolenie potvrdenia účasti.',	0),
(24,	5,	5,	1,	'oznam_prva_stranka',	'1',	'Id stránky, ktorá sa zobrazí ako 1. po načítaní webu',	0),
(25,	4,	5,	3,	'oznam_title_image_en',	'1',	'Povolenie pridávania titulného obrázku k oznamu. Ak je zakázané používajú sa ikonky.',	0),
(26,	5,	NULL,	1,	'google-analytics',	'UA-52835371-1',	'Id pre google-analytics. Ak sa reťazec nezačína na \"UA-\" nie je akceptovaný.',	0),
(27,	4,	8,	4,	'product_main_x',	'1510',	'Šírka hlavného obrázku produktu[10,2000]',	1),
(28,	4,	8,	4,	'product_main_y',	'1000',	'Výška hlavného obrázku produktu[10,2000]',	1),
(29,	4,	8,	4,	'product_main_quality',	'80',	'Kvalita kompresie hlavného obrázku[1,100]',	1),
(30,	4,	8,	4,	'product_thumb_x',	'226',	'Šírka náhľadového obrázku produktu[10,1000]',	1),
(31,	4,	8,	4,	'product_thumb_y',	'150',	'Výška náhľadového obrázku produktu[10,1000]',	1),
(32,	4,	8,	4,	'product_thumb_quality',	'70',	'Kvalita kompresie náhľadového obrázku[1,100]',	1),
(33,	4,	8,	4,	'product_max_upload_files',	'50',	'Max. počet naraz nahrávaných súborov[5,150]',	1);

DROP TABLE IF EXISTS `udaje_typ`;
CREATE TABLE `udaje_typ` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `nazov` varchar(20) COLLATE utf8_bin NOT NULL DEFAULT 'text' COMMENT 'Typ input-u pre danú položku',
  `comment` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT 'Text' COMMENT 'Popis navonok',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Typy prvkov pre tabuľku udaje';

INSERT INTO `udaje_typ` (`id`, `nazov`, `comment`) VALUES
(1,	'text',	'Text'),
(2,	'radio',	'Vyber jednu možnosť'),
(3,	'checkbox',	'Áno alebo nie'),
(4,	'number',	'Číslo');

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


DROP TABLE IF EXISTS `user_main`;
CREATE TABLE `user_main` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `id_user_roles` int(11) NOT NULL DEFAULT 0 COMMENT 'Úroveň registrácie a rola',
  `id_user_profiles` int(11) DEFAULT NULL COMMENT 'Užívateľský profil',
  `password` varchar(255) COLLATE utf8_bin NOT NULL COMMENT 'Heslo',
  `meno` varchar(50) COLLATE utf8_bin NOT NULL COMMENT 'Meno',
  `priezvisko` varchar(50) COLLATE utf8_bin NOT NULL COMMENT 'Priezvisko',
  `email` varchar(100) COLLATE utf8_bin NOT NULL COMMENT 'Email',
  `activated` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Aktivácia',
  `banned` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Zazázaný',
  `ban_reason` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT 'Dôvod zákazu',
  `new_password_key` varchar(100) COLLATE utf8_bin DEFAULT NULL COMMENT 'Kľúč nového hesla',
  `new_password_requested` datetime DEFAULT NULL COMMENT 'Čas požiadavky na nové heslo',
  `new_email` varchar(100) COLLATE utf8_bin DEFAULT NULL COMMENT 'Nový email',
  `new_email_key` varchar(100) COLLATE utf8_bin DEFAULT NULL COMMENT 'Kľúč nového emailu',
  `last_ip` varchar(40) COLLATE utf8_bin DEFAULT NULL COMMENT 'Posledná IP',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Vytvorenie užívateľa',
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Posledná zmena',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `id_registracia` (`id_user_roles`),
  KEY `id_user_profiles` (`id_user_profiles`),
  CONSTRAINT `user_main_ibfk_2` FOREIGN KEY (`id_user_profiles`) REFERENCES `user_profiles` (`id`) ON DELETE SET NULL,
  CONSTRAINT `user_main_ibfk_3` FOREIGN KEY (`id_user_roles`) REFERENCES `user_roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Hlavné údaje užívateľa';

INSERT INTO `user_main` (`id`, `id_user_roles`, `id_user_profiles`, `password`, `meno`, `priezvisko`, `email`, `activated`, `banned`, `ban_reason`, `new_password_key`, `new_password_requested`, `new_email`, `new_email_key`, `last_ip`, `created`, `modified`) VALUES
(1,	5,	1,	'$2y$10$RnzAjUCyc/B1GgiJ9k43/e27BDz5j1vsbN.DYlfnXIxweBvqxkABq',	'Peter',	'Vojtech',	'petak23@gmail.com',	1,	0,	NULL,	NULL,	NULL,	NULL,	NULL,	'217.12.48.22',	'2017-05-15 09:11:19',	'2020-07-14 08:20:23'),
(2,	4,	2,	'$2y$10$0KPg/2sE8I5EjAsgolRttulqhQPsUoVrhIHAxX8Ej3NAOHGuZIbW.',	'Juraj',	'Zámečník',	'bwfoto@bwfoto.sk',	1,	0,	NULL,	NULL,	NULL,	NULL,	NULL,	'178.143.98.194',	'2017-05-15 09:13:38',	'2020-05-15 17:26:00'),
(3,	4,	3,	'$2y$10$VOeK4y3ozjaUM1aMtiVmcuHRmtcmoVvC6J4yFX4j0LZoNbXlejyMi',	'Jozef',	'Petrenčík',	'jozue@anigraph.eu',	1,	0,	NULL,	NULL,	NULL,	NULL,	NULL,	'178.253.139.152',	'2017-05-15 09:12:22',	'2017-07-11 07:10:29');

DROP TABLE IF EXISTS `user_permission`;
CREATE TABLE `user_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `id_user_roles` int(11) NOT NULL DEFAULT 0 COMMENT 'Užívateľská rola',
  `id_user_resource` int(11) NOT NULL COMMENT 'Zdroj',
  `actions` varchar(100) COLLATE utf8_bin DEFAULT NULL COMMENT 'Povolenie na akciu. (Ak viac oddelené čiarkou, ak null tak všetko)',
  PRIMARY KEY (`id`),
  KEY `id_user_roles` (`id_user_roles`),
  KEY `id_user_resource` (`id_user_resource`),
  CONSTRAINT `user_permission_ibfk_1` FOREIGN KEY (`id_user_roles`) REFERENCES `user_roles` (`id`),
  CONSTRAINT `user_permission_ibfk_2` FOREIGN KEY (`id_user_resource`) REFERENCES `user_resource` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Užívateľské oprávnenia';

INSERT INTO `user_permission` (`id`, `id_user_roles`, `id_user_resource`, `actions`) VALUES
(1,	0,	4,	NULL),
(2,	0,	6,	NULL),
(3,	0,	7,	NULL),
(4,	0,	8,	NULL),
(5,	0,	1,	NULL),
(6,	0,	5,	NULL),
(7,	0,	2,	NULL),
(8,	0,	9,	'default'),
(9,	0,	3,	'activateNewEmail'),
(10,	1,	3,	'default,mailChange,passwordChange'),
(11,	3,	19,	'default,edit,edit2,add,add2,del'),
(12,	3,	13,	'default,edit,edit2,add,add2'),
(13,	3,	10,	NULL),
(14,	3,	15,	NULL),
(15,	4,	9,	NULL),
(16,	4,	19,	'addpol'),
(17,	4,	13,	'addpol'),
(18,	4,	12,	'default'),
(19,	4,	11,	'default'),
(20,	4,	14,	'default,edit'),
(21,	4,	18,	NULL),
(22,	4,	17,	NULL),
(23,	4,	20,	NULL),
(24,	4,	16,	'default,edit'),
(25,	5,	16,	NULL),
(26,	5,	14,	NULL),
(27,	5,	11,	NULL),
(28,	5,	12,	NULL),
(29,	5,	13,	NULL),
(30,	5,	19,	NULL),
(31,	3,	21,	NULL),
(32,	1,	23,	'default,mailChange,passwordChange,activateNewEmail'),
(33,	3,	7,	'default'),
(34,	4,	25,	NULL);

DROP TABLE IF EXISTS `user_prihlasenie`;
CREATE TABLE `user_prihlasenie` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `id_user_main` int(11) NOT NULL COMMENT 'Id užívateľa',
  `log_in_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Dátum a čas prihlásenia',
  PRIMARY KEY (`id`),
  KEY `id_user_profiles` (`id_user_main`),
  CONSTRAINT `user_prihlasenie_ibfk_1` FOREIGN KEY (`id_user_main`) REFERENCES `user_main` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Evidencia prihlásenia užívateľov';

INSERT INTO `user_prihlasenie` (`id`, `id_user_main`, `log_in_datetime`) VALUES
(29,	2,	'2018-01-22 07:42:42'),
(30,	1,	'2018-08-02 10:00:39'),
(31,	1,	'2018-08-28 09:02:57'),
(32,	1,	'2018-08-30 10:07:20'),
(33,	2,	'2020-03-18 20:53:05'),
(34,	1,	'2020-03-19 16:41:47'),
(35,	2,	'2020-03-21 18:28:42'),
(36,	1,	'2020-03-21 19:01:42'),
(37,	2,	'2020-03-22 01:03:11'),
(38,	2,	'2020-03-23 13:15:50'),
(39,	2,	'2020-03-23 17:53:55'),
(40,	2,	'2020-03-24 00:52:11'),
(41,	2,	'2020-03-24 01:11:52'),
(42,	1,	'2020-03-24 06:40:00'),
(43,	2,	'2020-03-24 11:40:21'),
(44,	2,	'2020-03-24 13:52:59'),
(45,	2,	'2020-03-24 22:24:38'),
(46,	1,	'2020-03-25 10:55:45'),
(47,	1,	'2020-03-26 08:22:22'),
(48,	1,	'2020-03-26 15:41:00'),
(49,	1,	'2020-03-30 07:18:02'),
(50,	1,	'2020-04-02 12:57:33'),
(51,	2,	'2020-04-02 16:09:34'),
(52,	2,	'2020-04-02 17:50:39'),
(53,	2,	'2020-04-03 10:55:51'),
(54,	2,	'2020-04-03 12:14:08'),
(55,	2,	'2020-04-03 13:49:14'),
(56,	1,	'2020-04-06 10:51:01'),
(57,	1,	'2020-04-07 14:15:52'),
(58,	1,	'2020-04-10 04:10:50'),
(59,	1,	'2020-04-12 10:39:46'),
(60,	1,	'2020-04-19 08:47:09'),
(61,	1,	'2020-04-20 06:56:40'),
(62,	1,	'2020-05-04 13:46:35'),
(63,	1,	'2020-05-13 14:36:11'),
(64,	1,	'2020-05-14 15:18:49'),
(65,	2,	'2020-05-15 19:26:00'),
(66,	2,	'2020-05-15 21:15:24'),
(67,	1,	'2020-05-23 05:43:39'),
(68,	1,	'2020-05-24 10:20:27'),
(69,	1,	'2020-05-26 15:48:32'),
(70,	1,	'2020-05-31 16:58:58'),
(71,	1,	'2020-06-15 11:06:10'),
(72,	1,	'2020-07-14 05:38:43'),
(73,	1,	'2020-07-14 10:20:23'),
(74,	1,	'2020-07-14 11:36:30'),
(75,	1,	'2020-07-28 07:23:15');

DROP TABLE IF EXISTS `user_profiles`;
CREATE TABLE `user_profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rok` int(11) DEFAULT NULL COMMENT 'Rok narodenia',
  `telefon` varchar(30) COLLATE utf8_bin DEFAULT NULL COMMENT 'Telefón',
  `poznamka` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT 'Poznámka',
  `pocet_pr` int(11) NOT NULL DEFAULT 0 COMMENT 'Počet prihlásení',
  `pohl` enum('Z','M') COLLATE utf8_bin NOT NULL DEFAULT 'M' COMMENT 'Pohlavie',
  `prihlas_teraz` datetime DEFAULT NULL COMMENT 'Posledné prihlásenie',
  `avatar` varchar(200) COLLATE utf8_bin DEFAULT NULL COMMENT 'Cesta k avatarovi veľkosti 75x75',
  `news` enum('A','N') COLLATE utf8_bin NOT NULL DEFAULT 'A' COMMENT 'Posielanie info emailou',
  `news_key` varchar(100) COLLATE utf8_bin DEFAULT NULL COMMENT 'Kľúč pre odhlásenie noviniek',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `user_profiles` (`id`, `rok`, `telefon`, `poznamka`, `pocet_pr`, `pohl`, `prihlas_teraz`, `avatar`, `news`, `news_key`) VALUES
(1,	NULL,	NULL,	NULL,	52,	'M',	'2020-07-28 07:23:15',	NULL,	'A',	NULL),
(2,	NULL,	NULL,	NULL,	23,	'M',	'2020-05-15 21:15:24',	NULL,	'A',	NULL),
(3,	NULL,	NULL,	NULL,	0,	'M',	NULL,	NULL,	'A',	NULL);

DROP TABLE IF EXISTS `user_resource`;
CREATE TABLE `user_resource` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `name` varchar(30) COLLATE utf8_bin NOT NULL COMMENT 'Názov zdroja',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Zdroje oprávnení';

INSERT INTO `user_resource` (`id`, `name`) VALUES
(1,	'Front:Homepage'),
(2,	'Front:User'),
(3,	'Front:UserLog'),
(4,	'Front:Dokumenty'),
(5,	'Front:Error'),
(6,	'Front:Oznam'),
(7,	'Front:Clanky'),
(8,	'Front:Menu'),
(9,	'Front:Faktury'),
(10,	'Admin:Homepage'),
(11,	'Admin:User'),
(12,	'Admin:Verzie'),
(13,	'Admin:Menu'),
(14,	'Admin:Udaje'),
(15,	'Admin:Dokumenty'),
(16,	'Admin:Lang'),
(17,	'Admin:Slider'),
(18,	'Admin:Oznam'),
(19,	'Admin:Clanky'),
(20,	'Admin:Texyla'),
(21,	'Edit:Homepage'),
(22,	'Edit:User'),
(23,	'Edit:UserLog'),
(24,	'Edit:Clanky'),
(25,	'Admin:Products'),
(26,	'Front:Search');

DROP TABLE IF EXISTS `user_roles`;
CREATE TABLE `user_roles` (
  `id` int(11) NOT NULL COMMENT '[A]Index',
  `role` varchar(30) COLLATE utf8_bin NOT NULL DEFAULT 'guest' COMMENT 'Rola pre ACL',
  `inherited` varchar(30) COLLATE utf8_bin DEFAULT NULL COMMENT 'Dedí od roli',
  `name` varchar(30) COLLATE utf8_bin NOT NULL DEFAULT 'Registracia cez web' COMMENT 'Názov úrovne registrácie',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Úrovne registrácie a ich názvy';

INSERT INTO `user_roles` (`id`, `role`, `inherited`, `name`) VALUES
(0,	'guest',	NULL,	'Bez registrácie'),
(1,	'register',	'guest',	'Registrácia cez web'),
(2,	'passive',	'register',	'Pasívny užívateľ'),
(3,	'active',	'passive',	'Aktívny užívateľ'),
(4,	'manager',	'active',	'Správca obsahu'),
(5,	'admin',	'manager',	'Administrátor');

DROP TABLE IF EXISTS `verzie`;
CREATE TABLE `verzie` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `id_user_main` int(11) NOT NULL DEFAULT 1 COMMENT 'Id užívateľa',
  `cislo` varchar(10) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT 'Číslo verzie',
  `subory` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT 'Zmenené súbory',
  `text` text COLLATE utf8_bin DEFAULT NULL COMMENT 'Popis zmien',
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Dátum a čas zmeny',
  PRIMARY KEY (`id`),
  UNIQUE KEY `cislo` (`cislo`),
  KEY `datum` (`modified`),
  KEY `id_clena` (`id_user_main`),
  CONSTRAINT `verzie_ibfk_1` FOREIGN KEY (`id_user_main`) REFERENCES `user_main` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Verzie webu';

INSERT INTO `verzie` (`id`, `id_user_main`, `cislo`, `subory`, `text`, `modified`) VALUES
(1,	1,	'0.1.0',	NULL,	'Východzia verzia',	'2017-09-07 12:43:25'),
(2,	1,	'0.1.1',	'Texy',	'- Implementácia texy a texyly\n- Oprava nájdených chýb',	'2017-10-10 09:52:04'),
(3,	1,	'0.1.3',	'Prílohy, slider',	'Testovacia verzia, v ktorej bolo opravené:\n\n- **Administrácia - Prílohy článkov:** možnosť pridať súčasne viac príloh, inline editácia popisu a názvu prílohy.\n- **Administrácia - Slider:** pridaná možnosť upravovať zobrazenie jednotlivých položiek.\n- **Administrácia:** náhrada ckeditora za texylu.\n- **Web - Slider:** zobrazovanie obrázku podľa príslušnej časti.\n- **Web - Lightbox:** upgrade na verziu 0.5.6 a pridanie popisu k obrázkom.',	'2017-10-10 09:51:40'),
(4,	1,	'0.1.4',	'Merge pull request #2 from petak23/nittro',	'Testovacia verzia v ktorej bolo zmenené:\n- Aplikácia \"nittra\":https://www.nittro.org/ .\n- Úprava požitia lightbox-u.\n- Úprava administračnej časti evidencie verzií.\n- Rozdelenie konfigurácie pre jednotlivé moduly.\n- Úprava adresárovej štruktúry.\n- Aktualizácia vzhľadu.\n- Aktualizácia vzhľadu príloh.\n- Pridanie možnosti pridania náhľadového obrázka pre neobrázkové prílohy.\n- Úprava sekcie zoznamu albumov podľa JP.\n',	'2017-11-15 10:47:16'),
(5,	1,	'0.1.5',	'rôzne',	'Prispôsobenie vzhľadu úrovne 3',	'2017-12-27 11:59:49'),
(6,	1,	'0.1.6',	'úroveň 3 a 4',	'- Prispôsobenie vzhľadu úrovne 3. a 4..\n- Aktualizácia nittra a lightbox-u.\n- Odstránenie koncovky z názvu prílohy.\n- Práca na admin časti pre rámčeky - pridaná časť pre fotoalbum zmeny rámčekov obrázkových príloh v úrovni 4. pre každú časť zvlášť.\n- Prispôsobenie 4. úrovne pre prehliadanie obrázkov s rámčekmi.',	'2017-12-27 12:04:06'),
(7,	1,	'0.1.7',	'mnohé',	'- Zmena názvov stĺpcov(spec_nazov=web_name, subor=main_file, thumb=thumb_file, zmena=change) tabuľky dokumenty a s tým súvisiace zmeny. \n- Pridané zobrazenie videa pre front modul.\n- Pridaná časť produktov aj pre front časť. \n- Zmena názvov stĺpcov(nazov=name, popis=description) tabuľky dokumenty a s tým súvisiace zmeny.\n- Pridaná časť produktov pre admin časť. Nedokončené. Update fontawesome na verziu 5.0.2 na admin strane.',	'2018-01-16 09:15:17'),
(8,	1,	'0.2.1',	'Issue #4, #5, #6',	'Sumár:\n======\n**Zmena načítania produktov.**\n\n**V administrácii: oprava ikoniek; pre slider: pridanie kompletného stromu menu do formulára - položka: *Zobrazenie pre*;** \n\nDetaily:\n======\nFrontend\n----------\n- Úrava pre IE.\n- Načítanie produktov ajaxovo.\n- Oprava chyby pri zobrazení príloh alebo produktov.\n\nAdministrácia\n-------------\n- Oprava ikoniek v administrácii a odstránenie nepotrebných. \n- Presun nastavení produktov do samostatnej časti. Issue \"#5\":https://github.com/petak23/bwfoto/issues/5\n- V údajoch pridaná možnosť zobraziť alebo skryť separátne nastavenia.\n- Slider: Odstránenie väzby súborov na adresáre a presun ich nastavení do configu.\n- Slider: Pridanie kompletného stromu menu do formulára - položka: *Zobrazenie pre*.\n- Slider: zmena názvu ikoniek.\n- Presun nastavenia okrajového rámčeka na kartu produktov Issues \"#4\":https://github.com/petak23/bwfoto/issues/4\n- Odstránenie chyby v mazaní príloh\n\nOstatné\n-------\n- Oprava zobrazovania textu(texi) v emailoch. Issue \"#6\":https://github.com/petak23/bwfoto/issues/6\n- Odstránenie zmetočných adresárov v prílohách článkov a produktov. \n- Zmazanie nepotrebných častí.\n- Presun index.php do adresára www a s tým spojené zmeny.\n- Update fontawesome na verziu 5.2.0; \n- Update bootstrap na verziu 4.1.2; \n- Update adminer na verziu 4.3.6. \n- Použitie npm správcu. \n- Odstránenie zbytočných css a js.\n',	'2018-08-30 08:35:32'),
(9,	1,	'0.3.0-alfa',	'Upgrade to v 0.3.0',	'- **Oprava chyby: zobrazenie prvého obrázku alebo produktu pri prvom zobrazení článku.**\n- Upgrade nette na verziu 2.4\n- Aplikácia zmien z echo-msz.eu ale s prispôsobením komponent na verziu PHP 7.0.33\n- Inovácia vzhľadu v administrácii, zatiaľ ešte nie úplne dokončená \"(AdminModule) .{color: gray}\".\n- Aktualizácia komponent vzhľadu v package.json \"(fontawesome: 5.2.0, bootstrap: 4.4.1, jquery: 3.4.1, naja: 1.5.1, nette-forms: 2.4.2, popper.js: 1.16.1) .{color: gray}\". \n- Aktualizácia správcu databázy \"(adminer v 4.7.6) .{color: gray}\". \n- Oprava chyby v menu FrontModule a upgrade pre novú verziu Texy.',	'2020-05-04 11:55:19'),
(10,	1,	'0.3.0-beta',	'Rôzne; jquery 3.5.0',	'- Oprava drobných chýb v prihlásení.\n- Oprava chyby v slideri(issue \"#12\":https://github.com/petak23/bwfoto/issues/12). Rebase model/Table.php.\n- Rebase model/Dokumenty.php; model/Products.php a model/UserManager.php.\n- Oprava chyby v pridávaní podčlánku v adninistrácii.\n- Refaktoring fotogalérie na frontende.\n- Oprava zobrazenia produktov v AdminModule. \n- Update jquery na 3.5.0.\n- Oprava zobrazenia príloh vo FrontModule a premenovanie adresára presenters na Presenters.\n- Oprava issue \"#11\":https://github.com/petak23/bwfoto/issues/11 - A13 - V administracii pri zozname príloh( miniatúry fotografií) keď kliknem na foto aby sa mi zobrazila fotka tak sa zobrazí na ľavej strane - nie je vycentrovaná na stred',	'2020-05-04 12:02:51'),
(11,	1,	'0.4.0-RC',	'Nette 3.0, adminer 4.7.7',	'- Oprava issue \"#10\":https://github.com/petak23/bwfoto/issues/10 - A10 - nekorektné zobrazenie úrovne 3. a 4.\n- Oprava zobrazenia formulárov na karte Produktu pre AdminModul a odstránenie chyby zobrazenia nastavenia okrajov. \n- Odstránenie chyby zobrazenia hlavného menu v AdminModul-e.\n- Úpravy potrebné pre prechod na nette 3.0,\n- odstránenie drobných chýb,\n- vypustenie webloader-a\n',	'2020-05-26 13:53:54');

-- 2020-07-28 10:48:34
