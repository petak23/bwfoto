<?php

namespace App\AdminModule\Presenters;

use DbTable;
use Nette\Application\UI\Form;
use Nette\Utils\Html;

/**
 * Prezenter pre spravu jazykov.
 *
 * Posledna zmena(last change): 04.01.2023
 *
 * Modul: ADMIN
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.0.9
 */
class LangPresenter extends BasePresenter
{

  /** @var DbTable\Hlavne_menu_lang @inject */
  public $hlavne_menu_lang;
  /** @var array kontrola jazykovych nastaveni */
  private $kontrola = ['sk' => ['final' => TRUE]];

  /**
   * Kontrola jazykových mutácií pre údaje webu
   * @param string $pole
   * @param string $skratka
   * @param string $comment */
  private function kontrolujUdaj(string $pole, string $skratka, string $comment): void
  {
    $nazov_polozky = $pole . '-' . $skratka;
    $polozka = $this->udaje->findOneBy(['nazov' => $nazov_polozky]);
    if (!isset($polozka->id)) {
      $this->udaje->pridaj([
        'id_user_roles' => $this->ur_reg['manager'],
        'nazov'         => $nazov_polozky,
        'comment'       => $comment . ' pre jazyk:' . $skratka,
      ]);
      $this->kontrola[$skratka] = [
        'final' => FALSE,
        'komentar' => ['Nebolo vytvorené pole ' . Html::el('strong', $nazov_polozky) . ' v tabulke údaje. Pole bolo vytvorené, ale je prázdne. ' .
          Html::el('a')->href($this->link('Udaje:'))->setText('Môžete ho vyplniť tu!')->addAttributes(['title' => "Údaje"])],
      ];
    } elseif ($polozka->text === NULL) {
      $this->kontrola[$skratka] = [
        'final' => FALSE,
        'komentar' => ['Nie je vyplnené pole ' . Html::el('strong', $nazov_polozky) . ' v tabulke údaje. ' .
          Html::el('a')->href($this->link('Udaje:'))->setText('Môžete zmeniť tu!')->addAttributes(['title' => "Údaje"])],
      ];
    }
  }
  /**
   * Kontrola položiek hlavného menu na jazykove mutacie
   * @param \Nette\Database\Table\ActiveRow $lang */
  private function kontrolujHlavneMenu(\Nette\Database\Table\ActiveRow $lang): void
  {
    $menu = $this->hlavne_menu->findAll();
    foreach ($menu as $pol) {
      if ($this->hlavne_menu_lang->findOneBy(['id_lang' => $lang->id, 'id_hlavne_menu' => $pol->id]) === null) {
        $this->kontrola[$lang->skratka]['final'] = false;
        $odkaz = $this->link('Menu:edit', ["id" => $pol->id]);
        if (($nchp = $this->hlavne_menu_lang->findOneBy(['id_lang' => 1, 'id_hlavne_menu' => $pol->id])) !== null) {
          $tmp_hlasenie = $nchp->menu_name;
        } else {
          $tmp_hlasenie = $pol->id;
        }
        $this->kontrola[$lang->skratka]['komentar'][] = 'Nebola nájdená jazyková mutácia pre položku hlavného menu:<strong> '
          . $tmp_hlasenie . '</strong>. <a href="' . $odkaz . '" title="Menu">Môžete ho vyplniť tu!</a>';
      }
    }
  }
  public function actionDefault()
  {
    //kontrola
    $jaz = $this->lang->findAll();
    $this->kontrola = [];
    $polia = [
      'titulka' => "Názov zobrazený v titulke",
      'titulka_2' => "Druhá časť titulky",
      'keywords'  => "Kľúčové slová",
    ];
    foreach ($jaz as $j) {
      $skratka = $j->skratka;
      $this->kontrola[$skratka]['final'] = TRUE; //Prednastavena hodnota
      //Kontrola udajov
      foreach ($polia as $po => $va) {
        $this->kontrolujUdaj($po, $skratka, $va);
      }
      if ($j->skratka != 'sk') {
        $this->kontrolujHlavneMenu($j);
      } //Nekontroluj slovencinu pri hl. menu
      if ($this->kontrola[$skratka]['final'] === FALSE) { //Opakovana kontrola neuspesna
        $j->update(['prijaty' => 0]);
      } elseif ($j->prijaty == 0) { //Uspesna kontrola a ak este nie je akceptovany akceptuj.
        $j->update(['prijaty' => 1]);
      }
    }
  }
  public function renderDefault()
  {
    $this->template->jazyky = $this->lang->findAll();
    $this->template->kontrola = $this->kontrola;
  }
  public function actionAdd()
  {
    $this->template->h2 = 'Pridanie jazyka';
    $this->setView('edit');
  }
  /**
   * Akcia pre editaciu jazyka
   * @param int $id Id editovaneho jazyka */
  public function actionEdit(int $id): void
  {
    if (($jazyk = $this->lang->find($id)) === FALSE) {
      $this->setView('notFound');
    } else {
      $this->template->h2 = 'Editácia jazyka: ' . $jazyk->nazov;
      $this["langEditForm"]->setDefaults($jazyk);
    }
  }
  /**
   * Edit language form component factory. */
  protected function createComponentLangEditForm(): Form
  {
    $form = new Form();
    $form->addProtection();
    $form->addHidden('id');
    $form->addText('skratka', 'Skratka:', 3, 3)
      ->addRule(Form::MIN_LENGTH, 'Skratka musí mať spoň %d znaky!', 2)
      ->setRequired('Skratka musí byť zadaná!');
    $form->addText('nazov', 'Miestny názov:', 15, 15)
      ->addRule(Form::MIN_LENGTH, 'Miestny názov musí mať spoň %d znaky!', 2)
      ->setRequired('Skratka musí byť zadaná!');
    $form->addText('nazov_en', 'Anglický názov:', 15, 15)
      ->addRule(Form::MIN_LENGTH, 'Anglický názov musí mať spoň %d znaky!', 2)
      ->setRequired('Anglická skratka musí byť zadaná!');
    $form->addSubmit('uloz', 'Ulož')
      ->setHtmlAttribute('class', 'btn btn-success')
      ->onClick[] = [$this, 'langEditFormSubmitted'];
    $form->addSubmit('cancel', 'Cancel')->setHtmlAttribute('class', 'btn btn-default')
      ->setValidationScope([])
      ->onClick[] = function () {
      $this->redirect('Lang:');
    };
    return $this->_vzhladForm($form);
  }
  public function langEditFormSubmitted($button)
  {
    $values = $button->getForm()->getValues();         //Nacitanie hodnot formulara
    $id_pol = (int)$values->id; // Ak je == 0 tak sa pridava
    unset($values->id);
    $uloz = $this->lang->uloz($values, $id_pol);
    if (isset($uloz['id'])) { //Ulozenie v poriadku
      $this->flashRedirect('Lang:', 'Jazyk bol úspešne uložený!', 'success');
    } else {                          //Ulozenie sa nepodarilo
      $this->flashMessage('Došlo k chybe a jazyk sa neuložil. Skúste neskôr znovu...', 'danger');
    }
  }
}
