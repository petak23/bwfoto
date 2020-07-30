<?php

namespace DbTable;

use Nette;
use Nette\Utils;
use Zet\FileUpload\Model;

/**
 * Model, ktory sa stara o tabulku products
 * 
 * Posledna zmena 09.07.2020
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2020 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.5
 */
class Products extends Table implements Model\IUploadModel {
  /** @var string */
  protected $tableName = 'products';
  
  /** @var string www adresar aplikacie */
  private $www_dir;

  public function setWwwDir(string $www_dir) {
    $this->www_dir = $www_dir;
  }
  
  public function getWwwDir(): string {
    return $this->www_dir;
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
    $tmp_dir = $params['products_dir']."tmp/";
    $fileName = $file->getSanitizedName();
    $pi = pathinfo($fileName);
		$fname = $pi['filename'];
		$ext = $pi['extension'];
		$additionalToken = 0;
    if (file_exists($params['products_dir'].$fileName)) {
			do { $additionalToken++;
			} while (file_exists($params['products_dir'].$fname.$additionalToken.".".$ext));
    }
		$finalFileName = ($additionalToken == 0) ? $fname : $fname.$additionalToken;
    $image_name = $tmp_dir.$finalFileName.".". $ext;
    $thumb_name = $tmp_dir.'tb_'.$finalFileName.".". $ext;

    $file->move($image_name);
    $image = Utils\Image::fromFile($image_name);
    $image->resize($params['products_settings']['product_main_x'],
                   $params['products_settings']['product_main_y'], 
                   Utils\Image::SHRINK_ONLY);
    $image->save($image_name, $params['products_settings']['product_main_quality']);
    
    copy($image_name, $thumb_name);
    $thumb = Utils\Image::fromFile($thumb_name);
    $thumb->resize($params['products_settings']['product_thumb_x'],
                   $params['products_settings']['product_thumb_y'], 
                   Utils\Image::SHRINK_ONLY); //| Image::EXACT
    $thumb->save($thumb_name, $params['products_settings']['product_thumb_quality']);
    
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
		return (is_file($subor)) ? unlink(/*$this->www_dir.*/$subor) : true;
	}
}