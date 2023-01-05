<?php

namespace App\AdminModule\Components\Faktury;

use Nette;

/**
 * Komponenta pre zobrazenie casti faktur pre ADMIN modul
 * Posledna zmena(last change): 03.01.2023
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.2
 */
class ViewFakturyControl extends Nette\Application\UI\Control
{
  /** @var int Zobrazená skupina */
  private $skupina;

  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Nastavenie skupiny */
  public function setSkupina($skupina): ViewFakturyControl
  {
    $this->skupina = $skupina;
    return $this;
  }

  public function render(): void
  {
    $this->template->setFile(__DIR__ . '/ViewFaktury.latte');
    $this->template->skupina = $this->skupina;
    $this->template->render();
  }
}
interface IViewFakturyControl
{
  function create(): ViewFakturyControl;
}
