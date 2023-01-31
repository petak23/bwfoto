<?php

namespace App\ApiModule\Presenters;

use DbTable;
use Language_support;

/**
 * Prezenter pre pristup k api jazykov.
 * Posledna zmena(last change): 31.01.2023
 *
 * Modul: API
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2022 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.0.0
 */
class LangPresenter extends BasePresenter
{

  // -- DB
  /** @var DbTable\Lang @inject */
  public $lang;

  /** @var Language_support\LanguageMain @inject */
  public $texts;

  public function __construct(array $parameters)
  {
    // Nastavenie z config-u
    $this->nastavenie = $parameters;
  }

  protected function startup(): void
  {
    parent::startup();

    // Nastav jazyk
    $lang_temp = $this->lang->findOneBy(['skratka' => $this->params['language']]);
    if (isset($lang_temp->skratka) && $lang_temp->skratka == $this->params['language']) {
      $this->language = $this->params['language'];
      $this->language_id = $lang_temp->id;
    }
    //Nastavenie textov podla jazyka 
    $this->texts->setLanguage($this->language);
  }

  /**
   * Vráti preložené texty */
  public function actionGetTexts(): void
  {
    /* from POST: */
    $values = json_decode(file_get_contents("php://input"), true); // @help 1.)

    //dumpe($values);
    $out = [];
    if ($values['texts'] !== null) {
      foreach ($values['texts'] as $v) {
        $out[$v] = $this->texts->translate($v);
      }
    }
    //if ($this->isAjax()) {
    $this->sendJson($out);
    /*} else {
      $this->redirect('');
    }*/
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

  /**
   * Uloženie zmeny v poradí submenu */
  public function actionSaveOrder(?int $id = null): void
  {
    $_post = json_decode(file_get_contents("php://input"), true); // @help 1.)

    //dumpe($_post['items']);
    $this->sendJson([
      'result' => $this->slider->saveOrder($_post['items']) ? 'OK' : 'ERR'
    ]);
  }
}
