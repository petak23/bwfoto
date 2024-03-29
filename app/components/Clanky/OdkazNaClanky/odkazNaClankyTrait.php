<?php

namespace PeterVojtech\Clanky\OdkazNaClanky;

use Nette\Application\UI\Multiplier;

/**
 * Traita pre zobrazenie odkazu na clanok
 * 
 * Posledna zmena(last change): 04.01.2023
 * 
 * 
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.0.4
 */
trait odkazNaClankyTrait
{
	/** @var IOdkazNaClankyControl @inject */
	public $odkazNaClankyControlFactory;

	/** 
	 * Vytvorenie komponenty */
	public function createComponentOdkazNaClanky(): Multiplier
	{
		$servise = $this;
		return new Multiplier(function ($id) use ($servise) {
			$out = $this->odkazNaClankyControlFactory->create((explode(":", $servise->name)[0] == "Front") ? $servise->language : 'sk');
			return $out->fromConfig($this->nastavenie['komponenty']['odkazNaClanky']); //Vrati komponentu aj s nastaveniami z komponenty.neon
		});
	}
}
