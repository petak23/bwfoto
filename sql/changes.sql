/**
 * Author:  petak23
 * Created: 05. 06. 2020
 */
/*ALTER TABLE `user_profiles`
ADD `news_key` varchar(100) COLLATE 'utf8_bin' NULL COMMENT 'Kľúč pre odhlásenie noviniek';*/

/*INSERT INTO `user_resource` (`name`)
VALUES ('Front:Search');*/


/*INSERT INTO `user_permission` (`id_user_roles`, `id_user_resource`, `actions`)
VALUES ('0', '26', NULL);*/

ALTER TABLE `products`
CHANGE `change` `change` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE CURRENT_TIMESTAMP COMMENT 'Čas zmeny' AFTER `thumb_file`,
ADD `saved` tinyint NOT NULL DEFAULT '0' COMMENT 'Ak 1 položka bola korektne nahratá na server';

UPDATE `products` SET `saved` = '1';

INSERT INTO `udaje` (`id_user_roles`, `id_druh`, `id_udaje_typ`, `nazov`, `text`, `comment`, `separate_settings`)
SELECT '4', '8', '4', 'product_max_upload_files', '50', 'Max. počet naraz nahrávaných súborov[5,150]', '1'
FROM `udaje`
WHERE ((`id` = '32'));