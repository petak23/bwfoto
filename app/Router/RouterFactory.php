<?php

namespace App;

use DbTable;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;

/**
 * Router
 * Posledna zmena 23.02.2024
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.1.5
 */
class RouterFactory
{

	/** @var DbTable\Hlavne_menu */
	public $hlavne_menu;

	/** @param DbTable\Hlavne_menu $hlavne_menu */
	public function __construct(DbTable\Hlavne_menu $hlavne_menu)
	{
		$this->hlavne_menu = $hlavne_menu;
	}

	public function createRouter(): RouteList
	{
		$servis = $this;
		$router = new RouteList;

		$router->addRoute('index.php', 'Front:Homepage:default', Route::ONE_WAY);
		$router->addRoute('clanky/domov', 'Front:Homepage:default', Route::ONE_WAY);
		$router->addRoute('clanky/home', 'Front:Homepage:default', Route::ONE_WAY);

		$router->withModule('Admin')
			->addRoute('administration/clanky[/<action=default>]/<id>', [
				'presenter' => 'Clanky',
				'id' => [
					Route::FILTER_IN => function ($id) use ($servis) {
						if (is_numeric($id)) {
							return $id;
						} else {
							$hh = $servis->hlavne_menu->findOneBy(['spec_nazov' => $id]);
							return $hh ? $hh->id : 0;
						}
					},
					Route::FILTER_OUT => function ($id) use ($servis) {
						if (!is_numeric($id)) {
							return $id;
						} else {
							$hh = $servis->hlavne_menu->find($id);
							return $hh ? $hh->spec_nazov : "";
						}
					}
				],
			])
			->addRoute('administration/section[/<id=0>]', 'Homepage:section')
			->addRoute('administration/<presenter>/<action>', 'Homepage:default');

		$router->withModule('Api')
			->addRoute('api/menu/<action>[/<id>[/<lmodule>]]', 'Menu:default')
			->addRoute('api/user/<action>[/<id>]', 'User:default')
			->addRoute('api/lang/<action>[/<id>]', 'Lang:default')
			->addRoute('api/documents/<action>[/<id>]', 'Dokumenty:default')
			->addRoute('api/products/<action>[/<id>]', 'Products:default')
			->addRoute('api/search[/<action>]', 'Search:default')
			->addRoute('api/sign[/<action>[/<id>]]', 'Sign:in')
			->addRoute('api/slider/<action>[/<id>]', 'Slider:default')
			->addRoute('api/texyla[/<action>[/<id>]]', 'Texyla:default')
			->addRoute('api/verzie[/<action>[/<id>]]', 'Verzie:default')
			->addRoute('api/udaje/<action>[/<s>]', 'Udaje:default');

		$router->withModule('Front')
			->addRoute('clanky[/<id>]', [
				'presenter' => 'Clanky',
				'action' => 'default',
				'id' => [
					Route::FILTER_IN => function ($id) use ($servis) {
						if (is_numeric($id)) {
							return $id;
						} else {
							$hh = $servis->hlavne_menu->findOneBy(['spec_nazov' => $id]);
							return $hh ? $hh->id : 0;
						}
					},
					Route::FILTER_OUT => function ($id) use ($servis) {
						if (!is_numeric($id)) {
							return $id;
						} else {
							$hh = $servis->hlavne_menu->find($id);
							return $hh ? $hh->spec_nazov : "";
						}
					}
				],
			])
			->addRoute('homepage/productlike', 'Homepage:productLikeView')
			->addRoute('homepage/basket/<id=1>', 'Homepage:basketView')
			->addRoute('homepage/section[/id]', 'Homepage:default')
			->addRoute('forgottenPassword', 'User:forgottenPassword')
			->addRoute('profile', 'UserLog:default')
			->addRoute('registration', 'User:registracia')
			->addRoute('login', 'User:default')
			->addRoute('user[/<action>]', 'User:default')
			->addRoute('userlog[/<action>]/<id>', 'UserLog:default')
			->addRoute('oznam[/<action>]', 'Oznam:default')
			->addRoute('error[/<action>]', 'Error:default')
			->addRoute('setlang[/<language>]', 'Homepage:setLang')
			->addRoute('<presenter>/<action>[/cokolvek]', 'Homepage:default')
			->addRoute('[<presenter>][/<action>][/<spec_nazov><? \.html?|\.php|>]', 'Homepage:default', Route::ONE_WAY);

		return $router;
	}
}
