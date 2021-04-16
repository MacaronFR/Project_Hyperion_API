<?php


namespace Hyperion\API;


class CategoryModel extends Model{

	protected string $id_name = "id_category";
	protected string $table_name = "CATEGORY";

	public function selectAll(int $iteration = 0): array|false{
		$start = $iteration * 500;
		return $this->query("SELECT id_category as id, name FROM CATEGORY LIMIT $start, 500");
	}
	public function selectByName(string $name): array| false{
		return $this->prepared_query("SELECT id_category as id, name FROM CATEGORY WHERE name=:name", ["name" => $name], unique: true);
	}

	public function select(int $id): array|false{
		// TODO: Implement select() method.
	}

	public function update(int $id, array $value): bool{
		// TODO: Implement update() method.
	}

	public function insert(array $value): bool{
		$query = $this->prepare_query_string($value, Model::INSERT);
		return $this->prepared_query($query, $value, fetch: false);
	}

	public function delete(int $id): bool{
		// TODO: Implement delete() method.
	}

	protected function prepare_column_and_parameter(string $name): array|false{
		return match ($name){
			"name" => ["name", "name"],
			default => false
		};
	}
}