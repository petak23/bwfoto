<?php

namespace App\AdminModule\Components\Clanky\ZobrazKartyPodclankov;

use Nette;
use DbTable;
use Language_support;

/**
 * Komponenta pre zobrazenie odkazu na iny clanok
 * Posledna zmena(last change): 03.01.2023
 * 
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com> 
 * @copyright Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.6
 */
class ZobrazKartyPodclankovControl extends Nette\Application\UI\Control
{

  /** @var Language_support\Clanky */
  public $texts;
  /** @var DbTable\Hlavne_menu_lang */
  public $hlavne_menu_lang;
  /** @var DbTable\Lang */
  public $lang;
  /** @var Nette\Database\Table\Selection */
  protected $articles;

  public function __construct(/*DbTable\Hlavne_menu_lang $hlavne_menu_lang, DbTable\Lang $lang, Language_support\Clanky $texts*/)
  {
    parent::__construct();
    //    $this->hlavne_menu_lang = $hlavne_menu_lang;
    //    $this->lang = $lang;
    //    $this->texts = $texts;
  }

  /* * 
   * Nacitanie zobrazovanych podclankov
   * @param int $id nadradeneho clanku
   * @param int $id_lang id zobrazovaneho jazyka */
  //  public function setArticle($id, $id_lang = 1): \App\FrontModule\Components\Clanky\ZobrazKartyPodclankov\ZobrazKartyPodclankovControl {
  //    $this->texts->setLanguage($this->lang->find($id_lang)->skratka);
  //    $this->articles = $this->hlavne_menu_lang->findBy(["id_lang"=>$id_lang, "id_nadradenej"=>$id]);
  //    return $this;
  //  }

  /** 
   * Render
   * @param array $p Parametre: template - pouzita sablona
   * @see Nette\Application\Control#render() */
  public function render($p = [])
  {
    //    $p_hlm = $this->article->hlavne_menu; //Pre skratenie zapisu
    $this->template->setFile(__DIR__ . "/ZobrazKartyPodclankov_default.latte");
    //    $this->template->texty = $this->texts;
    //    $this->template->link_presenter = $p_hlm->druh->presenter == "Menu" ? "Clanky:" : $p_hlm->druh->presenter.":";
    //    $this->template->articles = $this->articles;
    $this->template->render();
  }
}

interface IZobrazKartyPodclankovControl
{
  function create(): ZobrazKartyPodclankovControl;
}
