<?php

namespace PeterVojtech;

use DbTable;
use Language_support;
use Nette\Application\UI\Control;

/**
 * Komponenta pre vypis pokladnicky.
 * 
 * Posledna zmena(last change): 27.11.2019
 * 
 *	Modul: ADMIN
 * 
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2019 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.0.0
  */
class FrontBaseControl extends Control {

  /** @var Language_support\lang_supp_main */
  public $texts;
  /** @var array */
  private $paramsFromConfig;
  
  /**
   * Parametre z komponenty.neon
   * @param array $params
   * @return this */
  public function fromConfig(array $params) {
    $this->paramsFromConfig = $params;
    return $this;
  }

  public function main_render() {
    $this->template->setTranslator($this->texts);
    $this->template->render();
  }
}