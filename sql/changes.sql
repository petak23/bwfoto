/**
 * Author:  petak23
 * Change: 06.01.2021
 */
/* to 0.5.4 - vue fotogalery */

INSERT INTO `hlavne_menu_template` (`name`, `description`)
SELECT 'BWfoto_f_a_vue', 'Obsah foto albumu s vue'
FROM `hlavne_menu_template`;

UPDATE `dokumenty` SET `description` = 'Popis: ATELIER-ZAMECNIK-51' WHERE `id` = '81';
UPDATE `dokumenty` SET `description` = 'Popis: ATELIER-ZAMECNIK-52' WHERE `id` = '82';