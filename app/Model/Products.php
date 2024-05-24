<?php

namespace DbTable;

use Nette;
use Nette\Utils;

/**
 * Model, ktory sa stara o tabulku products
 * 
 * Posledna zmena 24.05.2024
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.1.5
 */
class Products extends Table
{
	/** @var string */
	protected $tableName = 'products';

	/** @var string relativny adresar k produktom */
	private $dir_to_products;

	/** @var Products_property */
	public $products_property;

	public function __construct(
		string $dir_to_products,
		Products_property $products_property,
		Nette\Database\Explorer $db,
	) {
		parent::__construct($db);
		$this->dir_to_products = $dir_to_products;
		$this->products_property = $products_property;
	}

	/** 
	 * Vracia vsetky produkty polozky
	 * @param int $id id_hlavne_menu prislusnej polozky */
	public function getProducts(int $id): Nette\Database\Table\Selection
	{
		return $this->findBy(["id_hlavne_menu" => $id]);
	}

	/** 
	 * Uloženie jedneho produktu */
	public function saveProduct(array $data, int $id = 0): ?Nette\Database\Table\ActiveRow
	{
		return $this->uloz($data, $id);
	}

	/**
	 * Ulozenie nahravaneho suboru cez vue uploader.
	 * Vracia Id ulozenej polozky */
	public function saveUpload(Nette\Http\FileUpload $file, array $params = []): int
	{
		$fileName = $file->getSanitizedName();
		$pi = pathinfo($fileName);
		$fname = $pi['filename'];
		$ext = $pi['extension'];
		$additionalToken = 0;
		if (file_exists($this->dir_to_products . $fileName)) {
			do {
				$additionalToken++;
			} while (file_exists($this->dir_to_products . $fname . $additionalToken . "." . $ext));
		}
		$finalFileName = ($additionalToken == 0) ? $fname : $fname . $additionalToken;
		$image_name = $this->dir_to_products . $finalFileName . "." . $ext;
		$thumb_name = $this->dir_to_products . 'tb_' . $finalFileName . "." . $ext;

		$file->move($image_name);
		$image = Utils\Image::fromFile($image_name);
		$products_settings = $params['products_settings'];
		$image->resize(
			$products_settings['product_main_x'],
			$products_settings['product_main_y'],
			Utils\Image::SHRINK_ONLY
		);
		$image->save($image_name, $products_settings['product_main_quality']);

		copy($image_name, $thumb_name);
		$thumb = Utils\Image::fromFile($thumb_name);
		$thumb->resize(
			$params['panorama'] ? null : $products_settings['product_thumb_x'],
			$products_settings['product_thumb_y'],
			Utils\Image::SHRINK_ONLY
		); //| Image::EXACT
		$thumb->save($thumb_name, $products_settings['product_thumb_quality']);

		$saved = $this->getTable()->insert(array_merge($params['main_data'], [
			'name'      => $finalFileName,
			'web_name'  => Utils\Strings::webalize($finalFileName),
			'main_file' => $image_name,
			'thumb_file' => $thumb_name,
		]));

		return $saved !== FALSE ? $saved->id : 0;
	}

	/**
	 * Zpracovanie požiadavky na zmazanie.
	 * Ak zmaže alebo neexistuje(nie je co mazat) tak vráti true inak false.
	 * @param $id Id mazaného produktu. */
	public function removeFile(int $id): bool
	{
		$pr = $this->find($id);
		if ($pr !== null) {
			if (($this->deleteFile($pr->main_file) ? $this->deleteFile($pr->thumb_file) : false)) {
				$pr->delete();
				return true;
			}
		}
		return false;
	}

	/**
	 * Funkcia pre fotogalériu
	 * @param int id Id_hlavne_menu 
	 * @param int filter Filter výberu produktov: 1 - všetky, 2 - len na sklade */
	public function getForFotogalery(int $id, int $filter = 1): array
	{
		$out = [];
		$p = $this->findBy(['id_hlavne_menu' => $id]);
		if ($filter == 2) {
			$p->where("ks > ?", 0)->where("id_products_status < ?", 3);
		}
		foreach ($p as $v) {
			$out[] = $this->_productToArray($v);
		}
		return $out;
	}

	/** Vráti konkrétny produkt s daným id ako pole */
	public function getProduct(int $id): ?array
	{
		$p = $this->find($id);
		return $p != null ? $this->_productToArray($p) : null;
	}

	private function _productToArray(Nette\Database\Table\ActiveRow $v): array
	{
		$_pp = $this->products_property->getProperties($v->id, $v->price);
		return [
			'id' => $v->id,
			'type' => 'product',
			'name' => $v->name,
			'web_name' => $v->web_name,
			'description' => $v->description,
			'main_file' => ($v->main_file && is_file($v->main_file)) ? $v->main_file : 'images/otaznik.png',
			'thumb_file' => $v->thumb_file,
			'price' => $v->price,
			'properties' => $_pp,
			'ks' => $v->ks,
			'id_products_status' => $v->id_products_status,
			'products_status' => $v->products_status->name,
		];
	}

	/** Vráti všetky produkty ako pole */
	public function getProductsArray(int $id_hlavne_menu): array
	{
		$t = $this->findBy(['id_hlavne_menu' => $id_hlavne_menu]);
		$o = [];
		foreach ($t as $p) {
			$o[] = $p->toArray();
		}
		return $o;
	}
}
