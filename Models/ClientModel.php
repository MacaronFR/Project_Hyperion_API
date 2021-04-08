<?php

namespace Hyperion\API;
use JetBrains\PhpStorm\ArrayShape;

require_once "Model.php";

class ClientModel extends Model
{
	protected string $table_name = "CLIENT";
	protected string $id_name = "id_client";

	protected function prepare_column_and_parameter(string $name):array|false{
		return match($name){
			"scope" => ["scope", "scope"],
			"client" => ["client_id", "client"],
			"client_secret" => ["client_secret", "client_secret"],
			"name" => ["name", "name"],
			"user" => ["user", "user"],
			default => false
		};
	}

	public function selectAll(int $iteration = 0): array|false
	{
		// TODO: Implement selectAll() method.
	}

	public function select(int $id): array|false
	{
		// TODO: Implement select() method.
	}
	#[ArrayShape([
		'id_client' => 'int',
		'client_secret' => 'string',
		'scope' => 'int'
	])]
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