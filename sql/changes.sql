SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
/*
ALTER TABLE `user_permission`
CHANGE `actions` `actions` varchar(255) COLLATE 'utf8mb3_bin' NULL COMMENT 'Povolenie na akciu. (Ak viac oddelené čiarkou, ak null tak všetko)' AFTER `id_user_resource`;

UPDATE `user_permission` SET `actions` = 'getsubmenu,getmenu,getonemenuarticle,getonemenuarticlesp,getonehlavnemenuarticle' WHERE `id` = '36';

INSERT INTO `hlavne_menu` (`id`, `spec_nazov`, `id_hlavne_menu_cast`, `id_user_roles`, `id_ikonka`, `id_druh`, `uroven`, `id_nadradenej`, `id_user_main`, `poradie`, `id_hlavicka`, `id_hlavne_menu_opravnenie`, `zvyrazni`, `pocitadlo`, `nazov_ul_sub`, `id_hlavne_menu_template`, `absolutna`, `ikonka`, `avatar`, `komentar`, `modified`, `datum_platnosti`, `aktualny_projekt`, `redirect_id`, `id_dlzka_novinky`, `border_a`, `border_b`, `border_c`, `id_user_categories`) VALUES
(142,	'fotocollage-settings',	3,	0,	NULL,	1,	0,	NULL,	1,	3,	0,	0,	0,	0,	NULL,	1,	NULL,	NULL,	NULL,	0,	'2023-01-26 14:13:39',	NULL,	0,	NULL,	1,	NULL,	NULL,	NULL,	NULL);

INSERT INTO `hlavne_menu_lang` (`id`, `id_lang`, `id_hlavne_menu`, `menu_name`, `h1part2`, `view_name`, `text`, `text_c`, `anotacia`) VALUES
(142,	1,	142,	'fotocollage-settings',	NULL,	'fotocollage-settings',	NULL,	'[\n  {\n    \"max_width\": 320,\n    \"schema\": [\n      2,\n      1,\n      3,\n      4,\n      4,\n      3,\n      4,\n      4\n    ],\n    \"height\": [\n      85,\n      60,\n      85,\n      60,\n      70,\n      95,\n      70,\n      60\n    ],\n    \"widerPhotoId\": [\n      -1,\n      2,\n      0,\n      1,\n      -1,\n      0,\n      2,\n      1\n    ]\n  },\n  {\n    \"max_width\": 700,\n    \"schema\": [\n      9,\n      3,\n      5,\n      4,\n      3,\n      4,\n      5,\n      4\n    ],\n    \"height\": [\n      130,\n      175,\n      105,\n      120,\n      175,\n      130,\n      105,\n      120\n    ],\n    \"widerPhotoId\": [\n      -1,\n      0,\n      2,\n      0,\n      -1,\n      2,\n      3,\n      1\n    ]\n  },\n  {\n    \"max_width\": 1300,\n    \"schema\": [\n      8,\n      7,\n      8,\n      7,\n      6,\n      8,\n      7,\n      6\n    ],\n    \"height\": [\n      175,\n      170,\n      135,\n      170,\n      225,\n      135,\n      170,\n      225\n    ],\n    \"widerPhotoId\": [\n      2,\n      -1,\n      0,\n      2,\n      -1,\n      1,\n      2,\n      1\n    ],\n    \"layout\": [\n      6,\n      7,\n      8,\n      7,\n      6\n    ]\n  },\n  {\n    \"max_width\": 10000,\n    \"schema\": [\n      6,\n      7,\n      8,\n      7,\n      6,\n      8,\n      7,\n      6\n    ],\n    \"height\": [\n      318,\n      240,\n      190,\n      240,\n      318,\n      190,\n      240,\n      318\n    ],\n    \"widerPhotoId\": [\n      3,\n      0,\n      -1,\n      2,\n      2,\n      -1,\n      3,\n      4\n    ]\n  }\n]',	NULL);

INSERT INTO `user_resource` (`name`)
VALUES ('Api:Lang');

INSERT INTO `user_permission` (`id_user_roles`, `id_user_resource`, `actions`)
VALUES ('0', '35', NULL);*/

/* INSERT INTO `user_resource` (`name`)
SELECT 'Api:Search'
FROM `user_resource`
WHERE ((`id` = '26'));


INSERT INTO `user_permission` (`id_user_roles`, `id_user_resource`, `actions`)
SELECT '0', '36', NULL
FROM `user_permission`
WHERE `id_user_resource` = '26' AND ((`id` = '35'));

CREATE TABLE `fotocollage_settings` (
  `id` int NOT NULL COMMENT '[A]Index' AUTO_INCREMENT PRIMARY KEY,
  `id_hlavne_menu` int NOT NULL COMMENT 'Id prvku, ku ktorému je koláž',
  `settings` json NOT NULL COMMENT 'Nastavenia vo formáte json',
  FOREIGN KEY (`id_hlavne_menu`) REFERENCES `hlavne_menu` (`id`)
) COMMENT='Nastavenia fotokoláže' ENGINE='InnoDB' COLLATE 'utf32_bin';

UPDATE `user_permission` SET `actions` = 'getsubmenu,getmenu,getonemenuarticle,getonemenuarticlesp,getonehlavnemenuarticle,getfotocollagesettings' WHERE `id_user_resource` = '27' AND `id` = '36';
*/
 -- updated in v 0.9.58

INSERT INTO `user_permission` (`id_user_roles`, `id_user_resource`, `actions`)
SELECT '0', '32', 'getall'
FROM `user_permission`
WHERE `id_user_resource` = '32' AND ((`id` = '42'));

UPDATE `user_permission` SET
`id` = '36',
`id_user_roles` = '0',
`id_user_resource` = '27',
`actions` = 'getsubmenu,getmenu,getonemenuarticle,getonemenuarticlesp,getonehlavnemenuarticle,getfotocollagesettings,getactualuserinfo'
WHERE `id` = '36';

-- updated in v 0.9.80

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `user_resource`;
CREATE TABLE `user_resource` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `name` varchar(30) COLLATE utf8mb3_bin NOT NULL COMMENT 'Názov zdroja',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin COMMENT='Zdroje oprávnení';

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
(26,	'Front:Search'),
(27,	'Api:Menu'),
(28,	'Api:User'),
(29,	'Api:Dokumenty'),
(30,	'Api:Products'),
(31,	'Api:Texyla'),
(32,	'Api:Slider'),
(34,	'Api:Verzie'),
(35,	'Api:Lang'),
(36,	'Api:Search'),
(37,	'Api:Udaje'),
(38,	'Api:Sign');

DROP TABLE IF EXISTS `user_permission`;
CREATE TABLE `user_permission` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '[A]Index',
  `id_user_roles` int NOT NULL DEFAULT '0' COMMENT 'Užívateľská rola',
  `id_user_resource` int NOT NULL COMMENT 'Zdroj',
  `actions` varchar(255) COLLATE utf8mb3_bin DEFAULT NULL COMMENT 'Povolenie na akciu. (Ak viac oddelené čiarkou, ak null tak všetko)',
  PRIMARY KEY (`id`),
  KEY `id_user_roles` (`id_user_roles`),
  KEY `id_user_resource` (`id_user_resource`),
  CONSTRAINT `user_permission_ibfk_1` FOREIGN KEY (`id_user_roles`) REFERENCES `user_roles` (`id`),
  CONSTRAINT `user_permission_ibfk_2` FOREIGN KEY (`id_user_resource`) REFERENCES `user_resource` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin COMMENT='Užívateľské oprávnenia';

INSERT INTO `user_permission` (`id`, `id_user_roles`, `id_user_resource`, `actions`) VALUES
(1,	0,	4,	NULL),
(2,	0,	6,	NULL),
(3,	0,	7,	'default'),
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
(33,	4,	7,	'default,edit'),
(34,	4,	25,	NULL),
(35,	0,	26,	NULL),
(36,	0,	27,	'getsubmenu,getmenu,getonemenuarticle,getonemenuarticlesp,getonehlavnemenuarticle,getfotocollagesettings,getactualuserinfo'),
(37,	4,	28,	NULL),
(38,	0,	29,	NULL),
(39,	4,	27,	NULL),
(40,	4,	30,	NULL),
(41,	0,	31,	NULL),
(42,	4,	32,	NULL),
(45,	3,	34,	NULL),
(46,	0,	35,	NULL),
(47,	0,	36,	NULL),
(48,	0,	32,	'getall'),
(49,	0,	37,	NULL),
(50,	0,	38,	NULL);