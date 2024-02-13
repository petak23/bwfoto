<?php

namespace DbTable;

use Nette\Database;

/**
 * Model, ktory sa stara o tabulku property
 * 
 * Posledna zmena 11.01.2024
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.0
 */
class Property extends Table
{
	/** @var string */
	protected $tableName = 'property';

	/** Vráti všetky dostupné vlastnosti */
	public function getAllProps(): array
	{
		$tmp = $this->findAll()->order('id_property_categories');
		$tipc = 0;
		$out = [];
		foreach ($tmp as $v) {
			$tipc = $tipc !== $v->id_property_categories ? $v->id_property_categories : $tipc;
			$out[$tipc][] = array_merge(
				$v->toArray(),
				[
					'category' 	=> $v->property_categories->name,
					'count'			=> count($v->related('products_property')),
				]
			);
		}

		return $out;
	}
}
