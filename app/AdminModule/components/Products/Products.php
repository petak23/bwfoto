<?php

declare(strict_types=1);

namespace App\AdminModule\Components\Products;

use DbTable;
use Nette;
use Nette\Security\User;
use Nette\Utils\Json;
//use Nette\Utils\Html;
//use Ublaboo\DataGrid\DataGrid;
//use Ublaboo\DataGrid\Column\Action\Confirmation;
//use Ublaboo\DataGrid\Localization\SimpleTranslator;

/**
 * Komponenta pre spravu produktov clanku.
 * 
 * Posledna zmena(last change): 06.05.2022
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com> 
 * @copyright Copyright (c) 2012 - 2022 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.1.9
 */

class ProductsControl extends Nette\Application\UI\Control {

  /** @var DbTable\Products */
  public $products;
  /** @var Nette\Database\Table\ActiveRow $clanok Info o clanku */
  private $clanok;

  /** @var array */
  private $admin_links;
  /** @var Nette\Security\User */
  private $user;
  
  /** @var array */
//  private $texts_for_translator;

  /**
   * @param array $texts_for_translator
   * @param DbTable\Products $products
   * @param User $user
   * @param Nette\Http\Request $request */
  public function __construct(//array $texts_for_translator,
                              DbTable\Products $products, 
                              User $user,
                              ) {
//    $this->texts_for_translator = $texts_for_translator;
    $this->products = $products;
    $this->user = $user;
  }
  
  /** 
   * Nastavenie komponenty
   * @param Nette\Database\Table\ActiveRow $clanok
   * @param string $name
   * @return ProductsControl */
  public function setTitle(Nette\Database\Table\ActiveRow $clanok, 
                           string $name): self {
    $this->clanok = $clanok;
   
    $hlm = $this->clanok->hlavne_menu; // Pre skratenie zapisu
    
    // Test opravnenia: Si vlastnik alebo admin? Ak nie, povolil vlastnik editaciu? A mám dostatocne id reistracie?
    $vlastnik = $this->user->isInRole('admin') ? true : $this->user->getIdentity()->id == $hlm->id_user_main;
    $opravnenie = $vlastnik || (boolean)($hlm->id_hlavne_menu_opravnenie & 2);
    $this->admin_links = [
      "alink" => $opravnenie && $this->user->isAllowed($name, 'add'),
      "elink" => $opravnenie && $this->user->isAllowed($name, 'edit'),
      "dlink" => $opravnenie && $this->user->isAllowed($name, 'del'),
      "vlastnik" => $vlastnik,
    ];
    return $this;
  }
  
  /** 
   * Render */
	public function render(): void {
    $this->template->id_hlavne_menu = $this->clanok->id_hlavne_menu;
    $this->template->admin_links = $this->admin_links;
    $this->template->addFilter('to_json', function ($value) {
      return Json::encode($value);
    }); 
		$this->template->render(__DIR__ . '/Products.latte');
	}
  
  /**
   * Grid pre produkty
   * @param string $name
   * @return void */
  /*public function createComponentProductsGrid(string $name): void {
		$grid = new DataGrid($this, $name);

		$grid->setDataSource($this->products->findBy(['id_hlavne_menu'=>$this->clanok->id_hlavne_menu]));
    $grid->addColumnText('main_file2', 'Obrázok', 'main_file')
          ->setRenderer(function($item){
            return Html::el('button', ['class' => 'btn btn-link btn-for-big-image'])
                            ->data('toogle', 'modal')
                            ->data('target', '#imageModalCenterProduct')
                            ->data('imgsrc', $item->main_file)
                            ->data('imgname', $item->name)
                            ->setHtml(Html::el('img', ['class' => 'img-thumbnail'])->src($this->template->basePath.'/'.$item->thumb_file)->alt($item->name));      
          });
    $grid->addColumnText('name', 'Názov')
          ->setEditableCallback(function($id, $value) {
            $this->products->repair($id, ['name'=>$value]);
          });
    $grid->addColumnText('description', 'Popis')
          ->setEditableCallback(function($id, $value) {
            $this->products->repair($id, ['description'=>$value]);
          });
    if ($this->admin_links['dlink']) {
      $grid->addAction('delete', '', 'confirmedDelete!')
            ->setIcon('trash-alt fa-2x')
            ->setClass('btn btn-danger btn-sm ajax')
            ->setTitle('Vymazanie položky')
            ->setConfirmation(
              new Confirmation\StringConfirmation('Naozaj chceš zmazať položku %s?', 'name')
            );
    }
    $grid->setTranslator(new SimpleTranslator($this->texts_for_translator));
	}*/
  
  /**
   * Signal na editaciu
   * @param int $id Id polozky na editaciu */
  /*public function handleEdit(int $id): void {
    $this->presenter->redirect('Products:edit', $id);
  }*/

  /**
   * Signal pre zobrazenie velkeho nahladu obrazka
   * @param int $id_big_image 
   * @return void */
  /*public function handleBigImg(int $id_big_image): void {
    $this->big_img = $this->products->find($id_big_image);
    if ($this->presenter->isAjax()) {
      $this->redrawControl('lightbox-image');
    }
  }*/
  
  /** 
   * Spracovanie signálu vymazavania
	 * @param int $id Id polozky */
	/*function handleConfirmedDelete(int $id): void	{
    if ($this->products->remove($id)) { 
      $this->flashMessage('Produkt bol vymazaný!', 'success'); 
    } else { 
      $this->flashMessage('Došlo k chybe a produkt nebol vymazaný!', 'danger'); 
    }
    if (!$this->presenter->isAjax()) {
      $this->presenter->redirect('this', ['tab'=>'products-tab']);
    } else {
      $this->redrawControl('flashes');
      $this->redrawControl('products');
      $this['productsGrid']->reload();
    }
  }*/
}

interface IProductsControl {
  /** @return ProductsControl */
  function create();
}