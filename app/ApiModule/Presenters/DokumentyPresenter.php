<?php
namespace App\ApiModule\Presenters;

use DbTable;
use Nette\Http\FileUpload;
use Nette\Utils\ArrayHash;
use Nette\Utils\Image;
use Nette\Utils\Strings;


/**
 * Prezenter pre pristup k api dokumentov.
 * Posledna zmena(last change): 03.02.2022
 *
 * Modul: API
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2022 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.0.0
 */
class DokumentyPresenter extends BasePresenter {

  // -- DB
  /** @var DbTable\Dokumenty @inject */
  public $documents;

  public $wwwDir;

  public function __construct(array $parameters, String $wwwDir) {
    // Nastavenie z config-u
    $this->nastavenie = $parameters;
    $this->wwwDir = $wwwDir;
  }

  /**
   * Vráti informácie o dokumente
   * @param int $id Dokumentu */
  public function actionDocument(int $id) { 
    $this->sendJson($this->documents->getDocument($id));
  }

  /** Uloženie dokumentu do DB 
   * @param int $id Id_hlavne_menu, ku ktorému ukladám dokument */
  public function actionSave(int $id) {
    /* from POST:
    * - description
    * - type
    * - name
    * - priloha
    * - thumb
    */
    $user = $this->getUser();
    $hl_menu = $this->hlavne_menu->find($id);
    try {
      $uloz = [ 
        'id_hlavne_menu'	 	=> $id,
        'id_user_main'      => $user->id,
        'id_user_roles'     => $hl_menu->id_user_roles,
        'description'				=> isset($values->description) && strlen($values->description)>2 ? $values->description : NULL,
        'change'						=> date("Y-m-d H:i:s", Time()),
        'type'              => $values->type
      ];
      $name = isset($values->name) ? $values->name : "";
      if ($values->priloha && $values->priloha->error == 0) { //Ak nahravam prilohu
        $priloha_info = $this->_uploadPriloha($values);
        $uloz = array_merge($uloz, [
          'name'				=> strlen($name)>2 ? $name : $priloha_info['finalFileName'],
          'web_name'  	=> Strings::webalize($priloha_info['finalFileName']),
          'pripona'			=> $priloha_info['pripona'],
          'main_file'		=> $this->nastavenie['prilohy_dir'].$priloha_info['finalFileName'].".".$priloha_info['pripona'],
          'thumb_file'	=> $priloha_info['thumb'],
          'type'        => $priloha_info['is_image'] ? 2 : $values->type
          ]);
      } elseif ($values->thumb->hasFile() && $values->thumb->isImage()) { //Ak nahravam len nahlad
        $uloz = array_merge($uloz, ['thumb_file'	=> $this->_uploadThumb($values)]);
      }  else { //Ak len menim
        $uloz = array_merge($uloz, ['name' => strlen($name)>2 ? $name : ""]);
      }
      $vysledok = $this->dokumenty->uloz($uloz, $values->id);
      if (!empty($vysledok) && isset($priloha_info['is_image']) && $priloha_info['is_image']) { 
        $this->dokumenty->oprav($vysledok['id'], ['znacka'=>'#I-'.$vysledok['id'].'#']);
      }
		} catch (Database\DriverException $e) {
			//$button->addError($e->getMessage());
		}
  }

  
  /**
   * Upload prilohy
   * @param ArrayHash $values
   * @return array */
  private function _uploadPriloha(ArrayHash $values): array {
    //dumpe($values);
    $pr = $this->documents->find($values->id);//Zmazanie starej prílohy
    if ($pr != null) {
      if (is_file($pr->main_file)) { unlink($this->wwwDir."/".$pr->main_file);}
      if (is_file($pr->thumb_file)) { unlink($this->wwwDir."/".$pr->thumb_file);}
    }
    $fileName = $values->priloha->getSanitizedName();
		$pi = pathinfo($fileName);
		$additionalToken = 0;
		//Najdi meno suboru
		if (file_exists($this->nastavenie['prilohy_dir'].$fileName)) {
			do { $additionalToken++;
			} while (file_exists($this->nastavenie['prilohy_dir'].$pi['filename'].$additionalToken.".".$pi['extension']));
    }
		$finalFileName = ($additionalToken == 0) ? $pi['filename'] : $pi['filename'].$additionalToken;
		//Presun subor na finalne miesto a ak je to obrazok tak vytvor nahlad
		$values->priloha->move($this->nastavenie['prilohy_dir'].$finalFileName.".". $pi['extension']);
		if ($values->priloha->isImage()) {  // Ak nahravam obrazok
			$image_name = $this->nastavenie['prilohy_dir'].$finalFileName.".". $pi['extension'];
			$thumb_name = $this->nastavenie['prilohy_dir'].'tb_'.$finalFileName.".". $pi['extension'];
			$image = Image::fromFile($image_name);
      $image->resize($this->nastavenie['prilohy_images']['x'], $this->nastavenie['prilohy_images']['y'], Image::SHRINK_ONLY);
      $image->save($image_name, $this->nastavenie['prilohy_images']['kvalita']);
			copy($image_name, $thumb_name);
			$thumb = Image::fromFile($thumb_name);
			$thumb->resize($this->nastavenie['prilohy_images']['tx'], $this->nastavenie['prilohy_images']['ty'], Image::SHRINK_ONLY);// | Image::EXACT
			$thumb->save($thumb_name, $this->nastavenie['prilohy_images']['tkvalita']);
    } else { // Ak nahravam subor
      if ($values->thumb->hasFile() && $values->thumb->isImage()) { // Ak mam nahlad
        $thumbInfo = pathinfo($values->thumb->name);
        $thumb_name = $this->nastavenie['prilohy_dir'].'tb_'.$finalFileName.".".$thumbInfo['extension']; 
        $values->thumb->move($thumb_name);
        $thumb = Image::fromFile($thumb_name);
        $thumb->resize($this->nastavenie['prilohy_images']['tx'], $this->nastavenie['prilohy_images']['ty'], Image::SHRINK_ONLY);// | Image::EXACT
        $thumb->save($thumb_name, $this->nastavenie['prilohy_images']['tkvalita']);
      } else { // Ak nemam nehlad
        $thumb_name = is_file($this->wwwDir. "/ikonky/Free-file-icons-master/48px/". $pi['extension'].".png") ? "www/ikonky/Free-file-icons-master/48px/". $pi['extension'].".png" : NULL;
      }
    }
  
		return [
			'finalFileName' => $finalFileName,
			'pripona'				=> $pi['extension'],
			'thumb'					=> isset($thumb_name) ? $thumb_name : NULL,
      'is_image'      => $values->priloha->isImage()  
		];
  }
  
  /**
   * Upload nahladu
   * @param FileUpload $values
   * @return array */
  private function _uploadThumb(FileUpload $values): array {
    $pr = $this->dokumenty->find($values->id);//Zmazanie starej prílohy
    if ($pr !== FALSE) {
      if (is_file($pr->thumb_file)) { unlink($this->wwwDir."/".$pr->thumb_file);}
    }
    $main_file = pathinfo($pr->main_file);
    $additionalToken = 0;
		//Najdi meno suboru
		if (file_exists($this->nastavenie['prilohy_dir'].'tb_'.$main_file['filename'].'.jpg')) {
			do { $additionalToken++;
			} while (file_exists($this->nastavenie['prilohy_dir'].'tb_'.$main_file['filename'].$additionalToken.".jpg"));
    }
		$finalFileName = 'tb_'.$main_file['filename'].($additionalToken == 0 ? '' : $additionalToken).".jpg";
    $values->thumb->move($this->nastavenie['prilohy_dir'].$finalFileName);
    $thumb = Image::fromFile($this->nastavenie['prilohy_dir'].$finalFileName);
    $thumb->resize($this->nastavenie['prilohy_images']['tx'], $this->nastavenie['prilohy_images']['ty'], Image::SHRINK_ONLY);// | Image::EXACT
    $thumb->save($this->nastavenie['prilohy_dir'].$finalFileName, $this->nastavenie['prilohy_images']['tkvalita']);
  
		return $this->nastavenie['prilohy_dir'].$finalFileName;
  }
}