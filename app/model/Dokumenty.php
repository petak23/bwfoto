<?php

namespace DbTable;
use Nette;

/**
 * Model, ktory sa stara o tabulku dokumenty
 * 
 * Posledna zmena 08.11.2017
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2016 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.5
 */
class Dokumenty extends Table {
  /** @var string */
  protected $tableName = 'dokumenty';

  /** Vracia vsetky prilohy polozky
   * @param int $id - id_hlavne_menu prislusnej polozky
   * @return Nette\Database\Table\Selection|FALSE */
  public function getPrilohy($id) {  
    return $this->findBy(["id_hlavne_menu", $id])->order("pripona ASC");
  }
  
  /** Vracia vsetky viditelne prilohy polozky
   * @param int $id - id_hlavne_menu prislusnej polozky
   * @param type $order Sposob zoradenia
   * @return Nette\Database\Table\Selection|FALSE */
  public function getViditelnePrilohy($id, $order = "pripona ASC") {
    return $this->findBy(["id_hlavne_menu"=>$id, "zobraz_v_texte"=>1])->order($order);
  }
  
  /** Vracia vsetky viditelne prilohy - obrazky polozky
   * @param int $id id_hlavne_menu prislusnej polozky
   * @param type $order Sposob zoradenia
   * @return Nette\Database\Table\Selection|FALSE */
  public function getVisibleImages($id, $order = "pripona ASC") {
    return $this->findBy(["id_hlavne_menu"=>$id, "zobraz_v_texte"=>1, "type"=>2])->order($order);
  }
  
  /** Vracia vsetky viditelne prilohy - video polozky
   * @param int $id id_hlavne_menu prislusnej polozky
   * @param type $order Sposob zoradenia
   * @return Nette\Database\Table\Selection|FALSE */
  public function getVisibleVideos($id, $order = "pripona ASC") {
    return $this->findBy(["id_hlavne_menu"=>$id, "zobraz_v_texte"=>1, "type"=>3])->order($order);
  }
  
  /** Vracia vsetky viditelne prilohy - audio polozky
   * @param int $id id_hlavne_menu prislusnej polozky
   * @param type $order Sposob zoradenia
   * @return Nette\Database\Table\Selection|FALSE */
  public function getVisibleAudios($id, $order = "pripona ASC") {
    return $this->findBy(["id_hlavne_menu"=>$id, "zobraz_v_texte"=>1, "type"=>4])->order($order);
  }
  
    /** Vracia vsetky viditelne prilohy - video polozky
   * @param int $id id_hlavne_menu prislusnej polozky
   * @param type $order Sposob zoradenia
   * @return Nette\Database\Table\Selection|FALSE */
  public function getVisibleOther($id, $order = "pripona ASC") {
    return $this->findBy(["id_hlavne_menu"=>$id, "zobraz_v_texte"=>1, "type"=>1])->order($order);
  }
  
  /** Uloženie jednej prílohy
   * @param array $data
   * @param int $id
   * @return \Nette\Database\Table\ActiveRow|FALSE */
  public function ulozPrilohu($data, $id) {
    $is_image = $data['is_image'];
    unset($data['is_image']);
    $vysledok = $this->uloz($data, $id);
    if ($vysledok !== FALSE && $is_image) { //Ak je to obrazok, tak prida znacku
      $vysledok = $this->oprav($vysledok['id'], ['znacka'=>'#I-'.$vysledok['id'].'#']);
    }
    return $vysledok;
  }
}
