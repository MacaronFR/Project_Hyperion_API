<?php

namespace Hyperion\API;

use JetBrains\PhpStorm\ArrayShape;

require_once "Model.php";

class ClientModel extends Model{
	protected string $table_name = "CLIENT";
	protected string $id_name = "id_client";
	protected array $column = [
		"scope" => "scope",
		"client" => "client_id",
		"secret" => "client_secret",
		"name" => "name",
		"user" => "user"
	];

	#[ArrayShape([
		'id_client' => 'int',
		'secret' => 'string',
		'scope' => 'int'
	])]
	public function selectFromClientID(string $client_id): array|false{
		return $this->prepared_query("SELECT id_client, client_secret as secret, scope FROM CLIENT WHERE client_id=:id", ['id' => $client_id], true);
	}
}