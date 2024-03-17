<?php

declare(strict_types=1);

namespace App\AdminModule\Forms\User;

use DbTable;
use Nette\Application\UI\Form;
use Nette\Database;
//use Nette\Security\User;

/**
 * Tovarnicka pre formular na editaciu profilu uzivatela
 * Posledna zmena 13.03.2024
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.7
 */
class EditUserProfilesFormFactory
{
	/** @var DbTable\User_profiles */
	private $user_profiles;

	public function __construct(DbTable\User_profiles $user_profiles/*, DbTable\User_roles $user_roles, User $user*/)
	{
		$this->user_profiles = $user_profiles;
		//$this->urovneReg = $user_roles->urovneReg(($user->isLoggedIn()) ? $user->getIdentity()->id_user_roles : 0); //Hodnoty id=>nazov pre formulare z tabulky user_roles
	}
	/**
	 * Formular
	 * @param array $user_view_fields Nastavenia z config.neon */
	public function create(array $user_view_fields): Form
	{
		$form = new Form();
		$form->addProtection();
		$form->addHidden('id');
		if ($user_view_fields["rok"]) {
			$form->addText('rok', 'Rok narodenia:', 4, 5)
				->addRule(Form::RANGE, 'Rok narodenia musí byť v rozsahu od %d do %d', [1900, date("Y", Time())])
				->setRequired(FALSE);
		}
		if ($user_view_fields["phone"]) {
			$form->addText('phone', 'Telefón:', 20, 20);
		}
		if ($user_view_fields["poznamka"]) {
			$form->addText('poznamka', 'Poznámka:', 50, 250);
		}
		if ($user_view_fields["pohl"]) {
			$form->addSelect('pohl', 'Pohlavie:', ['M' => 'Muž', 'Z' => 'Žena']);
		}
		$form->addSubmit('uloz', 'Ulož')
			->setHtmlAttribute('class', 'btn btn-success')
			->onClick[] = [$this, 'editUserFormSubmitted'];;
		$form->addSubmit('cancel', 'Cancel')->setHtmlAttribute('class', 'btn btn-default')
			->setValidationScope([]);
		return $form;
	}

	/** 
	 * Spracovanie vstupov z formulara */
	public function editUserFormSubmitted(\Nette\Forms\Controls\SubmitButton $button)
	{
		$values = $button->getForm()->getValues();
		try {
			$this->user_profiles->saveUser($values);
		} catch (Database\DriverException $e) {
			$button->addError($e->getMessage());
		}
	}
}
