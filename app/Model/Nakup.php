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

	/** @var Nette\Database\Table\Selection */
	protected $nakup_status;

	public function __construct(
		Database\Explorer $db
	) {
		parent::__construct($db);
		$this->nakup_status = $this->connection->table("nakup_status");
	}

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

	public function getLast(int $pocet = 10): array
	{
		$o = $this->findAll()->order("created DESC")->limit(10);
		$out = [];
		foreach ($o as $v) {
			$out[] = [
				'id'	=> $v->id,
				'user_name' => $v->user_main->name,
				'email'	=> $v->user_main->email,
				'user_profile'	=> [
					'phone'	=> $v->user_main->user_profile->phone,
					'street' => $v->user_main->user_profile->street,
					'town' => $v->user_main->user_profile->town,
					'psc' => $v->user_main->user_profile->psc,
					'country' => $v->user_main->user_profile->country,
					'adress2' => $v->user_main->user_profile->adress2 != null ? JSON::decode($v->user_main->user_profile->adress2) : null,
					'firm'	=> $v->user_main->user_profile->firm != null ? JSON::decode($v->user_main->user_profile->firm) : null,
				],
				'created'	=> $v->created->format("d.m.Y H:i:s"),
				'price'		=> $v->price,
				'code'		=> $v->code,
				'status'	=> $v->nakup_status->name,
				'shipping' => JSON::decode($v->shipping),
				'products' => JSON::decode($v->products),
				'status'	 => $v->nakup_status->name,
				'id_nakup_status' => $v->id_nakup_status,

			];
		}
		return $out;
	}

	public function getNakupStatus(): array
	{
		return $this->nakup_status->fetchPairs("id", "name");
	}

	public function changeNakupStatus(int $id_nakup, int $change_to): array
	{
		$out = $this->repair($id_nakup, ["id_nakup_status" => $change_to]);
		return $out->toArray();
	}
}
