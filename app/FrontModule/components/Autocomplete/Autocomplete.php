<?php

namespace App\FrontModule\Components\Autocomplete;

use Language_support;
use Nette;
use Nette\Utils\Json;

/**
 * Komponenta pre našepkávanie pri vyhladavani pre FRONT modul
 * 
 * Posledna zmena(last change): 26.04.2021
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2012 - 2021 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.2
 */

class AutocompleteControl extends Nette\Application\UI\Control {
  
  /** @var Language_support\LanguageMain */
  private $texts;

  /**
   * @param Language_support\LanguageMain $texts */
	public function __construct(Language_support\LanguageMain $texts) {
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
    $this->template->setFile(__DIR__ . '/Autocomplete.latte');
    $this->template->setTranslator($this->texts);
    $this->template->links = Json::encode([
      1 => $this->presenter->link('Clanky:'),
    ]);
		$this->template->render();
	}
}

interface IAutocompleteControl {
  /** @return AutocompleteControl */
  function create();
}
