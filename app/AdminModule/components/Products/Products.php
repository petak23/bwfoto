<?php
namespace App\AdminModule\Components\Products;

use DbTable;
use Nette;
use Nette\Security\User;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Localization\SimpleTranslator;

/**
 * Komponenta pre spravu produktov clanku.
 * 
 * Posledna zmena(last change): 26.07.2018
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com> 
 * @copyright Copyright (c) 2012 - 2018 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.3
 */

class ProductsControl extends Nette\Application\UI\Control {

  /** @var DbTable\Products */
  public $products;
  /** @var string $nazov_stranky */
  private $nazov_stranky;
  /** @var Nette\Database\Table\ActiveRow $clanok Info o clanku */
  private $clanok;
  /** @var int */
  private $upload_size;
  /** @var string */
  private $nastavenie;
  /** &var EditProductFormFactory */
  public $editProductForm;
  /** &var AddMultiProductsFormFactory */
  public $addMultiProductsForm;
  /** @var array */
  private $admin_links;
  /** @var Nette\Security\User */
  private $user;
  /** @var DbTable\Hlavne_menu */
  private $hlavne_menu;
  
  /** @var ZmenOkrajFormFactory */
	public $zmenOkraj;

  /**
   * @param DbTable\Hlavne_menu $hlavne_menu
   * @param DbTable\Products $products
   * @param \App\AdminModule\Forms\Products\AddMultiProductsFormFactory $addMultiProductsFormFactory
   * @param \App\AdminModule\Forms\Products\EditProoductFormFactory $editProductFormFactory
   * @param \App\AdminModule\Components\Products\ZmenOkrajFormFactory $zmenOkrajFormFactory
   * @param User $user */
  public function __construct(DbTable\Hlavne_menu $hlavne_menu,
                              DbTable\Products $products, 
                              \App\AdminModule\Forms\Products\AddMultiProductsFormFactory $addMultiProductsFormFactory,
                              \App\AdminModule\Forms\Products\EditProoductFormFactory $editProductFormFactory, 
                              ZmenOkrajFormFactory $zmenOkrajFormFactory,
                              User $user) {
    parent::__construct();
    $this->hlavne_menu = $hlavne_menu;
    $this->products = $products;
    $this->addMultiProductsForm = $addMultiProductsFormFactory;
    $this->editProductForm = $editProductFormFactory;
    $this->user = $user;
    $this->zmenOkraj = $zmenOkrajFormFactory;
  }
  
  /** 
   * Nastavenie komponenty
   * @param Nette\Database\Table\ActiveRow $clanok
   * @param type $nazov_stranky
   * @param type $upload_size
   * @param type $nastanenie
   * @param type $name
   * @return \App\AdminModule\Products\ProductsControl */
  public function setTitle(Nette\Database\Table\ActiveRow $clanok, $nazov_stranky, $nastanenie, $name) {
    $this->clanok = $clanok;
    $this->nazov_stranky = $nazov_stranky;
    $ini_v = trim(ini_get("upload_max_filesize"));
    $s = ['g'=> 1<<30, 'm' => 1<<20, 'k' => 1<<10];
    $this->upload_size =  intval($ini_v) * ($s[strtolower(substr($ini_v,-1))] ?: 1);
    $this->nastavenie = $nastanenie;
    
    $hlm = $this->clanok->hlavne_menu; // Pre skratenie zapisu
    $vlastnik = $this->user->isInRole('admin') ? TRUE : $this->user->getIdentity()->id == $hlm->id_user_main;//$this->vlastnik($hlm->id_user_main);
    // Test opravnenia na pridanie podclanku: Si admin? Ak nie, si vlastnik? Ak nie, povolil vlastnik pridanie, editaciu? A mám dostatocne id reistracie?
    $opravnenie_add = $vlastnik ? TRUE : (boolean)($hlm->id_hlavne_menu_opravnenie & 1);
    $opravnenie_edit = $vlastnik ? TRUE : (boolean)($hlm->id_hlavne_menu_opravnenie & 2);
    $opravnenie_del = $vlastnik ? TRUE : (boolean)($hlm->id_hlavne_menu_opravnenie & 4);
    // Test pre pridanie a odkaz: 0 - nemám oprávnenie; 1 - odkaz bude na addpol; 2 - odkaz bude na Clanky:add
    $druh_opravnenia = $opravnenie_add ? ($this->user->isAllowed($name, 'addpol') ? 1 : $this->user->isAllowed($this->name, 'add') ? 2 : 0) : 0;
    $this->admin_links = [
      "alink" => ["druh_opravnenia" => $druh_opravnenia,
                  "link"    => $druh_opravnenia ? ($druh_opravnenia == 1 ? ['main'=>$this->presenter->name.':addpol']
                                                                         : ['main'=>'Clanky:add', 'uroven'=>$hlm->uroven+1]) : NULL,
                  "text"    => "Pridaj produkt"
                 ],
      "elink" => $opravnenie_edit && $this->user->isAllowed($name, 'edit'),
      "dlink" => $opravnenie_del && $this->user->isAllowed($name, 'del') && !$this->hlavne_menu->maPodradenu($this->clanok->id_hlavne_menu),
      "vlastnik" => $vlastnik,
    ];
    return $this;
  }
  
  /** 
   * Render */
	public function render() {
    $this->template->setFile(__DIR__ . '/Products.latte');
    $this->template->clanok = $this->clanok;
    $this->template->admin_links_prilohy = $this->admin_links;
    $this->template->dir_to_images = $this->nastavenie["dir_to_images"];
		$this->template->render();
	}
  
  public function createComponentProductsGrid($name) {
		$grid = new DataGrid($this, $name);

		$grid->setDataSource($this->products->findBy(['id_hlavne_menu'=>$this->clanok->id_hlavne_menu]));
    $grid->addColumnText('main_file', 'Obrázok')
         ->setTemplate(__DIR__ . '/grid.subor.latte',['dir_to_product' => $this->nastavenie['dir_to_products']]);
    $grid->addColumnText('name', 'Názov')
         ->setEditableCallback(function($id, $value) {
           $this->products->oprav($id, ['name'=>$value]);
         });
    $grid->addColumnText('description', 'Popis')
         ->setEditableCallback(function($id, $value) {
           $this->products->oprav($id, ['description'=>$value]);
         });
    if ($this->admin_links['elink']) {
      $grid->addAction('edit', '')
           ->setIcon('pencil-alt fa-2x')
           ->setClass('btn btn-success btn-sm')
           ->setTitle('Editácia položky');
      $grid->addAction('delete', '', 'confirmedDelete!')
           ->setIcon('trash-alt fa-2x')
           ->setClass('btn btn-danger btn-sm ajax')
           ->setTitle('Vymazanie položky')
           ->setConfirm('Naozaj chceš zmazať položku %s?', 'name');
    }
    
    $translator = new SimpleTranslator([
      'ublaboo_datagrid.no_item_found_reset' => 'Žiadné položky neboli nájdené. Filter môžete vynulovať...',
      'ublaboo_datagrid.no_item_found' => 'Žiadné položky neboli nájdené.',
      'ublaboo_datagrid.here' => 'tu',
      'ublaboo_datagrid.items' => 'Položky',
      'ublaboo_datagrid.all' => 'všetky',
      'ublaboo_datagrid.from' => 'z',
      'ublaboo_datagrid.reset_filter' => 'Resetovať filter',
      'ublaboo_datagrid.group_actions' => 'Hromadné akcie',
      'ublaboo_datagrid.show_all_columns' => 'Zobraziť všetky stĺpce',
      'ublaboo_datagrid.hide_column' => 'Skryť stĺpec',
      'ublaboo_datagrid.action' => 'Akcia',
      'ublaboo_datagrid.previous' => 'Predošlá',
      'ublaboo_datagrid.next' => 'Daľšia',
      'ublaboo_datagrid.choose' => 'Vyberte',
      'ublaboo_datagrid.execute' => 'Vykonať',
      'ublaboo_datagrid.short' => 'Usporiadaj',
    ]);
    $grid->setTranslator($translator);
	}
  
  /**
   * Signal na editaciu
   * @param int $id Id polozky na editaciu */
  public function handleEdit($id) {
    $this->presenter->redirect('Products:edit', $id);
  }
  
  /** 
   * Komponenta formulara pre pridanie a editaciu produktu polozky.
   * @return Nette\Application\UI\Form */
  public function createComponentEditProductForm() {
    $form = $this->editProductForm->create($this->upload_size, $this->nastavenie["dir_to_products"]);
    $form->setDefaults(["id"=>0, "id_hlavne_menu"=>$this->clanok->id_hlavne_menu, "id_user_roles"=>$this->clanok->hlavne_menu->id_user_roles]);
    $form['uloz']->onClick[] = function ($button) { 
      $this->presenter->flashOut(!count($button->getForm()->errors), ['this',['tab'=>'products-tab']], 'Produkt bol úspešne uložený!', 'Došlo k chybe a zmena sa neuložila. Skúste neskôr znovu...');
		};
    return $this->presenter->_vzhladForm($form);
  }
  
  /** 
   * Komponenta formulara pre pridanie viacerich produktov polozky.
   * @return Nette\Application\UI\Form */
  public function createComponentAddMultiProductsForm() {
    $form = $this->addMultiProductsForm->create($this->upload_size, $this->nastavenie["dir_to_products"]);
    $form->setDefaults(["id"=>0, "id_hlavne_menu"=>$this->clanok->id_hlavne_menu, "id_user_roles"=>$this->clanok->hlavne_menu->id_user_roles]);
    $form['uloz']->onClick[] = function ($button) { 
      $this->presenter->flashOut(!count($button->getForm()->errors), ['this',['tab'=>'products-tab']], 'Produkty boli úspešne uložené!', 'Došlo k chybe a zmena sa neuložila. Skúste neskôr znovu...');
		};
    return $this->presenter->_vzhladForm($form);
  }
    
  /** 
   * @param Nette\Application\UI\Form $form
   * @return Nette\Application\UI\Form */
  protected function _formMessage($form) {
    $form['uloz']->onClick[] = function ($button) { 
      $this->presenter->flashOut(!count($button->getForm()->errors), 'this', 'Zmena bola úspešne uložená!', 'Došlo k chybe a zmena sa neuložila. Skúste neskôr znovu...');
		};
    return $this->presenter->_vzhladForm($form);
  }
  
  /** 
   * Komponenta formulara pre zmenu okraja obrázkových príloh polozky.
   * @return Nette\Application\UI\Form */
  public function createComponentZmenOkrajForm() {
    return $this->_formMessage($this->zmenOkraj->create($this->clanok->hlavne_menu));
  }
  
  /** 
   * Spracovanie signálu vymazavania
	 * @param int $id Id polozky */
	function handleConfirmedDelete($id)	{
    $pr = $this->products->find($id);//najdenie prislusnej polozky menu, ku ktorej priloha patri
    $pthis = $this->presenter;
    if ($pr !== FALSE) {
      $vysledok = $this->_vymazSubor($pr->main_file) ? $this->_vymazSubor($pr->thumb_file) : FALSE;
      if (($vysledok ? $pr->delete() : FALSE)) { 
        $this->flashMessage('Produkt bol vymazaný!', 'success'); 
      } else { 
        $this->flashMessage('Došlo k chybe a produkt nebol vymazaný!', 'danger'); 
      }
    } else { $this->flashMessage('Došlo k chybe a produkt nebol vymazaný!', 'danger');}
    if (!$pthis->isAjax()) {
      $pthis->redirect('this', ['tab'=>'products-tab']);
    } else {
      $this->redrawControl('flashes');
      $this->redrawControl('products');
      $this['productsGrid']->reload();
    }
  }
  
  /** 
   * Funkcia vymaze subor ak exzistuje
	 * @param string $subor Nazov suboru aj srelativnou cestou
	 * @return int Ak zmaze alebo neexistuje(nie je co mazat) tak 1 inak 0 */
	private function _vymazSubor($subor) {
		return (is_file($subor)) ? unlink($this->presenter->context->parameters["wwwDir"]."/".$subor) : -1;
	}
  
  protected function createTemplate($class = NULL) {
    $template = parent::createTemplate($class);
    $template->addFilter('border_x', function ($text){
      $pom = $text != null & strlen($text)>2 ? explode("|", $text) : ['#000000','0'];
      $xs = 'style="border: '.$pom[1].'px solid '.(strlen($pom[0])>2 ? $pom[0]:'inherit').'"';
      return $xs;
    });
    return $template;
	}
}

interface IProductsControl {
  /** @return ProductsControl */
  function create();
}