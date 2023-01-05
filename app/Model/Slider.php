<?php

namespace DbTable;

use Nette;

/**
 * Model, ktory sa stara o tabulku slider
 * 
 * Posledna zmena 05.01.2023
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.6
 */
class Slider extends Table
{
  /** @var string */
  protected $tableName = 'slider';

  /** @var string relativny adresar k obrázkom sider-a */
  private $dir_to_slider;

  private $www_dir;

  public function __construct(
    string $dir_to_slider,
    string $www_dir,
    Nette\Database\Explorer $db
  ) {
    parent::__construct($db);
    $this->dir_to_slider = $dir_to_slider;
    $this->www_dir = $www_dir;
  }

  /** 
   * Vrati vsetky polozky z tabulky slider usporiadane podla "usporiadaj"
   * @param string $usporiadaj - názov stlpca, podla ktoreho sa usporiadava a sposob */
  public function getSlider(string $usporiadaj = 'poradie ASC'): Nette\Database\Table\Selection
  {
    return $this->findAll()->order($usporiadaj);
  }

  public function getSliderArray(): array
  {
    $t = $this->getSlider();
    $o = [];
    foreach ($t as $p) {
      $o[] = [
        'id'          => $p->id,
        'id_hlavne_menu' => $p->id_hlavne_menu,
        'poradie' => $p->poradie,
        'nadpis' => $p->nadpis,
        'popis'        => $p->popis,
        'subor'    => $p->subor,
        'zobrazenie' => $p->zobrazenie,
      ];
    }
    return $o;
  }

  /** 
   * Vrati nasledujuce cislo poradia */
  public function getNextCounter(): int
  {
    $poradie = $this->findAll()->max('poradie');
    return $poradie ? (++$poradie) : 1;
  }

  /**
   * Funkcia pre ulozenie poradia */
  public function saveOrder(array $items = []): bool
  {
    $_tmp_c = 0;
    if (is_array($items) && count($items)) {
      foreach ($items as $k => $v) {
        if ((int)$v > 0) { // Id polozky musi by cislo
          $p = $this->repair((int)$v, ['poradie' => ((int)$k + 1)]);
          if ($p->id > 0) {
            $_tmp_c++;
          }
        }
      }
      return (count($items) == $_tmp_c);
    } else {
      return false;
    }
  }

  /** Vymazanie položky slider-a 
   * @param int $id Id položky */
  public function remove(int $id): int
  {
    $temp_del = $this->find($id);
    if (is_file($this->dir_to_slider . $temp_del->subor)) {
      unlink($this->www_dir . '/' . $this->dir_to_slider . $temp_del->subor);
    }
    return $temp_del->delete();
  }
}
