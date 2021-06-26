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

	public function selectAllByUserInvoiceTotal(int $user_id): int|false{
		return $this->selectAllByUserTotal($user_id, "id_cart");
	}

	public function selectAllByUserCreditTotal(int $user_id): int|false{
		return $this->selectAllByUserTotal($user_id, "id_offer");
	}

	private function selectAllByUserTotal(int $user_id, string $column): int|false{
		$sql = "SELECT COUNT($this->id_name) as count FROM $this->table_name WHERE id_user=:id AND $column IS NOT NULL ";
		$total = $this->prepared_query($sql, ['id' => $user_id], unique: true);
		if($total === false){
			return false;
		}
		return $total['count'];
	}

	private function selectAllByColumn(string $column, int $iteration = 0, bool $limit = true): array|false{
		$sql = "SELECT ";
		foreach($this->column as $name => $item){
			$sql .= " $item as $name,";
		}
		$sql .= " $this->id_name as id";
		$sql .= " FROM $this->table_name WHERE $column IS NOT NULL ";
		if($limit){
			$start = $iteration * $this->max_row;
			$sql .= "LIMIT $start, $this->max_row;";
		}
		return $this->query($sql);
	}

	public function selectAllInvoice(int $iteration = 0, bool $limit = true): array|false{
		return $this->selectAllByColumn("id_cart", $iteration, $limit);
	}

	public function selectAllCredit(int $iteration = 0, bool $limit = true): array|false{
		return $this->selectAllByColumn("id_offer", $iteration, $limit);
	}

	public function selectAllInvoiceTotal(): int|false{
		return $this->selectAllTotal("id_cart");
	}

	public function selectAllCreditTotal(): int|false{
		return $this->selectAllTotal("id_offer");
	}

	private function selectAllTotal(string $column): int|false{
		$sql = "SELECT COUNT($this->id_name) as count FROM $this->table_name WHERE $column IS NOT NULL ";
		$total = $this->query($sql, unique: true);
		if($total === false){
			return false;
		}
		return $total['count'];
	}
}