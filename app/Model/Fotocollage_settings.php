<?php

declare(strict_types=1);

namespace DbTable;

use Nette\Database\Table\ActiveRow;
use Nette\Utils\Json;

/**
 * Model, ktory sa stara o tabulku fotocollage_settings
 * 
 * Posledna zmena 02.03.2023
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.0
 */
class Fotocollage_settings extends Table
{

	const TMP_SCHEMA = [
		[
			// Max. šírka koláže pre ktorú platí napr. teraz od 0 do 320
			'max_width' 		=> 320,
			// Počet fotiek v jednotlivých riadkoch
			'schema' 				=> [2, 1, 3, 4, 4, 3, 4, 4],
			// Výška jednotlivých riadkov v px
			'height' 				=> [85, 60, 85, 60, 70, 95, 70, 60],
			// Veľkosť medzery pod daným riadkom
			'padding'				=> [2, 5, 0, 0, 0, 5, 0, 0],
			// Poradie fotky v riadku, ktorá má byť širšia ako ostatné v riadku:
			// Ak je zadané číslo väčšie ako 0 (1,2,...) tak tá konkrétna bude širšia, 
			// ak je zadané 0 generuje sa náhodne,
			// ak je zadané -1 všetky fotky v riadku budú rovnaké.
			'widerPhotoId'	=> [-1, 2, 0, 1, -1, 0, 2, 1],
		],
		[
			'max_width'			=> 700,
			'schema'				=> [4, 3, 5, 4, 3, 4, 5, 4],
			'height'				=> [130, 175, 105, 120, 175, 130, 105, 120],
			'padding'				=> [2, 5, 0, 0, 0, 5, 0, 0],
			'widerPhotoId'	=> [-1, 0, 2, 0, -1, 2, 3, 1],
		],
		[
			'max_width'			=> 1300,
			'schema'				=> [6, 7, 8, 7, 6, 8, 7, 6, 5, 4],
			'height'				=> [225, 170, 135, 170, 225, 135, 170, 225, 200, 200],
			'padding'				=> [2, 5, 0, 0, 0, 5, 0, 0, 0, 10],
			'widerPhotoId'	=> [2, -1, 0, 2, -1, 1, 2, 1, 0, -1],
		],
		[
			'max_width'			=> 10000,
			'schema'				=> [6, 7, 8, 7, 6, 8, 7, 6],
			'height'				=> [318, 240, 190, 240, 318, 190, 240, 318],
			'padding'				=> [2, 5, 0, 0, 0, 5, 0, 0],
			'widerPhotoId'	=> [3, 0, -1, 2, 2, -1, 3, 4],
		],
	];

	/** @var string */
	protected $tableName = 'fotocollage_settings';

	public function getSettings(int $id_hlavne_menu): ?ActiveRow
	{
		$tmp = $this->findOneBy(['id_hlavne_menu' => $id_hlavne_menu]);

		if ($tmp == null) {
			$tmp = $this->pridaj([
				'id_hlavne_menu' => $id_hlavne_menu,
				'settings' => Json::encode(self::TMP_SCHEMA)
			]);
		}

		return $tmp;
	}

	public function save(int $id_hlavne_menu, $data)
	{
		$tmp = $this->findOneBy(['id_hlavne_menu' => $id_hlavne_menu]);

		$dat = [
			'id_hlavne_menu' => $id_hlavne_menu,
			'settings' => Json::encode($data)
		];

		return ($tmp != null) ? $this->repair($tmp->id, $dat) : $this->pridaj($dat);
	}
}
