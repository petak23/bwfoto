<?php
namespace App\FrontModule\Presenters;

use Nette\Application\Responses;

/**
 * Prezenter pre vyhadavania.
 * 
 * Posledna zmena(last change): 02.06.2020
 *
 *	Modul: FRONT
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2020 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.0.0
 */
class SearchPresenter extends BasePresenter {
  
  /** Zakladna akcia */
  public function actionDefault(string $searchStr) {
    $searchStr = '%'.$searchStr.'%';
    $search = $this->hlavne_menu_lang->findAll()->where('menu_name LIKE ? OR h1part2 LIKE ? OR view_name LIKE ?', $searchStr, $searchStr, $searchStr)->fetchPairs('id');
    $out = [];
    foreach ($search as $k => $v) {
      $out[] = [
        'id'=>$k,
        'view_name'=>$v->view_name
      ];
    }
    $this->sendResponse(new Responses\JsonResponse($out));  
  }
}