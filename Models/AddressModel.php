<?php

namespace Hyperion\API;

require_once "autoload.php";
class AddressModel extends Model{

	protected string $table_name = "ADDRESSES";
	protected string $id_name = "id_address";
	protected array $column = [
		"zip" => "zip_code",
		"city" => "city",
		"address" => "address",
		"country" => "country",
		"region" => "region",
	];

	public function selectIdentical(array $fields){
		$sql = "SELECT ";
		foreach($this->column as $name => $column){
			$sql .= "$column as $name, ";
		}
		$sql .= "$this->id_name as id ";
		$sql .= "FROM $this->table_name WHERE zip_code=:zip AND city=:city AND address=:address AND country=:country AND region=:region;";
		return $this->prepared_query($sql,$fields, unique: true);
	}

}