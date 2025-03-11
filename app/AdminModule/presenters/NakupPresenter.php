<?php

namespace App\AdminModule\Presenters;

/**
 * Prezenter pre správu nákupov.
 * 
 * Posledna zmena(last change): 11.03.2025
 *
 * Modul: ADMIN
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2024 - 2025 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.0.1
 */

class NakupPresenter extends BasePresenter
{
	/*protected function startup()
	{
		parent::startup();
		$this->addAdminMenu([
			'link'  => "Products:ViewProps",
			'name'  => "Editácia vlastností",
			'avatar' => null
		]);
	}*/

	/** Vychodzia akcia */
	public function renderDefault()
	{
		$this->template->spravca = $this->udaje->getValByName("nakup_spravca");
	}
}
