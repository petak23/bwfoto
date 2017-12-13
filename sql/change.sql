/**
 * Author:  Ing. Peter VOJTECH m
 * Created: 12.12.2017
 */
ALTER TABLE `hlavne_menu`
ADD `border_a` varchar(15) NULL COMMENT 'Okraj A: ffffff|hhh f-farba h-hrúbka',
ADD `border_b` varchar(15) NULL COMMENT 'Okraj B: ffffff|hhh f-farba h-hrúbka' AFTER `border_a`,
ADD `border_c` varchar(15) NULL COMMENT 'Okraj C: ffffff|hhh f-farba h-hrúbka' AFTER `border_b`;