<?php
/*
 * prepare JSON post for files
 **/

namespace Hyperion\API;

require_once "autoload.php";

class ContributeModel extends Model{
	protected string $id_name = "id_contribute";
	protected string $table_name = "CONTRIBUTE";
	protected array $column = [
		"value" => "value",
		"project" => "id_project",
		"user" => "id_user",
	];
}