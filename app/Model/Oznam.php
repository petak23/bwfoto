<?php

namespace DbTable;

use Nette;
use Nette\Database;
use Nette\Utils\Random;
use Nette\Utils\Image;

/**
 * Model, ktory sa stara o tabulku oznam
 * 
 * Posledna zmena 05.01.2023
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.1.1
 */
class Oznam extends Table
{
  /** @var string */
  protected $tableName = 'oznam';

  /** Vypisanie vsetkych aktualnych oznamov
   * @param bool $usporiadanie Urcuje usporiadane podla datumu platnosti
   * @param int $id_user_roles Minimalna uroven registracie */
  public function aktualne(bool $usporiadanie = FALSE, int $id_user_roles = 0): Nette\Database\Table\Selection
  {
    return $this->findBy(["datum_platnosti >= '" . date("Y-m-d", strtotime("0 day")) . "'", "id_user_roles <= " . $id_user_roles])
      ->order('datum_platnosti ' . ($usporiadanie ? 'ASC' : 'DESC'));
  }

  /** 
   * Vrati uz neaktualne oznamy
   * @param int $id_user_roles Minimalna uroven registracie */
  public function neaktualne(int $id_user_roles = 0): Nette\Database\Table\Selection
  {
    return $this->findBy(["datum_platnosti < '" . date("Y-m-d", strtotime("0 day")) . "'", "id_user_roles <= " . $id_user_roles])->order('datum_platnosti DESC');
  }

  /** Vypisanie vsetkych oznamov aj s priznakom aktualnosti
   * @param bool $usporiadanie Urcuje usporiadane podla datumu platnosti */
  public function vsetky(bool $usporiadanie = FALSE): array
  {
    $oznamy = $this->findAll()->order('datum_platnosti ' . ($usporiadanie ? 'ASC' : 'DESC'));
    $out = [];
    foreach ($oznamy as $o) {
      $temp = ["oznam" => $o, "aktualny" => $o->datum_platnosti >= date("Y-m-d", strtotime("0 day"))];
      $out[] = $temp;
    }
    return $out;
  }

  /** Vymazanie oznamu
   * @param int $id Id oznamu
   * @throws Database\DriverException */
  public function vymazOznam(int $id): int
  {
    try {
      $this->connection->table('oznam_komentar')->where(['id_oznam' => $id])->delete();
      $this->connection->table('oznam_ucast')->where(['id_oznam' => $id])->delete();
      return $this->find($id)->delete();
    } catch (Database\DriverException $e) {
      throw new Database\DriverException('Chyba ulozenia: ' . $e->getMessage());
    }
  }

  /** Funkcia pre ulozenie oznamu
   * @throws Database\DriverException */
  public function ulozOznam(Nette\Utils\ArrayHash $values): ?Nette\Database\Table\ActiveRow
  {
    $val = clone $values;
    $id = isset($val->id) ? $val->id : 0;
    unset($val->id, $val->posli_news);
    try {
      return $this->uloz($val, $id);
    } catch (Database\DriverException $e) {
      throw new Database\DriverException('Chyba ulozenia: ' . $e->getMessage());
    }
  }

  /**
   * Ulozenie titulneho obrazku alebo ikonky alebo fa_class-u
   * @param Nette\Utils\ArrayHash $values
   * @param string $title_image_path
   * @param string $www_dir
   * @throws Database\DriverException */
  public function zmenTitleImage($values, $title_image_path, $www_dir)
  {
    $save_data = ['id_ikonka' => NULL, 'title_image' => NULL, 'title_fa_class' => NULL];
    if (!$values->title_image->error) {
      if ($values->title_image->isImage()) {
        $values->title_image = $this->_uploadTitleImage($values->title_image, $www_dir . "/www/" . $title_image_path);
        $this->uloz(array_merge($save_data, ['title_image' => $values->title_image]), $values->id);
      } else {
        throw new Database\DriverException('Pre titulný obrázok nebol použitý obrázok a tak nebol uložený!');
      }
    } elseif ($values->id_ikonka) {
      $this->_delAvatar($values->old_title_image, $title_image_path, $www_dir);
      $this->uloz(array_merge($save_data, ['id_ikonka' => $values->id_ikonka]), $values->id);
    } elseif ($values->title_fa_class) {
      $this->_delAvatar($values->old_title_image, $title_image_path, $www_dir);
      $this->uloz(array_merge($save_data, ['title_fa_class' => $values->title_fa_class]), $values->id);
    } else {
      throw new Database\DriverException('Pri pokuse o uloženie došlo k chybe! Pravdepodobná príčina je č.' . $values->title_image->error . ".");
    }
  }

  /**
   * Zmazanie titulneho obrazku a/alebo ikonky */
  public function zmazTitleImage(int $id, string $title_image_path, string $www_dir): ?Nette\Database\Table\ActiveRow
  {
    $hl = $this->find($id);
    $this->_delTitleImage($hl->title_image, $title_image_path, $www_dir);
    return $this->uloz(["ikonka" => NULL, "avatar" => NULL], $id);
  }

  private function _uploadTitleImage(Nette\Http\FileUpload $title_image, string $path): string
  {
    $pi = pathinfo($title_image->getSanitizedName());
    $ext = $pi['extension'];
    $title_image_name = Random::generate(15) . "." . $ext;
    $title_image->move($path . $title_image_name);
    $image = Image::fromFile($path . $title_image_name);
    $image->save($path . $title_image_name, 75);
    return $title_image_name;
  }

  private function _delTitleImage(string $title_image_name, string $title_image_path, string $www_dir): void
  {
    if ($title_image_name !== NULL && is_file("www/" . $title_image_path . $title_image_name)) {
      unlink($www_dir . "/www/" . $title_image_path . $title_image_name);
    }
  }
}
