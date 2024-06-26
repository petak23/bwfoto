<?php

namespace App\ApiModule\Presenters;

use DbTable;
use Nette\Application\Responses\FileResponse;
use Nette\Application\Responses\RedirectResponse;
use Nette\Http\FileUpload;
use Nette\Utils\Image;
use Nette\Utils\Strings;


/**
 * Prezenter pre pristup k api dokumentov.
 * Posledna zmena(last change): 24.05.2024
 *
 * Modul: API
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.1.2
 * 
 * @help 1.) https://forum.nette.org/cs/28370-data-z-post-request-body-reactjs-appka-se-po-ceste-do-php-ztrati
 */
class DokumentyPresenter extends BasePresenter
{

	// -- DB
	/** @var DbTable\Hlavne_menu_lang @inject */
	public $hlavne_menu_lang;
	/** @var DbTable\dokumenty @inject */
	public $documents;
	/** @var DbTable\products @inject */
	public $products;

	/** @var String */
	public $wwwDir;

	public function __construct(array $parameters, String $wwwDir)
	{
		// Nastavenie z config-u
		$this->nastavenie = $parameters;
		$this->wwwDir = $wwwDir;
	}

	/**
	 * Vráti informácie o dokumente
	 * @param int $id Dokumentu */
	public function actionDocument(int $id)
	{
		$this->sendJson($this->documents->getDocument($id));
	}

	/**
	 * Vráti relevantné prílohy
	 * @param int id Id hlavného menu */
	public function actionGetItems(int $id): void
	{
		$this->sendJson($this->documents->getDocumentsArray($id));
	}


	/** Uloženie dokumentu do DB 
	 * @param int $id Id_hlavne_menu, ku ktorému ukladám dokument */
	public function actionSave(int $id)
	{
		//$_post = json_decode(file_get_contents("php://input"), true);
		/* from POST:
		* - description
		* - type
		* - name
		*/
		$values = $this->getHttpRequest()->getPost();

		/* from POST:
		* - files
		* - thumb
		*/
		$files = $this->getHttpRequest()->getFiles();
		//dumpe($values, $files, is_array($files['files']));
		$user = $this->getUser();
		$hl_menu = $this->hlavne_menu->find($id);

		$data_save = [
			'id_hlavne_menu'     => $id,
			'id_user_main'      => $user->id,
			'id_user_roles'     => $hl_menu->id_user_roles,
			'description'        => isset($values['description']) && strlen($values['description']) > 2 ? $values['description'] : NULL,
			'change'            => date("Y-m-d H:i:s", Time()),
			'type'              => isset($values['type']) ? $values['type'] : 1,
			'name'              => (isset($values['name']) && strlen($values["name"]) > 2) ? $values['name'] : "",
		];

		if (is_array($files['files'])) { //MultiUpload
			$upload = [
				'status'  => 200,
				'data'    => [],
			];
			foreach ($files['files'] as $file) {
				$_up = $this->_saveDocument($file, 0, $data_save);
				if ($_up == null) {
					$upload['status'] = 500;
					$upload['data'][] = null;
				} else {
					$upload['data'][] = $_up;
				}
			}
		} else {  // SingleUpload
			$_up = $this->_saveDocument($files['files'], isset($values['id']) ? $values['id'] : 0, $data_save);
			$upload = $_up !== null ? ['status' => 200, 'data' => $_up] : ['status' => 500, 'data' => null];
		}

		if ($this->isAjax()) {
			$this->sendJson($upload);
		} else {
			$this->redirect(':Admin:Clanky:', $id);
		}
	}


	private function _saveDocument(FileUpload $file, int $id_doc, array $data_save): ?array
	{
		if ($file->error == 0) { //Ak nahravam prilohu
			$file_info = $this->_uploadPriloha($id_doc, $file);
			$data_save = array_merge($data_save, [
				'name'        => strlen($data_save['name']) > 2 ? $data_save['name'] : $file_info['finalFileName'],
				'web_name'    => Strings::webalize($file_info['finalFileName']),
				'pripona'      => $file_info['pripona'],
				'main_file'    => $this->nastavenie['prilohy_dir'] . $file_info['finalFileName'] . "." . $file_info['pripona'],
				'thumb_file'  => $file_info['thumb'],
				'type'        => $file_info['is_image'] ? 2 : $data_save['type']
			]);
		} /*elseif ($files['thumb']->hasFile() && $files['thumb']->isImage()) { //Ak nahravam len nahlad
			$data_save = array_merge($data_save, ['thumb_file'	=> $this->_uploadThumb($id_doc, $files['thumb'])]);
		} */

		$vysledok = $this->documents->uloz($data_save, $id_doc);
		if (!empty($vysledok) && isset($file_info['is_image']) && $file_info['is_image']) {
			$this->documents->repair($vysledok['id'], ['znacka' => '#I-' . $vysledok['id'] . '#']);
		}

		return !empty($vysledok) ? $this->documents->getDocument($vysledok['id']) : null;
	}


	/**
	 * Upload prilohy */
	private function _uploadPriloha(int $id, FileUpload $file): array
	{
		if ($id) { // Hladám, len ak mením
			$pr = $this->documents->find($id);
			if ($pr != null) { //Zmazanie starých súborov prílohy
				if (is_file($pr->main_file)) {
					unlink($this->wwwDir . "/" . $pr->main_file);
				}
				if (is_file($pr->thumb_file)) {
					unlink($this->wwwDir . "/" . $pr->thumb_file);
				}
			}
		}

		// Vytvor bezkonfliktné meno súboru na uloženie
		$fname = $this->_getFileName($file, $this->nastavenie['prilohy_dir']);

		//dumpe($file, $fname);
		//Presun subor na finalne miesto a ak je to obrazok tak vytvor nahlad
		$file->move($this->nastavenie['prilohy_dir'] . $fname['ename']);
		if ($file->isImage()) {  // Ak nahravam obrazok
			$image_name = $this->nastavenie['prilohy_dir'] . $fname['ename'];
			$thumb_name = $this->nastavenie['prilohy_dir'] . 'tb_' . $fname['ename'];
			$image = Image::fromFile($image_name);
			$image->resize($this->nastavenie['prilohy_images']['x'], $this->nastavenie['prilohy_images']['y'], Image::SHRINK_ONLY);
			$image->save($image_name, $this->nastavenie['prilohy_images']['kvalita']);
			copy($image_name, $thumb_name);
			$thumb = Image::fromFile($thumb_name);
			$thumb->resize($this->nastavenie['prilohy_images']['tx'], $this->nastavenie['prilohy_images']['ty'], Image::SHRINK_ONLY); // | Image::EXACT
			$thumb->save($thumb_name, $this->nastavenie['prilohy_images']['tkvalita']);
		} else { // Ak nahravam subor
			/*if ($values['thumb']->hasFile() && $values['thumb']->isImage()) { // Ak mam nahlad
				$thumbInfo = pathinfo($values['thumb']->name);
				$thumb_name = $this->nastavenie['prilohy_dir'].'tb_'.$fname['ename']; 
				$values['thumb']->move($thumb_name);
				$thumb = Image::fromFile($thumb_name);
				$thumb->resize($this->nastavenie['prilohy_images']['tx'], $this->nastavenie['prilohy_images']['ty'], Image::SHRINK_ONLY);// | Image::EXACT
				$thumb->save($thumb_name, $this->nastavenie['prilohy_images']['tkvalita']);
			} else {*/ // Ak nemam nahlad
			$thumb_name = is_file($this->wwwDir . "/ikonky/Free-file-icons-master/48px/" . $fname['extension'] . ".png") ? "www/ikonky/Free-file-icons-master/48px/" . $fname['extension'] . ".png" : NULL;
			//}
		}

		return [
			'finalFileName' => $fname['name'],
			'pripona'        => $fname['extension'],
			'thumb'          => isset($thumb_name) ? $thumb_name : NULL,
			'is_image'      => $file->isImage()
		];
	}

	/**
	 * Upload nahladu. Vracia kompletný názov uloženého súboru aj s reltívnou cestou.
	 * @param int $id Id dokumentu. Akje 0 tak sa pridáva
	 * @param FileUpload $thumb Súbor náhľadu. */
	private function _uploadThumb(int $id, FileUpload $thumb): String
	{
		if ($id) { // Mazanie sa vykoná len ak je $id > 0
			$pr = $this->documents->find($id); //Zmazanie starej prílohy
			if ($pr !== FALSE) {
				if (is_file($pr->thumb_file)) {
					unlink($this->wwwDir . "/" . $pr->thumb_file);
				}
			}
		}

		// Vytvor bezkonfliktné meno súboru na uloženie
		$fname = $this->_getFileName($thumb, $this->nastavenie['prilohy_dir'], 'tb_');

		// Uloženie náhľadu podľa paametrov
		$thumb->move($this->nastavenie['prilohy_dir'] . $fname['ename']);
		$thumb = Image::fromFile($this->nastavenie['prilohy_dir'] . $fname['ename']);
		$thumb->resize($this->nastavenie['prilohy_images']['tx'], $this->nastavenie['prilohy_images']['ty'], Image::SHRINK_ONLY); // | Image::EXACT
		$thumb->save($this->nastavenie['prilohy_dir'] . $fname['ename'], $this->nastavenie['prilohy_images']['tkvalita']);

		return $this->nastavenie['prilohy_dir'] . $fname['ename'];
	}

	/** 
	 * Vytvorenie bezpečného mena súboru 
	 * Vracia pole ['name'=>'meno súboru', 'ename'=>'meno súboru s príponou', 'extension'=>'prípona'] */
	private function _getFileName(FileUpload $file, String $dir, String $prefix = ""): array
	{
		$file_info = pathinfo($file->getSanitizedName());
		$additionalToken = 0;
		//Najdi meno suboru
		if (file_exists($dir . $prefix . $file_info['filename'] . $file_info['extension'])) {
			do {
				$additionalToken++;
			} while (file_exists($dir . $prefix . $file_info['filename'] . $additionalToken . $file_info['extension']));
		}

		return [
			'name' => $prefix . $file_info['filename'] . ($additionalToken == 0 ? '' : $additionalToken),
			'ename' => $prefix . $file_info['filename'] . ($additionalToken == 0 ? '' : $additionalToken) . "." . $file_info['extension'],
			'extension' => $file_info['extension']
		];
	}

	/** Vymazanie dokumentu z DB 
	 * @param int $id Iddokumentu */
	public function actionDelete(int $id)
	{
		if ($this->getUser()->isLoggedIn() && $this->getUser()->isAllowed($this->name, $this->action)) { //Preventývna kontrola
			$out = ($this->documents->removeFile($id)) ? ['status' => 200, 'data' => 'OK'] : ['status' => 500, 'data' => null];
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
			//dumpe($values['to_del']);
			$o = true;
			foreach ($values['to_del'] as $k => $v) {
				if (!$this->documents->removeFile($v)) $o = false;
			}
			$out = $o ? ['status' => 200, 'data' => 'OK'] : ['status' => 500, 'data' => null];
		} else {
			$out = ['status' => 401, 'data' => null]; //401 Unauthorized (RFC 7235) Používaný tam, kde je vyžadovaná autorizácia, ale zatiaľ nebola vykonaná. 
		}

		$this->sendJson($out);
	}

	/** 
	 * Oprava dokumentu v DB 
	 * @param int $id Id_hlavne_menu, ku ktorému ukladám dokument */
	public function actionUpdate(int $id): void
	{
		/* from POST: */
		$values = json_decode(file_get_contents("php://input"), true); // @help 1.)

		$this->documents->uloz($values, $id);
		$this->sendJson(['status' => 200, 'data' => 'OK']);
	}

	/**
	 * Nastaví počet položiek na stránku pre konkrétneho užívateľa */
	public function actionChangeperpage(): void
	{
		/* from POST: */
		$values = json_decode(file_get_contents("php://input"), true); // @help 1.)

		$out = $this->udaje->editKey('documents_per_page', $values['items_per_page'], $this->user->id);

		$this->sendJson($out != null ? ['status' => 200, 'data' => 'OK'] : ['status' => 500, 'data' => 'ER']);
	}

	/**
	 * Vráti počet položiek na stránku pre prihláseného používateľa */
	public function actionGetPerPage(): void
	{
		$this->sendJson((int)$this->udaje->getValByName('documents_per_page', $this->user->id));
	}

	/**
	 * Vráti prílohy pre fotokoláž */
	public function actionGetFotocollage(int $id): void
	{
		$this->sendJson($this->_ForCollage($this->documents->getForFotogalery($id)));
	}

	private function _ForCollage(array $att): array
	{
		$out = [];
		foreach ($att as $k => $v) {
			$out[] = [
				'id_foto'  => $v['id'],
				'source' => $this->template->basePath . "/" . $v['main_file'],
			];
		}
		return $out;
	}

	/**
	 * Načítanie príloh */
	public function actionGetFotogalery(int $id, int $filter = 1): void
	{
		// Výber podčlánkov
		$attachments = $this->_getForFotogalery($id);

		// Prílohy
		$attachments = array_merge($attachments, $this->documents->getForFotogalery($id));

		// Produkty
		$attachments = array_merge($attachments, $this->products->getForFotogalery($id, $filter));

		$this->sendJson($attachments);
	}

	/**
	 * Funkcia pre fotogalériu
	 * @param int id Id_hlavne_menu */
	private function _getForFotogalery(int $id): array
	{
		$out = [];
		foreach ($this->hlavne_menu_lang->findBy(["hlavne_menu.id_nadradenej" => $id, "lang.skratka" => $this->language]) as $v) {
			$av = 'files/menu/' . $v->hlavne_menu->avatar;
			$out[] = [
				'id' => $v->id,
				'type' => 'menu',
				'name' => $v->view_name,
				'web_name' => $this->presenter->link(':Front:Clanky:', $v->id_hlavne_menu),
				'description' => $v->anotacia,
				'main_file' => ($av && is_file($av)) ? $av : 'ikonky/128/list_ceruza128.png',
				'thumb_file' => ($av && is_file($av)) ? $av : 'ikonky/128/list_ceruza128.png',
			];
		}
		return $out;
	}

	public function actionGetVisibleAttachments(int $id, string $group = "images")
	{
		if ($group == "images") {
			$tmp = $this->documents->getVisibleImages($id);
		} elseif ($group == "audios") {
			$tmp = $this->documents->getVisibleAudios($id);
		} elseif ($group == "others") {
			$tmp = $this->documents->getVisibleOther($id);
		} else {
			$tmp = null;
		}
		$out = [];
		if ($tmp != null && $tmp->count() != 0) {
			foreach ($tmp as $v) {
				$_t =	$v->toArray();
				if ($group == "others") {
					$_t['fileDownload'] = $this->link("Dokumenty:fileDownload", $v->id);
					$_t['fileView'] = $this->link("Dokumenty:fileView", $v->id);
				}
				$out[] = $_t;
			}
		}
		$this->sendJson($out);
	}

	/**
	 * Signal pre odoslanie súboru na stiahnutie
	 * @param int $id Id dokumentu */
	public function actionFileDownload(int $id)
	{
		if (($dokument = $this->documents->find($id)) === null) {
			$this->error($this->texts->translate('dokument_not_found'));
		}
		if (!is_file($dokument->main_file)) {
			$this->error($this->texts->translate('dokument_not_found'));
		}
		$dokument->update(['pocitadlo' => $dokument->pocitadlo + 1]);
		try {
			$response = new FileResponse($dokument->main_file, $dokument->name . "." . $dokument->pripona, 'application/' . $dokument->pripona);
			$this->sendResponse($response);
		} catch (\Nette\Application\BadRequestException $ex) {
			$this->flashMessage('dokument_not_found', "danger");
		}
	}

	/**
	 * Signal pre odoslanie súboru na zobrazenie
	 * @param int $id Id dokumentu */
	public function actionFileView(int $id)
	{
		if (($dokument = $this->documents->find($id)) === FALSE) {
			$this->error($this->texts->translate('dokument_not_found'));
		}
		if (!is_file($dokument->main_file)) {
			$this->error($this->texts->translate('dokument_not_found'));
		}
		$dokument->update(['pocitadlo' => $dokument->pocitadlo + 1]);
		$httpR = $this->httpRequest->getUrl();
		$nazov_stranky = $httpR->host . $httpR->scriptPath; // Nazov stranky v tvare www.nieco.sk
		$nazov_stranky = substr($nazov_stranky, 0, strlen($nazov_stranky) - 1);
		try {
			$response = new RedirectResponse("http://" . $nazov_stranky . "/" . $dokument->main_file);
			$this->sendResponse($response);
		} catch (\Nette\Application\BadRequestException $ex) {
			$this->flashMessage('dokument_not_found', "danger");
		}
	}
}
