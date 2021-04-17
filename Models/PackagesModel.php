<?php


namespace Hyperion\API;

require_once "autoload.php";

class PackagesModel extends Model{
	protected string $id_name = "id_package";
	protected string $table_name = "PACKAGES";
	protected array $column = [
		"number" => "number",
		"offer" => "id_offer",
		"address" => "id_address"
	];
}