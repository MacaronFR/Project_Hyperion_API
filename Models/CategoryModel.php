<?php


namespace Hyperion\API;

require_once "autoload.php";
class CategoryModel extends Model{

	protected string $id_name = "id_category";
	protected string $table_name = "CATEGORY";
	protected array $column = [
		"name" => "name"
	];
	protected int $max_row = 10;
	public function selectByName(string $name): array| false{
		return $this->prepared_query("SELECT id_category as id, name FROM CATEGORY WHERE name=:name", ["name" => $name], unique: true);
	}
}