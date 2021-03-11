<?php
namespace PeterVojtech\Clanky\Fotogalery;

use DbTable;
use Language_support;
use Nette;
use Nette\Utils\Json;

/**
 * Komponenta pre zobrazenie fotogalérie k článku 
 * Posledna zmena(last change): 11.03.2021
 * 
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com> 
 * @copyright Copyright (c) 2021 - 2021 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.2
 */
class FotogaleryControl extends Nette\Application\UI\Control {
  
  /** @var Language_support\LanguageMain */
	public $texts;
  
  /** @var DbTable\Hlavne_menu_lang */
	public $hlavne_menu_lang;
  /** @var DbTable\Lang */
  public $lang;
  /** @var DbTable\dokumenty */
  public $dokumenty;
  /** @var DbTable\products */
	public $products;
  
  /** @var array */
  private $paramsFromConfig;
  /** @var array Pole podčlánkov, príloh a produktov článku v JSON formáte*/
  private $attachments;
  /** @var int Počet podčlánkov, príloh a produktov článku*/
  private $attachments_count;
  /** @var string Skratka jazyka */
  private $language = 'sk';
  /** @var Nette\Database\Table\ActiveRow */
  private $hlavne_menu;
  
  /**
   * @param string $language 
   * @param DbTable\Hlavne_menu_lang $hlavne_menu_lang
   * @param DbTable\Lang $lang
   * @param Language_support\LanguageMain $texts */   
  public function __construct(string $language, 
                              Nette\Database\Table\ActiveRow $hlavne_menu,
                              DbTable\Hlavne_menu_lang $hlavne_menu_lang,
                              DbTable\Lang $lang,
                              DbTable\Dokumenty $dokumenty,
                              DbTable\Products $products,
                              Language_support\LanguageMain $texts
                              ) {
    $this->hlavne_menu_lang = $hlavne_menu_lang;
    $this->lang = $lang;
    $this->language = $language;
    $this->texts = $texts;
    $this->texts->setLanguage($language);
    $this->hlavne_menu = $hlavne_menu;
    $this->dokumenty = $dokumenty;
    $this->products = $products;
  }
  
  /**
   * Parametre z komponenty.neon
   * @param array $params
   * @return FotogaleryControl */
  public function fromConfig(array $params) {
    $this->paramsFromConfig = $params;
    return $this;
  }

  /** 
   * Render funkcia pre vykreslenie fotogalérie
   * @param array $p Parametre: id_hlavne_menu - id odkazovaneho clanku, template - pouzita sablona
   * @see Nette\Application\Control#render() */
  public function render($p = []) {
    $this->getAttachments();
    $this->template->setFile(__DIR__ . "/Fotogalery".(isset($p["template"]) && strlen($p["template"]) ? "_".$p["template"] : "_default").".latte");
    $this->template->attachments = $this->attachments;
    $this->template->attachments_count = $this->attachments_count;
    $this->template->hlavne_menu = $this->hlavne_menu;
    $this->template->setTranslator($this->texts);
    $this->template->addFilter('border_x_vue', function ($text){
      $pom = $text != null && strlen($text)>2 ? explode("|", $text) : ['','0'];
      $xs = 'border: '.$pom[1].'px solid '.(strlen($pom[0])>2 ? ($pom[0]):'inherit');
      return $xs;
    });

    $this->template->render();
  }

  /**
   * Načítanie príloh
   * @return FotogaleryControl */
  public function getAttachments() {
    // Výber podčlánkov
    $attachments = $this->_getForFotogalery($this->hlavne_menu->id);

    // Prílohy
    $attachments = array_merge($attachments, $this->dokumenty->getForFotogalery($this->hlavne_menu->id));
    
    // Produkty
    $attachments = array_merge($attachments, $this->products->getForFotogalery($this->hlavne_menu->id));
    $this->attachments_count = count($attachments);
    $this->attachments = Json::encode($attachments);
    return $this;
  }

  /**
   * Funkcia pre fotogalériu
   * @param int id Id_hlavne_menu
   * @return array */
  private function _getForFotogalery(int $id): array {
    $out = [];
    foreach ($this->hlavne_menu_lang->findBy(["hlavne_menu.id_nadradenej"=> $id, "lang.skratka" => $this->language]) as $v) {
      $av = 'files/menu/'.$v->hlavne_menu->avatar;
      $out[] = [
        'id' => $v->id,
        'type'=>'menu',
        'name' => $v->view_name,
        'web_name' => $this->presenter->link('Clanky:', $v->id_hlavne_menu),
        'description' => $v->clanok_lang->anotacia,
        'main_file' => ($av && is_file($av)) ? $av : 'ikonky/128/list_ceruza128.png',
        'thumb_file' => ($av && is_file($av)) ? $av : 'ikonky/128/list_ceruza128.png',
      ];
    }
    return $out;
  }
}

interface IFotogaleryControl {
  /** @return FotogaleryControl */
  function create(string $language, Nette\Database\Table\ActiveRow $hlavne_menu);
}