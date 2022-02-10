<?php
namespace App\ApiModule\Presenters;

use DbTable;

/**
 * Prezenter pre pristup k api dokumentov.
 * Posledna zmena(last change): 03.02.2022
 *
 * Modul: API
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2022 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.0.0
 */
class DokumentyPresenter extends BasePresenter {

  // -- DB
  /** @var DbTable\Dokumenty @inject */
  public $documents;

  /**
   * Vráti posledné prihlásenia
   * @param int $count Počet zobrazených prihlásení */
  public function actionDocument(int $id) { 
    $this->sendJson($this->documents->getDocument($id));
  }
}