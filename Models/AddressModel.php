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

	public function selectByUser(int $id){
			return $this->prepared_query("SELECT id,zip_code,city,address,country,region FROM ADDRESSES INNER JOIN ON USERS.address = ADRESSES.id WHERE USERS.address = :id",["id"=>$id],unique:true);
	}

}