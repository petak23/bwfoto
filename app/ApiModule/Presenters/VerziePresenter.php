<?php

declare(strict_types=1);

namespace App\ApiModule\Presenters;

use Nette\Utils;
use PeterVojtech\Email;

/**
 * Prezenter pre pristup k api verzií.
 * Posledna zmena(last change): 05.03.2025
 *
 * Modul: API
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2025 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.0.7
 */
class VerziePresenter extends BasePresenter
{

	public function actionDefault(): void
  {
    $this->sendJson($this->verzie->vsetky(true));
  }

	/**
	 * Vráti konkrétnu verziu
	 * @param int $id Id verzie */
	public function actionGetVersion(int $id): void
	{
		$this->sendJson($this->verzie->find($id)->toArray());
	}

	/** Uloženie verzie do DB 
	 * @param int $id Id_hlavne_menu, ku ktorému ukladám dokument */
	public function actionSave(int $id): void
	{
		$_post = json_decode(file_get_contents("php://input"), true);
		//dumpe($_post['to_save']);
		$sk = $this->verzie->ulozVerziu(Utils\ArrayHash::from([
			'id'				=> $id,
			'cislo'     => $_post['number'],
			'text'      => $_post['text'],
			'modified'  => date("Y-m-d H:i:s"),
		]), true);
		$upload = $sk !== null ? ['status' => 200, 'data' => $this->verzie->vsetky(true)] : ['status'  => 500, 'data' => null];

		$this->sendJson($upload);
	}

	public function actionDelete(int $id): void 
	{
		$out = $this->user->isAllowed('Api:Verzie','del') ? 
						($this->verzie->zmaz($id) ? ['status' => 200] : ['status' => 500]) : ['status'=> 404];
		$this->sendJson($out);
	}

	/** Signal pre odoslanie informacneho emailu */
  public function actionSendInfoEmail($id): void
  {
    $values = $this->verzie->find($id);
    $params = [
      "site_name" => $this->nazov_stranky,
      "cislo"     => $values->cislo,
      "text"      => $values->text,
      "odkaz"     => $this->link("Verzie:default"),
    ];
    try {
      $send = $this->emailControl->nastav(__DIR__ . '/../templates/Verzie/verzie-html.latte', 1, 4)
        ->send($params, 'Nová verzia stránky');
			$out = [ 'status' => 200, 'message'=> 'E-mail bol odoslany v poriadku na emaily: ' . $send];
    } catch (Email\SendException $e) {
			$out = ['status'=> 500, 'message'=> $e->getMessage()];
    }
    $this->sendJson($out);
  }
}