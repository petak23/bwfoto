<?php

namespace App\AdminModule\Forms\User;

use DbTable;
use Nette;
use Nette\Application\UI\Form;
use Nette\Security\User;

/**
 * Tovarnicka pre formular na pridanie uzivatela
 * Posledna zmena 04.01.2023
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.4
 */
class AddUserFormFactory
{
  /** @var DbTable\User_main */
  private $user_main;
  /** @var array */
  private $urovneReg;


  public function __construct(DbTable\User_main $user_main, DbTable\User_roles $user_roles, User $user)
  {
    $this->user_main = $user_main;
    $this->urovneReg = $user_roles->urovneReg(($user->isLoggedIn()) ? $user->getIdentity()->id_user_roles : 0); //Hodnoty id=>nazov pre formulare z tabulky user_roles
  }

  /**
   * Edit hlavne menu form component factory. */
  public function create(): Form
  {
    $form = new Form();
    $form->addProtection();
    $form->addText('meno', 'Meno', 50, 50)
      ->addRule(Form::MIN_LENGTH, 'Meno musí mať aspoň %s znakov', 2)
      ->setHtmlAttribute('autofocus', 'autofocus')
      ->setRequired('Meno musí byť zadané!');
    $form->addText('priezvisko', 'Priezvisko', 50, 50)
      ->addRule(Form::MIN_LENGTH, 'Priezvisko musí mať aspoň %s znakov', 3)
      ->setRequired('Priezvisko musí byť zadané!');
    $form->addEmail('email', 'E-mailová adresa', 50, 50)
      ->addRule(Form::EMAIL, 'Musí byť zadaná korektná e-mailová adresa(napr. janko@hrasko.sk)')
      ->setRequired('E-mailová adresa musí byť zadaná!');
    $form->addSelect('id_user_roles', 'Úroveň registrácie užívateľa:', $this->urovneReg);
    $form->addPassword('heslo', 'Heslo', 50, 50)
      ->addRule(Form::MIN_LENGTH, 'Heslo musí mať aspoň %s znakov', 5)
      ->setRequired('Heslo musí byť zadané!');
    $form->addPassword('heslo2', 'Zopakovanie hesla', 50, 50)
      ->addRule(Form::EQUAL, 'Heslo a zopakované heslo musí byť rovnaké!', $form['heslo'])
      ->setRequired('Zopakované heslo musí byť zadané!');
    $form->onValidate[] = [$this, 'validateAddUserForm'];
    $form->addSubmit('uloz', 'Ulož')
      ->setHtmlAttribute('class', 'btn btn-success')
      ->onClick[] = [$this, 'addUserFormSubmitted'];
    $form->addSubmit('cancel', 'Cancel')->setHtmlAttribute('class', 'btn btn-default')
      ->setValidationScope([]);
    return $form;
  }

  /** Vlastná validácia pre AddUserForm
   * @param Nette\Application\UI\Form $button
   */
  public function validateAddUserForm($button)
  {
    $values = $button->getForm()->getValues();
    if ($button->isSubmitted()->name == 'uloz') {
      // Over, ci dany email uz existuje.
      if ($this->user_main->testEmail($values->email)) {
        $button->addError(sprintf('Zadaný e-mail %s už existuje! Zvolte prosím iný!', $values->email));
      }
    }
  }

  /** 
   * Spracovanie vstupov z formulara */
  public function addUserFormSubmitted(Nette\Forms\Controls\SubmitButton $button)
  {
    $values = $button->getForm()->getValues();
    try {
      $this->user_main->add($values->meno, $values->priezvisko, $values->email, $values->heslo, 1, $values->id_user_roles);
    } catch (DbTable\DuplicateEmailException $e) {
      $button->addError($e->getMessage());
    }
  }
}
