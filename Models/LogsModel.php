<?php


namespace Hyperion\API;

require_once "autoload.php";

class LogsModel extends Model{
	protected string $table_name = "LOGS";
	protected string $id_name = "id_logs";
	protected array $column = [
		"action" => "action",
		"date" => "log_date",
		"user" => "id_user",
		"client" => "id_client"
	];
}