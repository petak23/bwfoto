<?php

namespace DbTable;

use Nette;
use Nette\Utils;

/**
 * Model, ktory sa stara o tabulku products
 * 
 * Posledna zmena 28.04.2022
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2022 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.9
 */
class Products extends Table {
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
  
  /**
	 * Ulozenie nahravaneho suboru cez vue uploader.
	 * @param Nette\Http\FileUpload $file
	 * @param array $params Pole vlastnych hodnot.
	 * @return int Id ulozenej polozky ulozena v DB. */
  public function saveUpload(Nette\Http\FileUpload $file, array $params = []): int {
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
    $image_name = $this->dir_to_products.$finalFileName.".". $ext;
    $thumb_name = $this->dir_to_products.'tb_'.$finalFileName.".". $ext;

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
	 * @return mixed Vlastní návratová hodnota. 
   * @todo ---------- Nedokončené ------------
   * */
  public function rename($upload, $newName) {
    return $upload;
  }

	/**
	 * Zpracování požadavku o smazání souboru.
	 * @param $id Id mazaného produktu. 
   * @return bool Ak zmaze alebo neexistuje(nie je co mazat) tak true inak false */
  public function remove(int $id): bool {
    $pr = $this->find($id);
    if ($pr !== null) {
      $vysledok = $this->_vymazSubor($pr->main_file) ? $this->_vymazSubor($pr->thumb_file) : false;
      if ($vysledok) {
        $pr->delete();
        return true;
      }
    }
    return false;
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

  /**
   * Vráti konkrétny produkt s daným id 
   * @param int $id Id produktu */
  public function getProduct(int $id): array {
    $p = $this->find($id);
    return [
      'id'          => $p->id,
      'name'        => $p->name,
      'main_file'   => $p->main_file,
      'thumb_file'  => $p->thumb_file,
      'description' => $p->description,
    ];
  }

  /** Vráti všetky produkty ako pole */
  public function getProductsArray(int $id_hlavne_menu): array {
    $t = $this->findBy(['id_hlavne_menu'=>$id_hlavne_menu]);
    $o = [];
    foreach ($t as $p) {
      $o[] = [
        'id'          => $p->id,
        'id_hlavne_menu'=> $p->id_hlavne_menu,
        'id_user_main'=> $p->id_user_main,
        'id_user_roles'=> $p->id_user_roles,
        'name'        => $p->name,
        'web_name'    => $p->web_name,
        'description' => $p->description,
        'main_file'   => $p->main_file,
        'thumb_file'  => $p->thumb_file,
        'change'      => $p->change,
      ];
    }
    return $o;
  }
}