<?php

namespace App\FrontModule\Components\Clanky\PrilohyClanok;

use DbTable;
use Language_support;
use Nette\Application\UI\Control;

/**
 * Komponenta pre zobrazenie príloh clanku pre FRONT modul
 * 
 * Posledna zmena(last change): 07.11.2017
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2012 - 2016 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.4
 */
class PrilohyClanokControl extends Control {

  /** @var DbTable\Dokumenty */
  private $prilohy;
  /** @var Language_support\Clanky */
  public $texts;
  /** @var int */
  private $id_article;
  /** @var string */
  private $avatar_path;
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
   * @param int $id
   * @return \App\FrontModule\Components\Clanky\PrilohyClanokControl  */
  public function setNastav($id_article, $avatar_path, $id_lang) {
    $this->id_article = $id_article;
    $this->avatar_path = $avatar_path;
    $this->texts->setLanguage($id_lang);
    return $this;
  }

  /** Render */
  public function render($params = []) {
    $template_file = (isset($params['templateFile']) && is_file(__DIR__ ."/PrilohyClanok_".$params['templateFile'].".latte"))
                     ? $params['templateFile'] : "default";
    $this->template->setFile(__DIR__ . "/PrilohyClanok_".$template_file.".latte");
    $this->template->prilohy = $this->attachments != NULL ? $this->attachments : $this->prilohy->getViditelnePrilohy($this->id_article, "pripona DESC");
    $this->template->texts = $this->texts;
    $this->template->avatar_path = $this->avatar_path;
    $this->template->render();
  }

  public function renderImages($params = []) {
    $this->attachments = $this->prilohy->getVisibleImages($this->id_article);
    $this->render($params);
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
