<?php

declare(strict_types=1);

namespace App\AdminModule\Components\Oznam\TitleOznam;

use DbTable;
use Nette\Application\UI\Form;
use Nette\Database;
use Nette\Utils\ArrayHash;
use Nette\Utils\Html;

/**
 * Formular a jeho spracovanie pre zmenu vlastnika polozky.
 * Posledna zmena 04.01.2023
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.4
 */
class ZmenPresmerovanieFormFactory
{
  /** @var DbTable\Hlavne_menu_lang */
  private $hlavne_menu_lang;
  /** @var DbTable\Udaje */
  private $udaje;

  /**
   * @param DbTable\Hlavne_menu_lang $hlavne_menu_lang
   * @param DbTable\Udaje $udaje */
  public function __construct(DbTable\Hlavne_menu_lang $hlavne_menu_lang, DbTable\Udaje $udaje)
  {
    $this->hlavne_menu_lang = $hlavne_menu_lang;
    $this->udaje = $udaje;
  }

  /**
   * Formular pre zmenu vlastnika polozky. */
  public function create(): Form
  {
    $presm = $this->udaje->getUdajInt('oznam_presmerovanie');
    $form = new Form();
    $form->addProtection();
    $form->addHidden("kluc", "oznam_presmerovanie");
    $form->addCheckbox("presmerovanie_not", " Presmerovanie nie je")
      ->setDefaultValue($presm == 0 ? 1 : 0)
      ->addCondition(Form::EQUAL, TRUE)
      ->toggle("presmeruj", FALSE);
    $form->addGroup()->setOption('container', Html::el('fieldset')->id("presmeruj"));
    $form->addSelect('clanok', 'Presmerovanie na:', $this->hlavne_menu_lang->findAll()->fetchPairs('id', 'view_name'))
      ->setDefaultValue($presm == 0 ? 1 : $presm);
    $form->setCurrentGroup(NULL);
    $form->addSubmit('uloz', 'Zmeň')
      ->onClick[] = [$this, 'zmenPresmerovanieFormSubmitted'];
    $form->addSubmit('cancel', 'Cancel')
      ->setHtmlAttribute('data-dismiss', 'modal')
      ->setHtmlAttribute('aria-label', 'Close')
      ->setValidationScope([]);
    return $this->makeBootstrap4($form);
  }

  /** 
   * Spracovanie formulara pre zmenu vlastnika clanku.
   * @param \Nette\Forms\Controls\SubmitButton $button Data formulara
   * @param ArrayHash $values */
  public function zmenPresmerovanieFormSubmitted(\Nette\Forms\Controls\SubmitButton $button, ArrayHash $values)
  {
    try {
      $cl = $values->presmerovanie_not == 1 ? 0 : $values->clanok;
      $this->udaje->editKey($values->kluc, $cl);
    } catch (Database\DriverException $e) {
      $button->addError($e->getMessage());
    }
  }

  /**
   * Vzhlad formularov v style Bootstrap 4 */
  function makeBootstrap4(Form $form): Form
  {
    $renderer = $form->getRenderer();
    $renderer->wrappers['controls']['container'] = null;
    $renderer->wrappers['pair']['container'] = 'div class="form-group"';
    $renderer->wrappers['pair']['.error'] = 'has-danger';
    $renderer->wrappers['control']['container'] = null;
    $renderer->wrappers['label']['container'] = null;
    $renderer->wrappers['control']['description'] = 'div class="help-block alert alert-info"';
    $renderer->wrappers['control']['errorcontainer'] = 'span class=form-control-feedback';
    $renderer->wrappers['control']['.error'] = 'is-invalid';

    foreach ($form->getControls() as $control) {
      $type = $control->getOption('type');
      if ($type === 'button') { //Prve tlacitko je success ostatne secondary ale len ak uz class nemaju
        if (empty($control->getControlPrototype()->getClass())) {
          $control->getControlPrototype()->addClass(empty($usedPrimary) ? 'btn btn-success' : 'btn btn-secondary');
        }
        $usedPrimary = true;
      } elseif (in_array($type, ['text', 'textarea', 'select'], true)) {
        $control->getControlPrototype()->addClass('form-control');
      } /*elseif ($type === 'file') {
        $control->getControlPrototype()->addClass('form-control-file');

      } elseif (in_array($type, ['checkbox', 'radio'], true)) {
        if ($control instanceof \Nette\Forms\Controls\Checkbox) {
          $control->getLabelPrototype()->addClass('form-check-label');
        } else { //radio
          $control->getItemLabelPrototype()->addClass('form-check-label');
          $control->getSeparatorPrototype()->setName('div')->addClass('form-check');
        }
        $control->getControlPrototype()->addClass('form-check-input');
        
      }*/
    }
    return $form;
  }
}
