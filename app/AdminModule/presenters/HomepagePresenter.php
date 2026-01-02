<?php
namespace App\AdminModule\Presenters;
use DbTable;
/**
 * Domovský preseter administracie.
 *
 * Posledna zmena(last change): 02.01.2026
 *
 *	Modul: ADMIN
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2026 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.0.9
 */
class HomepagePresenter extends BasePresenter {
  
  // -- DB
  /** @var DbTable\Hlavne_menu_cast @inject */
  public $hlavne_menu_cast;
  /** @var string */
  private $dir_to_icons;
  /** @var bool */
  private $add_uroven0;

  public function __construct(string $dir_to_menu, string $dir_to_images, string $dir_to_icons, bool $add_uroven0)
	{
    parent::__construct($dir_to_menu, $dir_to_images);
		// Nastavenie z config-u
    $this->dir_to_icons = $dir_to_icons;
    $this->add_uroven0 = $add_uroven0;
	}
  
  /** Vychodzi render  pre zobrazenie základného panela */
  public function renderDefault() {
    $this->template->parts = $this->hlavne_menu_cast->findAll();
    $this->template->useri = $this->user_main->getUser($this->user->id);
    $this->template->dir_to_icons = $this->dir_to_icons;
  }

  /** Pre zobrazenie článokv v časti menu 
   * @param int $id Id casti hlavneho menu ako zaporne cislo */
  public function renderSection(int $id = 0) {

    if ($id == 0) $this->redirect("Homepage:"); // Ak nie je id zadané
    
    $user = $this->user;
    $this->template->avatar_path = $this->dir_to_menu;
    $this->template->id_menu = -1*$id;
    $this->template->view_add_link = $user->isAllowed('Admin:Menu', 'addpol') && 
                                    ($user->isInRole("admin") ? TRUE :	$this->add_uroven0) &&
                                    $this->id_reg >= $this->hlavne_menu_cast->find(-1*$id)->id_user_roles;
  }
}