<?php

declare(strict_types=1);

namespace App\AdminModule\Components\User\ContactCategori;

use Nette\Application\UI\Control;

/**
 * Komponenta pre vytvorenie zoznamu uzivatelov a kontaktov na nich 
 * cez kontaktny formular a odoslanie e-mailu
 * 
 * Posledna zmena(last change): 04.01.2023
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.0.1
 */

class ContactCategoriControl extends Control
{

  /** @see Nette\Application\Control#render() */
  public function render()
  {
    $this->template->setFile(__DIR__ . '/ContactCategori.latte');
    $this->template->render();
  }
}


interface IContactCategoriControl
{
  function create(): ContactCategoriControl;
}
