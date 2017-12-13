<?php
namespace App\FrontModule\Presenters;

use DbTable;
use Language_support;
use Nette\Application\UI\Multiplier;

/**
 * Prezenter pre vypisanie clankov.
 * 
 * Posledna zmena(last change): 29.11.2017
 *
 *	Modul: FRONT
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2017 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.1.4
 */

class ClankyPresenter extends BasePresenter {
	/** 
   * @inject
   * @var DbTable\Clanok_komponenty */
	public $clanok_komponenty;

  /**
   * @inject
   * @var Language_support\Clanky */
  public $texty_presentera;
  
  /** @var \App\FrontModule\Components\Clanky\PrilohyClanok\IPrilohyClanokControl @inject */
  public $prilohyClanokControlFactory;
  /** @var \App\FrontModule\Components\Faktury\IViewFakturyControl @inject */
  public $viewFakturyControlFactory;
  /** @var \App\FrontModule\Components\Clanky\ZobrazKartyPodclankov\IZobrazKartyPodclankovControl @inject */
  public $zobrazKartyPodclankovControlFactory;
  
	/** @var \Nette\Database\Table\ActiveRow|FALSE */
	public $zobraz_clanok = FALSE;
  private $kotva = "";
  public $viditelnePrilohy;
  public $prilohy_for_big_img;
  protected $big_img;

  /** Vychodzie nastavenia */
	protected function startup() {
    parent::startup();
    // Kontrola ACL
    if (!$this->user->isAllowed($this->name, $this->action)) {
      $this->flashRedirect('Homepage:notAllowed', sprintf($this->trLang('base_nie_je_opravnenie'), $this->action), 'danger');
    }
  }

  /** Zobrazenie konkretneho clanku
   * @param int $id Id hlavneho menu clanku */
	public function actionDefault($id = 0, $kotva = "", $id_big_img = 0) {
    if (($this->zobraz_clanok = $this->hlavne_menu_lang->getOneArticleId($id, $this->language_id, $this->id_reg)) === FALSE) {
      $this->setView("notFound");
    } else {
      $this->kotva = $kotva;
      if ($this->zobraz_clanok->hlavne_menu->redirect_id) { //Ak mám presmerovanie na podclanok
        $this->redirect("Clanky:", $this->zobraz_clanok->hlavne_menu->redirect_id);              
      } elseif ($this->zobraz_clanok->hlavne_menu->id_nadradenej !== NULL) { //Ak mam v nadradenej polozke zobrazovanie podclankov na kartach
        $nadr = $this->clanok_komponenty->findBy(['id_hlavne_menu' => $this->zobraz_clanok->hlavne_menu->id_nadradenej, 'spec_nazov' => 'zobrazKartyPodclankov']);
        if (count($nadr)) {
          $this->redirect("Clanky:", [$this->zobraz_clanok->hlavne_menu->id_nadradenej, $this->zobraz_clanok->hlavne_menu->spec_nazov]);
        } else {
          $this->setView($this->zobraz_clanok->hlavne_menu->hlavne_menu_template->name);
        }
      } else {
        $this->setView($this->zobraz_clanok->hlavne_menu->hlavne_menu_template->name);
      }
      //Spracovanie priloh
      $this->viditelnePrilohy = $this->dokumenty->getViditelnePrilohy($this->zobraz_clanok->id_hlavne_menu);
      $this->prilohy_for_big_img = $this->dokumenty->getVisibleImages($this->zobraz_clanok->id_hlavne_menu);
      $this->big_img = $id_big_img ? $id_big_img : ((count($pom = $this->viditelnePrilohy)) ? $pom->fetch()->id : 0);
      $this->template->big_img = $this->dokumenty->find($this->big_img);
    }
	}
 
  /** Render pre zobrazenie clanku */
	public function beforeRender() {
    parent::beforeRender();
    $this->getComponent('menu')->selectByUrl($this->link('Clanky:', ["id"=>$this->zobraz_clanok->id_hlavne_menu]));
    $this->template->komentare_povolene =  $this->udaje_webu["komentare"] && ($this->user->isAllowed('Front:Clanky', 'komentar') && $this->zobraz_clanok->hlavne_menu->komentar) ? $this->zobraz_clanok->id_hlavne_menu : 0;
		$this->template->h2 = $this->trLang('h2').$this->zobraz_clanok->view_name;
    $this->template->uroven = $this->zobraz_clanok->hlavne_menu->uroven+2;
    $this->template->avatar = $this->zobraz_clanok->hlavne_menu->avatar;
    $this->template->clanok_view = $this->zobraz_clanok->id_clanok_lang == NULL ? FALSE : TRUE;
    $this->template->clanok_hl_menu = $this->zobraz_clanok->hlavne_menu;
    $this->template->view_submenu = $this->zobraz_clanok->hlavne_menu->id_hlavicka < 3;
    $this->template->viac_info = "";//$this->trLang('viac_info');
    //Zisti, ci su k clanku priradene komponenty
    $this->template->komponenty = $this->clanok_komponenty->getKomponenty($this->zobraz_clanok->id_hlavne_menu, $this->nastavenie["komponenty"]);
    $this->template->prilohy = $this->viditelnePrilohy;
    if (!isset($this->template->prilohy_for_big_img)) {
      $this->template->prilohy_for_big_img = $this->prilohy_for_big_img;
    }
    $this->template->avatar_path = $this->avatar_path;
    $this->template->texts = $this->texty_presentera;
    $this->template->id_hlavne_menu_lang = $this->zobraz_clanok->id;
	}

  /** Render v prípade nenájdenia článku */
  public function renderNotFound() {
    $this->template->h2 = $this->trLang('nenajdeny_clanok_h2');
    $this->template->text = [
      1 => $this->trLang('ospravedln_clanok'),
      2 => $this->trLang('ospravedln_clanok_1')];
    $this->getHttpResponse()->setCode(\Nette\Http\IResponse::S404_NOT_FOUND);
  }
  
  public function handleBigImg($id_big_img = 0) {
//    $this->template->prilohy_for_big_img = /*$this->isAjax() ? [] : */$this->dokumenty->getVisibleImages($this->zobraz_clanok->id_hlavne_menu);
//    $this->template->prilohy_for_big_img[$this->big_img] = $this->dokumenty->find($this->big_img);
//    $this->big_img = $id_big_img/* ? $id_big_img : ((count($pom = $this->viditelnePrilohy)) ? $pom->fetch()->id : 0)*/;
//    $this->template->prilohy_for_big_img[$this->big_img] = $this->dokumenty->find($this->big_img);
    $this->template->big_img = $this->dokumenty->find($id_big_img);
//    $this->postGet('this');
    if ($this->isAjax()) {
      $this->redrawControl('bigimgName');
      $this->redrawControl('bigimgContainer');
      $this->redrawControl('prilohyClanok');
    } else {
      $this->redirect('this');
    }
  }

  /** Komponenta pre komentare k clanku
   * @return Multiplier */
	public function createComponentKomentar() {
		return new Multiplier(function ($id_hlavne_menu) {
      $komentar = $this->komentarControlControlFactory->create();
      $komentar->setParametre($id_hlavne_menu);
			return $komentar;
		});
	}

  /** Komponenta pre zobrazenie clanku
   * @return \App\FrontModule\Components\Clanky\ZobrazClanok\ZobrazClanokControl */
  public function createComponentUkazTentoClanok() {
    $ukaz_clanok = New \App\FrontModule\Components\Clanky\ZobrazClanok\ZobrazClanokControl($this->zobraz_clanok, $this->texy);
    $ukaz_clanok->setTexts([
      "not_found"         => $this->trLang('base_template_not_found'),
      "platnost_do"       => $this->trLang('base_platnost_do'),
      "zadal"             => $this->trLang('base_zadal'),
      "zobrazeny"         => $this->trLang('base_zobrazeny'),  
      "anotacia"          => $this->trLang('base_anotacia'),
      "viac"              => $this->trLang('base_viac'),
      "text_title_image"  => $this->trLang("base_text_title_image"),
      ]);
    $ukaz_clanok->setClanokHlavicka($this->udaje_webu['clanok_hlavicka']);
    return $ukaz_clanok;
  }

  /** 
   * Komponenta pre zobrazenie priloh
   * @return \App\FrontModule\Components\Clanky\PrilohyClanok\PrilohyClanokControl */
  public function createComponentPrilohy() {
    $prilohy = $this->prilohyClanokControlFactory->create();
    $prilohy->setNastav($this->zobraz_clanok, $this->avatar_path, $this->language_id);
    return $prilohy;
  }
  
  /** 
   * Komponenta pre zobrazenie faktur
   * @return \App\FrontModule\Components\Faktury\ViewFakturyControl */
  public function createComponentViewFaktury() {
    $viewFaktury = $this->viewFakturyControlFactory->create();
    $viewFaktury->setSkupina($this->zobraz_clanok->id_hlavne_menu);
    return $viewFaktury;
  }
  
  /** 
   * Komponenta pre vykreslenie podclankov na kartach
   * @return \App\FrontModule\Components\Clanky\ZobrazKartyPodclankov\ZobrazKartyPodclankovControl */
  public function createComponentZobrazKartyPodclankov() {
    $odkaz = $this->zobrazKartyPodclankovControlFactory->create();
    $odkaz->setArticle($this->zobraz_clanok->id_hlavne_menu, $this->language_id, $this->kotva);
    return $odkaz;
  }
  
  protected function createTemplate($class = NULL) {
    $servise = $this;
    $template = parent::createTemplate($class);
    $template->addFilter('odkazdo', function ($id) use($servise){
      $serv = $servise->link("Dokumenty:default", ["id"=>$id]);
      return $serv;
    });
    $template->addFilter('border_x', function ($text){
      $pom = $text != null & strlen($text)>2 ? explode("|", $text) : ['','0'];
      $xs = 'style="border: '.$pom[1].'px solid '.(strlen($pom[0])>2 ? ('#'.$pom[0]):'inherit').'"';
      return $xs;
    });
    $template->addFilter('border_css_x', function ($text){
      $pom = $text != null & strlen($text)>2 ? explode("|", $text) : ['','0'];
      $xs = 'border: '.$pom[1].'px solid '.(strlen($pom[0])>2 ? ('#'.$pom[0]):'inherit').';';
      return $xs;
    });
    $template->addFilter('border_width', function ($text){
      $pom = $text != null & strlen($text)>2 ? explode("|", $text) : ['','1'];
      return $pom[1];
    });
    $template->addFilter('border_color', function ($text){
      $pom = $text != null & strlen($text)>2 ? explode("|", $text) : ['','0'];
      return strlen($pom[0])>2 ? ('#'.$pom[0]):'inherit';
    });
    return $template;
	}
}