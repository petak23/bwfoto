<?php

namespace DbTable;

use Nette;
use Nette\Database;

/**
 * Model, ktory sa stara o tabulku user_in_categories
 * 
 * Posledna zmena 05.01.2023
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.2
 */
class User_in_categories extends Table
{
  /** @var string */
  protected $tableName = 'user_in_categories';

  /** Ulozenie kategorie
   * @param Nette\Utils\ArrayHash $values
   * @throws Database\DriverException */
  public function saveCategori(mixed $values): ?Database\Table\ActiveRow
  {
    try {
      $id = isset($values->id) ? $values->id : 0;
      unset($values->id);
      return $this->uloz($values, $id);
    } catch (Database\DriverException $e) {
      throw new Database\DriverException('Chyba ulozenia: ' . $e->getMessage());
    }
  }

  /** Uloženie viacerích kategórií
   * @param int $id_user_main
   * @param array $categori pole = ['id'=>'id_user_in_categories']
   * @throws Database\DriverException */
  public function saveMultiCategori(int $id_user_main, array $categori)
  {
    try {
      $this->findBy(['id_user_main' => $id_user_main])->delete();
      $out = [];
      foreach ($categori as $key => $value) {
        $out[] = ['id_user_main' => $id_user_main, 'id_user_categories' => $value];
      }
      $this->pridaj($out);
    } catch (Database\DriverException $e) {
      throw new Database\DriverException('Chyba ulozenia: ' . $e->getMessage());
    }
  }

  /**
   * Pre formular pre pridanie dietata */
  public function parentForForm(): array
  {
    $p = $this->findBy(['user_categories.main_category' => 'B'])->order('user_main.priezvisko ASC, user_main.meno ASC');
    $out = [];
    foreach ($p as $r) {
      $out[$r->id_user_main] = $r->user_main->meno . " " . $r->user_main->priezvisko;
    }
    return $out;
  }
}
