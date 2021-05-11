<?php


namespace Hyperion\API;

require_once "autoload.php";

class StateModel extends Model{
	protected string $table_name = "STATE";
	protected string $id_name = "id_state";
	protected array $column = [
		"name" => "name",
		"penality" => "penality"
	];
}