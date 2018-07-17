<?php
namespace PeterVojtech\News_key;

use DbTable;
use Nette\Application\UI;
use Nette\Security;

/**
 * Komponenta pre pracu s klucom pre novinky v odkaze
 * Posledna zmena(last change): 08.03.2018
 * 
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com> 
 * @copyright  Copyright (c) 2012 - 2018 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.0.1
 */

class NewsKeyControl extends UI\Control {
  
  /** @var DbTable\User_profiles */
  public $user_profiles;
  /** @var Security\Identity */
  public $user_identity;
  /** @var mix */
//  private $hasser;

  /**
   * @param DbTable\User_profiles $user_profiles
   * @param Security\User $user */
  public function __construct(DbTable\User_profiles $user_profiles, Security\User $user) {
    parent::__construct();
    $this->user_profiles = $user_profiles;
    $this->user_identity = $user->getIdentity();
//    $this->hasser = $user->getAuthenticator(); //Ziskanie objektu pre vytvaranie hash hesla a iných
//    $this->hasser->PasswordHash(8,FALSE);            //Nastavenie
  }
  
  /** Generuje news_key a uloží k danému uzivatelovi */
  public function Generate() {
    $this->user_profiles->find($this->user_identity->id_user_profiles)->update(['news_key'=>Security\Passwords::hash($this->user_identity->email."news=>A"), 'news'=>"A"]);
  }
  
  /** Vymaze news_key konkretneho uzivatela */
  public function Delete() {
    $this->user_profiles->find($this->user_identity->id_user_profiles)->update(['news_key'=>NULL, 'news'=>"N"]);
  }
  
  /**
   * Rozhodne co urobit
   * @param string $news Volba posielania novinky */
  public function Spracuj($news) {
    if ($news == "A") {
      $this->Generate();
    } else {
      $this->Delete();
    }
  }
}