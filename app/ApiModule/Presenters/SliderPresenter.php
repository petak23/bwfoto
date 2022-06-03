<?php

namespace App\ApiModule\Presenters;

use DbTable;

/**
 * Prezenter pre pristup k api slider-a.
 * Posledna zmena(last change): 03.06.2022
 *
 * Modul: API
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2022 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.0.0
 */
class SliderPresenter extends BasePresenter
{

  // -- DB
  /** @var DbTable\Slider @inject */
  public $slider;

  /** @var String */
  public $wwwDir;

  public function __construct(array $parameters, String $wwwDir)
  {
    // Nastavenie z config-u
    $this->nastavenie = $parameters;
    $this->wwwDir = $wwwDir;
  }

  /**
   * Vráti všetky položky slidera */
  public function actionGetAll(): void
  {
    $this->sendJson($this->slider->getSliderArray());
  }


  /** Vymazanie dokumentu z DB 
   * @param int $id Iddokumentu */
  public function actionDelete(int $id)
  {
    if ($this->getUser()->isLoggedIn() && $this->getUser()->isAllowed($this->name, $this->action)) { //Preventývna kontrola
      $out = $this->slider->remove($id) ? ['status' => 200, 'data' => 'OK'] : ['status' => 500, 'data' => null]; // 500 Internal Server Error
    } else {
      $out = ['status' => 401, 'data' => null]; //401 Unauthorized (RFC 7235) Používaný tam, kde je vyžadovaná autorizácia, ale zatiaľ nebola vykonaná. 
    }

    if ($this->isAjax()) {
      $this->sendJson($out);
    } else {
      $this->redirect(':Admin:Slider:');
    }
  }

  /** 
   * Oprava produktu v DB 
   * @param int $id Id_hlavne_menu, ku ktorému ukladám produkt 
   * */
  public function actionUpdate(int $id)
  {
    /* from POST: */
    //$values = $this->getHttpRequest()->getPost();
    $values = json_decode(file_get_contents("php://input"), true); // @help 1.)

    //dumpe($values);

    $this->slider->saveSlider($values, $id);
    if ($this->isAjax()) {
      $this->sendJson(['status' => 200, 'data' => 'OK']);
    } else {
      $this->redirect(':Admin:Slider:');
    }
  }
}
