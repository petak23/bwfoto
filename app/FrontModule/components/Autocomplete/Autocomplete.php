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
  private $hlavne_menu_lang;

	public $onSelect = [];
  
  /** @var Language_support\LanguageMain */
  private $texts;

  /**
   * @param DbTable\Hlavne_menu_lang $hlavne_menu_lang
   * @param Language_support\LanguageMain $texts */
	public function __construct(DbTable\Hlavne_menu_lang $hlavne_menu_lang, Language_support\LanguageMain $texts) {
		$this->hlavne_menu_lang = $hlavne_menu_lang;
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
		$this->template->handleParameter = $this->getParameterId('searchStr');
		$this->template->setFile(__DIR__ . '/Autocomplete.latte');
		$this->template->render();
	}

	public function handleSelect(string $searchStr) {
		$search = $this->hlavne_menu_lang->where('menu_name LIKE ? OR h1part2 LIKE ? OR view_name LIKE ?', $searchStr, $searchStr, $searchStr);
		$this->onSelect($search);
	}
}

interface IAutocompleteControl {
  /** @return AutocompleteControl */
  function create();
}