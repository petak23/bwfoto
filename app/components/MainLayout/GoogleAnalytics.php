<?php
namespace PeterVojtech\MainLayout;

use DbTable;
use Nette\Application\UI;

/**
 * Komponenta pre vlozenie kodu pre google analytics do stranky
 * Posledna zmena(last change): 07.09.2017
 * 
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com> 
 * @copyright  Copyright (c) 2012 - 2017 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.0.0
 */

class GoogleAnalyticsControl extends UI\Control {
  /** @var DbTable\Udaje */
  private $udaje;
  
  /** @param DbTable\Udaje $udaje */
  public function __construct(DbTable\Udaje $udaje) {
    parent::__construct();
    $this->udaje = $udaje;
  }
  
  public function render() {
    $this->template->setFile(__DIR__ . '/GoogleAnalytics.latte');
    $tmp = $this->udaje->getKluc("google-analytics")->text;
    $this->template->id_google_analytics = strpos($tmp, "UA-") === 0 ? $tmp : FALSE;
    $this->template->render();
  }
}