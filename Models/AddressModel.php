<?php

namespace Hyperion\API;


class AddressModel extends Model {

	public function selectAll(int $iteration = 0): array|false
	{
		return false;
	}

	public function select(int $id): array|false {
		return $this->prepared_query("SELECT zip_code, city, country, address, region FROM ADRESSES WHERE id_addresse=:id", ["id" => $id], unique: true);
	}

	public function update(int $id, array $value): bool
	{
		return false;
	}

	public function insert(array $value): bool
	{
		return false;
	}

	public function delete(int $id): bool
	{
		return false;
	}

	protected function prepare_column_and_parameter(string $name): array|false{
		return match($name){
			"zip" => ["zip_code", "zip"],
			"city" => ["city", "city"],
			"address" => ["address", "address"],
			"country" => ["country", "country"],
			"region" => ["region", "region"],
			default => false
		};
	}
}