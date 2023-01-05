<?php

namespace App\FrontModule\Components\Products;

use DbTable;
use Language_support;
use Nette\Application\UI\Control;

/**
 * Komponenta pre zobrazenie produktov pre FRONT modul
 * 
 * Posledna zmena(last change): 04.01.2023
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.3
 * 
 * @todo Odstranit premennu nastavenie, ak nebude potrebna.
 */
class ProductsViewControl extends Control
{

  /** @var DbTable\Products */
  private $products;
  /** @var Language_support\LanguageMain Prednastavene texty pre komponentu */
  public $texts;
  /** @var int */
  private $article;
  /** @var array */
  private $nastavenie;

  /**
   * @param DbTable\Products $products
   * @param DbTable\Lang $lang  */
  public function __construct(DbTable\Products $products, DbTable\Lang $lang)
  {
    parent::__construct();
    $this->products = $products;
    $this->texts = new Language_support\LanguageMain($lang);
  }

  /** Nastavenie id polozky, ku ktorej patria produkty
   * @param Nette\Database\Table\ActiveRow $article Polozka menu ku ktorej je priradeny
   * @param string|int $language jazyka
   * @param array $nastavenie Nastavenia z configu */
  public function setNastav($article, $language, array $nastavenie): ProductsViewControl
  {
    $this->article = $article;
    $this->texts->setLanguage($language);
    $this->nastavenie = $nastavenie;
    return $this;
  }

  /** 
   * Render 
   * @param array $params Doplnkové parametre ['templateFile'=>'Alternativny nazov template', 'big_img_id'=>'Id velkeho obrazku'] */
  public function render(array $params = [])
  {
    $template_file = ((isset($params['templateFile']) && is_file(__DIR__ . "/" . $params['templateFile'] . ".latte")) ? $params['templateFile'] : "default");
    $this->template->setFile(__DIR__ . "/" . $template_file . ".latte");
    $this->template->products = $this->products->getProducts($this->article->id_hlavne_menu);
    $this->template->setTranslator($this->texts);
    $this->template->big_img_id = isset($params['big_img_id']) ? $params['big_img_id'] : 0;
    $this->template->nastavenie = $this->nastavenie;
    $servise = $this;
    $this->template->addFilter('odkazdo', function ($id) use ($servise) {
      $serv = $servise->presenter->link("Products:default", ["id" => $id]);
      return $serv;
    });
    $this->template->render();
  }
}

interface IProductsViewControl
{
  function create(): ProductsViewControl;
}
