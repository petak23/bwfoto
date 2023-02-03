<?php

namespace DbTable;

/**
 * Model, ktory sa stara o tabulku hlavne_menu_template
 * 
 * Posledna zmena 03.02.2023
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.2
 */
class Hlavne_menu_template extends Table
{
	/** @var string */
	protected $tableName = 'hlavne_menu_template';

	/** Hodnoty id=>name-description pre formulare */
	public function formPairs(): array
	{
		return $this->findAll()->fetchPairs('id', 'description');
	}

	/** Hodnoty pre formular */
	public function formItems(): array
	{
		$out = [];
		foreach ($this->findAll() as $v) {
			$out[] = [
				'value' => $v->id,
				'text'  => $v->name . ' - ' . $v->description,
			];
		}
		return $out;
	}
}
