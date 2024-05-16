-- Adminer 4.8.1 MySQL 10.5.24-MariaDB-1:10.5.24+maria~ubu2004-log dump

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
  PRIMARY KEY (`id`),
  KEY `id_registracia` (`id_user_roles`),
  CONSTRAINT `admin_menu_ibfk_2` FOREIGN KEY (`id_user_roles`) REFERENCES `user_roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Administračné menu';


DROP TABLE IF EXISTS `clanok_komponenty`;
CREATE TABLE `clanok_komponenty` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `id_hlavne_menu` int(11) NOT NULL COMMENT 'Id hl. menu, ktorému je komponenta pripojená',
  `spec_nazov` varchar(30) DEFAULT NULL COMMENT 'Špecifický názov komponenty',
  `parametre` varchar(100) DEFAULT NULL COMMENT 'Parametre komponenty',
  PRIMARY KEY (`id`),
  KEY `id_clanok` (`id_hlavne_menu`),
  CONSTRAINT `clanok_komponenty_ibfk_3` FOREIGN KEY (`id_hlavne_menu`) REFERENCES `hlavne_menu` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Zoznam komponent, ktoré sú priradené k článku';


DROP TABLE IF EXISTS `dlzka_novinky`;
CREATE TABLE `dlzka_novinky` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `nazov` varchar(30) NOT NULL COMMENT 'Zobrazený názov',
  `dlzka` int(11) NOT NULL COMMENT 'Počet dní, v ktorých je novinka',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Tabuľka pre hodnoty dĺžky noviniek';


DROP TABLE IF EXISTS `dokumenty`;
CREATE TABLE `dokumenty` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_hlavne_menu` int(11) NOT NULL DEFAULT 1 COMMENT 'Id položky hl. menu ku ktorej patrí',
  `id_user_main` int(11) NOT NULL DEFAULT 1 COMMENT 'Id užívateľa',
  `id_user_roles` int(11) NOT NULL DEFAULT 0 COMMENT 'Id min úrovne registrácie pre zobrazenie',
  `znacka` varchar(20) DEFAULT NULL COMMENT 'Značka súboru pre vloženie do textu',
  `name` varchar(50) NOT NULL COMMENT 'Názov titulku pre daný dokument',
  `pripona` varchar(20) NOT NULL COMMENT 'Prípona súboru',
  `web_name` varchar(50) NOT NULL COMMENT 'Špecifický názov dokumentu pre URL',
  `description` varchar(255) DEFAULT NULL COMMENT 'Popis dokumentu',
  `main_file` varchar(255) NOT NULL COMMENT 'Názov súboru s relatívnou cestou',
  `thumb_file` varchar(255) DEFAULT NULL COMMENT 'Názov súboru thumb pre obrázky a iné ',
  `change` datetime /* mariadb-5.3 */ NOT NULL COMMENT 'Dátum uloženia alebo opravy - časová pečiatka',
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


DROP TABLE IF EXISTS `druh`;
CREATE TABLE `druh` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Id položiek',
  `druh` varchar(20) NOT NULL COMMENT 'Názov druhu stredného stĺpca',
  `modul` varchar(20) DEFAULT NULL COMMENT 'Názov špecifického modulu ak NULL vždy',
  `presenter` varchar(30) NOT NULL COMMENT 'Názov prezenteru pre Nette',
  `popis` varchar(255) DEFAULT 'Popis' COMMENT 'Popis bloku',
  `povolene` tinyint(4) NOT NULL DEFAULT 1 COMMENT 'Ak 1 tak daná položka je povolená',
  `je_spec_naz` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Ak 1 tak daný druh potrebuje špecif. názov',
  `robots` tinyint(4) NOT NULL DEFAULT 1 COMMENT 'Ak 1 tak je povolené indexovanie daného druhu',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS `faktury`;
CREATE TABLE `faktury` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_hlavne_menu` int(11) NOT NULL DEFAULT 1 COMMENT 'Ku ktorej položke patrí',
  `nazov` varchar(50) DEFAULT NULL COMMENT 'Názov titulku pre daný dokument',
  `cislo` varchar(20) DEFAULT NULL COMMENT 'Číslo faktúry, zmluvy, objednávky',
  `predmet` varchar(200) DEFAULT NULL COMMENT 'Predmet faktúry, zmluvy, objednávky',
  `cena` float(15,2) DEFAULT NULL COMMENT 'Cena faktúry, zmluvy, objednávky',
  `subjekt` varchar(200) DEFAULT NULL COMMENT 'Subjekt faktúry,  objednávky - (dodávateľ), zmluvy(Zmluvná strana)',
  `datum_vystavenia` date DEFAULT NULL COMMENT 'Dátum vystavenia pri faktúre a objednávke pri zmluve dátum uzatvorenia',
  `datum_ukoncenia` date DEFAULT NULL COMMENT 'Dátum ukoncenia zmluvy',
  `subor` varchar(50) NOT NULL COMMENT 'Názov súboru s relatívnou cestou',
  `id_user_main` int(11) NOT NULL DEFAULT 1 COMMENT 'Kto pridal dokument',
  `id_reg` int(11) NOT NULL DEFAULT 0 COMMENT 'Úroveň registrácie',
  `kedy` timestamp /* mariadb-5.3 */ NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Dátum uloženia alebo opravy - časová pečiatka',
  `pocitadlo` int(11) NOT NULL DEFAULT 0 COMMENT 'Počítadlo stiahnutí',
  `id_skupina` int(11) NOT NULL DEFAULT 0 COMMENT 'Id článku do ktorej časti dokument patrí',
  `id_rok` int(11) NOT NULL DEFAULT 0 COMMENT 'Id roku do ktorého sa má zaradiť',
  PRIMARY KEY (`id`),
  KEY `id_hlavne_menu` (`id_hlavne_menu`),
  KEY `id_user_profiles` (`id_user_main`),
  CONSTRAINT `faktury_ibfk_1` FOREIGN KEY (`id_hlavne_menu`) REFERENCES `hlavne_menu` (`id`),
  CONSTRAINT `faktury_ibfk_3` FOREIGN KEY (`id_user_main`) REFERENCES `user_main` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


SET NAMES utf8mb4;

DROP TABLE IF EXISTS `fotocollage_settings`;
CREATE TABLE `fotocollage_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `id_hlavne_menu` int(11) NOT NULL COMMENT 'Id prvku, ku ktorému je koláž',
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Nastavenia vo formáte json' CHECK (json_valid(`settings`)),
  PRIMARY KEY (`id`),
  KEY `id_hlavne_menu` (`id_hlavne_menu`),
  CONSTRAINT `fotocollage_settings_ibfk_1` FOREIGN KEY (`id_hlavne_menu`) REFERENCES `hlavne_menu` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_bin COMMENT='Nastavenia fotokoláže';


DROP TABLE IF EXISTS `hlavicka`;
CREATE TABLE `hlavicka` (
  `id` int(11) NOT NULL COMMENT '[A]Index',
  `nazov` varchar(40) NOT NULL DEFAULT 'Veľká' COMMENT 'Zobrazený názov pre daný typ hlavičky',
  `pripona` varchar(10) DEFAULT NULL COMMENT 'Prípona názvu súborov',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS `hlavne_menu`;
CREATE TABLE `hlavne_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[5]Id položky hlavného menu',
  `spec_nazov` varchar(100) DEFAULT NULL COMMENT 'Názov položky menu pre URL',
  `id_hlavne_menu_cast` int(11) NOT NULL DEFAULT 1 COMMENT '[5]Ku ktorej časti hl. menu patrí položka',
  `id_user_roles` int(11) NOT NULL DEFAULT 0 COMMENT 'Id min úrovne registrácie pre zobrazenie',
  `id_ikonka` int(11) DEFAULT NULL COMMENT '[4]Názov súboru ikonky aj s koncovkou',
  `id_druh` int(11) NOT NULL DEFAULT 1 COMMENT '[5]Výber druhu priradenej položky. Ak 1 tak je možné priradiť článok v náväznosti na tab. druh',
  `uroven` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Úroveň položky menu',
  `id_nadradenej` int(11) DEFAULT NULL COMMENT 'Id nadradenej položky menu z tejto tabuľky ',
  `id_user_main` int(11) NOT NULL DEFAULT 1 COMMENT 'Id užívateľa',
  `poradie` int(11) NOT NULL DEFAULT 1 COMMENT 'Poradie v zobrazení',
  `id_hlavicka` int(11) NOT NULL DEFAULT 0 COMMENT '[5]Druh hlavičky podľa tabuľky hlavicka. 1 - velka',
  `id_hlavne_menu_opravnenie` int(11) NOT NULL DEFAULT 0 COMMENT 'Povolenie pre nevlastníkov (0-žiadne,1- podčlánky,2-editacia,4-všetko)',
  `zvyrazni` tinyint(4) NOT NULL DEFAULT 0 COMMENT '[5]Zvýraznenie položky menu pri pridaní obsahu',
  `pocitadlo` int(11) NOT NULL DEFAULT 0 COMMENT '[R]Počítadlo kliknutí na položku',
  `nazov_ul_sub` varchar(20) DEFAULT NULL COMMENT '[5]Názov pomocnej triedy ul-elsementu sub menu',
  `id_hlavne_menu_template` int(11) NOT NULL DEFAULT 1 COMMENT 'Vzhľad šablóny',
  `absolutna` varchar(100) DEFAULT NULL COMMENT 'Absolútna adresa',
  `ikonka` varchar(30) DEFAULT NULL COMMENT 'Názov css ikonky',
  `avatar` varchar(300) DEFAULT NULL COMMENT 'Názov a cesta k titulnému obrázku',
  `komentar` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Povolenie komentárov',
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Posledná zmena',
  `datum_platnosti` date DEFAULT NULL COMMENT 'Platnosť',
  `aktualny_projekt` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Označenie aktuálneho projektu',
  `redirect_id` int(11) DEFAULT NULL COMMENT 'Id článku na ktorý sa má presmerovať',
  `id_dlzka_novinky` int(11) NOT NULL DEFAULT 1 COMMENT 'Do kedy je to novinka',
  `border_a` varchar(15) DEFAULT NULL COMMENT 'Okraj A: ffffff|hhh f-farba h-hrúbka',
  `border_b` varchar(15) DEFAULT NULL COMMENT 'Okraj B: ffffff|hhh f-farba h-hrúbka',
  `border_c` varchar(15) DEFAULT NULL COMMENT 'Okraj C: ffffff|hhh f-farba h-hrúbka',
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


DROP TABLE IF EXISTS `hlavne_menu_cast`;
CREATE TABLE `hlavne_menu_cast` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `view_name` varchar(50) NOT NULL DEFAULT 'Časť' COMMENT 'Názov časti',
  `id_user_roles` int(11) NOT NULL DEFAULT 5 COMMENT 'Id min úrovne registrácie pre editáciu',
  `mapa_stranky` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Ak 1 tak je časť zahrnutá do mapy',
  PRIMARY KEY (`id`),
  KEY `id_registracia` (`id_user_roles`),
  CONSTRAINT `hlavne_menu_cast_ibfk_2` FOREIGN KEY (`id_user_roles`) REFERENCES `user_roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Časti hlavného menu';


DROP TABLE IF EXISTS `hlavne_menu_lang`;
CREATE TABLE `hlavne_menu_lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `id_lang` int(11) NOT NULL DEFAULT 1 COMMENT 'Id Jazyka',
  `id_hlavne_menu` int(11) NOT NULL COMMENT 'Id hlavného menu, ku ktorému patrí',
  `menu_name` varchar(100) DEFAULT NULL COMMENT 'Názov položky v hlavnom menu pre daný jazyk',
  `h1part2` varchar(100) DEFAULT NULL COMMENT 'Druhá časť názvu pre daný jazyk',
  `view_name` varchar(255) DEFAULT NULL COMMENT 'Zobrazený názov položky pre daný jazyk',
  `text` text DEFAULT NULL COMMENT 'Text článku v danom jazyku',
  `text_c` text DEFAULT NULL COMMENT 'HTML Text článku v danom jazyku',
  `anotacia` varchar(255) DEFAULT NULL COMMENT 'Anotácia článku v danom jazyku',
  PRIMARY KEY (`id`),
  KEY `id_hlavne_menu` (`id_hlavne_menu`),
  KEY `id_lang` (`id_lang`),
  CONSTRAINT `hlavne_menu_lang_ibfk_1` FOREIGN KEY (`id_hlavne_menu`) REFERENCES `hlavne_menu` (`id`),
  CONSTRAINT `hlavne_menu_lang_ibfk_2` FOREIGN KEY (`id_lang`) REFERENCES `lang` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Popis položiek hlavného menu pre iný jazyk';


DROP TABLE IF EXISTS `hlavne_menu_opravnenie`;
CREATE TABLE `hlavne_menu_opravnenie` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `nazov` varchar(40) NOT NULL COMMENT 'Názov oprávnenia',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Oprávnenia nevlastníkov položiek hlavného menu';


DROP TABLE IF EXISTS `hlavne_menu_template`;
CREATE TABLE `hlavne_menu_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `name` varchar(20) NOT NULL COMMENT 'Názov vzhľadu',
  `description` varchar(100) NOT NULL COMMENT 'Popis vzhľadu',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Vzhľad šablón pre položky menu';


DROP TABLE IF EXISTS `ikonka`;
CREATE TABLE `ikonka` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `nazov` varchar(30) NOT NULL DEFAULT 'ikonka' COMMENT 'Kmeňová časť názvu súboru ikonky',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Ikonky';


DROP TABLE IF EXISTS `lang`;
CREATE TABLE `lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `skratka` varchar(3) NOT NULL DEFAULT 'sk' COMMENT 'Skratka jazyka',
  `nazov` varchar(15) NOT NULL DEFAULT 'Slovenčina' COMMENT 'Miestny názov jazyka',
  `nazov_en` varchar(15) NOT NULL DEFAULT 'Slovak' COMMENT 'Anglický názov jazyka',
  `prijaty` tinyint(4) DEFAULT NULL COMMENT 'Ak je > 0 jazyk je možné použiť na Frond',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Jazyky pre web';


DROP TABLE IF EXISTS `nakup`;
CREATE TABLE `nakup` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `id_user_main` int(11) NOT NULL COMMENT 'Id nákupujúceho',
  `products` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Nakúpené produkty' CHECK (json_valid(`products`)),
  `shipping` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Údaje o doprave a platbe' CHECK (json_valid(`shipping`)),
  `created` datetime NOT NULL COMMENT 'Dátum vytvorenia nákupu',
  `price` float NOT NULL DEFAULT 0 COMMENT 'Konečná cena nákupu',
  `code` varchar(10) NOT NULL COMMENT 'Kód objednávky a variabilný symbol',
  `id_nakup_status` int(11) NOT NULL DEFAULT 1 COMMENT 'Aktuálny stav nákupu',
  PRIMARY KEY (`id`),
  KEY `id_user_main` (`id_user_main`),
  KEY `id_nakup_status` (`id_nakup_status`),
  CONSTRAINT `nakup_ibfk_1` FOREIGN KEY (`id_user_main`) REFERENCES `user_main` (`id`),
  CONSTRAINT `nakup_ibfk_2` FOREIGN KEY (`id_nakup_status`) REFERENCES `nakup_status` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_bin;


DROP TABLE IF EXISTS `nakup_status`;
CREATE TABLE `nakup_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `name` varchar(30) NOT NULL COMMENT 'Status nákupu',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_bin COMMENT='Zoznam statusov nákupu';


DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `text` text NOT NULL COMMENT 'Text novinky',
  `created` timestamp /* mariadb-5.3 */ NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Dátum novinky',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Oznamy';


DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A] Index',
  `id_hlavne_menu` int(11) NOT NULL DEFAULT 1 COMMENT 'Id položky hl. menu ku ktorej patrí',
  `id_user_main` int(11) NOT NULL DEFAULT 1 COMMENT 'Id užívateľa',
  `id_user_roles` int(11) NOT NULL DEFAULT 0 COMMENT 'Id min úrovne registrácie pre zobrazenie',
  `name` varchar(50) NOT NULL COMMENT 'Názov pre daný produkt',
  `web_name` varchar(50) NOT NULL COMMENT 'Špecifický názov produktu pre URL',
  `description` varchar(255) DEFAULT NULL COMMENT 'Popis produktu',
  `main_file` varchar(255) NOT NULL COMMENT 'Názov súboru produktu s relatívnou cestou',
  `thumb_file` varchar(255) DEFAULT NULL COMMENT 'Názov súboru náhľadu pre obrázky a iné',
  `change` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Čas zmeny',
  `saved` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Ak 1 položka bola korektne nahratá na server',
  `price` float NOT NULL DEFAULT 0 COMMENT 'Cena produktu v €',
  `ks` int(11) NOT NULL DEFAULT 1 COMMENT 'Počet kusov produktu',
  `id_products_status` int(11) NOT NULL DEFAULT 1 COMMENT 'Status produktu',
  PRIMARY KEY (`id`),
  KEY `id_hlavne_menu` (`id_hlavne_menu`),
  KEY `id_user_main` (`id_user_main`),
  KEY `id_user_roles` (`id_user_roles`),
  KEY `id_products_status` (`id_products_status`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`id_hlavne_menu`) REFERENCES `hlavne_menu` (`id`),
  CONSTRAINT `products_ibfk_2` FOREIGN KEY (`id_user_main`) REFERENCES `user_main` (`id`),
  CONSTRAINT `products_ibfk_3` FOREIGN KEY (`id_user_roles`) REFERENCES `user_roles` (`id`),
  CONSTRAINT `products_ibfk_4` FOREIGN KEY (`id_products_status`) REFERENCES `products_status` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_bin COMMENT='Produkty';


DROP TABLE IF EXISTS `products_property`;
CREATE TABLE `products_property` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `id_products` int(11) NOT NULL COMMENT 'Id produktu',
  `id_property` int(11) NOT NULL COMMENT 'Id vlastnosti',
  PRIMARY KEY (`id`),
  KEY `id_products` (`id_products`),
  KEY `id_property` (`id_property`),
  CONSTRAINT `products_property_ibfk_1` FOREIGN KEY (`id_products`) REFERENCES `products` (`id`),
  CONSTRAINT `products_property_ibfk_2` FOREIGN KEY (`id_property`) REFERENCES `property` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_bin;


DROP TABLE IF EXISTS `products_status`;
CREATE TABLE `products_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `name` varchar(50) NOT NULL COMMENT 'Názov statusu',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_bin;


DROP TABLE IF EXISTS `property`;
CREATE TABLE `property` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `name` varchar(100) CHARACTER SET utf32 COLLATE utf32_slovak_ci NOT NULL COMMENT 'Názov vlastnosti',
  `id_property_categories` int(11) NOT NULL COMMENT 'Kategória vlastnosti',
  `price_increase_percentage` float DEFAULT NULL COMMENT 'Navýšenie ceny o percento',
  `price_increase_price` float DEFAULT NULL COMMENT 'Navýšenie ceny o sumu',
  PRIMARY KEY (`id`),
  KEY `id_property_categories` (`id_property_categories`),
  CONSTRAINT `property_ibfk_1` FOREIGN KEY (`id_property_categories`) REFERENCES `property_categories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_bin COMMENT='Vlastnosti produktu';


DROP TABLE IF EXISTS `property_categories`;
CREATE TABLE `property_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `name` varchar(50) NOT NULL COMMENT 'Názov kategórie vlastností',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_bin COMMENT='Kategórie vlastností';


DROP TABLE IF EXISTS `slider`;
CREATE TABLE `slider` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `poradie` int(11) NOT NULL DEFAULT 1 COMMENT 'Určuje poradie obrázkov v slidery',
  `nadpis` varchar(50) DEFAULT NULL COMMENT 'Nadpis obrázku',
  `popis` varchar(150) DEFAULT NULL COMMENT 'Popis obrázku slideru vypisovaný v dolnej časti',
  `subor` varchar(50) NOT NULL DEFAULT '*.jpg' COMMENT 'Názov obrázku slideru aj s relatívnou cestou',
  `zobrazenie` varchar(200) DEFAULT NULL COMMENT 'Kedy sa obrázok zobrazí',
  `id_hlavne_menu` int(11) DEFAULT NULL COMMENT 'Odkaz na položku hlavného menu',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Popis obrázkou slideru aj s názvami súborov';


DROP TABLE IF EXISTS `udaje`;
CREATE TABLE `udaje` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `id_user_roles` int(11) NOT NULL DEFAULT 5 COMMENT 'Id min úrovne pre editáciu',
  `id_user_main` int(11) DEFAULT NULL COMMENT 'Id užívateľa, pre ktorého toto nastavenie platí.',
  `id_druh` int(11) DEFAULT NULL COMMENT 'Druhová skupina pre nastavenia',
  `id_udaje_typ` int(11) NOT NULL DEFAULT 1 COMMENT 'Typ input-u',
  `nazov` varchar(30) NOT NULL DEFAULT 'nazov' COMMENT 'Názov prvku',
  `text` varchar(255) NOT NULL DEFAULT 'Definícia' COMMENT 'Hodnota prvku',
  `comment` varchar(255) DEFAULT NULL COMMENT 'Komentár k hodnote',
  `separate_settings` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Ak 1 tak má položka vlastnú časť nastavení',
  PRIMARY KEY (`id`),
  KEY `id_reg` (`id_user_roles`),
  KEY `id_druh` (`id_druh`),
  KEY `id_udaje_typ` (`id_udaje_typ`),
  KEY `id_user_main` (`id_user_main`),
  CONSTRAINT `udaje_ibfk_2` FOREIGN KEY (`id_druh`) REFERENCES `druh` (`id`),
  CONSTRAINT `udaje_ibfk_3` FOREIGN KEY (`id_udaje_typ`) REFERENCES `udaje_typ` (`id`),
  CONSTRAINT `udaje_ibfk_4` FOREIGN KEY (`id_user_roles`) REFERENCES `user_roles` (`id`),
  CONSTRAINT `udaje_ibfk_5` FOREIGN KEY (`id_user_main`) REFERENCES `user_main` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Tabuľka na uschovanie základných údajov o stránke';


DROP TABLE IF EXISTS `udaje_typ`;
CREATE TABLE `udaje_typ` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `nazov` varchar(20) NOT NULL DEFAULT 'text' COMMENT 'Typ input-u pre danú položku',
  `comment` varchar(50) NOT NULL DEFAULT 'Text' COMMENT 'Popis navonok',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Typy prvkov pre tabuľku udaje';


DROP TABLE IF EXISTS `user_categories`;
CREATE TABLE `user_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A] Index',
  `name` varchar(60) NOT NULL COMMENT 'Názov',
  `shortcut` varchar(6) NOT NULL COMMENT 'Skratka',
  `main_category` enum('V','R','O') NOT NULL DEFAULT 'R' COMMENT 'Hlavný druh kategórie(V-Vedenie; R-rodičia; O-ostatné',
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
  `password` varchar(255) DEFAULT NULL COMMENT 'Heslo',
  `name` varchar(80) NOT NULL COMMENT 'Meno',
  `email` varchar(100) NOT NULL COMMENT 'Email',
  `activated` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Aktivácia',
  `banned` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Zazázaný',
  `ban_reason` varchar(255) DEFAULT NULL COMMENT 'Dôvod zákazu',
  `new_password_key` varchar(100) DEFAULT NULL COMMENT 'Kľúč nového hesla',
  `new_password_requested` datetime DEFAULT NULL COMMENT 'Čas požiadavky na nové heslo',
  `new_email` varchar(100) DEFAULT NULL COMMENT 'Nový email',
  `new_email_key` varchar(100) DEFAULT NULL COMMENT 'Kľúč nového emailu',
  `last_ip` varchar(40) DEFAULT NULL COMMENT 'Posledná IP',
  `created` datetime DEFAULT NULL COMMENT 'Vytvorenie užívateľa',
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Posledná zmena',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `id_registracia` (`id_user_roles`),
  KEY `id_user_profiles` (`id_user_profiles`),
  CONSTRAINT `user_main_ibfk_2` FOREIGN KEY (`id_user_profiles`) REFERENCES `user_profiles` (`id`) ON DELETE SET NULL,
  CONSTRAINT `user_main_ibfk_3` FOREIGN KEY (`id_user_roles`) REFERENCES `user_roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_bin COMMENT='Hlavné údaje užívateľa';


DROP TABLE IF EXISTS `user_permission`;
CREATE TABLE `user_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `id_user_roles` int(11) NOT NULL DEFAULT 0 COMMENT 'Užívateľská rola',
  `id_user_resource` int(11) NOT NULL COMMENT 'Zdroj',
  `actions` varchar(255) DEFAULT NULL COMMENT 'Povolenie na akciu. (Ak viac oddelené čiarkou, ak null tak všetko)',
  PRIMARY KEY (`id`),
  KEY `id_user_roles` (`id_user_roles`),
  KEY `id_user_resource` (`id_user_resource`),
  CONSTRAINT `user_permission_ibfk_1` FOREIGN KEY (`id_user_roles`) REFERENCES `user_roles` (`id`),
  CONSTRAINT `user_permission_ibfk_2` FOREIGN KEY (`id_user_resource`) REFERENCES `user_resource` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Užívateľské oprávnenia';


DROP TABLE IF EXISTS `user_prihlasenie`;
CREATE TABLE `user_prihlasenie` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `id_user_main` int(11) NOT NULL COMMENT 'Id užívateľa',
  `log_in_datetime` datetime /* mariadb-5.3 */ NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Dátum a čas prihlásenia',
  PRIMARY KEY (`id`),
  KEY `id_user_profiles` (`id_user_main`),
  CONSTRAINT `user_prihlasenie_ibfk_1` FOREIGN KEY (`id_user_main`) REFERENCES `user_main` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Evidencia prihlásenia užívateľov';


DROP TABLE IF EXISTS `user_profiles`;
CREATE TABLE `user_profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rok` int(11) DEFAULT NULL COMMENT 'Rok narodenia',
  `phone` varchar(20) DEFAULT NULL COMMENT 'Telefón',
  `poznamka` varchar(255) DEFAULT NULL COMMENT 'Poznámka',
  `pocet_pr` int(11) NOT NULL DEFAULT 0 COMMENT 'Počet prihlásení',
  `pohl` enum('Z','M') NOT NULL DEFAULT 'M' COMMENT 'Pohlavie',
  `prihlas_teraz` datetime DEFAULT NULL COMMENT 'Posledné prihlásenie',
  `avatar` varchar(200) DEFAULT NULL COMMENT 'Cesta k avatarovi veľkosti 75x75',
  `news` enum('A','N') NOT NULL DEFAULT 'A' COMMENT 'Posielanie info emailou',
  `news_key` varchar(100) DEFAULT NULL COMMENT 'Kľúč pre odhlásenie noviniek',
  `street` varchar(100) DEFAULT NULL COMMENT 'Ulica a číslo domu',
  `town` varchar(40) DEFAULT NULL COMMENT 'Mesto',
  `psc` varchar(5) DEFAULT NULL COMMENT 'PSČ',
  `country` varchar(5) DEFAULT NULL COMMENT 'Krajina',
  `adress2` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Odlišná adresa dodania' CHECK (json_valid(`adress2`)),
  `firm` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Údaje o firme' CHECK (json_valid(`firm`)),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_bin;


DROP TABLE IF EXISTS `user_resource`;
CREATE TABLE `user_resource` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `name` varchar(30) NOT NULL COMMENT 'Názov zdroja',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Zdroje oprávnení';


DROP TABLE IF EXISTS `user_roles`;
CREATE TABLE `user_roles` (
  `id` int(11) NOT NULL COMMENT '[A]Index',
  `role` varchar(30) NOT NULL DEFAULT 'guest' COMMENT 'Rola pre ACL',
  `inherited` varchar(30) DEFAULT NULL COMMENT 'Dedí od roli',
  `name` varchar(30) NOT NULL DEFAULT 'Registracia cez web' COMMENT 'Názov úrovne registrácie',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Úrovne registrácie a ich názvy';


DROP TABLE IF EXISTS `verzie`;
CREATE TABLE `verzie` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `id_user_main` int(11) NOT NULL DEFAULT 1 COMMENT 'Id užívateľa',
  `cislo` varchar(10) NOT NULL DEFAULT '' COMMENT 'Číslo verzie',
  `subory` varchar(255) DEFAULT NULL COMMENT 'Zmenené súbory',
  `text` text DEFAULT NULL COMMENT 'Popis zmien',
  `modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp() COMMENT 'Dátum a čas zmeny',
  PRIMARY KEY (`id`),
  UNIQUE KEY `cislo` (`cislo`),
  KEY `datum` (`modified`),
  KEY `id_clena` (`id_user_main`),
  CONSTRAINT `verzie_ibfk_1` FOREIGN KEY (`id_user_main`) REFERENCES `user_main` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Verzie webu';


-- 2024-05-15 13:13:42
