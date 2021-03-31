<?php


namespace Hyperion\API;
use \Exception;
use JetBrains\PhpStorm\Language;
use function PHPUnit\Framework\isFalse;

require_once "autoload.php";

class UserModel extends Model{
	private function prepare_fields($value): array|false{
		try{
			return [
				'name' => $value['name'],
				'firstname' => $value['firstname'],
				'gc' => $value['green_coins'],
				'type' => $value['type'],
				'mail' => $value['mail'],
				'llog' => $value['last_login'],
				'ac_creation' => $value['account_creation'],
				'addr' => $value['address'],
				'passwd' => $value['password']
			];
		}catch(Exception){
			return false;
		}
	}
	private const INSERT = 1;
	private const UPDATE = 2;
	private function prepareColumnAndParameter(string $name): array|false{
		return match ($name) {
			"name" => ["name", "name"],
			"fname" => ["firstname", "fname"],
			"gc" => ["green_coins", "gc"],
			"type" => ["type", "type"],
			"mail" => ["mail", "mail"],
			"llog" => ["last_login", "llog"],
			"ac_creation" => ["account_creation", "ac_creation"],
			"addr" => ["address", "addr"],
			"passwd" => ["password", "passwd"],
			default => false,
		};
	}

	private function prepare_query_string(array $fields, int $type): string|false{
		return match($type){
			self::INSERT => $this->prepare_insert_query($fields),
			self::UPDATE => $this->prepare_update_query($fields),
			default => false
		};
	}

	private function prepare_insert_query(array $fields): string|false{
		$query = "INSERT INTO USERS ";
		$column = "(";
		$param = "(";
		foreach($fields as $name => $value){
			$tmp = $this->prepareColumnAndParameter($name);
			if($tmp === false){
				return false;
			}
			$column .= $tmp[0] . ", ";
			$param .= ":" . $tmp[1] . ", ";
		}
		$column = substr($column, 0, -2);
		$param = substr($param, 0, -2);
		$query .= $column . ") VALUES " . "$param" . ");";
		return $query;
	}
	private function prepare_update_query(array $fields): string|false{
		$query = "UPDATE USERS SET ";
		$arg = "";
		foreach($fields as $name => $value){
			$tmp = $this->prepareColumnAndParameter($name);
			if($tmp === false){
				return false;
			}
			$arg .= $tmp[0] . "=:" . $tmp[1] . ", ";
		}
		$arg = substr($arg, 0, -2);
		$query .= "$arg" . " WHERE id_user=:id;";
		return $query;
	}
	/**
	 * Retrieve user designated by $id or false if error occurs
	 * @param int $id User ID to retrieve
	 * @return array|false
	 */
	function select(int $id): array|false
	{
		return $this->prepared_query("SELECT * FROM USERS WHERE id_user=:id", ['id' => $id], true);
	}
	/**
	 * By providing full collection of user attributes, update it. Return false if no changes
	 * @param int $id User ID to update
	 * @param array $value Collection of attributes
	 * @return bool
	 */
	public function update(int $id, array $value): bool{
		$query = $this->prepare_query_string($value, self::UPDATE);
		if($query === false){
			return false;
		}
		$value['id'] = $id;
		return $this->prepared_query($query, $value, fetch: false);
	}
	/**
	 * By providing a full collection of user attributes, create it
	 * @param array $value
	 * @return bool
	 */
	public function insert(array $value): bool{
		$query = $this->prepare_query_string($value, self::INSERT);
		if($query === false)
			return false;
		return $this->prepared_query($query, $value, fetch: false);
	}
	/**
	 * Delete user designated by $id. Return true on success false on error
	 * @param int $id USer ID to delete
	 * @return bool
	 */
	public function delete(int $id): bool{
		return $this->prepared_query("DELETE FROM USERS WHERE id_user=:id", ["id" => $id], fetch: false);
	}

	/**
	 * select ALL users (limit to 500)
	 * @param int $iteration to have 500 next
	 * @return array|false
	 */
	public function selectAll(int $iteration = 0): array|false{
		$start = 500 * $iteration;
		$end = 500 * ($iteration + 1);
		return $this->query("SELECT * FROM USERS LIMIT ${start},${end}");
	}

	public function selectFromMail(string $mail): array|false{
		return $this->prepared_query("SELECT id_user, password, type, CONCAT(name, ' ', firstname) as name FROM USERS WHERE mail=:mail", ['mail' => $mail], true);
	}
}