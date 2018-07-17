<?php

namespace Language_support;

use Nette;
use DbTable;

/**
 * Hlavna trieda pre podporu jazykov lang_supp_main pre presentre vo FrontModule.
 * 
 * Posledna zmena(last change): 12.06.2018
 * 
 * @author Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2018 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.1.3
 */
abstract class lang_supp_main implements Nette\Localization\ITranslator {
  use Nette\SmartObject;
  
  /** @var string Skratka jazyka */
  protected $jazyk = 'sk';
  /** @var DbTable\Lang */
	public $lang;
  /** @var array Samotne texty podla jazykov */
  protected $texty;
  /** @var array Konkretny jazyk pre vystup */
  protected $out_texty;
  /** @var array Vychodzie texty pre BasePresenter */
  private $texty_base = [
      'sk'=>[
        'base_lang_not_def'           => 'Jazyk, ktorý požadujete, nie je pre tento web definovaný!<br />Language you require is not defined for this site!',
        'base_nie_je_opravnenie'      => 'Na požadovanú akciu %s nemáte dostatočné oprávnenie!',
        'base_nie_je_opravnenie1'     => 'Nemáte dostatočné oprávnenie na danú operáciu!',
        'base_loged_in_bad'           => 'Daná akcia je určená pre neprihláseného užívateľa!',  
        'base_internal_error'         => 'Je nám ľúto, ale došlo k vnútornej chybe! Prosím skúste úpredošlú operáciu neskôr znovu...',
        'base_log_out_mess'           => 'Boli ste odhlásený...',
        'base_nette_title'            => 'Nette Framework - populárny nástroj pre vytváranie webových aplikácií v PHP.',
        'base_nette_powered'          => 'vytvorené v nette',  
        'base_link_to_admin_log_in'   => 'Prihlásenie pre administráciu webu',
        'base_delete_text'            => 'Naozaj chceš zmazať %s: %s ?',
        'base_SignInForm_email'       => 'Email:',
        'base_SignInForm_email_req'   => 'Uveďte, prosím, užívateľský email.',
        'base_SignInForm_password'    => 'Heslo:',
        'base_SignInForm_password_req'=> 'Uveďte, prosím, heslo.',
        'base_SignInForm_remember'    => 'Pamätať si ma',
        'base_SignInForm_login'       => 'Prihlásiť sa...',
        'base_AdminLink_name'         => "Administrácia",
        'base_login_ok'               => 'Úspešne ste sa prihlásili!',
        'base_login_error'            => 'Pri prihlásení došlo k chybe: %s',
        'base_prihlaste_sa'           => 'Prosím, prihláste sa!',
        'base_save'                   => 'Ulož',
        'base_save_ok'                => 'Údaje boli uložené v poriadku!',
        'base_form_required'          => '<span class="form_required">Červeno</span> označené položky sú povinné!',
        'base_not_found'              => "Položka sa žiaľ nenašla! Možná príčina: %s",
        'base_platnost_do'            => "Platnosť do: ",
        "base_platil_do"              => "Platil do: ",
        "base_neplatny"               => "Článok už nie je platný!",
        'base_zadal'                  => "Zadal: ",
        "base_zobrazeny"              => "Zobrazený: ",  
        "base_anotacia"               => "Anotácia:",
				"base_aktualny_projekt"       => "Aktuálny projekt",
        "base_aktualne_clanky"        => "Aktuálne",
        'base_viac'                   => "viac",
        "base_title"                  => "Zobrazenie celého obsahu.",
        'base_view_all'               => "Zobrazenie celého obsahu.",
        'base_text_title_image'       => "Titulný obrázok",
        "base_error_bad_link"         => "Pokúšate sa dostať na neexzistujúcu stránku!",
        "base_template_not_found"     => "Chyba zobrazenia! Nesprávny názov šablóny[%s]...",
        "base_search"                 => "Hľadaj",
        'komponent_kontakt_h4'        => 'Kontaktný formulár',
        'komponent_kontakt_uvod'      => '',
        'komponent_kontakt_header'    => "Nová správa pre: ",
        'komponent_kontakt_meno'      => "Vaše meno:",
        'komponent_kontakt_memo_sr'   => "Vaše meno musí byť zadané!",
        'komponent_kontakt_email'     => "Váš e-mail(aby sme mohli odpovedať...):",
        'komponent_kontakt_email_ar'	=> "Prosím, zadajte e-mail v správnom tvare. Napr. jano@hruska.com",
        'komponent_kontakt_email_sr'  => "Váš e-mail musí byť zadaný!",
        'komponent_kontakt_text'      => "Váš dotaz:",
        'komponent_kontakt_text_sr'   => "Text dotazu musí byť zadaný!",
        'komponent_kontakt_text_s'    => "Vaša správa:",
        'komponent_kontakt_text_s_sr' => "Text správy musí byť zadaný!",
        'komponent_kontakt_uloz'      => "Pošli dotaz",
        'komponent_kontakt_send_ok'   => "Váš dotaz bol zaslaný v poriadku. Ďakujeme za zaslanie dotazu.",
        'komponent_kontakt_send_er'   => "Váš dotaz nebol zaslaný!. Došlo k chybe. Prosím skúste to neskôr znovu.<br />",
        'komponent_kontakt_send_s'    => "Odošli správu",
        'komponent_kontakt_send_s_ok' => "Vaša správa bola odoslaná v poriadku.",
        'komponent_kontakt_send_s_er' => "Vaša správa nebola odoslaná!. Došlo k chybe. Prosím skúste to neskôr znovu.<br />",
        'komponent_kontakt_remember'  => "Zapamätať email",
        'komponent_kontakt_warning'   => "<strong>Pozor!</strong><br>Po odoslaní Vašej správy je požadované potvrdenie Vašej e-mailovej adresy. Ak zaškrtnete \"Zapamätať e-mail\" 
                                          tak pri najbližšom kontaktovaní už nebudete musieť e-mail potvrdzovať.",  
        'komponent_kontakt_email_web' => 'Správa z kontaktného formulará stránky %s', 
        'komponent_kontakt_email_msg' => 'Užívateľ %s poslal nasledujúcu správu:',
        'komponent_kontakt_verifi_su' => 'Overenie emailu z kontaktného formulára stránky %s',
        'komponent_kontakt_verifi_tx' => 'Ďakujeme, že ste nás konaktovali. Pre úspešné odoslanie správy na meno: %s je potrebné overiť Vašu emailovú adresu.<br /> 
                                          Toto urobíte po kliknutí na nasledujúci odkaz:',
        'komponent_kontakt_verifi_mg' => 'Vaša správa: ',
        'komponent_nahodna_notFound'  => 'Žiadna fotka sa nenašla!',
        'komponent_nahodna_h4'        => 'niečo z galérie ...',
        'base_kontakt_h2'             => 'Kontakt',
        'base_aktualne_h2'            => 'AKTUÁLNE',
        'base_jedalny_listok'         => 'Pozrieť jedálny lístok',
        "base_to_foto_galery"         => "Odkaz na fotogalériu:",
        "base_to_article"             => "Odkaz na článok:",
        'base_component_news_h4'      => "Novinky:",
        'base_nice_day'               => "Príjemný deň praje ",  
      ],
      'en'=>[
        'base_lang_not_def'           => 'Language you require is not defined for this site!',
        'base_nie_je_opravnenie1'     => 'You do not have enough privileges for this operation!',
        'base_prihlaste_sa'           => 'Please log in!',
        'base_nette_title'            => 'Nette Framework - popular framework for creating a web aplications in PHP.',
        'base_nette_powered'          => 'nette powered',
        'base_link_to_admin_log_in'   => 'Log in for administration of web',
        'base_SignInForm_email'       => 'Email:',
        'base_SignInForm_email_req'   => 'Please indicate email.',
        'base_SignInForm_password'    => 'Password:',
        'base_SignInForm_password_req'=> 'Please indicate the password.',
        'base_SignInForm_remember'    => 'Remember Me',
        'base_SignInForm_login'       => 'Login ...',
        'base_AdminLink_name'         => "Administration",
        'base_save'                   => 'Save',
        'base_not_found'              => "Article not found! msg: %s",
        'base_platnost_do'            => "Expiration date: ",
        'base_zadal'                  => "Entered: ",
        "base_zobrazeny"              => "Displayed: ",  
        "base_anotacia"               => "Annotation:",
        'base_viac'                   => "more",
        "base_title"                  => "Show more",
        'base_view_all'               => "View all content.",
        'base_text_title_image'       => "Title image",
        "base_template_not_found"     => "Display error! Incorect name of template[%s]...",
        "base_search"                 => "Search",
        'komponent_kontakt_h4'        => 'Contact form',
        'komponent_kontakt_uvod'      => '',
        'komponent_kontakt_header'    => "New message for: ",
        'komponent_kontakt_meno'      => "Your name:",
        'komponent_kontakt_memo_sr'   => "Your name must be entered!",
        'komponent_kontakt_email'     => 'Your e-mail',
        'komponent_kontakt_email_ar'	=> 'Please, write e-mail in correct form. For example: jano@hruska.com',
        'komponent_kontakt_email_sr'  => 'Your e-mail must be specified!',
        'komponent_kontakt_text'      => 'Your query:',
        'komponent_kontakt_text_sr'   => 'Text your query must be specified!',
        'komponent_kontakt_text_s'    => "Your message:",
        'komponent_kontakt_text_s_sr' => "Message text must be typed!",
        'komponent_kontakt_uloz'      => "Send query",
        'komponent_kontakt_send_ok'   => "Your query has been sent okay. Thank you for posting your query.",
        'komponent_kontakt_send_er'   => "Your query has been sent!. An error has occurred. Please try again later.<br />",
        'komponent_kontakt_send_s'    => "Send the message",
        'komponent_kontakt_send_s_ok' => "Your message has been sent in order.",
        'komponent_kontakt_send_s_er' => "Your message was not sent !. An error occurred. Please try again later.<br />",
        'komponent_kontakt_remember'  => "Remember email",          
        'komponent_kontakt_warning'   => "<strong>Warning!</strong><br>Once your message has been submitted, the confirmation of your email address is required. If you check \"Remember email\", you will no longer need to confirm your email at the next contact.",  
        'komponent_kontakt_email_web' => 'Message from contact form from web %s',   
        'komponent_kontakt_email_msg' => 'User %s send you next message:',
        'komponent_kontakt_verifi_su' => 'Verify email from the contact form of the page %s',
        'komponent_kontakt_verifi_tx' => 'Thank you for contacting us. To successfully send your message to name: %s, you need to verify your email address. <br />
                                          To do this, click the following link:',
        'komponent_kontakt_verifi_mg' => 'Your message: ',
        'komponent_nahodna_notFound'  => 'No photo could not be found!',
        'komponent_nahodna_h4'        => 'something from the gallery ...',
        'base_kontakt_h2'             => 'Contact',
        'base_aktualne_h2'            => 'NEWS',
        'base_component_news_h4'      => "News:",
        'base_nice_day'               => "Pleasant day wishes ",
      ],
      'de'=>[
        'base_aktualne_h2'            => "CURRENT",
        'base_viac'                   => "mehr",
        'base_view_all'               => "Sehen Sie den gesamten Inhalt.",
        'base_not_found'              => "Ihr Artikel wurde leider nicht gefunden! Mögliche Ursache:% s",
        'base_text_title_image'       => "Cover-Bild",
        'base_platnost_do'            => "Ablaufdatum: ",
        'base_zadal'                  => "Zugeordnet: ",
        'komponent_kontakt_header'    => "Neue Nachricht für: ",
        'komponent_kontakt_meno'      => "Ihr Name:",
        'komponent_kontakt_memo_sr'   => "Ihr Name muss eingegeben werden!",
        'komponent_kontakt_email'     => "Ihre E-Mailadresse:",
        'komponent_kontakt_email_ar'	=> "Bitte geben Sie Ihre E-Mail in der richtigen Form ein. Z.B. jano@hruska.com",
        'komponent_kontakt_email_sr'  => "Deine Email muss getippt werden!",
        'komponent_kontakt_text'      => "Ihre Frage:",
        'komponent_kontakt_text_s'    => "Ihre Nachricht:",
        'komponent_kontakt_text_s_sr' => "Nachrichtentext muss eingegeben werden!",
        'komponent_kontakt_send_s'    => "Sende die Nachricht",
        'komponent_kontakt_send_s_ok' => "Ihre Nachricht wurde der Reihe nach gesendet.",
        'komponent_kontakt_send_s_er' => "Ihre Nachricht wurde nicht gesendet! Ein Fehler ist aufgetreten. Bitte versuche es später erneut.<br />",
        'komponent_kontakt_remember'  => "Merken Sie sich die Email",          
        'komponent_kontakt_warning'   => "<strong>Achtung!</strong><br>Sobald Ihre Nachricht gesendet wurde, ist die Bestätigung Ihrer E-Mail-Adresse erforderlich. Wenn Sie \"Merken Sie sich die Email\" aktivieren, müssen Sie Ihre E-Mail beim nächsten Kontakt nicht mehr bestätigen.",  
        'komponent_kontakt_verifi_su' => 'Überprüfen Sie die E-Mail im Kontaktformular der Seite %s',
        'komponent_kontakt_verifi_tx' => 'Danke, dass Sie uns kontaktiert haben. Um Ihren Nachricht erfolgreich an Name: %s zu senden, müssen Sie Ihre E-Mail-Adresse bestätigen. <br />
                                          Klicken Sie dazu auf den folgenden Link:',
        'komponent_kontakt_verifi_mg' => 'Ihre Nachricht: ',
        'base_nice_day'               => "Angenehme Tagwünsche ",
      ],
    ];
  
  /** @param DbTable\Lang $lang */
  public function __construct(DbTable\Lang $lang) {
    $this->lang = $lang;
  }

  /** Funkcia na pridanie textu do pola
   * @param string $key Nazov kluca
   * @param string $text Hodnota kluca */
  public function addTexty($key, $text) {
    if (isset($this->texty[$this->language][$key])) {
      $this->texty[$this->language][$key.'a'] = $text;
    } else {
      $this->texty[$this->language][$key] = $text;
    }
  }

  /** Nastavenie aktualneho jazyka
   * @param string|int $language Skratka jazyka alebo jeho id*/
  public function setLanguage($language) {
    $this->jazyk = is_numeric($language) ? $this->lang->find($language)->skratka : $language;
    $this->out_texty = array_merge($this->texty_base[$this->jazyk], $this->texty[$this->jazyk]);
  }

  /** Vratenie textu pre dany kluc a jazyk
   * @param string $key Kluc daneho textu
   * @return string */
  public function trText($key) {
    return isset($this->out_texty[$key]) ? $this->out_texty[$key]
                                         : (isset($this->texty_base['sk'][$key]) ? $this->texty_base['sk'][$key] : $key);
  }
  
  public function translate($message, $count = null) {
    return isset($this->out_texty[$message]) ? $this->out_texty[$message]
                                         : (isset($this->texty_base['sk'][$message]) ? $this->texty_base['sk'][$message] : $message);
  }

  /** Vrati pole textov pre prislusny jazyk
   * @param string $language Skratka jazyka
   * @return array|FALSE */
  public function getLanguageArray($language = 'sk') {
    return (isset($this->texty_base[$language]) && isset($this->texty[$language])) ?
              array_merge($this->texty_base[$language], $this->texty[$language]) : FALSE;
  }
}
