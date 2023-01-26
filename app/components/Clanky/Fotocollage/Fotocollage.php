<?php

namespace PeterVojtech\Clanky\Fotocollage;

use DbTable;
use Language_support;
use Nette;

/**
 * Komponenta pre zobrazenie foto koláže k článku 
 * Posledna zmena(last change): 26.01.2023
 * 
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com> 
 * @copyright Copyright (c) 2021 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.3
 */
class FotocollageControl extends Nette\Application\UI\Control
{

  /** @var Language_support\LanguageMain */
  public $texts;

  /** @var DbTable\Hlavne_menu_lang */
  public $hlavne_menu_lang;
  /** @var DbTable\Lang */
  public $lang;
  /** @var DbTable\dokumenty */
  public $dokumenty;

  /** @var array */
  //private $paramsFromConfig;
  /** @var array Pole podčlánkov, príloh a produktov článku v JSON formáte*/
  private $attachments;
  /** @var int Počet podčlánkov, príloh a produktov článku*/
  private $attachments_count;
  /** @var string Skratka jazyka */
  private $language = 'sk';
  /** @var Nette\Database\Table\ActiveRow */
  private $hlavne_menu;
  /** @var int Pre zobrazenie konkrétnej fotky na začiatku */
  //private $first_id = 0;

  public function __construct(
    string $language,
    Nette\Database\Table\ActiveRow $hlavne_menu,
    DbTable\Hlavne_menu_lang $hlavne_menu_lang,
    DbTable\Lang $lang,
    DbTable\Dokumenty $dokumenty,
    Language_support\LanguageMain $texts
  ) {
    $this->hlavne_menu_lang = $hlavne_menu_lang;
    $this->lang = $lang;
    $this->language = $language;
    $this->texts = $texts;
    $this->texts->setLanguage($language);
    $this->hlavne_menu = $hlavne_menu;
    $this->dokumenty = $dokumenty;
  }

  /**
   * Nastavenie first_id */
  /*public function set_first_id(int $first_id = 0): FotocollageControl
  {
    $this->first_id = $first_id;
    return $this;
  }*/

  /**
   * Parametre z komponenty.neon */
  public function fromConfig(array $params): FotocollageControl
  {
    //$this->paramsFromConfig = $params;
    return $this;
  }

  /** 
   * Render funkcia pre vykreslenie fotogalérie
   * @param array $p Parametre: id_hlavne_menu - id odkazovaneho clanku, template - pouzita sablona
   * @see Nette\Application\Control#render() */
  public function render($p = [])
  {
    $this->getAttachments();
    $this->template->setFile(__DIR__ . "/Fotocollage" . (isset($p["template"]) && strlen($p["template"]) ? "_" . $p["template"] : "_default") . ".latte");
    $this->template->attachments = $this->attachments;
    $this->template->attachments_count = $this->attachments_count;
    $this->template->setTranslator($this->texts);
    $this->template->article_id = $this->hlavne_menu->id;

    $this->template->render();
  }

  /**
   * Načítanie príloh */
  public function getAttachments(): FotocollageControl
  {
    // Prílohy
    $attachments = $this->dokumenty->getForFotogalery($this->hlavne_menu->id);
    $this->attachments_count = count($attachments);
    $this->attachments = Nette\Utils\Json::encode($this->_ForCollage($attachments));
    return $this;
  }

  private function _ForCollage(array $att): array
  {
    $out = [];
    foreach ($att as $k => $v) {
      $out[] = [
        'id_foto'  => $v['id'],
        'source' => $this->template->basePath . "/" . $v['main_file'],
      ];
    }
    return $out;
  }
}

interface IFotocollageControl
{
  function create(string $language, Nette\Database\Table\ActiveRow $hlavne_menu): FotocollageControl;
}
