<?php

namespace App\AdminModule\Forms\Products;

use DbTable;
use Nette\Application\UI\Form;
use Nette\Database;
use Nette\Security\User;

/**
 * Formular a jeho spracovanie pre editaciu nastaveni produktov
 * Posledna zmena 04.01.2023
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.4
 */
class SetupProductDataFormFactory
{

  /** @var DbTable\Udaje*/
  private $udaje;
  /** @var array */
  private $products_settings;
  /**
   * @param DbTable\Udaje $udaje
   * @param User $user */
  public function __construct(DbTable\Udaje $udaje, User $user)
  {
    $this->products_settings = $udaje->getDruh("Products", $user->getIdentity()->id_user_roles);
    $this->udaje = $udaje;
  }

  /** Formular pre pridanie prilohy a editaciu polozky. */
  public function create(): Form
  {
    $form = new Form();
    $form->addProtection();
    foreach ($this->products_settings as $ps) {
      if ($ps->udaje_typ->nazov == 'number') {
        $find_s = strpos($ps->comment, "[");
        $form_el = $form->addInteger($ps->nazov, ($find_s !== FALSE ? substr($ps->comment, 0, $find_s) : $ps->comment) . ":", 15)
          ->setHtmlAttribute('size', 15)
          ->setDefaultValue($ps->text);
        //Nájdi text s nastavenim min max v tvare sssss[xx,xx]...
        if ($find_s !== FALSE && ($find_e = strpos($ps->comment, "]")) !== FALSE && $find_s < $find_e) {
          $find_min_max = explode(",", substr($ps->comment, $find_s + 1, ($find_e - $find_s - 1)));
          $form_el->addRule($form::RANGE, 'Hodnota musí byť v rozsahu od %d do %d.', [$find_min_max[0], $find_min_max[1]]);
          $form_el->setOption('description', sprintf('Hodnota musí byť v rozsahu od %d do %d.', $find_min_max[0], $find_min_max[1]));
        }
      } else {
        $form_el = $form->addText($ps->nazov, $ps->comment, 55, 255);
      }
    }
    $form->addSubmit('uloz', 'Ulož')
      ->setHtmlAttribute('class', 'btn btn-success')
      ->onClick[] = [$this, 'formSubmitted'];
    $form->addSubmit('cancel', 'Cancel')
      ->setHtmlAttribute('class', 'btn btn-default')
      ->setHtmlAttribute('data-dismiss', 'modal')
      ->setHtmlAttribute('aria-label', 'Close')
      ->setValidationScope([]);
    return $form;
  }

  /** 
   * Spracovanie formulara.
   * @param Nette\Forms\Controls\SubmitButton $button Data formulara 
   * @throws Database\DriverException   */
  public function formSubmitted($button)
  {
    try {
      foreach ($button->getForm()->getValues() as $key => $value) {
        $this->udaje->editKey($key, $value);
      }
    } catch (Database\DriverException $e) {
      $button->addError($e->getMessage());
    }
  }
}
