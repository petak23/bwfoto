<?php

namespace App\AdminModule\Presenters;

use App\AdminModule\Components;
use App\AdminModule\Forms\Article;
use DbTable;
use Nette;
use Nette\Application\UI\Form;
use PeterVojtech;

/**
 * Zakladny presenter pre presentery obsluhujuce polozky hlavneho menu v module ADMIN
 * 
 * Posledna zmena(last change): 03.02.2023
 *
 * Modul: ADMIN
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.5.5
 */
abstract class ArticlePresenter extends BasePresenter
{

  // -- Traity
  //  use PeterVojtech\Clanky\ZobrazKartyPodclankov\zobrazKartyPodclankovTrait;
  use PeterVojtech\Clanky\OdkazNaClanky\odkazNaClankyTrait;

  // -- DB
  /** @var DbTable\Clanok_komponenty @inject */
  public $clanok_komponenty;
  /** @var DbTable\Druh @inject */
  public $druh;
  /** @var DbTable\Dokumenty @inject */
  public $dokumenty;
  /** @var DbTable\Hlavne_menu_cast @inject*/
  public $hlavne_menu_cast;
  /** @var DbTable\Hlavne_menu_lang @inject*/
  public $hlavne_menu_lang;
  /** @var DbTable\Products @inject */
  public $products;

  // -- Formulare
  /** @var Article\IEditMenuFormFactory @inject */
  public $editMenuFormFactory;

  // -- Komponenty
  /** @var Components\Article\IAdminAddMenu @inject */
  public $adminAddMenuControlFactory;
  /** @var Components\Article\TitleArticle\ITitleArticleControl @inject */
  public $titleArticleControlFactory;
  /** @var Components\Article\TitleImage\ITitleImageControl @inject */
  public $titleImageControlFactory;
  /** @var Components\Faktury\IViewFakturyControl @inject */
  public $viewFakturyControlFactory;
  /** @var Components\User\IKontaktControl @inject */
  public $kontaktControlFactory;
  /** @var Components\User\ContactCategori\IContactCategoriControl @inject */
  public $contactCategoriControlFactory;

  /** @var int hodnota id pre pridanie do menu */
  public $add_menu_id;

  /** @var array */
  public $pol_menu = [];

  /** @var array - pole pre menu formular */
  public $menuformuloz = ["text" => "Ulož", "redirect" => FALSE, "edit" => FALSE];

  /** @var Nette\Database\Table\ActiveRow|FALSE */
  public $zobraz_clanok;

  /** @var array */
  protected $form_nazov_modifi = [];
  /** @var array */
  protected $texts_for_notFound = [
    "h2" => "Nenájdená položka!",
    "text" => [
      1 => "Ľutujeme, ale Vami požadovaná položka menu sa nenašla.",
      2 => "Alebo došlo k chybe, alebo neexzistuje, alebo nemáte dostatočné oprávnenie na prezeranie.",
    ]
  ];
  /** @var int */
  protected $uroven;
  /** @var \Nette\Database\Table\Selection */
  public $jaz;
  /** @var array */
  public $admin_links;

  /**
   * Pre zjednodusenie vypisu
   * @param string $h2 Nadpis hlásenia
   * @param array $t Texty hlásenia
   */
  protected function _toNotFound($h2 = "Not Found", $t = "Article not found"): void
  {
    $this->texts_for_notFound["h2"] = $h2;
    if (isset($t) && is_array($t)) {
      foreach ($t as $k => $v) {
        $this->texts_for_notFound["text"][$k] = $v;
      }
    }
    $this->setView("notFound");
  }

  protected function startup()
  {
    parent::startup();
    $this->jaz = $this->lang->akceptovane();
  }

  /** Akcia pre zobrazenie polozky.
   * @param int $id Id polozky
   */
  public function actionDefault(int $id = 1)
  {
    try {
      $this->zobraz_clanok = $this->hlavne_menu_lang->getOneArticleId($id, $this->language_id, $this->id_reg);
    } catch (DbTable\ArticleMainMenuException $th) {
      $this->setView("notFound");
      return;
    }
  }

  /** 
   * Funkcia overi vlastnictvo clanku */
  public function vlastnik(int $id_user_main = 0): bool
  {
    $user = $this->user;
    return $user->isInRole('admin') ? TRUE : $user->getIdentity()->id == $id_user_main;
  }

  /** Render pre defaultnu akciu */
  public function renderDefault()
  {
    $hlm = $this->zobraz_clanok->hlavne_menu; // Pre skratenie zapisu
    // Test opravnenia na pridanie podclanku: Si admin? Ak nie, si vlastnik? Ak nie, povolil vlastnik pridanie, editaciu? A mám dostatocne id reistracie?
    $opravnenie_add = $this->vlastnik($hlm->id_user_main) ? TRUE : (bool)($hlm->id_hlavne_menu_opravnenie & 1);
    $opravnenie_edit = $this->vlastnik($hlm->id_user_main) ? TRUE : (bool)($hlm->id_hlavne_menu_opravnenie & 2);
    $opravnenie_del = $this->vlastnik($hlm->id_user_main) ? TRUE : (bool)($hlm->id_hlavne_menu_opravnenie & 4);
    // Test pre pridanie a odkaz: 0 - nemám oprávnenie; 1 - odkaz bude na addpol; 2 - odkaz bude na Clanky:add
    $druh_opravnenia = $opravnenie_add ? ($this->user->isAllowed($this->name, 'addpol') ? 1 : ($this->user->isAllowed($this->name, 'add') ? 2 : 0)) : 0;
    $modul_presenter = explode(":", $this->name);
    $this->admin_links = [
      "alink" => [
        "druh_opravnenia" => $druh_opravnenia,
        "link"    => $druh_opravnenia ? ($druh_opravnenia == 1 ? ['main' => $modul_presenter[1] . ':addpol']
          : ['main' => 'Clanky:add', 'uroven' => $hlm->uroven + 1]) : NULL,
        "text"    => "Pridaj podčlánok"
      ],
      "elink" => $opravnenie_edit && $this->user->isAllowed($this->name, 'edit'),
      "dlink" => $opravnenie_del && $this->user->isAllowed($this->name, 'del') && !$this->hlavne_menu->maPodradenu($this->zobraz_clanok->id_hlavne_menu),
      "vlastnik" => $this->vlastnik($hlm->id_user_main),
    ];
    $this->template->admin_links = $this->admin_links;
    $this->template->clanok = $this->zobraz_clanok;
  }

  /** 
   * Vypis ponuky s polozkami, ktore je mozne pridavat
   * @param int $id Id nadradenej polozky */
  public function actionAddpol(int $id)
  {
    $this->add_menu_id = $id;
  }

  /** 
   * Komponenta pre vytvorenie ponuky na pridanie do hlavneho menu na zaklade druhu. */
  public function createComponentAddMenu(): Components\Article\AdminAddMenuControl
  {
    return $this->adminAddMenuControlFactory->create($this->add_menu_id);
  }

  /** render s priradenim textu v prípade nenájdenia článku  */
  public function renderNotFound()
  {
    $this->template->info = $this->texts_for_notFound;
  }

  /** Akcia pre editovanie polozky - krok c.1 - udaje pre DB tab.: hlavne_menu a hlavne_menu_lang
   * @param int $id Id editovanej polozky
   */
  public function actionEdit(int $id)
  {
    if (($hlm_lang = $this->hlavne_menu_lang->findBy(["id_hlavne_menu" => $id])) !== FALSE) {
      $this->pol_menu = $hlm_lang->fetch()->hlavne_menu->toArray();
      $this["menuEditForm"]->setDefaults($this->pol_menu);
      $vychodzie_pre_form = [];
      foreach ($hlm_lang as $j) { //Pridanie vychodzich hodnot pre jazyky
        $la = $j->lang->skratka . "_";
        $vychodzie_pre_form = [
          $la . 'id' => $j->id,
          $la . 'menu_name' => $j->menu_name,
          $la . 'h1part2' => $j->h1part2,
          $la . 'view_name' => $j->view_name,
        ];
        $this->form_nazov_modifi = array_merge($this->form_nazov_modifi, [$la => $j->menu_name]);
      }
      $this["menuEditForm"]->setDefaults($vychodzie_pre_form);
      $this->template->pridanie = [];
    } else {
      return $this->_toNotFound("K požadovanému id som nenašiel položku na editovanie! id=' $id'!");
    }
  }

  public function renderEdit()
  {
    $this->template->h2 = 'Editácia položky: ';
    $this->setView("krok1");
  }

  /** 
   * Akcia pre pridanie polozky polozky - krok c.1 - udaje pre DB tab.: hlavne_menu a hlavne_menu_lang
   * @param int $id - id nadradenej polozky
   * @param int $uroven - uroven menu */
  public function actionAdd(int $id, int $uroven)
  {
    //Kontrola urovne
    if (!(isset($uroven))) {
      return $this->_toNotFound("Nezadaná úroveň!");
    }
    $this->uroven = (int)$uroven;
    if (($druh = $this->druh->findOneBy(["presenter" => ucfirst($this->udaje_webu['meno_presentera']), "povolene" => 1])) === FALSE) {
      $this->texts_for_notFound["h2"] = "Nepodarilo sa nájsť druh!";
      $this->setView("notFound");
      return FALSE;
    }
    $hladaj = $this->uroven == 0 ? "id_hlavne_menu_cast" : "id_nadradenej";
    $poradie = $this->hlavne_menu->findBy([$hladaj => (int)$id, "uroven" => $this->uroven])->max('poradie') + 1;
    $this->pol_menu = [
      'id'                  => 0,
      'id_druh'             => $druh->id,
      'id_user_main'        => $this->getUser()->getId(),
      'id_user_roles'       => $this->hlavne_menu->findOneBy(["id" => $id])->id_user_roles, //Priradi uroven registracie nadradenej polozky
      'poradie'             => $poradie,
      'uroven'              => $this->uroven,
      'id_hlavne_menu_cast' => (int)$id,
      'id_nadradenej'       => $this->uroven == 0 ? NULL : (int)$id,
    ];
    if (!$this->uroven) { //Pridavam priamo do casti
      $nad_pol = $this->hlavne_menu_cast->find($id);
      if ($nad_pol === FALSE) {
        return $this->_toNotFound("Nepodarilo sa nájsť časť!");
      }
    } else { //Pridavam do menu
      $nad_pol = $this->hlavne_menu_lang->findOneBy(["id_hlavne_menu" => $id, "id_lang" => 1]); //Ak nemam polozku
      if ($nad_pol === FALSE) {
        return $this->_toNotFound("Nepodarilo sa nájsť nadradenú položku!");
      }
      $this->pol_menu = array_merge($this->pol_menu, [
        'id_hlavne_menu_cast' => $nad_pol->hlavne_menu->id_hlavne_menu_cast,
        'id_hlavicka'         => $nad_pol->hlavne_menu->id_hlavicka,
      ]);
    }
    $this["menuEditForm"]->setDefaults($this->pol_menu);
    $vychodzie_pre_form = [];
    foreach ($this->jaz as $j) { //Pridanie vychodzich hodnot pre jazyky
      $vychodzie_pre_form = [
        $j->skratka . '_id' => 0,
        $j->skratka . '_menu_name' => "",
        $j->skratka . '_h1part2' => "",
        $j->skratka . '_view_name' => "",
      ];
    }
    $this["menuEditForm"]->setDefaults($vychodzie_pre_form);
    //-----------------------------------
    $this->template->h2 = 'Pridanie položky pre: ' . $nad_pol->view_name;
    $this->template->pridanie = $this->jaz;
    $this->setView('krok1');
  }

  /**
   * Edit hlavne menu form component factory. */
  public function createComponentMenuEditForm(): Form
  {
    $form = $this->editMenuFormFactory->create()->form($this->uroven, $this->menuformuloz["text"], $this->udaje_webu["meno_presentera"]);
    $form['uloz']->onClick[] = function ($button) {
      $this->menuEditFormSubmitted($button);
    };
    $form['cancel']->onClick[] = function ($button) {
      $form = $button->getForm();
      $values = $form->getHttpData($form::DATA_TEXT | $form::DATA_KEYS);
      $id = $values['id'] ? $values['id'] : ($values['uroven'] ? $values['id_nadradenej'] : -1 * $values['id_hlavne_menu_cast']);
      $pol = ($id > 0) ? $this->hlavne_menu->find($id)->druh->presenter : 'Homepage';
      $this->redirect($pol . ":", $id);
    };
    return $this->_vzhladForm($form);
  }

  /** 
   * Spracovanie formulara pre editaciu udajov menu(clanku).
   * @param Nette\Forms\Controls\SubmitButton $button Data formulara */
  public function menuEditFormSubmitted($button)
  {
    $values = $button->getForm()->getValues();   //Nacitanie hodnot formulara
    if (($ulozenie = $this->hlavne_menu->saveArticle($values, $this->pol_menu, $this->jaz))) {


      $this->flashMessage('Položka menu bola uložená!', 'success');
      $this->redirect($this->menuformuloz["redirect"] ? $this->menuformuloz["redirect"] : 'Menu:', $ulozenie);
    } else {
      $this->flashMessage('Došlo k chybe a položka sa neuložila. Skúste neskôr znovu...', 'danger');
    }
  }

  /** 
   * Komponenta pre tvorbu casti titulku polozky. */
  public function createComponentTitleArticle(): Components\Article\TitleArticle\TitleArticleControl
  {
    $title = $this->titleArticleControlFactory->create();
    $title->setTitle($this->zobraz_clanok, $this->name, $this->udaje_webu['komentare']);
    return $title;
  }

  /** 
   * Komponenta pre pre titulku polozky(titulny obrazok a nadpis). */
  public function createComponentTitleImage(): Components\Article\TitleImage\TitleImageControl
  {
    $title = $this->titleImageControlFactory->create();
    $title->setTitle($this->zobraz_clanok, $this->name);
    return $title;
  }

  /** Funkcia pre spracovanie signálu vymazavania
   * @param int $id Id polozky v hlavnom menu
   * @param string $druh Blizsia specifikacia, kde je to potrebne
   */
  function confirmedDelete($data, $druh = "")
  {
    $id = $data["id"];
    //Vstupna kontrola
    if (!(isset($id) && $id)) {
      $this->error("Id položky nie je nastavené!");
    }
    if ($druh != 'priloha') {
      $hl_m = $this->hlavne_menu_lang->findOneBy(["id_hlavne_menu" => $id, "id_lang" => 1]);
      if ($hl_m === FALSE) {
        $this->error("Položka s id = " . $id . " sa nenašla!");
      }
      $presenter = $hl_m->hlavne_menu->druh->presenter;
    }
    if ($druh == 'avatar') {
      $uloz = $this->hlavne_menu->zmazTitleImage($id, $this->nastavenie["dir_to_menu"], $this->nastavenie["wwwDir"]);
      $this->_ifMessage($uloz !== FALSE ? TRUE : FALSE, 'Titulný obrázok bol vymazaný!', 'Došlo k chybe a titulný obrázok nebol vymazaný!');
      $this->redirect($presenter . ':', $id);
    } elseif ($druh == 'priloha') { //Poziadavka na zmazanie prilohy
      $pr = $this->dokumenty->find($id); //najdenie prislusnej polozky menu, ku ktorej priloha patri
      if ($pr !== FALSE) {
        $id_nadradenej = $pr->id_hlavne_menu; //Nechcem smerovat na nadradeny clanok, len na seba
        $vysledok = $this->vymazSubor($pr->subor) ? (in_array(strtolower($pr->pripona), ['png', 'gif', 'jpg']) ? $this->vymazSubor($pr->thumb) : TRUE) : FALSE;
        $this->_ifMessage($vysledok ? $pr->delete() : FALSE, 'Príloha bola vymazaná!', 'Došlo k chybe a príloha nebola vymazaná!');
      } else {
        $this->flashRedirect("Homepage:", 'Došlo k chybe a príloha nebola vymazaná!', 'danger');
      }
    } elseif ($druh == "") {
      $id_nadradenej = ($hl_m->hlavne_menu->id_nadradenej == NULL) ? -1 * $hl_m->hlavne_menu->id_hlavne_menu_cast
        : $hl_m->hlavne_menu->id_nadradenej;
      if ($presenter == "Clanky") { //Mazanie clanku
        $this->_ifMessage($this->_delClanok($id), 'Článok bol úspešne vymazaný!', 'Došlo k chybe a článok nebol vymazaný!'); //Poziadavka na zmazanie clanku
      } elseif ($presenter == "Menu") {
        $this->_ifMessage(($id_nadradenej = $this->_delHlMenu($id)), 'Položka menu bola úspešne vymazaná!', 'Došlo k chybe a položka menu nebola vymazaná!');
      }
      if ($id_nadradenej < 0) {
        $this->redirect('Homepage:', ["id" => $id_nadradenej]);
      } else {
        if (($nadr = $this->hlavne_menu->find($id_nadradenej)) !== FALSE) {
          $this->redirect($nadr->druh->presenter . ':', $nadr->id);
        }
      }
    }
  }

  /** 
   * Funkcia vymaze subor ak exzistuje
   * Ak zmaze alebo neexistuje(nie je co mazat) tak vráti 1 inak 0
   * @param string $subor Nazov suboru aj srelativnou cestou */
  public function vymazSubor(string $subor): int
  {
    return (is_file($subor)) ? unlink($this->nastavenie["wwwDir"] . "/" . $subor) : -1;
  }

  /** 
   * Vymaze polozku hl. menu */
  protected function _delHlMenu(int $id): int
  {
    if ($id == 1) {
      $this->flashRedirect("Homepage:", '!!! Nesmieš zmazať položku menu s ID=1 !!!', 'danger');
    }
    $p = $this->hlavne_menu->find($id);
    if ($p->avatar !== NULL) {
      $this->vymazSubor('www/files/menu/' . $p->avatar);
    }
    if ($p !== FALSE) {
      $n = $p->id_nadradenej !== NULL ? $p->id_nadradenej : -1 * $p->id_hlavne_menu_cast;
      $out = ($this->hlavne_menu_lang->findBy(["id_hlavne_menu" => $id])->delete() > 0) && ($this->hlavne_menu->zmaz($id) == 1) ? $n : 0;
    } else {
      $out = 0;
    }
    return $out;
  }

  /** 
   * Vymaze clanok so vsetkym co k tomu patri 
   * @param int $id Id mazaqneho clanku */
  protected function _delClanok(int $id): bool
  {
    $dokumenty = $this->dokumenty->findBy(["id_hlavne_menu" => $id]);
    $komponenty = $this->clanok_komponenty->findBy(["id_hlavne_menu" => $id]);
    $products = $this->products->findBy(["id_hlavne_menu" => $id]);

    $this->hlavne_menu->zmazTitleImage($id, $this->nastavenie["dir_to_menu"], $this->nastavenie["wwwDir"]);
    if ($dokumenty !== null && ($pocita = count($dokumenty))) {
      $do = 0;
      foreach ($dokumenty as $pr) {
        $do = $do + ($this->vymazSubor($pr->main_file) ? ($pr->znacka !== NULL ? $this->vymazSubor($pr->thumb_file) : 1) : 0);
      }
      $out = ($do == $pocita) ? ($dokumenty->delete() == $pocita ? TRUE : FALSE) : FALSE;
    } else {
      $out = TRUE;
    }
    $pocita = 0;
    if ($products !== null && ($pocita = count($products))) {
      $do = 0;
      foreach ($products as $pr) {
        $do = $do + ($this->vymazSubor($pr->main_file) ? ($pr->znacka !== NULL ? $this->vymazSubor($pr->thumb_file) : 1) : 0);
      }
      $out_p = ($do == $pocita) ? ($products->delete() == $pocita ? TRUE : FALSE) : FALSE;
    } else {
      $out_p = TRUE;
    }
    $out_k = ($komponenty !== FALSE && ($pocita = count($komponenty))) ? ($komponenty->delete() == $pocita ? TRUE : FALSE) : TRUE;
    $out_h = $this->_delHlMenu($id);
    return $out and $out_p and $out_k and $out_h;
  }

  /** Komponenta pre vypis kontaktneho formulara */
  public function createComponentKontakt(): Components\User\KontaktControl
  {
    return $this->kontaktControlFactory->create();
  }

  /** 
   * Komponenta pre vykreslenie kontaktov pre skupinu uzivatelov */
  public function createComponentContactCategori(): Components\User\ContactCategori\ContactCategoriControl
  {
    return $this->contactCategoriControlFactory->create();
  }

  /** 
   * Komponenta pre zobrazenie priloh */
  public function createComponentViewFaktury(): Components\Faktury\ViewFakturyControl
  {
    $viewFaktury = $this->viewFakturyControlFactory->create();
    $viewFaktury->setSkupina($this->zobraz_clanok->id_hlavne_menu);
    return $viewFaktury;
  }
}
