<?php
namespace App\AdminModule\Presenters;

use DbTable;

/**
 * Prezenter pre smerovanie na dokumenty a editaciu produktov.
 * 
 * Posledna zmena(last change): 08.07.2020
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
  
  /** @var DbTable\Dokumenty @inject */
	public $documents;
  
  /** @var \Nette\Database\Table\ActiveRow */
  private $product;
  
  /** @var \Nette\Database\Table\Selection */
  protected $products_data;

  /** Vychodzia akcia
   * @param int $id Id produktu na zobrazenie */
	public function actionDefault(/*$id*/) {
    $this->redirect('Products:setup');
//		if (($this->product = $this->products->find($id)) === null) {
//      $this->error('Produkt, ktorý hľadáte, sa žiaľ nenašiel!');
//    }
//		$this->redirectUrl("http://".$this->nazov_stranky."/".$this->product->main_file);
//		exit;
	}

//  public function actionUpdate() {
//    $docc = $this->documents->findAll();
//    foreach ($docc as $do) {
//      $out = [
//         'main_file' => $this->nastavenie['prilohy_dir'].$do->main_file,
//         'thumb_file' => $this->nastavenie['prilohy_dir'].$do->thumb_file
//      ];
//      $this->documents->oprav($do->id, $out);
//    }
//    $proc = $this->products->findBy(['id < 17']);
//    foreach ($proc as $do) {
//      $out = [
//         'main_file' => $this->nastavenie['dir_to_products'].$do->main_file,
//         'thumb_file' => $this->nastavenie['dir_to_products'].$do->thumb_file
//      ];
//      $this->products->oprav($do->id, $out);
//    }
//    $this->flashRedirect('Products:setup', "Opravené oba", "succese");
//  }
  
    
  /** Akcia pre nastavenie časti produktov */
  public function actionSetup() {
		$this->products_data = $this->udaje->getDruh("Products");
	}
  
  /** Render pre nastavenie časti produktov. */
	public function renderSetup() {
		$this->template->h2 = 'Nastavenia časti produktov';
    $this->template->products_data = $this->products_data;
	}
  
  /** 
   * Akcia pre editaciu informacii o produkte
   * @param int $id Id produktu na editaciu 
   * @param int $id_hlavne_menu Id polozky v hlavnom menu */
  public function actionEdit(int $id, int $id_hlavne_menu) {
		if (($this->product = $this->products->find($id)) == null) { 
      $this->flashRedirect(['Clanky:#products-tab', ['id'=>$id_hlavne_menu, 'tab'=>'products-tab']], 
                           "Produkt, ktorý chcete editovať, sa žiaľ nenašiel! Skúste to, prosím, neskôr.", 
                           "warning");
    } else {
      $this["editForm"]->setDefaults($this->product);
    }
  }
  
  /** Render pre editaciu produktu. */
	public function renderEdit() {
		$this->template->h2 = 'Editácia údajov produktu:'.$this->product->name;
	}

  /** 
   * Formular pre editaciu nastaveni casti produktov.
	 * @return Nette\Application\UI\Form */
	protected function createComponentSetupForm() {
    $ft = new \App\AdminModule\Forms\Products\SetupProductDataFormFactory($this->udaje, $this->user);
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
   * Formular pre editaciu info. o produkte.
	 * @return Nette\Application\UI\Form */
	protected function createComponentEditForm() {
    $ft = new \App\AdminModule\Forms\Products\EditProoductFormFactory($this->products, $this->udaje, $this->user, $this->nastavenie['wwwDir']);
    $form = $ft->create($this->nastavenie['dir_to_products']);
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