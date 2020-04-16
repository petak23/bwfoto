<?php
namespace App\AdminModule\Components\Clanky\Komponenty;

use DbTable;
use Nette;
use Nette\Security\User;

/**
 * Komponenta pre spravu priloh clanku.
 * 
 * Posledna zmena(last change): 14.12.2018
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com> 
 * @copyright Copyright (c) 2012 - 2018 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.1.0
 */

class KomponentyControl extends Nette\Application\UI\Control {

  /** @var DbTable\Clanok_komponenty */
	public $clanok_komponenty;
  /** @var string $nazov_stranky */
  private $nazov_stranky;
  /** @var Nette\Database\Table\ActiveRow $clanok Info o clanku */
  private $clanok;
  /** @var array */
  private $nastavenie_k;
  /** @var Nette\Security\User */
  private $user;


  /**
   * @param array $nastavenie_k Nastavenie obrazkov pre prilohy - Nastavenie priamo cez servises.neon
   * @param DbTable\Clanok_komponenty $clanok_komponenty
   * @param User $user */
  public function __construct($nastavenie_k,
                              DbTable\Clanok_komponenty $clanok_komponenty,
                              User $user) {
//    parent::__construct();
    $this->clanok_komponenty = $clanok_komponenty;
    $this->user = $user;
    $this->nastavenie_k = $nastavenie_k;
  }
  
  /** 
   * Nastavenie komponenty
   * @param Nette\Database\Table\ActiveRow $clanok
   * @param string $nazov_stranky
   * @param string $name
   * @return \App\AdminModule\Components\Clanky\PrilohyClanok\PrilohyClanokControl */
  public function setTitle(Nette\Database\Table\ActiveRow $clanok, $nazov_stranky/*, $name*/) {
    $this->clanok = $clanok;
    $this->nazov_stranky = $nazov_stranky;
  }
  
  protected function createComponentBase() {
//    $ctmp = $this->clanok_komponenty->getKomponenty($this->clanok->id_hlavne_menu, $this->nastavenie_k);
//    $control = new BaseControl();
//    foreach ($ctmp as $k) {
//      $control->addComponent(new $k['route']($k['service']), $k['nazov']);
//    }
//    return $control;
  } 
  
  protected function createComponent($name) {
//    $ctmp = $this->clanok_komponenty->getKomponentyName($this->clanok->id_hlavne_menu, $this->nastavenie_k);
//		if(in_array($name, $ctmp)) {
//      $co = $this->nastavenie_k[$name];
//      $tmp = new $co["route"]($co['service']);
//			return $tmp;
//    } 
//		return parent::createComponent($name);
  } 
  
  /** 
   * Render */
	public function render() {
    $this->template->setFile(__DIR__ . '/Komponenty.latte');
    $this->template->clanok = $this->clanok;
    //Zisti, ci su k clanku priradene komponenty
    $this->template->komponenty = $this->clanok_komponenty->getKomponenty($this->clanok->id_hlavne_menu, $this->nastavenie_k);
    //Kontrola jedinecnych komponent. Ak uz su priradene tak sa vypustia
    $this->template->zoznam_komponent = $this->clanok_komponenty->testJedinecnosti($this->nastavenie_k, $this->clanok->id_hlavne_menu);
		$this->template->render();
	}
}

interface IKomponentyControl {
  /** @return KomponentyControl */
  function create();
}