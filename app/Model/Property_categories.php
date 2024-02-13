<?php

namespace DbTable;

use Nette\Database;

/**
 * Model, ktory sa stara o tabulku property_categories
 * 
 * Posledna zmena 07.02.2024
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.0
 */
class Property_categories extends Table
{
	/** @var string */
	protected $tableName = 'property_categories';

	/*public function getAllProps(): array
	{
		$tmp = $this->findAll()->order('id_property_categories');
		$tipc = 0;
		$out = [];
		foreach ($tmp as $v) {
			$tipc = $tipc !== $v->id_property_categories ? $v->id_property_categories : $tipc;
			$out[$tipc][] = array_merge($v->toArray(), [/*'property_categori_name'* /'category' => $v->property_categories->name]);
		}

		return $out;
	}*/
}
