<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use DbTable;
use Nette;
use Nette\Application\UI\Multiplier;
use PeterVojtech;

/**
 * Prezenter pre vypisanie clankov.
 * 
 * Posledna zmena(last change): 21.03.2023
 *
 *	Modul: FRONT
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.5.5
 */

class ClankyPresenter extends BasePresenter
{

	use PeterVojtech\Clanky\OdkazNaClanky\odkazNaClankyTrait;

	/** @var DbTable\Clanok_komponenty @inject*/
	public $clanok_komponenty;
	/** @var DbTable\Products @inject */
	public $products;

	/** @var Nette\Database\Table\ActiveRow|null */
	public $zobraz_clanok = null;
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
			if ($this->zobraz_clanok->hlavne_menu->redirect_id) { //Ak mám presmerovanie na podclanok
				$this->redirect("Clanky:", $this->zobraz_clanok->hlavne_menu->redirect_id);
			} elseif ($this->zobraz_clanok->hlavne_menu->id_nadradenej !== NULL) { //Ak mam v nadradenej polozke zobrazovanie podclankov na kartach
				$nadr = $this->clanok_komponenty->findBy(['id_hlavne_menu' => $this->zobraz_clanok->hlavne_menu->id_nadradenej, 'spec_nazov' => 'zobrazKartyPodclankov']);
				if (count($nadr)) {
					$this->redirect("Clanky:", [$this->zobraz_clanok->hlavne_menu->id_nadradenej, $this->zobraz_clanok->hlavne_menu->spec_nazov]);
				}
			}
		} catch (DbTable\ArticleMainMenuException $e) {
			if ($e->getCode() == 2) { //Article missing permissions
				$this->flashRedirect(
					["User:", ['backlink' => $this->storeRequest()]],
					$this->texty_presentera->translate('clanky_najdeny_neprihlaseny'),
					"warning"
				);
			} else {                  //Article not found
				$this->setView("notFound");
			}
		}
	}

	/** Render pre zobrazenie clanku */
	public function renderDefault()
	{
		$this->template->komentare_povolene =  $this->udaje_webu["komentare"] && ($this->user->isAllowed('Front:Clanky', 'komentar') && $this->zobraz_clanok->hlavne_menu->komentar) ? $this->zobraz_clanok->id_hlavne_menu : 0;
		$this->template->article = $this->zobraz_clanok;
		$this->template->for_admin_link = ':Admin:' . $this->zobraz_clanok->hlavne_menu->druh->presenter . ':';

		//Ak je v odkaze parameter z vyhľadávania...
		$this->template->first_id = isset($this->params['first_id']) ? $this->params['first_id'] : 0;

		//Id zobrazenej template
		$this->template->template_id = $this->zobraz_clanok->hlavne_menu->id_hlavne_menu_template;

		//Zisti, ci su k clanku priradene komponenty
		$this->template->komponenty = $this->clanok_komponenty->getKomponenty($this->zobraz_clanok->id_hlavne_menu, $this->nastavenie["komponenty"]);

		// Pre edit-article
		$this->template->article_hlavicka = $this->udaje->getValByName("clanok_hlavicka");
		$this->template->this_link = $this->link('this');
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
}
