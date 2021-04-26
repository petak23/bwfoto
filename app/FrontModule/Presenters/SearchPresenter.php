<?php
namespace App\FrontModule\Presenters;

use Nette\Application\Responses;

/**
 * Prezenter pre vyhadavania.
 * 
 * Posledna zmena(last change): 26.04.2021
 *
 *	Modul: FRONT
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2021 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.0.3
 */
class SearchPresenter extends BasePresenter {
  
  /** Zakladna akcia */
  public function actionDefault(string $searchStr) {
    $searchStr = '%'.$searchStr.'%';
    $search = $this->hlavne_menu_lang
                    ->findBy(['hlavne_menu.hlavne_menu_cast.mapa_stranky'=>1])
                    ->where('LOWER(menu_name) LIKE LOWER(?) OR LOWER(h1part2) LIKE LOWER(?) OR LOWER(hlavne_menu_lang.view_name) LIKE LOWER(?)', $searchStr, $searchStr, $searchStr)
                    ->fetchPairs('id', 'view_name');
    //TODO serach in clanok_lang
    $out = [];
    foreach ($search as $k => $v) {
      $out[] = [
        'id'=>$k,
        'view_name'=>$v
      ];
    }
    $this->sendResponse(new Responses\JsonResponse($out));  
  }
}
