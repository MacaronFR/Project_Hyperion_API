<?php


namespace Hyperion\API;
require_once "autoload.php";

class ProductModel extends Model{
	protected string $table_name = "PRODUCTS";
	protected string $id_name = "id_product";
	protected array $column = [
		"state" => "state",
		"sell_p" => "selling_price",
		"buy_p" => "buying_price",
		"status" => "status",
		"offer" => "id_offers",
		"ref" => "id_ref",
		"buy_d" => "buying_date",
		"sell_d" => "selling_date"
	];

	public function selectAllByType(int $id_type, int $iteration = 0): array|false{
		$start = 500 * $iteration;
		$sql = "SELECT PRODUCTS.id_product FROM PRODUCTS INNER JOIN REFERENCE_PRODUCTS RP on PRODUCTS.id_ref = RP.id_product WHERE RP.type=:id LIMIT $start, 500;";
		return $this->prepared_query($sql, ["id" => $id_type]);
	}

	public function selectAll(int $iteration = 0): array|false{
		$start = 500 * $iteration;
		$end = 500 * ($iteration + 1);
		$query1 = "SELECT PROD.id_product, PROD.buying_price, PROD.selling_price, T.type, REF.id_product as id_ref FROM PRODUCTS PROD INNER JOIN REFERENCE_PRODUCTS REF ON PROD.id_ref = REF.id_product INNER JOIN TYPES T on REF.type = T.id_type LIMIT ${start}, ${end};";
		$query2 = "SELECT name, value FROM REF_HAVE_SPEC INNER JOIN SPECIFICATION S on REF_HAVE_SPEC.id_spec = S.id_specification WHERE id_product=:id;";
		$query3 = "SELECT name, value FROM PRODUCT_HAVE_SPEC INNER JOIN SPECIFICATION S on PRODUCT_HAVE_SPEC.id_spec = S.id_specification WHERE id_product=:id;";
		$products = $this->query($query1);
		foreach($products as &$prod){
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
}