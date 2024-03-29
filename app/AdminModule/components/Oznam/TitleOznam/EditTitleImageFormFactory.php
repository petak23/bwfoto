<?php

declare(strict_types=1);

namespace App\AdminModule\Components\Oznam\TitleOznam;

use DbTable;
use Nette\Application\UI\Form;
use Nette\Database;
use Nette\Utils\Html;

/**
 * Formular a jeho spracovanie pre pridanie a editaciu titulneho obrazku polozky.
 * 
 * Posledna zmena 04.01.2023
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.2
 */
class EditTitleImageFormFactory
{
  /** @var DbTable\Oznam */
  private $oznam;
  /** @var string */
  private $title_image_path;
  /** @var string */
  private $www_dir;
  /**
   * @param DbTable\Oznam $oznam */
  public function __construct(DbTable\Oznam $oznam)
  {
    $this->oznam = $oznam;
  }

  /**
   * Formular pre pridanie a editaciu titulneho obrazku polozky. */
  public function create(string $title_image_path, string $www_dir): Form
  {
    $this->title_image_path = $title_image_path;
    $this->www_dir = $www_dir;
    $form = new Form();
    $form->addProtection();
    $form->addHidden("id");
    $form->addHidden("old_title_image");
    $form->addGroup("Zvoľ čo ideš meniť:");
    $form->addRadioList('vyber', 'Zmeň:', [1 => "Ikonku", 2 => "Obrázok", 3 => "Ikonka Font Awesome"])
      ->addCondition(Form::EQUAL, 1)
      ->toggle("view_ikonka")
      ->endCondition()
      ->addCondition(Form::EQUAL, 2)
      ->toggle("view_title_image")
      ->endCondition()
      ->addCondition(Form::EQUAL, 3)
      ->toggle("view_fa_class");
    $form->addGroup("Obrázok")->setOption('container', Html::el('fieldset id=view_title_image'));
    $form->addUpload('title_image', 'Titulný obrázok')
      ->setOption('description', sprintf('Max veľkosť obrázka v bytoch %d kB', 300 * 1024 / 1000 /* v bytoch */))
      ->setRequired(FALSE)
      ->addRule(Form::MAX_FILE_SIZE, 'Max veľkosť obrázka v bytoch %d B', 300 * 1024 /* v bytoch */)
      ->addRule(Form::IMAGE, 'Titulný obrázok musí byť JPEG, PNG alebo GIF.');
    $form->addGroup("Ikonka")->setOption('container', Html::el('fieldset id=view_ikonka'));
    $form->addText('id_ikonka', 'Názov class ikonky pre FontAwesome:', 0, 30);
    $form->addGroup("");
    $form->addGroup("Ikonka Font Awesome")->setOption('container', Html::el('fieldset id=view_fa_class'));
    $form->addText('title_fa_class', 'Názov class ikonky pre FontAwesome:', 0, 30);
    $form->addGroup("");
    $form->addSubmit('uloz', 'Zmeň')
      ->setHtmlAttribute('class', 'btn btn-success')
      ->onClick[] = [$this, 'editTitleImageFormSubmitted'];
    $form->addSubmit('cancel', 'Cancel')
      ->setHtmlAttribute('class', 'btn btn-default')
      ->setHtmlAttribute('data-dismiss', 'modal')
      ->setHtmlAttribute('aria-label', 'Close')
      ->setValidationScope([]);
    return $form;
  }

  /** 
   * Spracovanie formulara pre zmenu titulneho obrazku.
   * @param \Nette\Forms\Controls\SubmitButton $button Data formulara 
   * @throws Database\DriverException   */
  public function editTitleImageFormSubmitted(\Nette\Forms\Controls\SubmitButton $button)
  {
    $values = $button->getForm()->getValues();   //Nacitanie hodnot formulara
    try {
      $this->oznam->zmenTitleImage($values, $this->title_image_path, $this->www_dir);
    } catch (Database\DriverException $e) {
      $button->addError($e->getMessage());
    }
  }
}
