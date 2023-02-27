<?php

declare(strict_types=1);

namespace App\ApiModule\Presenters;

use App\ApiModule\Components\Menu;
use DbTable;

/**
 * Prezenter pre pristup k api hlavneho menu a pridružených vecí ako je aj obsah článku.
 * Posledna zmena(last change): 27.02.2023
 *
 * Modul: API
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.1.1
 * 
 * @help 1.) https://forum.nette.org/cs/28370-data-z-post-request-body-reactjs-appka-se-po-ceste-do-php-ztrati
 */
class MenuPresenter extends BasePresenter
{

	// -- DB
	/** @var DbTable\Admin_menu @inject */
	public $admin_menu;
	/** @var DbTable\Hlavne_menu_lang @inject */
	public $hlavne_menu_lang;
	/** @var DbTable\Hlavne_menu_template $hlavne_menu_template @inject */
	public $hlavne_menu_template;

	/**
	 * Vráti kompletné menu v json 
	 * @param String $lmodule Modul, pre ktorý sa vatvárajú odkazy v submenu */
	public function actionGetMenu(String $lmodule = "Admin"): void
	{
		$menu = new Menu\Menu;
		$menu->setNastavenie($this->nastavenie);
		$hl_m = $this->hlavne_menu->getMenuAdmin(1);
		if (count($hl_m)) {
			$servise = $this;
			$menu->fromTable($hl_m, function ($node, $row) use ($servise, $lmodule) {
				foreach (["name", "tooltip", "avatar", "anotacia", "node_class", "id", "datum_platnosti"] as $v) {
					$node->$v = $row['node']->$v;
				}

				$presenter = is_array($row['node']->link) ? $row['node']->link[0] : $row['node']->link;
				$p_for_link = ($lmodule == "Front" && $presenter == "Menu:") ? "Clanky:" : $presenter;

				$node->link = is_array($row['node']->link) ?
					$servise->link(":" . $lmodule . ":" . $p_for_link, ["id" => $row['node']->id]) :
					$servise->link(":" . $lmodule . ":" . $p_for_link, []);
				return $row['nadradena'] ? $row['nadradena'] : null;
			});
		}
		$this->sendJson($menu->getApiMenu());
	}

	/** 
	 * Akcia vráti konkrétne submenu pre nadradenú položku
	 * @param int $id Id nadradenej polozky
	 * @param String $lmodule Modul, pre ktorý sa vatvárajú odkazy v submenu */
	public function actionGetSubmenu(int $id, String $lmodule = "Admin"): void
	{
		$tmp = $this->hlavne_menu_lang
			->findBy(['hlavne_menu.id_nadradenej' => $id, 'id_lang' => 1])
			->order('poradie ASC');
		$out = [];
		foreach ($tmp as $v) {
			$p = $lmodule == "front" && $v->hlavne_menu->druh->presenter == "Menu" ? "Clanky" : $v->hlavne_menu->druh->presenter;
			$out[$v->hlavne_menu->poradie] = [
				'id'    => $v->id_hlavne_menu,
				'order'  => $v->hlavne_menu->poradie,
				'name'  => $v->menu_name,
				'link'  => $this->link(":" . ucfirst($lmodule) . ":$p:", $v->id_hlavne_menu),
				'avatar' => $v->hlavne_menu->avatar,
				'node_class' => ($v->hlavne_menu->ikonka !== null && strlen($v->hlavne_menu->ikonka) > 2) ? $v->hlavne_menu->ikonka : null,
			];
		}
		$this->sendJson($out);
	}

	/**
	 * Uloženie zmeny v poradí submenu */
	public function actionSaveOrderSubmenu(?int $id = null): void
	{
		$_post = json_decode(file_get_contents("php://input"), true); // @help 1.)

		$this->sendJson([
			'result' => $this->hlavne_menu->saveOrderSubmenu($_post['items']) ? 'OK' : 'ERR'
		]);
	}

	/** Vráti administračné menu podľa úrovne registrácie */
	public function actionGetAdminMenu(): void
	{
		$am = $this->admin_menu->getAdminMenu($this->id_reg);

		foreach ($am as $k => $v) {
			$am[$k]['link'] = $this->link(':Admin:' . $v['link']);
		}

		$this->sendJson($am);
	}

	/** Vráti jednu položku hlavne_menu_lang */
	/*public function actionGetOneMenuArticle(?int $id = null): void {
		$tmp = $this->hlavne_menu_lang->getOneArticleId($id, 1, $this->id_reg);
		$this->sendJson($tmp->toArray());
	}*/

	/** Akcia uloží editovaný text článku */
	public function actionTextsSave(int $id): void
	{
		$_post = json_decode(file_get_contents("php://input"), true); // @help 1.)

		$_tp = $this->hlavne_menu_lang->saveText($id, $this->lang->getLngId($this->language), $_post['texts']) ? 'OK' : 'ERR';
		$this->actionGetOneMenuArticle($id, $_tp);
	}

	/** 
	 * Akcia uloží editovaný text článku 
	 * @param int $id hlavne_menu_lang.id  */
	public function actionH1Save(int $id): void
	{
		$_post = json_decode(file_get_contents("php://input"), true); // @help 1.)

		$_tp = $this->hlavne_menu_lang->h1Save($id, $_post['article'])  ? 'OK' : 'ERR';
		$this->actionGetOneMenuArticle($id, $_tp);
	}

	/** Vráti jednu položku hlavne_menu */
	public function actionGetOneHlavneMenuArticle(?int $id = null): void
	{
		$tmp = $this->hlavne_menu->findOneBy(['id' => $id, 'id_user_roles <=' . $this->id_reg]);
		//dumpe($tmp->toArray());
		$this->sendJson($tmp->toArray());
	}

	/** Vráti jednu položku hlavne_menu_lang */
	public function actionGetOneMenuArticle(int $id, string|null $result = null): void
	{
		try {
			$tmp = $this->hlavne_menu_lang->getOneArticleAPI($id, $this->id_reg);
		} catch (DbTable\ArticleMainMenuException $e) {
			$tmp = ['error' => $e->getCode()];
		}
		if ($result != null) {
			$tmp = array_merge($tmp, ['result' => $result]);
		}
		$this->sendJson($tmp);
	}

	/** Vráti jednu položku hlavne_menu_lang */
	public function actionGetOneMenuArticleSp(String $id): void
	{
		try {
			$tmp = $this->hlavne_menu_lang->getOneArticleSpAPI($id, $this->id_reg);
		} catch (DbTable\ArticleMainMenuException $e) {
			$tmp = ['error' => $e->getCode()];
		}
		$this->sendJson($tmp);
	}

	/** Akcia uloží okrajové rámčeky pre položku */
	public function actionSaveBorder(int $id): void
	{
		$_post = json_decode(file_get_contents("php://input"), true); // @help 1.)

		$this->sendJson([
			'result' => $this->hlavne_menu->changeBorders($_post['borders'], $id)->toArray(),
		]);
	}

	/** Vráti jednu položku hlavne_menu_lang */
	public function actionGetForFormTemplate(): void
	{
		try {
			$tmp = $this->hlavne_menu_template->formItems();
		} catch (DbTable\ArticleMainMenuException $e) {
			$tmp = ['error' => $e->getCode()];
		}
		$this->sendJson($tmp);
	}

	/**
	 * Robí zmeny v tabuľke hlavne_menu	 */
	public function actionSaveMainMenuField(int $id): void
	{
		$_post = json_decode(file_get_contents("php://input"), true); // @help 1.)

		$this->hlavne_menu->uloz($_post['data'], $id);

		$this->actionGetOneMenuArticle($_post['id_hlavne_menu_lang']);
	}
}
