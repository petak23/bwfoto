<?php

namespace DbTable;

use Nette;

/**
 * Model starajuci sa o tabulku user_roles
 * 
 * Posledna zmena 05.01.2023
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.1
 */
class User_roles extends Table
{
  /** @var string */
  protected $tableName = 'user_roles';

  /** 
   * Hlada urovne registracie uzivatela v rozsahu od do */
  public function hladaj_urovne(int $min, int $max): Nette\Database\Table\Selection
  {
    return $this->findBy(['id>=' . $min, 'id<=' . $max]);
  }

  /** 
   * Dava vsetky urovne registracie do poľa role=>id */
  public function vsetky_urovne_array(): array
  {
    return $this->findAll()->fetchPairs('role', 'id');
  }

  /** 
   * Hodnoty id=>name pre formulare
   * @param int $id_reg Uroven registracie*/
  public function urovneReg(int $id_reg): array
  {
    return $this->hladaj_urovne(0, $id_reg)->fetchPairs('id', 'name');
  }
}
