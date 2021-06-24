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
		"offer" => "id_offer",
		"user" => "id_user",
		"status" => "status"
	];
	protected int $max_row = 10;

	public function selectAllByUserInvoice(int $user_id, int $iteration = 0, bool $limit = true): array|false{
		return $this->selectAllByUser($user_id, "id_cart", $iteration, $limit);
	}

	public function selectAllByUserCredit(int $user_id, int $iteration = 0, bool $limit = true): array|false{
		return $this->selectAllByUser($user_id, "id_offer", $iteration, $limit);
	}

	private function selectAllByUser(int $user_id, string $column, int $iteration = 0, bool $limit = true): array|false{
		$sql = "SELECT";
		foreach($this->column as $name => $item){
			$sql .= " $item as $name,";
		}
		$sql .= " $this->id_name as id";
		$sql .= " FROM $this->table_name WHERE id_user=:id AND $column IS NOT NULL ";
		if($limit){
			$start = $iteration * $this->max_row;
			$sql .= "LIMIT $start, $this->max_row;";
		}
		return $this->prepared_query($sql, ['id' => $user_id]);
	}
}