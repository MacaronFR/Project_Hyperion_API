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

	public function selectByCategory(int $id_category, int $iteration = 0): array|false{
		$start = $iteration * 500;
		return $this->prepared_query("SELECT type,category FROM TYPES WHERE category=:id LIMIT $start,500", ["id" => $id_category]);
	}

	public function selectByType(string $name): array|false{
		return $this->prepared_query("SELECT type, id_type, category FROM TYPES WHERE type=:type", ['type' => $name], unique: true);
	}
}