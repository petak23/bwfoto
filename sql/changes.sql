/**
 * Author:  petak23
 * Change: 03.11.2021
 *
 * to 0.9.0 
 */

INSERT INTO `user_permission` (`id`, `id_user_roles`, `id_user_resource`, `actions`) VALUES
(35,	0,	26,	NULL);

INSERT INTO `hlavne_menu_template` (`name`, `description`) VALUES
('BWfoto_foto_album_lg', 'Obsah foto albumu pre panorámy');

INSERT INTO `user_resource` (`name`)
VALUES ('Api:Menu');

INSERT INTO `user_permission` (`id_user_roles`, `id_user_resource`, `actions`)
VALUES ('4', '27', NULL);

ALTER TABLE `hlavne_menu`
DROP `poradie_podclankov`;
