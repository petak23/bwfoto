<?php

namespace App\AdminModule\Components\User\AddPasswordLess;

use DbTable;
use Nette\Application\UI\Form;
use Nette\Database;

/**
 * Formular a jeho spracovanie pre pridanie uzivatela bez opravneia
 * Posledna zmena 11.03.2024
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.3
 */
class AddPasswordLessFormFactory
{
	/** @var DbTable\User_main */
	private $user_main;
	/** @var DbTable\User_categories */
	private $user_categories;
	/** @var DbTable\User_in_categories */
	private $user_in_categories;

	/**
	 * @param DbTable\User_categories $user_categories
	 * @param DbTable\User_in_categories $user_in_categories
	 * @param DbTable\User_main $user_main */
	public function __construct(
		DbTable\User_categories $user_categories,
		DbTable\User_in_categories $user_in_categories,
		DbTable\User_main $user_main
	) {
		$this->user_categories = $user_categories;
		$this->user_in_categories = $user_in_categories;
		$this->user_main = $user_main;
	}

	/**
	 * Edit hlavne menu form component factory. */
	public function create(): Form
	{
		$cat = $this->user_categories->findBy(['main_category' => 'B'])->fetchPairs('id', 'name');
		$form = new Form();
		$form->addProtection();
		$form->addGroup();
		$form->addText('titul_pred', 'Titul pred menom:', 15, 15);
		$form->addText('meno', 'Meno a PRIEZVISKO:', 50, 80)
			->addRule(Form::MIN_LENGTH, 'Meno a priezvisko musí mať spoň %d znakov!', 3)
			->setRequired('Meno a priezvisko musí byť zadané!');
		$form->addText('titul_za', 'Titul za menom:', 15, 15);
		$form->addEmail('email', 'E-mailová adresa', 50, 50)
			->addRule(Form::EMAIL, 'Musí byť zadaná korektná e-mailová adresa(napr. janko@hrasko.sk)')
			->setRequired(FALSE);
		$form->addMultiSelect('categori', 'Rodič:', $cat)
			->setHtmlAttribute('size', count($cat));
		$form->onValidate[] = [$this, 'validateEditUserForm'];
		$form->addSubmit('uloz', 'Ulož')
			->setHtmlAttribute('class', 'btn btn-success')
			->onClick[] = [$this, 'editUserFormSubmitted'];
		$form->addSubmit('cancel', 'Cancel')->setHtmlAttribute('class', 'btn btn-default')
			->setValidationScope([]);
		return $form;
	}

	/** 
	 * Vlastná validácia
	 * @param Nette\Application\UI\Form $button */
	public function validateEditUserForm($button): void
	{
		$values = $button->getForm()->getValues();
		if ($button->isSubmitted()->name == 'uloz') {
			// Over, ci dany email uz existuje.
			$ue = $this->user_main->findOneBy(['email' => $values->email]);
			if ($ue != FALSE && $ue->email != null) {
				$button->addError(sprintf('Zadaný e-mail %s už existuje! Zvolte prosím iný!', $values->email));
			}
		}
	}

	/** 
	 * Spracovanie vstupov z formulara
	 * @param Nette\Forms\Controls\SubmitButton $button Data formulara */
	public function editUserFormSubmitted($button): void
	{
		$values = $button->getForm()->getValues();
		$categori = $values->categori;
		unset($values->categori);
		try {
			$user_main = $this->user_main->add($values->name, $values->email == "" ? null : $values->email, 'A654-*@+askjhdk--', 1, 1);
			$this->user_in_categories->saveMultiCategori($user_main->id, $categori);
		} catch (Database\DriverException $e) {
			$button->addError($e->getMessage());
		}
	}
}
