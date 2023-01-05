<?php

namespace App\FrontModule\Components\Clanky;

use DbTable;
use Language_support;
use Nette;

/**
 * Komponenta pre zobrazenie aktualnych projektov pre FRONT modul
 * 
 * Posledna zmena(last change): 04.01.2023
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.3
 */
class AktualnyProjektControl extends Nette\Application\UI\Control
{

  /** @var Language_support\LanguageMain */
  private $texts;
  /** @var DbTable\Hlavne_menu_lang */
  private $hlavne_menu_lang;
  /** @var string $avatar_path  Cesta k titulnemu obrazku clanku */
  private $avatar_path = "";

  public function __construct(DbTable\Hlavne_menu_lang $hlavne_menu_lang, Language_support\LanguageMain $texts)
  {
    parent::__construct();
    $this->hlavne_menu_lang = $hlavne_menu_lang;
    $this->texts = $texts;
  }

  /** 
   * Nastavenie jazyka */
  public function setLanguage(int|string $language): AktualnyProjektControl
  {
    $this->texts->setLanguage($language);
    return $this;
  }
  /** 
   * Nastavenie cesty k obrazku
   * @param string $avatar_path Cesta k titulnemu obrazku clanku */
  public function setAvatarPath($avatar_path): AktualnyProjektControl
  {
    $this->avatar_path = $avatar_path;
    return $this;
  }

  /** Render funkcia pre vypisanie odkazu na clanok 
   * @see Nette\Application\Control#render()
   */
  public function render()
  {
    $this->template->setFile(__DIR__ . '/AktualnyProjekt.latte');
    $this->template->clanok = $this->hlavne_menu_lang->findBy(['hlavne_menu.aktualny_projekt' => 1, 'id_lang' => $this->texts->language_id]);
    $this->template->texts = $this->texts;
    $this->template->avatar_path = $this->avatar_path;
    $this->template->render();
  }
}
interface IAktualnyProjektControl
{
  function create(): AktualnyProjektControl;
}
