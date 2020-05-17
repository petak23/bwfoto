<?php
declare(strict_types=1);

namespace DbTable;
use Nette;
use Nette\Database\Table;

/**
 * Model, ktory sa stara o tabulku dokumenty
 * 
 * Posledna zmena 14.05.2020
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2020 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.7
 */
class Dokumenty extends \DbTable\Table {
  /** @var string */
  protected $tableName = 'dokumenty';

  /** 
   * Vracia vsetky prilohy polozky
   * @param int $id id_hlavne_menu prislusnej polozky
   * @return Table\Selection */
  public function getPrilohy(int $id): Table\Selection {  
    return $this->findBy(["id_hlavne_menu" => $id])->order("pripona ASC");
  }
  
  /** 
   * Vracia vsetky viditelne prilohy polozky
   * @param int $id id_hlavne_menu prislusnej polozky
   * @param string $order Sposob zoradenia
   * @return Table\Selection */
  public function getViditelnePrilohy(int $id, string $order = "pripona ASC"): Table\Selection {
    return $this->findBy(["id_hlavne_menu" => $id, "zobraz_v_texte" => 1])->order($order);
  }
  
  /** 
   * Vracia vsetky viditelne prilohy - obrazky polozky
   * @param int $id id_hlavne_menu prislusnej polozky
   * @param string $order Sposob zoradenia
   * @return Table\Selection */
  public function getVisibleImages(int $id, string $order = "pripona ASC"): Table\Selection {
    return $this->findBy(["id_hlavne_menu" => $id, "zobraz_v_texte" => 1, "type" => 2])->order($order);
  }
  
  /** 
   * Vracia vsetky viditelne prilohy - video polozky
   * @param int $id id_hlavne_menu prislusnej polozky
   * @param string $order Sposob zoradenia
   * @return Table\Selection */
  public function getVisibleVideos(int $id, string $order = "pripona ASC"): Table\Selection {
    return $this->findBy(["id_hlavne_menu" => $id, "zobraz_v_texte" => 1, "type" => 3])->order($order);
  }
  
  /** 
   * Vracia vsetky viditelne prilohy - audio polozky
   * @param int $id id_hlavne_menu prislusnej polozky
   * @param string $order Sposob zoradenia
   * @return Table\Selection */
  public function getVisibleAudios(int $id, string $order = "pripona ASC"): Table\Selection {
    return $this->findBy(["id_hlavne_menu" => $id, "zobraz_v_texte" => 1, "type" => 4])->order($order);
  }
  
  /** 
   * Vracia vsetky viditelne prilohy - video polozky
   * @param int $id id_hlavne_menu prislusnej polozky
   * @param type $order Sposob zoradenia
   * @return Table\Selection */
  public function getVisibleOther(int $id, string $order = "pripona ASC"): Table\Selection {
    return $this->findBy(["id_hlavne_menu" => $id, "zobraz_v_texte" => 1, "type" => 1])->order($order);
  }
  
  /** 
   * Uloženie jednej prílohy
   * @param array $data 
   * @param mixed $id primary key
   * @return Nette\Database\Table\ActiveRow|null */
  public function ulozPrilohu(array $data, $id): ?Table\ActiveRow {
    $is_image = $data['is_image'];
    unset($data['is_image']);
    $vysledok = $this->uloz($data, $id);
    if ($vysledok !== FALSE && $is_image) { //Ak je to obrazok, tak prida znacku
      $vysledok = $this->oprav($vysledok['id'], ['znacka'=>'#I-'.$vysledok['id'].'#']);
    }
    return $vysledok;
  }
}
