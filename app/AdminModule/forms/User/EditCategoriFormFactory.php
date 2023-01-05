<?php

declare(strict_types=1);

namespace App\AdminModule\Forms\User;

use DbTable;
use Nette\Application\UI\Form;
use Nette\Database;
use Nette\Security\User;

/**
 * Tovarnicka pre formular na pridanie a editaciu kategorie
 * Posledna zmena 04.01.2023
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.4
 */
class EditCategoriFormFactory
{
  /** @var DbTable\User_categories */
  private $user_categories;
  /** @var array */
  private $urovneReg;


  public function __construct(DbTable\User_categories $user_categories, DbTable\User_roles $user_roles, User $user)
  {
    $this->user_categories = $user_categories;
    $this->urovneReg = $user_roles->urovneReg(($user->isLoggedIn()) ? $user->getIdentity()->id_user_roles : 0);
  }

  /**
   * Formular pre pridanie alebo editaciu kategorie */
  public function create(): Form
  {
    $form = new Form();
    $form->addProtection();
    $form->addHidden('id');
    $form->addText('nazov', 'Názov:', 30, 30)
      ->addRule(Form::MIN_LENGTH, 'Názov musí mať spoň %d znaky!', 3)
      ->setRequired('Názov musí byť zadaný!');
    $form->addText('fa_class', 'Ikonka:', 30, 30);
    $form->addSelect('id_user_roles', 'Úroveň registrácie člena:', $this->urovneReg);
    $form->addSubmit('uloz', 'Ulož')
      ->setHtmlAttribute('class', 'btn btn-success')
      ->onClick[] = [$this, 'editCategoriFormSubmitted'];
    $form->addSubmit('cancel', 'Cancel')->setHtmlAttribute('class', 'btn btn-default')
      ->setValidationScope([]);
    return $form;
  }

  /** 
   * Spracovanie vstupov z formulara */
  public function editCategoriFormSubmitted(\Nette\Forms\Controls\SubmitButton $button)
  {
    $values = $button->getForm()->getValues();
    try {
      $this->user_categories->saveCategori($values);
    } catch (Database\DriverException $e) {
      $button->addError($e->getMessage());
    }
  }
}
