<?php
namespace PeterVojtech\Base;
use Nette;
use Nette\Utils\Html;

/**
 * Komponenta pre vlozenie css a js suborov do stranky
 * Posledna zmena(last change): 11.05.2020
 * 
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com> 
 * @copyright  Copyright (c) 2012 - 2020 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.0.1
 */

class CssJsFilesControl extends Nette\Application\UI\Control {
  
  /** @var array */
  private $files;
  /** @var string Nazov modulu*/
  private $module;
  /** @var string Nazov:Presenter_akcia */
  private $pa;

  /**
   * @param array $files zoznam vsetkych suborov
   * @param string $name Meno modulu a presentera
   * @param string $action Aktualna akcia */
  public function __construct(array $files, string $name, string $action) {
    $this->files = $files;
    $modul_presenter = explode(":", $name);
    $this->module = $modul_presenter[0];         //Modul
    $this->pa = $modul_presenter[1]."_".$action; //Presenter_akcia
  }

  /** 
   *Render funkcia pre vypisanie odkazu na clanok
   * @param string $t Nazov template 
   * @see Nette\Application\Control#render() */
  public function render(string $t): void {
    $this->template->setFile(__DIR__ . '/'.$t.'.latte');
    $this->template->render();
  }
  
  /**
   * Render pre css subory */
  public function renderCss(): void {
    $css = isset($this->files[$this->module][$this->pa]['css']) ? 
                array_merge($this->files[$this->module]['css'], $this->files[$this->module][$this->pa]['css']) :
                $this->files[$this->module]['css'];
    $this->template->files = $css;
    $this->render('Css');
  }
  
  /**
   * Render pre js subory */
  public function renderJs(): void {
    $js = isset($this->files[$this->module][$this->pa]['js']) ? 
              array_merge($this->files[$this->module]['js'], $this->files[$this->module][$this->pa]['js']) : 
              $this->files[$this->module]['js'];
    $out = [];              
    foreach ($js as $j) {
      $path = is_array($j) ? $j['path'] : $j;
      $out[] = Html::el('script', ['type' => is_array($j) ? $j['typejs'] : 'text/javascript'])
                      ->src((strpos($path, 'http') === FALSE ? $this->template->basePath . "/" : '') . $path);
    }
    $this->template->files = $out; 
    $this->render('Js');
  }
}