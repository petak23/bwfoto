<?php

namespace DbTable;
use Nette;

/**
 * Model, ktory sa stara o tabulku products
 * 
 * Posledna zmena 14.04.2020
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2020 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.2
 */
class Products extends Table {
  /** @var string */
  protected $tableName = 'products';

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
}