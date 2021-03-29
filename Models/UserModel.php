<?php


namespace Hyperion\API;
use \PDOStatement;
require_once "Model.php";

class UserModel extends Model{
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
	 * @param int $id
	 * @return bool
	 */
	public function update(int $id, array $value)
	{
		// TODO: Implement update() method.
	}
	/**
	 * @return mixed
	 */
	public function insert(array $value)
	{
		// TODO: Implement insert() method.
	}
	/**
	 * @return mixed
	 */
	public function delete(int $id)
	{
		// TODO: Implement delete() method.
	}

	/**
	 * @return mixed
	 */
	public function selectAll()
	{
		// TODO: Implement selectAll() method.
	}
}