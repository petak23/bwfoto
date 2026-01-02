<?php

namespace App\AdminModule\Presenters;

use App\AdminModule\Forms\Products;
use DbTable;
use Nette\Application\UI\Form;
use Nette\Database\Table;
use Nette\Forms\Controls;
use PeterVojtech\Confirm\ConfirmationDialog;

/**
 * Prezenter pre smerovanie na dokumenty a editaciu produktov.
 * 
 * Posledna zmena(last change): 02.01.2026
 *
 * Modul: ADMIN
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2026 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.0.4
 */

class ProductsPresenter extends BasePresenter
{

	// -- DB
	/** @var DbTable\Products @inject */
	public $products;

	/** @var DbTable\Dokumenty @inject */
	public $documents;

	/** @var DbTable\Property @inject */
	public $property;

	/** @var DbTable\Property_categories @inject */
	public $property_cat;


	/** @var Table\ActiveRow */
	private $product;

	/** @var Table\Selection */
	protected $products_data;

	/** @var string */
  private $dir_to_products;
	/** @var string */
  private $wwwDir;

  public function __construct(string $dir_to_menu, string $dir_to_images, string $dir_to_products, string $wwwDir)
	{
    parent::__construct($dir_to_menu, $dir_to_images);
		// Nastavenie z config-u
    $this->dir_to_products = $dir_to_products;
		$this->wwwDir = $wwwDir;
	}

	protected function startup()
	{
		parent::startup();
		$this->addAdminMenu([
			//'id'    => $v->id,
			'link'  => "Products:ViewProps",
			'name'  => "Editácia vlastností",
			'avatar' => null
		]);
	}

	/** Vychodzia akcia */
	public function actionDefault()
	{
		$this->redirect('Products:setup');
	}

	/** Akcia pre nastavenie časti produktov */
	public function actionSetup()
	{
		$this->products_data = $this->udaje->getDruh("Products");
	}

	/** Render pre nastavenie časti produktov. */
	public function renderSetup()
	{
		$this->template->h2 = 'Nastavenia časti produktov';
		$this->template->products_data = $this->products_data;
	}

	/** 
	 * Akcia pre editaciu informacii o produkte
	 * @param int $id Id produktu na editaciu 
	 * @param int $id_hlavne_menu Id polozky v hlavnom menu */
	public function actionEdit(int $id, int $id_hlavne_menu)
	{
		if (($this->product = $this->products->find($id)) == null) {
			$this->flashRedirect(
				['Clanky:#products-tab', ['id' => $id_hlavne_menu, 'tab' => 'products-tab']],
				"Produkt, ktorý chcete editovať, sa žiaľ nenašiel! Skúste to, prosím, neskôr.",
				"warning"
			);
		} else {
			$this["editForm"]->setDefaults($this->product);
		}
	}

	/** Render pre editaciu produktu. */
	public function renderEdit()
	{
		$this->template->h2 = 'Editácia údajov produktu:' . $this->product->name;
	}

	/** 
	 * Formular pre editaciu nastaveni casti produktov. */
	protected function createComponentSetupForm(): Form
	{
		$ft = new Products\SetupProductDataFormFactory($this->udaje, $this->user);
		$form = $ft->create();
		$form['uloz']->onClick[] = function ($button) {
			$this->flashOut(!count($button->getForm()->errors), 'Products:setup', 'Nastavenia boli úspešne uložené!', 'Došlo k chybe a zmena sa neuložila. Skúste neskôr znovu...');
		};
		$form['cancel']->onClick[] = function () {
			$this->redirect('Products:setup');
		};
		return $this->_vzhladForm($form);
	}

	/** 
	 * Formular pre editaciu info. o produkte. */
	protected function createComponentEditForm(): Form
	{
		$ft = new Products\EditProoductFormFactory($this->products, $this->udaje, $this->user, $this->wwwDir);
		$form = $ft->create($this->dir_to_products);
		$form->setDefaults($this->product);
		$form['uloz']->onClick[] = function ($button) {
			$this->flashOut(!count($button->getForm()->errors), ['Clanky:#products-tab', ['id' => $this->product->id_hlavne_menu, 'tab' => 'products-tab']], 'Produkt bol úspešne uložený!', 'Došlo k chybe a zmena sa neuložila. Skúste neskôr znovu...');
		};
		$form['cancel']->onClick[] = function () {
			$this->redirect('Clanky:#products-tab', ['id' => $this->product->id_hlavne_menu, 'tab' => 'products-tab']);
		};
		return $this->_vzhladForm($form);
	}

	public $property_data;

	public function actionViewProps(): void
	{
		$this->property_data = $this->property->getAllProps();
	}

	public function renderViewProps(): void
	{
		$this->template->h2 = 'Nastavenie vlastností produktov';
		$this->template->property_data = $this->property_data;
	}

	public function actionEditProps(int $id = 0, int $cat = 0): void
	{
		if ($id) {
			$pp = $this->property->find($id);
		} else {
			$pp = [
				'id' => 0,
				'id_property_categories' => $cat,
			];
		}
		$this['editPropsForm']->setDefaults($pp);
	}

	public function createComponentEditPropsForm(): Form
	{
		$form = new Form();
		$form->addProtection();
		$form->addHidden('id')
			->addFilter(function ($value) {
				return (int)$value;
			});
		$form->addHidden('id_property_categories')
			->addFilter(function ($value) {
				return (int)$value;
			});
		$form->addText('name', "Názov vlastnosti:");
		$form->addFloat('price_increase_percentage', "Percentuálne navýšenie ceny:");
		$form->addFloat('price_increase_price', "Suma navýšenia ceny:");
		$form->addSubmit('uloz', 'Ulož')
			->setHtmlAttribute('class', 'btn btn-success')
			->onClick[] = [$this, 'editPropsFormSubmitted'];
		$form->addSubmit('cancel', 'Cancel')
			->setHtmlAttribute('class', 'btn btn-default')
			->setValidationScope([])
			->onClick[] = [$this, 'cancelPropsFormSubmitted'];
		return $this->_vzhladForm($form);
	}

	public function editPropsFormSubmitted(Controls\SubmitButton $button, $values): void
	{
		$id = $values->id;
		unset($values->id);
		$this->property->uloz($values, $id);
		$this->flashRedirect("Products:ViewProps", "Položka uložená", "success");
	}

	public function cancelPropsFormSubmitted(): void
	{
		$this->redirect("Products:ViewProps");
	}

	public function actionEditPropsCat(int $id = 0): void
	{
		if ($id) {
			$pp = $this->property_cat->find($id);
		} else {
			$pp = [
				'id' => 0,
			];
		}
		$this['editPropsCatForm']->setDefaults($pp);
	}

	public function createComponentEditPropsCatForm(): Form
	{
		$form = new Form();
		$form->addProtection();
		$form->addHidden('id')
			->addFilter(function ($value) {
				return (int)$value;
			});
		$form->addText('name', "Názov vlastnosti:");
		$form->addSubmit('uloz', 'Ulož')
			->setHtmlAttribute('class', 'btn btn-success')
			->onClick[] = [$this, 'editPropsCatFormSubmitted'];
		$form->addSubmit('cancel', 'Cancel')
			->setHtmlAttribute('class', 'btn btn-default')
			->setValidationScope([])
			->onClick[] = [$this, 'cancelPropsCatFormSubmitted'];
		return $this->_vzhladForm($form);
	}

	public function editPropsCatFormSubmitted(Controls\SubmitButton $button, $values): void
	{
		$id = $values->id;
		unset($values->id);
		$cat = $this->property_cat->uloz($values, $id);
		if ($id == 0) {
			$this->property->pridaj([
				'name' => "Vlastnosť pre: " . $cat->name,
				'id_property_categories' => $cat->id,
			]);
		}
		$this->flashRedirect("Products:ViewProps", "Položka uložená", "success");
	}

	public function cancelPropsCatFormSubmitted(): void
	{
		$this->redirect("Products:ViewProps");
	}

	/** 
	 * Funkcia pre spracovanie signálu vymazavania
	 * @param int $id - id polozky */
	function confirmedDelete($id)
	{
		$this->_ifMessage($this->property->zmaz($id['id']) == 1, 'Vlastnosť bola vymazaná!', 'Došlo k chybe a vlastnosť nebola vymazaná!');
		if (!$this->isAjax()) {
			$this->redirect('Products:ViewProps');
		}
	}

	/**
	 * Zostavenie otázky pre ConfDialog s parametrom */
	public function questionDelete(ConfirmationDialog $dialog, array $params): string
	{
		$dialog->getQuestionPrototype();
		return sprintf("Naozaj chceš zmazať údaj: %s?", isset($params['nazov']) ? "'" . $params['nazov'] . "'" : "");
	}
}
