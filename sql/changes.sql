/**
 * Author:  petak23
 * Change: 28.02.2022
 *
 * to 0.8.32 
 */

/*INSERT INTO `user_resource` (`name`) VALUES 
('Api:Menu'),
('Api:User');

INSERT INTO `user_permission` (`id_user_roles`, `id_user_resource`, `actions`) VALUES 
('4', '27', NULL),
('4', '28', NULL);

ALTER TABLE `hlavne_menu`
DROP `poradie_podclankov`;

DELETE FROM `udaje`
WHERE ((`id` = '8') OR (`id` = '9') OR (`id` = '10') OR (`id` = '11') OR (`id` = '12') OR (`id` = '13') OR (`id` = '15'));

INSERT INTO `hlavne_menu_template` (`name`, `description`)
VALUES ('BWfoto_foto_collage', 'Koláž fotiek');

INSERT INTO `user_resource` (`name`)
VALUES ('Api:Dokumenty');

INSERT INTO `user_permission` (`id_user_roles`, `id_user_resource`, `actions`)
VALUES ('0', '29', NULL);

ALTER TABLE `hlavne_menu_lang`
ADD `text` text COLLATE 'utf8_bin' NULL COMMENT 'Text článku v danom jazyku',
ADD `anotacia` varchar(255) COLLATE 'utf8_bin' NULL COMMENT 'Anotácia článku v danom jazyku' AFTER `text`;*/