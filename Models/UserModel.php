<?php


namespace Hyperion\API;

require_once "autoload.php";

class UserModel extends Model{

	protected string $table_name = "USERS";
	protected string $id_name = "id_user";
	protected array $column = [
		"name" => "name",
		"fname" => "firstname",
		"gc" => "green_coins",
		"type" => "type",
		"mail" => "mail",
		"llog" => "last_login",
		"ac_creation" => "account_creation",
		"addr" => "address",
		"passwd" => "password"
	];

	public function selectFromMail(string $mail): array|false{
		return $this->prepared_query("SELECT id_user, password, type, CONCAT(name, ' ', firstname) as name FROM USERS WHERE mail=:mail", ['mail' => $mail], true);
	}
}