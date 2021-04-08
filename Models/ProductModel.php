<?php


namespace Hyperion\API;
require_once "autoload.php";

class ProductModel extends Model{

	public function selectAll(int $iteration = 0): array|false {
		$start = 500 * $iteration;
		$end = 500 * ($iteration + 1);
		$query1 = "SELECT PROD.id_product, PROD.buying_price, PROD.selling_price, T.type, REF.id_product as id_ref FROM PRODUCTS PROD INNER JOIN REFERENCE_PRODUCTS REF ON PROD.id_ref = REF.id_product INNER JOIN TYPES T on REF.type = T.id_type LIMIT ${start}, ${end};";
		$query2 = "SELECT name, value FROM REF_HAVE_SPEC INNER JOIN SPECIFICATION S on REF_HAVE_SPEC.id_spec = S.id_specification WHERE id_product=:id;";
		$query3 = "SELECT name, value FROM PRODUCT_HAVE_SPEC INNER JOIN SPECIFICATION S on PRODUCT_HAVE_SPEC.id_spec = S.id_specification WHERE id_product=:id;";
		$products = $this->query($query1);
		foreach ($products as &$prod){
			$refspec = $this->prepared_query($query2, ["id" => $prod['id_ref']]);
			$prodspec = $this->prepared_query($query3, ["id" => $prod['id_product']]);
			foreach($refspec as $spec){
				$prod["spec"][$spec["name"]] = $spec["value"];
			}
			foreach($prodspec as $spec){
				$prod["spec"][$spec["name"]] = $spec["value"];
			}
		}
		return $products;
	}

	public function select(int $id): array|false
	{
		// TODO: Implement select() method.
	}

	public function update(int $id, array $value): bool
	{
		// TODO: Implement update() method.
	}

	public function insert(array $value): bool
	{
		// TODO: Implement insert() method.
	}

	public function delete(int $id): bool
	{
		// TODO: Implement delete() method.
	}

	protected function prepare_column_and_parameter(string $name): array|false
	{
		// TODO: Implement prepare_column_and_parameter() method.
	}
}