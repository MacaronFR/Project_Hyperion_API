<?php

namespace Hyperion\API;
require_once "Model.php";

class ClientModel extends Model
{

	public function selectAll(int $iteration): array|false
	{
		// TODO: Implement selectAll() method.
	}

	public function select(int $id): array|false
	{
		// TODO: Implement select() method.
	}
	public function selectFromClientID(string $client_id): array|false{
		return $this->prepared_query("SELECT id_client, client_secret, scope FROM CLIENT WHERE client_id=:id", ['id' => $client_id], true);
	}
	public function update(int $id, array $value): bool
	{
		// TODO: Implement update() method.
	}

	public function insert(array $value): bool
	{
		// TODO: Implement insert() method.
	}

	public function delete(int $id): bool
	{
		// TODO: Implement delete() method.
	}
}