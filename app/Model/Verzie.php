<?php
declare(strict_types=1);

namespace DbTable;

use Nette\Database;
use Nette\Utils\ArrayHash;
/**
 * Model, ktory sa stara o tabulku verzie
 * 
 * Posledna zmena 06.04.2022
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2022 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.2
 */
class Verzie extends Table {
	/** @var string */
	protected $tableName = 'verzie';

	/** Vrati vsetky verzie v poradi od najnovsej */
	public function vsetky(bool $return_as_array = false): Database\Table\Selection|array {
		$t = $this->getTable()->order('modified DESC');
		if ($return_as_array) {
			$o = [];
			foreach ($t as $v) {
				$o[] = [
					'cislo'=> $v->cislo,
					'id'  => $v->id,
					'user'=> $v->user_main->name,
					'modified'=> $v->modified->format('d.m.Y'),
					'subory'=> $v->subory,
					'text'  => $v->text,
				];
				
			}
			return $o;
		}
		return $t;
	}

	/** Vrati najnovsiu verziu */
	public function posledna(bool $return_as_array = false): Database\Table\ActiveRow|array|null {
		$out = $this->vsetky()->limit(1)->fetch();
		if ($return_as_array) {
			$out = [
				'id'=> $out->id,
				'cislo'=> $out->cislo,
				'user'=> $out->user_main->name,
				'modified'=> $out->modified->format('d.m.Y'),
				'subory'=> $out->subory,
				'text'  => $out->text,
			];
		}
		return $out;
	}
	
	/** Ulozi verziu
	 * @throws Database\DriverException */
	public function ulozVerziu(ArrayHash $values, bool $return_as_array = false): Database\Table\ActiveRow|array|null  {
		try {
			$id = $values->id ?? 0; // Náhrada za isset($x) ? $x : 0 
			unset($values->posli_news, $values->id);
			$out = $this->uloz($values, $id);
			if ($return_as_array) {
				$out = [
					'id'=> $out->id,
					'cislo'=> $out->cislo,
					'user'=> $out->user_main->name,
					'modified'=> $out->modified->format('d.m.Y'),
					'subory'=> $out->subory,
					'text'  => $out->text,
				];
			}
			return $out;
		} catch (Database\DriverException $e) {
			throw new Database\DriverException('Chyba ulozenia: '.$e->getMessage());
		}
	}
}
