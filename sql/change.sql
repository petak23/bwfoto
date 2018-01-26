SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

INSERT INTO `admin_menu` (`odkaz`, `nazov`, `id_user_roles`, `avatar`, `view`)
VALUES ('Products:setup', 'Nastavenie produktov', '4', 'ikonky/AzulLustre_icons/Programas.png', '1');

INSERT INTO `udaje_typ` (`nazov`, `comment`)
VALUES ('number', 'Číslo');

ALTER TABLE `udaje`
ADD `separate_settings` tinyint NOT NULL DEFAULT '0' COMMENT 'Ak 1 tak má položka vlastnú časť nastavení';

UPDATE `udaje` SET `id_udaje_typ` = '4', `comment` = 'Šírka hlavného obrázku produktu[10,2000]', `separate_settings` = '1' WHERE `id` = '27';
UPDATE `udaje` SET `id_udaje_typ` = '4', `comment` = 'Výška hlavného obrázku produktu[10,2000]', `separate_settings` = '1' WHERE `id` = '28';
UPDATE `udaje` SET `id_udaje_typ` = '4', `comment` = 'Kvalita kompresie hlavného obrázku[1,100]', `separate_settings` = '1' WHERE `id` = '29';
UPDATE `udaje` SET `id_udaje_typ` = '4', `comment` = 'Šírka náhľadového obrázku produktu[10,1000]', `separate_settings` = '1' WHERE `id` = '30';
UPDATE `udaje` SET `id_udaje_typ` = '4', `comment` = 'Výška náhľadového obrázku produktu[10,1000]', `separate_settings` = '1' WHERE `id` = '31';
UPDATE `udaje` SET `id_udaje_typ` = '4', `comment` = 'Kvalita kompresie náhľadového obrázku[1,100]', `separate_settings` = '1' WHERE `id` = '32';