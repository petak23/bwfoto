<?php

namespace PeterVojtech\Clanky\ZobrazKartyPodclankov;

use Nette;
use DbTable;

/**
 * Komponenta pre zobrazenie odkazu na iny clanok
 * Posledna zmena(last change): 04.01.2023
 * 
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com> 
 * @copyright Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.7
 */
class AdminZobrazKartyPodclankovControl extends Nette\Application\UI\Control
{

  /** @var DbTable\Hlavne_menu_lang */
  //	public $hlavne_menu_lang;

  /** @var Nette\Database\Table\Selection */
  protected $articles;

  /** @var array */
  private $paramsFromConfig;

  /** @param DbTable\Hlavne_menu_lang $hlavne_menu_lang */
  public function __construct(/*DbTable\Hlavne_menu_lang $hlavne_menu_lang*/)
  {
    parent::__construct();
    //    $this->hlavne_menu_lang = $hlavne_menu_lang;
  }

  /**
   * Parametre z komponenty.neon
   * @param array $params */
  public function fromConfig(array $params): AdminZobrazKartyPodclankovControl
  {
    $this->paramsFromConfig = $params;
    return $this;
  }

  /** 
   * Nacitanie zobrazovanych podclankov
   * @param int $id nadradeneho clanku */
  //  public function setArticle(int $id): AdminZobrazKartyPodclankovControl {
  //    $this->articles = $this->hlavne_menu_lang->findBy(["id_lang"=>1, "id_nadradenej"=>$id]);
  //    return $this;
  //  }

  /** 
   * Render
   * @param array $p Parametre: template - pouzita sablona
   * @see Nette\Application\Control#render() */
  public function render($p = [])
  {
    //    $p_hlm = $this->article->hlavne_menu; //Pre skratenie zapisu
    $this->template->setFile(__DIR__ . "/AdminZobrazKartyPodclankov_default.latte");
    //    $this->template->link_presenter = $p_hlm->druh->presenter == "Menu" ? "Clanky:" : $p_hlm->druh->presenter.":";
    //    $this->template->articles = $this->articles;
    $this->template->render();
  }
}

interface IAdminZobrazKartyPodclankovControl
{
  function create(): AdminZobrazKartyPodclankovControl;
}
