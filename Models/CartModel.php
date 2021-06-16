<?php


namespace Hyperion\API;
require_once "autoload.php";

class CartModel extends Model{
	protected string $table_name = "CART";
	protected string $id_name = "id_cart";
	protected array $column = [
		"status" => "status",
		'total' => "total",
		'user' => 'id_user'
	];

	public function selectByUser(int $user, bool $active = false): array|false{
		$unique = false;
		$sql = "SELECT";
		foreach($this->column as $name => $item){
			$sql .= " $item as $name,";
		}
		$sql .= " $this->id_name as id";
		$sql .= " FROM $this->table_name WHERE id_user=:id";
		if($active){
			$sql .= " AND status=0";
			$unique = true;
		}
		return $this->prepared_query($sql, ["id" => $user], unique: $unique);
	}
}