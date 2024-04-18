<?php

declare(strict_types=1);

namespace DbTable;

use Nette;
use Nette\Database;
use Nette\Security\User;

/**
 * Model, ktory sa stara o tabulku user_main
 * 
 * Posledna zmena 13.03.2024
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.9
 */
class User_main extends Table
{
	const
		COLUMN_ID = 'id',
		COLUMN_ID_USER_ROLES = 'id_user_roles',
		COLUMN_ID_USER_PROFILES = 'id_user_profiles',
		COLUMN_PASSWORD_HASH = 'password',
		COLUMN_TITUL_PRED = 'titul_pred',
		COLUMN_NAME = 'name',
		COLUMN_TITUL_ZA = 'titul_za',
		COLUMN_EMAIL = 'email',
		COLUMN_ACTIVATED = 'activated',
		COLUMN_BANNED = 'banned',
		COLUMN_BAN_REASON = 'ban_reason',
		COLUMN_LAST_IP = 'last_ip',
		COLUMN_CREATED = 'created';

	/** @var string */
	protected $tableName = 'user_main';

	private $passwords;

	public function __construct(Nette\Security\Passwords $passwords, Database\Explorer $db)
	{
		$this->passwords = $passwords;
		$this->connection = $db;
	}

	/** Test existencie emailu */
	public function testEmail(string $email): bool
	{
		return $this->findBy([self::COLUMN_EMAIL => $email])->count() > 0 ? true : false;
	}

	/** Aktualizuje IP adresu posledneho prihlasenia */
	public function logLastIp(int $id, string $ip): bool
	{
		return $this->find($id)->update([self::COLUMN_LAST_IP => $ip]);
	}

	public function updateUserByShopping(
		string $name,
		string $email,
		string $password = null,
		int $activated = 0,
		int $role = 0,
		array $profile = []
	): Database\Table\ActiveRow {
		$u = $this->findOneBy([self::COLUMN_EMAIL => $email]);
		if ($u != null) {
			if ($u->name != $name) $u->update([self::COLUMN_NAME => $name]);
			$this->connection->table('user_profiles')->get($u->{self::COLUMN_ID_USER_PROFILES})->update($profile);
		} else {
			$u = $this->add($name, $email, $password, $activated, $role, $profile);
		}
		return $u;
	}

	/** Adds new user.
	 * @throws DuplicateEmailException */
	public function add(
		string $name,
		string $email,
		string $password = null,
		int $activated = 0,
		int $role = 0,
		array $profile = []
	): Database\Table\ActiveRow|int|bool {
		try {
			$user_profiles = $this->connection->table('user_profiles')->insert($profile);
			return $this->pridaj([
				self::COLUMN_NAME             => $name,
				self::COLUMN_PASSWORD_HASH    => $password !== null ? $this->passwords->needsRehash($password) : null,
				self::COLUMN_EMAIL            => $email,
				self::COLUMN_ID_USER_PROFILES => $user_profiles->id,
				self::COLUMN_ACTIVATED        => $activated,
				self::COLUMN_ID_USER_ROLES    => $role,
				self::COLUMN_CREATED          => date("Y-m-d H:i:s", Time()),
			]);
		} catch (Database\UniqueConstraintViolationException $e) {
			throw new DuplicateEmailException($e->getMessage());
		}
	}

	/**
	 * @throws Database\DriverException */
	public function saveUser(Nette\Utils\ArrayHash $values): Database\Table\ActiveRow|int|bool
	{
		try {
			$id = $values->{self::COLUMN_ID};
			if (isset($values->{self::COLUMN_BANNED}) && !$values->{self::COLUMN_BANNED}) {
				$values->offsetSet("ban_reason", NULL);
			}
			if (isset($values->{self::COLUMN_TITUL_PRED}) && !strlen($values->{self::COLUMN_TITUL_PRED})) {
				$values->offsetSet(self::COLUMN_TITUL_PRED, NULL);
			}
			if (isset($values->{self::COLUMN_TITUL_ZA}) && !strlen($values->{self::COLUMN_TITUL_ZA})) {
				$values->offsetSet(self::COLUMN_TITUL_ZA, NULL);
			}

			unset($values->id);
			return $this->uloz($values, $id);
		} catch (\Exception $e) {
			throw new Database\DriverException('Chyba ulozenia: ' . $e->getMessage());
		}
	}

	/**
	 * Funkcia pre formulár na zostavenie zoznamu všetkých užívateľov
	 * Vráti pole uzivatelov vo formate: id => "meno priezvisko" */
	public function uzivateliaForm(int $verzia = 0, int $min_reg = 0): array
	{
		$u = $this->findBy(['id_user_roles >= ?' => $min_reg])->order('id ASC');
		$out = [];
		foreach ($u as $v) {
			if ($verzia == 1) {
				$out[] = [
					'text' => $v->{self::COLUMN_NAME},
					'value' => $v->{self::COLUMN_ID},
				];
			} else {
				$out[$v->{self::COLUMN_ID}] = $v->{self::COLUMN_NAME};
			}
		}
		return $out;
	}

	/**
	 * Funkcia na zostavenie ratazca emailov podla urovne registracie pre odoslanie info mailu. 
	 * @param int $id_user_roles Minimalna uroven registracie
	 * Vráti retazec emailov oddelených ciarkami */
	public function emailUsersListStr(int $id_user_roles = 5): string
	{
		$cl = $this->findBy(['id_user_roles >=' . $id_user_roles, 'user_profiles.news' => 'A']);
		$out = "";
		$sum = count($cl);
		$iter = 0;
		foreach ($cl as $c) {
			$iter++;
			$out .= $sum == $iter ? $c->email : $c->email . ', ';
		}
		return $out;
	}

	/**
	 * Funkcia na zostavenie ratazca emailov podla urovne registracie pre odoslanie info mailu. 
	 * @param int $id_user_roles Minimalna uroven registracie
	 * Vráti pole id=>email */
	public function emailUsersListArray(int $id_user_roles = 5): array
	{
		$cl = $this->findBy(['id_user_roles >=' . $id_user_roles, 'user_profiles.news' => 'A']);
		$out = [];
		foreach ($cl as $c) {
			if ($c->email != '---') {
				$out[$c->id] = $c->email;
			}
		}
		return $out;
	}

	/**
	 * Najde id uzivatela podla parametrov 
	 * @param array $param Pole parametrov */
	public function findIdBy(array $param = []): int
	{
		return ($tmp = $this->findOneBy($param)) !== FALSE ? $tmp->{self::COLUMN_ID} : 0;
	}

	public function getUser(int $id): ?Database\Table\ActiveRow
	{
		return $this->find($id);
	}

	/**
	 * Nájdenie info o jednom užívateľovy
	 * @param int $id primary key
	 * @return Database\Table\ActiveRow|array */
	public function getUserForApi(int $id, bool $with_password = false): array
	{
		$usr = $this->find($id);
		if ($usr == null) return ['error' => "User not found", 'error_n' => 1, 'user_id' => $id];
		$_cols = $this->getTableColsInfo();
		$out = [];
		foreach ($_cols as $k => $v) {
			if ($usr->{$v['field']} !== null && $v['type'] == "datetime") {
				$out[$v['field']] = $usr->{$v['field']}->format('d.m.Y H:i:s');
			} else {
				$out[$v['field']] = $usr->{$v['field']};
			}
		}
		if (!$with_password) unset($out[self::COLUMN_PASSWORD_HASH]);
		return $out;
	}
}

class DuplicateEmailException extends \Exception
{
}
