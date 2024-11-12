<?php

namespace App\ApiModule\Presenters;

use DbTable;
use Nette\Utils\Strings;

/**
 * Domáci presenter pre API.
 * Posledna zmena(last change): 12.11.2024
 *
 * Modul: API
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.0.6
 * 
 * @help 1.) https://forum.nette.org/cs/28370-data-z-post-request-body-reactjs-appka-se-po-ceste-do-php-ztrati
 */
class HomepagePresenter extends BasePresenter
{
	// -- DB
	/** @var DbTable\User_main @inject */
	public $user_main;
	/** @var DbTable\News @inject */
  public $news;

	public function actionMyAppSettings(): void
	{
		$hl_m_db_info = $this->user_main->getDBInfo();
		$adminerLink = $this->user->isLoggedIn() && $this->user->isInRole('admin') ?
			$this->template->baseUrl . "/www/adminer/?server=" . $hl_m_db_info['host'] . "&db=" . $hl_m_db_info['dbname'] : null;

		$admin = $this->user_main->findOneBy(['user_roles.role'=>'admin']);	

		$spravca = $this->user_main->findOneBy(['user_roles.role'=>'manager']);

		$out = array_merge($this->udaje_webu, [
				'config'				=> $this->nastavenie,
				'basePath'			=> $this->template->basePath,
				'adminLink'			=> $this->user->isAllowed('Admin:Homepage', 'default') ? $this->link(':Admin:Homepage:') : null,
				'adminerLink'		=> $adminerLink,
				'last_change'		=> $this->verzie->posledna()->modified->format('j.n.Y'),
				'user_admin' 		=> ['name' => $admin->name,
														'email'=> $admin->email,
														'email_u'	=> Strings::replace($admin->email, ['~@~' => '[@]', '~\.~' => '[dot]'])
													],
				'user_spravca'	=> $spravca != null && $admin->id != $spravca->id ? [
														'name' => $spravca->name,
														'email'=> $spravca->email,
														'email_u'	=> Strings::replace($spravca->email, ['~@~' => '[@]', '~\.~' => '[dot]'])
														] : null,
			]);

		$this->sendJson($out);
	}

	/** 
	 * Akcia pre vrátenie posledných noviniek 
	 * @param int $limit max. počet vrátených noviniek */
	public function actionGetNews(int $limit = 5) : void {
		$out = [];
		foreach ($this->news->findAll()->order("created DESC")->limit($limit) as $value) {
			$out[] = [
				'id'	=> $value->id,
				'text'	=> $value->text,
				'created' => $value->created->format('j.n.Y')
			];
		}
		$this->sendJson($out);
	}

	public function actionUpdateNews(int $id) : void {
		/* from POST: */
		$values = json_decode(file_get_contents("php://input"), true); // @help 1.)

		$this->news->uloz($values, $id);
		$this->actionGetNews();
	}

	public function actionDeleteNews(int $id) : void {
		if ($id > 0 && $this->user->isLoggedIn() && $this->user->isAllowed("Api:Homepage", "delete")) {
			$del = $this->news->find($id)->delete();
			$this->actionGetNews();
		} else {
			$this->sendJson(['status' => 500]);
		}
	}
}
