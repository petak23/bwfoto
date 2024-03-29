<?php

declare(strict_types=1);

namespace App\AdminModule\Forms\Udaje;

use DbTable;
use Nette\Application\UI\Form;
use Nette\Database;
use Nette\Utils\Html;

/**
 * Tovarnicka pre formular pre pridanie/editaciu udaja
 * Posledna zmena 04.01.2023
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.4
 */
class EditUdajeFormFactory
{
  /** @var DbTable\Udaje */
  private $udaje;
  /** @array */
  private $ur_reg;

  /** @param DbTable\Udaje $udaje  */
  public function __construct(DbTable\Udaje $udaje)
  {
    $this->udaje = $udaje;
  }

  /**
   * Formular pre editaciu udajov */
  public function create(bool $admin, array $druh, array $ur_reg, array $editFormDefaults): Form
  {
    $this->ur_reg = $ur_reg;
    $form = new Form();
    $form->addProtection();
    $form->addGroup();
    $form->addHidden('id');
    $form->addHidden('id_udaje_typ');
    if ($admin) {
      $form->addText('nazov', 'Názov prvku:', 20, 20)
        ->addRule(Form::MIN_LENGTH, 'Názov musí mať spoň %d znaky!', 2)
        ->setHtmlAttribute('class', 'heading')
        ->setRequired('Názov musí byť zadaný!');
      $form->addText('comment', 'Komentár k hodnote :', 90, 255)
        ->addRule(Form::MIN_LENGTH, 'Komentár musí mať spoň %d znaky!', 2)
        ->setRequired('Komentár musí byť zadaný!');
    } else {
      $form->addHidden('nazov');
      $form->addHidden('comment');
    }
    if ($editFormDefaults['id_udaje_typ'] == 3) {
      $form->addCheckbox('text', 'Zvol platnosť prvku(áno/nie):');
    } elseif ($editFormDefaults['id_udaje_typ'] == 2) {
      $tb = strpos($editFormDefaults['comment'], '[');
      $po = explode(';', substr($editFormDefaults['comment'], $tb + 1, strpos($editFormDefaults['comment'], ']') - $tb - 1));
      $out = [];
      foreach ($po as $key => $v) {
        $tt = explode('=', $v);
        $out[(int)$tt[0]] = $tt[1];
      }
      $form->addRadioList('text', 'Vyber možnosť:', $out);
    } else {
      $form->addText('text', 'Hodnota prvku:', 90, 255)
        ->setRequired('Hodnota prvku musí byť zadaná!');
    }
    if ($admin) {
      $form->addCheckbox('spravca', ' Povolená zmena pre správcu')
        ->setDefaultValue(1);
      $form->addCheckbox("druh_null", " Hodnota druhu je NULL")
        ->setDefaultValue(1)
        ->addCondition(Form::EQUAL, TRUE)
        ->toggle("druh", FALSE);
      $form->addGroup()->setOption('container', Html::el('fieldset')->id("druh"));
      $form->addSelect('id_druh', 'Druhová skupina pre nastavenia:', $druh)
        ->setDefaultValue(1);
      $form->setCurrentGroup(NULL);
      $form->addCheckbox('separate_settings', ' Hodnota sa bude nastavovať v zvláštnej časti')
        ->setDefaultValue(0);
    }
    $form->setDefaults($editFormDefaults);
    $form->setDefaults([
      'spravca'   => $editFormDefaults['id_user_roles'] == $this->ur_reg['admin'] ? 0 : 1,
      'druh_null' => $editFormDefaults['id_druh'] == null ? 1 : 0,
    ]);
    $form->addSubmit('uloz', 'Ulož')
      ->setHtmlAttribute('class', 'btn btn-success')
      ->onClick[] = [$this, 'editUdajeFormSubmitted'];
    $form->addSubmit('cancel', 'Cancel')->setHtmlAttribute('class', 'btn btn-default')
      ->setValidationScope([]);
    return $form;
  }

  /** 
   * Spracovanie vstupov z formulara */
  public function editUdajeFormSubmitted(\Nette\Forms\Controls\SubmitButton $button)
  {
    try {
      $this->udaje->ulozUdaj($button->getForm()->getValues(), $this->ur_reg);
    } catch (Database\DriverException $e) {
      $button->addError($e->getMessage());
    }
  }
}
