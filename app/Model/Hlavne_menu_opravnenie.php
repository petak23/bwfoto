<?php

namespace DbTable;

/**
 * Model, ktory sa stara o tabulku hlavne_menu_opravnenie
 * 
 * Posledna zmena 05.01.2023
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.1
 */
class Hlavne_menu_opravnenie extends Table
{
  /** @var string */
  protected $tableName = 'hlavne_menu_opravnenie';

  /** Hodnoty id=>nazov pre formulare */
  public function opravnenieForm(): array
  {
    return $this->findAll()->fetchPairs('id', 'nazov');
  }
}
