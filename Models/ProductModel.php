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
		$sql = "SELECT PRODUCTS.id_product as id FROM PRODUCTS INNER JOIN REFERENCE_PRODUCTS RP on PRODUCTS.id_ref = RP.id_product WHERE RP.type=:id LIMIT $start, 500;";
		return $this->prepared_query($sql, ["id" => $id_type]);
	}

	public function selectWithDetail(int $id): array|false{
		$sql1 = "SELECT S.* FROM PRODUCTS INNER JOIN REFERENCE_PRODUCTS RP on PRODUCTS.id_ref = RP.id_product INNER JOIN REF_HAVE_SPEC RHS on RP.id_product = RHS.id_product INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification WHERE PRODUCTS.id_product=:id";
		$sql2 = "SELECT S.* FROM PRODUCTS INNER JOIN PRODUCT_HAVE_SPEC PHS on PRODUCTS.id_product = PHS.id_product INNER JOIN SPECIFICATION S on PHS.id_spec = S.id_specification WHERE PRODUCTS.id_product=:id";
		$ref_spec = $this->prepared_query($sql1, ["id" => $id]);
		$prod_spec = $this->prepared_query($sql2, ["id" => $id]);
		$prod = [];
		if($ref_spec !== false && $prod_spec !== false){
			foreach($ref_spec as $spec){
				$prod["spec"][$spec["name"]] = $spec["value"];
			}
			foreach($prod_spec as $spec){
				$prod["spec"][$spec["name"]] = $spec["value"];
			}
			return $prod;
		}
		return false;
	}

	public function selectAllDetails(int $iteration = 0): array|false{
		$start = 500 * $iteration;
		$query1 = "SELECT PROD.id_product, PROD.buying_price, PROD.selling_price, T.type, REF.id_product as id_ref FROM PRODUCTS PROD INNER JOIN REFERENCE_PRODUCTS REF ON PROD.id_ref = REF.id_product INNER JOIN TYPES T on REF.type = T.id_type LIMIT $start, 500;";
		$products = $this->query($query1);
		foreach($products as &$prod){
			$prod = array_merge($prod, $this->selectWithDetail($prod['id']));
		}
		return $products;
	}

	public function selectAllByMark(string $mark, int $iteration = 0): array|false{
		$start = 500 * $iteration;
		$sql = 	"SELECT PRODUCTS.id_product as id FROM PRODUCTS
					INNER JOIN REFERENCE_PRODUCTS RP on PRODUCTS.id_ref = RP.id_product
					INNER JOIN REF_HAVE_SPEC RHS on RP.id_product = RHS.id_product
					INNER JOIN SPECIFICATION S on RHS.id_spec = S.id_specification
				WHERE S.name=\"mark\" AND S.value=:mark LIMIT $start,500;";
		return $this->prepared_query($sql, ["mark" => $mark]);
	}
}