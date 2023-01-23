<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\FrontModule\Components;
use App\FrontModule\Forms;
use DbTable;
use Latte\Runtime;
use Nette;
use Nette\Application\UI\Form;
use Nette\Application\UI\Multiplier;
use Nette\Utils;
use PeterVojtech;

/**
 * Prezenter pre vypisanie clankov.
 * 
 * Posledna zmena(last change): 23.01.2023
 *
 *	Modul: FRONT
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.4.7
 */

class ClankyPresenter extends BasePresenter
{

  //  use PeterVojtech\Clanky\ZobrazKartyPodclankov\zobrazKartyPodclankovTrait;
  use PeterVojtech\Clanky\OdkazNaClanky\odkazNaClankyTrait;
  use PeterVojtech\Clanky\Fotogalery\fotogaleryTrait;
  use PeterVojtech\Clanky\Fotocollage\fotocollageTrait;
  use PeterVojtech\Clanky\Fotopanorama\fotopanoramaTrait;

  /** @var DbTable\Clanok_komponenty @inject*/
  public $clanok_komponenty;
  /** @var DbTable\Products @inject */
  public $products;

  // -- Komponenty
  /** @var Components\Clanky\Attachments\IAttachmentsControl @inject */
  public $attachmentsClanokControlFactory;
  /** @var Components\Products\IProductsViewControl @inject */
  public $productsViewControlFactory;
  /** @var Components\Faktury\IViewFakturyControl @inject */
  public $viewFakturyControlFactory;

  // -- Forms
  /** @var Forms\Article\EditArticleTitleFormFactory @inject */
  public $editArticleTitleFormControlFactory;

  /** @var Nette\Database\Table\ActiveRow */
  public $zobraz_clanok;
  /** @var string */
  private $kotva = "";

  /** 
   * Zobrazenie konkretneho clanku
   * @param int $id Id hlavneho menu clanku
   * @param string $kotva */
  public function actionDefault(int $id = 0, string $kotva = "")
  {
    // Vsuvka pre presmerovanie ak nemam ziadnu aktualitu
    if (
      in_array("aktualne", $this->clanok_komponenty->getKomponentyName($id)) &&  //Mam k clanku priradenu komponentu aktualne
      isset($this["aktualne"]) &&                                                //Mam je vytvorenu
      is_array($re = $this["aktualne"]->getPresmerovanie())
    ) {                   //Je nastavene presmerovanie
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
    }

    if ($this->zobraz_clanok->hlavne_menu->redirect_id) { //Ak mám presmerovanie na podclanok
      $this->redirect("Clanky:", $this->zobraz_clanok->hlavne_menu->redirect_id);
    } elseif ($this->zobraz_clanok->hlavne_menu->id_nadradenej !== NULL) { //Ak mam v nadradenej polozke zobrazovanie podclankov na kartach
      $nadr = $this->clanok_komponenty->findBy(['id_hlavne_menu' => $this->zobraz_clanok->hlavne_menu->id_nadradenej, 'spec_nazov' => 'zobrazKartyPodclankov']);
      if (count($nadr)) {
        $this->redirect("Clanky:", [$this->zobraz_clanok->hlavne_menu->id_nadradenej, $this->zobraz_clanok->hlavne_menu->spec_nazov]);
      } else {
        $_tt = $this->zobraz_clanok->hlavne_menu->hlavne_menu_template->name;
        $edit = $this->user->isLoggedIn() && $this->user->isAllowed('Front:Clanky', 'edit');
        $this->setView($edit ? ($_tt . '_log') : $_tt);
      }
    } else {
      $_tt = $this->zobraz_clanok->hlavne_menu->hlavne_menu_template->name;
      $edit = $this->user->isLoggedIn() && $this->user->isAllowed('Front:Clanky', 'edit');
      $this->setView($edit ? ($_tt . '_log') : $_tt);
    }
  }

  private function _renderArticleText(string $s): Runtime\Html|string
  {
    // obr_v_txt
    /*$rozloz = explode("#", $s); // Nutnosť zmeny značky na napr. {I-xxx}
    $vysledok = '';
    $cesta = 'http://' . $this->nazov_stranky . "/";
    if (count($rozloz) > 1) {    //Ak som nasiel znacku
      dump($rozloz);
      foreach ($rozloz as $k => $cast) {
        if (substr($cast, 0, 2) == "I-") {
          $obr = $this->dokumenty->find((int)substr($cast, 2));
          if ($obr !== FALSE) {
            $cast = Utils\Html::el('a class="fotky" rel="fotky"')->href($cesta . $obr->subor)
              ->title($obr->nazov)
              ->setHtml(Utils\Html::el('img')
                ->src($cesta . $obr->thumb)
                ->alt($obr->nazov));
          }
        }
        $vysledok .= $cast;
      }
    } else */
    $vysledok = $s;

    // koncova_znacka
    $rozloz = explode("{end}", $vysledok);
    if (count($rozloz) > 1) {    //Ak som nasiel znacku
      $vysledok = $rozloz[0]   // Podľa: https://getbootstrap.com/docs/4.6/components/collapse/#example
        . Utils\Html::el('a class="btn btn-link" data-toggle="collapse" aria-expanded="false" aria-controls="collapseArticle" id="colArt"')
        ->href("#collapseArticle")
        ->title($this->texty_presentera->translate("base_title"))
        ->setHtml('&gt;&gt;&gt; ' . $this->texty_presentera->translate("base_viac"))
        . Utils\Html::el('div class="collapse" id="collapseArticle"')
        ->setHtml($rozloz[1]);
    }
    // https://latte.nette.org/cs/develop#toc-vypnuti-auto-escapovani-promenne
    return new Runtime\Html($vysledok);
  }

  /** Render pre zobrazenie clanku */
  public function beforeRender()
  {
    parent::beforeRender();
    if ($this->zobraz_clanok !== FALSE) {
      $this->getComponent('menu')->selectByUrl($this->link('Clanky:', ["id" => $this->zobraz_clanok->id_hlavne_menu]));
      $this->template->komentare_povolene =  $this->udaje_webu["komentare"] && ($this->user->isAllowed('Front:Clanky', 'komentar') && $this->zobraz_clanok->hlavne_menu->komentar) ? $this->zobraz_clanok->id_hlavne_menu : 0;
      $this->template->article = $this->zobraz_clanok;
      $this->template->for_admin_link = ':Admin:' . $this->zobraz_clanok->hlavne_menu->druh->presenter . ':';

      //$this->template->article_text = new Runtime\Html($this->zobraz_clanok->text_c);
      $this->template->article_text = $this->_renderArticleText($this->zobraz_clanok->text_c);

      $this->template->uroven = $this->zobraz_clanok->hlavne_menu->uroven + 2;
      $this->template->avatar = $this->zobraz_clanok->hlavne_menu->avatar;
      $this->template->podclanky = $this->hlavne_menu->maPodradenu($this->zobraz_clanok->id);
      $this->template->view_submenu = $this->zobraz_clanok->hlavne_menu->id_hlavicka < 3;
      $this->template->viac_info = $this->texty_presentera->translate('clanky_viac_info');
      //Zisti, ci su k clanku priradene komponenty
      $this->template->komponenty = $this->clanok_komponenty->getKomponenty($this->zobraz_clanok->id_hlavne_menu, $this->nastavenie["komponenty"]);
      $this->template->id_hlavne_menu_lang = $this->zobraz_clanok->id;
      $this->template->article_hlavicka = $this->udaje->getValByName("clanok_hlavicka");
      $this->template->this_link = $this->link('this');

      $servise = $this;
      $this->template->addFilter('odkazdo', function ($id) use ($servise) {
        $serv = $servise->link("Dokumenty:default", ["id" => $id]);
        return $serv;
      });
      $this->template->addFilter('tojson', function ($data) {
        return Nette\Utils\Json::encode($data);
      });
    }
  }

  /** Render v prípade nenájdenia článku */
  public function renderNotFound()
  {
    $this->getHttpResponse()->setCode(\Nette\Http\IResponse::S404_NotFound);
  }

  /** 
   * Komponenta pre komentare k clanku */
  public function createComponentKomentar(): Multiplier
  {
    return new Multiplier(function ($id_hlavne_menu) {
      $komentar = $this->komentarControlFactory->create();
      $komentar->setParametre($id_hlavne_menu);
      return $komentar;
    });
  }

  /**
   * Komponenta pre zobrazenie clanku */
  public function createComponentUkazTentoClanok(): Components\Clanky\ZobrazClanok\ZobrazClanokControl
  {
    $ukaz_clanok = $this->zobrazClanokControlFactory->create();
    $ukaz_clanok->setArticle($this->zobraz_clanok)
      ->setLanguage($this->language_id)
      ->setClanokHlavicka($this->udaje_webu['clanok_hlavicka']);
    return $ukaz_clanok;
  }

  /** 
   * Komponenta pre zobrazenie priloh */
  public function createComponentAttachments(): Components\Clanky\Attachments\AttachmentsControl
  {
    $attachments = $this->attachmentsClanokControlFactory->create();
    $attachments->setNastav($this->zobraz_clanok, $this->language);
    return $attachments;
  }

  /** 
   * Komponenta pre zobrazenie produktov */
  public function createComponentViewProducts(): Components\Products\ProductsViewControl
  {
    $products = $this->productsViewControlFactory->create();
    $products->setNastav($this->zobraz_clanok, $this->language_id, $this->nastavenie);
    return $products;
  }

  /** 
   * Komponenta pre zobrazenie faktur */
  public function createComponentViewFaktury(): Components\Faktury\ViewFakturyControl
  {
    $viewFaktury = $this->viewFakturyControlFactory->create();
    $viewFaktury->setSkupina($this->zobraz_clanok->id_hlavne_menu);
    return $viewFaktury;
  }

  /** 
   * Komponenta pre vykreslenie podclankov na kartach */
  /*public function createComponentZobrazKartyPodclankov(): Components\Clanky\ZobrazKartyPodclankov\ZobrazKartyPodclankovControl
  {
    $odkaz = $this->zobrazKartyPodclankovControlFactory->create();
    $odkaz->setArticle($this->zobraz_clanok->id_hlavne_menu, $this->language_id, $this->kotva);
    return $odkaz;
  }*/

  public function createComponentEditArticleTitleForm(): Form
  {
    $form = $this->editArticleTitleFormControlFactory->create($this->zobraz_clanok->id_hlavne_menu);
    $form['uloz']->onClick[] = function () {
      $this->flashMessage('Položka menu bola uložená!', 'success');
      $this->redirect('this');
    };
    return $form;
  }
}
