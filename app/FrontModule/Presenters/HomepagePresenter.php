<?php

namespace App\FrontModule\Presenters;

/**
 * Prezenter pre homepage.
 * 
 * Posledna zmena(last change): 23.02.2024
 *
 *	Modul: FRONT
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.1.8
 */
class HomepagePresenter extends BasePresenter
{

	/** Vychodzie nestavenia */
	protected function startup()
	{
		parent::startup();
		//Len na to aby som vedel zobraziť odkaz na aktuality
		$this->template->aktuality = $this->hlavne_menu->findBy([
			"datum_platnosti >= '" . date("Y-m-d", strtotime("0 day")) . "'",
			"id_user_roles <= " . (($this->user->isLoggedIn()) ? $this->user->getIdentity()->id_user_roles : 0),
			"id_nadradenej = " . ($this->template->id_nadradeny_aktuality = 1)
		]);
	}

	/** Zakladna akcia */
	public function actionDefault()
	{
		//Ak je presmerovanie povolene v configu
		if ($this->nastavenie['homepage_redirect']) {
			$pom = explode(" ", $this->nastavenie['homepage_redirect']);
			if (count($pom) > 1) {
				$this->redirect(301, $pom[0], $pom[1]);
			} else {
				$this->redirect(301, $pom[0]);
			}
		}
	}

	/** Akcia pri presmerovani z nedovoleneho pristupu */
	public function actionNotAllowed()
	{
		$this->setView("default");
	}

	/** Zobrazí stránku vybraných obľúbených produktov */
	public function actionProductLikeView(): void
	{
	}

	/** Zobrazí stránku košíka produktov */
	public function renderBasketView(int $id = 1): void
	{
		$this->template->view_part = $id;
	}
}
