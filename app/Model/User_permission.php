<?php

namespace DbTable;

use Nette;

/**
 * Model starajuci sa o tabulku user_permission
 * 
 * Posledna zmena 18.12.2023
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.0
 */
class User_permission extends Table
{
	/** @var string */
	protected $tableName = 'user_permission';

	/** 
	 * Hlada urovne registracie uzivatela v rozsahu od do */
	public function getAllowedPermission(int $id_user_roles = 0, bool $return_as_array = false): Nette\Database\Table\Selection|array
	{
		//dump($id_user_roles);
		$out = $this->findBy(['id_user_roles <= ' . $id_user_roles]);
		if ($return_as_array) {
			$_tmp = [];
			foreach ($out as $p) {
				//dump($p);
				$ov = 0;
				foreach ($_tmp as $k => $v) {
					if ($v['resource'] == $p->user_resource->name && $v['id_user_roles'] < $p->id_user_roles)
						$ov = $k;
				}
				if ($ov) {
					$_tmp[$ov] = [
						'resource' => $p->user_resource->name,
						'action' => $p->actions != null ? explode(",", $p->actions) : null,
						'id_user_roles' => $p->id_user_roles,
					];
				} else {
					$_tmp[] = [
						'resource' => $p->user_resource->name,
						'action' => $p->actions != null ? explode(",", $p->actions) : null,
						'id_user_roles' => $p->id_user_roles,
					];
				}
			}
			$out = $_tmp;
		}
		//dumpe($out);
		return $out;
	}

	/** 
	 * Dava vsetky urovne registracie do poľa role=>id */
	public function vsetky_urovne_array(): array
	{
		return $this->findAll()->fetchPairs('role', 'id');
	}

	/** 
	 * Hodnoty id=>name pre formulare
	 * @param int $id_reg Uroven registracie*/
	public function urovneReg(int $id_reg): array
	{
		return $this->hladaj_urovne(0, $id_reg)->fetchPairs('id', 'name');
	}
}
