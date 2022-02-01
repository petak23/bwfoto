<?php
namespace PeterVojtech\Clanky\Fotocollage;

/**
 * Traita pre zobrazenie foto koláže k článku
 * 
 * Posledná zmena(last change): 01.02.2022
 * 
 * 
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2021 - 2022 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.0.0
 */
trait fotocollageTrait {
  /** @var IFotocollageControl @inject */
  public $fotocollageControlFactory;

  /** 
   * Vytvorenie komponenty 
   * @return FotocollageControl */
	public function createComponentFotocollage() {
      $out = $this->fotocollageControlFactory->create((explode(":", $this->name)[0] == "Front") ? $this->language : 'sk',
                                                      $this->zobraz_clanok->hlavne_menu);
      if (isset($this->params['first_id']) && $this->params['first_id'] > 0) {
				$out->set_first_id($this->params['first_id']);
      }
      return $out->fromConfig($this->nastavenie['komponenty']['fotogalery']); //Vrati komponentu aj s nastaveniami z komponenty.neon
	}
}