<?php
namespace App\ApiModule\Presenters;

use DbTable;
use Nette\Http\FileUpload;
use Nette\Utils\ArrayHash;
use Nette\Utils\Image;
use Nette\Utils\Strings;


/**
 * Prezenter pre pristup k api dokumentov.
 * Posledna zmena(last change): 20.02.2022
 *
 * Modul: API
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2022 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.0.1
 */
class DokumentyPresenter extends BasePresenter {

  // -- DB
  /** @var DbTable\Dokumenty @inject */
  public $documents;

  /** @var String */
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
    //$_post = json_decode(file_get_contents("php://input"), true);
    /* from POST:
    * - description
    * - type
    * - name
    */
    $values = $this->getHttpRequest()->getPost();

    /* from POST:
    * - priloha
    * - thumb
    */
    $files = $this->getHttpRequest()->getFiles();
    $user = $this->getUser();
    $hl_menu = $this->hlavne_menu->find($id);
    //try {
      $uloz = [ 
        'id_hlavne_menu'	 	=> $id,
        'id_user_main'      => $user->id,
        'id_user_roles'     => $hl_menu->id_user_roles,
        'description'				=> isset($values['description']) && strlen($values['description'])>2 ? $values['description'] : NULL,
        'change'						=> date("Y-m-d H:i:s", Time()),
        'type'              => $values['type']
      ];
      $name = isset($values['name']) ? $values['name'] : "";
      if ($files['priloha'] && $files['priloha']->error == 0) { //Ak nahravam prilohu
        $priloha_info = $this->_uploadPriloha($values['id'], $files);
        $uloz = array_merge($uloz, [
          'name'				=> strlen($name)>2 ? $name : $priloha_info['finalFileName'],
          'web_name'  	=> Strings::webalize($priloha_info['finalFileName']),
          'pripona'			=> $priloha_info['pripona'],
          'main_file'		=> $this->nastavenie['prilohy_dir'].$priloha_info['finalFileName'].".".$priloha_info['pripona'],
          'thumb_file'	=> $priloha_info['thumb'],
          'type'        => $priloha_info['is_image'] ? 2 : $values->type
          ]);
      } elseif ($files['thumb']->hasFile() && $files['thumb']->isImage()) { //Ak nahravam len nahlad
        $uloz = array_merge($uloz, ['thumb_file'	=> $this->_uploadThumb($values['id'], $files['thumb'])]);
      }  else { //Ak len menim
        $uloz = array_merge($uloz, ['name' => strlen($name)>2 ? $name : ""]);
      }
      $vysledok = $this->documents->uloz($uloz, $values['id']);
      if (!empty($vysledok) && isset($priloha_info['is_image']) && $priloha_info['is_image']) { 
        $this->documents->oprav($vysledok['id'], ['znacka'=>'#I-'.$vysledok['id'].'#']);
      }
      if ($this->isAjax()) {
        //
        $this->sendJson(['status'=>202, 'data'=>$this->documents->getDocument($vysledok['id'])]);
      } else {
        $this->redirect(':Admin:Clanky:', $id);
      }
      
		//} catch (Database\DriverException $e) {
			//$button->addError($e->getMessage());
		//}
  }

  
  /**
   * Upload prilohy
   * @return array */
  private function _uploadPriloha(int $id, array $values): array {
    if ($id) { // Hladám, len ak mením
      $pr = $this->documents->find($id);
      if ($pr != null) { //Zmazanie starých súborov prílohy
        if (is_file($pr->main_file)) { unlink($this->wwwDir."/".$pr->main_file);}
        if (is_file($pr->thumb_file)) { unlink($this->wwwDir."/".$pr->thumb_file);}
      }
    }
    
    // Vytvor bezkonfliktné meno súboru na uloženie
    $fname = $this->_getFileName($values['priloha'], $this->nastavenie['prilohy_dir']);

    //dumpe($values['priloha'], $fname);
		//Presun subor na finalne miesto a ak je to obrazok tak vytvor nahlad
		$values['priloha']->move($this->nastavenie['prilohy_dir'].$fname['ename']);
		if ($values['priloha']->isImage()) {  // Ak nahravam obrazok
			$image_name = $this->nastavenie['prilohy_dir'].$fname['ename'];
			$thumb_name = $this->nastavenie['prilohy_dir'].'tb_'.$fname['ename'];
			$image = Image::fromFile($image_name);
      $image->resize($this->nastavenie['prilohy_images']['x'], $this->nastavenie['prilohy_images']['y'], Image::SHRINK_ONLY);
      $image->save($image_name, $this->nastavenie['prilohy_images']['kvalita']);
			copy($image_name, $thumb_name);
			$thumb = Image::fromFile($thumb_name);
			$thumb->resize($this->nastavenie['prilohy_images']['tx'], $this->nastavenie['prilohy_images']['ty'], Image::SHRINK_ONLY);// | Image::EXACT
			$thumb->save($thumb_name, $this->nastavenie['prilohy_images']['tkvalita']);
    } else { // Ak nahravam subor
      if ($values['thumb']->hasFile() && $values['thumb']->isImage()) { // Ak mam nahlad
        $thumbInfo = pathinfo($values['thumb']->name);
        $thumb_name = $this->nastavenie['prilohy_dir'].'tb_'.$fname['ename']; 
        $values['thumb']->move($thumb_name);
        $thumb = Image::fromFile($thumb_name);
        $thumb->resize($this->nastavenie['prilohy_images']['tx'], $this->nastavenie['prilohy_images']['ty'], Image::SHRINK_ONLY);// | Image::EXACT
        $thumb->save($thumb_name, $this->nastavenie['prilohy_images']['tkvalita']);
      } else { // Ak nemam nehlad
        $thumb_name = is_file($this->wwwDir. "/ikonky/Free-file-icons-master/48px/". $fname['extension'].".png") ? "www/ikonky/Free-file-icons-master/48px/". $pi['extension'].".png" : NULL;
      }
    }
  
		return [
			'finalFileName' => $fname['name'],
			'pripona'				=> $fname['extension'],
			'thumb'					=> isset($thumb_name) ? $thumb_name : NULL,
      'is_image'      => $values['priloha']->isImage()  
		];
  }
  
  /**
   * Upload nahladu
   * @param int $id Id dokumentu. Akje 0 tak sa pridáva
   * @param FileUpload $thumb Súbor náhľadu.
   * @return String Kompletný názov uloženého súboru aj s reltívnou cestou */
  private function _uploadThumb(int $id, FileUpload $thumb): String {
    if ($id) { // Mazanie sa vykoná len ak je $id > 0
      $pr = $this->dokumenty->find($id);//Zmazanie starej prílohy
      if ($pr !== FALSE) {
        if (is_file($pr->thumb_file)) { unlink($this->wwwDir."/".$pr->thumb_file);}
      }
    }
    
    // Vytvor bezkonfliktné meno súboru na uloženie
    $fname = $this->_getFileName($thumb, $this->nastavenie['prilohy_dir'], 'tb_');

    // Uloženie náhľadu podľa paametrov
    $thumb->move($this->nastavenie['prilohy_dir'].$fname['ename']);
    $thumb = Image::fromFile($this->nastavenie['prilohy_dir'].$fname['ename']);
    $thumb->resize($this->nastavenie['prilohy_images']['tx'], $this->nastavenie['prilohy_images']['ty'], Image::SHRINK_ONLY);// | Image::EXACT
    $thumb->save($this->nastavenie['prilohy_dir'].$fname['ename'], $this->nastavenie['prilohy_images']['tkvalita']);
  
		return $this->nastavenie['prilohy_dir'].$fname['ename'];
  }

  /** 
   * Vytvorenie bezpečného mena súboru 
   * @return array ['name'=>'meno súboru', 'ename'=>'meno súboru s príponou', 'extension'=>'prípona'] */
  private function _getFileName(FileUpload $file, String $dir, String $prefix = ""): array {
    $file_info = pathinfo($file->getSanitizedName());
    $additionalToken = 0;
		//Najdi meno suboru
		if (file_exists($dir.$prefix.$file_info['filename'].$file_info['extension'])) {
			do { $additionalToken++;
			} while (file_exists($dir.$prefix.$file_info['filename'].$additionalToken.$file_info['extension']));
    }
	
    return [
      'name'=> $prefix.$file_info['filename'].($additionalToken == 0 ? '' : $additionalToken),
      'ename' => $prefix.$file_info['filename'].($additionalToken == 0 ? '' : $additionalToken).".".$file_info['extension'],
      'extension' => $file_info['extension']
    ];
  }
}