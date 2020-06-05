/**
 * Author:  petak23
 * Created: 05. 06. 2020
 */
ALTER TABLE `user_profiles`
ADD `news_key` varchar(100) COLLATE 'utf8_bin' NULL COMMENT 'Kľúč pre odhlásenie noviniek';

INSERT INTO `user_resource` (`name`)
VALUES ('Front:Search');


INSERT INTO `user_permission` (`id_user_roles`, `id_user_resource`, `actions`)
VALUES ('0', '26', NULL);