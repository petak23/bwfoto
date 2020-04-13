<?php
namespace DbTable;

use Nette;
use Nette\Database\Table\ActiveRow;
use Nette\Utils\Strings;


/**
 * Reprezentuje repozitar pre databázovu tabulku
 * 
 * Posledna zmena(last change): 13.04.2020
 * 
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2020 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version 1.1.3 
 *
 * @todo Pre verziu Nette 3 budu niektore funkcie vracat ActiveRow|null namiesto ActiveRow|false
 */
abstract class Table {

  use Nette\SmartObject;
  
  /** @var Nette\Database\Context */
  protected $connection;

  /** @var string */
  protected $tableName;
  
  /** @var Nette\Security\User */
  protected $user;

  /**
   * @param Nette\Database\Context $db
   * @throws Nette\InvalidStateException */
  public function __construct(Nette\Database\Context $db) {
    $this->connection = $db;
    if ($this->tableName === NULL) {
      $class = get_class($this);
      throw new Nette\InvalidStateException("Názov tabuľky musí byť definovaný v $class::\$tableName.");
    }
  }

  /** 
   * Vracia celu tabulku z DB
   * @return Nette\Database\Table\Selection */
  protected function getTable() {
      return $this->connection->table($this->tableName);
  }

  /** 
   * V poli vrati info o jednotlivych stlpcoch tabulky
   * @return array */
  public function getTableColsInfo(): array {
    $pom = $this->connection->getConnection()->getSupplementalDriver()->getColumns($this->tableName);
    $out = [];
    foreach ($pom as $key => $value) {
      $out[$key] = $value["vendor"];
    }
    return $out;
  }

  /** 
   * Funkcia v poli vrati zakladne info. o pripojeni.
   * @return array */
  public function getDBInfo(): array {
    $pom = explode(":", $this->connection->getConnection()->getDsn());
    $out = [];
    foreach (explode(";", $pom[1]) as $v) {
      $t = explode("=", $v);
      $out[$t[0]] = $t[1];
    }
    return $out;
  }
  
  /** 
   * Vracia vsetky zaznamy z DB
   * @return Nette\Database\Table\Selection */
  public function findAll() {
    return $this->getTable();
  }

  /** 
   * Vracia vyfiltrovane zaznamy na zaklade vstupneho pola
   * @param string|string[] $by 
   * @return Nette\Database\Table\Selection */
  public function findBy($by) {
    return $this->getTable()->where($by);
  }

  /** 
   * Rovnak ako findBy ale vracia len jeden zaznam
   * @param string|string[] $by
   * @return ActiveRow|false 
   * @todo Pre verziu Nette 3 bude vracat ActiveRow|null
   */
  public function findOneBy($by) {
    return $this->findBy($by)->limit(1)->fetch();
  }

  /** 
   * Vracia zaznam s danym primarnym klucom
   * @param mixed $id primary key
   * @return ActiveRow|false 
   * @todo Pre verziu Nette 3 bude vracat ActiveRow|null. Zmen find($id) na find($id): ?ActiveRow
   */
  public function find($id) {
    return $this->getTable()->get($id);
  }

  /** 
   * Hlada jednu polozku podla specifickeho nazvu a min. urovne registracie uzivatela
   * @param string $spec_nazov
   * @param int|NULL $id_reg
   * @return ActiveRow|false 
   * @todo Pre verziu Nette 3 bude vracat ActiveRow|null
   */
  public function hladaj_spec(string $spec_nazov, $id_reg = NULL) {
    if (!isset($spec_nazov)) { return false; } //Spec nazov nie je nastaveny
    return $this->findOneBy(isset($id_reg) ? ["spec_nazov"=>$spec_nazov, "id_user_roles <= ".$id_reg] : ["spec_nazov"=>$spec_nazov]);
  }

  /** 
   * Hlada jednu polozku podla id a min. urovne registracie uzivatela
   * @param int $id
   * @param int|NULL $id_reg
   * @return ActiveRow|false 
   * @todo Pre verziu Nette 3 bude vracat ActiveRow|null
   */
  public function hladaj_id(int $id = 0, $id_reg = NULL) {
    return (isset($id_reg)) ? $this->findOneBy(["id"=>$id, "id_user_roles <= ".$id_reg]) : $this->find($id);
  }

  /** 
   * Zmeni spec nazov na '-' ak min. uroven registracie uzivatela suhlasi
   * @param string $spec_nazov
   * @param int|NULL $id_reg */
  public function delSpecNazov(string $spec_nazov, $id_reg = NULL) {
    if (isset($spec_nazov)) { $this->hladaj_spec($spec_nazov, $id_reg)->update(['spec_nazov'=>'-']); }
  }

  /** 
   * Funkcia skontroluje a priradi specificky nazov pre polozku
   * @param string $nazov nazov clanku
	 * @return string */
	public function najdiSpecNazov(string $nazov): string {
    //Prevedie na tvar pre URL s tym, ze _ akceptuje
    $spec_nazov = Strings::webalize($nazov, '_');
    $pom = 0;
    if ($this->hladaj_spec($spec_nazov)) {
			do{
				$pom++;
			} while ($this->hladaj_spec($spec_nazov.$pom));
		}
    return $spec_nazov.($pom == 0 ? '' : $pom);
	}
  
  /** 
   * Prida zaznam(y) do tabulky
   * @param  array|\Traversable|Selection array($column => $value)|\Traversable|Selection for INSERT ... SELECT
   * @return ActiveRow|int|bool Returns IRow or number of affected rows for Selection or table without primary key */
  public function pridaj($data) {
    return $this->getTable()->insert($data);
  }

  /** 
   * Opravy v tabulke zaznam s danym id
   * @param mixed $id primary key
   * @param iterable (column => value)
   * @return ActiveRow */
  public function oprav($id, $data): ActiveRow {
    $this->find($id)->update($data);
    return $this->find($id);
  }

  /** 
   * Funkcia pridava alebo aktualizuje v DB podla toho, ci je zadané ID
   * @param iterable $data
   * @param mixed $id primary key
   * @return ActiveRow|int|bool */
  public function uloz($data, $id) {
    return (isset($id) && $id) ? $this->oprav($id, $data) : $this->pridaj($data);
  }
  
  /**
   * Zmaze v tabulke zaznam s danym id
   * @param mixed $id primary key
   * @return int return number of affected rows */
  public function zmaz($id): int {
    return isset($id) ? $this->find($id)->delete() : 0; 
  }
}