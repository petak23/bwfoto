<?php
namespace App\AdminModule\Presenters;

/**
 * Prezenter pre administraciu hlavneho menu.
 * Posledna zmena(last change): 08.11.2021
 *
 * Modul: ADMIN
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2021 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.1.2
 */
class MenuPresenter extends ArticlePresenter {
  
  /** Render pre defaultnu akciu */
  public function renderDefault() {
    parent::renderDefault();
		
    $this->template->uroven = $this->zobraz_clanok->hlavne_menu->uroven+2;

    // Pre iste specialne pripady nastav ine sablony
    if ($this->zobraz_clanok->hlavne_menu->absolutna !== NULL) {
      $this->setTemplate('absolute');
    } elseif ($this->hlavne_menu->findBy(["id_nadradenej"=>$this->zobraz_clanok->hlavne_menu->id])->count() == 0) {
      $this->setTemplate('nullArticle');
    }
  }
  
  /** Akcia pre 1. krok pridania polozky - udaje pre hl. menu.
   * @param int $id - id nadradenej polozky
   * @param int $uroven - uroven menu
   */
  public function actionAdd($id, $uroven) {
		$this->menuformuloz["redirect"] = "Menu:default";
    parent::actionAdd($id, $uroven);
	}
	
  /** Akcia pre 1. krok editovania clanku - udaje pre hl. menu.
   * @param int $id - id editovanej polozky
   */
  public function actionEdit($id) {
    $this->menuformuloz["redirect"] = "Menu:default";
    parent::actionEdit($id);
	}
}