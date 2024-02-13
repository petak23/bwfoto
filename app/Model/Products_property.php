<?php

namespace DbTable;

use Nette\Database;

/**
 * Model, ktory sa stara o tabulku products_property
 * 
 * Posledna zmena 13.02.2024
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.1
 */
class Products_property extends Table
{
	/** @var string */
	protected $tableName = 'products_property';

	public function getProperties(int $id_products, float $price = 0): array
	{
		$out = $this->findBy(['id_products' => $id_products]);
		$final_price = $price;

		$_tmp = [];
		foreach ($out as $v) {
			$_tmp[] = [
				'id' => $v->id,
				'name' => $v->property->name,
				'category' => $v->property->property_categories->name,
				'price_increase_percentage' => $v->property->price_increase_percentage,
				'price_increase_price' => $v->property->price_increase_price,
			];
			if ($v->property->price_increase_percentage !== null) {
				$final_price += $price * ($v->property->price_increase_percentage / 100);
			}
			if ($v->property->price_increase_price !== null) {
				$final_price += $v->property->price_increase_price;
			}
		}
		$out = [
			'props' => $_tmp,
			'final_price' => $final_price,
		];
		return $out;
	}

	public function saveProductProps(array $values): array
	{

		$pr = $this->findBy(['id_products' => $values['id_products']]);
		foreach ($pr as $v) {
			if (!in_array($v->id, $values['id_products_property']))
				$this->find($v->id)->delete();
		}
		if (count($values['id_new_property'])) {
			$new = [];
			foreach ($values['id_new_property'] as $v) {
				$new[] = [
					'id_products' => $values['id_products'],
					'id_property' => $v
				];
			}
			$this->pridaj($new);
		}

		return $this->getProperties($values['id_products'], $values['price']);
	}
}
