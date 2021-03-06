<?php
declare(strict_types=1);

namespace App\MapaModule\Presenters;

use App\MapaModule\Components\Menu\Menu;
use Nette\Application\UI;
use DbTable;

/**
 * Prezenter pre vypisanie suboru sitemap.xml
 * Posledna zmena(last change): 22.03.2021
 *
 *	Modul: MAPA
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2021 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.0.7
 */
class MapaPresenter extends UI\Presenter {
  // -- DB
  /** @var DbTable\Hlavne_menu @inject */
	public $hlavne_menu;
  
  /** 
   * Komponenta pre nacitanie menu 
   * @return Menu */
  public function createComponentMenu(): Menu {
    $servise = $this;
    $menu = new Menu;
    $hl_m = $this->hlavne_menu->getMenuMapa(1);
    if (count($hl_m)) {
      $menu->fromTable($hl_m, function($node, $row) use($servise) {
        $node->id = $row['node']->id;
        // Nasledujuca cast priradi do $node->link odkaz podla kriteria:
        // Ak $rna == NULL - vytvori link ako odkaz do aplikacie
        // Ak $rna zacina "http" - pouzije sa absolutna adresa
        // Ak $rna obsahuje text "Clanky:default 2" - vytvorí sa odkaz do aplikácie na clanok s id 2 - moze byt aj bez casti ":2" odkazu ale musí byť aj default
        $rna = $row['node']->absolutna;
        if ($rna !== NULL) {
          $node->link = strpos($rna, 'http') !== FALSE ? $rna 
                                                        : (count($p = explode(" ", $rna)) == 2 ? $servise->link(':Front:'.$p[0], ["id"=>$p[1]]) 
                                                                                              : $servise->link(':Front:'.$p[0]));
        } else {
          $node->link = is_array($row['node']->link) ? $servise->link($row['node']->link[0], ["id"=>$row['node']->id]) 
                                                      : $servise->link($row['node']->link);
        }
        return $row['nadradena'] ? $row['nadradena'] : null;
      });
    }  
    return $menu;
  }
}