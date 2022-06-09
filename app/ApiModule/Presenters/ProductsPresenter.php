<?php

namespace App\ApiModule\Presenters;

use DbTable;
use Nette\Http\FileUpload;

/**
 * Prezenter pre pristup k api produktov.
 * Posledna zmena(last change): 08.06.2022
 *
 * Modul: API
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2022 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.0.4
 * 
 * @help 1.) https://forum.nette.org/cs/28370-data-z-post-request-body-reactjs-appka-se-po-ceste-do-php-ztrati
 */
class ProductsPresenter extends BasePresenter
{

  // -- DB
  /** @var DbTable\Products @inject */
  public $products;

  /** @var String */
  public $wwwDir;
  /** @var array */
  private $products_settings;

  public function __construct(array $parameters, String $wwwDir)
  {
    // Nastavenie z config-u
    $this->nastavenie = $parameters;
    $this->wwwDir = $wwwDir;
  }

  /**
   * Vráti informácie o produkte
   * @param int $id Id produktu */
  public function actionProduct(int $id): void
  {
    $this->sendJson($this->products->getProduct($id));
  }

  /**
   * Vráti relevantné produkty
   * @param int id Id hlavného menu */
  public function actionGetItems(int $id): void
  {
    $this->sendJson($this->products->getProductsArray($id));
  }

  /** 
   * Uloženie produktu/produktov do DB 
   * @param int $id Id_hlavne_menu, ku ktorému ukladám produkt 
   * ----------------------------------------
   * @todo Kontrola duplicitných súborov
   * @todo Kontrola max. veľkosti súboru
   * ----------------------------------------
   * */
  public function actionSave(int $id)
  {
    /* from POST:
    * - description
    * - name */
    $values = $this->getHttpRequest()->getPost();

    /* from POST:
    * - files */
    $files = $this->getHttpRequest()->getFiles();

    // Ak niet čo ukladať...
    if (!(isset($files['files']) && is_array($files['files']) && count($files['files']))) {
      $this->sendJson([
        'status'  => 500,
        'data'    => null,
        'error'   => 'Nothing to save...',
      ]);
      exit;
    }
    $user = $this->getUser();
    $hl_menu = $this->hlavne_menu->find($id);
    $this->products_settings = $this->udaje->findBy(['id_druh' => 8])->fetchPairs('nazov', 'text');

    $data_save = [
      'id_hlavne_menu'     => $id,
      'id_user_main'      => $user->id,
      'id_user_roles'     => $hl_menu->id_user_roles,
      //'description'				=> isset($values['description']) && strlen($values['description'])>2 ? $values['description'] : NULL,
      //'name'              => (isset($values['name']) && strlen($values["name"]) > 2) ? $values['name'] : "",
      'panorama'          => $hl_menu->id_hlavne_menu_template == 6,
    ];

    if (is_array($files['files'])) { //MultiUpload
      $upload = [
        'status'  => 200,
        'data'    => [],
      ];
      foreach ($files['files'] as $file) {
        $_up = $this->_saveProduct($file, $data_save);
        if ($_up == null) {
          $upload['status'] = 500;
          $upload['data'][] = null;
        } else {
          $upload['data'][] = $_up;
        }
      }
    } else {  // SingleUpload
      $_up = $this->_saveProduct($files['files'], $data_save);
      $upload = $_up !== null ? ['status' => 200, 'data' => $_up] : ['status' => 500, 'data' => null];
    }

    if ($this->isAjax()) {
      $this->sendJson($upload);
    } else {
      $this->redirect(':Admin:Clanky:', $id);
    }
  }

  private function _saveProduct(FileUpload $file, array $data_save): ?array
  {
    $result = ($file->error == 0) ? $this->products->saveUpload(
      $file,
      [
        'products_settings' => $this->products_settings,
        'panorama' => $data_save['panorama'],
        'main_data' => [
          'id_hlavne_menu'  => $data_save['id_hlavne_menu'],
          'id_user_main'    => $data_save['id_user_main'],
          'id_user_roles'   => $data_save['id_user_roles'],
        ]
      ]
    ) : 0; // Ak je chyba v uploade
    return $result ? $this->products->getProduct($result) : null;
  }

  /** Vymazanie dokumentu z DB 
   * @param int $id Iddokumentu */
  public function actionDelete(int $id)
  {
    if ($this->getUser()->isLoggedIn() && $this->getUser()->isAllowed($this->name, $this->action)) { //Preventývna kontrola
      $out = $this->products->remove($id) ? ['status' => 200, 'data' => 'OK'] : ['status' => 500, 'data' => null]; // 500 Internal Server Error
    } else {
      $out = ['status' => 401, 'data' => null]; //401 Unauthorized (RFC 7235) Používaný tam, kde je vyžadovaná autorizácia, ale zatiaľ nebola vykonaná. 
    }

    if ($this->isAjax()) {
      $this->sendJson($out);
    } else {
      $this->redirect(':Admin:Clanky:', $id);
    }
  }

  /** 
   * Oprava produktu v DB 
   * @param int $id Id_hlavne_menu, ku ktorému ukladám produkt */
  public function actionUpdate(int $id): void
  {
    /* from POST: */
    $values = json_decode(file_get_contents("php://input"), true); // @help 1.)

    $this->products->saveProduct($values, $id);
    $this->sendJson(['status' => 200, 'data' => 'OK']);
  }

  /**
   * Nastaví počet položiek na stránku pre konkrétneho užívateľa */
  public function actionChangeperpage(): void
  {
    /* from POST: */
    $values = json_decode(file_get_contents("php://input"), true); // @help 1.)

    $out = $this->udaje->editKey('products_per_page', $values['items_per_page'], $this->user->id);

    $this->sendJson($out != null ? ['status' => 200, 'data' => 'OK'] : ['status' => 500, 'data' => 'ER']);
  }

  /**
   * Vráti počet položiek na stránku pre prihláseného používateľa */
  public function actionGetPerPage(): void
  {
    $this->sendJson($this->udaje->getValByName('products_per_page', $this->user->id));
  }
}
