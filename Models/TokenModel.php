<?php


namespace Hyperion\API;

class TokenModel extends Model
{
	protected string $table_name = "TOKEN";
	protected string $id_name = "id_token";
	protected function prepare_column_and_parameter(string $name): array|false{
		return match($name){
			"token" => ["value", "token"],
			"scope" => ["scope", "scope"],
			"end" => ["expire", "end"],
			"client" => ["id_client", "client"],
			"user" => ["id_user", "user"],
			default => false
		};
	}

	public function selectAll(int $iteration = 0): array|false{return false;}
	/**
	 * Select Token by is DataBase ID
	 * @param int $id
	 * @return array|false
	 */
	public function select(int $id): array|false{
		return $this->prepared_query("SELECT id_token, id_user, id_client, scope, expire FROM TOKEN WHERE id_token=:id", ['id' => $id], true);
	}

	/**
	 * Select token by value
	 * @param string $token 64 character wide string equal to existing token in Database
	 * @return array|false
	 */
	public function selectByToken(string $token): array|false{
		return $this->prepared_query("SELECT id_token, id_user, id_client, scope, expire FROM TOKEN WHERE value=:val", ['val' => $token], true);
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

	/**
	 * Update the token designated by $id
	 * @param int $id Token ID
	 * @param array $value Value to update
	 * @return bool Return true on success, false on failure or if nothing changed
	 */
	public function update(int $id, array $value): bool{
		$query = $this->prepare_query_string($value, self::UPDATE);
		if($query === false)
			return false;
		$value['id'] = $id;
		return $this->prepared_query($query, $value, fetch: false);
	}

	/**
	 * Insert new token
	 * @param array $value Expect User ID, Client ID, Token Value, Token Scope and Token expiration date
	 * @return bool
	 */
	public function insert(array $value): bool{
		$query = $this->prepare_query_string($value, self::INSERT);
		if($query === false)
			return false;
		return $this->prepared_query($query, $value, fetch: false);
	}

	/**
	 * Delete Token designated by $id, Return false on failure
	 * @param int $id
	 * @return bool
	 */
	public function delete(int $id): bool{
		return $this->prepared_query("DELETE FROM TOKEN WHERE id_token=:id",['id' => $id], fetch: false);
	}
}