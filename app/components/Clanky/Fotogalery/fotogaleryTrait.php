<?php

namespace PeterVojtech\Clanky\Fotogalery;

/**
 * Traita pre zobrazenie fotogalérie k článku
 * 
 * Posledná zmena(last change): 04.01.2023
 * 
 * 
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2021 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.0.2
 */
trait fotogaleryTrait
{
	/** @var IFotogaleryControl @inject */
	public $fotogaleryControlFactory;

	/** 
	 * Vytvorenie komponenty */
	public function createComponentFotogalery(): FotogaleryControl
	{
		$out = $this->fotogaleryControlFactory->create((explode(":", $this->name)[0] == "Front") ? $this->language : 'sk',
			$this->zobraz_clanok->hlavne_menu
		);
		if (isset($this->params['first_id']) && $this->params['first_id'] > 0) {
			$out->set_first_id($this->params['first_id']);
		}
		return $out->fromConfig($this->nastavenie['komponenty']['fotogalery']); //Vrati komponentu aj s nastaveniami z komponenty.neon
	}
}
