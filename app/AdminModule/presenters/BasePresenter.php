<?php

namespace App\AdminModule\Presenters;

use App\AdminModule\Components;
use DbTable;
use Nette;
use Nette\Application\UI;
use Nette\Http;
use Nette\Utils\Json;
use PeterVojtech;
use Texy;

/**
 * Zakladny presenter pre vsetky presentery v module ADMIN
 * 
 * Posledna zmena(last change): 08.02.2024
 *
 * Modul: ADMIN
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.4.2
 */
abstract class BasePresenter extends UI\Presenter
{

	use PeterVojtech\MainLayout\Favicon\faviconTrait;

	// -- DB
	/** @var DbTable\Admin_menu @inject */
	public $admin_menu;
	/** @var DbTable\Hlavne_menu @inject */
	public $hlavne_menu;
	/** @var DbTable\Lang @inject*/
	public $lang;
	/** @var DbTable\User_main @inject */
	public $user_main;
	/** @var DbTable\User_roles @inject */
	public $user_roles;
	/** @var DbTable\Udaje @inject */
	public $udaje;
	/** @var DbTable\Verzie @inject */
	public $verzie;

	/** @var string Adresar pre prilohy clankov */
	public $prilohy_adresar;

	/** @persistent */
	public $language = 'sk';
	/** @persistent */
	public $backlink = '';

	/** @var Http\Request @inject*/
	public $httpRequest;

	/** @var Texy\Texy @inject */
	public $texy;

	/** @var string kmenovy nazov stranky pre rozne ucely typu www.neco.sk*/
	public $nazov_stranky;
	/** @var int Uroven registracie uzivatela  */
	public $id_reg;
	/** @var int Maximalna uroven registracie uzivatela */
	public $max_id_reg = 0;

	/** @var string Specificky nazov casti */
	public $spec_nazov;

	/** @var array Pole s hlavnymi udajmi webu */
	public $udaje_webu;

	/** @var int */
	public $language_id = 1;

	/** @var array nastavenie z config-u */
	public $nastavenie;
	/** @var string - relatívna cesta pre avatar poloziek menu */
	public $menu_avatar;

	/** @var array Hodnoty role=>id v DB tab user_roles */
	public $ur_reg = [];
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

	/** Výsledná podoba admin. menu */
	public $admin_menu_final;
	/** Aktuálna aktívna položka admin. menu */
	public $admin_menu_active;

	public function __construct($parameters)
	{
		// Nastavenie z config-u
		$this->nastavenie = $parameters;
	}

	/** Vychodzie nastavenia */
	protected function startup()
	{
		parent::startup();
		// Sprava uzivatela
		$user = $this->getUser(); //Nacitanie uzivatela
		// Kontrola prihlasenia a nacitania urovne registracie
		$this->id_reg = ($user->isLoggedIn()) ? $this->user_main->getUser($user->id)->id_user_roles : 0;
		// Kontrola prihlasenia
		if ($user->isLoggedIn()) { //Prihlaseny uzivatel
			if (!$user->isAllowed($this->name, $this->action)) { //Kontrola ACL
				$this->flashRedirect('Homepage:', 'Na požadovanú akciu nemáte dostatočné oprávnenie! Ide o:' . $this->name . ':' . $this->action, 'danger');
			}
		} else { //Neprihlaseny
			$ur = $user->getLogoutReason();
			if ($ur === Nette\Security\UserStorage::LOGOUT_INACTIVITY) {
				$backlink = $this->getApplication()->storeRequest();
				$this->flashRedirect([':Front:User:', ['backlink' => $backlink]], 'Boli ste príliš dlho neaktívny a preto ste boli odhlásený! Prosím, prihláste sa znovu.', 'danger');
			} elseif ($ur === Nette\Security\UserStorage::LOGOUT_MANUAL) {
				$this->flashRedirect(':Front:User:', 'Nemáte dostatočné oprávnenie na danú operáciu, pretože ste sa odhlásili!', 'warning');
			} else {
				$this->flashRedirect(':Front:User:', 'Nemáte dostatočné oprávnenie na danú operáciu!', 'danger');
			}
		}
		$modul_presenter = explode(":", $this->name);
		$this->language = 'sk';
		$this->language_id = 1;
		//Nacitanie a spracovanie hlavnych udajov webu
		$this->udaje_webu = $this->udaje->findAll()->fetchPairs('nazov', 'text');
		$vysledok = [];
		//Nacitanie len tych premennych, ktore platia pre danu jazykovu mutaciu
		foreach ($this->udaje_webu as $key => $value) {
			$kluc = explode("-", $key);
			if (count($kluc) == 2 && $kluc[1] == $this->language) {
				$vysledok[substr($key, 0, strlen($key) - strlen($this->language) - 1)] = $value;
			}
			if (count($kluc) == 1) {
				$vysledok[$key] = $value;
			}
		}
		$this->udaje_webu = $vysledok;
		// Nacitanie pomocnych premennych
		$this->udaje_webu['meno_presentera'] = $modul_presenter[1]; //Meno aktualneho presentera
		$httpR = $this->httpRequest->getUrl();
		$this->nazov_stranky = $httpR->host . $httpR->scriptPath; // Nazov stranky v tvare www.nieco.sk
		$this->nazov_stranky = substr($this->nazov_stranky, 0, strlen($this->nazov_stranky) - 1);
		// Priradenie hlavnych parametrov a udajov
		$this->max_id_reg = $this->user_roles->findAll()->max('id'); //Najdi max. ur. reg.
		$this->ur_reg = $this->user_roles->vsetky_urovne_array(); //Najdi max. ur. reg.

		$this->admin_menu_final = $this->admin_menu->getAdminMenu($this->id_reg);
		// Zistenie id aktivnej polozky admin. menu.
		$aml = $this->admin_menu->findAll()->where('odkaz LIKE ?', '%' . $this->udaje_webu['meno_presentera'] . ':%')->limit(1)->fetch();
		$this->admin_menu_active = $aml == null ? 0 : $aml->id;
	}

	public function addAdminMenu(array $item): void
	{
		$this->admin_menu_final[$this->admin_menu_active]['child'][] = $item;
	}

	/** Nastevenie premennych pre vsetky sablony */
	public function beforeRender()
	{
		$this->getComponent('menu')->selectByUrl($this->link($this->udaje_webu['meno_presentera'] . ":", isset($this->params["id"]) ? ["id" => (int)$this->params["id"]] : []));
		$this->template->verzia = $this->verzie->posledna();
		$this->template->nazov_stranky = $this->nazov_stranky;
		$this->template->udaje = array_merge($this->udaje_webu, $this->hlavne_menu->getDBInfo());
		$this->template->urovregistr = $this->id_reg;
		$this->template->lang_menu = $this->lang->findAll();
		$this->template->language = $this->language;
		$this->template->avatar_path = $this->nastavenie["dir_to_menu"];

		$this->template->admin_menu = $this->admin_menu_final;
		$this->template->admin_menu_active = $this->admin_menu_active;

		// Zistenie id aktivnej polozky hl. menu. $this->zobraz_clanok je definované v articlePresenter 
		$this->template->main_menu_active = isset($this->params["id"]) ? $this->params["id"] : (isset($this->zobraz_clanok) ? $this->zobraz_clanok->id : 0);
		$this->template->clanok = null; // Pre prípady, kedy nemám článok

		$this->template->nastavenie = $this->nastavenie;
		$this->template->dir_to_images = $this->nastavenie['dir_to_images'];
		$this->template->dir_to_icons = $this->nastavenie['dir_to_icons'];
		$this->template->items_per_page = [1 => "10", 2 => "20", 3 => "50", -1 => "Všetky"];

		$servise = $this;
		$this->template->addFilter('hlmenuclass', function ($id, $id_user_roles, $hl_udaje) {
			$polozka_class = $id_user_roles > 2 ? 'adminPol' : '';
			//TODO $classPol .= ' zvyrazni';
			if ($id == $hl_udaje) {
				$polozka_class .= ' active';
			}
			return $polozka_class;
		});
		$this->template->addFilter('nahodne', function ($max) { //Generuje nahodne cislo do template v rozsahu od 0 do max
			return (int)rand(0, $max);
		});
		$this->template->addFilter('uprav_email', function ($email) { //Upravi email aby sa nedal pouzit ako nema
			return Nette\Utils\Strings::replace($email, ['~@~' => '[@]', '~\.~' => '[dot]']);
		});
		$this->template->addFilter('textreg', function ($text, $id_user_roles, $max_id_reg) {
			for ($i = $max_id_reg; $i >= 0; $i--) {
				$z_zac = "#REG" . $i . "#"; //Pociatocna znacka
				$z_alt = "#REG-A" . $i . "#"; //Alternativna znacka
				$z_kon = "#/REG" . $i . "#"; //Koncova znacka
				if (($p_zac = strpos($text, $z_zac)) !== FALSE && ($p_kon = strpos($text, $z_kon)) !== FALSE && $p_zac < $p_kon) { //Ak som našiel začiatok a koniec a sú v správnom poradí
					$text = substr($text, 0, $p_zac) //Po zaciatocnu zancku
						. (($p_alt = strpos($text, $z_alt)) === FALSE ? // Je alternativa
							($i < $id_user_roles ? substr($text, $p_zac + strlen($z_zac), $p_kon - $p_zac - strlen($z_zac)) : '') : // Bez alternativy
							($i < $id_user_roles ? substr($text, $p_zac + strlen($z_zac), $p_alt - $p_zac - strlen($z_zac)) : substr($text, $p_alt + strlen($z_alt), $p_kon - $p_alt - strlen($z_alt)))) // S alternativou
						. substr($text, $p_kon + strlen($z_kon)); //Od koncovej znacky
				}
			}
			return $text;
		});
		$this->template->addFilter('vytvor_odkaz', function ($row) use ($servise) {
			return isset($row->absolutna) ? $row->absolutna : (isset($row->spec_nazov) ? $servise->link($row->druh->presenter . ':default', $row->spec_nazov)
				: $servise->link($row->druh->presenter . ':default'));
		});
		$this->template->addFilter('menu_mutacia_nazov', function ($id) use ($servise) {
			$pom = $servise->hlavne_menu_lang->findOneBy(['id_hlavne_menu' => $id, 'id_lang' => $servise->language_id]);
			return $pom !== FALSE ? $pom->nazov : $id;
		});
		$this->template->addFilter('menu_mutacia_title', function ($id) use ($servise) {
			$pom = $servise->hlavne_menu_lang->findOneBy(['id_hlavne_menu' => $id, 'id_lang' => $servise->language_id]);
			return $pom !== FALSE ? ((isset($pom->view_name) && strlen($pom->view_name)) ? $pom->view_name : $pom->menu_name) : $id;
		});
		$this->template->addFilter('menu_mutacia_h1part2', function ($id) use ($servise) {
			$pom = $servise->hlavne_menu_lang->findOneBy(['id_hlavne_menu' => $id, 'id_lang' => $servise->language_id]);
			return $pom !== FALSE ? $pom->h1part2 : $id;
		});
		$this->template->addFilter('to_json', function ($value) {
			return Json::encode($value);
		});
		$this->texy->allowedTags = TRUE;
		$this->texy->headingModule->balancing = "FIXED";
		$this->template->addFilter('texy', [$this->texy, 'process']);

		//For texyla
		$this->template->previewPath = $this->link("Texyla:preview");
		$this->template->filesPath = $this->link("Texyla:listFiles");
		$this->template->filesUploadPath = $this->link("Texyla:upload");
		$this->template->filesMkDirPath = $this->link("Texyla:mkDir");
		$this->template->filesRenamePath = $this->link("Texyla:rename");
		$this->template->filesDeletePath = $this->link("Texyla:delete");
	}

	//  ---- Komponenty ---- 

	/** 
	 * Komponenta pre výpis css a js súborov */
	public function createComponentFiles(): PeterVojtech\Base\CssJsFilesControl
	{
		return new PeterVojtech\Base\CssJsFilesControl($this->nastavenie['web_files'], $this->name, $this->action);
	}

	/** 
	 * Vytvorenie komponenty pre hlavne menu */
	public function createComponentMenu(): Components\Menu\Menu
	{
		$menu = new Components\Menu\Menu;
		$menu->setNastavenie($this->nastavenie);
		$hl_m = $this->hlavne_menu->getMenuAdmin($this->language_id);
		if (count($hl_m)) {
			$servise = $this;
			$menu->fromTable($hl_m, function ($node, $row) use ($servise) {
				foreach (["name", "tooltip", "avatar", "anotacia", "node_class", "id", "datum_platnosti"] as $v) {
					$node->$v = $row['node']->$v;
				}
				$node->link = is_array($row['node']->link) ? $servise->link($row['node']->link[0], ["id" => $row['node']->id]) : $servise->link($row['node']->link);
				return $row['nadradena'] ? $row['nadradena'] : null;
			});
		}
		return $menu;
	}

	/**
	 * Komponenta Confirmation Dialog pre delete News */
	public function createComponentConfirmForm(): PeterVojtech\Confirm\ConfirmationDialog
	{
		$form = new PeterVojtech\Confirm\ConfirmationDialog($this->getSession('news'));
		$form->addConfirmer(
			'delete', // názov signálu bude confirmDelete!
			[$this, 'confirmedDelete'], // callback na funkciu pri kliknutí na YES
			[$this, 'questionDelete'] // otázka
		);
		return $form;
	}

	/**
	 * Zostavenie otázky pre ConfDialog s parametrom */
	public function questionDelete(PeterVojtech\Confirm\ConfirmationDialog $dialog, array $params): string
	{
		$dialog->getQuestionPrototype();
		return sprintf(
			"Naozaj chceš zmazať %s '%s'?",
			isset($params['zdroj_na_zmazanie']) ? $params['zdroj_na_zmazanie'] : "položku",
			isset($params['nazov']) ? $params['nazov'] : ''
		);
	}

	/** 
	 * Vypis spravy podla podmienky */
	public function _ifMessage(bool $if, string $dobre, string $zle)
	{
		if ($if) {
			$this->flashMessage($dobre, 'success');
		} else {
			$this->flashMessage($zle, 'danger');
		}
	}

	/** Funkcia pre zjednodusenie vypisu flash spravy a presmerovania
	 * @param array|string $redirect Adresa presmerovania
	 * @param string $text Text pre vypis hlasenia
	 * @param string $druh - druh hlasenia */
	public function flashRedirect(array|string $redirect, string $text = "", string $druh = "info")
	{
		$this->flashMessage($text, $druh);
		if (is_array($redirect)) {
			if (count($redirect) > 1) {
				$this->redirect($redirect[0], $redirect[1]);
			} elseif (count($redirect) == 1) {
				$this->redirect($redirect[0]);
			}
		} else {
			$this->redirect($redirect);
		}
	}

	/**
	 * Funkcia pre zjednodusenie vypisu flash spravy a presmerovania aj pre chybovy stav
	 * @param boolean $ok Podmienka
	 * @param array|string $redirect Adresa presmerovania
	 * @param string $textOk Text pre vypis hlasenia ak je podmienka splnena
	 * @param string $textEr Text pre vypis hlasenia ak NIE je podmienka splnena
	 */
	public function flashOut($ok, $redirect, $textOk = "", $textEr = "")
	{
		if ($ok) {
			$this->flashRedirect($redirect, $textOk, "success");
		} else {
			$this->flashMessage($textEr, 'danger');
		}
	}

	/**
	 * Nastavenie vzhľadu formulara */
	public function _vzhladForm(UI\Form $form): UI\Form
	{
		$renderer = $form->getRenderer();
		// Vzhlad pre bootstrap 4 link: https://github.com/nette/forms/blob/96b3e90/examples/bootstrap4-rendering.php  
		$renderer->wrappers['controls']['container'] = null;
		$renderer->wrappers['pair']['container'] = 'div class="form-group row"';
		$renderer->wrappers['pair']['.error'] = 'has-danger';
		$renderer->wrappers['control']['container'] = 'div class=col-sm-9';
		$renderer->wrappers['label']['container'] = 'div class="col-sm-3 col-form-label"';
		$renderer->wrappers['control']['description'] = 'span class="form-text alert alert-info"';
		$renderer->wrappers['control']['errorcontainer'] = 'span class="form-control-feedback alert alert-danger"';
		$renderer->wrappers['control']['.error'] = 'is-invalid';

		foreach ($form->getControls() as $control) {
			$type = $control->getOption('type');
			if ($type === 'button') {
				$control->getControlPrototype()->addClass(empty($usedPrimary) ? 'btn btn-primary' : 'btn btn-secondary');
				$usedPrimary = true;
			} elseif (in_array($type, ['text', 'textarea', 'select'], true)) {
				$control->getControlPrototype()->addClass('form-control');
			} elseif ($type === 'file') {
				$control->getControlPrototype()->addClass('form-control-file');
			} elseif (in_array($type, ['checkbox', 'radio'], true)) {
				if ($control instanceof \Nette\Forms\Controls\Checkbox) {
					$control->getLabelPrototype()->addClass('form-check-label');
				} else {
					$control->getItemLabelPrototype()->addClass('form-check-label');
				}
				$control->getControlPrototype()->addClass('form-check-input');
				$control->getSeparatorPrototype()->setName('div')->addClass('form-check');
			}
		}
		return $form;
	}
}
