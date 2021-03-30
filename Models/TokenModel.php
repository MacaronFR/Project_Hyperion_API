<?php


namespace Hyperion\API;
use DateTime;
use DateInterval;

class TokenModel extends Model
{
	private function prepareFields(array $value): array|false{
		try {
			return [
				'value' => $value['token'],
				'scope' => $value['scope'],
				'expire' => $value['end'],
				'idc' => $value['client'],
				'idu' => $value['user']
			];
		}catch (\Exception){
			return false;
		}
	}
	public function selectAll(int $iteration): array|false{return false;}
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
		return $this->prepared_query("SELECT id_token, id_user, id_client, scope, expire FROM TOKEN WHERE id_client=:client", ['client' => $client]);
	}

	public function update(int $id, array $value): bool{}

	/**
	 * Refresh the token instead of recreate it. Return false if no changes
	 * @param int $id Token ID in Database
	 * @return string|false
	 */
	public function refreshToken(int $id):string|false{
		$now = new DateTime();
		$now->add(new DateInterval("PT2H"));
		if($this->prepared_query("UPDATE TOKEN SET expire=:expire WHERE id_token=:id", ['expire' => $now->format("Y-m-d H:i:s"), 'id' => $id], fetch: false)){
			return $now->format("Y-m-d H:i:s");
		}
		return false;
	}

	/**
	 * Insert new token
	 * @param array $value Expect User ID, Client ID, Token Value, Token Scope and Token expiration date
	 * @return bool
	 */
	public function insert(array $value): bool{
		$formattedValue = $this->prepareFields($value);
		if($formattedValue !== false){
			return $this->prepared_query("INSERT INTO TOKEN (value, scope, expire, id_client, id_user) VALUE (:value, :scope, :expire, :idc, :idu)", $formattedValue, fetch: false);
		}
		return false;
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