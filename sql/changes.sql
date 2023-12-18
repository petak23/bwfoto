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

-- updated in v 0.9.79

INSERT INTO `user_resource` (`name`);

INSERT INTO `user_permission` (`id_user_roles`, `id_user_resource`, `actions`)
VALUES ('0', '37', NULL);