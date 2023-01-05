<?php

namespace App\FrontModule\Components\Faktury;

use DbTable;
use Nette;
use Nette\Application\UI\Form;

/**
 * Komponenta pre zobrazenie casti faktur pre FRONT modul
 * 
 * Posledna zmena(last change): 04.01.2023
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.4
 */
class ViewFakturyControl extends Nette\Application\UI\Control
{
  /** @var DbTable\Faktury */
  private $faktury;

  /** @var DokumentFormFactory */
  public $dokumentFormFactory;
  /** @var int */
  private $skupina;
  /** @var boolean */
  private $zmluvy = FALSE;
  /** @var Nette\Security\User */
  private $user;
  /** @var int Id dokumentu */
  private $id_dokument = 0;

  public function __construct(DbTable\Faktury $faktury, DokumentFormFactory $dokumentFormFactory, Nette\Security\User $user)
  {
    parent::__construct();
    $this->faktury = $faktury;
    $this->dokumentFormFactory = $dokumentFormFactory;
    $this->user = $user;
  }

  /**
   * Nastavenie skupiny
   * @param int $skupina */
  public function setSkupina($skupina)
  {
    $this->skupina = $skupina;
    if ($this->skupina == 25) {
      $this->zmluvy = TRUE;
    }
  }

  /** 
   * Render 
   * @see Nette\Application\Control#render() */
  public function render()
  {
    $toIdDocument = explode("=", $this->presenter->httpRequest->getUrl()->query);
    if (isset($toIdDocument[1]) && $toIdDocument[0] == 'viewFaktury-id_dokument') {
      $this->id_dokument = $toIdDocument[1];
      $this->template->id_dokument = $this->id_dokument;
      $this["dokumentForm"]->setDefaults($this->faktury->find($this->id_dokument));
    }
    $this->template->setFile(__DIR__ . '/ViewFaktury.latte');
    $this->template->skupina = $this->skupina;
    $this->template->id_dokument = $this->id_dokument;
    $this->template->render();
  }
  /** Grid */
  public function createComponentFakturyGrid($name)
  {
    /*	$grid = new DataGrid($this, $name);
		$grid->setDataSource($this->faktury->findBy(['id_hlavne_menu'=>  $this->skupina])->order('datum_vystavenia DESC'));
		$grid->addColumnText('cislo', 'Číslo');
    if ($this->zmluvy) { 
      $grid->addColumnText('subjekt', "Zmluvná strana");
      $grid->addColumnText('nazov', 'Názov');
    }
    $grid->addColumnText('predmet', 'Predmet');
    if ($this->zmluvy) {
      $grid->addColumnNumber('cena', $this->zmluvy ? "Cena vrátane DPH" : "Suma")->setFormat(2,',','');
    }
    $grid->addColumnDateTime('datum_vystavenia', $this->zmluvy ? "Dátum uzatvorenia zmluvy" : "Dátum vystavenia");
    if ($this->zmluvy) { $grid->addColumnDateTime('datum_ukoncenia', 'Dátum ukončenia zmluvy');}
    $grid->addColumnLink('subor', 'Detail', 'dokumentOpen', 'subor', ['subor'])->setOpenInNewTab();
    if ($this->user->isAllowed('Front:Faktury', 'edit')) {
      $grid->addAction('id', 'Edit', 'Edit')->setIcon('pencil');
    }
    if ($this->user->isAllowed('Front:Faktury', 'del')) {
      $grid->addAction('delete', '', 'delete!')
      ->setIcon('trash')
      ->setTitle('Delete')
      ->setClass('btn btn-xs btn-danger')
      ->setConfirm('Naozaj chcete zmezať položku %s?', 'predmet');
    }
    $grid->setTranslator($this->translator);*/
  }

  /**
   * Signál pre otvorenie položky
   * @param int $subor Id otváranej položky */
  public function handleDokumentOpen($subor)
  {
    $this->presenter->redirectUrl("http://" . $this->presenter->nazov_stranky . "/www/files/dokumenty/" . $subor);
    exit;
  }

  /**
   * Signál pre editovanie položky
   * @param int $id Id editovanej položky */
  public function handleEdit($id)
  {
    $this->redirect('this', ['id_dokument' => $id]);
  }

  /**
   * Signál pre mazanie položky
   * @param int $id Id mazanej položky */
  public function handleDelete($id)
  {
    $pr = $this->faktury->find($id); //najdenie prislusnej polozky menu, ku ktorej priloha patri
    if ($pr !== FALSE) {
      $vysledok = is_file("www/files/dokumenty/" . $pr->subor) ? unlink($this->presenter->context->parameters["wwwDir"] . "/www/files/dokumenty/" . $pr->subor) : FALSE;
      $this->presenter->flashOut($vysledok ? $pr->delete() : FALSE, 'this', 'Položka bola vymazaná!', 'Došlo k chybe a položka nebola vymazaná!');
    } else {
      $this->presenter->flashRedirect('this', 'Došlo k chybe. Požadovaná položka sa nenašla a teda nebola vymazaná!', 'danger');
    }
  }


  protected function _formMessage(Form $form): Form
  {
    $form['uloz']->onClick[] = function ($button) {
      if (!count($button->getForm()->errors)) {
        $this->id_dokument = 0;
      }
      $this->presenter->flashOut(!count($button->getForm()->errors), 'this', 'Zmena bola úspešne uložená!', 'Došlo k chybe a zmena sa neuložila. Skúste neskôr znovu...');
    };
    return $this->presenter->_vzhladForm($form);
  }

  /** 
   * Komponenta formulara pre pridanie a editaciu dokumentu. */
  public function createComponentDokumentForm(): Form
  {
    return $this->_formMessage($this->dokumentFormFactory->create($this->skupina, $this->user->getId(), $this->presenter->upload_size, $this->presenter->context->parameters["wwwDir"]));
  }
}
interface IViewFakturyControl
{
  function create(): ViewFakturyControl;
}
