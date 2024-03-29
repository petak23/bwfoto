<?php

namespace App\AdminModule\Components\Article;

use DbTable;
use Nette\Application\UI\Control;

/**
 * Komponenta pre vytvorenie ponuky na pridanie do hlavneho menu na zaklade druhu.
 * 
 * Posledna zmena(last change): 03.01.2023
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com> 
 * @copyright Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.7
 */
class AdminAddMenuControl extends Control
{

  /** @var string Pomocna chybova premenna s udajom, ktory je chybny */
  private $chyba = "";
  /** @var int */
  private $id;
  /** @var DbTable\Druh */
  private $druh;
  /** @var string $parentTemplate Nazov suboru template na zobrazenie */
  private $parentTemplate = "AdminAddMenu_default.latte";
  /** @var DbTable\Hlavne_menu_cast */
  public $hlavne_menu_cast;
  /** @var DbTable\Hlavne_menu_lang */
  public $hlavne_menu_lang;

  /**
   * @param int $id Id nadradenej polozky
   * @param DbTable\Druh $druh
   * @param DbTable\Hlavne_menu_cast $hlavne_menu_cast
   * @param DbTable\Hlavne_menu_lang $hlavne_menu_lang */
  public function __construct(
    int $id,
    DbTable\Druh $druh,
    DbTable\Hlavne_menu_cast $hlavne_menu_cast,
    DbTable\Hlavne_menu_lang $hlavne_menu_lang
  ) {
    $this->druh = $druh;
    $this->hlavne_menu_cast = $hlavne_menu_cast;
    $this->hlavne_menu_lang = $hlavne_menu_lang;
    $this->id = (int)$id;
  }

  /** 
   * Rucne nastavenie defaultnej template pre zobrazenie.
   * @param string|null $pt Nazov template */
  public function setClanokTemplate(?string $pt): self
  {
    if (isset($pt) && strlen($pt)) {
      if (is_file(__DIR__ . "/AdminAddMenu_" . $pt . ".latte")) {
        $this->parentTemplate = "AdminAddMenu_" . $pt . ".latte";
      }
    }
    return $this;
  }

  /** Vytvorenie ponuky */
  private function _addpolmenu(): array
  {
    $ponuka = $this->druh->findBy(["povolene" => 1, "je_spec_naz > 0"]);
    if ($this->id < 0) { //Zaporne id je pre cast
      $this->id = (-1 * $this->id);
      if ((($cast = $this->hlavne_menu_cast->find($this->id)) != null)) {
        $out["nadpis"] = $cast->view_name;
      } else {
        $this->chyba = "Chybne zadané id = " . $this->id;
        return [];
      }
      $uroven = 0;
    } else { //Kladne id je pre polozku
      if ((($cast = $this->hlavne_menu_lang->findOneBy(["id_hlavne_menu" => $this->id, "id_lang" => 1])) != null)) {
        $out["nadpis"] = $cast->view_name;
      } else {
        $this->chyba = "Chybne zadané id = " . $this->id;
        return [];
      }
      $uroven = $cast->hlavne_menu->uroven + 1;
    }
    foreach ($ponuka as $g) {
      $out["ponuka"][] = [
        "link" => $g->presenter . ":add",
        "id" => $this->id,
        "uroven" => $uroven,
        "presenter" => $g->presenter,
        "popis" => $g->popis,
      ];
    }
    return $out;
  }

  /** Render */
  public function render()
  {
    if (!$this->id) {
      $this->chyba = 'Došlo k chybe. Nie je zadané id!';  //Ak nemam id
    } else {
      $out = $this->_addpolmenu();
    } //Vytvorenie menu
    if ($this->chyba != "") { //Je nejaka chyba
      $this->template->setFile(__DIR__ . '/AdminAddMenu_error.latte');
      $this->template->text = $this->chyba;
    } else { //Vsetko je OK
      $this->template->setFile(__DIR__ . '/' .  $this->parentTemplate);
      $this->template->out = $out;
    }
    $this->template->render();
  }
}

interface IAdminAddMenu
{
  function create(int $id): AdminAddMenuControl;
}
