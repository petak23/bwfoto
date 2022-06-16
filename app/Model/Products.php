<?php

namespace DbTable;

use Nette;
use Nette\Utils;

/**
 * Model, ktory sa stara o tabulku products
 * 
 * Posledna zmena 06.05.2022
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2022 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.1.0
 */
class Products extends Table
{
  /** @var string */
  protected $tableName = 'products';

  /** @var string relativny adresar k produktom */
  private $dir_to_products;

  public function __construct(
    string $dir_to_products,
    Nette\Database\Explorer $db
  ) {
    parent::__construct($db);
    $this->dir_to_products = $dir_to_products;
  }

  /** 
   * Vracia vsetky produkty polozky
   * @param int $id id_hlavne_menu prislusnej polozky
   * @return Nette\Database\Table\Selection|FALSE */
  public function getProducts(int $id)
  {
    return $this->findBy(["id_hlavne_menu" => $id]);
  }

  /** 
   * Uloženie jedneho produktu
   * @param array $data
   * @param mixed $id primary key
   * @return Nette\Database\Table\ActiveRow|FALSE */
  public function saveProduct($data, $id = 0)
  {
    return $this->uloz($data, $id);
  }

  /**
   * Ulozenie nahravaneho suboru cez vue uploader.
   * @param Nette\Http\FileUpload $file
   * @param array $params Pole vlastnych hodnot.
   * @return int Id ulozenej polozky ulozena v DB. */
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
   * @param $id Id mazaného produktu. 
   * @return bool Ak zmaže alebo neexistuje(nie je co mazat) tak true inak false */
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
   * @return array */
  public function getForFotogalery(int $id): array
  {
    $out = [];
    foreach ($this->findBy(['id_hlavne_menu' => $id]) as $v) {
      $out[] = [
        'id' => $v->id,
        'type' => 'product',
        'name' => $v->name,
        'web_name' => $v->web_name,
        'description' => $v->description,
        'main_file' => ($v->main_file && is_file($v->main_file)) ? $v->main_file : 'images/otaznik.png',
        'thumb_file' => $v->thumb_file
      ];
    }
    return $out;
  }

  /** Vráti konkrétny produkt s daným id ako pole */
  public function getProduct(int $id): array
  {
    return $this->find($id)->toArray();
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
