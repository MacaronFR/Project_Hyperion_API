<?php


namespace Hyperion\API;
use \Exception;
require_once "Model.php";

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
		$fields = $this->prepare_fields($value);
		if($fields === false){
			return false;
		}
		$fields['id'] = $id;
		return $this->prepared_query(
			'UPDATE USERS SET firstname=:firstname, name=:name, green_coins=:gc, type=:type, mail=:mail,
			 last_login=:llog, account_creation=:ac_creation, address=:addr, password=:passwd WHERE id_user=:id;',
			$fields, fetch: false);
	}
	/**
	 * By providing a full collection of user attributes, create it
	 * @param array $value
	 * @return bool
	 */
	public function insert(array $value): bool{
		$fields = $this->prepare_fields($value);
		if($fields === false)
			return false;
		return $this->prepared_query("INSERT INTO USERS 
    (name, firstname, green_coins, type, mail, last_login, account_creation, address, password)
    VALUE (:name, :firstname, :gc, :type, :mail, :llog, :ac_creation, :addr, :passwd);", $fields, fetch: false);
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
		return $this->prepared_query("SELECT id_user, password, type FROM USERS WHERE mail=:mail", ['mail' => $mail], true);
	}
}