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
	protected int $max_row = 10;

	public function selectByCategory(int $id_category, int $iteration = 0, bool $limit = true): array|false{
		$start = $iteration * $this->max_row;
		$sql = "SELECT type, id_type as id FROM TYPES WHERE category=:id ";
		if($limit){
			$sql .= "LIMIT $start, $this->max_row";
		}
		return $this->prepared_query($sql, ["id" => $id_category]);
	}

	public function selectTotalByCategory(int $category): int|false{
		$sql = "SELECT COUNT($this->id_name) as count FROM $this->table_name WHERE {$this->column['category']}=$category";
		$res = $this->query($sql);
		if($res !== false){
			return (int)$res[0]['count'];
		}
		return false;
	}

	public function selectByType(string $name): array|false{
		return $this->prepared_query("SELECT type, id_type, category FROM TYPES WHERE type=:type", ['type' => $name], unique: true);
	}
}