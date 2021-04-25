<?php


namespace Hyperion\API;

require_once "autoload.php";

class TokenModel extends Model
{
	protected string $table_name = "TOKEN";
	protected string $id_name = "id_token";
	protected array $column = [
		"token" => "value",
		"scope" => "scope",
		"end" => "expire",
		"client" => "id_client",
		"user" => "id_user"
	];

	/**
	 * Select token by value
	 * @param string $token 64 character wide string equal to existing token in Database
	 * @return array|false
	 */
	public function selectByToken(string $token): array|false{
		return $this->prepared_query("SELECT id_token as id, id_user as user, id_client as client, scope as scope, expire as end FROM TOKEN WHERE value=:val", ['val' => $token], true);
	}

	/**
	 * Select User's Token via User ID
	 * @param int $user Token's User ID store in Database
	 * @return array|false
	 */
	public function selectByUser(int $user): array|false{
		return $this->prepared_query("SELECT id_token, id_user, id_client, scope, expire, value FROM TOKEN WHERE id_user=:usr", ['usr'=> $user], true);
	}

	/**
	 * Select token by client application
	 * @param int $client application ID
	 * @return array|false
	 */
	public function selectByClient(int $client): array|false{
		return $this->prepared_query("SELECT id_token, id_user, id_client, scope, expire, value FROM TOKEN WHERE id_client=:client", ['client' => $client], true);
	}
}