<?php
namespace App\AdminModule\Components\Clanky\PrilohyClanok;

use DbTable;
use Nette;
use Nette\Security\User;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Localization\SimpleTranslator;

/**
 * Komponenta pre spravu priloh clanku.
 * 
 * Posledna zmena(last change): 15.01.2018
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com> 
 * @copyright Copyright (c) 2012 - 2018 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.7
 */

class PrilohyClanokControl extends Nette\Application\UI\Control {

  /** @var DbTable\Dokumenty $clanok Info o clanku */
  public $dokumenty;
  /** @var string $nazov_stranky */
  private $nazov_stranky;
  /** @var Nette\Database\Table\ActiveRow $clanok Info o clanku */
  private $clanok;
  /** @var int */
  private $upload_size;
  /** @var string */
  private $prilohy_adresar;
  /** @var array */
  private $prilohy_images;
  /** &var EditPrilohyFormFactory */
  public $editPrilohyForm;
  /** &var AddMultiPrilohyFormFactory */
  public $addMultiPrilohyForm;
  /** @var array */
  private $admin_links;
  /** @var Nette\Security\User */
  private $user;
  /** @var DbTable\Hlavne_menu */
  private $hlavne_menu;

  /**
   * @param DbTable\Dokumenty $dokumenty
   * @param EditPrilohyFormFactory $editPrilohyFormFactory */
  public function __construct(DbTable\Dokumenty $dokumenty, EditPrilohyFormFactory $editPrilohyFormFactory, AddMultiPrilohyFormFactory $addMultiPrilohyFormFactory, User $user, DbTable\Hlavne_menu $hlavne_menu) {
    parent::__construct();
    $this->dokumenty = $dokumenty;
    $this->editPrilohyForm = $editPrilohyFormFactory;
    $this->addMultiPrilohyForm = $addMultiPrilohyFormFactory;
    $this->user = $user;
    $this->hlavne_menu = $hlavne_menu;
  }
  
  /** 
   * Nastavenie komponenty
   * @param Nette\Database\Table\ActiveRow $clanok
   * @param string $nazov_stranky
   * @param int $upload_size
   * @param string $prilohy_adresar
   * @param array $prilohy_images Nastavenie obrazkov pre prilohy
   * @return \App\AdminModule\Components\Clanky\PrilohyClanok\PrilohyClanokControl */
  public function setTitle(Nette\Database\Table\ActiveRow $clanok, $nazov_stranky, $upload_size, $prilohy_adresar, $prilohy_images, $name) {
    $this->clanok = $clanok;
    $this->nazov_stranky = $nazov_stranky;
    $this->upload_size = $upload_size;
    $this->prilohy_adresar = $prilohy_adresar;
    $this->prilohy_images = $prilohy_images;
    
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
                  "text"    => "Pridaj podčlánok"
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
    $this->template->setFile(__DIR__ . '/PrilohyClanok.latte');
    $this->template->clanok = $this->clanok;
    $this->template->admin_links_prilohy = $this->admin_links;
		$this->template->render();
	}
  
  public function createComponentPrilohyGrid($name) {
		$grid = new DataGrid($this, $name);
//    dump($grid);die();
//    $grid->icon_prefix = 'fas fa-';
		$grid->setDataSource($this->dokumenty->findBy(['id_hlavne_menu'=>$this->clanok->id_hlavne_menu]));
    $grid->addColumnText('znacka', 'Značka');
    $grid->addColumnText('subor', 'Súbor')
         ->setTemplate(__DIR__ . '/grid.subor.latte');
    $grid->addColumnText('name', 'Názov')
         ->setEditableCallback(function($id, $value) {
           $this->dokumenty->oprav($id, ['name'=>$value]);
         });
    $grid->addColumnText('description', 'Popis')
         ->setEditableCallback(function($id, $value) {
           $this->dokumenty->oprav($id, ['description'=>$value]);
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
           ->setConfirm('Naozaj chceš zmazať položku %s?', 'nazov');
      $grid->addAction('showInText', '')
           ->setIcon('adjust fa-2x')
           ->setClass(function($item) { 
                        $pr = strtolower($item->pripona);
                        return ($pr == 'jpg' OR $pr == 'png' OR $pr == 'gif' OR $pr == 'bmp') ? ("btn ".($item->zobraz_v_texte ? 'btn-success' : 'btn-warning')." btn-sm ajax") : 'display-none' ; 
                      })
           ->setTitle('Nezobraz obrázok v prílohách');
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
    $this->presenter->redirect('Dokumenty:edit', $id);
  }
  
  /** 
   * Komponenta formulara pre pridanie a editaciu prílohy polozky.
   * @return Nette\Application\UI\Form */
  public function createComponentEditPrilohyForm() {
    $form = $this->editPrilohyForm->create($this->upload_size, $this->prilohy_adresar, $this->prilohy_images);
    $form->setDefaults(["id"=>0, "id_hlavne_menu"=>$this->clanok->id_hlavne_menu, "id_user_roles"=>$this->clanok->hlavne_menu->id_user_roles]);
    $form['uloz']->onClick[] = function ($button) { 
      $this->presenter->flashOut(!count($button->getForm()->errors), ['this',['tab'=>'prilohy-tab']], 'Príloha bola úspešne uložená!', 'Došlo k chybe a zmena sa neuložila. Skúste neskôr znovu...');
		};
    return $this->presenter->_vzhladForm($form);
  }
  
  /** 
   * Komponenta formulara pre pridanie viacerich prílohy polozky.
   * @return Nette\Application\UI\Form */
  public function createComponentAddMultiPrilohyForm() {
    $form = $this->addMultiPrilohyForm->create($this->upload_size, $this->prilohy_adresar, $this->prilohy_images);
    $form->setDefaults(["id"=>0, "id_hlavne_menu"=>$this->clanok->id_hlavne_menu, "id_user_roles"=>$this->clanok->hlavne_menu->id_user_roles]);
    $form['uloz']->onClick[] = function ($button) { 
      $this->presenter->flashOut(!count($button->getForm()->errors), ['this',['tab'=>'prilohy-tab']], 'Prílohy boli úspešne uložené!', 'Došlo k chybe a zmena sa neuložila. Skúste neskôr znovu...');
		};
    return $this->presenter->_vzhladForm($form);
  }
  
  public function handleShowInText($id) {
    $priloha = $this->dokumenty->find($id);
    $priloha->update(['zobraz_v_texte'=>(1 - $priloha->zobraz_v_texte)]);
		if (!$this->presenter->isAjax()) {
      $this->redirect('this');
    } else {
      $this->redrawControl('flashes');
      $this->redrawControl('prilohy-in');
    }
  }
  
  /** 
   * Signal vymazavania
	 * @param int $id Id polozky na zmazanie */
	function handleConfirmedDelete($id)	{
    $pr = $this->dokumenty->find($id);//najdenie prislusnej polozky menu, ku ktorej priloha patri
    $pthis = $this->presenter;
    if ($pr !== FALSE) {
      $vysledok = $this->_vymazSubor($pr->subor) ? (in_array(strtolower($pr->pripona), ['png', 'gif', 'jpg']) ? $this->_vymazSubor($pr->thumb) : TRUE) : FALSE;
      if (($vysledok ? $pr->delete() : FALSE)) { 
        $this->flashMessage('Príloha bola vymazaná!', 'success'); 
      } else { 
        $this->flashMessage('Došlo k chybe a príloha nebola vymazaná!', 'danger'); 
      }
    } else { $this->flashMessage('Došlo k chybe a príloha nebola vymazaná!', 'danger');}
    if (!$pthis->isAjax()) {
      $this->redirect('this');
    } else {
      $this->redrawControl('flashes');
      $this->redrawControl('prilohy');
      $this['prilohyGrid']->reload();
    }
  }
  
  /** 
   * Funkcia vymaze subor ak exzistuje
	 * @param string $subor Nazov suboru aj srelativnou cestou
	 * @return int Ak zmaze alebo neexistuje(nie je co mazat) tak 1 inak 0 */
	private function _vymazSubor($subor) {
		return (is_file($subor)) ? unlink($this->presenter->context->parameters["wwwDir"]."/".$subor) : -1;
	}
}

interface IPrilohyClanokControl {
  /** @return PrilohyClanokControl */
  function create();
}