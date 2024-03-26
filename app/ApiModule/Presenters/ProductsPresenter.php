<?php

namespace App\ApiModule\Presenters;

use DbTable;
use Nette\Database;
use Nette\Http\FileUpload;
use Nette\Utils\Json;
use PeterVojtech\Email;

/**
 * Prezenter pre pristup k api produktov.
 * Posledna zmena(last change): 20.03.2024
 *
 * Modul: API
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.0.9
 * 
 * @help 1.) https://forum.nette.org/cs/28370-data-z-post-request-body-reactjs-appka-se-po-ceste-do-php-ztrati
 */
class ProductsPresenter extends BasePresenter
{

	// -- DB
	/** @var DbTable\Products @inject */
	public $products;
	/** @var DbTable\Property @inject */
	public $property;
	/** @var DbTable\Products_property @inject */
	public $products_property;

	/** @var String */
	public $wwwDir;
	/** @var array */
	private $products_settings;

	public function __construct(array $parameters, String $wwwDir)
	{
		// Nastavenie z config-u
		$this->nastavenie = $parameters;
		$this->wwwDir = $wwwDir;
	}

	/**
	 * Vráti informácie o produkte
	 * @param int $id Id produktu */
	public function actionProduct(int $id): void
	{
		$p = $this->products->getProduct($id);
		if ($p != null) {
			$p = array_merge(
				$p,
				$this->products_property->getProperties($id, $p['price'])
			);
		} else {
			$p = [
				'message' => "Not found",
				'status'	=> 401
			];
		}
		$this->sendJson($p);
	}

	/**
	 * Vráti relevantné produkty
	 * @param int id Id hlavného menu */
	public function actionGetItems(int $id): void
	{
		$this->sendJson($this->products->getProductsArray($id));
	}

	/** 
	 * Uloženie produktu/produktov do DB 
	 * @param int $id Id_hlavne_menu, ku ktorému ukladám produkt 
	 * ----------------------------------------
	 * @todo Kontrola duplicitných súborov
	 * @todo Kontrola max. veľkosti súboru
	 * ----------------------------------------
	 * */
	public function actionSave(int $id)
	{
		/* from POST:
		* - description
		* - name */
		$values = $this->getHttpRequest()->getPost();

		/* from POST:
		* - files */
		$files = $this->getHttpRequest()->getFiles();

		// Ak niet čo ukladať...
		if (!(isset($files['files']) && is_array($files['files']) && count($files['files']))) {
			$this->sendJson([
				'status'  => 500,
				'data'    => null,
				'error'   => 'Nothing to save...',
			]);
			exit;
		}
		$user = $this->getUser();
		$hl_menu = $this->hlavne_menu->find($id);
		$this->products_settings = $this->udaje->findBy(['id_druh' => 8])->fetchPairs('nazov', 'text');

		$data_save = [
			'id_hlavne_menu'     => $id,
			'id_user_main'      => $user->id,
			'id_user_roles'     => $hl_menu->id_user_roles,
			//'description'				=> isset($values['description']) && strlen($values['description'])>2 ? $values['description'] : NULL,
			//'name'              => (isset($values['name']) && strlen($values["name"]) > 2) ? $values['name'] : "",
			'panorama'          => $hl_menu->id_hlavne_menu_template == 6,
		];

		if (is_array($files['files'])) { //MultiUpload
			$upload = [
				'status'  => 200,
				'data'    => [],
			];
			foreach ($files['files'] as $file) {
				$_up = $this->_saveProduct($file, $data_save);
				if ($_up == null) {
					$upload['status'] = 500;
					$upload['data'][] = null;
				} else {
					$upload['data'][] = $_up;
				}
			}
		} else {  // SingleUpload
			$_up = $this->_saveProduct($files['files'], $data_save);
			$upload = $_up !== null ? ['status' => 200, 'data' => $_up] : ['status' => 500, 'data' => null];
		}

		if ($this->isAjax()) {
			$this->sendJson($upload);
		} else {
			$this->redirect(':Admin:Clanky:', $id);
		}
	}

	private function _saveProduct(FileUpload $file, array $data_save): ?array
	{
		$result = ($file->error == 0) ? $this->products->saveUpload(
			$file,
			[
				'products_settings' => $this->products_settings,
				'panorama' => $data_save['panorama'],
				'main_data' => [
					'id_hlavne_menu'  => $data_save['id_hlavne_menu'],
					'id_user_main'    => $data_save['id_user_main'],
					'id_user_roles'   => $data_save['id_user_roles'],
				]
			]
		) : 0; // Ak je chyba v uploade
		return $result ? $this->products->getProduct($result) : null;
	}

	/** Vymazanie dokumentu z DB 
	 * @param int $id Iddokumentu */
	public function actionDelete(int $id)
	{
		if ($this->getUser()->isLoggedIn() && $this->getUser()->isAllowed($this->name, $this->action)) { //Preventývna kontrola
			$out = $this->products->removeFile($id) ? ['status' => 200, 'data' => 'OK'] : ['status' => 500, 'data' => null]; // 500 Internal Server Error
		} else {
			$out = ['status' => 401, 'data' => null]; //401 Unauthorized (RFC 7235) Používaný tam, kde je vyžadovaná autorizácia, ale zatiaľ nebola vykonaná. 
		}

		if ($this->isAjax()) {
			$this->sendJson($out);
		} else {
			$this->redirect(':Admin:Clanky:', $id);
		}
	}

	/** Vymazanie viacerých dokumentu z DB */
	public function actionDeleteMore()
	{
		if ($this->getUser()->isLoggedIn() && $this->getUser()->isAllowed($this->name, $this->action)) { //Preventývna kontrola
			/* from POST: */
			$values = json_decode(file_get_contents("php://input"), true); // @help 1.)
			$o = true;
			foreach ($values['to_del'] as $k => $v) {
				if (!$this->products->removeFile($v)) $o = false;
			}
			$out = $o ? ['status' => 200, 'data' => 'OK'] : ['status' => 500, 'data' => null];
		} else {
			$out = ['status' => 401, 'data' => null]; //401 Unauthorized (RFC 7235) Používaný tam, kde je vyžadovaná autorizácia, ale zatiaľ nebola vykonaná. 
		}

		$this->sendJson($out);
	}

	/** 
	 * Oprava produktu v DB 
	 * @param int $id Id_hlavne_menu, ku ktorému ukladám produkt */
	public function actionUpdate(int $id): void
	{
		/* from POST: */
		$values = json_decode(file_get_contents("php://input"), true); // @help 1.)

		$this->products->saveProduct($values, $id);
		$this->sendJson(['status' => 200, 'data' => 'OK']);
	}

	/**
	 * Nastaví počet položiek na stránku pre konkrétneho užívateľa */
	public function actionChangeperpage(): void
	{
		/* from POST: */
		$values = json_decode(file_get_contents("php://input"), true); // @help 1.)

		$out = $this->udaje->editKey('products_per_page', $values['items_per_page'], $this->user->id);

		$this->sendJson($out != null ? ['status' => 200, 'data' => 'OK'] : ['status' => 500, 'data' => 'ER']);
	}

	/**
	 * Vráti počet položiek na stránku pre prihláseného používateľa */
	public function actionGetPerPage(): void
	{
		$this->sendJson((int)$this->udaje->getValByName('products_per_page', $this->user->id));
	}

	/** Vráti všetky vlastnosti */
	public function actionProductPropsCategories(): void
	{
		$this->sendJson($this->property->getAllProps());
	}

	public function actionSaveProductProps(): void
	{
		/* from POST: */
		$values = json_decode(file_get_contents("php://input"), true); // @help 1.)

		$out = $this->products_property->saveProductProps($values);
		$_price = $this->products->repair($values['id_products'], ['price' => $values['price']]);

		$this->sendJson(array_merge($out, ['price' => $_price->price]));
	}

	/* ----------------------------- Nákupy ------------------------------- */

	// -- DB
	/** @var DbTable\User_main @inject */
	public $user_main;
	/** @var DbTable\Nakup @inject */
	public $nakup;
	/** @var DbTable\User_profiles @inject */
	public $user_profiles;

	// -- Components
	/** @var Email\EmailControl @inject */
	public $emailControl;

	public function actionNakup(): void
	{
		/* from POST: */
		$values = json_decode(file_get_contents("php://input"), true); // @help 1.)
		//dumpe($values);

		$profile = [
			'phone' => $values['adress']['phone'],
			'street' => $values['adress']['street'],
			'town' =>	$values['adress']['town'],
			'psc' =>	$values['adress']['psc'],
			'country' => $values['adress']['country'],
			'adress2'	=> strlen($values['adress']['adress2']['town']) ? Json::encode($values['adress']['adress2']) : null,
			'firm'	=> strlen($values['adress']['firm']['name']) ? Json::encode($values['adress']['firm']) : null,
		];

		if ($this->user->isLoggedIn() && $values['adress']['email'] == $this->user->getIdentity()->email) {
			$u = $this->user_main->find($this->user->getId());
			if ($this->user->getIdentity()->name != $values['adress']['name']) $u->update(['name' => $values['adress']['name']]);
			$this->user_profiles->repair($u->id_user_profiles, $profile);
		} else {
			$u = $this->add($values['adress']['name'], $values['adress']['email'], null, 0, 0, $profile);
		}

		$data_nakup = $this->nakup->add($u->id, $values);

		foreach ($values['product'] as $p) {
			$this->products->repair($p['id_product'], ['id_products_status' => 2]); // Zarezervuj produkt
		}

		if ($this->user->isLoggedIn() && $values['adress']['email'] == $this->user->getIdentity()->email) {
			// Pošli email o nákupe nakupujúcemu aj predávajúcemu.
			// Pošli info nazad o potvrdení a prechode na ukončenie.
			$out = $this->sendEmailAboutShopping($values, $data_nakup);
			$out_s = $this->sendEmailAboutShopping($values, $data_nakup, true); //pošli info správcovi e-shopu
			if ($out['status'] == 200 && $out_s['status'] == 200) { // email bol odoslaný

			}
		} else {
			// Pošli email o potvrdení emailovej adresy
			// Pošli info nazad o zaslaní infa o potvrdzovacom emaile
			$out = ['status' => 200, 'message' => "Zatiaľ nič..."];
		}
		//dumpe($out);
		$this->sendJson($out);
	}

	/** Funkcia pre odoslanie informacneho emailu */
	public function sendEmailAboutShopping(
		array $values,
		Database\Table\ActiveRow $data_nakup,
		bool $to_admin = false
	): string|array {
		$params = [
			'product' => $values['product'],
			'shipping' => $values['shipping'],
			'final_price' => $values['final_price'],
			'dph' => $values['dph'],
			'adress' => $values['adress'],
			'data_nakup' => $data_nakup,
			'basePath' => $this->template->baseUrl,
		];
		$template = __DIR__ . '/../templates/emails/' . /*($to_admin ? 'shopping_admin' : */ 'shopping_sumar'/*)*/ . '.latte';
		$header = $to_admin ? "Nový nákup" : "Zhrnutie nákupu";
		$to = $to_admin ? "petak23@echo-msz.eu" : $values['adress']['email'];
		try {
			$this->emailControl->sendMail(2, $to,	$header, null, $params,	$template);
			return ['status' => 200, 'message' => "OK"]; //$params; //"OK";
		} catch (Email\SendException $e) {
			return ['status' => 500, 'message' => $e->getMessage()];
		}
	}
}
