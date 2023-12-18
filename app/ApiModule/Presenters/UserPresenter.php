<?php

namespace App\ApiModule\Presenters;

use DbTable;

/**
 * Prezenter pre pristup k api užívateľa.
 * Posledna zmena(last change): 24.02.2023
 *
 * Modul: API
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.0.2
 */
class UserPresenter extends BasePresenter
{

	// -- DB
	/** @var DbTable\User_prihlasenie @inject */
	public $user_prihlasenie;

	/** @var DbTable\User_main @inject */
	public $user_main;
	/** @var DbTable\User_permission @inject */
	public $user_permission;


	/**   ----  USER_PRIHLASENIE  ----   */

	/**
	 * Vráti posledné prihlásenia
	 * @param int $count Počet zobrazených prihlásení */
	public function actionGetLastLogin(int $count = 25): void
	{
		$this->sendJson($this->user_prihlasenie->getLastLogin($count, true));
	}

	/**
	 * Vymaže všetky záznamy o prihlásení z DB tab. user_prihlasenie
	 * Ak vracia 0 tak OK */
	public function actionDeleteAllLogin(): void
	{
		$this->sendJson(['result' => $this->user_prihlasenie->delAll()]);
	}

	/**
	 * Funkcia pre formulár na zostavenie zoznamu všetkých užívateľov
	 * Vráti pole uzivatelov vo formate: id => "meno priezvisko" */
	public function actionUserChangeFormUsers(): void
	{
		$this->sendJson($this->user_main->uzivateliaForm(1));
	}

	public function actionGetActualUserInfo(): void
	{
		$out = [];
		if ($this->user->isLoggedIn()) {
			$exported_fields = ['id', 'id_user_roles', 'meno', 'priezvisko', 'email', 'pocet_pr', 'avatar', 'user_role'];
			foreach ($exported_fields as $k) {
				$out[$k] = $this->user->getIdentity()->data[$k];
			}
			$out['prihlas_teraz'] = $this->user->getIdentity()->data['prihlas_teraz']->format('d.m.Y H:i:s');
		}

		$out['permission'] = $this->user_permission->getAllowedPermission($this->user->getIdentity()->data['id_user_roles'], true);

		$this->sendJson(['result' => $out]);
	}

	/**
	 * Otestuje či je užívateľ prihlásený a či má oprávnenie na požadovanú operáciu
	 * $_post = ['resource', 'action'] */
	public function actionIsAllowed(int $id): void
	{
		$_post = json_decode(file_get_contents("php://input"), true);

		$allowed = $this->user->isLoggedIn()	// Kontrola prihlásenia
			&& $this->user->getId() == $id			// Kontrola užívateľovho id
			&& $this->user->isAllowed($_post['resource'], $_post['action']) ? 1 : 0; // Kontrola oprávnenia

		$this->sendJson(['result' => $allowed]);
	}
}
