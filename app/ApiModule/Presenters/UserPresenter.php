<?php

namespace App\ApiModule\Presenters;

use DbTable;
use Nette;
use Nette\Utils\Random;
use PeterVojtech\Email;

/**
 * Prezenter pre pristup k api užívateľa.
 * Posledna zmena(last change): 20.09.2024
 *
 * Modul: API
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.0.5
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

	/** @var Nette\Security\Passwords */
	private $passwords;

	public function __construct(array $parameters, Nette\Security\Passwords $passwords)
	{
		// Nastavenie z config-u
		$this->nastavenie = $parameters;
		$this->passwords = $passwords;
	}

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
			$message = "Pokus o obnovu hesla na neexistujúcom maily: '" . $_post['email'] . "'.";
			$this->myMailer->sendAdminMail("Zabudnuté heslo:", $message);
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
					"odkaz"     => $this->link("//:Front:User:resetPassword", $user_info->id, $new_password_key),
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
					$out = [
						'status' 	=> '200',
						'message'	=> $this->texty_presentera->translate('forgot_pass_email_ok'),
						'params'	=> $params
					];
				} catch (Email\SendException $e) {
					$out = ['status' => '404', 'message'=>$this->texty_presentera->translate('send_email_err')];
				}
			} else {                          //Taky uzivatel neexzistuje
				$out = [
					'status' => '404', 
					'message'=>sprintf($this->texty_presentera->translate('forgot_pass_user_err'), $user_info->email)
				];
				$message = "Pokus o obnovu hesla na neexistujúcom maily(2): '" . $_post['email'] . "'.";
				$this->myMailer->sendAdminMail("Zabudnuté heslo(2):", $message);
			}
		}

		$this->sendJson($out);
	}

	public function actionRegistration() {
		$_post = json_decode(file_get_contents("php://input"), true);
		$new_password_key = Random::generate(25);
		if ($this->user_main->testEmail($_post['email'])) {
			$message = "Pokus o registráciu na existujúci mail: '" . $_post['email'] . "'.";
			$this->myMailer->sendAdminMail("Registrácia:", $message);
			$out = ['status' => '404', 'message'=>"Email found!"];
    } else {
			if (($uloz_user_profiles = $this->user_profiles->uloz(['pohl' => isset($values->pohl) ? $values->pohl : 'Z'])) !== FALSE) { //Ulozenie v poriadku
				$user_reg = $this->user_main->uloz([ 
					'id_user_profiles' => $uloz_user_profiles['id'],
					'name'      => $_post['name'],
					'password'  => $this->passwords->hash($_post['password']),
					'email'     => $_post['email'],
					'activated' => 0,
					'created'   => date("Y-m-d H:i:s", Time()),
				]);

				$params = [
					"site_name" => $this->nazov_stranky,
					"nadpis"    => sprintf($this->texty_presentera->translate('email_activate_nadpis'),$this->nazov_stranky),
					"email_activate_txt" 		=> $this->texty_presentera->translate('email_activate_txt'),
					"email_nefunkcny_odkaz" => $this->texty_presentera->translate('email_nefunkcny_odkaz'),
					"email_pozdrav" => $this->texty_presentera->translate('email_pozdrav'),
					"nazov"     => $this->texty_presentera->translate('register_aktivacia'),
					"odkaz" 		=> $this->link("//:Front:User:activateUser", $user_reg->id, $new_password_key),
					"basePath"	=> $this->template->basePath,
					"podpis"		=> "echo-msz.eu",
					"logo"			=> $this->template->basePath . "/www/images/logo_CC.png"
				];
				try {
					$this->myMailer->sendMail(
						1, 
						$user_reg->email, 
						$this->texty_presentera->translate('register_aktivacia'), 
						null, 
						$params, __DIR__ . '/../templates/emails/email_activate.latte'
					);
					$user_reg->update([
						'new_password_key' => $new_password_key,
						'new_password_requested' => date("Y-m-d H:i:s", Time())
					]);
					$this->myMailer->sendAdminMail("Registrácia užívateľa", "Požiadavka na registráciu užívateľa:" . $user_reg->name);
					$out = [
						'status' 	=> '200', 
						'message'	=>$this->texty_presentera->translate('register_email_ok', ),
						'params'	=> $params
					];
				} catch (Email\SendException $e) {
					$out = ['status' => '404', 'message'=>$this->texty_presentera->translate('send_email_err')];
				}
			}
		}
		
		$this->sendJson($out);
	}

	/** 
	 * Akcia pre reset hesla pri zabudnutom hesle 
	 * @param int $id Id uzivatela pre reset hesla
	 * @param string $new_password_key Kontrolny retazec pre reset hesla */
	public function actionResetPassword(int $id, string $new_password_key): void {
		$_post = json_decode(file_get_contents("php://input"), true);

		
		//Vygeneruj kluc pre zmenu hesla
    /*$new_password = $this->passwords->hash($values->new_heslo);
    unset($values->new_heslo); //Len pre istotu
    $this->user_main->find($values->id)
      ->update([
        'password' => $new_password,
        'new_password_key' => NULL,
        'new_password_requested' => NULL
      ]);*/

		if (!isset($id) OR !isset($new_password_key)) {
			$this->flashRedirect('Homepage:', $this->texty_presentera->translate('reset_pass_err1'), 'danger');
		} else {
			$user_main_data = $this->user_main->find($id);
			if ($new_password_key == $user_main_data->new_password_key){ 
				$this->template->email = sprintf($this->texty_presentera->translate('reset_pass_email'), $user_main_data->email);
				$this["resetPasswordForm"]->setDefaults(["id"=>$id]); //Nastav vychodzie hodnoty
			} else { 
				$this->flashRedirect('Homepage:', $this->texty_presentera->translate('reset_pass_err'.($user_main_data->new_password_key == NULL ? '2' : '3')), 'danger');
			}
		}
	}

	/** Vráti údaje o užívateľovi bez hesla */
	public function actionGetUserNpk(int $id, string $new_password_key): void
	{
		$user_main_data = $this->user_main->getUserForApi($id);
		$ok = !isset($user_main_data['error']) && ($new_password_key == $user_main_data['new_password_key']);
		$out = [
			'status' => $ok ? '200' : '404',
			'params' => $user_main_data	
		];
		$this->sendJson($out);
	}
}