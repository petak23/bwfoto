<?php

namespace App\FrontModule\Components\Products;

use DbTable;
use Language_support;
use Nette\Application\UI\Control;

/**
 * Komponenta pre zobrazenie produktov pre FRONT modul
 * 
 * Posledna zmena(last change): 15.01.2018
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2012 - 2018 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.0
 */
class ProductsViewControl extends Control {

  /** @var DbTable\Products */
  private $products;
  /** @var Language_support\Clanky */
  public $texts;
  /** @var int */
  private $article;
  /** @var array */
  private $nastavenie;

  /**
   * @param DbTable\Products $products
   * @param Language_support\Clanky $texts */
  public function __construct(DbTable\Products $products, Language_support\Clanky $texts) {
    parent::__construct();
    $this->products = $products;
    $this->texts = $texts;
  }

  /** Nastavenie id polozky, ku ktorej patria produkty
   * @param Nette\Database\Table\ActiveRow $article Polozka menu ku ktorej je priradeny
   * @param int $id_lang Id jazyka
   * @param array $nastavenie Nastavenia z configu
   * @return \App\FrontModule\Components\Products\ProductsViewControl */
  public function setNastav($article, $id_lang, $nastavenie) {
    $this->article = $article;
    $this->texts->setLanguage($id_lang);
    $this->nastavenie = $nastavenie;
    return $this;
  }

  /** Render */
  public function render($params = []) {
    $template_file = ((isset($params['templateFile']) && is_file(__DIR__ ."/".$params['templateFile'].".latte")) ? $params['templateFile'] : "default");
    $this->template->setFile(__DIR__ . "/".$template_file.".latte");
    $this->template->products = $this->products->getProducts($this->article->id_hlavne_menu);
    $this->template->texts = $this->texts;
    $this->template->big_img_id = isset($params['big_img_id']) ? $params['big_img_id'] : 0;
    $this->template->nastavenie = $this->nastavenie;
    $this->template->render();
  }

  protected function createTemplate($class = NULL) {
    $servise = $this;
    $template = parent::createTemplate($class);
    $template->addFilter('odkazdo', function ($id) use($servise) {
      $serv = $servise->presenter->link("Products:default", ["id" => $id]);
      return $serv;
    });
    return $template;
  }

}

interface IProductsViewControl {
  /** @return ProductsViewControl */
  function create();
}