<?php

declare(strict_types=1);

namespace PeterVojtech;

use Nette\Localization\Translator;
use Nette\Application\UI\Control;

/**
 * Komponenta pre vypis pokladnicky.
 * 
 * Posledna zmena(last change): 04.01.2023
 * 
 *	Modul: ADMIN
 * 
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.0.1
 */
class FrontBaseControl extends Control
{

  /** @var Translator */
  public $texts;
  /** @var array */
  private $paramsFromConfig;

  /**
   * Parametre z komponenty.neon */
  public function fromConfig(array $params): FrontBaseControl
  {
    $this->paramsFromConfig = $params;
    return $this;
  }

  public function main_render()
  {
    $this->template->setTranslator($this->texts);
    $this->template->render();
  }
}
