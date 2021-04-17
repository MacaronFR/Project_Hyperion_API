<?php


namespace Hyperion\API;

require_once "autoload.php";

class DepositModel extends Model{

	protected string $id_name = "id_deposit";
	protected string $table_name = "DEPOSIT";
	protected array $column = [
		"space" => "space",
		"address" => "address",
	];
}