<?php

declare(strict_types=1);

namespace App\AdminModule\Components\Clanky\PrilohyClanok;

use DbTable;
use Nette;
use Nette\Security\User;
//use Nette\Utils\Html;

/**
 * Komponenta pre spravu priloh clanku.
 * 
 * Posledna zmena(last change): 03.01.2023
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com> 
 * @copyright Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.1.6
 */
class PrilohyClanokAControl extends Nette\Application\UI\Control
{

  /** @var DbTable\Dokumenty $clanok Info o clanku */
  public $dokumenty;
  /** @var string $nazov_stranky */
  //private $nazov_stranky;
  /** @var Nette\Database\Table\ActiveRow $clanok Info o clanku */
  private $clanok;
  /** @var array */
  //private $prilohy_images;
  /** @var string */
  //private $wwwDir;

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

  /** @var mixed */
  protected $big_img;
  /** @var string */
  private $prilohy_adresar;

  /**
   * @param array $prilohy_images Nastavenie obrazkov pre prilohy - Nastavenie priamo cez servises.neon
   * @param DbTable\Dokumenty $dokumenty
   * @param DbTable\Hlavne_menu $hlavne_menu
   * @param EditPrilohyFormFactory $editPrilohyFormFactory
   * @param AddMultiPrilohyFormFactory $addMultiPrilohyFormFactory
   * @param User $user */
  public function __construct(
    array $prilohy_images,
    string $wwwDir,
    string $prilohy_adresar,
    DbTable\Dokumenty $dokumenty,
    DbTable\Hlavne_menu $hlavne_menu,
    EditPrilohyFormFactory $editPrilohyFormFactory,
    //AddMultiPrilohyFormFactory $addMultiPrilohyFormFactory, 
    User $user
  ) {
    $this->dokumenty = $dokumenty;
    $this->editPrilohyForm = $editPrilohyFormFactory;
    //$this->addMultiPrilohyForm = $addMultiPrilohyFormFactory;
    $this->user = $user;
    $this->hlavne_menu = $hlavne_menu;
    //$this->prilohy_images = $prilohy_images;
    $this->prilohy_adresar = $prilohy_adresar;
    //$this->wwwDir = $wwwDir;
  }

  /** 
   * Nastavenie komponenty */
  public function setTitle(Nette\Database\Table\ActiveRow $clanok, string $nazov_stranky, string $name): PrilohyClanokAControl
  {
    $this->clanok = $clanok;
    //$this->nazov_stranky = $nazov_stranky;

    $hlm = $this->clanok->hlavne_menu; // Pre skratenie zapisu
    $vlastnik = $this->user->isInRole('admin') ? TRUE : $this->user->getIdentity()->id == $hlm->id_user_main; //$this->vlastnik($hlm->id_user_main);
    // Test opravnenia na pridanie podclanku: Si admin? Ak nie, si vlastnik? Ak nie, povolil vlastnik pridanie, editaciu? A mám dostatocne id reistracie?
    $opravnenie_add = $vlastnik ? TRUE : (bool)($hlm->id_hlavne_menu_opravnenie & 1);
    $opravnenie_edit = $vlastnik ? TRUE : (bool)($hlm->id_hlavne_menu_opravnenie & 2);
    $opravnenie_del = $vlastnik ? TRUE : (bool)($hlm->id_hlavne_menu_opravnenie & 4);
    // Test pre pridanie a odkaz: 0 - nemám oprávnenie; 1 - odkaz bude na addpol; 2 - odkaz bude na Clanky:add
    $druh_opravnenia = $opravnenie_add ? ($this->user->isAllowed($name, 'addpol') ? 1 : ($this->user->isAllowed($this->name, 'add') ? 2 : 0)) : 0;
    $this->admin_links = [
      "alink" => [
        "druh_opravnenia" => $druh_opravnenia,
        "link"    => $druh_opravnenia ? ($druh_opravnenia == 1 ? ['main' => $name . ':addpol']
          : ['main' => 'Clanky:add', 'uroven' => $hlm->uroven + 1]) : NULL,
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
  public function render()
  {
    $this->template->setFile(__DIR__ . '/PrilohyClanok.latte');
    $this->template->clanok = $this->clanok;
    $this->template->admin_links_prilohy = $this->admin_links;
    $this->template->big_img = $this->big_img;
    $this->template->prilohy_adresar = $this->prilohy_adresar;
    $this->template->render();
  }

  /** 
   * Komponenta formulara pre pridanie a editaciu prílohy polozky. */
  public function createComponentEditPrilohyForm(): Nette\Application\UI\Form
  {
    $form = $this->editPrilohyForm->create(
      $this->clanok->id_hlavne_menu,
      $this->template->basePath,
      /*$this->presenter->link('Clanky:', $this->clanok->id_hlavne_menu)*/
    );
    $form->setDefaults(["id" => 0, "id_hlavne_menu" => $this->clanok->id_hlavne_menu, "id_user_roles" => $this->clanok->hlavne_menu->id_user_roles]);
    $form['uloz']->onClick[] = function ($button) {
      $this->presenter->flashRedirect(['this', ['tab' => 'prilohy-tab']], 'Príloha bola úspešne uložená!', 'success');
    };
    return $this->presenter->_vzhladForm($form);
  }

  public function handleShowInText(int $id): void
  {
    $priloha = $this->dokumenty->find($id);
    $priloha->update(['zobraz_v_texte' => (1 - $priloha->zobraz_v_texte)]);
    if (!$this->presenter->isAjax()) {
      $this->redirect('this');
    } else {
      $this->redrawControl('flashes');
      $this->redrawControl('prilohy-in');
    }
  }
}

interface IPrilohyClanokAControl
{
  function create(): PrilohyClanokAControl;
}
