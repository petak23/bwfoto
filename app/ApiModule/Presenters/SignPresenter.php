<?php

namespace App\ApiModule\Presenters;

use DbTable;
use Nette;
//use Firebase\JWT\JWT; // https://github.com/firebase/php-jwt
use Nette\Utils\Validators;

/**
 * Prezenter pre pristup k api prihlasovania a odhlasovania užívateľov.
 * Posledna zmena(last change): 30.07.2024
 *
 * Modul: API
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.0.2
 * @help 1.) https://forum.nette.org/cs/28370-data-z-post-request-body-reactjs-appka-se-po-ceste-do-php-ztrati
 * @help 2.) https://www.php.net/manual/en/function.checkdnsrr.php#48157
 */
class SignPresenter extends BasePresenter
{

	// -- DB
	/** @var DbTable\User_main @inject */
	public $user_main;

	/** Akcia pre prihlásenie */
	/*public function actionIn(): void
	{
		$_post = json_decode(file_get_contents("php://input"), true); // @help 1.)
		$email = isset($_post['email']) ? $_post['email'] : $this->pok_log["email"];
		$password = isset($_post['password']) ?	$_post['password'] : $this->pok_log["password"];

		try {
			$this->user->login($email, $password);

			$privateKey = openssl_pkey_get_private(
				file_get_contents(__DIR__ . '/../../ssl/private_key.pem'),
				$this->config->getPassPhase()
			);

			$user_data = $this->user_main->getUser(
				$this->user->getId(),
				$this->user,
				$this->template->baseUrl,
				true
			);
			// Payload data you want to include in the token
			$payload = [
				'user_id' => $user_data['id'],
				'email' => $user_data['email'],
				'exp' => time() + 7200, // Token expiration time (2 hour)
			];

			// Generate JWT token with private key
			$jwt = JWT::encode($payload, $privateKey, 'RS256');

			$httpResponse = $this->getHttpResponse();
			$httpResponse->addHeader('Access-Control-Allow-Origin', 'http://localhost:5173');
			$httpResponse->addHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, DELETE, PUT');
			//$httpResponse->addHeader('Access-Control-Allow-Headers', 'DNT,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type,Range,Authorization');
			//$httpResponse->addHeader("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
			$httpResponse->addHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');

			$this->sendJson([
				'token' => $jwt,
				'user_data' => $user_data,
			]);
		} catch (Nette\Security\AuthenticationException $e) {
			$this->sendJson(['error' => 'Uživateľské meno alebo heslo je nesprávne!!!']);
		}
	}*/


	public function actionIn(): void
	{
		$_post = json_decode(file_get_contents("php://input"), true); // @help 1.)

		try {
			/*if (!Validators::isEmail($_post['email'])) { 
				throw new Nette\InvalidArgumentException;
			}*/

			// Kontroly. Pri chybe vyhadzuje výnimku: Nette\Utils\AssertionException
			Validators::assert($_post['email'], 'email', 'Nesprávny tvar emailu'); // Kontrola, či bol zadaný email v správnom tvare

			/*if (!checkdnsrr(array_pop(explode("@", $_post['email'])), "MX")) { // @help 2.)
				throw new Nette\Utils\AssertionException('Daná doména pre email neexzistuje!');					// Kontrola, či daná doména existuje
			}*/

			Validators::assert($_post['password'], 'string:3..', 'Heslo musí mať viac ako 3 znaky!'); // Kontrola, či heslo má aspoň 3 znaky

			$this->user->login($_post['email'], $_post['password']);
			
			$message = "Užívateľ: '" . $this->user->getIdentity()->name . "' sa prihlásil"; 
			$this->myMailer->sendAdminMail("Prihlásenie", $message);

			$this->getActualUserInfo();
		} catch (Nette\Security\AuthenticationException $e) {
			$message = "Pokus o prihlásenie z emailu: '" . $_post['email'] . "' sa nepodaril z dôvodu: " . $e->getMessage();
			$this->myMailer->sendAdminMail("Neúspešné prihlásenie", $message);
			$this->sendJson(['status' => 500, 'error' => 'Uživateľské meno alebo heslo je nesprávne!!!']);
		} catch (Nette\Utils\AssertionException $e) {
			$this->sendJson(['status' => 500, 'error' => 'Zadajte hodnoty v správnom tvare!!!']);
		}
	}

	public function actionOut(): void
	{
		$this->user->logout(true);
		$this->sendJson(['status' => 200]);
	}
}
