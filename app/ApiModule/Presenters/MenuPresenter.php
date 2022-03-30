<?php
namespace App\ApiModule\Presenters;

use DbTable;
use Nette\Application\Responses\TextResponse;
use Nette\Utils;
use Texy;

/**
 * Prezenter pre pristup k api hlavneho menu.
 * Posledna zmena(last change): 25.03.2022
 *
 * Modul: API
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2022 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.0.3
 */
class MenuPresenter extends BasePresenter {

  // -- DB
  /** @var DbTable\Admin_menu @inject */
  public $admin_menu;
  /** @var DbTable\Hlavne_menu_lang @inject */
	public $hlavne_menu_lang;

  /** @var Texy\Texy @inject */
	public $texy;

  /**
   * Vráti kompletné menu v json */
  public function actionGetMenu() { 
    $menu = new \App\AdminModule\Components\Menu\Menu;
    $menu->setNastavenie($this->nastavenie);
    $hl_m = $this->hlavne_menu->getMenuAdmin(1);
    if (count($hl_m)) {
      $servise = $this;
      $menu->fromTable($hl_m, function($node, $row) use($servise){
        foreach (["name", "tooltip", "avatar", "anotacia", "node_class", "id", "datum_platnosti"] as $v) { 
          $node->$v = $row['node']->$v; 
        }
        $node->link = is_array($row['node']->link) ? 
                      $servise->link(":Admin:".$row['node']->link[0], ["id"=>$row['node']->id]) : 
                      $servise->link(":Admin:".$row['node']->link);
        return $row['nadradena'] ? $row['nadradena'] : null;
      });
    }
    $this->sendJson($menu->getApiMenu());
  }

  /** 
   * @param int $id Id nadradenej polozky */
  public function actionGetSubmenu(int $id, String $lmodule = "Admin") {
    $tmp = $this->hlavne_menu_lang
                ->findBy(['hlavne_menu.id_nadradenej'=>$id, 'id_lang'=>1])
                ->order('poradie ASC');
    $out = [];
    foreach ($tmp as $v) {
      $p = $lmodule == "front" && $v->hlavne_menu->druh->presenter == "Menu" ? "Clanky" : $v->hlavne_menu->druh->presenter;
      $out[$v->hlavne_menu->poradie] = [
        'id'		=> $v->id_hlavne_menu,
        'order'	=> $v->hlavne_menu->poradie,
        'name'	=> $v->menu_name,
        'link'	=> $this->link(":".ucfirst($lmodule).":$p:", $v->id_hlavne_menu), 
        'avatar'=> $v->hlavne_menu->avatar,
        'node_class' => ($v->hlavne_menu->ikonka !== null && strlen($v->hlavne_menu->ikonka)>2) ? $v->hlavne_menu->ikonka : null,
        ];
    }
    //dumpe($out);
    $this->sendJson($out);
	}

  /**
   * Uloženie zmeny v poradí submenu */
  public function actionSaveOrderSubmenu(int $id = null) {
    // https://forum.nette.org/cs/28370-data-z-post-request-body-reactjs-appka-se-po-ceste-do-php-ztrati
    $_post = json_decode(file_get_contents("php://input"), true);

    $this->sendJson([
      'result' => $this->hlavne_menu->saveOrderSubmenu($_post['items']) ? 'OK' : 'ERR'
    ]);
  }

  /**
   * Vráti administračné menu podľa úrovne registrácie */
  public function actionGetAdminMenu() {
    $am = $this->admin_menu->getAdminMenu($this->user->getIdentity()->id_user_roles);
    
    foreach ($am as $k => $v) {
      $am[$k]['link'] = $this->link(':Admin:'.$v['link']);
    }
    
    $this->sendJson($am);
  }

  /**
   * Vráti jednu položku menu */
  public function actionGetOneMenuArticle(int $id = null) {
    $tmp = $this->hlavne_menu_lang->getOneArticleId($id, $id_lang = 1, $this->user->getIdentity()->id_user_roles = 0);
    //dumpe($tmp->toArray());
    $this->sendJson($tmp->toArray());
  }
  
  public function actionTexylaPreview() {
    // https://forum.nette.org/cs/28370-data-z-post-request-body-reactjs-appka-se-po-ceste-do-php-ztrati
    $_post = json_decode(file_get_contents("php://input"), true);

    $this->sendResponse(new TextResponse($this->texy->process($_post["texy"])));
  }

  public function actionTexylaSave(int $id) {
    // https://forum.nette.org/cs/28370-data-z-post-request-body-reactjs-appka-se-po-ceste-do-php-ztrati
    $_post = json_decode(file_get_contents("php://input"), true);
    
    $this->sendJson([
      'result' => $this->hlavne_menu_lang->saveText($id, $this->lang->getLngId($this->language), $_post['texy']) ? 'OK' : 'ERR'
    ]);
  }
}