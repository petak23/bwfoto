<?php

namespace App\ApiModule\Presenters;

use DbTable;

/**
 * Prezenter pre pristup k api užívateľa.
 * Posledna zmena(last change): 22.02.2023
 *
 * Modul: API
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.0.1
 */
class UserPresenter extends BasePresenter
{

	// -- DB
	/** @var DbTable\User_prihlasenie @inject */
	public $user_prihlasenie;

	/** @var DbTable\User_main @inject */
	public $user_main;

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
		$this->sendJson($this->user_main->uzivateliaForm());
	}
}
