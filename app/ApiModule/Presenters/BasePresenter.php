<?php

declare(strict_types=1);

namespace App\ApiModule\Presenters;

use DbTable;
use Language_support;
use Nette\Application\UI\Presenter;
use Nette\Http;

/**
 * Zakladny presenter pre vsetky presentery v module API
 * 
 * Posledna zmena(last change): 11.03.2024
 *
 * Modul: API
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.0.8
 */
abstract class BasePresenter extends Presenter
{

	// -- DB
	/** @var DbTable\Hlavne_menu @inject */
	public $hlavne_menu;
	/** @var DbTable\Lang @inject*/
	public $lang;
	/** @var DbTable\User_permission @inject */
	public $user_permission;
	/** @var DbTable\User_roles @inject */
	public $user_roles;
	/** @var DbTable\Udaje @inject */
	public $udaje;
	/** @var DbTable\Verzie @inject */
	public $verzie;


	/** @var string Skratka aktualneho jazyka 
	 * @persistent */
	public $language = 'sk';

	/** @var int Id aktuálneho jazyka */
	public $language_id = 1;

	/** @var Language_support\LanguageMain @inject */
	public $texty_presentera;

	/** @var int Uroven registracie uzivatela  */
	public $id_reg;

	/** @var array nastavenie z config-u */
	public $nastavenie;
	/** @var array - pole s chybami pri uploade */
	public $upload_error = [
		0 => "Bez chyby. Súbor úspešne nahraný.",
		1 => "Nahrávaný súbor je väčší ako systémom povolená hodnota!",
		2 => "Nahrávaný súbor je väčší ako je formulárom povolená hodnota!",
		3 => "Nahraný súbor bol nahraný len čiastočne...",
		4 => "Žiadny súbor nebol nahraný... Pravdepodobne ste vo formuláry žiaden nezvolili!",
		5 => "Upload error 5.",
		6 => "Chýbajúci dočasný priečinok!",
	];

	/** @var Http\Request @inject*/
	public $httpRequest;

	public function __construct(array $parameters)
	{
		// Nastavenie z config-u
		$this->nastavenie = $parameters;
	}

	/** Vychodzie nastavenia */
	protected function startup(): void
	{
		parent::startup();
		// Sprava uzivatela
		$user = $this->getUser(); //Nacitanie uzivatela
		// Kontrola prihlasenia a nacitania urovne registracie
		$this->id_reg = ($user->isLoggedIn()) ? $user->getIdentity()->id_user_roles : 0;

		// Kontrola ACL
		if (!($user->isAllowed($this->name, $this->action))) {
			$this->sendJson(['status' => 401, 'message' => "Not allowed"]);
		}

		$this->texty_presentera->setLanguage($this->language);
	}

	/** Odošle údaje o aktuálne prihlásenom užívateľovy */
	public function getActualUserInfo(): void
	{
		$out = [];
		if ($this->user->isLoggedIn()) {
			$exported_fields = ['id', 'id_user_roles', 'name', 'email', 'pocet_pr', 'avatar', 'user_role'];
			foreach ($exported_fields as $k) {
				$out['user'][$k] = $this->user->getIdentity()->data[$k];
			}
			$out['user']['prihlas_teraz'] = $this->user->getIdentity()->data['prihlas_teraz']->format('d.m.Y H:i:s');
			$out['status'] = '200';
		} else {
			$out = [
				'status' => '401',
				'error' => "Neprihlásený užívateľ"
			];
		}

		$idur = $this->user->isLoggedIn() ? $this->user->getIdentity()->data['id_user_roles'] : 0;

		$out['user']['permission'] = $this->user_permission->getAllowedPermission($idur, true);

		$this->sendJson($out);
	}
}
