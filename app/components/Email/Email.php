<?php
namespace PeterVojtech\Email;
use Nette;
use Latte;
use DbTable;

/**
 * Komponenta pre zjedndusenie odoslania emailu
 * Posledna zmena(last change): 30.09.2021
 * 
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com> 
 * @copyright  Copyright (c) 2012 - 2021 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.0.9
 */

class EmailControl extends Nette\Application\UI\Control {
  
  /** @var Nette\Mail\Message */
  private $mail;
  /** @var string */
  private $email_list;
  /** @var string */
  private $template;
  /** @var string */
  private $from;
  /** @var DbTable\User_main */
  public $user_main;

  /**
   * @param DbTable\User_main $user_main */
  public function __construct(DbTable\User_main $user_main) {
    $this->mail = new Nette\Mail\Message;
    $this->user_main = $user_main;
  }
  
  /** 
   * @param string $template Kompletná cesta k súboru template
   * @param int $id_from Od koho
   * @param int $id_user_roles Minimalna uroven registracie
   * @return \PeterVojtech\Email\EmailControl */
  public function nastav(string $template, int $id_from, int $id_user_roles) {
    $this->template = $template;
    $this->from = $this->user_main->find($id_from)->email;
    $this->email_list = $this->user_main->emailUsersListStr($id_user_roles);
    foreach (explode(",", $this->email_list) as $c) {
      $this->mail->addTo(trim($c));
    }
    return $this;
  }
  
  /** Funkcia pre odoslanie emailu
   * @param array $params Parametre správy
   * @param string $subjekt Subjekt emailu
   * @return string Zoznam komu bol odoslany email
   * @throws SendException
   */
  public function send($params, $subjekt) {
    $templ = new Latte\Engine;
    $this->mail->setFrom($params["site_name"].' <'.  $this->from.'>');
    $this->mail->setSubject($subjekt)
         ->setHtmlBody($templ->renderToString($this->template, $params));
    try {
      $sendmail = new Nette\Mail\SendmailMailer;
      $sendmail->send($this->mail);
      return $this->email_list;
    } catch (Nette\Mail\SendException $e) {
      throw new SendException('Došlo k chybe pri odosielaní e-mailu. Skúste neskôr znovu...'.$e->getMessage());
    }
  }
}

interface IEmailControl {
  /** @return EmailControl */
  function create();
}

class SendException extends \Exception
{
}