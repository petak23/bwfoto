/**
 * Author:  petak23
 * Change: 02.12.2021
 *
 * to 0.8.20 
 */

INSERT INTO `user_resource` (`name`) VALUES 
('Api:Menu'),
('Api:User');

INSERT INTO `user_permission` (`id_user_roles`, `id_user_resource`, `actions`) VALUES 
('4', '27', NULL),
('4', '28', NULL);

ALTER TABLE `hlavne_menu`
DROP `poradie_podclankov`;

DELETE FROM `udaje`
WHERE ((`id` = '8') OR (`id` = '9') OR (`id` = '10') OR (`id` = '11') OR (`id` = '12') OR (`id` = '13') OR (`id` = '15'));