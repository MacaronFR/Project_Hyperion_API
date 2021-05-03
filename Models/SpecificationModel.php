<?php


namespace Hyperion\API;

require_once "autoload.php";

class SpecificationModel extends Model{
    protected string $id_name = "id_specification";
    protected string $table_name = "SPECIFICATION";
    protected array $column = [
        "name"=>"name",
        "value"=>"value"
    ];
    protected int $max_row = 10;

	public function selectIdentical(array $fields): array|false{
		$sql = "SELECT ";
		foreach($this->column as $name => $column){
			$sql .= "$column as $name, ";
		}
		$sql .= "$this->id_name as id ";
		$sql .= "FROM $this->table_name WHERE name=:name AND value=:value;";
		return $this->prepared_query($sql,$fields, unique: true);
	}
	public function selectAllName(int $iteration = 0, bool $limit = true): array|false{
		$start = $this->max_row * $iteration;
		$sql = "SELECT";
		$sql .= " $this->column['name'] as name";
		$sql .= " FROM $this->table_name";
		if($limit)
			$sql .= " LIMIT $start, $this->max_row";
		return $this->query($sql);
	}
}