<?php
namespace App\ApiModule\Presenters;

use DbTable;
use Nette\Utils;

/**
 * Prezenter pre pristup k api hlavneho menu.
 * Posledna zmena(last change): 03.11.2021
 *
 * Modul: API
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2021 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.0.1
 */
class MenuPresenter extends BasePresenter {

  // -- DB
  /** @var DbTable\Hlavne_menu_lang @inject */
	public $hlavne_menu_lang;

  /** 
   * @param int $id Id nadradenej polozky */
  public function actionGetSubmenu(int $id) {
    $tmp = $this->hlavne_menu_lang
                ->findBy(['hlavne_menu.id_nadradenej'=>$id, 'id_lang'=>1])
                ->order('poradie ASC');
    $out = [];
    foreach ($tmp as $v) {
      $out[$v->hlavne_menu->poradie] = [
        'id'		=> $v->id_hlavne_menu,
        'order'	=> $v->hlavne_menu->poradie,
        'name'	=> $v->menu_name,
        'link'	=> $this->link(":Admin:".$v->hlavne_menu->druh->presenter.":", $v->id_hlavne_menu), 
        'avatar'=> $v->hlavne_menu->avatar
        ];
    }
    //dumpe($out);
    $this->sendJson($out);
	}

  /**
   * Uloženie zmeny v poradí submenu
   */
  public function actionSaveOrderSubmenu(int $id = null) {
    // https://forum.nette.org/cs/28370-data-z-post-request-body-reactjs-appka-se-po-ceste-do-php-ztrati
    $_post = json_decode(file_get_contents("php://input"), true);

    $this->sendJson([
      'result' => $this->hlavne_menu->saveOrderSubmenu($_post['items']) ? 'OK' : 'ERR'
    ]);
  }
}