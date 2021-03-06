<?php

namespace DbTable;

/**
 * Model, ktory sa stara o tabulku hlavicka
 * 
 * Posledna zmena 06.06.2017
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2016 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.2
 */
class Hlavicka extends Table {
  /** @var string */
  protected $tableName = 'hlavicka';
  
  /** Hodnoty id=>nazov pre formulare
   * @return array */
  public function hlavickaForm() {
    return $this->findAll()->fetchPairs('id', 'nazov');
  }  
}
