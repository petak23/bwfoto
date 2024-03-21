<?php

namespace DbTable;

use Nette\Database;
use Nette\Utils\Json;

/**
 * Model, ktory sa stara o tabulku nakup
 * 
 * Posledna zmena 20.03.2024
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.1
 */
class Nakup extends Table
{
	/** @var string */
	protected $tableName = 'nakup';

	public function add(int $id_user_main, array $data): Database\Table\ActiveRow
	{
		$firstOfThisMonth = strtotime(date('Y-m') . '-01 00:00:00');
		$c = count($this->findAll()->where('created >= ?', date("Y-m-d H:i:s", $firstOfThisMonth)));

		return $this->pridaj(
			[
				'id_user_main' => $id_user_main,
				'shipping' => Json::encode($data['shipping']),
				'products' => Json::encode($data['product']),
				'created' => date("Y-m-d H:i:s", time()),
				'price' => (float)$data['final_price'],
				'code'	=> date("Ym", time()) . ($c + 1),
			]
		);
	}
}
