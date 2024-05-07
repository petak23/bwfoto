<?php

namespace App\ApiModule\Presenters;

use DbTable;
use Nette\Utils\Random;
use PeterVojtech\Email;

/**
 * Prezenter pre pristup k api užívateľa.
 * Posledna zmena(last change): 24.04.2024
 *
 * Modul: API
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.0.4
 */
class UserPresenter extends BasePresenter
{

	// -- DB
	/** @var DbTable\User_prihlasenie @inject */
	public $user_prihlasenie;
	/** @var DbTable\User_main @inject */
	public $user_main;
	/** @var DbTable\User_profiles @inject */
	public $user_profiles;

	/** @var Email\EmailControl @inject */
	public $myMailer;

	/**
	 * Vráti posledné prihlásenia
	 * @param int $count Počet zobrazených prihlásení */
	public function actionGetLastLogin(int $count = 25): void
	{
		$this->sendJson($this->user_prihlasenie->getLastLogin($count, true));
	}

	/**
	 * Vymaže všetky záznamy o prihlásení z DB tab. user_prihlasenie
	 * Ak vracia 0 tak OK */
	public function actionDeleteAllLogin(): void
	{
		$this->sendJson(['result' => $this->user_prihlasenie->delAll()]);
	}

	/**
	 * Funkcia pre formulár na zostavenie zoznamu všetkých užívateľov
	 * Vráti pole uzivatelov vo formate: id => "meno priezvisko" 
	 * @param int id Minimálna úroveň registrácie */
	public function actionUserChangeFormUsers(int $id = 5): void
	{
		$this->sendJson($this->user_main->uzivateliaForm(1, $id));
	}

	public function actionGetActualUserInfo(): void
	{
		$this->getActualUserInfo();
	}

	public function actionGetActualUserProfile(): void
	{
		$this->sendJson($this->user_profiles->getProfile($this->user->getIdentity()->id_user_profiles));
	}

	/**
	 * Otestuje či je užívateľ prihlásený a či má oprávnenie na požadovanú operáciu
	 * $_post = ['resource', 'action'] */
	public function actionIsAllowed(int $id): void
	{
		$_post = json_decode(file_get_contents("php://input"), true);

		$allowed = $this->user->isLoggedIn()	// Kontrola prihlásenia
			&& $this->user->getId() == $id			// Kontrola užívateľovho id
			&& $this->user->isAllowed($_post['resource'], $_post['action']) ? 1 : 0; // Kontrola oprávnenia

		$this->sendJson(['result' => $allowed]);
	}

	/** Vráti údaje o užívateľovi bez hesla */
	public function actionGetUserInfo(int $id): void
	{
		$this->sendJson($this->user_main->getUserForApi($id));
	}

	public function actionTestUserEmail(): void
	{
		$_post = json_decode(file_get_contents("php://input"), true);

		$this->sendJson(['status' => $this->user_main->testEmail($_post['email']) ? 200 : 404]);
	}

	public function actionForgottenPassword() : void 
	{
		$_post = json_decode(file_get_contents("php://input"), true);

		if (!$this->user_main->testEmail($_post['email'])) {
			$out = ['status' => '404', 'message'=>"Email not found!"];
    } else {
			$user_info = $this->user_main->findOneBy(['email' => $_post['email']]);

			$new_password_key = Random::generate(25);
			if (isset($user_info->email) && $user_info->email == $_post['email']) { //Taky uzivatel existuje
				$params = [
					"site_name" => $this->nazov_stranky,
					"nadpis"    => sprintf($this->texty_presentera->translate('email_reset_nadpis'), $this->nazov_stranky),
					"email_reset_txt" => $this->texty_presentera->translate('email_reset_txt'),
					"email_nefunkcny_odkaz" => $this->texty_presentera->translate('email_nefunkcny_odkaz'),
					"email_pozdrav" => $this->texty_presentera->translate('email_pozdrav'),
					"nazov"     => $this->texty_presentera->translate('forgot_pass'),
					"odkaz"     => 'http://' . $this->nazov_stranky . $this->link(":Front:User:resetPassword", $user_info->id, $new_password_key),
					"basePath"	=> $this->template->basePath,
				];
				try {
					$this->myMailer->sendMail(1, $user_info->email, $this->texty_presentera->translate('forgot_pass'), null, $params, __DIR__ . '/../templates/emails/forgot_password.latte');
					$user_forg = $this->user_main->find($user_info->id);
					$user_forg->update([
						'new_password_key' => $new_password_key,
						'new_password_requested' => date("Y-m-d H:i:s", Time())
					]);
					$this->myMailer->sendAdminMail("Zabudnuté heslo", "Požiadavka na zabudnuté heslo užívateľa:" . $user_forg->name);
					$out = ['status' => '200', 'message'=>$this->texty_presentera->translate('forgot_pass_email_ok')];
				} catch (Email\SendException $e) {
					$out = ['status' => '404', 'message'=>$this->texty_presentera->translate('send_email_err')];
				}
			} else {                          //Taky uzivatel neexzistuje
				$out = [
					'status' => '404', 
					'message'=>sprintf($this->texty_presentera->translate('forgot_pass_user_err'), $user_info->email)
				];
			}
		}

		$this->sendJson($out);
	}
}
