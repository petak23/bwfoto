INSERT INTO `admin_menu` (`odkaz`, `nazov`, `id_user_roles`, `avatar`, `view`) VALUES
('UserCategories:',	'Editácia kategórií',	5,	'Matrilineare_icon_set/png/Apps libreoffice base.png',	1);

INSERT INTO `user_resource` (`id`, `name`) VALUES
(32,	'Admin:UserCategories');

INSERT INTO `user_permission` (`id_user_roles`, `id_user_resource`, `actions`) VALUES
(5,	32,	NULL);