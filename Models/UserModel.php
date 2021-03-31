<?php


namespace Hyperion\API;
use JetBrains\PhpStorm\ArrayShape;

require_once "autoload.php";

class UserModel extends Model{

	protected string $table_name = "USERS";
	protected string $id_name = "id_user";
	/**
	 * Take param name and return array with Database column name and param name;
	 * @param string $name param name
	 * @return array|false
	 */
	#[ArrayShape([
		'string',
		'string'
	])]
	protected function prepare_column_and_parameter(string $name): array|false{
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
	 * @codeCoverageIgnore
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