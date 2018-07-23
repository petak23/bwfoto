<?php

namespace App\FrontModule\Components\Clanky\PrilohyClanok;

use DbTable;
use Language_support;
use Nette\Application\UI\Control;

/**
 * Komponenta pre zobrazenie príloh clanku pre FRONT modul
 * 
 * Posledna zmena(last change): 16.01.2018
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2012 - 2018 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.7
 */
class PrilohyClanokControl extends Control {

  /** @var DbTable\Dokumenty */
  private $prilohy;
  /** @var Language_support\Clanky */
  public $texts;
  /** @var int */
  private $article;
  /** @var array */
  private $nastavenie;
  /** @var Nette\Database\Table\Selection|FALSE */
  private $attachments = NULL;

  /**
   * @param DbTable\Dokumenty $dokumenty
   * @param Language_support\Clanky $texts */
  public function __construct(DbTable\Dokumenty $dokumenty, Language_support\Clanky $texts) {
    parent::__construct();
    $this->prilohy = $dokumenty;
    $this->texts = $texts;
  }

  /** Nastavenie id polozky, ku ktorej patria prilohy
   * @param Nette\Database\Table\ActiveRow $article Polozka menu ku ktorej je priradeny
   * @param type $nastavenie Adresar avatara
   * @param int $id_lang Id jazyka
   * @return \App\FrontModule\Components\Clanky\PrilohyClanok\PrilohyClanokControl  */
  public function setNastav($article, $nastavenie, $id_lang) {
    $this->article = $article;
    $this->nastavenie = $nastavenie;
    $this->texts->setLanguage($id_lang);
    return $this;
  }

  /** Render */
  public function render($params = [], $template = '') {
    $template = ($template != '' ? "_" : "").$template;
    $template_file = ((isset($params['templateFile']) && is_file(__DIR__ ."/".$params['templateFile'].$template.".latte")) ? $params['templateFile'].$template : "PrilohyClanok_default");
    $this->template->setFile(__DIR__ . "/".$template_file.".latte");
    $this->template->prilohy = $this->attachments != NULL ? $this->attachments : $this->prilohy->getViditelnePrilohy($this->article->id_hlavne_menu);
    $this->template->texts = $this->texts;
    $this->template->nastavenie = $this->nastavenie;
    $this->template->id_hlavne_menu_lang = $this->article->id;
    $this->template->big_img_id = isset($params['big_img_id']) ? $params['big_img_id'] : 0;
    $this->template->render();
  }

  public function renderImages($params = []) {
    $this->attachments = $this->prilohy->getVisibleImages($this->article->id_hlavne_menu);
    $this->render($params, 'images');
  }
  
  public function renderOthers($params = []) {
    $this->attachments = $this->prilohy->getVisibleOther($this->article->id_hlavne_menu);
    $this->render($params, 'others');
  }
  
  public function renderVideos($params = []) {
    $this->attachments = $this->prilohy->getVisibleVideos($this->article->id_hlavne_menu);
    $this->render($params, 'videos');
  }

  protected function createTemplate($class = NULL) {
    $servise = $this;
    $template = parent::createTemplate($class);
    $template->addFilter('odkazdo', function ($id) use($servise) {
      $serv = $servise->presenter->link("Dokumenty:default", ["id" => $id]);
      return $serv;
    });

    return $template;
  }

}

interface IPrilohyClanokControl {
  /** @return PrilohyClanokControl */
  function create();
}
