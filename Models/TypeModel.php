<?php


namespace Hyperion\API;

require_once "autoload.php";

class TypeModel extends Model{
	protected string $id_name = "id_type";
	protected string $table_name = "TYPES";
	protected array $column = [
		"type" => "type",
		"category" => "category"
	];

	public function selectByCategory(int $id_category, int $iteration = 0, bool $limit = true): array|false{
		$start = $iteration * 500;
		$sql = "SELECT type,category FROM TYPES WHERE category=:id ";
		if($limit){
			$sql .= "LIMIT $start, $this->max_row";
		}
		return $this->prepared_query($sql, ["id" => $id_category]);
	}

	public function selectByType(string $name): array|false{
		return $this->prepared_query("SELECT type, id_type, category FROM TYPES WHERE type=:type", ['type' => $name], unique: true);
	}
}