<?php

namespace App\AdminModule\Presenters;

use App\AdminModule\Components;
use DbTable;
use Nette\Application\Responses\FileResponse;
use Nette\Application\UI\Form;

/**
 * Prezenter pre smerovanie na dokumenty a editaciu popisu dokumentu.
 * 
 * Posledna zmena(last change): 04.01.2023
 *
 * Modul: ADMIN
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.1.0
 */

class DokumentyPresenter extends BasePresenter
{

  // -- DB
  /** @var DbTable\Dokumenty @inject */
  public $dokumenty;

  /** @var \Nette\Database\Table\ActiveRow */
  private $dokument;

  // -- Forms
  /** @var Components\Clanky\PrilohyClanok\EditPrilohyFormFactory @inject */
  public $editPrilohyFormFactory;

  /** Vychodzia akcia
   * @param int $id Id polozky na zobrazenie */
  public function actionDefault($id)
  {
    if (($this->dokument = $this->dokumenty->find($id)) === FALSE) {
      $this->error('Dokument s id=' . $id . ', ktorý hľadáte, sa žiaľ nenašiel!');
    }
    if (!is_file($this->dokument->main_file)) {
      $this->error('Dokument, ktorý hľadáte, sa žiaľ nenašiel alebo neexzistuje! ' . $this->dokument->main_file);
    }
    $response = new FileResponse($this->dokument->main_file, $this->dokument->name . "." . $this->dokument->pripona, 'application/' . $this->dokument->pripona);
    $this->sendResponse($response);
  }

  /** Akcia pre editaciu informacii o dokumente
   * @param int $id Id dokumentu na editaciu */
  public function actionEdit($id)
  {
    if (($this->dokument = $this->dokumenty->find($id)) === FALSE) {
      return $this->error(sprintf("Pre zadané id som nenašiel prílohu! id=' %s'!", $id));
    }
    $this["dokumentEditForm"]->setDefaults($this->dokument);
  }

  /** Render pre editaciu prilohy. */
  public function renderEdit()
  {
    $this->template->h2 = 'Editácia údajov dokumentu:' . $this->dokument->name;
  }

  /** Formular pre editaciu info. o dokumente. */
  protected function createComponentDokumentEditForm(): Form
  {
    $form = $this->editPrilohyFormFactory->create($this->dokument->id_hlavne_menu, $this->template->basePath);
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
