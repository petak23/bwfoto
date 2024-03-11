<?php

namespace App\FrontModule\Forms\User;

use DbTable;
use Language_support;
use Nette\Application\UI\Form;
use Nette\Security;
//use Nette\Utils\Random;

/**
 * Registracny formular
 * Posledna zmena 11.03.2024
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.1.3
 */
class RegisterFormFactory
{
	/** @var Security\User */
	protected $user;
	/** @var Language_support\LanguageMain */
	private $texts;
	/** @var DbTable\User_main */
	private $user_main;
	/** @var DbTable\User_profiles */
	private $user_profiles;
	/** @var string */
	private $link_forgot;
	/** @var Security\Passwords */
	private $passwords;

	public function __construct(
		Security\User $user,
		Language_support\LanguageMain $language_main,
		DbTable\User_main $user_main,
		DbTable\User_profiles $user_profiles,
		Security\Passwords $passwords
	) {
		$this->user = $user;
		$this->texts = $language_main;
		$this->user_main = $user_main;
		$this->user_profiles = $user_profiles;
		$this->passwords = $passwords;
	}

	public function create(array $user_view_fields, string $link_forgot, string $language): Form
	{
		$this->link_forgot = $link_forgot;
		$this->texts->setLanguage($language);
		$form = new Form();
		$form->addProtection();
		$form->setTranslator($this->texts);
		$form->addText('name', 'RegistraciaForm_meno')
			->setHtmlAttribute('maxlength', 80)
			->addRule(Form::MIN_LENGTH, 'RegistraciaForm_meno_ar', 2)
			->setHtmlAttribute('autofocus', 'autofocus')
			->setRequired('RegistraciaForm_meno_sr');
		$form->addEmail('email', 'Form_email')
			->addRule(Form::EMAIL, 'Form_email_ar')
			->setRequired('Form_email_sr');
		$form->addPassword('heslo', 'RegistraciaForm_heslo')
			->addRule(Form::MIN_LENGTH, 'RegistraciaForm_heslo_ar', 5)
			->setRequired('RegistraciaForm_heslo_sr');
		$form->addPassword('heslo2', 'RegistraciaForm_heslo2')
			->setOmitted() // https://doc.nette.org/cs/3.1/form-presenter#toc-validacni-pravidla;
			->addRule(Form::EQUAL, 'RegistraciaForm_heslo2_ar', $form['heslo'])
			->setRequired('RegistraciaForm_heslo2_sr');
		/*if ($user_view_fields["pohl"]) {
			$form->addSelect('pohl', 'RegistraciaForm_pohl', ['M' => 'RegistraciaForm_m', 'Z' => 'RegistraciaForm_z']);
		}
		/*		$form->addReCaptcha('recaptcha', $label = 'Captcha')
				 ->setRequired('Táto položka je požadovaná!')
				 ->setMessage('Are you a bot?');
		*/
		$form->onValidate[] = [$this, 'validateRegisterForm'];
		$form->addSubmit('uloz', 'RegistraciaForm_uloz')
			->setHtmlAttribute('class', 'btn btn-success');
		return $form;
	}

	/** Vlastná validácia pre RegisterForm */
	public function validateRegisterForm(Form $form, \stdClass $data): void
	{
		// Over, ci dany email uz existuje.
		if ($this->user_main->testEmail($data->email)) {
			$form["email"]->addError(sprintf($this->texts->translate('registracia_email_duble2'), $data->email, $this->link_forgot));
		}
	}

	/** 
	 *Ulozenie po registracii */
	public function registerFormSubmitted(Form $form, $values)
	{
		//try {
		//$new_password_key = Random::generate(25);
		if (($uloz_user_profiles = $this->user_profiles->uloz(['pohl' => isset($values->pohl) ? $values->pohl : 'Z'])) !== FALSE) { //Ulozenie v poriadku
			$this->user_main->uloz([
				'id_user_profiles' => $uloz_user_profiles['id'],
				'meno'      => $values->name,
				'password'  => $this->passwords->hash($values->heslo),
				'email'     => $values->email,
				'activated' => 0,
				'created'   => date("Y-m-d H:i:s", Time()),
			]);
		}
		//} catch (Security\AuthenticationException $e) {
		//$form->addError($this->texts->translate("SignInForm_error_".$e->getCode()));
		//}
	}
}
