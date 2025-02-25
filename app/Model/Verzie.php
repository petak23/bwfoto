<?php
declare(strict_types=1);

namespace DbTable;

use Nette\Database;
use Nette\Utils\ArrayHash;
/**
 * Model, ktory sa stara o tabulku verzie
 * 
 * Posledna zmena 06.04.2022
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2022 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.2
 */
class Verzie extends Table {
  /** @var string */
  protected $tableName = 'verzie';

  /** Vrati vsetky verzie v poradi od najnovsej */
  public function vsetky(bool $return_as_array = false): Database\Table\Selection|array {
    $t = $this->getTable()->order('modified DESC');
    if ($return_as_array) {
      $o = [];
      foreach ($t as $v) {
        $o[] = [
          'cislo'=> $v->cislo,
          'id'  => $v->id,
          'user'=> $v->user_main->name,
          'modified'=> $v->modified->format('d.m.Y'),
          'subory'=> $v->subory,
          'text'  => $v->text,
        ];
        
      }
      return $o;
    }
    return $t;
  }

  /** Vrati najnovsiu verziu */
  public function posledna(): ?Database\Table\ActiveRow {
    return $this->vsetky()->limit(1)->fetch();
  }
  
  /** Ulozi verziu
   * @throws Database\DriverException */
  public function ulozVerziu(ArrayHash $values): ?Database\Table\ActiveRow  {
    try {
      $id = isset($values->id) ? $values->id : 0;
      unset($values->posli_news, $values->id);
      return $this->uloz($values, $id);
    } catch (Database\DriverException $e) {
      throw new Database\DriverException('Chyba ulozenia: '.$e->getMessage());
    }
  }
}
