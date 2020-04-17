<?php
namespace App\FrontModule\Presenters;

use DbTable;
//use Language_support;
use Nette\Application\UI\Multiplier;
use PeterVojtech;

/**
 * Prezenter pre vypisanie clankov.
 * 
 * Posledna zmena(last change): 17.04.2020
 *
 *	Modul: FRONT
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2020 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.3.3
 */

class ClankyPresenter extends BasePresenter {

//  use PeterVojtech\Clanky\ZobrazKartyPodclankov\zobrazKartyPodclankovTrait;
  use PeterVojtech\Clanky\OdkazNaClanky\odkazNaClankyTrait;
  
	/** @var DbTable\Clanok_komponenty @inject*/
	public $clanok_komponenty;
  /** @var DbTable\Products @inject */
	public $products;
  
  // -- Komponenty
  /** @var \App\FrontModule\Components\Clanky\Attachments\IAttachmentsControl @inject */
  public $attachmentsClanokControlFactory;
  /** @var \App\FrontModule\Components\Products\IProductsViewControl @inject */
  public $productsViewControlFactory;
  /** @var \App\FrontModule\Components\Faktury\IViewFakturyControl @inject */
  public $viewFakturyControlFactory;
  
  /** @var \Nette\Database\Table\ActiveRow|FALSE */
	public $zobraz_clanok = FALSE;
  private $kotva = "";
  public $viditelnePrilohy;
  public $prilohy_for_big_img;
  /** @var mixed */
  protected $big_img;
  
  private $attachments = [];
  
  /** 
   * Zobrazenie konkretneho clanku
   * @param int $id Id hlavneho menu clanku */
	public function actionDefault(int $id = 0, $kotva = ""/*, $id_big_img = 0, $product = 0*/) {
		// Vsuvka pre presmerovanie ak nemam ziadnu aktualitu
		if (in_array("aktualne", $this->clanok_komponenty->getKomponentyName($id)) &&  //Mam k clanku priradenu komponentu aktualne
				isset($this["aktualne"]) && 																							 //Mam je vytvorenu
				is_array($re = $this["aktualne"]->getPresmerovanie())) {									 //Je nastavene presmerovanie
			$this->flashRedirect(['Clanky:', $re['id']], $re['txt'], 'info');
		}
    
    $this->kotva = $kotva;
    
		try { //Find article
      $this->zobraz_clanok = $this->hlavne_menu_lang->getOneArticleId($id, $this->language_id, $this->id_reg); // Najdi clanok
    } catch (DbTable\ArticleMainMenuException $e) {
      if ($e->getCode() == 2) { //Article missing permissions
        $this->flashRedirect(["User:", ['backlink' => $this->storeRequest()]], 'clanky_najdeny_neprihlaseny', "warning");
      } else {                  //Article not found
        $this->setView("notFound"); 
      }  
      return;
    }

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
    $miniatury = $this->hlavne_menu_lang->findBy(["hlavne_menu.id_nadradenej"=> $this->zobraz_clanok->id_hlavne_menu, "id_lang" => $this->language_id]);
    $this->viditelnePrilohy = $this->dokumenty->getViditelnePrilohy($this->zobraz_clanok->id_hlavne_menu, "type ASC");
    $produsts = $this->products->findBy(["id_hlavne_menu"=>$this->zobraz_clanok->id_hlavne_menu]);
    foreach ($miniatury as $v) {
      $this->attachments[] = ["type"=>"menu", "article"=>$v];
    }
    foreach ($this->viditelnePrilohy as $v) {
      $this->attachments[] = ["type"=>"attachments".$v->type, "article"=>$v];
    }
    foreach ($produsts as $v) {
      $this->attachments[] = ["type"=>"product", "article"=>$v];
    }

    $this->big_img = count($this->attachments) ? array_merge(['id' => 0], $this->attachments[0]) : [];
//    dump($this->big_img);
//    die();
	}
 
  /** Render pre zobrazenie clanku */
	public function beforeRender() {
    parent::beforeRender();
    if ($this->zobraz_clanok !== FALSE) {
      $this->getComponent('menu')->selectByUrl($this->link('Clanky:', ["id"=>$this->zobraz_clanok->id_hlavne_menu]));
      $this->template->komentare_povolene =  $this->udaje_webu["komentare"] && ($this->user->isAllowed('Front:Clanky', 'komentar') && $this->zobraz_clanok->hlavne_menu->komentar) ? $this->zobraz_clanok->id_hlavne_menu : 0;
      $this->template->h2 = $this->zobraz_clanok->view_name;
      $this->template->uroven = $this->zobraz_clanok->hlavne_menu->uroven+2;
      $this->template->avatar = $this->zobraz_clanok->hlavne_menu->avatar;
      $this->template->clanok_view = $this->zobraz_clanok->id_clanok_lang == NULL ? FALSE : TRUE;
      $this->template->clanok_hl_menu = $this->zobraz_clanok->hlavne_menu;
      $this->template->podclanky = $this->hlavne_menu->maPodradenu($this->zobraz_clanok->id);
      $this->template->view_submenu = $this->zobraz_clanok->hlavne_menu->id_hlavicka < 3;
      $this->template->viac_info = $this->texty_presentera->translate('clanky_viac_info');
      //Zisti, ci su k clanku priradene komponenty
      $this->template->komponenty = $this->clanok_komponenty->getKomponenty($this->zobraz_clanok->id_hlavne_menu, $this->nastavenie["komponenty"]);
      $this->template->prilohy = $this->viditelnePrilohy;
      if (!isset($this->template->prilohy_for_big_img)) {
         $this->template->prilohy_for_big_img = $this->prilohy_for_big_img;
      }
      $this->template->id_hlavne_menu_lang = $this->zobraz_clanok->id;
      
      $this->template->attachments = $this->attachments;
      $this->template->big_img = $this->big_img;
    }
	}

  /** Render v prípade nenájdenia článku */
  public function renderNotFound() {
    $this->getHttpResponse()->setCode(\Nette\Http\IResponse::S404_NOT_FOUND);
  }
  
  /**
   * Signal pre zobrazenie velkeho nahladu
   * @param int $id_big_img */
  public function handleOpenAsBigImage(int $id_big_img) {
    $this->big_img = array_merge(['id' => $id_big_img], $this->attachments[$id_big_img]);
    if ($this->isAjax()) {
      $this->redrawControl('bigimgContainer');
      $this->redrawControl('lightbox-image');
    }
  }
  
  /**
   * Signal pre nacitanie predosleho prvku v lightboxe
   * @param int $id_big_img */
  public function handleBigImgBefore(int $id_big_img) {
		$keys = array_keys($this->attachments);
    $new_id = $id_big_img;
    do {
      $new_id  = ($new_id <= $keys[0]) ? $keys[count($this->attachments)-1] : $new_id - 1;
    } while (!in_array($this->attachments[$new_id]['type'],["attachments2", "product"]));
    $this->big_img = array_merge(['id' => $new_id], $this->attachments[$new_id]); 
		if ($this->isAjax()) {
      $this->redrawControl('bigimgContainer');
      $this->redrawControl('lightbox-image');
    }
	}

  /**
   * Signal pre nacitanie nasledujuceho prvku v lightboxe
   * @param int $id_big_img */
	public function handleBigImgAfter(int $id_big_img) {
		$keys = array_keys($this->attachments);
    $new_id = $id_big_img;
    do {
      $new_id = ($new_id >= $keys[count($this->attachments)-1]) ? $keys[0] : $new_id + 1;
    } while (!in_array($this->attachments[$new_id]['type'],["attachments2", "product"]));
    $this->big_img = array_merge(['id' => $new_id], $this->attachments[$new_id]);
		if ($this->isAjax()) {
      $this->redrawControl('bigimgContainer');
      $this->redrawControl('lightbox-image');
    }
	}
  
  /** 
   * Komponenta pre komentare k clanku
   * @return Multiplier */
	public function createComponentKomentar() {
		return new Multiplier(function ($id_hlavne_menu) {
      $komentar = $this->komentarControlFactory->create();
      $komentar->setParametre($id_hlavne_menu);
			return $komentar;
		});
	}

  /**
   * Komponenta pre zobrazenie clanku
   * @return \App\FrontModule\Components\Clanky\ZobrazClanok\ZobrazClanokControl */
  public function createComponentUkazTentoClanok() {
    $ukaz_clanok = $this->zobrazClanokControlFactory->create();
    $ukaz_clanok->setArticle($this->zobraz_clanok)
                ->setLanguage($this->language_id)
                ->setClanokHlavicka($this->udaje_webu['clanok_hlavicka']);
    return $ukaz_clanok;
  }

  /** 
   * Komponenta pre zobrazenie priloh
   * @return \App\FrontModule\Components\Clanky\Attachments\AttachmentsControl */
  public function createComponentAttachments() {
    $attachments = $this->attachmentsClanokControlFactory->create();
    $attachments->setNastav($this->zobraz_clanok, $this->language);
    return $attachments;
  }
  
  /** 
   * Komponenta pre zobrazenie produktov
   * @return \App\FrontModule\Components\Products\ProductsViewControl */
  public function createComponentViewProducts() {
    $products = $this->productsViewControlFactory->create();
    $products->setNastav($this->zobraz_clanok, $this->language_id, $this->nastavenie);
    return $products;
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
      return strlen($pom[0])>2 ? $pom[0]:'inherit';
    });
    return $template;
	}
}
