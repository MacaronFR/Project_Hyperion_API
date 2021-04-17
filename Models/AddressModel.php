<?php

namespace Hyperion\API;


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
}