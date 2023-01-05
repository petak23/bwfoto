<?php

declare(strict_types=1);

namespace App\AdminModule\Forms\Products;

use DbTable;
use Nette\Application\UI\Form;
use Nette\Database;
use Nette\Security\User;
use Nette\Utils\Strings;
use Nette\Utils\Image;

/**
 * Formular a jeho spracovanie pre pridanie a editaciu prilohy polozky.
 * Posledna zmena 04.01.2023
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.5
 */
class EditProoductFormFactory
{

  /** @var DbTable\Products */
  private $products;
  /** @var string */
  private $products_dir;
  /** @var array */
  private $products_settings;
  /** @var int */
  private $id_user_main;
  /** @var string */
  private $wwwDir;
  /**
   * @param DbTable\Products $products
   * @param DbTable\Udaje $udaje
   * @param User $user
   * @param string $wwwDir */
  public function __construct(DbTable\Products $products, DbTable\Udaje $udaje, User $user, string $wwwDir = "")
  {
    $this->products = $products;
    $this->products_settings = $udaje->findBy(['id_druh' => 8])->fetchPairs('nazov', 'text');
    $this->id_user_main = $user->getId();
    $this->wwwDir = $wwwDir;
  }

  /**
   * Formular pre pridanie prilohy a editaciu polozky. */
  public function create(string $products_dir): Form
  {
    $this->products_dir = $products_dir;
    $ini_v = trim(ini_get("upload_max_filesize"));
    $s = ['g' => 1 << 30, 'm' => 1 << 20, 'k' => 1 << 10];
    $upload_size =  intval($ini_v) * ($s[strtolower(substr($ini_v, -1))] ?: 1);
    $form = new Form();
    $form->addProtection();
    $form->addHidden("id");
    $form->addHidden("id_hlavne_menu");
    $form->addHidden("id_user_roles");
    $form->addUpload('product', 'Pridaj obrázok produktu')
      ->setOption('description', sprintf('Max veľkosť obrázku produktu v bytoch %s kB', $upload_size / 1024))
      //         ->setRequired("Obrázky produktov musia byť vybrané!")
      ->setRequired(FALSE)
      ->setHtmlAttribute('accept', 'image/*')
      ->addCondition(Form::FILLED)
      ->addRule(Form::MAX_FILE_SIZE, 'Max veľkosť obrázku produktu je v bytoch %d B', $upload_size)
      ->addRule(Form::IMAGE, 'Musíte vybrať obrázky!');
    $form->addText('name', 'Názov produktu:', 55, 255)
      ->setOption('description', sprintf('Názov by mal mať aspoň %s znakov. Inak nebude akceptovaný a bude použitý názov súboru obrázku produktu!', 2));
    $form->addText('description', 'Podrobnejší popis produktu:', 55, 255)
      ->setOption('description', sprintf('Popis by mal mať aspoň %s znakov. Inak nebude akceptovaný!', 2));
    $form->addSubmit('uloz', 'Ulož')
      ->setHtmlAttribute('class', 'btn btn-success')
      ->onClick[] = [$this, 'productFormSubmitted'];
    $form->addSubmit('cancel', 'Cancel')
      ->setHtmlAttribute('class', 'btn btn-default')
      ->setHtmlAttribute('data-dismiss', 'modal')
      ->setHtmlAttribute('aria-label', 'Close')
      ->setValidationScope([]);
    return $form;
  }

  /** 
   * Spracovanie formulara.
   * @param \Nette\Forms\Controls\SubmitButton $button Data formulara 
   * @throws Database\DriverException   */
  public function productFormSubmitted(\Nette\Forms\Controls\SubmitButton $button)
  {
    $values = $button->getForm()->getValues();   //Nacitanie hodnot formulara
    try {
      $data = [
        'id_hlavne_menu'     => $values->id_hlavne_menu,
        'id_user_main'      => $this->id_user_main,
        'id_user_roles'     => $values->id_user_roles,
        'description'        => isset($values->description) && strlen($values->description) > 2 ? $values->description : NULL,
        'name'              => isset($values->name) ? $values->name : "",
      ];
      if ($values->product->error == 0) {
        $data = array_merge($data, $this->_uploadProduct($values));
      }
      $this->products->saveProduct($data, $values->id);
    } catch (Database\DriverException $e) {
      $button->addError($e->getMessage());
    }
  }

  /**
   * Upload prilohy */
  private function _uploadProduct(\Nette\Utils\ArrayHash $values): array
  {
    $pr = $this->products->find($values->id); //Zmazanie stareho obrazka
    if ($pr != null) {
      if (is_file($pr->main_file)) {
        unlink($this->wwwDir . "/" . $pr->main_file);
      }
      if (is_file($pr->thumb_file)) {
        unlink($this->wwwDir . "/" . $pr->thumb_file);
      }
    }
    $fileName = $values->product->getSanitizedName();
    $pi = pathinfo($fileName);
    $additionalToken = 0;
    //Najdi meno suboru
    if (file_exists($this->products_dir . $fileName)) {
      do {
        $additionalToken++;
      } while (file_exists($this->products_dir . $pi['filename'] . $additionalToken . "." . $pi['extension']));
    }
    $finalFileName = ($additionalToken == 0) ? $pi['filename'] : $pi['filename'] . $additionalToken;
    //Presun subor na finalne miesto a ak je to obrazok tak vytvor nahlad
    $values->product->move($this->products_dir . $finalFileName . "." . $pi['extension']);
    $image_name = $this->products_dir . $finalFileName . "." . $pi['extension'];
    $thumb_name = $this->products_dir . 'tb_' . $finalFileName . "." . $pi['extension'];
    $image = Image::fromFile($image_name);
    $image->resize($this->products_settings['product_main_x'], $this->products_settings['product_main_y'], Image::SHRINK_ONLY);
    $image->save($image_name, (int)$this->products_settings['product_main_quality']);
    copy($image_name, $thumb_name);
    $thumb = Image::fromFile($thumb_name);
    $thumb->resize($this->products_settings['product_thumb_x'], $this->products_settings['product_thumb_y'], Image::SHRINK_ONLY); //| Image::EXACT
    $thumb->save($thumb_name, (int)$this->products_settings['product_thumb_quality']);

    $name = isset($values->name) ? $values->name : "";
    return [
      'name' => strlen($name) > 2 ? $name : $finalFileName,
      'web_name'  => Strings::webalize($finalFileName),
      'main_file'  => $this->products_dir . $finalFileName . "." . $pi['extension'],
      'thumb_file' => $thumb_name,
    ];
  }
}
