<?php

namespace DbTable;

/**
 * Model, ktory sa stara o tabulku hlavicka
 * 
 * Posledna zmena 05.01.2023
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.3
 */
class Hlavicka extends Table
{
  /** @var string */
  protected $tableName = 'hlavicka';

  /** Hodnoty id=>nazov pre formulare */
  public function hlavickaForm(): array
  {
    return $this->findAll()->fetchPairs('id', 'nazov');
  }
}
