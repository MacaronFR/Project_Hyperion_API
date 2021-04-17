<?php


namespace Hyperion\API;

require_once "autoload.php";

class OffersModel extends Model{
	protected string $id_name = "id_offers";
	protected string $table_name = "OFFERS";
	protected array $column = [
		"offer" => "offer",
		"counter" => "counter_offer",
		"status" => "status",
		"user" => "id_user"
	];

}