<?php

namespace DbTable;
use Nette;

/**
 * Model, ktory sa stara o tabulku products
 * 
 * Posledna zmena 15.01.2018
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2018 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.1
 */
class Products extends Table {
  /** @var string */
  protected $tableName = 'products';

  /** Vracia vsetky produkty polozky
   * @param int $id - id_hlavne_menu prislusnej polozky
   * @return Nette\Database\Table\Selection|FALSE */
  public function getProducts($id) {
    return $this->findBy(["id_hlavne_menu" => $id]);
  }
  
  /** Uloženie jedneho produktu
   * @param array $data
   * @param int $id
   * @return \Nette\Database\Table\ActiveRow|FALSE */
  public function saveProduct($data, $id = 0) {
    return $this->uloz($data, $id);
  }
}
