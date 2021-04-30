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

	public function selectIdentical(array $fields){
		$sql = "SELECT ";
		foreach($this->column as $name => $column){
			$sql .= "$column as $name, ";
		}
		$sql .= "$this->id_name as id ";
		$sql .= "FROM $this->table_name WHERE name=:name AND value=:value;";
		var_dump($sql,$fields);
		return $this->prepared_query($sql,$fields, unique: true);
	}
}