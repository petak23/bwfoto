<?php
namespace App\ApiModule\Presenters;

use DbTable;
use Nette\Utils;

/**
 * Prezenter pre pristup k api hlavneho menu.
 * Posledna zmena(last change): 28.10.2021
 *
 * Modul: API
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2021 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.0.0
 */
class MenuPresenter extends BasePresenter {

  // -- DB
  /** @var DbTable\Hlavne_menu_lang @inject */
	public $hlavne_menu_lang;

  /** 
   * @param int $id Id nadradenej polozky */
  public function actionGetSubmenu(int $id) {
    $tmp = $this->hlavne_menu_lang->findBy(['hlavne_menu.id_nadradenej'=>$id, 'id_lang'=>1]);
    $out = [];
    foreach ($tmp as $v) {
      $out[$v->id_hlavne_menu] = [
        'id'		=> $v->id,
        'order'	=> $v->hlavne_menu->poradie,
        'name'	=> $v->menu_name,
        'link'	=> $this->link(":Admin:".$v->hlavne_menu->druh->presenter.":", $v->id_hlavne_menu), 
        'avatar'=> $v->hlavne_menu->avatar
        ];
    }
    //dumpe($out);
    $this->sendJson($out);
	}

  public function actionSaveSubmenu(json $submenu) {
    try {
      $tmp = Utils\Json::decode($json);
    } catch (Utils\JsonException $e) {
      // Ošetrenie výnimky...
    }
    dumpe($tmp);       
  }
}