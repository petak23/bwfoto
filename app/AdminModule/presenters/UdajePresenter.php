<?php

namespace App\AdminModule\Presenters;

use App\AdminModule\Forms\Udaje;
use DbTable;
use Nette\Application\UI\Form;
use PeterVojtech\Confirm\ConfirmationDialog;

/**
 * Prezenter pre administraciu hlavnych udajov webu.
 * 
 * Posledna zmena(last change): 08.02.2024
 *
 *	Modul: ADMIN
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.1.5
 */
class UdajePresenter extends BasePresenter
{

	/** @persistent */
	public $separate = 0;
	/** @var array */
	private $editFormDefaults;

	// -- DB
	/** @var DbTable\Druh @inject */
	public $druh;
	/** @var DbTable\Udaje_typ @inject */
	public $udaje_typ;

	// -- Forms
	/** @var Udaje\AddTypeUdajeFormFactory @inject*/
	public $addTypeUdajeForm;
	/** @var Udaje\EditUdajeFormFactory @inject*/
	public $editUdajeForm;

	/** Akcia pre pridanie udaju prvy krok */
	//public function actionAdd()
	//{
	//}

	/** 
	 * Akcia pre pridanie udaju druhy krok
	 * @param int $type */
	public function actionAdd2(int $type = 0)
	{
		$this->editFormDefaults = [
			'id_user_roles' => 5,
			'id_udaje_typ' => $type,
			'id_druh' => null,
			'nazov' => '',
			'text' => '',
			'comment' => ''
		];
		//$this["editUdajeForm"]->setDefaults(['id_udaje_typ'  => $type]);
		$this->setView('edit');
	}
	/** 
	 * Akcia pre editaciu udaju 
	 * @param int $id */
	public function actionEdit(int $id = 0)
	{
		if (($this->editFormDefaults = $this->udaje->find($id)->toArray()) === null) {
			$this->setView('notFound');
		}
	}

	public function renderDefault()
	{
		//Vyber len nastavenia na ktore je opravnanie a nemaju vlastne nastavenie inde
		$this->template->udaje_w = $this->udaje->findBy($this->separate ? ['id_user_roles <=' . $this->id_reg] : ['id_user_roles <=' . $this->id_reg, 'separate_settings' => 0]);
		$this->template->separate = $this->separate;
		$this->template->addFilter('uprav_radio', function ($com) {
			$tb = strpos($com, '[');
			$po = explode(';', substr($com, $tb + 1, strpos($com, ']') - $tb - 1));
			$out = [];
			foreach ($po as $key => $v) {
				$tt = explode('=', $v);
				$out[(int)$tt[0]] = $tt[1];
			}
			return $out;
		});
	}

	/**
	 * Formular pre urcenie typu udaju */
	protected function createComponentAddTypeUdajeForm(): Form
	{
		$form = $this->addTypeUdajeForm->create($this->udaje_typ->findAll()->fetchPairs('id', 'nazov'));
		$form['uloz']->onClick[] = function ($button) {
			$values = $button->getForm()->getValues();
			$this->redirect('Udaje:add2', $values->id_udaje_typ);
		};
		$form['cancel']->onClick[] = function () {
			$this->redirect('Udaje:');
		};
		return $this->_vzhladForm($form);
	}

	/** 
	 * Formular pre editaciu udaov */
	protected function createComponentEditUdajeForm(): Form
	{
		$form = $this->editUdajeForm->create(
			$this->user->isInRole("admin"),
			$this->druh->findAll()->fetchPairs('id', 'popis'),
			$this->ur_reg,
			$this->editFormDefaults
		);
		$form['uloz']->onClick[] = function ($form) {
			$this->flashOut(!count($form->errors), 'Udaje:', 'Údaj bol uložený!', 'Došlo k chybe a údaj sa neuložil. Skúste neskôr znovu...' . (isset($form->errors[0]) ? " Možná príčina: " . $form->errors[0] : ""));
		};
		$form['cancel']->onClick[] = function () {
			$this->redirect('Udaje:');
		};
		return $this->_vzhladForm($form);
	}

	/** 
	 * Funkcia pre spracovanie signálu vymazavania
	 * @param int $id - id polozky v hlavnom menu */
	function confirmedDelete($id)
	{
		$this->_ifMessage($this->udaje->zmaz($id) == 1, 'Údaj bol úspešne vymazaný!', 'Došlo k chybe a údaj nebol vymazaný!');
		if (!$this->isAjax()) {
			$this->redirect('Udaje:');
		}
	}

	/**
	 * Zostavenie otázky pre ConfDialog s parametrom */
	public function questionDelete(ConfirmationDialog $dialog, array $params): string
	{
		$dialog->getQuestionPrototype();
		return sprintf("Naozaj chceš zmazať údaj: %s?", isset($params['nazov']) ? "'" . $params['nazov'] . "'" : "");
	}

	/** Signal pre zobrazenie/ukritie separatnych nastaveni */
	public function handleViewSeparate()
	{
		$this->separate = 1 - $this->separate;
		$this->redirect('this');
	}
}
