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



	public function selectByName(string $name): array|false{
		return $this->prepared_query("SELECT type, value FROM SPECIFICATION WHERE name=:name", ['name' => $name], unique: true);
	}
}