<?php

namespace DbTable;

/**
 * Model, ktory sa stara o tabulku dlzka_novinky
 * 
 * Posledna zmena 05.01.2023
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.2
 */
class Dlzka_novinky extends Table
{
  /** @var string */
  protected $tableName = 'dlzka_novinky';

  /** Hodnoty id=>nazov pre formulare */
  public function dlzkaNovinkyForm(): array
  {
    return $this->findAll()->fetchPairs('id', 'nazov');
  }
}
