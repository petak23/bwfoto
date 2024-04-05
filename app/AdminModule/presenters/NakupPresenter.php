<?php

namespace App\AdminModule\Presenters;

use DbTable;
use Nette\Utils\Json;

/**
 * Prezenter pre správu nákupov.
 * 
 * Posledna zmena(last change): 26.03.2024
 *
 * Modul: ADMIN
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2024 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.0.0
 */

class NakupPresenter extends BasePresenter
{

	// -- DB
	/** @var DbTable\Nakup @inject */
	public $nakup;

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
		$this->template->nakup = Json::encode($this->nakup->getLast());
	}
}
