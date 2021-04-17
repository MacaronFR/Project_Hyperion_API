<?php


namespace Hyperion\API;

require_once "autoload.php";

class InvoiceModel extends Model{
	protected string $id_name = "id_invoice";
	protected string $table_name = "INVOICE";
	protected array $column = [
		"creation" => "date_creation",
		"total" => "total",
		"file" => "id_file",
		"cart" => "id_cart",
		"offer" => "id_offer"
	];
}