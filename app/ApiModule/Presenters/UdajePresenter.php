<?php

namespace App\ApiModule\Presenters;

use DbTable;

/**
 * Prezenter pre pristup k api udajov.
 * Posledna zmena(last change): 12.12.2023
 *
 * Modul: API
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.0.0
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
			$out[$k] = isset($this->nastavenie[$k]) ? $this->nastavenie[$k] : null;
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
	 * Otestuje či je užívateľ prihlásený a či má oprávnenie na požadovanú operáciu
	 * $_post = ['resource', 'action'] */
	/*public function actionIsAllowed(int $id): void
	{
		$_post = json_decode(file_get_contents("php://input"), true);

		$allowed = $this->user->isLoggedIn()	// Kontrola prihlásenia
			&& $this->user->getId() == $id			// Kontrola užívateľovho id
			&& $this->user->isAllowed($_post['resource'], $_post['action']) ? 1 : 0; // Kontrola oprávnenia

		$this->sendJson(['result' => $allowed]);
	}*/
}
