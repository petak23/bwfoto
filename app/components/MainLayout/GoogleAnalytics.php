<?php
namespace PeterVojtech\MainLayout;

use DbTable;
use Nette\Application\UI\Control;
use Nette\Http\Request;

/**
 * Komponenta pre vlozenie kodu pre google analytics do stranky
 * Posledna zmena(last change): 18.07.2018
 * 
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com> 
 * @copyright  Copyright (c) 2012 - 2018 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.0.2
 */

class GoogleAnalyticsControl extends Control {
  /** @var \Nette\Database\Table\ActiveRow|FALSE */
  private $udaj;
  /** @var string */
  private $host;
  
  /** @param DbTable\Udaje $udaje */
  public function __construct(DbTable\Udaje $udaje, Request $request) {
    parent::__construct();
    $this->udaj = $udaje->getKluc("google-analytics");
    $this->host = $request->getUrl()->host;
  }
  
  public function render() {
    $this->template->setFile(__DIR__ . '/GoogleAnalytics.latte');
    $this->template->id_google_analytics = ($this->udaj != FALSE & $this->host != "localhost") ? (strpos($this->udaj->text, "UA-") === 0 ? $this->udaj->text : FALSE) : FALSE;
    $this->template->render();
  }
}

interface IGoogleAnalyticsControl {
  /** @return GoogleAnalyticsControl */
  function create();
}