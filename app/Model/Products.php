<?php

namespace DbTable;

use Nette;
use Nette\Utils;
use Zet\FileUpload\Model;

/**
 * Model, ktory sa stara o tabulku products
 * 
 * Posledna zmena 08.03.2022
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2022 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.7
 */
class Products extends Table implements Model\IUploadModel {
  /** @var string */
  protected $tableName = 'products';

  /** @var string relativny adresar k produktom */
  private $dir_to_products;

  public function __construct(string $dir_to_products,
                              Nette\Database\Explorer $db)  {
    parent::__construct($db);
    $this->dir_to_products = $dir_to_products;
  }

  /** 
   * Vracia vsetky produkty polozky
   * @param int $id id_hlavne_menu prislusnej polozky
   * @return Nette\Database\Table\Selection|FALSE */
  public function getProducts(int $id) {
    return $this->findBy(["id_hlavne_menu" => $id]);
  }
  
  /** 
   * Uloženie jedneho produktu
   * @param array $data
   * @param mixed $id primary key
   * @return Nette\Database\Table\ActiveRow|FALSE */
  public function saveProduct($data, $id = 0) {
    return $this->uloz($data, $id);
  }
  
  // --- Funkcie pre upload ---
  /**
	 * Ulozenie nahravaneho suboru.
	 * @param Nette\Http\FileUpload $file
	 * @param array $params Pole vlastnych hodnot.
	 * @return int Id ulozenej polozky ulozena v DB. */
  public function save(Nette\Http\FileUpload $file, array $params = []): int {
    $tmp_dir = $this->dir_to_products."tmp/";
    $fileName = $file->getSanitizedName();
    $pi = pathinfo($fileName);
		$fname = $pi['filename'];
		$ext = $pi['extension'];
		$additionalToken = 0;
    if (file_exists($this->dir_to_products.$fileName)) {
			do { $additionalToken++;
			} while (file_exists($this->dir_to_products.$fname.$additionalToken.".".$ext));
    }
		$finalFileName = ($additionalToken == 0) ? $fname : $fname.$additionalToken;
    $image_name = $tmp_dir.$finalFileName.".". $ext;
    $thumb_name = $tmp_dir.'tb_'.$finalFileName.".". $ext;

    $file->move($image_name);
    $image = Utils\Image::fromFile($image_name);
    $products_settings = $params['products_settings'];
    $image->resize($products_settings['product_main_x'],
                   $products_settings['product_main_y'], 
                   Utils\Image::SHRINK_ONLY);
    $image->save($image_name, $products_settings['product_main_quality']);
    
    copy($image_name, $thumb_name);
    $thumb = Utils\Image::fromFile($thumb_name);
    $thumb->resize($params['panorama'] ? null : $products_settings['product_thumb_x'],
                   $products_settings['product_thumb_y'], 
                   Utils\Image::SHRINK_ONLY); //| Image::EXACT
    $thumb->save($thumb_name, $products_settings['product_thumb_quality']);
    
    $saved = $this->getTable()->insert(array_merge($params['main_data'],[
      'name'      => $finalFileName,
      'web_name'	=> Utils\Strings::webalize($finalFileName),
      'main_file' => $image_name,
      'thumb_file'=> $thumb_name,
    ]));
     
    return $saved !== FALSE ? $saved->id : 0;
  }
  
	/**
	 * Zpracování přejmenování souboru.
	 * @param $upload Hodnota navrácená funkcí save.
	 * @param $newName Nové jméno souboru.
	 * @return mixed Vlastní návratová hodnota. */
  public function rename($upload, $newName) {
    return $upload;
  }

	/**
	 * Zpracování požadavku o smazání souboru.
	 * @param $uploaded Hodnota navrácená funkcí save. */
  public function remove($uploaded) {
    $pr = $this->find($uploaded);
    if ($pr !== null) {
      $vysledok = $this->_vymazSubor($pr->main_file) ? $this->_vymazSubor($pr->thumb_file) : FALSE;
      if ($vysledok) {
        $pr->delete();
      }
    }
    return true;
  }
  
  /** 
   * Funkcia vymaze subor ak exzistuje
	 * @param string $subor Nazov suboru aj srelativnou cestou
	 * @return bool Ak zmaze alebo neexistuje(nie je co mazat) tak true inak false */
	private function _vymazSubor(string $subor): bool {
		return (is_file($subor)) ? unlink($subor) : true;
  }
  
  /**
   * Funkcia pre fotogalériu
   * @param int id Id_hlavne_menu
   * @return array */
  public function getForFotogalery(int $id): array {
    $out = [];
    foreach ($this->findBy(['id_hlavne_menu'=>$id]) as $v) {
      $out[] = [
        'id' => $v->id,
        'type'=>'product',
        'name' => $v->name,
        'web_name' => $v->web_name,
        'description' => $v->description,
        'main_file' => ($v->main_file && is_file($v->main_file)) ? $v->main_file : 'images/otaznik.png',
        'thumb_file' => $v->thumb_file
      ];
    }
    return $out;
  }
}