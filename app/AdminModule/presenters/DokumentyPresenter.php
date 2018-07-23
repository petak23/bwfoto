<?php
namespace App\AdminModule\Presenters;

use DbTable;

/**
 * Prezenter pre smerovanie na dokumenty a editaciu popisu dokumentu.
 * 
 * Posledna zmena(last change): 20.07.2018
 *
 * Modul: ADMIN
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2018 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.0.6
 */

class DokumentyPresenter extends BasePresenter {
	
  // -- DB
  /** @var DbTable\Dokumenty @inject */
	public $dokumenty;
  
  /** @var \App\AdminModule\Components\Clanky\PrilohyClanok\EditPrilohyFormFactory @inject */
  public $editPrilohyForm;
  
  /** @var \Nette\Database\Table\ActiveRow */
  private $dokument;
  
  /** Vychodzia akcia
   * @param int $id Id polozky na zobrazenie */
	public function actionDefault($id) {
		if (($this->dokument = $this->dokumenty->find($id)) === FALSE) {
      $this->error('Dokument, ktorý hľadáte, sa žiaľ nenašiel!');
    }
		$this->redirectUrl("http://".$this->nazov_stranky."/".$this->nastavenie["prilohy_dir"].$this->dokument->main_file);
		exit;
	}

  /** Akcia pre editaciu informacii o dokumente
   * @param int $id Id dokumentu na editaciu */
  public function actionEdit($id) {
		if (($this->dokument = $this->dokumenty->find($id)) === FALSE) { 
      return $this->error(sprintf("Pre zadané id som nenašiel prílohu! id=' %s'!", $id)); 
    }
    $this["dokumentEditForm"]->setDefaults($this->dokument);
  }
  
  /** Render pre editaciu prilohy. */
	public function renderEdit() {
		$this->template->h2 = 'Editácia údajov dokumentu:'.$this->dokument->name;
	}

  /** Formular pre editaciu info. o dokumente.
	 * @return Nette\Application\UI\Form */
	protected function createComponentDokumentEditForm() {
    $form = $this->editPrilohyForm->create($this->upload_size, $this->nastavenie);
//    $form->setDefaults(["id"=>0, "id_hlavne_menu"=>$this->dokument->id_hlavne_menu, "id_user_roles"=>$this->dokument->hlavne_menu->id_user_roles]);
    $form->setDefaults($this->dokument);
    $form['uloz']->onClick[] = function ($button) {
      $this->flashOut(!count($button->getForm()->errors), ['Clanky:', $this->dokument->id_hlavne_menu], 'Príloha bola úspešne uložená!', 'Došlo k chybe a zmena sa neuložila. Skúste neskôr znovu...');
		};
    $form['cancel']->onClick[] = function () {
			$this->redirect('Clanky:', $this->dokument->id_hlavne_menu);
		};
		return $this->_vzhladForm($form);
	}
}