<?php

namespace App\ApiModule\Presenters;

use DbTable;

/**
 * Prezenter pre pristup k api užívateľa.
 * Posledna zmena(last change): 24.04.2024
 *
 * Modul: API
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.0.4
 */
class UserPresenter extends BasePresenter
{

	// -- DB
	/** @var DbTable\User_prihlasenie @inject */
	public $user_prihlasenie;

	/** @var DbTable\User_main @inject */
	public $user_main;
	/** @var DbTable\User_profiles @inject */
	public $user_profiles;


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
	 * Vráti pole uzivatelov vo formate: id => "meno priezvisko" 
	 * @param int id Minimálna úroveň registrácie */
	public function actionUserChangeFormUsers(int $id = 5): void
	{
		$this->sendJson($this->user_main->uzivateliaForm(1, $id));
	}

	public function actionGetActualUserInfo(): void
	{
		$this->getActualUserInfo();
	}

	public function actionGetActualUserProfile(): void
	{
		$this->sendJson($this->user_profiles->getProfile($this->user->getIdentity()->id_user_profiles));
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

	/** Vráti údaje o užívateľovi bez hesla */
	public function actionGetUserInfo(int $id): void
	{
		$this->sendJson($this->user_main->getUserForApi($id));
	}

	public function actionTestUserEmail(): void
	{
		$_post = json_decode(file_get_contents("php://input"), true);

		$this->sendJson(['status' => $this->user_main->testEmail($_post['email']) ? 200 : 404]);
	}
}
