<?php

declare(strict_types=1);

namespace App\AdminModule\Components\User\AddPasswordLess;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;

/**
 * Komponenta pre vytvorenie pridanie užívateľa bez oprávnenia.
 * 
 * Posledna zmena(last change): 04.01.2023
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com> 
 * @copyright Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.2
 */
class AddPasswordLessControl extends Control
{
  /** @var AddPasswordLessFormFactory */
  public $addPasswordLessFormFactory;

  /**
   * @param \App\AdminModule\Components\User\AddPasswordLess\AddPasswordLessFormFactory $addPasswordLessFormFactory */
  public function __construct(AddPasswordLessFormFactory $addPasswordLessFormFactory)
  {
    $this->addPasswordLessFormFactory = $addPasswordLessFormFactory;
  }

  /** 
   * Render */
  public function render()
  {
    $this->template->setFile(__DIR__ . '/addPasswordLess.latte');
    $this->template->render();
  }

  /** 
   * Komponenta formulara pre zmenu vlastnika. */
  public function createComponentAddPasswordLessForm(): Form
  {
    $form = $this->addPasswordLessFormFactory->create();
    $form['uloz']->onClick[] = function ($button) {
      $this->presenter->flashOut(!count($button->getForm()->errors), 'this', 'Zmena bola úspešne uložená!', 'Došlo k chybe a zmena sa neuložila. Skúste neskôr znovu...');
    };
    return $this->presenter->_vzhladForm($form);
  }
}

interface IAddPasswordLessControl
{
  function create(): AddPasswordLessControl;
}
