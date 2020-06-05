<?php

namespace App\FrontModule\Components\Autocomplete;

use DbTable;
use Language_support;
use Nette;

/**
 * Komponenta pre našepkávanie pri vyhladavani pre FRONT modul
 * 
 * Posledna zmena(last change): 24.05.2020
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2012 - 2020 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.0
 */

class AutocompleteControl extends Nette\Application\UI\Control {

	/** @var DbTable\Hlavne_menu_lang $hlavne_menu_lang */
//  private $hlavne_menu_lang;

	public $onSelect = [];
  
  /** @var Language_support\LanguageMain */
  private $texts;

  /**
   * @param DbTable\Hlavne_menu_lang $hlavne_menu_lang
   * @param Language_support\LanguageMain $texts */
	public function __construct(/*DbTable\Hlavne_menu_lang $hlavne_menu_lang, */Language_support\LanguageMain $texts) {
//		$this->hlavne_menu_lang = $hlavne_menu_lang;
    $this->texts = $texts;
	}
  
  /** 
   * Nastavenie jazyka 
   * @param string $language jazyk 
   * @return AutocompleteControl */
  public function setLanguage(string $language) {
    $this->texts->setLanguage($language);
    return $this;
  }

	public function render() {
//	$this->template->handleParameter = $this->getParameterId('id');
		$this->template->setFile(__DIR__ . '/Autocomplete.latte');
		$this->template->render();
	}

	/*public function handleSearch(string $searchStr = "") {
    $searchStr = '%'.$searchStr.'%';
    $search = $this->hlavne_menu_lang
                   ->findBy(['hlavne_menu.hlavne_menu_cast.mapa_stranky'=>1])
                   ->where('menu_name LIKE ? OR h1part2 LIKE ? OR hlavne_menu_lang.view_name LIKE ?', $searchStr, $searchStr, $searchStr)
                   ->fetchPairs('id', 'view_name');
    $out = [];
    foreach ($search as $k => $v) {
      $out[] = [
        'id'=>$k,
        'view_name'=>$v
      ];
    }
    $this->sendResponse(new Responses\JsonResponse($out));  
	} */
}

interface IAutocompleteControl {
  /** @return AutocompleteControl */
  function create();
}