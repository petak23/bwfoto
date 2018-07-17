<?php
namespace App\FrontModule\Presenters;

use DbTable;
use Language_support;
use Latte;
use Nette\Application\UI\Form;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;
use Nette\Security\Passwords;
use Nette\Utils\Random;

/**
 * Prezenter pre spravu uzivatela po prihlásení.
 * (c) Ing. Peter VOJTECH ml.
 * Posledna zmena(last change): 17.06.2018
 *
 *	Modul: FRONT
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2018 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.1.5
 */
class UserLogPresenter extends BasePresenter {
  
  // -- DB
  /** @var DbTable\User_main @inject */
	public $user_main;
  /** @var DbTable\User_profiles @inject */
	public $user_profiles;
  /** @var Language_support\UserLog @inject */
  public $texty_presentera;
  
  /** @var \Nette\Database\Table\ActiveRow|FALSE */
  private $uzivatel;
  /** @var array Nastavenie zobrazovania volitelnych poloziek */
  private $user_view_fields;

  // -- Forms
  /** @var \App\FrontModule\Forms\UserLog\UserEditFormFactory @inject*/
	public $userEditFormFactory;

	protected function startup() {
    parent::startup();
    if ($this->action != 'activateNewEmail') {
      if (!$this->user->isLoggedIn()) { //Neprihlaseneho presmeruj
        $this->flashRedirect(['User:', ['backlink'=>$this->storeRequest()]], $this->trLang('base_nie_je_opravnenie1').'<br/>'.$this->trLang('base_prihlaste_sa'), 'danger,n');
      }
    }
    // Kontrola ACL
    if (!$this->user->isAllowed($this->name, $this->action)) {
      $this->flashRedirect('Homepage:', sprintf($this->trLang('base_nie_je_opravnenie'), $this->action), 'danger');
    }
    //Najdem aktualne prihlaseneho clena
    $this->uzivatel = $this->user_main->find($this->user->getIdentity()->getId());
    $this->user_view_fields = $this->nastavenie['user_view_fields'];
	}
  
  public function actionDefault() {
    $this["userEditForm"]->setDefaults($this->uzivatel);
    $this["userEditForm"]->setDefaults(['news'=> $this->uzivatel->user_profile->news]);
  }
  
  public function renderDefault() {
    $this->template->uzivatel = $this->uzivatel;
    $this->template->zdroj_na_zmazanie = $this->trLang('zdroj_na_zmazanie');
    $this->template->user_view_fields = $this->user_view_fields;
    $this->template->setTranslator($this->texty_presentera);
  }

  /**
	 * Edit user form component factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentUserEditForm() {
    $form = $this->userEditFormFactory->create($this->user_view_fields, $this->nastavenie, $this->template->basePath, $this->uzivatel);  
    $form['uloz']->onClick[] = function ($form) {
      $this->flashOut(!count($form->errors), 'UserLog:', $this->trLang('user_edit_save_ok'), $this->trLang('user_edit_save_err'));
		};
    $form['cancel']->onClick[] = function () {
			$this->redirect('UserLog:');
		};
    $form = $this->_vzhladForm($form);
    $renderer = $form->getRenderer();
    $renderer->wrappers['control']['container'] = 'div class="col-sm-8 control-field"';
    $renderer->wrappers['label']['container'] = 'div class="col-sm-4 control-label"';
		return $form;
	}
  
  public function actionMailChange() {
    $this->template->h2 = sprintf($this->trLang('mail_change_h2'),$this->uzivatel->meno, $this->uzivatel->priezvisko);
    $this->template->email = sprintf($this->trLang('mail_change_txt'),$this->uzivatel->email);
	}

	/**
	 * Mail change component factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentMailChangeForm() {
		$form = new Form();
		$form->addProtection();
    $form->addHidden('id', $this->uzivatel->id);
		$form->addPassword('heslo', $this->trLang('MailChangeForm_heslo'), 50, 80)
				 ->setRequired($this->trLang('MailChangeForm_heslo_sr'));
    $form->addText('email', $this->trLang('MailChangeForm_new_email'), 50, 80)
				 ->addRule(Form::EMAIL, $this->trLang('MailChangeForm_new_email_ar'))
				 ->setRequired($this->trLang('MailChangeForm_new_email_sr'));
    $form->addSubmit('uloz', $this->trLang('base_save'))
         ->setAttribute('class', 'btn btn-success')
         ->onClick[] = [$this, 'userMailChangeFormSubmitted'];
    $form->addSubmit('cancel', 'Cancel')
         ->setAttribute('class', 'btn btn-default')
         ->setValidationScope([])
         ->onClick[] = function () { $this->redirect('UserLog:');};
		return $this->_vzhladForm($form);
	}

	public function userMailChangeFormSubmitted($button) {
		$values = $button->getForm()->getValues(); 	//Nacitanie hodnot formulara
		if (!Passwords::verify($values->heslo, $this->uzivatel->password)) {
			$this->flashRedirect('this', $this->trLang('pass_incorect'), 'danger');
		}
    // Over, ci dany email uz existuje. Ak ano konaj.
    if ($this->user_main->testEmail($values->email)) {
      $this->flashMessage(sprintf($this->trLang('mail_change_email_duble'), $values->email), 'danger');
      return;
    }
    //Vygeneruj kluc pre zmenu e-mailu
    $email_key = Random::generate(25);//$this->hasser->HashPassword($values->email.StrFTime("%Y-%m-%d %H:%M:%S", Time()));
    $uzivatel = $this->user_main->find(1); //Najdenie odosielatela emailu
    $templ = new Latte\Engine;
    $params = [
      "site_name" => $this->nazov_stranky,
      "nadpis"    => sprintf($this->trLang('email_change_mail_nadpis'),$this->nazov_stranky),
      "email_change_mail_txt" => sprintf($this->trLang('email_change_mail_txt'),$this->nazov_stranky),
      "email_change_mail_txt1" => $this->trLang('email_change_mail_txt1'),
      "email_nefunkcny_odkaz" => $this->trLang('email_nefunkcny_odkaz'),
      "email_pozdrav" => $this->trLang('email_pozdrav'),
      "nazov"     => $this->trLang('mail_change'),
      "odkaz" 		=> 'http://'.$this->nazov_stranky.$this->link("UserLog:activateNewEmail", $this->uzivatel->id, $email_key),
    ];
    $mail = new Message;
    $mail->setFrom($this->nazov_stranky.' <'.$uzivatel->email.'>')
         ->addTo($values->email)
         ->setSubject($this->trLang('mail_change'))
         ->setHtmlBody($templ->renderToString(__DIR__ . '/templates/UserLog/email_change-html.latte', $params));
    try {
      $sendmail = new SendmailMailer;
      $sendmail->send($mail);
      $this->uzivatel->update(['new_email'=>$values->email, 'new_email_key'=>$email_key]);
      $this->flashRedirect('UserLog:', $this->trLang('mail_change_send_ok'), 'success');
    } catch (Exception $e) {
      $this->flashMessage($this->trLang('mail_change_send_err').$e->getMessage(), 'danger');
    } 
	}

  public function actionActivateNewEmail($id, $new_email_key) {
    $user_main_data = $this->user_main->find($id);
    if ($new_email_key == $user_main_data->new_email_key){ //Aktivacia prebeha v poriadku
      try {
        $this->user_main->uloz(['email'=>$user_main_data->new_email,
                                'new_email'=>NULL,
                                'new_email_key'=>NULL], $user_main_data->id);
        $this->flashMessage($this->trLang('activate_mail_ok').($this->user->isLoggedIn() ? '' : $this->trLang('activate_mail_login')), 'success');
      } catch (Exception $e) {
        $this->flashMessage($this->trLang('activate_mail_err').$e->getMessage(), 'danger,n');
      }
    } else { $this->flashMessage($this->trLang('activate_mail_err1'), 'danger'); } 	//Neuspesna aktivacia
    $this->redirect('Homepage:');
  }

  public function actionPasswordChange() {
    $this->template->h2 = sprintf($this->trLang('pass_change_h2'),$this->uzivatel->meno,$this->uzivatel->priezvisko);
	}

	/**
	 * Password change form component factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentPasswordChangeForm() {
		$form = new Form();
		$form->addProtection();
    $form->addHidden('id', $this->uzivatel->id);
		$form->addPassword('heslo', $this->trLang('PasswordChangeForm_heslo'), 50, 80)
				 ->setRequired($this->trLang('PasswordChangeForm_heslo_sr'));
    $form->addPassword('new_heslo', $this->trLang('PasswordChangeForm_new_heslo'), 50, 80)
         ->addRule(Form::MIN_LENGTH, $this->trLang('PasswordChangeForm_new_heslo_ar'), 3)
				 ->setRequired($this->trLang('PasswordChangeForm_new_heslo_sr'));
		$form->addPassword('new_heslo2', $this->trLang('PasswordChangeForm_new_heslo2'), 50, 80)
         ->addRule(Form::EQUAL, $this->trLang('PasswordChangeForm_new_heslo2_ar'), $form['new_heslo'])
				 ->setRequired($this->trLang('PasswordChangeForm_new_heslo2_sr'));
    $form->addSubmit('uloz', $this->trLang('base_save'))
         ->setAttribute('class', 'btn btn-success')
         ->onClick[] = [$this, 'userPasswordChangeFormSubmitted'];
    $form->addSubmit('cancel', 'Cancel')
         ->setAttribute('class', 'btn btn-default')
         ->setValidationScope([])
         ->onClick[] = function () { $this->redirect('UserLog:');};
		return $this->_vzhladForm($form);
	}

	public function userPasswordChangeFormSubmitted($button) {
		$values = $button->getForm()->getValues(); 	//Nacitanie hodnot formulara
		if ($values->new_heslo != $values->new_heslo2) {
			$this->flashRedirect('this', $this->trLang('PasswordChangeForm_new_heslo2_ar'), 'danger');
		}
		if (!Passwords::verify($values->heslo, $this->uzivatel->password)) {
			$this->flashRedirect('this', $this->trLang('pass_incorect'), 'danger');
		}
		//Vygeneruj kluc pre zmenu hesla
		$new_password = Passwords::hash($values->new_heslo);
    unset($values->new_heslo, $values->new_heslo2);
    try {
      $this->uzivatel->update(['password'=>$new_password]);
			$this->flashMessage($this->trLang('pass_change_ok'), 'success');
		} catch (Exception $e) {
			$this->flashMessage($this->trLang('pass_change_err').$e->getMessage(), 'danger,n');
		}
    $this->redirect('UserLog:');
	}
  
  /*********** signal processing ***********/
	function confirmedDelete($id, $nazov) {
    if (!$this->user_view_fields['delete']) {
      $this->flashRedirect("User:", $this->trLang('base_nie_je_opravnenie1'), "danger");
      return;
    }
		$path = $this->context->parameters['wwwDir'] . "/files/".$id;
    if (is_dir($path)) { //Vymazanie adresaru s avatarom
      foreach (glob("$path*.{jpg,jpeg,gif,png}", GLOB_BRACE) as $file) {
        @unlink($file);
      }
      rmdir($path);
    }
    $uzivatel_id_up = $this->user_profiles->findOneBy(['id_user_main'=>$id])->id;
    try {
      $this->getUser()->logout();
      $this->user_profiles->delUser($uzivatel_id_up);
      $this->user_main->oprav($uzivatel_id_up, ['id_user_profiles'=>1]);
      $this->user_profiles->zmaz($uzivatel_id_up);
      $this->user_main->zmaz($id);
      
      $this->flashMessage(sprintf($this->trLang('delete_user_ok'),$nazov), 'success');
      
		} catch (Exception $e) {
			$this->flashMessage($this->trLang('delete_user_err').$e->getMessage(), 'danger');
		}
    if (!$this->isAjax()) { $this->redirect('User:'); }
	}
}
