-- Adminer 4.8.1 MySQL 5.5.5-10.5.13-MariaDB-1:10.5.13+maria~focal dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

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
  `new_password_requested` datetime /* mariadb-5.3 */ DEFAULT NULL COMMENT 'Čas požiadavky na nové heslo',
  `new_email` varchar(100) COLLATE utf8_bin DEFAULT NULL COMMENT 'Nový email',
  `new_email_key` varchar(100) COLLATE utf8_bin DEFAULT NULL COMMENT 'Kľúč nového emailu',
  `last_ip` varchar(40) COLLATE utf8_bin DEFAULT NULL COMMENT 'Posledná IP',
  `created` datetime /* mariadb-5.3 */ NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Vytvorenie užívateľa',
  `modified` timestamp /* mariadb-5.3 */ NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Posledná zmena',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `id_registracia` (`id_user_roles`),
  KEY `id_user_profiles` (`id_user_profiles`),
  CONSTRAINT `user_main_ibfk_2` FOREIGN KEY (`id_user_profiles`) REFERENCES `user_profiles` (`id`) ON DELETE SET NULL,
  CONSTRAINT `user_main_ibfk_3` FOREIGN KEY (`id_user_roles`) REFERENCES `user_roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Hlavné údaje užívateľa';

INSERT INTO `user_main` (`id`, `id_user_roles`, `id_user_profiles`, `password`, `meno`, `priezvisko`, `email`, `activated`, `banned`, `ban_reason`, `new_password_key`, `new_password_requested`, `new_email`, `new_email_key`, `last_ip`, `created`, `modified`) VALUES
(1,	5,	1,	'$2y$10$RnzAjUCyc/B1GgiJ9k43/e27BDz5j1vsbN.DYlfnXIxweBvqxkABq',	'Peter',	'Vojtech',	'petak23@gmail.com',	1,	0,	NULL,	NULL,	NULL,	NULL,	NULL,	'188.112.80.61',	'2017-05-15 09:11:19',	'2022-01-12 10:15:09'),
(2,	4,	2,	'$2y$10$0KPg/2sE8I5EjAsgolRttulqhQPsUoVrhIHAxX8Ej3NAOHGuZIbW.',	'Juraj',	'Zámečník',	'bwfoto@bwfoto.sk',	1,	0,	NULL,	NULL,	NULL,	NULL,	NULL,	'178.143.110.65',	'2017-05-15 09:13:38',	'2021-11-23 21:24:15'),
(3,	4,	3,	'$2y$10$rrhoDAbAniSaH5IxLhobiO4ym.Qt83LdCyDUyXkw/TQCiNebhwjkq',	'Jozef',	'Petrenčík',	'jozue@anigraph.eu',	1,	0,	NULL,	NULL,	NULL,	NULL,	NULL,	'178.253.139.152',	'2017-05-15 09:12:22',	'2017-07-11 07:10:29');
