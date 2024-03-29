<?php

declare(strict_types=1);

namespace App\AdminModule\Components\Article\TitleArticle;

use DbTable;
use Nette\Application\UI\Form;
use Nette\Database;
use Nette\Utils\Html;

/**
 * Formular a jeho spracovanie pre zmenu opravnenia podla kategorie polozky.
 * Posledna zmena 03.01.2023
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.2
 */
class ZmenOpravnenieKategoriaFormFactory
{
  /** @var DbTable\Hlavne_menu */
  private $hlavne_menu;

  /** @var array Hodnoty id=>nazov pre formulare z tabulky user_categories */
  private $user_categories;

  /**
   * @param DbTable\Hlavne_menu $hlavne_menu
   * @param DbTable\User_categories $user_categories */
  public function __construct(DbTable\Hlavne_menu $hlavne_menu, DbTable\User_categories $user_categories)
  {
    $this->hlavne_menu = $hlavne_menu;
    $this->user_categories = $user_categories->opravnenieForm();
  }

  /**
   * Formular.
   * @param Database\Table\ActiveRow $hlavne_menu Polozka v hlavnom menu */
  public function create(Database\Table\ActiveRow $hlavne_menu): Form
  {
    $form = new Form();
    $form->addProtection();
    $form->addHidden("id", (string)$hlavne_menu->id);
    $form->addGroup();
    if ($hlavne_menu->id_user_categories != null) {
      $form->addCheckbox("kategoria_null", " Zruš nastavenie kategórie")
        ->addCondition(Form::EQUAL, TRUE)
        ->toggle("zobr", FALSE);
      $form->addGroup()->setOption('container', Html::el('fieldset')->id("zobr"));
    }
    $ucat = $form->addSelect('id_user_categories', 'Kategória:', $this->user_categories)
      ->setOption('description', 'Výberom sa umožní prístup len užívateľom z danej kategórie.');
    if ($hlavne_menu->id_user_categories != null) {
      $ucat->setDefaultValue($hlavne_menu->id_user_categories);
      $form->setCurrentGroup(NULL);
    }
    $form->addSubmit('uloz', 'Zmeň')
      ->setHtmlAttribute('class', 'btn btn-success')
      ->onClick[] = [$this, 'formSubmitted'];
    $form->addSubmit('cancel', 'Cancel')
      ->setHtmlAttribute('class', 'btn btn-default')
      ->setHtmlAttribute('data-dismiss', 'modal')
      ->setHtmlAttribute('aria-label', 'Close')
      ->setValidationScope([]);
    return $form;
  }

  /** 
   * Spracovanie formulara.
   * @param \Nette\Forms\Controls\SubmitButton $button Data formulara */
  public function formSubmitted(\Nette\Forms\Controls\SubmitButton $button)
  {
    $values = $button->getForm()->getValues();   //Nacitanie hodnot formulara
    try {
      if (isset($values->kategoria_null) && $values->kategoria_null) {
        $values->offsetSet("id_user_categories", NULL);
      }
      unset($values->kategoria_null);
      $this->hlavne_menu->zmenOpravnenieKategoria($values);
    } catch (Database\DriverException $e) {
      $button->addError($e->getMessage());
    }
  }
}
