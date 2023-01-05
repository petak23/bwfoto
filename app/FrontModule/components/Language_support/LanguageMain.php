<?php

namespace Language_support;

use DbTable;
use Nette;

/**
 * Hlavna trieda pre podporu jazykov lang_supp_main pre presentre vo FrontModule.
 * 
 * Posledna zmena(last change): 04.01.2023
 * 
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.2.2
 *
 * @property-read string $jazyk Skratka aktualneho jazyka
 * @property-read int $language_id Id aktualneho jazyka
 */
class LanguageMain implements Nette\Localization\Translator
{
  use Nette\SmartObject;

  /** @var string Skratka jazyka */
  private $jazyk = 'sk';
  /** @var int Id jazyka */
  private $language_id = 1;
  /** @var DbTable\Lang */
  public $lang;
  /** @var array Samotne texty podla jazykov */
  //protected $texty;
  /** @var array Konkretny jazyk pre vystup */
  private $out_texty = [];

  /** @param DbTable\Lang $lang */
  public function __construct(DbTable\Lang $lang)
  {
    $this->lang = $lang;
  }

  /**
   * Pripojenie textov z neon súboru
   * @param string $file subor aj s cestou */
  public function appendLanguageFile(string $file): LanguageMain
  {
    $this->out_texty = array_merge($this->out_texty, Nette\Neon\Neon::decode(file_get_contents($file)));
    return $this;
  }

  /**
   * Nahradí existujúce texty novmi z neon súboru
   * @param string $file subor aj s cestou */
  public function setNewLanguageFile(string $file): LanguageMain
  {
    $this->out_texty = Nette\Neon\Neon::decode(file_get_contents($file));
    return $this;
  }

  /** 
   * Nastavenie aktualneho jazyka
   * @param string|int $language Skratka jazyka alebo jeho id 
   * @throws LanguageNotExist*/
  public function setLanguage(string|int $language): LanguageMain
  {
    $lang = $this->lang->findOneBy(is_numeric($language) ? ['id' => $language] : ['skratka' => $language]);
    if (!$lang) {
      throw new LanguageNotExist("This language is not set! Požadovaný jazyk sa nenašiel!", 0);
    }

    // Nacitanie skratky jazyka
    $this->jazyk = $lang->skratka;
    // Nacitanie ID jazyka
    $this->language_id = $lang->id;

    // Nacitanie základných textov z neon suboru podla jazyka
    $this->out_texty = Nette\Neon\Neon::decode(file_get_contents(__DIR__ . '/lang_' . $this->jazyk . '.neon'));
    return $this;
  }


  public function getJazyk(): string
  {
    return $this->jazyk;
  }

  /** 
   * Vratenie textu pre dany kluc a jazyk
   * @param string $key Kluc daneho textu
   * @deprecated since version 1.3.0 use translate */
  public function trText(string $key): string
  {
    return isset($this->out_texty[$key]) ? $this->out_texty[$key] : $key;
  }

  /**
   * Preklad kluca
   * @param string $message */
  public function translate($message, ...$parameters): string
  {
    return array_key_exists($message, $this->out_texty) ?  $this->out_texty[$message] : $message;
  }

  /** 
   * Vrati id jazyka */
  public function getLanguage_id(): int
  {
    return $this->language_id;
  }

  /**
   * Vrati pole textov ak je pod klucm definovane
   * @param string $key Nazov kluca */
  public function getKeyArray(string $key): array|bool
  {
    return is_array($this->out_texty[$key]) ? $this->out_texty[$key] : FALSE;
  }

  public function getOutTexty()
  {
    return $this->out_texty;
  }
}

class LanguageNotExist extends \Exception
{
}
