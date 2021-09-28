<?php

declare(strict_types=1);

namespace App\AdminModule\Components\Products;

use DbTable;
use Nette;
use Nette\Security\User;
use Nette\Utils\Html;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Column\Action\Confirmation;
use Ublaboo\DataGrid\Localization\SimpleTranslator;

/**
 * Komponenta pre spravu produktov clanku.
 * 
 * Posledna zmena(last change): 29.09.2021
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com> 
 * @copyright Copyright (c) 2012 - 2021 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.1.5
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
	//public $zmenOkraj;

  /** @var mixed */
  protected $big_img;

  /** @var mixed */
  protected $httpRequest; 
  /** @var array */
  private $texts_for_translator;
  /** @var string */
  private $wwwDir;
  /** @var string */
  private $dir_to_products;

  /**
   * @param array $texts_for_translator
   * @param string $wwwDir 
   * @param DbTable\Hlavne_menu $hlavne_menu
   * @param DbTable\Products $products
   * @param \App\AdminModule\Forms\Products\AddMultiProductsFormFactory $addMultiProductsFormFactory
   * @param \App\AdminModule\Forms\Products\EditProoductFormFactory $editProductFormFactory
   * @param \App\AdminModule\Components\Products\ZmenOkrajFormFactory $zmenOkrajFormFactory
   * @param User $user
   * @param Nette\Http\Request $request */
  public function __construct(array $texts_for_translator,
                              string $wwwDir,
                              string $dir_to_products,
                              DbTable\Hlavne_menu $hlavne_menu,
                              DbTable\Products $products, 
                              \App\AdminModule\Forms\Products\AddMultiProductsFormFactory $addMultiProductsFormFactory,
                              \App\AdminModule\Forms\Products\EditProoductFormFactory $editProductFormFactory, 
                              //ZmenOkrajFormFactory $zmenOkrajFormFactory,
                              User $user,
                              Nette\Http\Request $request
                              ) {
    $this->texts_for_translator = $texts_for_translator;
    $this->wwwDir = $wwwDir;
    $this->dir_to_products = $dir_to_products;
    $this->hlavne_menu = $hlavne_menu;
    $this->products = $products;
    $this->addMultiProductsForm = $addMultiProductsFormFactory;
    $this->editProductForm = $editProductFormFactory;
    $this->user = $user;
    //$this->zmenOkraj = $zmenOkrajFormFactory;
    $this->httpRequest = $request;
  }
  
  /** 
   * Nastavenie komponenty
   * @param Nette\Database\Table\ActiveRow $clanok
   * @param string $nazov_stranky
   * @param string $name
   * @return \App\AdminModule\Products\ProductsControl */
  public function setTitle(Nette\Database\Table\ActiveRow $clanok, string $nazov_stranky,string $name): self {
    $this->clanok = $clanok;
    $this->nazov_stranky = $nazov_stranky;
    $ini_v = trim(ini_get("upload_max_filesize"));
    $s = ['g'=> 1<<30, 'm' => 1<<20, 'k' => 1<<10];
    $this->upload_size =  intval($ini_v) * ($s[strtolower(substr($ini_v,-1))] ?: 1);
    
    $hlm = $this->clanok->hlavne_menu; // Pre skratenie zapisu
    $vlastnik = $this->user->isInRole('admin') ? TRUE : $this->user->getIdentity()->id == $hlm->id_user_main;//$this->vlastnik($hlm->id_user_main);
    // Test opravnenia na pridanie podclanku: Si admin? Ak nie, si vlastnik? Ak nie, povolil vlastnik pridanie, editaciu? A mám dostatocne id reistracie?
    $opravnenie_add = $vlastnik ? TRUE : (boolean)($hlm->id_hlavne_menu_opravnenie & 1);
    $opravnenie_edit = $vlastnik ? TRUE : (boolean)($hlm->id_hlavne_menu_opravnenie & 2);
    $opravnenie_del = $vlastnik ? TRUE : (boolean)($hlm->id_hlavne_menu_opravnenie & 4);
    // Test pre pridanie a odkaz: 0 - nemám oprávnenie; 1 - odkaz bude na addpol; 2 - odkaz bude na Clanky:add
    $druh_opravnenia = $opravnenie_add ? ($this->user->isAllowed($name, 'addpol') ? 1 : ($this->user->isAllowed($this->name, 'add') ? 2 : 0)) : 0;
    $this->admin_links = [
      "alink" => ["druh_opravnenia" => $druh_opravnenia,
                  "link"    => $druh_opravnenia ? ($druh_opravnenia == 1 ? ['main'=>$name.':addpol']
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
	public function render(): void {
    //$this->template->setFile(__DIR__ . '/Products.latte');
    $this->template->clanok = $this->clanok;
    $this->template->admin_links_prilohy = $this->admin_links;
    $this->template->big_img = $this->big_img;
    $this->template->addFilter('border_x', function ($text){
      $pom = $text != null && strlen($text)>2 ? explode("|", $text) : ['#000000','0'];
      $xs = 'style="border: '.$pom[1].'px solid '.(strlen($pom[0])>2 ? $pom[0]:'inherit').'"';
      return $xs;
    });
    $this->products->setWwwDir($this->wwwDir."/");
		$this->template->render(__DIR__ . '/Products.latte');
	}
  
  /**
   * Grid pre produkty
   * @param string $name
   * @return void */
  public function createComponentProductsGrid(string $name): void {
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
            $this->products->oprav($id, ['name'=>$value]);
          });
    $grid->addColumnText('description', 'Popis')
          ->setEditableCallback(function($id, $value) {
            $this->products->oprav($id, ['description'=>$value]);
          });
    if ($this->admin_links['elink']) {
      $service = $this;
      $grid->addActionCallback('edit', '')
            ->setIcon('pencil-alt fa-2x')
            ->setClass('btn btn-success btn-sm')
            ->setTitle('Editácia položky')
            ->onClick[] = function($item_id) use ($service) {
              $service->presenter->redirect('Products:edit', ['id'=>$item_id, 'id_hlavne_menu' => $service->clanok->id_hlavne_menu]);
            };
      $grid->addAction('delete', '', 'confirmedDelete!')
            ->setIcon('trash-alt fa-2x')
            ->setClass('btn btn-danger btn-sm ajax')
            ->setTitle('Vymazanie položky')
            ->setConfirmation(
              new Confirmation\StringConfirmation('Naozaj chceš zmazať položku %s?', 'name')
            );
    }
    $grid->setTranslator(new SimpleTranslator($this->texts_for_translator));
	}
  
  /**
   * Signal na editaciu
   * @param int $id Id polozky na editaciu */
  public function handleEdit(int $id): void {
    $this->presenter->redirect('Products:edit', $id);
  }

  /**
   * Signal pre zobrazenie velkeho nahladu obrazka
   * @param int $id_big_image 
   * @return void */
  public function handleBigImg(int $id_big_image): void {
    $this->big_img = $this->products->find($id_big_image);
    if ($this->httpRequest->isAjax()) {
      $this->redrawControl('lightbox-image');
    }
  }
  
  /** 
   * Komponenta formulara pre pridanie a editaciu produktu polozky.
   * @return Nette\Application\UI\Form */
  public function createComponentEditProductForm(): Nette\Application\UI\Form {
    $form = $this->editProductForm->create($this->dir_to_products);
    $form->setDefaults(["id"=>0, "id_hlavne_menu"=>$this->clanok->id_hlavne_menu, "id_user_roles"=>$this->clanok->hlavne_menu->id_user_roles]);
    $form['uloz']->onClick[] = function ($button) { 
      $this->presenter->flashOut(!count($button->getForm()->errors), ['this',['tab'=>'products-tab']], 'Produkt bol úspešne uložený!', 'Došlo k chybe a zmena sa neuložila. Skúste neskôr znovu...');
		};
    return $this->makeBootstrap4($form);
  }
  
  /** 
   * Komponenta formulara pre pridanie viacerich produktov polozky.
   * @return Nette\Application\UI\Form */
  public function createComponentAddMultiProductsForm(): Nette\Application\UI\Form {
    $form = $this->addMultiProductsForm
                  ->create($this->dir_to_products, $this->wwwDir."/", $this->clanok);
    $form->setDefaults(["id"=>0, "id_hlavne_menu"=>$this->clanok->id_hlavne_menu, "id_user_roles"=>$this->clanok->hlavne_menu->id_user_roles]);
    $form['ulozz']->onClick[] = function (/*$button*/) { 
//      $this->presenter->flashOut(!count($button->getForm()->errors), ['this',['tab'=>'products-tab']], 'Produkty boli úspešne uložené!', 'Došlo k chybe a zmena sa neuložila. Skúste neskôr znovu...');
      $this->presenter->redirect('this',['tab'=>'products-tab']);
		};
    $form['ulozk']->onClick[] = function (/*$button*/) { 
//      $this->presenter->flashOut(!count($button->getForm()->errors), ['this',['tab'=>'products-tab']], 'Produkty boli úspešne uložené!', 'Došlo k chybe a zmena sa neuložila. Skúste neskôr znovu...');
      $this->presenter->redirect('this',['tab'=>'products-tab']);
		};
    return $this->makeBootstrap4($form);
  }
  
  /** 
   * Komponenta formulara pre zmenu okraja obrázkových príloh polozky.
   * @return Nette\Application\UI\Form */
  /*public function createComponentZmenOkrajForm(): Nette\Application\UI\Form {
    $form = $this->zmenOkraj->create($this->clanok->hlavne_menu);
    $form['uloz']->onClick[] = function ($button) { 
      $this->presenter->flashOut(!count($button->getForm()->errors), 
                                  'this', 
                                  'Zmena bola úspešne uložená!', 
                                  'Došlo k chybe a zmena sa neuložila. Skúste neskôr znovu...');
		};
    return $this->makeBootstrap4($form); 
  }*/
  
  /** 
   * Spracovanie signálu vymazavania
	 * @param int $id Id polozky */
	function handleConfirmedDelete(int $id): void	{
    $pr = $this->products->find($id);//najdenie prislusnej polozky menu, ku ktorej priloha patri
    $pthis = $this->presenter;
    if ($pr !== null) {
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
	 * @return bool Ak zmaze alebo neexistuje(nie je co mazat) tak true inak false */
	private function _vymazSubor(string $subor): bool {
		return (is_file($subor)) ? unlink($this->wwwDir."/".$subor) : true;
	}
  
  /**
   * Vzhlad formularov v style Bootstrap 4
   * @param Nette\Application\UI\Form $form
   * @return \Nette\Application\UI\Form */
  function makeBootstrap4(Nette\Application\UI\Form $form): Nette\Application\UI\Form {
    $renderer = $form->getRenderer();
    $renderer->wrappers['controls']['container'] = null;
    $renderer->wrappers['pair']['container'] = 'div class="form-group row"';
    $renderer->wrappers['pair']['.error'] = 'has-danger';
    $renderer->wrappers['control']['container'] = 'div class=col-sm-9';
    $renderer->wrappers['label']['container'] = 'div class="col-sm-3 col-form-label"';
    $renderer->wrappers['control']['description'] = 'div class="help-block alert alert-info"';
    $renderer->wrappers['control']['errorcontainer'] = 'span class=form-control-feedback';
    $renderer->wrappers['control']['.error'] = 'is-invalid';

    foreach ($form->getControls() as $control) {
      $type = $control->getOption('type');
      if ($type === 'button') {
        $control->getControlPrototype()->addClass(empty($usedPrimary) ? 'btn btn-primary' : 'btn btn-secondary');
        $usedPrimary = true;

      } elseif (in_array($type, ['text', 'textarea', 'select'], true)) {
        $control->getControlPrototype()->addClass('form-control');

      } elseif ($type === 'file') {
        $control->getControlPrototype()->addClass('form-control-file');

      } elseif (in_array($type, ['checkbox', 'radio'], true)) {
        if ($control instanceof Nette\Forms\Controls\Checkbox) {
          $control->getLabelPrototype()->addClass('form-check-label');
        } else {
          $control->getItemLabelPrototype()->addClass('form-check-label');
        }
        $control->getControlPrototype()->addClass('form-check-input');
        $control->getSeparatorPrototype()->setName('div')->addClass('form-check');
      }
    }
    return $form;
  }
}

interface IProductsControl {
  /** @return ProductsControl */
  function create();
}