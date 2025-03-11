<?php

namespace App\ApiModule\Presenters;

use DbTable;
use Nette\Database;
use Nette\Http\FileUpload;
use Nette\Utils\Json;
use Nette\Utils\Random;
use PeterVojtech\Email;

/**
 * Prezenter pre pristup k api produktov.
 * Posledna zmena(last change): 11.03.2025
 *
 * Modul: API
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2025 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.1.2
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

	/** @var string */
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
	 * @param int $id Id_hlavne_menu, ku ktorému ukladám produkt */
	// ----------------------------------------
	// TODO Kontrola duplicitných súborov
	// TODO Kontrola max. veľkosti súboru
	// ----------------------------------------
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
		// Ak je užívateľ prihlásený, tak uprav údaje v jeho profile
		if ($this->user->isLoggedIn() && $values['adress']['email'] == $this->user->getIdentity()->email) {
			$u = $this->user_main->find($this->user->getId());
			if ($this->user->getIdentity()->name != $values['adress']['name']) $u->update(['name' => $values['adress']['name']]);
			$this->user_profiles->repair($u->id_user_profiles, $profile);
		} else { // Ak nie je prihlásený, tak vytvor nového "bez hesla" 
			$u = $this->user_main->add($values['adress']['name'], $values['adress']['email'], null, 0, 0, $profile);
		}

		// Ulož nákup
		$data_nakup = $this->nakup->add($u->id, $values);

		// Zarezervuj produkty z košíka
		foreach ($values['product'] as $p) {
			$this->products->repair($p['id_product'], ['id_products_status' => 2]);
		}

		// Pre prihláseného užívateľa
		if ($this->user->isLoggedIn() && $values['adress']['email'] == $this->user->getIdentity()->email) {
			// Pošli email o nákupe nakupujúcemu aj predávajúcemu.
			$out_u = $this->sendEmailAboutShopping($data_nakup);
			$out_s = $this->sendEmailAboutShopping($data_nakup, true); //správcovi

			//$out_u['status'] = 500;
			// Pošli info nazad o potvrdení a prechode na ukončenie.
			if ($out_u['status'] == 200 && $out_s['status'] == 200) { // email bol odoslaný
				$out = ['status' => 200, 'message' => "Informačný e-mail bol odoslaný"];
			} else {
				$out = [
					'status' => 500,
					'message' => "Pri posielaní informačného e-mailu"
						. ($out_u['status'] != 200 ? " užívateľovi " : "")
						. ($out_s['status'] != 200 ? ", správcovi " : "")
						. "došlo k chybe!"
				];
			}
		} else { // neprihlásený
			// Pošli email o potvrdení emailovej adresy	
			// Pošli info nazad o zaslaní infa o potvrdzovacom emaile
			$new_password_key = Random::generate(25);
			$params = [
				"site_name" => $this->nazov_stranky,
				"nadpis"    => sprintf($this->texty_presentera->translate('email_activate_nadpis'), $this->nazov_stranky),
				"email_activate_txt" => $this->texty_presentera->translate('email_activate_txt'),
				"email_nefunkcny_odkaz" => $this->texty_presentera->translate('email_nefunkcny_odkaz'),
				"email_pozdrav" => $this->texty_presentera->translate('email_pozdrav'),
				"nazov"     => $this->texty_presentera->translate('register_aktivacia'),
				"odkaz"     => 'http://' . $this->nazov_stranky . $this->link("Products:ActivateEmail", $u->id, $new_password_key, $data_nakup->id),
				"basePath"	=> $this->template->basePath,
			];
			try {
				$this->emailControl->sendMail(
					1, 
					$values['adress']['email'], 
					$this->texty_presentera->translate('register_aktivacia'), 
					null, 
					$params, 
					__DIR__ . '/../templates/emails/email_activate.latte'
				);
				$this->user_main->find($u->id)->update([
					'new_password_key' => $new_password_key,
					'new_password_requested' => date("Y-m-d H:i:s", Time())
				]);
				$this->emailControl->sendAdminMail("Overenie e-mailu", "Požiadavka na overenie e-mailu:" . $u->email);
				$out = ['status' => '200', 'message'=>$this->texty_presentera->translate('register_email_ok')];
			} catch (Email\SendException $e) {
				$out = ['status' => '404', 'message'=>$this->texty_presentera->translate('send_email_err')];
			}
		}
		//dumpe($out);
		$this->sendJson($out);
	}

	/** Funkcia pre odoslanie informacneho emailu */
	public function sendEmailAboutShopping(
		Database\Table\ActiveRow $data_nakup,
		bool $to_admin = false
	): string|array {
		$adress = [
			'name'		=> $data_nakup->user_main->name,
			'email'		=> $data_nakup->user_main->email,
			'street'	=> $data_nakup->user_main->user_profiles->street,
			'town'		=> $data_nakup->user_main->user_profiles->town,
			'psc'			=> $data_nakup->user_main->user_profiles->psc,
			'country'	=> $data_nakup->user_main->user_profiles->country,
			'phone'		=> $data_nakup->user_main->user_profiles->phone,
		];

		$params = [
			'product' => JSON::decode($data_nakup->product),
			'shipping' => JSON::decode($data_nakup->shipping), 
			'final_price' => $data_nakup->price, 
			'adress' => $adress,
			'data_nakup' => $data_nakup,
			'basePath' => $this->template->baseUrl,
		];
		$template = __DIR__ . '/../templates/emails/shopping_sumar' . ($to_admin ? '_admin' : '') . '.latte';
		$header = $to_admin ? "Nový nákup" : "Zhrnutie nákupu";
		$sp_id = $this->udaje->getValByName('nakup_spravca');
		$spravca = $this->user_main->find($sp_id)->email;
		$to = $to_admin ? $spravca : $data_nakup->user_main->email;
		try {
			$this->emailControl->sendMail(2, $to,	$header, null, $params,	$template);
			return ['status' => 200, 'message' => "OK"]; //$params; //"OK";
		} catch (Email\SendException $e) {
			return ['status' => 500, 'message' => $e->getMessage()];
		}
	}

	public function actionGetNakupy(): void
	{
	}

	public function actionGetLastNakup(): void
	{
		$this->sendJson($this->nakup->getLast());
	}

	public function actionGetNakupStatus(): void
	{
		$out = $this->nakup->getNakupStatus();
		$this->sendJson($out);
	}

	public function actionChangeNakupStatus(): void
	{
		/* from POST: */
		$values = json_decode(file_get_contents("php://input"), true); // @help 1.)

		$ch_status = $this->nakup->changeNakupStatus((int)$values["id_nakup"], (int)$values["change_to"]);

		$out = [
			'new_status' => $ch_status["id_nakup_status"],
			'ch_status' => $ch_status,
		];

		$params = [
			'code'	=> $ch_status['code'],
			'created'	=> $ch_status['created']->format("d.m.Y"),
			'shipping' => JSON::decode($ch_status['shipping'], JSON::FORCE_ARRAY), 
			'basePath' => $this->template->baseUrl,
		];
		$to = $this->user_main->find($ch_status['id_user_main'])->email;
		$template = __DIR__ . '/../templates/emails/nakup_' . $ch_status["id_nakup_status"] . '.latte';
		$message_er = "Pri odosielaní emailu došlo k neočakávanej chybe: %s . Skúste to, prosím, neskôr znovu. ";

		if ($ch_status["id_nakup_status"] >= 2 && $ch_status["id_nakup_status"] < 5) { // Ak je status OK
			
			if ($ch_status["id_nakup_status"] == 2) { // Akceptovanie nákupu
				$params = array_merge($params, [
					'ucet' => $this->udaje->getValByName('ucet'),
					'final_price' => $ch_status['price'],
				]);
				$header = "Akceptovanie nákupu";
				$message_ok = "Objednávateľovi bol zaslaný informačný e-mail o akceptácii objednávky.";
			} elseif ($ch_status["id_nakup_status"] == 3) { // Zaplatenie nákupu
				$header = "Zaplatenie nákupu";
				$message_ok = "Objednávateľovi bol zaslaný informačný e-mail o zaplatení objednávky.";
			} elseif ($ch_status["id_nakup_status"] == 4) { // Odoslanie nákupu
				$message_ok = "Objednávateľovi bol zaslaný informačný e-mail o odoslaní objednávky.";
				$header = "Odoslanie nákupu";
			} 

			try {
				$this->emailControl->sendMail(
					(int)$this->udaje->getValByName('nakup_spravca'), 
					$to,	
					$header, 
					null,
					$params,	
					$template
				);
				$out['message'] = $message_ok;
				$out['status'] = 200;
			} catch (Email\SendException $e) {
				$out['message'] = sprintf($message_er, $e->getMessage());
				$out['status'] = 500;
				$ch_status = $this->nakup->changeNakupStatus((int)$values["id_nakup"], (int)$ch_status["old_id_status"]);
			}
		}	elseif ($ch_status["id_nakup_status"] == 5) { // Uknčenie nákupu
			
			$products =  JSON::decode($ch_status['products']);
			//dump($products);
			foreach ($products as $p) {
				//dumpe($p);
				$ks = $this->products->find($p->id_product)->ks;
				$this->products->repair($p->id_product, [
					'id_products_status' => 3, // Predaný
					'ks' => $ks < 1 ? 0 : $ks - 1,
				]);
			}
			
			$out['message'] = "Objednávka bola ukončená.";
			$out['status'] = 200; 
		} else {
			$out['message'] = "Status objednávky je chybný.";
			$out['status'] = 500;
		}

		$this->sendJson($out);
	}

	/**
	 * Akcia pre aktivaciu emailu užívateľa, ktorý sa registruje počas nákupu
	 * @param int $id Id uzivatela
	 * @param string $new_password_key Kontrolny retazec pre aktivaciu */
	public function actionActivateEmail(int $id, string $new_password_key, int $id_nakup) : void {
		$user_main_data = $this->user_main->find($id); // Najdi uzivatela
		if ($new_password_key == $user_main_data->new_password_key) { //Aktivacne data su v poriadku
			$user_main_data->update(['id_user_roles' => 1, 'activated' => 1, 'new_password_key' => NULL]); // Aktivacia uzivatela
			$this->emailControl->sendAdminMail("Aktivácia", "Aktivácia užívateľa:" . $user_main_data->name);
			
			$nakup = $this->nakup->find($id_nakup);
			// Odoslanie info. e-mailu o nákupe objednávateľovi a aj správcovi
			$out_u = $this->sendEmailAboutShopping($nakup);
			$out_s = $this->sendEmailAboutShopping($nakup, true); //správcovi

			// Pošli info nazad o potvrdení a prechode na ukončenie.
			if ($out_u['status'] == 200 && $out_s['status'] == 200) { // email bol odoslaný
				$out = "Informačný e-mail o nákupe bol odoslaný.";
			} else {
				$out = "Pri posielaní informačného e-mailu"
						. ($out_u['status'] != 200 ? " užívateľovi " : "")
						. ($out_s['status'] != 200 ? ", správcovi " : "")
						. "došlo k chybe!";
			}

			$this->flashMessage($this->texty_presentera->translate('activate_ok'), 'success');
			$this->flashRedirect(':Front:User:', $out , 'success');

		} else { //Neuspesna aktivacia
			$this->flashMessage($this->texty_presentera->translate('activate_err2'), 'danger');
		}
		$this->redirect('Homepage:');
	}

}