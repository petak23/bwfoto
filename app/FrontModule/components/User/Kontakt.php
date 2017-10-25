<?php
namespace App\FrontModule\Components\User;

use Language_support;
use Latte;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;

/**
 * Komponenta pre vytvorenie kontaktneho formulara a odoslanie e-mailu
 * 
 * Posledna zmena(last change): 24.10.2017
 *
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2017 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.0.4
 */

class KontaktControl extends Control {
  /** @var string */
  private $email_to_send = "";
  /** @var int */
  private $textA_rows = 8;
  /** @var int */
  private $textA_cols = 60;
	
  /** @var Language_support\Clanky */
	protected $texts;
  
  /** @var string */
  private $nazov_stranky = "";
  
  /** @param Language_support\Clanky $texts */
  public function __construct(Language_support\Clanky $texts) {
    parent::__construct();
    $this->texts = $texts;
    $this->texts->setLanguage("sk");
  }

  /** Funkcia pre nastavenie 
   * @param int $rows - pocet riadkov textarea
   * @param int $cols - pocet stlpcov textarea */
  public function setNastav($language = 'sk', $rows = 8, $cols = 60) {
    $this->texts->setLanguage($language);
    $this->textA_rows = (int)$rows;
    $this->textA_cols = (int)$cols;
    return $this;
  }

  /** Funkcia pre nastavenie emailovej adriesy, na ktoru sa odosle formular
	 * @param string $email emailova adresa */
  public function setEmailsToSend($email) {
    if (isset($email)) { $this->email_to_send = $email; }
    return $this;
  }
  
  /** Funkcia pre nastavenie nazvu stranky
	 * @param  string $nazov_stranky */
  public function setNazovStranky($nazov_stranky) {
    $this->nazov_stranky = $nazov_stranky;
    return $this;
  }

  /** @see Nette\Application\Control#render() */
  public function render() {
    $this->template->setFile(__DIR__ . '/Kontakt.latte');
    $this->template->texts = $this->texts;
    $this->template->render();
  }

  /** Kontaktny formular
   * @return Nette\Application\UI\Form */
  protected function createComponentKontaktForm() {
      $form = new Form;
      $form->addProtection();
      $form->addText('meno', $this->texts->trText('komponent_kontakt_meno'), 30, 50);
      $form->addText('email', $this->texts->trText('komponent_kontakt_email'), 30, 50)
         ->setType('email')
				 ->addRule(Form::EMAIL, $this->texts->trText('komponent_kontakt_email_ar'))
				 ->setRequired($this->texts->trText('komponent_kontakt_email_sr'));
      $form->addTextArea('text', $this->texts->trText('komponent_kontakt_text'))
           ->setAttribute('rows', $this->textA_rows)
           ->setAttribute('cols', $this->textA_cols)
           ->setRequired($this->texts->trText('komponent_kontakt_text_sr'));
      $renderer = $form->getRenderer();
      $renderer->wrappers['controls']['container'] = 'dl';
      $renderer->wrappers['pair']['container'] = NULL;
      $renderer->wrappers['label']['container'] = 'dt';
      $renderer->wrappers['control']['container'] = 'dd';
      $form->addSubmit('uloz', $this->texts->trText('komponent_kontakt_uloz'));
      $form->onSuccess[] = [$this, 'onZapisDotaz'];
      return $form;
  }

  /** Spracovanie formulara
   * @param \Nette\Application\UI\Form $form */
  public function onZapisDotaz(Form $form) {
    $values = $form->getValues();
    $templ = new Latte\Engine;
    $params = array(
      "nadpis"      => sprintf($this->texts->trText('komponent_kontakt_email_web'), $this->nazov_stranky),
      "dotaz_meno"  => sprintf($this->texts->trText('komponent_kontakt_email_msg'), $values->meno),
      "dotaz_text"  => $values->text,
    );
    $mail = new Message;
    $mail->setFrom($values->meno.' <'.$values->email.'>')
         ->addTo($this->email_to_send)
         ->setSubject(sprintf($this->texts->trText('komponent_kontakt_email_web'), $this->nazov_stranky))
         ->setHtmlBody($templ->renderToString(__DIR__ . '/Kontakt_email-html.latte', $params));
    try {
      $sendmail = new SendmailMailer;
      $sendmail->send($mail);
      $this->presenter->flashMessage($this->texts->trText('komponent_kontakt_send_ok'), 'success');
    } catch (Exception $e) {
      $this->presenter->flashMessage($this->texts->trText('komponent_kontakt_send_er').$e->getMessage(), 'danger,n');
    }
    $this->redirect('this');
  }
}


interface IKontaktControl {
  /** @return KontaktControl */
  function create();
}