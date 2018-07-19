-- Adminer 4.6.3 MySQL dump

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
  `id_lang` int(11) NOT NULL DEFAULT '1' COMMENT 'Id jazyka',
  `text` text COLLATE utf8_bin,
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
(17,	1,	'Toto je časť A4',	'Tu nájdete všetky novinky.'),
(18,	1,	'ydfgydf ydfgy str  gzus v',	'Spevní nášho zboru.'),
(19,	1,	'Lorem ipsum dolor sit amet, consectetur adipiscing elit. \n\n*Phasellus quis lectus metus, at posuere neque*. \n8-O\nSed pharetra nibh eget orci convallis at posuere leo convallis. ΩSed blandit augue vitae augue scelerisque bibendum. VIVAMUS SIT amet libero turpis, non venenatis urna. In blandit, odio convallis suscipit venenatis, ante ipsum cursus augue.\n\n> Lorem ipsum dolor sit amet, consectetur adipiscing elit. \n\n\n\n',	'Ešte niečo iné'),
(20,	1,	'Fotografie krajiny',	''),
(21,	1,	'Fotografie kostolov',	'fotografie rôznych kostolov'),
(22,	1,	'Fotografie stromov',	''),
(23,	1,	'Fotografie budov',	''),
(24,	1,	'Fotografie v protisvetle',	'');

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
  `id_hlavne_menu` int(11) NOT NULL DEFAULT '1' COMMENT 'Id položky hl. menu ku ktorej patrí',
  `id_user_main` int(11) NOT NULL DEFAULT '1' COMMENT 'Id užívateľa',
  `id_user_roles` int(11) NOT NULL DEFAULT '0' COMMENT 'Id min úrovne registrácie pre zobrazenie',
  `znacka` varchar(20) COLLATE utf8_bin DEFAULT NULL COMMENT 'Značka súboru pre vloženie do textu',
  `name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT 'Názov titulku pre daný dokument',
  `pripona` varchar(20) COLLATE utf8_bin NOT NULL COMMENT 'Prípona súboru',
  `web_name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT 'Špecifický názov dokumentu pre URL',
  `description` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT 'Popis dokumentu',
  `main_file` varchar(255) COLLATE utf8_bin NOT NULL COMMENT 'Názov súboru s relatívnou cestou',
  `thumb_file` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT 'Názov súboru thumb pre obrázky a iné ',
  `change` datetime NOT NULL COMMENT 'Dátum uloženia alebo opravy - časová pečiatka',
  `zobraz_v_texte` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'Zobrazenie obrázku v texte',
  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'Typ prílohy',
  `pocitadlo` int(11) NOT NULL DEFAULT '0' COMMENT 'Počítadlo stiahnutí',
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
(43,	24,	1,	0,	'#I-43#',	'protisvelo2',	'jpg',	'protisvelo2',	NULL,	'www/files/prilohy/protisvelo2.jpg',	'www/files/prilohy/tb_protisvelo2.jpg',	'2017-12-18 08:48:37',	1,	2,	0),
(44,	24,	1,	0,	'#I-44#',	'protisvetlo1',	'JPG',	'protisvetlo1',	NULL,	'www/files/prilohy/protisvetlo1.JPG',	'www/files/prilohy/tb_protisvetlo1.JPG',	'2017-12-18 08:48:37',	1,	2,	0),
(53,	22,	1,	0,	NULL,	'Dokument',	'pdf',	'usmernenie-k-sr1023',	NULL,	'www/files/prilohy/Usmernenie-k-SR1023.pdf',	'www/files/prilohy/tb_Usmernenie-k-SR1023.jpg',	'2018-01-03 09:16:58',	1,	1,	2),
(54,	22,	1,	0,	NULL,	'video',	'MOV',	'dsc-1566',	NULL,	'www/files/prilohy/DSC-1566.MOV',	'www/files/prilohy/tb_DSC-1566.jpg',	'2018-01-16 11:24:09',	1,	3,	2),
(55,	22,	1,	0,	NULL,	'thfg dfghfgh dfgfd ',	'pdf',	't-115',	'dfgfdd dfddf ',	'www/files/prilohy/T-115.pdf',	'www/files/prilohy/tb_T-115.jpg',	'2018-01-16 11:31:00',	1,	1,	1);

DROP TABLE IF EXISTS `druh`;
CREATE TABLE `druh` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Id položiek',
  `druh` varchar(20) COLLATE utf8_bin NOT NULL COMMENT 'Názov druhu stredného stĺpca',
  `modul` varchar(20) COLLATE utf8_bin DEFAULT NULL COMMENT 'Názov špecifického modulu ak NULL vždy',
  `presenter` varchar(30) COLLATE utf8_bin NOT NULL COMMENT 'Názov prezenteru pre Nette',
  `popis` varchar(255) COLLATE utf8_bin DEFAULT 'Popis' COMMENT 'Popis bloku',
  `povolene` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'Ak 1 tak daná položka je povolená',
  `je_spec_naz` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Ak 1 tak daný druh potrebuje špecif. názov',
  `robots` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'Ak 1 tak je povolené indexovanie daného druhu',
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
  `id_hlavne_menu` int(11) NOT NULL DEFAULT '1' COMMENT 'Ku ktorej položke patrí',
  `nazov` varchar(50) COLLATE utf8_bin DEFAULT NULL COMMENT 'Názov titulku pre daný dokument',
  `cislo` varchar(20) COLLATE utf8_bin DEFAULT NULL COMMENT 'Číslo faktúry, zmluvy, objednávky',
  `predmet` varchar(200) COLLATE utf8_bin DEFAULT NULL COMMENT 'Predmet faktúry, zmluvy, objednávky',
  `cena` float(15,2) DEFAULT NULL COMMENT 'Cena faktúry, zmluvy, objednávky',
  `subjekt` varchar(200) COLLATE utf8_bin DEFAULT NULL COMMENT 'Subjekt faktúry,  objednávky - (dodávateľ), zmluvy(Zmluvná strana)',
  `datum_vystavenia` date DEFAULT NULL COMMENT 'Dátum vystavenia pri faktúre a objednávke pri zmluve dátum uzatvorenia',
  `datum_ukoncenia` date DEFAULT NULL COMMENT 'Dátum ukoncenia zmluvy',
  `subor` varchar(50) COLLATE utf8_bin NOT NULL COMMENT 'Názov súboru s relatívnou cestou',
  `id_user_main` int(11) NOT NULL DEFAULT '1' COMMENT 'Kto pridal dokument',
  `id_reg` int(11) NOT NULL DEFAULT '0' COMMENT 'Úroveň registrácie',
  `kedy` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Dátum uloženia alebo opravy - časová pečiatka',
  `pocitadlo` int(11) NOT NULL DEFAULT '0' COMMENT 'Počítadlo stiahnutí',
  `id_skupina` int(11) NOT NULL DEFAULT '0' COMMENT 'Id článku do ktorej časti dokument patrí',
  `id_rok` int(11) NOT NULL DEFAULT '0' COMMENT 'Id roku do ktorého sa má zaradiť',
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
  `id_hlavne_menu_cast` int(11) NOT NULL DEFAULT '1' COMMENT '[5]Ku ktorej časti hl. menu patrí položka',
  `id_user_roles` int(11) NOT NULL DEFAULT '0' COMMENT 'Id min úrovne registrácie pre zobrazenie',
  `id_ikonka` int(11) DEFAULT NULL COMMENT '[4]Názov súboru ikonky aj s koncovkou',
  `id_druh` int(11) NOT NULL DEFAULT '1' COMMENT '[5]Výber druhu priradenej položky. Ak 1 tak je možné priradiť článok v náväznosti na tab. druh',
  `uroven` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Úroveň položky menu',
  `id_nadradenej` int(11) DEFAULT NULL COMMENT 'Id nadradenej položky menu z tejto tabuľky ',
  `id_user_main` int(11) NOT NULL DEFAULT '1' COMMENT 'Id užívateľa',
  `poradie` int(11) NOT NULL DEFAULT '1' COMMENT 'Poradie v zobrazení',
  `poradie_podclankov` int(11) NOT NULL DEFAULT '0' COMMENT 'Poradie podčlánkov ak sú: 0 - od 1-9, 1 - od 9-1',
  `id_hlavicka` int(11) NOT NULL DEFAULT '0' COMMENT '[5]Druh hlavičky podľa tabuľky hlavicka. 1 - velka',
  `id_hlavne_menu_opravnenie` int(11) NOT NULL DEFAULT '0' COMMENT 'Povolenie pre nevlastníkov (0-žiadne,1- podčlánky,2-editacia,4-všetko)',
  `zvyrazni` tinyint(4) NOT NULL DEFAULT '0' COMMENT '[5]Zvýraznenie položky menu pri pridaní obsahu',
  `pocitadlo` int(11) NOT NULL DEFAULT '0' COMMENT '[R]Počítadlo kliknutí na položku',
  `nazov_ul_sub` varchar(20) COLLATE utf8_bin DEFAULT NULL COMMENT '[5]Názov pomocnej triedy ul-elsementu sub menu',
  `id_hlavne_menu_template` int(11) NOT NULL DEFAULT '1' COMMENT 'Vzhľad šablóny',
  `absolutna` varchar(100) COLLATE utf8_bin DEFAULT NULL COMMENT 'Absolútna adresa',
  `ikonka` varchar(30) COLLATE utf8_bin DEFAULT NULL COMMENT 'Názov css ikonky',
  `avatar` varchar(300) COLLATE utf8_bin DEFAULT NULL COMMENT 'Názov a cesta k titulnému obrázku',
  `komentar` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Povolenie komentárov',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Posledná zmena',
  `datum_platnosti` date DEFAULT NULL COMMENT 'Platnosť',
  `aktualny_projekt` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Označenie aktuálneho projektu',
  `redirect_id` int(11) DEFAULT NULL COMMENT 'Id článku na ktorý sa má presmerovať',
  `id_dlzka_novinky` int(11) NOT NULL DEFAULT '1' COMMENT 'Do kedy je to novinka',
  `border_a` varchar(15) COLLATE utf8_bin DEFAULT NULL COMMENT 'Okraj A: ffffff|hhh f-farba h-hrúbka',
  `border_b` varchar(15) COLLATE utf8_bin DEFAULT NULL COMMENT 'Okraj B: ffffff|hhh f-farba h-hrúbka',
  `border_c` varchar(15) COLLATE utf8_bin DEFAULT NULL COMMENT 'Okraj C: ffffff|hhh f-farba h-hrúbka',
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
  CONSTRAINT `hlavne_menu_ibfk_10` FOREIGN KEY (`id_user_roles`) REFERENCES `user_roles` (`id`),
  CONSTRAINT `hlavne_menu_ibfk_11` FOREIGN KEY (`id_hlavne_menu_opravnenie`) REFERENCES `hlavne_menu_opravnenie` (`id`),
  CONSTRAINT `hlavne_menu_ibfk_12` FOREIGN KEY (`id_hlavne_menu_template`) REFERENCES `hlavne_menu_template` (`id`),
  CONSTRAINT `hlavne_menu_ibfk_2` FOREIGN KEY (`id_ikonka`) REFERENCES `ikonka` (`id`),
  CONSTRAINT `hlavne_menu_ibfk_4` FOREIGN KEY (`id_hlavicka`) REFERENCES `hlavicka` (`id`),
  CONSTRAINT `hlavne_menu_ibfk_5` FOREIGN KEY (`id_hlavne_menu_cast`) REFERENCES `hlavne_menu_cast` (`id`),
  CONSTRAINT `hlavne_menu_ibfk_6` FOREIGN KEY (`id_druh`) REFERENCES `druh` (`id`),
  CONSTRAINT `hlavne_menu_ibfk_8` FOREIGN KEY (`id_dlzka_novinky`) REFERENCES `dlzka_novinky` (`id`),
  CONSTRAINT `hlavne_menu_ibfk_9` FOREIGN KEY (`id_user_main`) REFERENCES `user_main` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Položky hlavného menu';

INSERT INTO `hlavne_menu` (`id`, `spec_nazov`, `id_hlavne_menu_cast`, `id_user_roles`, `id_ikonka`, `id_druh`, `uroven`, `id_nadradenej`, `id_user_main`, `poradie`, `poradie_podclankov`, `id_hlavicka`, `id_hlavne_menu_opravnenie`, `zvyrazni`, `pocitadlo`, `nazov_ul_sub`, `id_hlavne_menu_template`, `absolutna`, `ikonka`, `avatar`, `komentar`, `modified`, `datum_platnosti`, `aktualny_projekt`, `redirect_id`, `id_dlzka_novinky`, `border_a`, `border_b`, `border_c`) VALUES
(1,	'bw-fotografia',	1,	0,	NULL,	1,	0,	NULL,	2,	1,	0,	1,	0,	0,	0,	NULL,	3,	NULL,	NULL,	'zqqzqharq0m1h9j.jpg',	0,	'2017-11-03 07:56:48',	NULL,	0,	NULL,	1,	NULL,	NULL,	NULL),
(2,	'architekura',	1,	0,	NULL,	1,	0,	NULL,	2,	3,	0,	1,	0,	0,	0,	NULL,	3,	NULL,	NULL,	'93eca27ct6k1szj.jpg',	0,	'2017-11-03 07:56:59',	NULL,	0,	NULL,	1,	NULL,	NULL,	NULL),
(3,	'foto-rgb',	1,	0,	NULL,	1,	0,	NULL,	2,	5,	0,	1,	0,	0,	0,	NULL,	3,	NULL,	NULL,	'jlhbvumzttwtah7.jpg',	0,	'2017-11-03 07:57:10',	NULL,	0,	NULL,	1,	NULL,	NULL,	NULL),
(4,	'produkty',	1,	0,	NULL,	1,	0,	NULL,	2,	2,	0,	2,	0,	0,	0,	NULL,	1,	NULL,	NULL,	'5663at38ptotbps.png',	0,	'2017-11-03 07:50:20',	NULL,	0,	NULL,	1,	NULL,	NULL,	NULL),
(5,	'novinky',	1,	0,	NULL,	1,	0,	NULL,	2,	4,	0,	2,	0,	0,	0,	NULL,	1,	NULL,	NULL,	'adpkihqqxy4pihf.png',	0,	'2017-11-03 07:49:47',	NULL,	0,	NULL,	1,	NULL,	NULL,	NULL),
(6,	'vystavy',	1,	0,	NULL,	1,	0,	NULL,	2,	6,	0,	2,	0,	0,	0,	NULL,	1,	NULL,	NULL,	'hfgdcjheatw7xe8.png',	0,	'2017-11-03 07:50:38',	NULL,	0,	NULL,	1,	NULL,	NULL,	NULL),
(9,	'cast-a-1',	1,	0,	NULL,	1,	1,	2,	2,	1,	0,	1,	0,	0,	0,	NULL,	2,	NULL,	NULL,	'3zjqsfzl5baff44.jpg',	0,	'2017-10-05 05:42:11',	NULL,	0,	NULL,	1,	NULL,	NULL,	NULL),
(10,	'cast-a-2',	1,	0,	NULL,	1,	1,	2,	2,	2,	0,	1,	0,	0,	0,	NULL,	2,	NULL,	NULL,	'qejapifs8ada1do.JPG',	0,	'2017-10-05 05:42:11',	NULL,	0,	NULL,	1,	NULL,	NULL,	NULL),
(11,	'obchodne-podmienky',	2,	0,	NULL,	1,	0,	NULL,	2,	1,	0,	0,	0,	0,	0,	NULL,	1,	NULL,	NULL,	NULL,	0,	'2017-10-05 05:54:09',	NULL,	0,	NULL,	1,	NULL,	NULL,	NULL),
(12,	'kontakt',	2,	0,	NULL,	1,	0,	NULL,	2,	2,	0,	0,	0,	0,	0,	NULL,	1,	NULL,	NULL,	'x8jx3wr9rxs47fc.jpg',	0,	'2018-07-19 09:19:35',	NULL,	0,	NULL,	1,	NULL,	NULL,	NULL),
(13,	'kalibracia',	2,	0,	NULL,	1,	0,	NULL,	2,	3,	0,	0,	0,	0,	0,	NULL,	1,	NULL,	NULL,	NULL,	0,	'2017-10-05 06:14:20',	NULL,	0,	NULL,	1,	NULL,	NULL,	NULL),
(14,	'homepage',	3,	0,	NULL,	1,	0,	NULL,	2,	1,	0,	0,	0,	0,	0,	NULL,	4,	NULL,	NULL,	NULL,	0,	'2017-11-23 11:49:33',	NULL,	0,	NULL,	1,	NULL,	NULL,	NULL),
(15,	'homepage-dole',	3,	0,	NULL,	1,	0,	NULL,	2,	2,	0,	0,	0,	0,	0,	NULL,	5,	NULL,	NULL,	NULL,	0,	'2017-11-23 11:49:33',	NULL,	0,	NULL,	1,	NULL,	NULL,	NULL),
(16,	'cast-a-3',	1,	0,	NULL,	1,	1,	2,	2,	3,	0,	1,	0,	0,	0,	NULL,	2,	NULL,	NULL,	'kp0x78f0j5xot9x.jpg',	0,	'2017-11-23 11:49:33',	NULL,	0,	NULL,	1,	NULL,	NULL,	NULL),
(17,	'cast-a-4',	1,	0,	NULL,	1,	1,	2,	2,	4,	0,	1,	0,	0,	0,	NULL,	2,	NULL,	NULL,	'29pxn9zg3h8mk1s.jpg',	0,	'2017-11-23 11:49:33',	NULL,	0,	NULL,	1,	NULL,	NULL,	NULL),
(18,	'cast-a-5',	1,	0,	NULL,	1,	1,	2,	2,	5,	0,	1,	0,	0,	0,	NULL,	2,	NULL,	NULL,	NULL,	0,	'2017-11-23 11:49:33',	NULL,	0,	NULL,	1,	NULL,	NULL,	NULL),
(19,	'cast-a-1-1',	1,	0,	NULL,	1,	2,	9,	2,	1,	0,	1,	0,	0,	0,	NULL,	2,	NULL,	NULL,	NULL,	0,	'2017-11-23 11:49:33',	NULL,	0,	NULL,	1,	NULL,	NULL,	NULL),
(20,	'krajina',	1,	0,	NULL,	1,	1,	3,	2,	1,	0,	1,	0,	0,	0,	NULL,	2,	NULL,	NULL,	'n6ulnlj97dzydn2.JPG',	0,	'2017-11-23 11:49:33',	NULL,	0,	NULL,	1,	NULL,	NULL,	NULL),
(21,	'kostoly',	1,	0,	NULL,	1,	1,	3,	2,	2,	0,	1,	0,	0,	0,	NULL,	2,	NULL,	NULL,	'hi0a9wovlqrbgdv.JPG',	0,	'2017-11-23 11:49:33',	NULL,	0,	NULL,	1,	NULL,	NULL,	NULL),
(22,	'stromy',	1,	0,	NULL,	1,	1,	3,	2,	3,	0,	1,	0,	0,	0,	NULL,	2,	NULL,	NULL,	'lebld9nagwmbdn3.JPG',	0,	'2018-01-16 10:34:03',	NULL,	0,	NULL,	1,	'#e5e5e5|3',	'#454545|5',	'#9d9d9d|2'),
(23,	'budovy',	1,	0,	NULL,	1,	1,	3,	2,	4,	0,	1,	0,	0,	0,	NULL,	2,	NULL,	NULL,	'rubrr55ww87q8z2.JPG',	0,	'2017-12-27 11:24:02',	NULL,	0,	NULL,	1,	'#525252|1',	'#5063e4|2',	'#9b9b00|3'),
(24,	'protisvetlo',	1,	0,	NULL,	1,	1,	3,	2,	5,	0,	1,	0,	0,	0,	NULL,	2,	NULL,	NULL,	'9e1gm84fckjtsos.JPG',	0,	'2017-11-23 11:49:33',	NULL,	0,	NULL,	1,	NULL,	NULL,	NULL);

DROP TABLE IF EXISTS `hlavne_menu_cast`;
CREATE TABLE `hlavne_menu_cast` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `view_name` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT 'Časť' COMMENT 'Názov časti',
  `id_user_roles` int(11) NOT NULL DEFAULT '5' COMMENT 'Id min úrovne registrácie pre editáciu',
  `mapa_stranky` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Ak 1 tak je časť zahrnutá do mapy',
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
  `id_lang` int(11) NOT NULL DEFAULT '1' COMMENT 'Id Jazyka',
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
(17,	1,	17,	17,	'Časť A-4',	NULL,	'Časť A-4'),
(18,	1,	18,	18,	'Časť A-5',	NULL,	'Časť A-5'),
(19,	1,	19,	19,	'Časť A-1-1',	NULL,	'Časť A-1-1'),
(20,	1,	20,	20,	'Krajina',	NULL,	'Krajina'),
(21,	1,	21,	21,	'Kostoly',	NULL,	'Kostoly'),
(22,	1,	22,	22,	'Stromy',	NULL,	'Stromy'),
(23,	1,	23,	23,	'Budovy',	NULL,	'Budovy'),
(24,	1,	24,	24,	'Protisvetlo',	NULL,	'Protisvetlo');

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
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Dátum novinky',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS `oznam`;
CREATE TABLE `oznam` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `id_user_main` int(11) NOT NULL DEFAULT '1' COMMENT 'Id užívateľa',
  `id_user_roles` int(11) NOT NULL DEFAULT '0' COMMENT 'Id min úrovne registrácie pre zobrazenie',
  `id_ikonka` int(11) DEFAULT NULL COMMENT 'Id použitej ikonky',
  `datum_platnosti` date NOT NULL DEFAULT '0000-00-00' COMMENT 'Dátum platnosti',
  `datum_zadania` date NOT NULL DEFAULT '0000-00-00' COMMENT 'Dátum zadania oznamu',
  `nazov` varchar(50) DEFAULT NULL COMMENT 'Názov oznamu',
  `text` text COMMENT 'Text oznamu',
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

INSERT INTO `products` (`id`, `id_hlavne_menu`, `id_user_main`, `id_user_roles`, `name`, `web_name`, `description`, `main_file`, `thumb_file`, `change`) VALUES
(1,	23,	1,	0,	'budovy11',	'budovy11',	NULL,	'www/files/prilohy/budovy11.JPG',	'www/files/prilohy/tb_budovy11.JPG',	'2018-01-02 09:40:44'),
(10,	23,	1,	0,	'Naša búda',	'dedina12',	'adf ysdfdsf yxvyyasdfgasd ysdfgysy sd dsxfysdydfv afgsd a a',	'www/files/prilohy/dedina12.JPG',	'www/files/prilohy/tb_dedina12.JPG',	'2018-01-03 07:26:17'),
(11,	23,	1,	0,	'dedina11 sdfg',	'dedina11',	'ghjgh dfdd',	'www/files/prilohy/dedina11.JPG',	'www/files/prilohy/tb_dedina11.JPG',	'2018-01-03 07:28:39'),
(15,	22,	1,	0,	'krajina1',	'krajina1',	NULL,	'www/files/prilohy/krajina1.JPG',	'www/files/prilohy/tb_krajina1.JPG',	'2018-01-03 08:13:05'),
(16,	22,	1,	0,	'obr07 yssd',	'obr07',	'dfgdf  ffxd ',	'www/files/prilohy/obr07.jpg',	'www/files/prilohy/tb_obr07.jpg',	'2018-01-16 10:32:49');

DROP TABLE IF EXISTS `slider`;
CREATE TABLE `slider` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `poradie` int(11) NOT NULL DEFAULT '1' COMMENT 'Určuje poradie obrázkov v slidery',
  `nadpis` varchar(50) COLLATE utf8_bin DEFAULT NULL COMMENT 'Nadpis obrázku',
  `popis` varchar(150) COLLATE utf8_bin DEFAULT NULL COMMENT 'Popis obrázku slideru vypisovaný v dolnej časti',
  `subor` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '*.jpg' COMMENT 'Názov obrázku slideru aj s relatívnou cestou',
  `zobrazenie` varchar(20) COLLATE utf8_bin DEFAULT NULL COMMENT 'Kedy sa obrázok zobrazí',
  `id_hlavne_menu` int(11) DEFAULT NULL COMMENT 'Odkaz na položku hlavného menu',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Popis obrázkou slideru aj s názvami súborov';

INSERT INTO `slider` (`id`, `poradie`, `nadpis`, `popis`, `subor`, `zobrazenie`, `id_hlavne_menu`) VALUES
(6,	1,	'',	'',	'bg-bw.png',	NULL,	NULL),
(7,	2,	'',	'',	'bg-bwb.png',	'2',	NULL);

DROP TABLE IF EXISTS `udaje`;
CREATE TABLE `udaje` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `id_user_roles` int(11) NOT NULL DEFAULT '5' COMMENT 'Id min úrovne pre editáciu',
  `id_druh` int(11) DEFAULT NULL COMMENT 'Druhová skupina pre nastavenia',
  `id_udaje_typ` int(11) NOT NULL DEFAULT '1' COMMENT 'Typ input-u',
  `nazov` varchar(30) COLLATE utf8_bin NOT NULL DEFAULT 'nazov' COMMENT 'Názov prvku',
  `text` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT 'Definícia' COMMENT 'Hodnota prvku',
  `comment` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT 'Komentár k hodnote',
  `separate_settings` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Ak 1 tak má položka vlastnú časť nastavení',
  PRIMARY KEY (`id`),
  KEY `id_reg` (`id_user_roles`),
  KEY `id_druh` (`id_druh`),
  KEY `id_udaje_typ` (`id_udaje_typ`),
  CONSTRAINT `udaje_ibfk_2` FOREIGN KEY (`id_druh`) REFERENCES `druh` (`id`),
  CONSTRAINT `udaje_ibfk_3` FOREIGN KEY (`id_udaje_typ`) REFERENCES `udaje_typ` (`id`),
  CONSTRAINT `udaje_ibfk_4` FOREIGN KEY (`id_user_roles`) REFERENCES `user_roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Tabuľka na uschovanie základných údajov o stránke';

INSERT INTO `udaje` (`id`, `id_user_roles`, `id_druh`, `id_udaje_typ`, `nazov`, `text`, `comment`, `separate_settings`) VALUES
(1,	5,	NULL,	1,	'titulka-sk',	'BWfoto',	'Názov zobrazený v titulke',	0),
(2,	5,	NULL,	1,	'titulka_2-sk',	'',	'Druhá časť titulky pre jazyk: sk',	0),
(3,	5,	NULL,	1,	'titulka_citat_enable',	'0',	'Povolenie zobrazenia citátu',	0),
(4,	5,	NULL,	1,	'titulka_citat_podpis',	'',	'Podpis pod citát na titulke',	0),
(5,	5,	NULL,	1,	'titulka_citat-sk',	'',	'Text citátu, ktorý sa zobrazí na titulke pre jazyk: sk',	0),
(6,	5,	NULL,	1,	'keywords-sk',	'BWfoto, Spišské Bystré, fotograf',	'Kľúčové slová',	0),
(7,	5,	NULL,	1,	'autor',	'Ing. Peter VOJTECH ml. - VZ',	'Autor stránky',	0),
(8,	5,	NULL,	1,	'log_out-sk',	'Odhlás sa...',	'Text pre odkaz na odhlásenie sa',	0),
(9,	5,	NULL,	1,	'log_in-sk',	'Prihlás sa',	'Text pre odkaz na prihlásenie sa',	0),
(10,	5,	NULL,	1,	'forgot_password-sk',	'Zabudnuté heslo?',	'Text pre odkaz na zabudnuté heslo',	0),
(11,	5,	NULL,	1,	'register-sk',	'Registrácia',	'Text pre odkaz na registráciu',	0),
(12,	5,	NULL,	1,	'last_update-sk',	'Posledná aktualizácia',	'Text pre odkaz na poslednú aktualizáciu',	0),
(13,	4,	NULL,	1,	'spravca-sk',	'Správca obsahu',	'Text pre odkaz na správcu',	0),
(14,	4,	NULL,	1,	'copy',	'BWfoto',	'Text, ktorý sa vypíše za znakom copyright-u',	0),
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
(32,	4,	8,	4,	'product_thumb_quality',	'70',	'Kvalita kompresie náhľadového obrázku[1,100]',	1);

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
  `id_user_roles` int(11) NOT NULL DEFAULT '0' COMMENT 'Úroveň registrácie a rola',
  `id_user_profiles` int(11) DEFAULT NULL COMMENT 'Užívateľský profil',
  `password` varchar(255) COLLATE utf8_bin NOT NULL COMMENT 'Heslo',
  `meno` varchar(50) COLLATE utf8_bin NOT NULL COMMENT 'Meno',
  `priezvisko` varchar(50) COLLATE utf8_bin NOT NULL COMMENT 'Priezvisko',
  `email` varchar(100) COLLATE utf8_bin NOT NULL COMMENT 'Email',
  `activated` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Aktivácia',
  `banned` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Zazázaný',
  `ban_reason` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT 'Dôvod zákazu',
  `new_password_key` varchar(100) COLLATE utf8_bin DEFAULT NULL COMMENT 'Kľúč nového hesla',
  `new_password_requested` datetime DEFAULT NULL COMMENT 'Čas požiadavky na nové heslo',
  `new_email` varchar(100) COLLATE utf8_bin DEFAULT NULL COMMENT 'Nový email',
  `new_email_key` varchar(100) COLLATE utf8_bin DEFAULT NULL COMMENT 'Kľúč nového emailu',
  `last_ip` varchar(40) COLLATE utf8_bin DEFAULT NULL COMMENT 'Posledná IP',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Vytvorenie užívateľa',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Posledná zmena',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `id_registracia` (`id_user_roles`),
  KEY `id_user_profiles` (`id_user_profiles`),
  CONSTRAINT `user_main_ibfk_2` FOREIGN KEY (`id_user_profiles`) REFERENCES `user_profiles` (`id`) ON DELETE SET NULL,
  CONSTRAINT `user_main_ibfk_3` FOREIGN KEY (`id_user_roles`) REFERENCES `user_roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Hlavné údaje užívateľa';

INSERT INTO `user_main` (`id`, `id_user_roles`, `id_user_profiles`, `password`, `meno`, `priezvisko`, `email`, `activated`, `banned`, `ban_reason`, `new_password_key`, `new_password_requested`, `new_email`, `new_email_key`, `last_ip`, `created`, `modified`) VALUES
(1,	5,	1,	'$2y$10$RnzAjUCyc/B1GgiJ9k43/e27BDz5j1vsbN.DYlfnXIxweBvqxkABq',	'Peter',	'Vojtech',	'petak23@gmail.com',	1,	0,	NULL,	NULL,	NULL,	NULL,	NULL,	'::1',	'2017-05-15 09:11:19',	'2018-01-15 10:00:41'),
(2,	4,	2,	'$2y$10$FvhA/KkVmKR4lrVftRYch.4ER1Lc4H6sl0/NK5VuJ5keSrMg8kbay',	'Juraj',	'Zámečník',	'bwfoto@bwfoto.sk',	1,	0,	NULL,	NULL,	NULL,	NULL,	NULL,	'::1',	'2017-05-15 09:13:38',	'2018-01-22 06:42:42'),
(3,	4,	3,	'$2y$10$VOeK4y3ozjaUM1aMtiVmcuHRmtcmoVvC6J4yFX4j0LZoNbXlejyMi',	'Jozef',	'Petrenčík',	'jozue@anigraph.eu',	1,	0,	NULL,	NULL,	NULL,	NULL,	NULL,	'178.253.139.152',	'2017-05-15 09:12:22',	'2017-07-11 07:10:29');

DROP TABLE IF EXISTS `user_permission`;
CREATE TABLE `user_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `id_user_roles` int(11) NOT NULL DEFAULT '0' COMMENT 'Užívateľská rola',
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
(29,	2,	'2018-01-22 07:42:42');

DROP TABLE IF EXISTS `user_profiles`;
CREATE TABLE `user_profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rok` int(11) DEFAULT NULL COMMENT 'Rok narodenia',
  `telefon` varchar(30) COLLATE utf8_bin DEFAULT NULL COMMENT 'Telefón',
  `poznamka` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT 'Poznámka',
  `pocet_pr` int(11) NOT NULL DEFAULT '0' COMMENT 'Počet prihlásení',
  `pohl` enum('Z','M') COLLATE utf8_bin NOT NULL DEFAULT 'M' COMMENT 'Pohlavie',
  `prihlas_teraz` datetime DEFAULT NULL COMMENT 'Posledné prihlásenie',
  `avatar` varchar(200) COLLATE utf8_bin DEFAULT NULL COMMENT 'Cesta k avatarovi veľkosti 75x75',
  `news` enum('A','N') COLLATE utf8_bin NOT NULL DEFAULT 'A' COMMENT 'Posielanie info emailou',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `user_profiles` (`id`, `rok`, `telefon`, `poznamka`, `pocet_pr`, `pohl`, `prihlas_teraz`, `avatar`, `news`) VALUES
(1,	NULL,	NULL,	NULL,	23,	'M',	'2018-01-15 11:00:41',	NULL,	'A'),
(2,	NULL,	NULL,	NULL,	6,	'M',	'2018-01-22 07:42:42',	NULL,	'A'),
(3,	NULL,	NULL,	NULL,	0,	'M',	NULL,	NULL,	'A');

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
(25,	'Admin:Products');

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
  `id_user_main` int(11) NOT NULL DEFAULT '1' COMMENT 'Id užívateľa',
  `cislo` varchar(10) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT 'Číslo verzie',
  `subory` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT 'Zmenené súbory',
  `text` text COLLATE utf8_bin COMMENT 'Popis zmien',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Dátum a čas zmeny',
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
(6,	1,	'0.1.6',	'úroveň 3 a 4',	'- Prispôsobenie vzhľadu úrovne 3. a 4..\n- Aktualizácia nittra a lightbox-u.\n- Odstránenie koncovky z názvu prílohy.\n- Práca na admin časti pre rámčeky - pridaná časť pre fotoalbum zmeny rámčekov obrázkových príloh v úrovni 4. pre každú časť zvlášť.\n- Prispôsobenie 4. úrovne pre prehliadanie obrázkov s rámčekmi.',	'2017-12-27 12:04:06');

-- 2018-07-19 10:26:04
