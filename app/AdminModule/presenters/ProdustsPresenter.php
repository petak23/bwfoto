<?php
namespace App\AdminModule\Presenters;

use DbTable;

/**
 * Prezenter pre smerovanie na dokumenty a editaciu produktov.
 * 
 * Posledna zmena(last change): 28.03.2020
 *
 * Modul: ADMIN
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2020 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.0.1
 */

class ProductsPresenter extends BasePresenter {
	
  // -- DB
  /** @var DbTable\Products @inject */
	public $products;
  
  /** @var \Nette\Database\Table\ActiveRow */
  private $product;
  
  /** @var \Nette\Database\Table\Selection */
  protected $products_data;

  /** Vychodzia akcia
   * @param int $id Id produktu na zobrazenie */
	public function actionDefault($id) {
		if (($this->product = $this->products->find($id)) === FALSE) {
      $this->error('Produkt, ktorý hľadáte, sa žiaľ nenašiel!');
    }
		$this->redirectUrl("http://".$this->nazov_stranky."/".$this->product->main_file);
		exit;
	}

  /** Akcia pre nastavenie časti produktov */
  public function actionSetup() {
		$this->products_data = $this->udaje->getDruh("Products");
	}
  
  /** Render pre nastavenie časti produktov. */
	public function renderSetup() {
		$this->template->h2 = 'Nastavenia časti produktov';
    $this->template->products_data = $this->products_data;
	}
  
  /** Akcia pre editaciu informacii
   * @param int $id Id produktu na editaciu */
  public function actionEdit($id) {
		if (($this->product = $this->products->find($id)) === FALSE) { 
      $this->error('Produkt, ktorý hľadáte, sa žiaľ nenašiel!');
    }
    $this["editForm"]->setDefaults($this->product);
  }
  
  /** Render pre editaciu produktu. */
	public function renderEdit() {
		$this->template->h2 = 'Editácia údajov produktu:'.$this->product->name;
	}

  /** Formular pre editaciu info. o dokumente.
	 * @return Nette\Application\UI\Form */
	protected function createComponentSetupForm() {
    $ft = new \App\AdminModule\Forms\Products\SetupProductDataFormFactory($this->udaje, $this->user/*, $this->nastavenie['wwwDir']*/);
    $form = $ft->create(/*$this->upload_size, $this->prilohy_adresar*/);
//    $form->setDefaults($this->product);
    $form['uloz']->onClick[] = function ($button) {
      $this->flashOut(!count($button->getForm()->errors), 'Products:setup', 'Nastavenia boli úspešne uložené!', 'Došlo k chybe a zmena sa neuložila. Skúste neskôr znovu...');
		};
    $form['cancel']->onClick[] = function () {
			$this->redirect('Products:setup');
		};
		return $this->_vzhladForm($form);
	}
  
  /** Formular pre editaciu info. o dokumente.
	 * @return Nette\Application\UI\Form */
	protected function createComponentEditForm() {
    $ft = new \App\AdminModule\Forms\Products\EditProoductFormFactory($this->products, $this->udaje, $this->user, $this->nastavenie['wwwDir']);
    $form = $ft->create($this->upload_size, $this->prilohy_adresar);
    $form->setDefaults($this->product);
    $form['uloz']->onClick[] = function ($button) {
      $this->flashOut(!count($button->getForm()->errors), ['Clanky:#products-tab', ['id'=>$this->product->id_hlavne_menu, 'tab'=>'products-tab']], 'Produkt bol úspešne uložený!', 'Došlo k chybe a zmena sa neuložila. Skúste neskôr znovu...');
		};
    $form['cancel']->onClick[] = function () {
			$this->redirect('Clanky:#products-tab', ['id'=>$this->product->id_hlavne_menu, 'tab'=>'products-tab']);
		};
		return $this->_vzhladForm($form);
	}
}