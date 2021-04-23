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
		$sql = "select *  from ".$this->table_name." WHERE zip_code=:zip and city=:city and address=:address and country=:country and region=:region";
		return $this->prepared_query($sql,$fields);
	}

}