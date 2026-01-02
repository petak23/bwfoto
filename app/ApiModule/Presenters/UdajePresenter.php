<?php

namespace App\ApiModule\Presenters;

use DbTable;
use DbTable\Udaje;

/**
 * Prezenter pre pristup k api udajov.
 * Posledna zmena(last change): 15.04.2024
 *
 * Modul: API
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.0.1
 */
class UdajePresenter extends BasePresenter
{

	// -- DB
	/** @var DbTable\Udaje @inject */
	public $udaje;

	/** @var DbTable\User_main @inject */
	public $user_main;

	/**   ----  USER_PRIHLASENIE  ----   */

	/**
	 * Vráti požadovanú položku z nastavení
	 * @param string $s Názov položky */
	public function actionGetFromSettings(string|null $s = null): void
	{
		$allowed_fields =
			[
				'komponenty', 'homepage_redirect', 'add_uroven0', 'send_e_mail_news',
				'dir_to_images', 'dir_to_icons', 'dir_to_user', 'dir_to_menu', 'slider',
				'hlavne_menu_obr_ikonky', 'user_panel', 'article_avatar_view_in', 'dir_to_products',
				'prilohy_images', 'prilohy_dir', 'user_view_fields', 'clanky', 'wwwDir'
			];

		$out = [];
		foreach ($allowed_fields as $k) {
			$out[$k] = $this->nastavenie[$k] ?? null; //isset($this->nastavenie[$k]) ? $this->nastavenie[$k] : null;
		}
		$out['basePath'] = $this->template->basePath;
		$out['basUrl'] = $this->template->baseUrl;
		$this->sendJson(
			$s === null ? ['status' => 'OK', 'data' => $out]
				: (isset($out[$s])
					? ['status' => 'OK', 'data' => $out[$s]]
					: ['status' => 'Er', 'data' => "Not found!"])
		);
	}

	/**
	 * Vráti požadovanú položku z tabuľky udaj
	 * @param string $s Názov položky */
	public function actionGetFromUdaj(string $s): void
	{
		$this->sendJson(['result' => $this->udaje->getValByName($s)]);
	}

	/**
	 * Uloží zmenu v údaji
	 * $_post = ['key', 'val'] nazov, text*/
	public function actionSaveUdaj(): void
	{
		$_post = json_decode(file_get_contents("php://input"), true);

		$saved = $this->udaje->editKey($_post['key'], $_post['val']);


		$this->sendJson([
			'result' => $saved != null ? '200' : '404',
			'new_val' => $this->udaje->getValByName($_post['key']),
		]);
	}

	public function actionGetUdaje(): void
	{
		dumpe($this->udaje->findBy(['id_user_roles <= ?' => $this->id_reg]));
		$this->sendJson($this->udaje->findBy(['id_user_roles <= ?' => $this->id_reg]));
	}
}
